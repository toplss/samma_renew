<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class TbRecipe extends Model
{
    //
    protected $table = 'tb_recipe';
    protected $primaryKey = 'id';

    public $timestamps = false;


    public static function getRecipeBestProduct($recipe_opt)
    {
        return self::select([
            'tb_recipe.id',
            'tb_recipe.title',
            'tb_recipe.opt2_name', 
            'tb_recipe.img1',
            'tb_recipe.p_id'
        ])
        ->join('tb_recipe_category as rec_ct', 'tb_recipe.opt2', '=', 'rec_ct.ca_id')
        ->where('tb_recipe.state', '2')
        ->where(DB::raw('SUBSTRING(rec_ct.ca_id, 1, 2)'), $recipe_opt)
        ->where(DB::raw('LENGTH(rec_ct.ca_id)'), '4')
        ->where('rec_ct.ca_use', '1')
        ->where('tb_recipe.new_state', 'y')
        ->inRandomOrder()
        ->limit(50)
        ->get()
        ->toArray();
    }


    public static function getRecipeItems($request, $recipe_opt)
    {
        $key  = "shop:receipe:".$request->input('opt2', '');
        $lock = "lock:shop:receipe:".$request->input('opt2', '');

        if ($data = Cache::get($key)) {
            return $data;
        }

        
        return Cache::lock($lock, 5)->block(3, function () use ($key, $recipe_opt, $request) { // 5락 유지시간, 3락 대기시간
            if ($data = Cache::get($key)) {
                return $data;
            }
            
            return self::where('state', '2')
            ->when($recipe_opt, function($query) use ($recipe_opt) {
            $query->where('opt', $recipe_opt);
            })
            ->when($request->filled('opt2'), function($query) use ($request) {
                $query->where('opt2', $request->input('opt2'));
            })
            ->when($request->filled('opt3'), function($query) use ($request) {
                $query->where('opt3', $request->input('opt3'));
            })
            ->when($request->filled('best_state') && $request->input('bast_state') == 'y', function($query) {
                $query->where('best_state', 'y');
            })
            ->when($request->filled('recipe_skeyword'), function($query) use ($request) {
                $query->where('title', 'LIKE', '%'.$request->recipe_skeyword.'%');
            })
            ->orderBy('regdate', 'desc')
            ->pluck('id')
            ->toArray();
        });
    }
}
