<?php

namespace App\Livewire;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Livewire\Component;
use Illuminate\Contracts\View\View;
use Joaopaulolndev\FilamentEditProfile\Concerns\HasSort;

class TeacherDetailsComponent extends Component implements HasForms
{
    use InteractsWithForms;
    use HasSort;

    public ?array $data = [];

    protected static int $sort = 10;

    public function mount(): void
    {
        $profile = auth()->user()->teacherProfile;

        if ($profile) {
            $this->form->fill([
                'phone' => $profile->phone,
                'secondary_phone' => $profile->secondary_phone,
                'whatsapp' => $profile->whatsapp,
                'address' => $profile->address,
                'education' => $profile->education,
                'experience_years' => $profile->experience_years,
                'experience' => $profile->experience ?? [
                    ['organization' => '', 'position' => '', 'start_date' => '', 'end_date' => ''],
                    ['organization' => '', 'position' => '', 'start_date' => '', 'end_date' => ''],
                    ['organization' => '', 'position' => '', 'start_date' => '', 'end_date' => ''],
                ],
            ]);
        }
    }

    public function form($form)
    {
        return $form
            ->schema([
                Section::make('Teacher Profile Details')
                    ->aside()->compact()
                    ->description('Update your personal info and experience')
                    ->schema([
                        Wizard::make([
                            Step::make('Basic Info')
                                ->schema([
                                    TextInput::make('phone')->label('Phone')->tel()->maxLength(20),
                                    TextInput::make('secondary_phone')->label('Secondary Phone')->tel()->maxLength(20),
                                    TextInput::make('whatsapp')->label('WhatsApp')->tel()->maxLength(20),
                                    TextInput::make('education')->label('Education')->maxLength(150),
                                    Textarea::make('address')->label('Address')->rows(3)->columnSpanFull(),
                                ])->columns(2),

                            Step::make('Experience 1')
                                ->schema([
                                    TextInput::make('experience_years')->label('Years of Experience')->numeric()->default(1)->columnSpanFull()->suffix('Years'),
                                    Fieldset::make('Experience 1')
                                        ->schema([
                                            TextInput::make('experience.0.organization')->label('Organization')->maxLength(150)->placeholder('Schools or any other...'),
                                            TextInput::make('experience.0.position')->label('Position')->maxLength(100)->placeholder('Designation...'),
                                            DatePicker::make('experience.0.start_date')->label('Start Date')->native(false)->placeholder('dd-mm-yy'),
                                            DatePicker::make('experience.0.end_date')->label('End Date')->native(false)->placeholder('dd-mm-yy'),
                                        ]),
                                    Fieldset::make('Experience 2')
                                        ->schema([
                                            TextInput::make('experience.1.organization')->label('Organization')->maxLength(150)->placeholder('Schools or any other...'),
                                            TextInput::make('experience.1.position')->label('Position')->maxLength(100)->placeholder('Designation...'),
                                            DatePicker::make('experience.1.start_date')->label('Start Date')->native(false)->placeholder('dd-mm-yy'),
                                            DatePicker::make('experience.1.end_date')->label('End Date')->native(false)->placeholder('dd-mm-yy'),
                                        ]),
                                    Fieldset::make('Experience 3')
                                        ->schema([
                                            TextInput::make('experience.2.organization')->label('Organization')->maxLength(150)->placeholder('Schools or any other...'),
                                            TextInput::make('experience.2.position')->label('Position')->maxLength(100)->placeholder('Designation...'),
                                            DatePicker::make('experience.2.start_date')->label('Start Date')->native(false)->placeholder('dd-mm-yy'),
                                            DatePicker::make('experience.2.end_date')->label('End Date')->native(false)->placeholder('dd-mm-yy'),
                                        ]),


                                ])->columns(1),


                        ]),
                    ]),
            ])
            ->statePath('data');
    }


    public function save(): void
    {
        $data = $this->form->getState();

        auth()->user()->teacherProfile()->updateOrCreate(
            ['user_id' => auth()->id()],
            $data
        );

        Notification::make()
            ->title('Profile Updated')
            ->body('Teacher profile updated successfully!')
            ->success()
            ->send();
    }


    public function render(): View
    {
        return view('livewire.teacher-details-component');
    }
}