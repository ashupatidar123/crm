<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserDocument extends Model{
    use HasFactory, SoftDeletes;
    protected $table = 'user_documents';

    function single_document(){
        return $this->hasOne(Document::class,'id','document_id')->select('id','category_name');
    }
}
