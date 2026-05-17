<?php

namespace App\Filament\Resources\DropshipOrderResource\RelationManagers;

use App\Enums\OrderTrackingSource;
use App\Enums\OrderTrackingStatus;
use App\Services\OrderTrackingService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class TrackingEventsRelationManager extends RelationManager
{
    protected static string $relationship = 'trackingEvents';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('status')
                ->options(collect(OrderTrackingStatus::cases())->mapWithKeys(fn (OrderTrackingStatus $status) => [$status->value => $status->label()]))
                ->required(),
            Forms\Components\TextInput::make('title')
                ->maxLength(255),
            Forms\Components\Textarea::make('description')
                ->columnSpanFull(),
            Forms\Components\TextInput::make('location')
                ->maxLength(255),
            Forms\Components\DateTimePicker::make('occurred_at')
                ->default(now())
                ->required(),
            Forms\Components\KeyValue::make('metadata')
                ->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('occurred_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status.value')
                    ->label('Status')
                    ->badge(),
                Tables\Columns\TextColumn::make('title'),
                Tables\Columns\TextColumn::make('location')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('source.value')
                    ->label('Source')
                    ->badge(),
            ])
            ->defaultSort('occurred_at')
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->using(function (array $data) {
                        $dropshipOrder = $this->getOwnerRecord();

                        return app(OrderTrackingService::class)->record($dropshipOrder->order, $data['status'], $data + [
                            'dropship_order' => $dropshipOrder,
                            'source' => OrderTrackingSource::Admin,
                        ]);
                    }),
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
