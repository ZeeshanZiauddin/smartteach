<x-filament-widgets::widget id="askAiWidget" style="position: relative;">
    <x-filament::section style=" height: 60vh;overflow:hidden">

        {{-- --- Custom Header Section --- --}}
        <x-slot name="heading">
            <div style="display: flex;justify-content:space-between">
                <div>
                    <div> {{ static::$heading }}</div>
                    <p style="font-size:14px; font-weight: 400;">{{ static::$description }}</p>
                </div>

                {{-- 🧠 Type Selector (in header) --}}
                <div class="flex items-center gap-2">
                    <x-filament::input.wrapper>
                        <x-filament::input.select wire:model.live="selectedType" class="text-sm">
                            <option value="quiz">🎯 Quiz</option>
                            <option value="assignment">📝 Assignment</option>
                            <option value="material">📚 Material</option>
                        </x-filament::input.select>
                    </x-filament::input.wrapper>
                </div>
            </div>
        </x-slot>
        <style>
            #askAiWidget .fi-section-content-ctn {
                height: 100%;
                background: #FAFAFA;
            }

            #askAiWidget .fi-section-content {
                height: 100%;
            }

            .question-box {
                width: 100%;
                position: absolute;
                bottom: 0;
                left: 0;
                padding: 24px;
                display: flex;
                gap: 1rem;
                background: #fff;
                border-bottom-left-radius: 24px;
                border-bottom-right-radius: 24px;
                box-shadow: rgba(0, 0, 0, 0) 0px 0px 0px 0px, rgba(0, 0, 0, 0) 0px 0px 0px 0px, rgba(0, 0, 0, 0) 0px 0px 0px 0px, oklab(0.141 0.00136333 -0.00481054 / 0.05) 0px 0px 0px 1px, rgba(0, 0, 0, 0.1) 0px 1px 3px 0px, rgba(0, 0, 0, 0.1) 0px 1px 2px -1px;
            }

            .answer-box {
                box-shadow: rgba(0, 0, 0, 0) 0px 0px 0px 0px, rgba(0, 0, 0, 0) 0px 0px 0px 0px, rgba(0, 0, 0, 0) 0px 0px 0px 0px, oklab(0.141 0.00136333 -0.00481054 / 0.05) 0px 0px 0px 1px, rgba(0, 0, 0, 0.1) 0px 1px 3px 0px, rgba(0, 0, 0, 0.1) 0px 1px 2px -1px
            }

            #askAiWidget .question-box .fi-input-wrp.main-input {
                flex-grow: 1
            }

            /* Ensure Markdown content looks good inside Filament */
            .filament-markdown {
                font-size: 0.95rem;
                line-height: 1.7;
            }

            /* Headings */
            .filament-markdown h1,
            .filament-markdown h2,
            .filament-markdown h3 {
                font-weight: 600;
                margin-top: 1rem;
                margin-bottom: 0.5rem;
            }

            /* Paragraphs */
            .filament-markdown p {
                margin-bottom: 0.75rem;
            }

            /* Code blocks */
            .filament-markdown pre {
                background-color: #1e1e1e;
                padding: 1rem;
                border-radius: 0.5rem;
                overflow-x: auto;
                margin-bottom: 1rem;
            }

            /* Inline code */
            .filament-markdown code {
                background-color: rgba(243, 244, 246, 0.1);
                color: #e11d48;
                padding: 0.2rem 0.4rem;
                border-radius: 0.25rem;
                font-family: 'Fira Code', monospace;
            }

            /* Tables */
            .filament-markdown table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 1rem;
                margin-bottom: 1rem;
            }

            .filament-markdown th,
            .filament-markdown td {
                border: 1px solid #d1d5db;
                padding: 0.5rem 0.75rem;
                text-align: left;
            }

            .filament-markdown th {
                background-color: #f3f4f6;
                font-weight: 600;
            }

            /* Unordered Lists */
            .filament-markdown ul {
                list-style-type: disc;
                margin-left: 1.5rem;
                margin-bottom: 0.75rem;
            }

            .filament-markdown ul ul {
                list-style-type: circle;
            }

            /* Ordered Lists */
            .filament-markdown ol {
                list-style-type: decimal;
                margin-left: 1.5rem;
                margin-bottom: 0.75rem;
            }

            .filament-markdown ol ol {
                list-style-type: lower-alpha;
            }

            /* Blockquotes */
            .filament-markdown blockquote {
                border-left: 4px solid #6366f1;
                background-color: #f9fafb;
                padding: 0.75rem 1rem;
                margin: 1rem 0;
                color: #374151;
                font-style: italic;
            }

            /* Links */
            .filament-markdown a {
                color: #2563eb;
                text-decoration: underline;
            }

            .filament-markdown a:hover {
                color: #1d4ed8;
                text-decoration: none;
            }

            /* Horizontal rule */
            .filament-markdown hr {
                border: none;
                border-top: 1px solid #e5e7eb;
                margin: 1.5rem 0;
            }
        </style>

        <div class="border rounded-lg p-4 bg-gray-50 overflow-y-auto space-y-2"
            style="height: 40vh; overflow-y: scroll; padding-bottom: 100px;">
            @if (!empty($this->messages))
                @foreach ($this->messages as $msg)
                    <div
                        style=" margin-top: 16px; width:100%;display:flex;justify-content: {{ $msg['role'] === 'assistant' ? 'start' : 'end' }};">
                        <div style="width:70%;">
                            <div class="answer-box"
                                style="padding:1rem;border-radius:10px;background-color: {{ $msg['role'] === 'assistant' ? 'white' : '#FFB900' }};">
                                <div class="fi-section-content">
                                    <article class="filament-markdown">
                                        <x-markdown>
                                            {{-- {!! dd($msg['content'])!!} --}}
                                            {!! $msg['content']!!}
                                        </x-markdown>
                                    </article>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <p class="text-sm text-gray-500">Start the conversation by asking a question!</p>
            @endif
        </div>

        <div class="mt-3 flex gap-2 question-box">
            {{-- Dynamic item selector --}}
            <x-filament::input.wrapper>
                <x-filament::input.select wire:model.live="selectedItemId" wire:key="{{ $selectedType }}">
                    <option value="">Select {{ ucfirst($selectedType) }}</option>
                    @forelse ($this->items as $id => $title)
                        <option value="{{ $id }}">{{ $title }}</option>
                    @empty
                        <option value="" disabled>No {{ $selectedType }} available</option>
                    @endforelse
                </x-filament::input.select>
            </x-filament::input.wrapper>


            {{-- Question input --}}
            <x-filament::input.wrapper class="main-input">
                <x-filament::input wire:model="question" wire:keydown.enter="ask" placeholder="Type your question..." />
            </x-filament::input.wrapper>

            {{-- Send button --}}
            <x-filament::button wire:click="ask"
                style="color: black; display: flex; justify-content: center; align-items: center;">
                <x-heroicon-o-paper-airplane height="20" />
            </x-filament::button>
        </div>


    </x-filament::section>
</x-filament-widgets::widget>