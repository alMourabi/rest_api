<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserSubjectClasse extends Model
{
		protected $fillable = ['user_id','subject_id', 'classe_id'];
		
		public function subject(){
			return $this->belongsTo('\App\Subject')->withDefault();
		}

		public function classe(){
			return $this->belongsTo('\App\Classe')->withDefault();
		}
		
		public function user(){
			return $this->belongsTo('\App\User')->withDefault();
		}
}
