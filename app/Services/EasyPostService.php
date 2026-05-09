<?php

namespace App\Services;

use EasyPost\EasyPostClient;

class EasyPostService
{
    public function __construct(
        private readonly EasyPostClient $client,
    ) {}

    public function getRates(array $destinationAddress, array $parcel, ?array $originAddress = null): array
    {
        $shipment = $this->client->shipment->create([
            'from_address' => $originAddress ?? $this->defaultIndonesiaOrigin(),
            'to_address' => $destinationAddress,
            'parcel' => $parcel,
        ]);

        return array_map(
            fn ($rate) => method_exists($rate, 'toArray') ? $rate->toArray() : (array) $rate,
            $shipment->rates ?? []
        );
    }

    public function createShippingLabel(string $shipmentId, string $rateId): array
    {
        $shipment = $this->client->shipment->buy($shipmentId, ['rate' => ['id' => $rateId]]);

        return method_exists($shipment, 'toArray') ? $shipment->toArray() : (array) $shipment;
    }

    public function getTracking(string $trackingCode, ?string $carrier = null): array
    {
        $tracker = $this->client->tracker->create(array_filter([
            'tracking_code' => $trackingCode,
            'carrier' => $carrier,
        ]));

        return method_exists($tracker, 'toArray') ? $tracker->toArray() : (array) $tracker;
    }

    private function defaultIndonesiaOrigin(): array
    {
        return [
            'name' => config('app.name'),
            'street1' => 'Jl. Placeholder No. 1',
            'city' => 'Jakarta',
            'state' => 'DKI Jakarta',
            'zip' => '10110',
            'country' => 'ID',
            'phone' => '+620000000000',
            'email' => 'shipping@example.test',
        ];
    }
}
