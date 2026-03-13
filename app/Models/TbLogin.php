<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TbLogin extends Model
{
    //
    protected $table = 'tb_login';
    protected $primaryKey = 'login_idx';
    protected $guarded = []; // 모든 필드를 허용

    
    public $timestamps = false;

    
}
