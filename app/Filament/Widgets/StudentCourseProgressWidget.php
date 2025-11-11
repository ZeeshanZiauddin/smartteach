<?php

namespace App\Filament\Widgets;

use App\Models\Course;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class StudentCourseProgressWidget extends Widget
{
    protected string $view = 'filament.widgets.student-course-progress-widget';

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = '📚 My Courses';

    public static function canView(): bool
    {
        $user = Auth::user();
        return $user && $user->hasRole('student');
    }

    public function getCourses()
    {
        $student = Auth::user();

        return Course::query()
            ->whereHas('students', function ($query) use ($student) {
                $query->where('student_id', $student->id);
            })
            ->with('teacher')
            ->get()
            ->map(function ($course) {
                $now = Carbon::now();
                $from = Carbon::parse($course->from_date);
                $to = Carbon::parse($course->to_date);

                $totalDays = max($from->diffInDays($to), 1);
                $passedDays = max($from->diffInDays(min($now, $to)), 0);
                $progress = min(100, round(($passedDays / $totalDays) * 100, 1));

                return [
                    'title' => $course->title,
                    'teacher' => $course->teacher->name ?? 'N/A',
                    'from' => $from->toFormattedDateString(),
                    'to' => $to->toFormattedDateString(),
                    'progress' => $progress,
                ];
            });
    }
}