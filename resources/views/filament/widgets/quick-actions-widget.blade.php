<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">{{ static::$title }}</x-slot>
        <style>
            .actionRender {
                display: grid;
                grid-template-columns: 1fr;
                gap: 8px
            }
        </style>
        <div class="actionRender">
            @foreach ($this->getActions() as $action)
                {{ $action }}
            @endforeach
        </div>
    </x-filament::section>
</x-filament-widgets::widget>