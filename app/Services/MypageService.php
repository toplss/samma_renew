<?php
/**
 * Class Name : MypageService
 * Description : 마이페이지 서비스
 * Author : Kim Hairyong
 * Created Date : 2026-02-07
 * Version : 1.0
 * 
 * History :
 *   - 2026-02-07 : Initial creation
 */

namespace App\Services;

use Illuminate\Support\Facades\DB;

class MypageService 
{


    /**
     * method Name : FranchiseCategory
     * Description : 가맹점유형 분류
     * Created Date : 2026-01-28
     * Params : Params
     * History :
     *   - 2026-01-28 : Initial creation
     */
    private function FranchiseCategory($ca_id_length)
    {
        $items = DB::table('tb_franchise_category')
                ->select('ca_id', 'ca_name', 'ca_use')
                ->whereRaw('LENGTH(ca_id) = ?', [$ca_id_length])
                ->where('ca_use', '1')
                ->orderBy('ca_order')
                ->orderBy('ca_id', 'asc')
                ->get();

        return $items;
    }    


    /**
     * method Name : FranchiseSubCategory
     * Description : 가맹점유형 하위분류
     * Created Date : 2026-01-28
     * Params : Params
     * History :
     *   - 2026-01-28 : Initial creation
     */
    public function FranchiseSubCategory($ca_id_length, $ca_id)
    {

        $ca_id_length->validate([
            'ca_id' => 'required|integer',
            'ca_id_length' => 'required|integer',
        ]);
        
        $items = DB::table('tb_franchise_category')
                ->select('ca_id', 'ca_name', 'ca_use')
                ->whereRaw('LENGTH(ca_id) = ?', [$ca_id_length])
                ->whereRaw('SUBSTRING(ca_id, 1, 2) = ?', [$ca_id])
                ->where('ca_use', '1')
                ->orderBy('ca_order')
                ->orderBy('ca_id', 'asc')
                ->get();

        return response()->json($items);
    }



}