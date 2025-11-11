<?php

namespace App\Filament\Widgets;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Livewire\TemporaryUploadedFile;
use Filament\Forms\Components\FileUpload;

class UpcomingAssignments extends BaseWidget
{
    protected static ?string $heading = '📅 Upcoming Assignments';

    public static function canView(): bool
    {
        $user = Filament::auth()->user();
        return $user && $user->hasRole('student');
    }
    public function table(Tables\Table $table): Tables\Table
    {
        $user = Auth::user();

        return $table
            ->query(
                Assignment::query()
                    ->whereHas('course.students', function (Builder $q) use ($user) {
                        $q->where('student_id', $user->id);
                    })
                    ->whereDate('due_date', '>=', now())
                    ->orderBy('due_date', 'asc')
            )
            ->columns([
                TextColumn::make('title')
                    ->label('Assignment')
                    ->searchable()
                    ->sortable(),

                BadgeColumn::make('status')
                    ->label('Status')
                    ->getStateUsing(function ($record) use ($user) {
                        return AssignmentSubmission::where('assignment_id', $record->id)
                            ->where('user_id', $user->id)
                            ->exists() ? 'Submitted' : 'Pending';
                    })
                    ->colors([
                        'success' => 'Submitted',
                        'warning' => 'Pending',
                    ]),
            ])
            ->actions([
                Action::make('submit')
                    ->label('📤 Submit')
                    ->icon('heroicon-o-paper-airplane')
                    ->visible(fn($record) => !AssignmentSubmission::where('assignment_id', $record->id)
                        ->where('user_id', Auth::id())
                        ->exists())
                    ->form([
                        FileUpload::make('file')
                            ->label('Upload your assignment file')
                            ->required()
                            ->disk('public')
                            ->directory('submissions'),
                    ])
                    ->action(function (array $data, $record) {
                        AssignmentSubmission::create([
                            'assignment_id' => $record->id,
                            'user_id' => Auth::id(),
                            'file' => $data['file'],
                            'status' => 'submitted',
                            'submitted_at' => now(),
                        ]);

                        Notification::make()
                            ->title('Assignment submitted successfully!')
                            ->success()
                            ->body('Your file has been uploaded. Plz wait for teacher grading')
                            ->send();

                    }),
            ])
            ->emptyStateHeading('No upcoming assignments 🎉')
            ->paginated(false);
    }
}