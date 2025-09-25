<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DiscountCode extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'discount_codes';

    protected $fillable = [
        'code', 'type', 'value', 'min_order_value', 'start_date', 'end_date'
    ];

    protected $casts = [
        'min_order_value' => 'float',
        'value' => 'float',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}
