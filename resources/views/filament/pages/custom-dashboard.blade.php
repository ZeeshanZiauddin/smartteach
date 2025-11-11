<x-filament::page>
    <style>
        /* ✅ Custom dashboard layout overrides */
        .dashboard-wrapper {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .dashboard-top {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1rem;
        }

        .dashboard-main {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }

        /* ✅ Responsive layout for large screens */
        @media (min-width: 1024px) {
            .dashboard-main {
                grid-template-columns: repeat(3, 1fr);
            }

            .dashboard-left {
                grid-column: span 2 / span 2;
                display: flex;
                flex-direction: column;
                gap: 1.5rem;
            }

            .dashboard-right {
                display: flex;
                flex-direction: column;
                gap: 1.5rem;
            }
        }

        /* Optional: Add smooth widget spacing */
    </style>

    <div class="dashboard-wrapper">
        {{-- ✅ Top full-width section --}}
        <div class="dashboard-top">
            @foreach ($this->getTopWidgets() as $widget)

                @if ($widget::canView())
                    <div class="filament-widgets">
                        @livewire($widget)
                    </div>
                @endif

            @endforeach
        </div>

        {{-- ✅ Main grid layout --}}
        <div class="dashboard-main">
            {{-- 🧩 Left: 2 columns --}}
            <div class="dashboard-left">
                @foreach ($this->getLeftWidgets() as $widget)

                    @if ($widget::canView())
                        <div class="filament-widgets">
                            @livewire($widget)
                        </div>
                    @endif

                @endforeach
            </div>

            {{-- 🧩 Right: 1 column --}}
            <div class="dashboard-right">
                @foreach ($this->getRightWidgets() as $widget)

                    @if ($widget::canView())
                        <div class="filament-widgets">
                            @livewire($widget)
                        </div>
                    @endif

                @endforeach
            </div>
        </div>
    </div>
</x-filament::page>