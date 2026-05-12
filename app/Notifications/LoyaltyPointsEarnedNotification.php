<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;

class LoyaltyPointsEarnedNotification extends GlobalDropshipNotification
{
    public function __construct(mixed $loyaltyTransaction = null, array $data = [])
    {
        parent::__construct($loyaltyTransaction, $data);
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $points = (int) $this->value('points', 0);
        $payload = $this->mailPayload($notifiable, [
            'pointsEarned' => number_format($points),
            'totalPoints' => number_format((int) $this->value('total_points', data_get($notifiable, 'loyaltyPoint.balance', $points))),
            'actionUrl' => $this->value('loyalty_url', url('/account/loyalty-points')),
        ]);

        return $this->makeMailMessage('You earned loyalty points', 'emails.loyalty-points-earned', $payload);
    }

    public function toDatabase(object $notifiable): array
    {
        $points = (int) $this->value('points', 0);

        return $this->makeDatabasePayload(
            'loyalty_points_earned',
            'Loyalty points earned',
            "You earned {$points} loyalty points.",
            $this->value('loyalty_url', url('/account/loyalty-points')),
            ['points' => $points, 'total_points' => $this->value('total_points')]
        );
    }
}
