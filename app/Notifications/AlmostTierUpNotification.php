<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AlmostTierUpNotification extends Notification implements ShouldQueue
{
    use Queueable;
    
    protected $nextTier;
    protected $pointsNeeded;

    public function __construct($nextTier, $pointsNeeded)
    {
        $this->nextTier = $nextTier;
        $this->pointsNeeded = $pointsNeeded;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('📈 Bạn còn ' . $this->pointsNeeded . ' điểm nữa để lên hạng ' . $this->nextTier)
            ->greeting('Xin chào ' . $notifiable->name . ',')
            ->line('Bạn chỉ còn **' . $this->pointsNeeded . ' điểm** nữa để lên hạng **' . $this->nextTier . '**!')
            ->line('Hãy đặt thêm vé để nhận được nhiều ưu đãi hơn.')
            ->action('Đặt vé ngay', url('/'))
            ->line('Cảm ơn bạn đã sử dụng dịch vụ của chúng tôi!');
    }

    public function toArray($notifiable)
    {
        return [
            'message' => 'Còn ' . $this->pointsNeeded . ' điểm nữa để lên hạng ' . $this->nextTier,
            'url' => route('me.profile'),
            'icon' => '📈'
        ];
    }
}
