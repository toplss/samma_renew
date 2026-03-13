<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TbReturnReceptionModel extends Model
{
    //
    protected $table = 'tb_return_reception';
    protected $primaryKey = 'idx';
    
    public $timestamps = false;


    protected $fillable = [
        'req_id',
        'mb_num',
        'mb_id',
        'gubun',
        'term',
        'name',
        'company',
        'email',
        'tel',
        'phone',
        'title_no',
        'title',
        'cnt',
        'addr',
        'contents',
        'url',
        'file1',
        'file2',
        'memo',
        'state',
        'reg_date',
        'modify_date',
    ];
}
