<?php

namespace App\Filament\Resources\Courses\RelationManagers;

use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\AttachAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class StudentsRelationManager extends RelationManager
{
    protected static string $relationship = 'Students';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('student_id')
                    ->label('Student')
                    ->relationship('students', 'name', function ($query) {
                        $query->where('role', 'student');
                    })
                    ->searchable()
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Student Name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('email'),
            ])
            ->headerActions([
                Action::make('enroll_students')
                    ->label('Enroll Students')
                    ->modalHeading('Enroll Multiple Students')
                    ->modalDescription('Select one or more students to enroll in this course.')
                    ->form([
                        Select::make('student_ids')
                            ->label('Students')
                            ->multiple()
                            ->required()
                            ->options(fn() => $this->getAvailableStudentsOptions()),
                    ])
                    ->action(function (array $data, RelationManager $livewire) {
                        /** @var \App\Models\Course $course */
                        $course = $this->getOwnerRecord();

                        // Make sure we have an array of IDs
                        $studentIds = $data['student_ids'] ?? [];

                        // Attach without duplicating (syncWithoutDetaching)
                        $course->students()->syncWithoutDetaching($studentIds);
                    })
                    ->modalWidth('xl'),
            ])
            ->actions([
                DetachAction::make('unenroll')
                    ->label('Unenroll')
                    ->requiresConfirmation()
                    ->color('danger'),
            ])
            ->bulkActions([
                DetachBulkAction::make('bulk_unenroll')
                    ->label('Unenroll Selected')
                    ->color('danger')
                    ->requiresConfirmation(),
            ]);
    }

    /**
     * Return options for the students select, excluding already enrolled students.
     *
     * @return array [id => name]
     */
    protected function getAvailableStudentsOptions(): array
    {
        $course = $this->getOwnerRecord();

        // get already enrolled ids
        $enrolledIds = $course->students()->pluck('users.id')->toArray();

        // adjust the query according to your role implementation:
        // If you're using Spatie roles:
        $query = User::query()->whereHas('roles', fn($q) => $q->where('name', 'student'));

        // If you store role in users table directly, use:
        // $query = User::query()->where('role', 'student');

        if (!empty($enrolledIds)) {
            $query->whereNotIn('id', $enrolledIds);
        }

        return $query->pluck('name', 'id')->toArray();
    }
}