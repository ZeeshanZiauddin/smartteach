<div>
    <form wire:submit="save">
        {{ $this->form }}

        <div class="fi-form-actions " style="display: flex;justify-content:end; margin-top: 2rem;">
            <div class="fi-ac fi-align-end">
                <x-filament::button type="submit">
                    {{ __('filament-edit-profile::default.save') }}
                </x-filament::button>
            </div>
        </div>
    </form>

    <x-filament-actions::modals />
</div>