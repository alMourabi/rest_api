<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Code extends Model
{
		protected $fillable = ['user_id','verified','code','point'];
		
}
