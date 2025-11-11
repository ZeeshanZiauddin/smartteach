<?php

namespace App\Filament\Resources\Quizzes\Schemas;

use App\Models\Course;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Icetalker\FilamentTableRepeater\Forms\Components\TableRepeater;

class QuizForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->schema([
            Wizard::make([
                // ────────────────
                // STEP 1: QUIZ DETAILS
                // ────────────────
                Step::make('Quiz Details')
                    ->icon('heroicon-o-clipboard-document')
                    ->schema([
                        Section::make('Quiz Information')
                            ->schema([

                                Hidden::make('user_id')
                                    ->default(fn() => auth()->id())
                                    ->afterStateHydrated(function (callable $set, $state) {
                                        if ($state) {
                                            $firstCourse = Course::where('user_id', $state)->first();
                                            $set('course_id', $firstCourse?->id);
                                        }
                                    }),

                                Hidden::make('course_id')
                                    ->default(fn() => Course::where('user_id', auth()->id())->first()?->id),

                                Forms\Components\TextInput::make('title')
                                    ->label('Quiz Title')
                                    ->required()
                                    ->columnSpanFull(),

                                Forms\Components\RichEditor::make('description')
                                    ->label('Description')
                                    ->columnSpanFull(),

                                Forms\Components\DateTimePicker::make('start_at')
                                    ->label('Start Time')
                                    ->native(false),

                                Forms\Components\DateTimePicker::make('end_at')
                                    ->label('End Time')
                                    ->native(false),

                                Forms\Components\TextInput::make('duration')
                                    ->numeric()
                                    ->suffix('minutes')
                                    ->nullable(),
                                Forms\Components\TagsInput::make('topic')
                                    ->label('Quiz Topics')
                                    ->placeholder('Add quiz topics or tags')
                                    ->required()
                            ])
                            ->columns(2),
                    ]),

                // ────────────────
                // STEP 2: QUESTIONS
                // ────────────────
                Step::make('Questions')
                    ->icon('heroicon-o-question-mark-circle')
                    ->schema([
                        Section::make('Add Questions')
                            ->schema([
                                Forms\Components\Repeater::make('questions')
                                    ->relationship() // binds to QuizQuestion
                                    ->schema([
                                        Forms\Components\TextInput::make('question_text')
                                            ->label('Question')
                                            ->required(),

                                        Forms\Components\TextInput::make('marks')
                                            ->label('Marks')
                                            ->numeric()
                                            ->default(1)
                                            ->required(),

                                        // Table Repeater for options
                                        TableRepeater::make('options')
                                            ->label('Options')
                                            ->relationship()
                                            ->schema([
                                                Forms\Components\TextInput::make('option_text')
                                                    ->label('Option')
                                                    ->required(),

                                                Forms\Components\Toggle::make('is_correct')
                                                    ->label('Correct')
                                                    ->default(false),
                                            ])
                                            ->defaultItems(2)
                                            ->createItemButtonLabel('Add Option')
                                            ->columns(2)
                                            ->columnSpan('full'),
                                    ])
                                    ->createItemButtonLabel('Add Question')
                                    ->orderable()
                                    ->collapsible(),
                            ]),
                    ]),
            ])
            ,
        ]);
    }
}