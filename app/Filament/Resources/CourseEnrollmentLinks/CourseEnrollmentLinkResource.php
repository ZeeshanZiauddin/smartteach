<?php

namespace App\Filament\Resources\CourseEnrollmentLinks;

use App\Filament\Resources\CourseEnrollmentLinks\Pages\CreateCourseEnrollmentLink;
use App\Filament\Resources\CourseEnrollmentLinks\Pages\EditCourseEnrollmentLink;
use App\Filament\Resources\CourseEnrollmentLinks\Pages\ListCourseEnrollmentLinks;
use App\Filament\Resources\CourseEnrollmentLinks\Schemas\CourseEnrollmentLinkForm;
use App\Filament\Resources\CourseEnrollmentLinks\Tables\CourseEnrollmentLinksTable;
use App\Models\CourseEnrollmentLink;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CourseEnrollmentLinkResource extends Resource
{
    protected static ?string $model = CourseEnrollmentLink::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'CourseEnrollmentLink';

    public static function form(Schema $schema): Schema
    {
        return CourseEnrollmentLinkForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CourseEnrollmentLinksTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCourseEnrollmentLinks::route('/'),
            'create' => CreateCourseEnrollmentLink::route('/create'),
            'edit' => EditCourseEnrollmentLink::route('/{record}/edit'),
        ];
    }
}
