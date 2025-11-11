<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\AdvancedTeacherStatsOverviewWidget;
use App\Filament\Widgets\AskAiWidget;
use App\Filament\Widgets\TeacherStatsOverview;
use App\Filament\Widgets\UploadCourseMaterial;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Filament\Widgets;
use BackedEnum;
class CustomDashboard extends Page
{
    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-home';
    protected string $view = 'filament.pages.custom-dashboard';
    protected static ?string $navigationLabel = 'Dashboard';
    public function getTitle(): string
    {
        $user = Auth::user();
        return 'Welcome ' . ($user?->name ?? 'User');
    }

    public function getSubheading(): ?string
    {
        $user = Auth::user();

        if (!$user) {
            return 'Please log in to access your dashboard.';
        }

        // Customize by role
        if ($user->hasRole('admin')) {
            return 'Manage users, courses, and materials across the platform.';
        }

        if ($user->hasRole('teacher')) {
            return 'Manage your students & courses Gracefully.';
        }

        if ($user->hasRole('student')) {
            return 'Access materials shared by your teachers and track your progress.';
        }

        // Default fallback
        return 'Welcome to your learning dashboard.';
    }

    public function getTopWidgets(): array
    {
        return [
            TeacherStatsOverview::class,

        ];
    }

    /** 
     * 🧱 Widgets for the left section (2 columns)
     */
    public function getLeftWidgets(): array
    {
        return [
            AskAiWidget::class,
            UploadCourseMaterial::class,
            \App\Filament\Widgets\StudentCourseProgressWidget::class,

        ];
    }

    /** 
     * 🧱 Widgets for the right section (1 column)
     */
    public function getRightWidgets(): array
    {
        return [
            Widgets\AccountWidget::class,
            \App\Filament\Widgets\StudentCourseMaterialsWidget::class,
            \App\Filament\Widgets\StudentQuizzesWidget::class,
            \App\Filament\Widgets\UpcomingAssignments::class,
            \App\Filament\Widgets\TeacherCoursesWidget::class,


            // e.g. NotificationsWidget, etc.


            \App\Filament\Widgets\QuickActionsWidget::class,

        ];
    }

}