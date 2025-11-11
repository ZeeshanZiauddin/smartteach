<?php

namespace App\Filament\Widgets;

use App\Models\CourseMaterial;
use App\Models\Course;
use Asmit\FilamentUpload\Forms\Components\AdvancedFileUpload;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
use Filament\Forms\Form;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class UploadCourseMaterial extends Widget implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $heading = 'Course Material';
    protected static ?string $description = 'Upload New Course Materials For you Students...';
    protected string $view = 'filament.widgets.upload-course-material';
    protected static ?int $sort = 1;
    protected int|string|array $columnSpan = 2;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public static function canView(): bool
    {
        $user = auth()->user();
        return $user && $user->hasRole('teacher');
    }

    // ✅ v4 syntax: define form here instead of getFormSchema()
    public function form($form)
    {
        return $form
            ->schema([
                TextInput::make('title')
                    ->label('Material Title')
                    ->required()
                    ->maxLength(255),

                Textarea::make('description')
                    ->label('Description')
                    ->maxLength(500),

                AdvancedFileUpload::make('file')
                    ->label('Upload File')
                    ->disk('public')
                    ->directory('course_materials')
                    ->getUploadedFileNameForStorageUsing(
                        fn(TemporaryUploadedFile $file): string =>
                        'course-material-' . now()->format('YmdHis') . '.' . $file->getClientOriginalExtension()

                    )
                    ->visibility('public')
                    ->required(),
            ])
            ->statePath('data'); // ✅ tells Filament to use $data array for form state
    }

    public function submit(): void
    {
        $data = $this->form->getState();

        $courseId = Course::where('user_id', Auth::id())->value('id');

        if (!$courseId) {
            Notification::make()
                ->title('No course found!')
                ->danger()
                ->body('You must have a course assigned before uploading materials.')
                ->send();
            return;
        }

        CourseMaterial::create([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'file' => $data['file'],
            'teacher_id' => Auth::id(),
            'course_id' => $courseId,
        ]);

        $this->form->fill(); // reset form

        Notification::make()
            ->title('Material uploaded successfully!')
            ->success()
            ->send();
    }

    protected function getFormModel(): string
    {
        return CourseMaterial::class;
    }
}