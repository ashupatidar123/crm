<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CompanyBranch extends Model{
    use HasFactory, SoftDeletes;
    protected $table = 'company_branches';

    public function single_company(){
        return $this->hasOne(Company::class,'id','company_id')->select('id','company_name');
    }
}
