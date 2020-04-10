<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
		protected $fillable = ['classe_id','name'];
		
		public function classe(){
			return $this->belongsTo('\App\Classe')->withDefault();
		}

		public function pdf(){
			return $this->hasMany('\App\ClassPDF');
		}

		public function videos(){
			return $this->hasMany('\App\Video');
		}
}
