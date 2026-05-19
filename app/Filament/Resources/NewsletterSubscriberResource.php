<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NewsletterSubscriberResource\Pages;
use App\Models\NewsletterSubscriber;
use App\Services\NewsletterService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification as FilamentNotification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class NewsletterSubscriberResource extends Resource
{
    protected static ?string $model = NewsletterSubscriber::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $navigationGroup = 'Marketing';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),
                Forms\Components\Select::make('status')
                    ->options([
                        NewsletterService::STATUS_ACTIVE => 'Active',
                        NewsletterService::STATUS_UNSUBSCRIBED => 'Unsubscribed',
                    ])
                    ->default(NewsletterService::STATUS_ACTIVE)
                    ->required(),
                Forms\Components\DateTimePicker::make('subscribed_at'),
                Forms\Components\DateTimePicker::make('unsubscribed_at'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        NewsletterService::STATUS_ACTIVE => 'success',
                        NewsletterService::STATUS_UNSUBSCRIBED => 'gray',
                        default => 'warning',
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('subscribed_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('unsubscribed_at')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('Still active'),
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
                    ->options([
                        NewsletterService::STATUS_ACTIVE => 'Active',
                        NewsletterService::STATUS_UNSUBSCRIBED => 'Unsubscribed',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\Action::make('broadcast')
                    ->label('Send newsletter')
                    ->icon('heroicon-o-paper-airplane')
                    ->form([
                        Forms\Components\TextInput::make('subject')
                            ->required()
                            ->maxLength(120),
                        Forms\Components\Textarea::make('message')
                            ->required()
                            ->rows(8)
                            ->maxLength(5000),
                    ])
                    ->action(function (array $data): void {
                        $sent = app(NewsletterService::class)->broadcast($data['subject'], $data['message']);

                        FilamentNotification::make()
                            ->title("Newsletter queued for {$sent} active subscribers")
                            ->success()
                            ->send();
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('unsubscribe')
                    ->icon('heroicon-o-no-symbol')
                    ->color('gray')
                    ->requiresConfirmation()
                    ->visible(fn (NewsletterSubscriber $record): bool => $record->status === NewsletterService::STATUS_ACTIVE)
                    ->action(function (NewsletterSubscriber $record): void {
                        $record->forceFill([
                            'status' => NewsletterService::STATUS_UNSUBSCRIBED,
                            'unsubscribed_at' => now(),
                        ])->save();
                    }),
                Tables\Actions\Action::make('reactivate')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (NewsletterSubscriber $record): bool => $record->status !== NewsletterService::STATUS_ACTIVE)
                    ->action(function (NewsletterSubscriber $record): void {
                        $record->forceFill([
                            'status' => NewsletterService::STATUS_ACTIVE,
                            'subscribed_at' => now(),
                            'unsubscribed_at' => null,
                        ])->save();
                    }),
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
            'index' => Pages\ListNewsletterSubscribers::route('/'),
            'create' => Pages\CreateNewsletterSubscriber::route('/create'),
            'edit' => Pages\EditNewsletterSubscriber::route('/{record}/edit'),
        ];
    }
}
