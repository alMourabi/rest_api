<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    //

    protected $fillable = [
        'rating', 
        'user_id', 
        'video_id', 
        'name', 
        'text'
    ];
}
