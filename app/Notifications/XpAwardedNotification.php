<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class XpAwardedNotification extends Notification
{
    public int $amount;
    public string $reason;

    public function __construct(int $amount, string $reason)
    {
        $this->amount = $amount;
        $this->reason = $reason;
    }

    /**
     * Only store in DB – no queue, no broadcast (those need extra infrastructure).
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'amount'  => $this->amount,
            'reason'  => $this->reason,
            'message' => "لقد حصلت على +{$this->amount} نقطة خبرة (XP) لـ: {$this->reason}",
            'icon'    => 'xp',
        ];
    }
}
