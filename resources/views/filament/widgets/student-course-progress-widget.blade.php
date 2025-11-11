<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">{{ static::$heading }}</x-slot>

        <style>
            .course-container {
                display: grid;
                gap: 20px;
                margin-top: 15px;
            }

            .course-card {
                background: #fff;
                border: 1px solid #e5e7eb;
                border-radius: 12px;
                padding: 16px;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
                transition: transform 0.2s ease;
            }

            .course-card:hover {
                transform: translateY(-2px);
            }

            .course-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 8px;
            }

            .course-title {
                font-size: 1.1rem;
                font-weight: 600;
                color: #222;
            }

            .course-date {
                font-size: 0.85rem;
                color: #6b7280;
            }

            .course-teacher {
                font-size: 0.9rem;
                color: #555;
                margin-bottom: 10px;
            }

            .progress-container {
                width: 100%;
                height: 10px;
                background-color: #e5e7eb;
                border-radius: 5px;
                overflow: hidden;
                margin-top: 6px;
            }

            .progress-bar {
                height: 100%;
                background: linear-gradient(90deg, #ff7300, #FFB900);
                transition: width 0.5s ease;
            }

            .progress-label {
                text-align: right;
                font-size: 0.85rem;
                margin-top: 4px;
                color: #444;
                font-weight: 500;
            }

            /* Responsive */
            @media (max-width: 768px) {
                .course-header {
                    flex-direction: column;
                    align-items: flex-start;
                    gap: 4px;
                }
            }
        </style>

        @php
            $courses = $this->getCourses();
        @endphp

        @if ($courses->isEmpty())
            <p style="text-align:center; color:#777;">You are not enrolled in any courses yet.</p>
        @else
            <div class="course-container">
                @foreach ($courses as $course)
                    <div class="course-card">
                        <div class="course-header">
                            <h3 class="course-title">{{ $course['title'] }}</h3>
                            <span class="course-date">{{ $course['from'] }} → {{ $course['to'] }}</span>
                        </div>

                        <p class="course-teacher">👨‍🏫 {{ $course['teacher'] }}</p>

                        <div class="progress-container">
                            <div class="progress-bar" style="width: {{ $course['progress'] }}%;"></div>
                        </div>
                        <div class="progress-label">{{ $course['progress'] }}%</div>
                    </div>
                @endforeach
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>