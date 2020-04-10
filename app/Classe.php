<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Classe extends Model
{
		protected $fillable = ['user_id','name'];
		
		public function subjects(){
			return $this->hasMany('\App\Subject');
		}

		public function pdf(){
			return $this->hasMany('\App\ClassPDF');
		}
}
