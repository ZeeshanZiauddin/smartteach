<?php

namespace App\Filament\Resources\Assignments\Schemas;

use App\Models\Course;
use Asmit\FilamentUpload\Enums\PdfViewFit;
use Asmit\FilamentUpload\Forms\Components\AdvancedFileUpload;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;

class AssignmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Wizard::make([
                    // 🟢 Step 1 — Basic Info
                    Step::make('Assignment Details')
                        ->icon('heroicon-o-book-open')
                        ->schema([
                            Hidden::make('user_id')
                                ->default(fn() => auth()->id())
                                ->afterStateHydrated(function ($set, $state) {
                                    if ($state) {
                                        $firstCourse = Course::where('user_id', $state)->first();
                                        $set('course_id', $firstCourse?->id);
                                    }
                                }),

                            Hidden::make('course_id')
                                ->default(fn() => Course::where('user_id', auth()->id())->first()?->id),
                            Forms\Components\TextInput::make('title')
                                ->required()
                                ->maxLength(255),
                            Forms\Components\RichEditor::make('description')

                                ->nullable(),
                        ]),

                    // 🟣 Step 2 — Description & File
                    Step::make('File')
                        ->icon('heroicon-o-document-arrow-up')
                        ->schema([


                            AdvancedFileUpload::make('file')
                                ->label('Upload PDF')

                                ->disk('public')
                                ->visibility('public')
                                ->directory('assignments')
                        ]),

                    // 🔵 Step 3 — Due Date & Key Points
                    Step::make('Schedule & Key Points')
                        ->icon('heroicon-o-calendar-days')
                        ->schema([
                            Forms\Components\DatePicker::make('due_date')
                                ->label('Due Date')
                                ->required(),

                            Forms\Components\Textarea::make('key_points')
                                ->label('Key Points / Instructions')
                                ->rows(3)
                                ->nullable(),
                        ]),
                ])
            ]);
    }
}