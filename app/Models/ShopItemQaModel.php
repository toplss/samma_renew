<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopItemQaModel extends Model
{
    //
    protected $table = 'g5_shop_item_qa';
    protected $primaryKey = 'iq_id';
    public $timestamps = false;

    protected $fillable = [
        'it_id',
        'it_mb_num',
        'it_mb_id',
        'mb_id',
        'iq_secret',
        'iq_gubun',
        'iq_name',
        'iq_email',
        'iq_hp',
        'iq_password',
        'iq_subject',
        'iq_question',
        'iq_answer',
        'iq_time',
        'iq_ans_time',
        'iq_ip',
        'chk',
        'chk_ans',
    ];
}
