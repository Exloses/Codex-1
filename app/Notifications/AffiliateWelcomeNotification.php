<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;

class AffiliateWelcomeNotification extends GlobalDropshipNotification
{
    public function __construct(mixed $affiliate = null, array $data = [])
    {
        parent::__construct($affiliate, $data);
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $payload = $this->mailPayload($notifiable, [
            'referralCode' => $this->value('referral_code', 'PENDING'),
            'referralLink' => $this->value('referral_link', url('/affiliate')),
            'commissionRate' => $this->percent($this->value('commission_rate', 0)),
            'actionUrl' => $this->value('dashboard_url', url('/affiliate/dashboard')),
        ]);

        return $this->makeMailMessage('Welcome to the GlobalDropship affiliate program', 'emails.affiliate-welcome', $payload);
    }

    public function toDatabase(object $notifiable): array
    {
        return $this->makeDatabasePayload(
            'affiliate_welcome',
            'Affiliate account ready',
            'Your affiliate referral code is ready to share.',
            $this->value('dashboard_url', url('/affiliate/dashboard')),
            ['referral_code' => $this->value('referral_code'), 'commission_rate' => $this->value('commission_rate')]
        );
    }
}
