<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;

class AffiliateTierUpgradeNotification extends GlobalDropshipNotification
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
        $newTier = $this->value('new_tier', $this->value('tier', 'next tier'));
        $payload = $this->mailPayload($notifiable, [
            'oldTier' => $this->value('old_tier', 'previous tier'),
            'newTier' => $newTier,
            'benefits' => $this->value('benefits', [
                'Higher commission rate: '.$this->percent($this->value('commission_rate', 0)),
                'Priority affiliate support',
                'More visibility for top-performing referrals',
            ]),
            'actionUrl' => $this->value('dashboard_url', url('/affiliate/dashboard')),
        ]);

        return $this->makeMailMessage("Affiliate tier upgraded to {$newTier}", 'emails.affiliate-tier-upgrade', $payload);
    }

    public function toDatabase(object $notifiable): array
    {
        return $this->makeDatabasePayload(
            'affiliate_tier_upgrade',
            'Affiliate tier upgraded',
            'Your affiliate tier is now '.$this->value('new_tier', $this->value('tier', 'updated')).'.',
            $this->value('dashboard_url', url('/affiliate/dashboard')),
            ['old_tier' => $this->value('old_tier'), 'new_tier' => $this->value('new_tier', $this->value('tier'))]
        );
    }
}
