<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;

class VendorApprovedNotification extends GlobalDropshipNotification
{
    public function __construct(mixed $vendor = null, array $data = [])
    {
        parent::__construct($vendor, $data);
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $storeName = $this->value('store_name', 'your vendor store');
        $payload = $this->mailPayload($notifiable, [
            'storeName' => $storeName,
            'actionUrl' => $this->value('dashboard_url', url('/vendor/dashboard')),
        ]);

        return $this->makeMailMessage('Your vendor store is approved', 'emails.vendor-approved', $payload);
    }

    public function toDatabase(object $notifiable): array
    {
        return $this->makeDatabasePayload(
            'vendor_approved',
            'Vendor approved',
            'Your vendor store is approved and ready to receive orders.',
            $this->value('dashboard_url', url('/vendor/dashboard')),
            ['store_name' => $this->value('store_name')]
        );
    }
}
