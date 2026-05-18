<?php

namespace App\Filament\Resources;

use App\Enums\ReturnRequestStatus;
use App\Filament\Resources\ReturnRequestResource\Pages;
use App\Models\ReturnRequest;
use App\Services\ReturnRefundService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ReturnRequestResource extends Resource
{
    protected static ?string $model = ReturnRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path-rounded-square';

    protected static ?string $navigationGroup = 'Customer Support';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('order_id')
                    ->relationship('order', 'order_number')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\TextInput::make('return_number')
                    ->required()
                    ->disabled()
                    ->dehydrated(),
                Forms\Components\TextInput::make('reason')
                    ->required(),
                Forms\Components\Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('images_display')
                    ->label('Images')
                    ->afterStateHydrated(fn (Forms\Components\Textarea $component, ?ReturnRequest $record) => $component->state(implode(PHP_EOL, $record?->images ?? [])))
                    ->disabled()
                    ->dehydrated(false)
                    ->columnSpanFull(),
                Forms\Components\Select::make('status')
                    ->options(self::statusOptions())
                    ->required(),
                Forms\Components\Select::make('refund_method')
                    ->options(self::refundMethodOptions()),
                Forms\Components\TextInput::make('refund_amount_usd')
                    ->numeric(),
                Forms\Components\TextInput::make('refund_reference')
                    ->disabled()
                    ->dehydrated(),
                Forms\Components\DateTimePicker::make('refund_processed_at')
                    ->disabled()
                    ->dehydrated(),
                Forms\Components\Textarea::make('refund_error')
                    ->disabled()
                    ->dehydrated(),
                Forms\Components\Textarea::make('admin_notes')
                    ->columnSpanFull(),
                Forms\Components\DateTimePicker::make('resolved_at'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('return_number')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('order.order_number')
                    ->label('Order')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('reason')
                    ->searchable()
                    ->limit(32),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn ($state): string => self::statusLabel($state))
                    ->color(fn ($state): string => match (self::statusValue($state)) {
                        ReturnRequestStatus::Pending->value => 'warning',
                        ReturnRequestStatus::UnderReview->value => 'info',
                        ReturnRequestStatus::Approved->value => 'primary',
                        ReturnRequestStatus::Rejected->value => 'danger',
                        ReturnRequestStatus::RefundPending->value => 'warning',
                        ReturnRequestStatus::Refunded->value => 'success',
                        ReturnRequestStatus::Cancelled->value => 'gray',
                        default => 'gray',
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('refund_method')
                    ->formatStateUsing(fn (?string $state): string => str_replace('_', ' ', $state ?? '-'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('refund_amount_usd')
                    ->money('USD')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('resolved_at')
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
                Tables\Filters\SelectFilter::make('status')
                    ->options(self::statusOptions()),
                Tables\Filters\SelectFilter::make('refund_method')
                    ->options(self::refundMethodOptions()),
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('from'),
                        Forms\Components\DatePicker::make('until'),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query
                        ->when($data['from'] ?? null, fn (Builder $query, $date) => $query->whereDate('created_at', '>=', $date))
                        ->when($data['until'] ?? null, fn (Builder $query, $date) => $query->whereDate('created_at', '<=', $date))),
            ])
            ->actions([
                Tables\Actions\Action::make('under_review')
                    ->label('Mark under review')
                    ->icon('heroicon-m-eye')
                    ->visible(fn (ReturnRequest $record): bool => $record->statusValue() === ReturnRequestStatus::Pending->value)
                    ->form([
                        Forms\Components\Textarea::make('admin_notes')
                            ->maxLength(5000),
                    ])
                    ->action(fn (ReturnRequest $record, array $data) => app(ReturnRefundService::class)->markUnderReview($record, $data['admin_notes'] ?? null)),
                Tables\Actions\Action::make('approve')
                    ->icon('heroicon-m-check-circle')
                    ->visible(fn (ReturnRequest $record): bool => in_array($record->statusValue(), [ReturnRequestStatus::Pending->value, ReturnRequestStatus::UnderReview->value], true))
                    ->form([
                        Forms\Components\TextInput::make('refund_amount_usd')
                            ->numeric()
                            ->minValue(0.01)
                            ->required(),
                        Forms\Components\Textarea::make('admin_notes')
                            ->maxLength(5000),
                    ])
                    ->action(fn (ReturnRequest $record, array $data) => app(ReturnRefundService::class)->approve($record, (float) $data['refund_amount_usd'], $data['admin_notes'] ?? null)),
                Tables\Actions\Action::make('reject')
                    ->icon('heroicon-m-x-circle')
                    ->color('danger')
                    ->visible(fn (ReturnRequest $record): bool => ! ReturnRequestStatus::fromValue($record->statusValue())->isTerminal())
                    ->form([
                        Forms\Components\Textarea::make('admin_notes')
                            ->required()
                            ->maxLength(5000),
                    ])
                    ->action(fn (ReturnRequest $record, array $data) => app(ReturnRefundService::class)->reject($record, $data['admin_notes'])),
                Tables\Actions\Action::make('process_refund')
                    ->label('Process refund')
                    ->icon('heroicon-m-banknotes')
                    ->color('success')
                    ->visible(fn (ReturnRequest $record): bool => in_array($record->statusValue(), [ReturnRequestStatus::Approved->value, ReturnRequestStatus::RefundPending->value], true))
                    ->form([
                        Forms\Components\Textarea::make('admin_notes')
                            ->maxLength(5000),
                    ])
                    ->action(fn (ReturnRequest $record, array $data) => app(ReturnRefundService::class)->processRefund($record, $data['admin_notes'] ?? null)),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                //
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListReturnRequests::route('/'),
            'edit' => Pages\EditReturnRequest::route('/{record}/edit'),
        ];
    }

    private static function statusOptions(): array
    {
        return collect(ReturnRequestStatus::cases())
            ->mapWithKeys(fn (ReturnRequestStatus $status) => [$status->value => $status->label()])
            ->all();
    }

    private static function refundMethodOptions(): array
    {
        return collect(ReturnRefundService::REFUND_METHODS)
            ->mapWithKeys(fn (string $method) => [$method => str_replace('_', ' ', $method)])
            ->all();
    }

    private static function statusValue(mixed $state): string
    {
        return $state instanceof ReturnRequestStatus ? $state->value : (string) $state;
    }

    private static function statusLabel(mixed $state): string
    {
        return ReturnRequestStatus::fromValue(self::statusValue($state))->label();
    }
}
