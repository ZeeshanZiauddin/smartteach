<?php

namespace App\Filament\Widgets;

use App\Models\CourseMaterial;
use Filament\Actions\Action;
use Filament\Tables;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;

class TeacherCourseMaterials extends BaseWidget
{
    protected static ?string $heading = 'My Course Materials';
    protected static ?int $sort = 4;
    public static function canView(): bool
    {
        $user = auth()->user();

        // Make sure user is logged in and has the Shield role 'student'
        return $user && $user->hasRole('teacher');
    }

    public function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->query(
                CourseMaterial::query()
                    ->where('teacher_id', Auth::id())
                    ->latest()
            )
            ->columns([
                Tables\Columns\TextColumn::make('title')->label('Title')->searchable(),
                Tables\Columns\TextColumn::make('course.name')->label('Course'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Uploaded At')
                    ->dateTime()
                    ->sortable(),
            ])
            ->actions([
                Action::make('download')
                    ->label('Download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn($record) => asset('storage/' . $record->file))
                    ->openUrlInNewTab(),
            ])
            ->defaultPaginationPageOption(5);
    }
}