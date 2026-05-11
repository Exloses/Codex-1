<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AffiliatePayoutResource\Pages;
use App\Models\AffiliatePayout;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AffiliatePayoutResource extends Resource
{
    protected static ?string $model = AffiliatePayout::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('affiliate_id')
                    ->relationship('affiliate', 'id')
                    ->required(),
                Forms\Components\Select::make('payout_method_id')
                    ->relationship('payoutMethod', 'id')
                    ->required(),
                Forms\Components\TextInput::make('amount_usd')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('fee_usd')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('net_amount_usd')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('status')
                    ->required(),
                Forms\Components\TextInput::make('payout_type')
                    ->required(),
                Forms\Components\TextInput::make('paypal_email')
                    ->email(),
                Forms\Components\TextInput::make('wise_email')
                    ->email(),
                Forms\Components\TextInput::make('bank_account'),
                Forms\Components\TextInput::make('transaction_ref'),
                Forms\Components\DateTimePicker::make('processed_at'),
                Forms\Components\Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('affiliate.id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('payoutMethod.id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount_usd')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('fee_usd')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('net_amount_usd')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('payout_type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('paypal_email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('wise_email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('bank_account')
                    ->searchable(),
                Tables\Columns\TextColumn::make('transaction_ref')
                    ->searchable(),
                Tables\Columns\TextColumn::make('processed_at')
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAffiliatePayouts::route('/'),
            'create' => Pages\CreateAffiliatePayout::route('/create'),
            'edit' => Pages\EditAffiliatePayout::route('/{record}/edit'),
        ];
    }
}
