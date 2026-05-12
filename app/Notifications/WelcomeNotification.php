<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;

class WelcomeNotification extends GlobalDropshipNotification
{
    public function __construct(mixed $user = null, array $data = [])
    {
        parent::__construct($user, $data);
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $payload = $this->mailPayload($notifiable, [
            'name' => $this->value('name', data_get($notifiable, 'name', 'there')),
            'storeUrl' => $this->value('store_url', url('/')),
            'accountUrl' => $this->value('account_url', url('/account')),
        ]);

        return $this->makeMailMessage('Welcome to GlobalDropship', 'emails.welcome', $payload);
    }

    public function toDatabase(object $notifiable): array
    {
        return $this->makeDatabasePayload(
            'welcome',
            'Welcome to GlobalDropship',
            'Your account is ready. Start exploring global dropship products.',
            $this->value('account_url', url('/account')),
            ['user_id' => data_get($notifiable, 'id')]
        );
    }
}
