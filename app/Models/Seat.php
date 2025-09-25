<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Seat extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['room_id', 'code', 'type'];

    protected $casts = [
        'type' => 'string'
    ];
    
    // Lấy số ghế từ mã ghế (ví dụ: A1 -> 1)
    public function getSeatNumberAttribute()
    {
        return preg_replace('/[^0-9]/', '', $this->code);
    }
    
    // Lấy hàng ghế từ mã ghế (ví dụ: A1 -> A)
    public function getSeatRowAttribute()
    {
        return preg_replace('/[^A-Za-z]/', '', $this->code);
    }

    public function isVip()
    {
        return $this->type === 'vip';
    }
    
    public function getFormattedTypeAttribute()
    {
        return $this->type === 'vip' ? 'VIP' : 'Thường';
    }
    
    public function getPriceAttribute($showtimePrice)
    {
        $multiplier = $this->isVip() ? 1.5 : 1.0;
        return round($showtimePrice * $multiplier, -3); // Làm tròn đến hàng nghìn
    }
    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}
