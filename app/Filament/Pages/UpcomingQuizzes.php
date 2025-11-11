<?php

namespace App\Filament\Pages;

use App\Models\Quiz;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Illuminate\Database\Eloquent\Builder;
use BackedEnum;
use Kirschbaum\Commentions\Filament\Actions\CommentsAction;
use Kirschbaum\Commentions\Filament\Actions\CommentsTableAction;
class UpcomingQuizzes extends Page implements HasTable
{
    use InteractsWithTable;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-clock';
    protected string $view = 'filament.pages.upcoming-quizzes';
    protected static ?string $navigationLabel = 'Upcoming Quizzes';
    protected static ?string $title = 'Upcoming Quizzes';
    public static function shouldRegisterNavigation(): bool
    {
        $user = Filament::auth()->user();

        // Only show navigation for users with the 'student' role
        return $user && $user->hasRole('student');
    }

    public function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->query(function (Builder $query) {
                $user = auth()->user();

                // ✅ get enrolled course IDs (using your pivot table)
                $courseIds = $user->enrolledCourses()->pluck('courses.id');

                // ✅ fetch quizzes for those courses
                return Quiz::query()
                    ->whereIn('course_id', $courseIds)
                    ->orderBy('start_at', 'desc');
            })
            ->columns([
                TextColumn::make('title')->label('Quiz Title')->searchable()->sortable(),
                TextColumn::make('course.title')->label('Course'),
                TextColumn::make('teacher.name')->label('Teacher'),
                TextColumn::make('start_at')->label('Starts At')->dateTime(),
                TextColumn::make('end_at')->label('Ends At')->dateTime(),
                TextColumn::make('topic')->label('Topics')->formatStateUsing(function ($state) {
                    return is_array($state) ? implode(', ', $state) : $state;
                }),
            ])

            ->recordActions([
                Action::make('take_quiz')
                    ->label('Take Quiz')
                    ->icon('heroicon-o-pencil-square')
                    ->color('success')
                    ->url(fn($record) => url('admin/take-quiz/' . $record->id))
                    ->visible(function ($record) {
                        $user = auth()->user();

                        // Check if quiz already submitted
                        $hasSubmitted = \App\Models\QuizSubmission::where('quiz_id', $record->id)
                            ->where('user_id', $user->id)
                            ->exists();

                        // Only show if quiz is active (within start/end time)
                        $now = now();
                        $isActive = $record->start_at <= $now && $record->end_at >= $now;

                        return !$hasSubmitted && $isActive;
                    }),
                CommentsAction::make()
                    ->mentionables(User::all())
            ]);
    }
}