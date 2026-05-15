<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SupportTicketResource\Pages;
use App\Models\SupportTicket;
use App\Notifications\SupportTicketReplyNotification;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SupportTicketResource extends Resource
{
    protected static ?string $model = SupportTicket::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationGroup = 'Customer Support';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),
                Forms\Components\TextInput::make('guest_email')
                    ->email(),
                Forms\Components\TextInput::make('guest_name'),
                Forms\Components\Select::make('order_id')
                    ->relationship('order', 'order_number')
                    ->searchable()
                    ->preload(),
                Forms\Components\TextInput::make('ticket_number')
                    ->required()
                    ->disabled()
                    ->dehydrated(),
                Forms\Components\TextInput::make('subject')
                    ->required(),
                Forms\Components\Textarea::make('message')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Select::make('status')
                    ->options(array_combine(SupportTicket::statuses(), SupportTicket::statuses()))
                    ->required(),
                Forms\Components\Select::make('priority')
                    ->options(array_combine(SupportTicket::priorities(), SupportTicket::priorities()))
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('guest_email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('guest_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('order.order_number')
                    ->label('Order')
                    ->sortable(),
                Tables\Columns\TextColumn::make('ticket_number')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('subject')
                    ->searchable()
                    ->limit(44),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        SupportTicket::STATUS_OPEN => 'warning',
                        SupportTicket::STATUS_PENDING_CUSTOMER => 'info',
                        SupportTicket::STATUS_RESOLVED => 'success',
                        SupportTicket::STATUS_CLOSED => 'gray',
                        default => 'gray',
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('priority')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        SupportTicket::PRIORITY_LOW => 'gray',
                        SupportTicket::PRIORITY_NORMAL => 'info',
                        SupportTicket::PRIORITY_HIGH => 'warning',
                        SupportTicket::PRIORITY_URGENT => 'danger',
                        default => 'gray',
                    })
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
                Tables\Filters\SelectFilter::make('status')
                    ->options(array_combine(SupportTicket::statuses(), SupportTicket::statuses())),
                Tables\Filters\SelectFilter::make('priority')
                    ->options(array_combine(SupportTicket::priorities(), SupportTicket::priorities())),
            ])
            ->actions([
                Tables\Actions\Action::make('reply')
                    ->icon('heroicon-m-paper-airplane')
                    ->form([
                        Forms\Components\Textarea::make('message')
                            ->required()
                            ->maxLength(5000),
                    ])
                    ->action(function (SupportTicket $record, array $data): void {
                        $reply = $record->replies()->create([
                            'user_id' => auth()->id(),
                            'message' => $data['message'],
                            'is_staff' => true,
                        ]);

                        $record->update(['status' => SupportTicket::STATUS_PENDING_CUSTOMER]);

                        if ($record->user) {
                            $record->user->notify(new SupportTicketReplyNotification($record, $reply->load('user')));
                        }
                    }),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListSupportTickets::route('/'),
            'create' => Pages\CreateSupportTicket::route('/create'),
            'edit' => Pages\EditSupportTicket::route('/{record}/edit'),
        ];
    }
}
