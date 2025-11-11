<x-filament-panels::page>
    <div class="space-y-6">
        <h2 class="text-2xl font-bold text-gray-800">
            🧠 {{ $this->quiz->title }}
        </h2>


        @if ($this->submitted)
            <div class="p-6 bg-green-50 border border-green-200 rounded-xl space-y-2">
                <h3 class="text-xl font-semibold text-green-800">🎉 Quiz Results</h3>
                <p>✅ Correct Answers: <strong>{{ $this->correctCount }}/{{ $this->totalQuestions }}</strong></p>
                <p>🏆 Marks Earned: <strong>{{ $this->earnedMarks }}/{{ $this->totalMarks }}</strong></p>
                <p>📊 Percentage: <strong>{{ round(($this->earnedMarks / $this->totalMarks) * 100, 2) }}%</strong></p>
            </div>
        @else
            <form wire:submit.prevent="submit" class="space-y-8">
                {{ $this->form }}

                <div class="flex justify-end" style="margin-top: 12px">


                    {{-- Submit Button --}}
                    <x-filament::button type="submit" :disabled="count(array_filter($this->data['answers'] ?? [])) !== $this->totalQuestions">
                        <x-filament::loading-indicator wire:loading class="h-5 w-5" />
                        Submit Quiz
                    </x-filament::button>
                </div>
            </form>
        @endif
    </div>
</x-filament-panels::page>