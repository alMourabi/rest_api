<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    //
    protected $fillable = [
        'url', 
        'title',
        'user_id', 
        'payed', 
        'thumbnail', 
        'description', 
        'subject_id', 
        'classe_id',
        'class_pdf_id', 
        'type'
    ];

    public function comments(){
        return $this->hasMany(Comment::class);
    }

    public function ratings(){
        return $this->hasMany(Rating::class);
    }

    public function pdf(){
        return $this->belongsTo(ClassPDF::class, 'class_pdf_id');
    }
}
