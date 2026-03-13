<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ShopCategoryModel extends Model
{
    //
    protected $table = 'g5_shop_category';
    protected $primaryKey = 'idx';

    public $timestamps = false;


    // 카테고리 리스트
    public static function getMainCategoryList()
    {
        return self::select(['ca_id', 'ca_name', 'ca_img1', 'ca_icon1', 'ca_use'])
        ->where(DB::raw('LENGTH(ca_id)'), '2')
        ->where('ca_use', '1')
        ->where('ca_display', '1')
        ->orderBy('ca_order', 'asc')
        ->orderBy('ca_id', 'asc')
        ->get()->toArray();
    }


    public static function getMainCategorySubList()
    {
        return self::select(['ca_id', 'ca_name', 'ca_img1', 'ca_icon1', 'ca_use'])
        ->where(DB::raw('LENGTH(ca_id)'), '4')
        ->where('ca_use', '1')
        ->orderBy('ca_order', 'asc')
        ->orderBy('ca_id', 'asc')
        ->get()->toArray();
    }
}
