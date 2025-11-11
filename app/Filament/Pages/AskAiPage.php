<?php

namespace App\Filament\Pages;

use App\Models\Quiz;
use Filament\Pages\Page;
use Filament\Forms;
use Illuminate\Support\Facades\Http;
use Filament\Notifications\Notification;

class AskAiPage extends Page
{
    protected string $view = 'filament.pages.ask-ai-page';
    protected static ?string $slug = 'ask-ai/{quiz}';
    protected static ?string $routeName = 'filament.pages.ask-ai-page';

    public ?Quiz $quiz = null;
    public array $messages = [];
    public string $question = '';

    public static function shouldRegisterNavigation(): bool
    {
        return false; // Hidden from sidebar, only accessible via link/button
    }

    public function mount(Quiz $quiz): void
    {
        $this->quiz = $quiz;

        $this->messages[] = [
            'role' => 'assistant',
            'content' => '👋 Hi! I’m your AI tutor. Ask anything about this quiz.',
        ];
    }

    public function ask(): void
    {
        if (blank($this->question)) {
            Notification::make()->title('Please enter a question.')->warning()->send();
            return;
        }

        $this->messages[] = ['role' => 'user', 'content' => $this->question];

        try {
            $quizContext = sprintf(
                "You are helping a student prepare for the quiz titled '%s'. Description: %s Topics: %s Start: %s End: %s",
                $this->quiz->title,
                $this->quiz->description ?? 'No description available.',
                implode(', ', $this->quiz->topic ?? []),
                optional($this->quiz->start_at)->format('d M Y H:i'),
                optional($this->quiz->end_at)->format('d M Y H:i'),
            );

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('HF_TOKEN'),
                'Content-Type' => 'application/json',
            ])->post('https://router.huggingface.co/v1/chat/completions', [
                        "model" => "openai/gpt-oss-20b:groq",
                        "stream" => false,
                        "messages" => array_merge([
                            [
                                'role' => 'system',
                                'content' => "You are an AI tutor for a quiz system. Use this quiz info to answer contextually:\n\n{$quizContext}",
                            ],
                        ], $this->messages),
                    ]);

            $json = $response->json();
            $answer = $json['choices'][0]['message']['content'] ?? 'No response from AI.';

            // $answer = "***Ai Answers Goes here";
        } catch (\Throwable $e) {
            $answer = '⚠️ Error getting response from AI: ' . $e->getMessage();
        }

        $this->messages[] = ['role' => 'assistant', 'content' => $answer];
        $this->question = '';
    }

    public function getTitle(): string
    {
        return 'AI Tutor: ' . ($this->quiz?->title ?? '');
    }
}