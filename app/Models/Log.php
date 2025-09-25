<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Log extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'admin_id', 'action', 'table_name',
        'record_id', 'description'
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function getDescriptionArrayAttribute(): array
    {
        // Always return array for safe rendering
        return is_array($this->description) ? $this->description : (json_decode($this->description, true) ?: []);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
