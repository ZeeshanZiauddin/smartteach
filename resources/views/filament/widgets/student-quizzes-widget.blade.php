<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">{{ static::$heading }}</x-slot>
        <x-slot name="description">{{ static::$description }}</x-slot>

        <style>
            .quiz-container {
                display: grid;
                gap: 16px;
            }

            .quiz-card {
                padding: 16px;
                background: #fff;
                border: 1px solid #e5e5e5;
                border-radius: 12px;
                box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
            }

            .quiz-card-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 8px;
            }

            .quiz-card-title {
                font-weight: 600;
                font-size: 16px;
                color: #1f2937;
            }

            .quiz-card-course,
            .quiz-card-dates {
                font-size: 12px;
                color: #6b7280;
            }

            .quiz-card-link a {
                color: #FFB900;
                font-weight: 600;
                text-decoration: none;
            }

            .quiz-card-link a:hover {
                text-decoration: underline;
            }

            .no-quizzes {
                text-align: center;
                color: #6b7280;
                font-size: 14px;
            }

            .quiz-results {
                background: #f0fdf4;
                border: 1px solid #d1fae5;
                padding: 12px;
                border-radius: 8px;
                font-size: 13px;
            }
        </style>

        <div class="space-y-16">
            {{-- Pending Quizzes --}}
            @if ($pending->isNotEmpty())
                <h3 style="margin:10px 0px; color:#6b7280; font-weight: bold;"> Pending Quizzes</h3>
                <div class="quiz-container">
                    @foreach ($pending as $quiz)
                        <div class="quiz-card">
                            <div class="quiz-card-header">
                                <div class="quiz-card-title">{{ $quiz['title'] }}</div>
                                <div class="quiz-card-course">{{ $quiz['course'] }}</div>
                            </div>
                            <div class="quiz-card-dates">🕒 {{ $quiz['start_at'] }} → {{ $quiz['end_at'] }}</div>
                            <div class="quiz-card-link" style="text-align: right;">
                                <a href="{{ url('admin/take-quiz/' . $quiz['id']) }}">Take Quiz</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Completed Quizzes --}}
            @if ($completed->isNotEmpty())
                <h3 style="margin:10px 0px; color:#6b7280; font-weight: bold;">Completed Quizzes</h3>
                <div class="quiz-container">
                    @foreach ($completed as $quiz)
                        <div class="quiz-card">
                            <div class="quiz-card-header">
                                <div class="quiz-card-title">{{ $quiz['title'] }}</div>
                                <div class="quiz-card-course">{{ $quiz['course'] }}</div>
                            </div>
                            <div class="quiz-card-dates">🕒 {{ $quiz['start_at'] }} → {{ $quiz['end_at'] }}</div>
                            <div class="quiz-results">
                                ✅ Correct Answers: {{ $quiz['correctQuestions'] ?? 0 }}/{{$quiz['totalQuestions'] ?? 0  }}<br>
                                🏆 Marks: {{ $quiz['totalMarks'] ?? 0 }}/{{ $quiz['obtainedmarks'] ?? 0 }}<br>
                                📊 Percentage: {{ $quiz['percentage'] ?? 0 }}%
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            @if ($pending->isEmpty() && $completed->isEmpty())
                <p class="no-quizzes">You are not enrolled in any quizzes yet.</p>
            @endif
        </div>
    </x-filament::section>
</x-filament-widgets::widget>