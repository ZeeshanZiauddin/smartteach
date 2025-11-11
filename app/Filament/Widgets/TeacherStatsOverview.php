<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class TeacherStatsOverview extends StatsOverviewWidget
{
    public static function canView(): bool
    {
        $user = Auth::user();
        return $user && $user->hasRole('teacher');
    }

    public function getColumns(): int|array
    {
        return [
            'md' => 4,
            'xl' => 5,
        ];
    }
    protected function getStats(): array
    {

        return [
            Stat::make('Total Studnets', number_format($this->getTotalStudents()))
                ->description('Students of all courses')
                ->color('primary')
                ->descriptionIcon('heroicon-m-user-group'),
            Stat::make('Active Courses', $this->getNumberOfCourses())
                ->description('Courses assigned')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('info'),

            Stat::make('Average time on page', '3:12')
                ->description('3% increase')
                ->color('secondary')
                ->descriptionIcon('heroicon-m-arrow-trending-up'),
            Stat::make('Average time on page', '3:12')
                ->description('3% increase')
                ->color('secondary')
                ->descriptionIcon('heroicon-m-arrow-trending-up'),
            Stat::make('Average time on page', '3:12')
                ->description('3% increase')
                ->color('secondary')
                ->descriptionIcon('heroicon-m-arrow-trending-up'),
        ];
    }

    // Get Total Nomber of students
    protected function getTotalStudents(): int
    {
        $user = Auth::user();
        return $user->courses()
            ->withCount('students')
            ->get()
            ->sum('students_count');
    }

    // Get number of courses of current user
    protected function getNumberOfCourses(): int
    {
        $user = Auth::user();
        return $user->courses()->count();
    }

}