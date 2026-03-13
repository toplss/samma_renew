<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TbMember extends Model
{
    //
    protected $table = 'tb_member';
    protected $primaryKey = 'mb_num';

    public $timestamps = false; 

    protected $fillable = [
        'mb_id',
        'mb_name',
        'mb_password',
        'login_ip',
        'login_count',
        'login_date',
        'remember_token',
    ];

}
