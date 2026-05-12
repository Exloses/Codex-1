<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;

class AffiliatePayoutPaidNotification extends GlobalDropshipNotification
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
            'transactionRef' => $this->value('transaction_ref', 'Processing reference pending'),
            'paidDate' => $this->readableDate($this->value('processed_at', $this->value('paid_at', now()))),
            'actionUrl' => $this->value('payouts_url', url('/affiliate/payouts')),
        ]);

        return $this->makeMailMessage('Your affiliate payout has been paid', 'emails.affiliate-payout-paid', $payload);
    }

    public function toDatabase(object $notifiable): array
    {
        $amount = $this->value('net_amount_usd', $this->value('amount_usd', 0));

        return $this->makeDatabasePayload(
            'affiliate_payout_paid',
            'Payout paid',
            'Your payout for '.$this->money($amount).' has been sent.',
            $this->value('payouts_url', url('/affiliate/payouts')),
            ['amount_usd' => $amount, 'transaction_ref' => $this->value('transaction_ref')]
        );
    }
}
