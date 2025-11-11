<?php

// app/Models/CourseMaterial.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use Spatie\PdfToText\Pdf;

class CourseMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'teacher_id',
        'title',
        'description',
        'file',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function summary()
    {
        return $this->morphOne(Summary::class, 'summarizable');
    }

    protected static function booted()
    {
        static::creating(function ($material) {
            if (auth()->check()) {
                $material->teacher_id = auth()->id();
                $material->course_id = \App\Models\Course::where('user_id', auth()->id())->value('id');
            }
        });

        static::created(function ($material) {
            $material->generateSummary();
        });
    }

    public function generateSummary()
    {
        try {
            // 1️⃣ Extract text using Spatie
            // $path = storage_path('app/public/' . $this->file);
            // dd(file_exists($path), $path);

            // $text = Pdf::getText($path);
            // dd($text);
            $path = storage_path('app/public/' . $this->file);

            $pdf = new Pdf(config('pdf.binary'));
            $text = $pdf->getText($path);
            // dd($text);
            if (!$text)
                return;


            // 2️⃣ Call Hugging Face summarization API
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('HF_TOKEN'),
                'Content-Type' => 'application/json',
            ])->post('https://router.huggingface.co/v1/chat/completions', [
                        "model" => "openai/gpt-oss-20b:groq",
                        "stream" => false,
                        "messages" => [
                            [
                                'role' => 'system',
                                'content' => 'You are a text summarizer. Summarize the following PDF content briefly keep it short but dont lose detail',
                            ],
                            [
                                'role' => 'user',
                                'content' => $text, // limit to 4k chars
                            ],
                        ],
                    ]);

            $summaryText = $response->json('choices.0.message.content') ?? 'No summary generated.';
            //dd($summaryText);
            // 3️⃣ Store summary
            $this->summary()->create([
                'summary' => $summaryText,
            ]);

        } catch (\Exception $e) {
            \Log::error('Summary generation failed: ' . $e->getMessage());
        }
    }
}