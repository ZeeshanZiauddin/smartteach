<?php

namespace App\Filament\Widgets;

use App\Models\CourseMaterial;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

use Filament\Widgets\TableWidget;
use Illuminate\Support\Facades\Auth;

class StudentCourseMaterialsWidget extends TableWidget
{

    protected static ?string $heading = '💭 Ask AI Assistant';

    public static function canView(): bool
    {
        return Auth::user()?->hasRole('student');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(function () {
                $user = Auth::user();

                // Adjust this according to your student-course relationship
                // For example, if students are related via pivot `course_user` table:
                $courseIds = $user->enrolledCourses()->pluck('courses.id');

                return CourseMaterial::query()
                    ->whereIn('course_id', $courseIds)
                    ->with('course');
            })
            ->columns([

                TextColumn::make('title')
                    ->label('Material Title')
                    ->limit(40),


            ])
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->recordActions([
                Action::make('download')
                    ->label('Download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->iconButton()
                    ->color('primary')
                    ->url(fn($record) => asset('storage/' . $record->file))
                    ->openUrlInNewTab(),
            ])
            ->toolbarActions([
                // BulkActionGroup::make([
                //     //
                // ]),
            ]);
    }

    protected function getTableEmptyStateHeading(): ?string
    {
        return 'No materials available for your courses.';
    }

    protected function getTableEmptyStateDescription(): ?string
    {
        return 'Once your teacher uploads materials, they will appear here.';
    }
}