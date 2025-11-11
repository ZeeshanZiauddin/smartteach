<div class="space-y-2 max-h-96 overflow-y-auto p-2">
    @php
        $messages = $getState() ?? [];
        // dd($messages);
    @endphp

    @if (!empty($messages))
        @foreach ($messages as $msg)
            <div class="{{ $msg['role'] === 'user' ? 'text-right' : 'text-left' }}">
                <div
                    class="inline-block px-3 py-2 rounded-2xl {{ $msg['role'] === 'user' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-800' }}">
                    {!! nl2br(e($msg['content'])) !!}
                </div>
            </div>
        @endforeach
    @else
        <p class="text-sm text-gray-500">Start the conversation by asking a question!</p>
    @endif
</div>