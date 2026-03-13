<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TbRecipeCategory extends Model
{
    //
    protected $table = 'tb_recipe_category';

    public $timestamps = false;
    

    public static function getRecipeCategory()
    {
        return self::select([
            'ca_id', 
            'ca_name', 
            'ca_img1', 
            'ca_icon1', 
            'ca_use'
        ])
        ->whereRaw("LENGTH(ca_id) = 4")
        ->whereRaw("SUBSTRING(ca_id,1,2) = '20'")
        ->where('ca_use', '1')
        ->orderBy('ca_order')
        ->orderBy('ca_id', 'asc')
        ->get();
    }


    public static function getSubRecipeCategory()
    {

        $subQuery = DB::table('tb_recipe')->selectRaw('count(*) as cnt, opt, opt2, opt3')
        ->where('state', '2')
        ->groupBy('opt', 'opt2', 'opt3');

        return self::select('ca_id',
        'ca_name',
        'ca_img1',
        'ca_icon1',
        'ca_use',
        'category.cnt')
        ->leftJoinSub($subQuery, 'category', function($join){
            $join->on(DB::raw('SUBSTRING(tb_recipe_category.ca_id, 1, 2)'), '=', 'category.opt')
             ->on(DB::raw('SUBSTRING(tb_recipe_category.ca_id, 1, 4)'), '=', 'category.opt2')
             ->on('tb_recipe_category.ca_id', '=', 'category.opt3');
        })
        ->whereRaw('LENGTH(ca_id) = 6')
        ->where('ca_use', '1')
        ->get();
    }
}
