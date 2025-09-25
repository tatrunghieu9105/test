<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Actor extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['name', 'bio', 'birth_date'];

    public function movies()
    {
        return $this->belongsToMany(Movie::class, 'movie_actors');
    }
}
