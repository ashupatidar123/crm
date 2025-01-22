<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserDocumentAccess extends Model{
    use HasFactory, SoftDeletes;
    protected $table = 'user_document_access';


    function single_user(){
        return $this->hasOne(User::class,'id','user_id')->select('id','name_title','first_name','middle_name','last_name');
    }

    function single_document(){
        return $this->hasOne(UserDocument::class,'id','document_id');
    }
}
