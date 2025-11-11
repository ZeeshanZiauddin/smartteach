<?php

namespace App\Filament\Pages;

use App\Models\Quiz;
use App\Models\QuizSubmission;
use Filament\Forms\Components\Radio;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use BackedEnum;

class TakeQuiz extends Page implements HasSchemas
{
    use InteractsWithSchemas;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-pencil-square';
    protected static ?string $navigationLabel = 'Take Quiz';
    protected static ?string $slug = 'take-quiz/{quiz_id}';
    protected static bool $shouldRegisterNavigation = false;
    protected string $view = 'filament.pages.take-quiz';

    public ?Quiz $quiz = null;
    public array $answers = [];
    public ?Carbon $quizStartTime = null;
    public bool $submitted = false;

    public int $correctCount = 0;
    public float $earnedMarks = 0;
    public float $totalMarks = 0;
    public int $totalQuestions = 0;
    public static function canView(): bool
    {
        $user = auth()->user();
        return $user && $user->hasRole('student');
    }

    public function mount(int $quiz_id): void
    {
        $this->quiz = Quiz::with('questions.options')->findOrFail($quiz_id);
        $this->totalQuestions = $this->quiz->questions->count();
        $this->totalMarks = $this->quiz->questions->sum('marks');

        // Check if the student already submitted the quiz
        $submission = QuizSubmission::where('quiz_id', $quiz_id)
            ->where('user_id', Auth::id())
            ->first();

        if ($submission) {
            $this->submitted = true;
            $this->answers = $submission->answers;
            $this->earnedMarks = ($submission->score / 100) * $this->totalMarks;
            $this->correctCount = count(array_filter($this->answers, function ($questionId, $optionId) {
                $question = $this->quiz->questions->find($questionId);
                return $question && $question->correctOption && $question->correctOption->id == $optionId;
            }, ARRAY_FILTER_USE_BOTH));
        }
    }

    public function form(Schema $form): Schema
    {
        $steps = [];

        foreach ($this->quiz->questions as $index => $question) {
            $steps[] = Step::make('Q' . ($index + 1))
                ->label("Question " . ($index + 1))
                ->schema([
                    Radio::make("answers.{$question->id}")
                        ->label($question->question_text)
                        ->options($question->options->pluck('option_text', 'id'))
                        ->required()
                        ->reactive(),
                ]);
        }

        return $form
            ->schema([
                Wizard::make($steps)

                    ->skippable(false),
            ])
            ->statePath('data');
    }

    public $data = [];

    public function submit()
    {
        $answers = $this->data['answers'] ?? [];

        $correctCount = 0;
        $earnedMarks = 0;

        foreach ($answers as $questionId => $optionId) {
            $question = $this->quiz->questions()->find($questionId);

            if ($question && $question->correctOption && $question->correctOption->id == $optionId) {
                $correctCount++;
                $earnedMarks += $question->marks;
            }
        }

        $score = $this->totalMarks > 0
            ? round(($earnedMarks / $this->totalMarks) * 100, 2)
            : 0;

        QuizSubmission::updateOrCreate(
            [
                'quiz_id' => $this->quiz->id,
                'user_id' => Auth::id(),
            ],
            [
                'answers' => $answers,
                'score' => $score,
            ]
        );

        $this->submitted = true;
        $this->correctCount = $correctCount;
        $this->earnedMarks = $earnedMarks;

        Notification::make()
            ->title('Quiz Submitted Successfully!')
            ->body("✅ Correct: {$correctCount}/{$this->totalQuestions}\n📊 Score: {$score}%\n🏆 Marks: {$earnedMarks}/{$this->totalMarks}")
            ->success()
            ->send();
    }
}