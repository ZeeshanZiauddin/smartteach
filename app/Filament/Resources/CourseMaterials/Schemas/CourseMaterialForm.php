<?php

namespace App\Filament\Resources\CourseMaterials\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms;
use Illuminate\Support\Facades\Auth;
class CourseMaterialForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\Hidden::make('teacher_id')
                    ->default(fn() => Auth::id()),

                Forms\Components\Hidden::make('course_id')
                    ->default(function () {
                        return \App\Models\Course::where('user_id', Auth::id())->value('id');
                    }),

                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Textarea::make('description')
                    ->maxLength(1000)
                    ->columnSpanFull(),

                Forms\Components\FileUpload::make('file')
                    ->label('Material File')
                    ->directory('course_materials')
                    ->preserveFilenames()
                    ->required()
                    ->columnSpanFull(),
            ]);
    }
}