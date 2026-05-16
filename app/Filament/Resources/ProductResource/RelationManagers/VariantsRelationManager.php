<?php

namespace App\Filament\Resources\ProductResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class VariantsRelationManager extends RelationManager
{
    protected static string $relationship = 'variants';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\KeyValue::make('combination')
                ->keyLabel('Option')
                ->valueLabel('Value')
                ->required()
                ->columnSpanFull(),
            Forms\Components\TextInput::make('sku')
                ->label('SKU')
                ->maxLength(255),
            Forms\Components\TextInput::make('price')
                ->numeric()
                ->minValue(0)
                ->prefix('$'),
            Forms\Components\TextInput::make('vendor_price')
                ->numeric()
                ->minValue(0),
            Forms\Components\TextInput::make('stock')
                ->required()
                ->numeric()
                ->minValue(0)
                ->default(0),
            Forms\Components\TextInput::make('image')
                ->url()
                ->maxLength(2048)
                ->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('sku')
            ->columns([
                Tables\Columns\TextColumn::make('combination')
                    ->formatStateUsing(fn ($state) => collect($state ?? [])
                        ->map(fn ($value, $key) => "{$key}: {$value}")
                        ->join(', '))
                    ->wrap(),
                Tables\Columns\TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('vendor_price')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('stock')
                    ->numeric()
                    ->sortable(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
