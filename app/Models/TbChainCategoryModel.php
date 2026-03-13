<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TbChainCategoryModel extends Model
{
    //
    protected $table = 'tb_chain_category';

    protected $primaryKey = 'idx';

    public $timestamps = false;


    // 메뉴별 배너 조회
    public static function getMenuBannerList()
    {
        return self::select('idx', 'ca_id', 'ca_name', 'ca_img1')
        ->whereRaw("LENGTH(ca_id) = 4")
        ->whereRaw("SUBSTRING(ca_id, 1, 2) = '10'")
        ->whereNotIn('ca_id', ['1001'])
        ->where('ca_use', '1')
        ->whereIn('ca_img_display', ['1'])
        ->get();
    }


    
}
