<?php

namespace App\Filament\Resources\DropshipOrderResource\Pages;

use App\Filament\Resources\DropshipOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDropshipOrders extends ListRecords
{
    protected static string $resource = DropshipOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
