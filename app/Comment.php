<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    //
    protected $fillable = [
        'user_id', 
        'video_id', 
        'name', 
        'text', 
        'sub', 
        'comment_id'
		];
		
		public function comments(){
			return $this->hasMany('\App\Comment');
		}
}
