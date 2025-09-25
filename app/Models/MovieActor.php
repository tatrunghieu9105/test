<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class MovieActor extends Pivot
{
    protected $table = 'movie_actors';

    protected $fillable = ['movie_id', 'actor_id'];
}
