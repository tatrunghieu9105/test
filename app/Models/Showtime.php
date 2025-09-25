<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Showtime extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'movie_id', 'room_id', 'start_time',
        'end_time', 'price'
    ];


    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('start_time', '>=', now())->orderBy('start_time');
    }

    /**
     * Lấy giá vé dựa trên loại ghế
     *
     * @param string $type Loại ghế ('standard' hoặc 'vip')
     * @return float Giá vé
     */
    public function getPriceForSeatType(string $type = 'standard'): float
    {
        $multiplier = $type === 'vip' ? 1.5 : 1.0;
        return round($this->price * $multiplier, -3); // Làm tròn đến hàng nghìn
    }

    public function overlaps(string $start, string $end): bool
    {
        return $this->where('room_id', $this->room_id)
            ->whereNull('deleted_at')
            ->where(function($q) use ($start, $end) {
                $q->whereBetween('start_time', [$start, $end])
                  ->orWhereBetween('end_time', [$start, $end])
                  ->orWhere(function($q2) use ($start, $end){
                      $q2->where('start_time', '<=', $start)
                         ->where('end_time', '>=', $end);
                  });
            })->exists();
    }
}
