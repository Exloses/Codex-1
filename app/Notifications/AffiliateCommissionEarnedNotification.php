<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;

class AffiliateCommissionEarnedNotification extends GlobalDropshipNotification
{
    public function __construct(mixed $commission = null, array $data = [])
    {
        parent::__construct($commission, $data);
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $amount = $this->value('commission_usd', $this->value('amount_usd', 0));
        $payload = $this->mailPayload($notifiable, [
            'commissionAmount' => $this->money($amount),
            'orderNumber' => $this->value('order.order_number', $this->value('order_number', 'Recent order')),
            'totalBalance' => $this->money($this->value('total_balance_usd', data_get($this->resource, 'affiliate.total_earned_usd', $amount))),
            'actionUrl' => $this->value('commissions_url', url('/affiliate/commissions')),
        ]);

        return $this->makeMailMessage('You earned a new affiliate commission', 'emails.affiliate-commission-earned', $payload);
    }

    public function toDatabase(object $notifiable): array
    {
        $amount = $this->value('commission_usd', $this->value('amount_usd', 0));

        return $this->makeDatabasePayload(
            'affiliate_commission_earned',
            'Commission earned',
            'You earned '.$this->money($amount).' from an affiliate order.',
            $this->value('commissions_url', url('/affiliate/commissions')),
            ['commission_usd' => $amount, 'order_number' => $this->value('order.order_number')]
        );
    }
}
