<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseEnrollmentQR extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'course_id',
        'code',
        'type',
        'format',
        'size',
        'qr_url',
        'qr_options',
        'limit',
        'used',
    ];

    protected $casts = [
        'qr_options' => 'array', // Larazeus QR options stored as JSON
    ];

    // Relationships
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
    public static function generateQrUrl(string $code): string
    {
        return url('/enroll?code=' . $code);
    }

}