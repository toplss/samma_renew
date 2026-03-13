<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\TbErpFile;

class TbBbsBody extends Model
{
    //
    protected $table = 'tb_bbs_body';
    protected $primaryKey = 'bd_num';

    public $timestamps = false;

    protected $fillable = [
        'bbs_code',
        'bd_notice',
        'mb_num',
        'mb_id',
        'bd_name',
        'bd_ext1',
        'bd_subject',
        'bd_content',
        'bd_write_date',
        'bd_modify_date',
    ];


    /**
     * 메인공지
     *
     * @return 
     */
    public static function getMainNoticeList()
    {
        return self::select(['bd_num', 'bd_ext1', 'bd_subject'])
        ->where('bbs_code', 'notice')
        ->where('bd_delete', '0')
        ->where('bd_notice', '1')
        ->orderByDesc('bd_num')
        ->limit(7)
        ->get()
        ->toArray();
    }


    public function files()
    {
        return $this->hasMany(TbErpFile::class, 'table_idx', 'bd_num')->where('table_name', 'tb_bbs_body');
    }
}
