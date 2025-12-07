<?php

namespace App\Filament\Resources\CourseEnrollmentLinks\Pages;

use App\Filament\Resources\CourseEnrollmentLinks\CourseEnrollmentLinkResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCourseEnrollmentLinks extends ListRecords
{
    protected static string $resource = CourseEnrollmentLinkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
