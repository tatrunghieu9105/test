<?php

namespace App\Models;

use App\Models\PointHistory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'fullname', 'points', 'role_id', 'is_active'
    ];
    
    protected $attributes = [
        'points' => 0,
        'role_id' => 3, // ID của role 'customer'
        'is_active' => true,
    ];
    
    protected $casts = [
        'points' => 'integer',
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    protected $hidden = ['password'];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
    
    public function pointHistories()
    {
        return $this->hasMany(PointHistory::class)->orderBy('created_at', 'desc');
    }

    public function logs()
    {
        return $this->hasMany(Log::class, 'admin_id');
    }
    
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
    
}
