<x-filament::page style="height: 80vh">
    <style>
        .fi-page-main {
            height: 100vh !important;
        }

        .fi-page-main .fi-page-content {
            height: 100%;
        }
    </style>
    <div class="space-y-4 " style="position: relative; height: 100%;">
        <div class="p-4 rounded-lg bg-white shadow">
            <h2 class="text-xl font-bold">{{ $quiz->title }}</h2>
            <p class="text-gray-600">{!!  $quiz->description !!}</p>
            <p class="text-sm text-gray-400 mt-1">
                {{ optional($quiz->start_at)->format('d M Y H:i') }} →
                {{ optional($quiz->end_at)->format('d M Y H:i') }}
            </p>
        </div>

        <div class="border rounded-lg p-4 bg-gray-50 overflow-y-auto space-y-2" style="height:80vh">
            @foreach($messages as $msg)
                <div
                    style="margin-top: 16px; width:100%;display:flex; justify-content: {{ $msg['role'] === 'assistant' ? 'start' : 'end' }};">
                    <div class="fi-section" style="width:70%">
                        <div class="fi-section-content-ctn">
                            <div class="fi-section-content">
                                {{-- {!! nl2br(e()) !!} --}}
                                {{-- @markdown($msg['content']) --}}
                                {{ Illuminate\Mail\Markdown::parse($msg['content']) }}
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div style="position:sticky; bottom: 100px;left:100px">
            <form wire:submit.prevent="ask" class="flex space-x-2">
                <x-filament::input.wrapper style="flex-grow: 1">
                    <x-filament::input wire:model.defer="question" placeholder="Ask a question about this quiz..." />
                </x-filament::input.wrapper>
                <x-filament::button type="submit" icon="heroicon-o-paper-airplane" style="margin-left: 20px">
                    Send
                </x-filament::button>
            </form>
        </div>
    </div>
</x-filament::page>