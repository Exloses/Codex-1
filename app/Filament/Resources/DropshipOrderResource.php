<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DropshipOrderResource\Pages;
use App\Filament\Resources\DropshipOrderResource\RelationManagers\TrackingEventsRelationManager;
use App\Models\DropshipOrder;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DropshipOrderResource extends Resource
{
    protected static ?string $model = DropshipOrder::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('order_id')
                    ->relationship('order', 'id')
                    ->required(),
                Forms\Components\Select::make('vendor_id')
                    ->relationship('vendor', 'id')
                    ->required(),
                Forms\Components\TextInput::make('dropship_number')
                    ->required(),
                Forms\Components\TextInput::make('status')
                    ->required(),
                Forms\Components\TextInput::make('vendor_total_idr')
                    ->required()
                    ->numeric(),
                Forms\Components\Toggle::make('is_paid_to_vendor')
                    ->required(),
                Forms\Components\DateTimePicker::make('paid_at'),
                Forms\Components\TextInput::make('tracking_number'),
                Forms\Components\TextInput::make('carrier'),
                Forms\Components\TextInput::make('shipping_label'),
                Forms\Components\DateTimePicker::make('shipped_at'),
                Forms\Components\DateTimePicker::make('delivered_at'),
                Forms\Components\Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order.id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('vendor.id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('dropship_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('vendor_total_idr')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_paid_to_vendor')
                    ->boolean(),
                Tables\Columns\TextColumn::make('paid_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tracking_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('carrier')
                    ->searchable(),
                Tables\Columns\TextColumn::make('shipping_label')
                    ->searchable(),
                Tables\Columns\TextColumn::make('shipped_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('delivered_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            TrackingEventsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDropshipOrders::route('/'),
            'create' => Pages\CreateDropshipOrder::route('/create'),
            'edit' => Pages\EditDropshipOrder::route('/{record}/edit'),
        ];
    }
}
