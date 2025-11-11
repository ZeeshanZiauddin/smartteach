<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use Spatie\PdfToText\Pdf;

class Assignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'user_id',
        'title',
        'description',
        'file',
        'due_date',
        'key_points',
    ];

    // 🔹 Relationships
    public function submissions()
    {
        return $this->hasMany(\App\Models\AssignmentSubmission::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function summary()
    {
        return $this->morphOne(Summary::class, 'summarizable');
    }

    // 🔹 Automatically attach user and generate summary
    protected static function booted()
    {
        static::creating(function ($assignment) {
            if (auth()->check()) {
                $assignment->user_id = auth()->id();
            }
        });

        static::created(function ($assignment) {
            $assignment->generateSummary();
        });
    }

    // 🔹 Generate summary using AI
    public function generateSummary()
    {
        try {
            $text = '';

            // 1️⃣ Extract text if file exists (PDF)
            if (!empty($this->file)) {
                $path = storage_path('app/public/' . $this->file);

                if (file_exists($path)) {
                    $pdf = new Pdf(config('pdf.binary'));
                    $text = $pdf->getText($path);
                }
            }

            // 2️⃣ Fallback: use title + description
            if (blank($text)) {
                $text = "Assignment Title: {$this->title}\nDescription: {$this->description}\nKey Points: {$this->key_points}";
            }

            // 3️⃣ Call Hugging Face (or switch to OpenAI)
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('HF_TOKEN'),
                'Content-Type' => 'application/json',
            ])->post('https://router.huggingface.co/v1/chat/completions', [
                        "model" => "openai/gpt-oss-20b:groq",
                        "stream" => false,
                        "messages" => [
                            [
                                'role' => 'system',
                                'content' => 'You are a text summarizer. Summarize the following assignment clearly and briefly but without losing important details.',
                            ],
                            [
                                'role' => 'user',
                                'content' => mb_substr($text, 0, 4000), // truncate to 4k chars
                            ],
                        ],
                    ]);

            $summaryText = $response->json('choices.0.message.content') ?? 'No summary generated.';

            // 4️⃣ Save summary to DB
            $this->summary()->create([
                'summary' => $summaryText,
            ]);

        } catch (\Throwable $e) {
            \Log::error('Assignment summary generation failed: ' . $e->getMessage());
        }
    }
}