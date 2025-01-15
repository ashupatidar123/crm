<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VesselDocument extends Model{
    use HasFactory, SoftDeletes;
    protected $table = 'vessel_documents';

    function single_document(){
        return $this->hasOne(Document::class,'id','document_id')->select('id','category_name');
    }

    function single_vessel(){
        return $this->hasOne(Vessel::class,'id','vessel_id')->select('id','vessel_name');
    }
}