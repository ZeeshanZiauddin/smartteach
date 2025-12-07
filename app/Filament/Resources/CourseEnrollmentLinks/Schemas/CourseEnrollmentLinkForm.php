<?php

namespace App\Filament\Resources\CourseEnrollmentLinks\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class CourseEnrollmentLinkForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('course_id')
                    ->relationship('course', 'title')
                    ->required(),

                TextInput::make('max_uses')
                    ->numeric()
                    ->nullable()
                    ->label('Max Students'),

                DateTimePicker::make('expires_at')
                    ->nullable()->native(false),

                Hidden::make('token')
                    ->default(fn() => (string) Str::uuid()),
            ]);
    }
}