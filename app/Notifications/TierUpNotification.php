<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TierUpNotification extends Notification implements ShouldQueue
{
    use Queueable;
    
    protected $tier;
    protected $discounts = [
        'Silver' => 3,
        'Gold' => 7,
        'Diamond' => 10
    ];

    public function __construct($tier)
    {
        $this->tier = $tier;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        $discount = $this->discounts[$this->tier] ?? 0;
        
        return (new MailMessage)
            ->subject('🎉 Chúc mừng bạn đã lên hạng ' . $this->tier . '!')
            ->greeting('Xin chào ' . $notifiable->name . ',')
            ->line('Chúc mừng bạn đã được thăng hạng lên **' . $this->tier . '**!')
            ->line('Bây giờ bạn sẽ được giảm giá **' . $discount . '%** cho mỗi đơn hàng.')
            ->action('Đặt vé ngay', url('/'))
            ->line('Cảm ơn bạn đã sử dụng dịch vụ của chúng tôi!');
    }

    public function toArray($notifiable)
    {
        $discount = $this->discounts[$this->tier] ?? 0;
        
        return [
            'message' => 'Chúc mừng bạn đã lên hạng ' . $this->tier . '! Được giảm ' . $discount . '% cho mỗi đơn hàng.',
            'url' => route('me.profile'),
            'icon' => '🎉'
        ];
    }
}
