<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;

class AffiliatePayoutApprovedNotification extends GlobalDropshipNotification
{
    public function __construct(mixed $payout = null, array $data = [])
    {
        parent::__construct($payout, $data);
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $amount = $this->value('net_amount_usd', $this->value('amount_usd', 0));
        $payload = $this->mailPayload($notifiable, [
            'amount' => $this->money($amount),
            'payoutMethod' => $this->value('payoutMethod.type', $this->value('payout_type', 'Selected payout method')),
            'estimatedProcess' => $this->value('estimated_process', '1-3 business days'),
            'actionUrl' => $this->value('payouts_url', url('/affiliate/payouts')),
        ]);

        return $this->makeMailMessage('Your affiliate payout was approved', 'emails.affiliate-payout-approved', $payload);
    }

    public function toDatabase(object $notifiable): array
    {
        $amount = $this->value('net_amount_usd', $this->value('amount_usd', 0));

        return $this->makeDatabasePayload(
            'affiliate_payout_approved',
            'Payout approved',
            'Your payout for '.$this->money($amount).' has been approved.',
            $this->value('payouts_url', url('/affiliate/payouts')),
            ['amount_usd' => $amount, 'payout_type' => $this->value('payout_type')]
        );
    }
}
