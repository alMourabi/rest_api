<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ClassPDF extends Model
{
    protected $fillable = ['classe_id', 'subject_id', 'title','pdf', 'type'];
}
