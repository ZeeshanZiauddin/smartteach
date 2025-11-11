<?php

namespace App\Filament\Widgets;

use App\Models\Quiz;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Support\Facades\Http;
use Filament\Forms;

class UpcomingAssignmentsWidget extends TableWidget
{
    protected static ?string $heading = '📘 Upcoming Assignments';
    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        $user = Filament::auth()->user();
        return $user && $user->hasRole('student');
    }

    public function table(Table $table): Table
    {
        $user = Filament::auth()->user();

        return $table
            ->query(function () use ($user) {
                return Quiz::query()
                    ->whereHas('course.students', fn($q) => $q->where('student_id', $user->id))
                    ->whereDate('start_at', '>=', now())
                    ->orderBy('start_at', 'asc');
            })
            ->columns([
                Tables\Columns\TextColumn::make('title')->label('Title')->searchable(),
                Tables\Columns\TextColumn::make('course.title')->label('Course')->sortable(),
                Tables\Columns\TextColumn::make('start_at')->label('Starts At')->dateTime(),
                Tables\Columns\TextColumn::make('end_at')->label('Ends At')->dateTime(),
            ])
            ->actions([
                Action::make('ask_ai')
                    ->label('Ask AI About This Quiz')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color('primary')
                    ->url(fn($record) => \App\Filament\Pages\AskAiPage::getUrl(['quiz' => $record->id]))

            ])
            ->emptyStateHeading('No upcoming assignments')
            ->emptyStateDescription('You have no upcoming quizzes or assignments in your enrolled courses.');
    }
}