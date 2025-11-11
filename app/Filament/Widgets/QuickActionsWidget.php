<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Auth;

class QuickActionsWidget extends Widget
{
    protected static ?string $title = '💭 Quick Actions ';

    protected string $view = 'filament.widgets.quick-actions-widget';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 5;
    public function getActions(): array
    {
        $user = Auth::user();

        if ($user->hasRole('admin')) {
            return [
                Action::make('manageUsers')
                    ->label('Manage Users')
                    ->icon('heroicon-o-users')
                    ->color('secondary')
                    ->badge()
                ,

                Action::make('viewReports')
                    ->label('View Reports')
                    ->icon('heroicon-o-chart-bar')
                    ->color('secondary')
                    ->badge()
                ,
            ];
        }

        if ($user->hasRole('teacher')) {
            return [
                Action::make('uploadMaterial')
                    ->label('Upload Material')
                    ->icon('heroicon-o-cloud-arrow-up')
                    ->color('secondary')
                    ->badge()
                ,

                Action::make('viewSubmissions')
                    ->label('View Submissions')
                    ->icon('heroicon-o-folder-open')
                    ->color('secondary')
                    ->badge()
                ,
            ];
        }

        if ($user->hasRole('student')) {
            return [
                Action::make('viewCourses')
                    ->label('My Courses')
                    ->icon('heroicon-o-academic-cap')
                    ->badge()
                    ->color('secondary')
                ,

                Action::make('downloadMaterials')
                    ->label('Download Materials')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->badge()
                    ->color('secondary')
                ,
            ];
        }

        return [];
    }
}