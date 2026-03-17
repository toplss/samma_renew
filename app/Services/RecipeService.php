<?php
/**
 * Class Name : RecipeService
 * Description : 레시피 서비스
 * Author : Kim Hairyong
 * Created Date : 2026-03-16
 * Version : 1.0
 * 
 * History :
 *   - 2026-03-16 : Initial creation
 */

namespace App\Services;

use Illuminate\Support\Facades\DB;

class RecipeService 
{


    /**
     * method Name : RecipeCategory
     * Description : 레시피 분류
     * Created Date : 2026-03-16
     * Params : Params
     * History :
     *   - 2026-03-16 : Initial creation
     */
    public  function getRecipeCategory($member, $request)
    {

        if ($member) {
            if(substr($member['mb_level'],0,2) >= '70' && substr($member['mb_level'],0,2) <= '90' || $member['mb_launching'] == '10') { // 에스엠
                $banner_opt = "10";
            } else if($member['mb_launching'] == '20') { // 비바쿡
                $banner_opt = "20";
            } else if($member['mb_launching'] == '30') { // 카르포스
                $banner_opt = "30";
            } else {
                $banner_opt = "";
            }

        }

        $result = DB::table('tb_recipe_category')
                ->select(
                    'ca_id', 
                    'ca_name', 
                    'ca_img1', 
                    'ca_icon1', 
                    'ca_use'
                )
                ->whereRaw("LENGTH(ca_id) = 4")
                ->when($banner_opt, function($query) use ($banner_opt) {
                    $query->whereRaw("SUBSTRING(ca_id,1,2) = ?", [$banner_opt]);
                })
                ->where('ca_use', '1')
                ->orderBy('ca_order')
                ->orderBy('ca_id', 'asc')
                ->get()
                ->map(function ($item) {
                    return (array) $item;
                })
                ->toArray();

        return $result;
    }    


    /**
     * method Name : getSubRecipeCategory
     * Description : 레시피 하위 분류
     * Created Date : 2026-03-16
     * Params : Params
     * History :
     *   - 2026-03-16 : Initial creation
     */
    public  function getSubRecipeCategory($member, $request)
    {

        if ($member) {
            if(substr($member['mb_level'],0,2) >= '70' && substr($member['mb_level'],0,2) <= '90' || $member['mb_launching'] == '10') { // 에스엠
                $banner_opt = "10";
            } else if($member['mb_launching'] == '20') { // 비바쿡
                $banner_opt = "20";
            } else if($member['mb_launching'] == '30') { // 카르포스
                $banner_opt = "30";
            } else {
                $banner_opt = "";
            }

        }


        $subQuery = DB::table('tb_recipe')
            ->selectRaw('COUNT(*) as cnt, opt, opt2, opt3')
            ->where('state', '2')
            ->groupBy('opt', 'opt2', 'opt3');

        $result = DB::table('tb_recipe_category')
            ->select(
                'ca_id',
                'ca_name',
                'ca_img1',
                'ca_icon1',
                'ca_use',
                'category.cnt'
            )
            ->leftJoinSub($subQuery, 'category', function ($join) {
                $join->on(DB::raw('SUBSTRING(tb_recipe_category.ca_id, 1, 2)'), '=', 'category.opt')
                    ->on(DB::raw('SUBSTRING(tb_recipe_category.ca_id, 1, 4)'), '=', 'category.opt2')
                    ->on('tb_recipe_category.ca_id', '=', 'category.opt3');
            })
            ->where('ca_use', '1')
            ->whereRaw("SUBSTRING(ca_id,1,2) = $banner_opt")
            ->whereRaw('LENGTH(ca_id) = 6')
            ->get()
            ->map(function ($item) {
                return (array) $item;
            })
            ->toArray();

        return $result;
    }    
    

}