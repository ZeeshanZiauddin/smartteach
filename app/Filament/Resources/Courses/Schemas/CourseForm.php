<?php

namespace App\Filament\Resources\Courses\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class CourseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required(),
                Select::make('user_id')
                    ->label('Teacher')
                    ->relationship(
                        name: 'teacher',
                        titleAttribute: 'name',
                        modifyQueryUsing: function ($query, $record) {
                            // Only users with teacher role
                            $query->whereHas('roles', fn($q) => $q->where('name', 'teacher'));
                        }
                    )
                    ->required()
                    ->searchable()
                    ->preload(),

                DatePicker::make('from_date')->native(false),
                DatePicker::make('to_date')->native(false),
                RichEditor::make('description')
                    ->columnSpanFull(),

            ]);
    }
}