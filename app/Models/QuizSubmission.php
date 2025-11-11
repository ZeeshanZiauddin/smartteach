<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_id',
        'user_id',
        'answers',
        'score',
        'submitted_at',
    ];

    protected $casts = [
        'answers' => 'array',
    ];

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Computed property: total correct answers
    public function getCorrectCountAttribute()
    {
        $correct = 0;

        if (!$this->quiz || empty($this->answers))
            return 0;

        foreach ($this->answers as $questionId => $optionId) {
            $question = $this->quiz->questions()->find($questionId);
            if ($question && $question->correctOption && $question->correctOption->id == $optionId) {
                $correct++;
            }
        }

        return $correct;
    }

    // Computed property: earned marks
    public function getEarnedMarksAttribute()
    {
        $marks = 0;

        if (!$this->quiz || empty($this->answers))
            return 0;

        foreach ($this->answers as $questionId => $optionId) {
            $question = $this->quiz->questions()->find($questionId);
            if ($question && $question->correctOption && $question->correctOption->id == $optionId) {
                $marks += $question->marks;
            }
        }

        return $marks;
    }

    // Computed property: percentage
    public function getPercentageAttribute()
    {
        $totalMarks = $this->quiz ? $this->quiz->questions->sum('marks') : 0;
        return $totalMarks > 0 ? round(($this->earned_marks / $totalMarks) * 100, 2) : 0;
    }
    // Computed property: total marks of the quiz
    public function getTotalMarksAttribute()
    {
        return $this->quiz ? $this->quiz->questions->sum('marks') : 0;
    }

    // Computed property: total number of questions
    public function getTotalQuestionsAttribute()
    {
        return $this->quiz ? $this->quiz->questions->count() : 0;
    }
}