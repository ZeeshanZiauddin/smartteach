<?php

namespace App\Filament\Widgets;

use App\Models\CourseMaterial;
use App\Models\Quiz;
use App\Models\Assignment;
use Filament\Widgets\Widget;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Http;

class AskAiWidget extends Widget
{
    protected string $view = 'filament.widgets.ask-ai-widget';
    protected static ?string $heading = '💭 Ask AI Assistant';
    protected static ?string $description = 'Ask questions related to your quiz, assignment, or study material.';

    public string $selectedType = 'quiz';
    public ?int $selectedItemId = null;
    public string $question = '';
    public array $messages = [];

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->check() && auth()->user()->hasRole('student');
    }

    public function mount(): void
    {
        $this->messages[] = [
            'role' => 'assistant',
            'content' => '👋 Hi! I’m your AI tutor. Ask me anything about your quiz, assignment, or materials.',
        ];

        // Automatically select first item when widget loads
        $this->selectFirstItem();
    }

    protected function selectFirstItem(): void
    {
        $user = auth()->user();

        $firstItem = match ($this->selectedType) {
            'quiz' => Quiz::whereIn('course_id', $user->enrolledCourses()->pluck('courses.id'))->latest()->first(),
            'assignment' => Assignment::whereIn('course_id', $user->enrolledCourses()->pluck('courses.id'))->latest()->first(),
            'material' => CourseMaterial::whereIn('course_id', $user->enrolledCourses()->pluck('courses.id'))->latest()->first(),
            default => null,
        };

        if ($firstItem) {
            $this->selectedItemId = $firstItem->id;
            $this->messages[] = [
                'role' => 'assistant',
                'content' => "✅ Ready to answer questions about **{$firstItem->title}** ({$this->selectedType}).",
            ];
        } else {
            $this->messages[] = [
                'role' => 'assistant',
                'content' => "⚠️ No {$this->selectedType} items found yet.",
            ];
        }
    }

    public function getItemsProperty()
    {
        $user = auth()->user();

        return match ($this->selectedType) {
            'quiz' => Quiz::whereIn('course_id', $user->enrolledCourses()->pluck('courses.id'))
                ->latest()->pluck('title', 'id')->toArray(),
            'assignment' => Assignment::whereIn('course_id', $user->enrolledCourses()->pluck('courses.id'))
                ->latest()->pluck('title', 'id')->toArray(),
            'material' => CourseMaterial::whereIn('course_id', $user->enrolledCourses()->pluck('courses.id'))
                ->latest()->pluck('title', 'id')->toArray(),
            default => [],
        };
    }

    public function updatedSelectedType()
    {
        $this->selectedItemId = null;

        $this->messages[] = [
            'role' => 'assistant',
            'content' => "🎯 You selected **" . ucfirst($this->selectedType) . "**. Loading first available item...",
        ];

        // Automatically pick first item of the new type
        $this->selectFirstItem();
    }

    public function updatedSelectedItemId($id)
    {
        if (!$id)
            return;

        $item = match ($this->selectedType) {
            'quiz' => Quiz::find($id),
            'assignment' => Assignment::find($id),
            'material' => CourseMaterial::find($id),
            default => null,
        };

        if ($item) {
            $this->messages[] = [
                'role' => 'assistant',
                'content' => "✅ Ready to answer questions about **{$item->title}** ({$this->selectedType}).",
            ];
        }
    }

    public function ask(): void
    {
        if (blank($this->question)) {
            Notification::make()->title('Please enter a question.')->warning()->send();
            return;
        }

        $this->messages[] = ['role' => 'user', 'content' => $this->question];
        $user = auth()->user();

        try {
            $item = match ($this->selectedType) {
                'quiz' => Quiz::find($this->selectedItemId),
                'assignment' => Assignment::find($this->selectedItemId),
                'material' => CourseMaterial::find($this->selectedItemId),
                default => null,
            };

            $context = match ($this->selectedType) {
                'quiz' => $item
                ? "Quiz titled '{$item->title}'. Description: {$item->description}"
                : 'No quiz selected.',
                'assignment' => $item
                ? "Assignment titled '{$item->title}'. Instructions: {$item->summary}"
                : 'No assignment selected.',
                'material' => $item
                ? "Material titled '{$item->title}'. Summary: {$item->summary}"
                : 'No material selected.',
                default => 'No context available.',
            };

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('HF_TOKEN'),
                'Content-Type' => 'application/json',
            ])->post('https://router.huggingface.co/v1/chat/completions', [
                        "model" => "openai/gpt-oss-20b:groq",
                        "stream" => false,
                        "messages" => array_merge([
                            [
                                'role' => 'system',
                                'content' => "You are an AI tutor for a quiz system. Use this quiz info to answer contextually:\n\n{$context}",
                            ],
                        ], $this->messages),
                    ]);

            $json = $response->json();
            $answer = $json['choices'][0]['message']['content'] ?? 'No response from AI.';
        } catch (\Throwable $e) {
            $answer = '⚠️ Error fetching info: ' . $e->getMessage();
        }

        $this->messages[] = ['role' => 'assistant', 'content' => $answer];
        $this->question = '';
    }
}