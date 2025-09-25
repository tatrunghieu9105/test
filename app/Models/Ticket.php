<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Ticket extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'showtime_id', 'seat_id',
        'combo_id', 'discount_code_id',
        'price', 'status', 'used_at', 'code'
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($ticket) {
            $ticket->code = 'TKT-' . substr(md5(uniqid()), 0, 10);
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function showtime()
    {
        return $this->belongsTo(Showtime::class);
    }

    public function seat()
    {
        return $this->belongsTo(Seat::class);
    }

    public function combo()
    {
        return $this->belongsTo(Combo::class);
    }

    public function discount()
    {
        return $this->belongsTo(DiscountCode::class, 'discount_code_id');
    }
}
