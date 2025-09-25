<?php

namespace App\Services;

use App\Models\Ticket;
use App\Models\User;

class LoyaltyService
{
    // 1 point per 10,000 VND
    private const POINTS_PER_VND = 1 / 10000;

    public static function awardForTicket(Ticket $ticket): void
    {
        if (!$ticket->user_id) return;
        $user = User::find($ticket->user_id);
        if (!$user) return;

        $earned = (int) floor(($ticket->price ?? 0) * self::POINTS_PER_VND);
        if ($earned <= 0) return;

        $oldPoints = $user->points;
        $user->points = (int)($oldPoints ?? 0) + $earned;
        $user->save();
    }
}
