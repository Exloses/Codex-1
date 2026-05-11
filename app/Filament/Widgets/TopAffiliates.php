<?php

namespace App\Filament\Widgets;

use App\Models\Affiliate;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class TopAffiliates extends TableWidget
{
    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading('Top Affiliates')
            ->query(
                fn (): Builder => Affiliate::query()
                    ->with('user')
                    ->orderByDesc('total_earned_usd')
                    ->orderByDesc('total_sales')
            )
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Affiliate')
                    ->searchable(),
                Tables\Columns\TextColumn::make('referral_code')
                    ->label('Code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tier')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_sales')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_earned_usd')
                    ->label('Earned')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
            ])
            ->defaultPaginationPageOption(5);
    }
}
