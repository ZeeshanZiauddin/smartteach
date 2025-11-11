<?php

namespace App\Filament\Resources\AssignmentSubmissions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AssignmentSubmissionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('assignment.title')->label('Assignment')->sortable()->searchable(),
                TextColumn::make('user.name')->label('Student')->sortable()->searchable(),
                TextColumn::make('grade')->label('Grade')->sortable(),
                BadgeColumn::make('status')
                    ->colors([
                        'info' => 'submitted',
                        'success' => 'graded',
                        'danger' => 'late',
                    ]),
                TextColumn::make('submitted_at')->dateTime('M d, Y h:i A'),
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