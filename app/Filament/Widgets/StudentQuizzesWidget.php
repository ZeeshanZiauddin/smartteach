<?php

namespace App\Filament\Widgets;

use App\Models\Quiz;
use App\Models\QuizSubmission;
use Filament\Widgets\Widget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class StudentQuizzesWidget extends Widget
{
    protected static ?string $heading = '📚 Your Quizzes';
    protected static ?string $description = 'All quizzes from your enrolled courses.';
    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';
    protected string $view = 'filament.widgets.student-quizzes-widget';

    public $quizzes = [];
    public $pending = [];
    public $completed = [];

    public function mount(): void
    {
        $this->quizzes = $this->getQuizzes();

        $this->pending = $this->quizzes->filter(fn($quiz) => !$quiz['submitted']);
        $this->completed = $this->quizzes->filter(fn($quiz) => $quiz['submitted']);
    }

    protected function getQuizzes()
    {
        $user = Auth::user();

        return Quiz::with('course')
            ->whereIn('course_id', $user->enrolledCourses->pluck('id'))
            ->orderBy('start_at', 'desc')
            ->get()
            ->map(function ($quiz) use ($user) {
                $submission = QuizSubmission::where('quiz_id', $quiz->id)
                    ->where('user_id', $user->id)
                    ->first();

                return [
                    'id' => $quiz->id,
                    'title' => $quiz->title,
                    'course' => $quiz->course->title ?? 'Unknown',
                    'start_at' => $quiz->start_at ? Carbon::parse($quiz->start_at)->format('d M Y') : 'N/A',
                    'end_at' => $quiz->end_at ? Carbon::parse($quiz->end_at)->format('d M Y') : 'N/A',
                    'submitted' => $submission ? true : false,
                    'correctQuestions' => $submission ? $submission->correct_count ?? 0 : null,
                    'totalQuestions' => $submission ? $submission->total_questions ?? 0 : null,
                    'totalMarks' => $submission ? $submission->total_marks ?? 0 : null,
                    'obtainedmarks' => $submission ? $submission->earned_marks ?? 0 : null,
                    'percentage' => $submission ? $submission->score ?? 0 : null,
                ];
            });
    }

    public static function canView(): bool
    {
        $user = auth()->user();
        return $user && $user->hasRole('student');
    }
}