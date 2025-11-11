<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kirschbaum\Commentions\Contracts\Commentable;
use Kirschbaum\Commentions\HasComments;

class Quiz extends Model implements Commentable
{
    use HasFactory, HasComments;

    protected $fillable = [
        'course_id',
        'user_id',
        'title',
        'description',
        'start_at',
        'end_at',
        'duration',
        'topic',
    ];
    protected $casts = [
        'topic' => 'array', // important for TagsInput
    ];
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function questions()
    {
        return $this->hasMany(QuizQuestion::class);
    }

    public function submissions()
    {
        return $this->hasMany(QuizSubmission::class);
    }
}