<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $table = 'tbl_user';
    protected $fillable = [
        'name',
        'email',
        'password',
    ];


}
