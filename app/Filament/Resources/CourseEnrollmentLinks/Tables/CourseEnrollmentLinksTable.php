<?php

namespace App\Filament\Resources\CourseEnrollmentLinks\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CourseEnrollmentLinksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('course.title')
                    ->label('Course')
                    ->sortable(),

                TextColumn::make('token')
                    ->copyable()
                    ->searchable(),

                TextColumn::make('max_uses')
                    ->label('Max'),

                TextColumn::make('used_count')
                    ->label('Used'),

                IconColumn::make('expires_at')
                    ->label('Expired?')
                    ->boolean()
                    ->getStateUsing(fn($record) => $record->isExpired()),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}