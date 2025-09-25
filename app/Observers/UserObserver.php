<?php

namespace App\Observers;

use App\Models\PointHistory;
use App\Models\User;
use App\Notifications\TierUpNotification;

class UserObserver
{
    public function updated(User $user)
    {
        // Ghi log lịch sử điểm
        if ($user->wasChanged('points')) {
            $oldPoints = $user->getOriginal('points');
            $newPoints = $user->points;
            $diff = $newPoints - $oldPoints;
            
            if ($diff != 0) {
                $user->pointHistories()->create([
                    'points' => abs($diff),
                    'action' => $diff > 0 ? 'earned' : 'used',
                    'description' => $diff > 0 ? 'Tích điểm từ đặt vé' : 'Sử dụng điểm'
                ]);
            }
        }
    }
}
