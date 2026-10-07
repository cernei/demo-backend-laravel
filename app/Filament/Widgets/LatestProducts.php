<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class LatestProducts extends TableWidget
{
    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading('Latest Products')
            ->query(
                Product::query()
                    ->with('admin')
                    ->latest('created_at')
                    ->limit(5),
            )
            ->columns([
                TextColumn::make('title')
                    ->searchable(),
                TextColumn::make('admin.name')
                    ->label('Admin'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->label('Created'),
            ])
            ->searchable(false)
            ->paginated(false);
    }
}
