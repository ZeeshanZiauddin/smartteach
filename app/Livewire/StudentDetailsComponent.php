<?php

namespace App\Livewire;

use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Livewire\Component;
use Illuminate\Contracts\View\View;
use Joaopaulolndev\FilamentEditProfile\Concerns\HasSort;

class StudentDetailsComponent extends Component implements HasForms
{
    use InteractsWithForms;
    use HasSort;

    public ?array $data = [];

    protected static int $sort = 10;

    public function mount(): void
    {
        $profile = auth()->user()->studentProfile;

        if ($profile) {
            $this->form->fill([
                'roll_number' => $profile->roll_number,
                'address' => $profile->address,
                'phone_no' => $profile->phone_no,
                'whatsapp_no' => $profile->whatsapp_no,
                'home_number' => $profile->home_number,
                'guardian_name' => $profile->guardian_name,
                'guardian_phone' => $profile->guardian_phone,
                'guardian_accupation' => $profile->guardian_accupation,
            ]);
        }
    }

    public function form($form)
    {
        return $form
            ->schema([
                Section::make('Student Profile Details')
                    ->aside()
                    ->description('Update your personal and guardian information')
                    ->schema([

                        \Filament\Forms\Components\TextInput::make('roll_number')
                            ->label('Roll Number')
                            ->maxLength(100),

                        \Filament\Forms\Components\Textarea::make('address')
                            ->label('Address')
                            ->rows(3),

                        \Filament\Forms\Components\TextInput::make('phone_no')
                            ->label('Phone Number')
                            ->tel()
                            ->maxLength(20),

                        \Filament\Forms\Components\TextInput::make('whatsapp_no')
                            ->label('WhatsApp Number')
                            ->tel()
                            ->maxLength(20),

                        \Filament\Forms\Components\TextInput::make('home_number')
                            ->label('Home Number')
                            ->maxLength(50),

                        \Filament\Forms\Components\TextInput::make('guardian_name')
                            ->label('Guardian Name')
                            ->maxLength(150),

                        \Filament\Forms\Components\TextInput::make('guardian_phone')
                            ->label('Guardian Phone')
                            ->tel()
                            ->maxLength(20),

                        \Filament\Forms\Components\TextInput::make('guardian_accupation')
                            ->label('Guardian Occupation')
                            ->maxLength(150),
                    ]),
            ])
            ->statePath('data');
    }


    public function save(): void
    {
        $data = $this->form->getState();

        auth()->user()->studentProfile()->updateOrCreate(
            ['user_id' => auth()->id()],
            $data
        );

        Notification::make()
            ->title('Profile Updated')
            ->body('Student profile updated successfully!')
            ->success()
            ->send();
    }


    public function render(): View
    {
        return view('livewire.student-details-component');
    }
}