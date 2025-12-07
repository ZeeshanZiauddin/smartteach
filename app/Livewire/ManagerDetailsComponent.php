<?php

namespace App\Livewire;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Livewire\Component;
use Illuminate\Contracts\View\View;
use Joaopaulolndev\FilamentEditProfile\Concerns\HasSort;

class ManagerDetailsComponent extends Component implements HasForms
{
    use InteractsWithForms;
    use HasSort;

    public ?array $data = [];

    protected static int $sort = 0;
    public function mount(): void
    {
        $profile = auth()->user()->managerProfile;

        if ($profile) {
            $this->form->fill([
                'phone' => $profile->phone,
                'secondary_phone' => $profile->secondary_phone,
                'whatsapp' => $profile->whatsapp,
                'address' => $profile->address,
            ]);
        }
    }

    public function form($form)
    {
        return $form
            ->schema([
                Section::make('Manager Profile')
                    ->aside()
                    ->description('Update your contact information')
                    ->schema([
                        TextInput::make('phone')
                            ->label('Phone')
                            ->tel()
                            ->maxLength(20),

                        TextInput::make('secondary_phone')
                            ->label('Secondary Phone')
                            ->tel()
                            ->maxLength(20),

                        TextInput::make('whatsapp')
                            ->label('WhatsApp')
                            ->tel()
                            ->maxLength(20),

                        TextInput::make('address')
                            ->label('Address')
                            ->maxLength(255),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        auth()->user()->managerProfile()->updateOrCreate(
            ['user_id' => auth()->id()],
            $data
        );

        Notification::make()
            ->title('Profile Updated')
            ->body('Manager profile updated successfully!')
            ->success()
            ->send();
    }


    public function render(): View
    {
        return view('livewire.manager-details-component');
    }
}