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
        'code', 'type', 'value', 'start_date', 'end_date'
    ];

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}
