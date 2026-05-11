<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                Forms\Components\Select::make('address_id')
                    ->relationship('address', 'id'),
                Forms\Components\TextInput::make('order_number')
                    ->required(),
                Forms\Components\TextInput::make('guest_email')
                    ->email(),
                Forms\Components\TextInput::make('guest_name'),
                Forms\Components\TextInput::make('status')
                    ->required(),
                Forms\Components\TextInput::make('subtotal_usd')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('shipping_cost_usd')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('discount_usd')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('total_usd')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('buyer_currency')
                    ->required(),
                Forms\Components\TextInput::make('exchange_rate')
                    ->required()
                    ->numeric()
                    ->default(1),
                Forms\Components\TextInput::make('total_buyer_currency')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('payment_status')
                    ->required(),
                Forms\Components\TextInput::make('payment_method'),
                Forms\Components\TextInput::make('stripe_payment_id'),
                Forms\Components\TextInput::make('paypal_order_id'),
                Forms\Components\TextInput::make('affiliate_code'),
                Forms\Components\Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('address.id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('order_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('guest_email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('guest_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('subtotal_usd')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('shipping_cost_usd')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('discount_usd')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_usd')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('buyer_currency')
                    ->searchable(),
                Tables\Columns\TextColumn::make('exchange_rate')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_buyer_currency')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('payment_method')
                    ->searchable(),
                Tables\Columns\TextColumn::make('stripe_payment_id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('paypal_order_id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('affiliate_code')
                    ->searchable(),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
