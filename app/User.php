<?php

namespace App;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
// use App\Notifications\VerifyEmail;
use Illuminate\Auth\Notifications\VerifyEmail;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, Notifiable;



    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'fname',
        'lname',
        'phone',
        'birthday',
        'grade',
        'establishment',
        'region',
        'subscribed',
        'subject',
        'admin',
        'email',
        'classe_id',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

		public function subjectclasse(){
			return $this->hasMany('\App\UserSubjectClasse');
		}

    public function set()
    {
        if (ClassPDF::where(['class' => request()->input('class')])->count() != 0)
            $this->pdf = ClassPDF::where(['class' => request()->input('class')])->first()->pdf;
        if (ClassPicture::where(['class' => request()->input('class')])->count() != 0)
            $this->image = ClassPicture::where(['class' => request()->input('class')])->first()->image;
    }

    public function image()
    {
        if (ClassPicture::where(['class' => request()->input('class')])->count() != 0)
            return ClassPicture::where(['class' => request()->input('class')])->first()->image;
        return '';
    }

    public function pdf()
    {
        if (ClassPDF::where(['class' => request()->input('class')])->count() != 0)
            return ClassPDF::where(['class' => request()->input('class')])->first()->pdf;
        return '';
    }

		public function classe(){
			return $this->belongsTo('\App\Classe');
		}

		public function subjects(){
			$subjects = Subject::where(['classe_id'=>$this->classe_id])->get();
			return $subjects;
		}

    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmail); // my notification
    }
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }
}
