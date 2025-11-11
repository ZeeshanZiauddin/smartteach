<?php

namespace App\Filament\Resources\AssignmentSubmissions\Schemas;

use Filament\Forms;
use Filament\Schemas\Schema;

class AssignmentSubmissionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\Select::make('assignment_id')
                    ->relationship('assignment', 'title')
                    ->required()
                    ->searchable()
                    ->preload(),

                Forms\Components\Select::make('user_id')
                    ->label('Student')
                    ->relationship('user', 'name', function ($q) {
                        $q->whereHas('roles', fn($r) => $r->where('name', 'student'));
                    })
                    ->searchable()
                    ->required(),

                Forms\Components\FileUpload::make('file')
                    ->directory('AssignmentsSubmissions')
                    ->disk('public')
                    ->visibility('public')
                    ->acceptedFileTypes(['application/pdf'])
                    ->label('Upload Assignment')
                    ->required(),

                Forms\Components\Textarea::make('remarks')->nullable(),

                Forms\Components\TextInput::make('grade')->nullable(),

                Forms\Components\Select::make('status')
                    ->options([
                        'submitted' => 'Submitted',
                        'graded' => 'Graded',
                        'late' => 'Late',
                    ])
                    ->default('submitted'),
            ]);
    }
}