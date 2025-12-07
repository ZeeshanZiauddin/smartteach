<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CourseEnrollmentLink extends Model
{
    protected $fillable = [
        'course_id',
        'token',
        'max_uses',
        'used_count',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public static function generateForCourse($courseId, $maxUses = null, $expiry = null)
    {
        return self::create([
            'course_id' => $courseId,
            'token' => Str::uuid(),
            'max_uses' => $maxUses,
            'expires_at' => $expiry,
        ]);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function isExpired(): bool
    {
        return $this->expires_at && now()->greaterThan($this->expires_at);
    }

    public function isLimitReached(): bool
    {
        return $this->max_uses !== null && $this->used_count >= $this->max_uses;
    }
}