<?php

namespace App\Filament\Widgets;

use App\Models\Course;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class TeacherCoursesWidget extends Widget
{
    protected string $view = 'filament.widgets.teacher-courses-widget';
    protected static ?int $sort = 1;
    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'My Courses';

    public static function canView(): bool
    {
        $user = Auth::user();
        return $user && $user->hasRole('teacher');
    }

    public function getCourses()
    {
        $teacherId = Auth::id();

        return Course::withCount('students')
            ->where('user_id', $teacherId)
            ->get()
            ->map(function ($course) {
                $today = now()->startOfDay();
                $toDate = $course->to_date ? \Carbon\Carbon::parse($course->to_date) : null;

                return [
                    'title' => $course->title,
                    'students' => $course->students_count,
                    'from' => $course->from_date ? \Carbon\Carbon::parse($course->from_date)->format('M d, Y') : 'N/A',
                    'to' => $toDate ? $toDate->format('M d, Y') : 'N/A',
                    'status' => $toDate && $toDate->isPast() ? 'Inactive' : 'Active',
                ];
            });
    }
}