<div>
    @if($role === 'student')
        <livewire:student-details-component />
    @elseif($role === 'teacher')
        <livewire:teacher-details-component />
    @elseif($role === 'manager')
        <livewire:manager-details-component />
    @else
        <p>No profile available for your role.</p>
    @endif
</div>