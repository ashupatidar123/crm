<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vessel extends Model{
    use HasFactory, SoftDeletes;

    function single_user(){
        return $this->hasOne(Document::class,'id','document_id')->select('id','category_name');
    }
}
