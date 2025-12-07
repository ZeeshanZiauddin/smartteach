<?php

namespace App\Filament\Resources\CourseEnrollmentLinks\Pages;

use App\Filament\Resources\CourseEnrollmentLinks\CourseEnrollmentLinkResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCourseEnrollmentLink extends EditRecord
{
    protected static string $resource = CourseEnrollmentLinkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
