<?php

namespace App\Livewire;

use Joaopaulolndev\FilamentEditProfile\Concerns\HasSort;
use Livewire\Component;

class UserDetailsComponent extends Component
{
    use HasSort;
    public ?string $role = null;

    protected static int $sort = 10;

    public function mount(): void
    {
        $user = auth()->user();
        if ($user) {
            if ($user->hasRole('student')) {
                $this->role = 'student';
            } elseif ($user->hasRole('teacher')) {
                $this->role = 'teacher';
            } elseif ($user->hasRole('super_admin')) {
                $this->role = 'manager';
            }
        }
    }

    public function render()
    {
        return view('livewire.user-details-component');
    }
}