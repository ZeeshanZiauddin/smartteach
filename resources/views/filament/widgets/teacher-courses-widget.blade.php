<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">{{ static::$heading }}</x-slot>

        <style>
            .teacher-course-container {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
                gap: 20px;
                margin-top: 10px;
            }

            .teacher-course-card {
                background: #FAFAFA;
                border: 1px solid #e5e7eb;
                border-radius: 12px;
                padding: 12px;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
                transition: transform 0.2s ease, box-shadow 0.2s ease;
                display: flex;
                justify-content: space-between;
                align-items: center
            }

            .teacher-course-title {
                font-size: 1.1rem;
                font-weight: 600;
                color: #222;
            }

            .teacher-course-dates {
                font-size: 0.85rem;
                color: #6b7280;
            }

            .teacher-course-students {
                font-size: 0.9rem;
                color: #444;
            }

            .teacher-course-status {
                font-size: 0.9rem;
                font-weight: 600;
                padding: 4px 10px;
                border-radius: 20px;
                display: inline-block;
                color: #fff;
                height: fit-content;
            }

            .status-active {
                background-color: #ddf0ea;
                color: #444;
            }

            .status-inactive {
                background-color: #ef4444;
            }

            @media (max-width: 768px) {
                .teacher-course-header {
                    flex-direction: column;
                    align-items: flex-start;
                }
            }
        </style>

        @php
            $courses = $this->getCourses();
        @endphp

        @if ($courses->isEmpty())
            <p style="text-align:center; color:#777;">You haven't created any courses yet.</p>
        @else
            <div class="teacher-course-container">
                @foreach ($courses as $course)
                    <div class="teacher-course-card">
                        <div class="teacher-course-header">
                            <h3 class="teacher-course-title">{{ $course['title'] }}</h3>
                            <p class="teacher-course-students">{{ $course['students'] }} Students </p>
                        </div>



                        <span
                            class="teacher-course-status {{ $course['status'] === 'Active' ? 'status-active' : 'status-inactive' }}">
                            {{ $course['status'] }}
                        </span>
                    </div>
                @endforeach
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>