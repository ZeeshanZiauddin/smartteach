<x-filament-widgets::widget>
    <x-filament::section>

        <x-slot name="heading">{{ static::$heading }}</x-slot>
        <x-slot name="description">{{ static::$description }}</x-slot>


        <form wire:submit.prevent="submit">
            {{ $this->form }}

            <div style="margin-top: 16px ">
                <x-filament::button style="width: 100%" type="submit">
                    Upload Material
                </x-filament::button>
            </div>
        </form>
    </x-filament::section>
</x-filament-widgets::widget>