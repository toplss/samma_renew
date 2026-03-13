<?php
/**
 * Class Name : LoginStatsService
 * Description : 로그인 통계 서비스
 * Author : Kim Hairyong
 * Created Date : 2026-03-07
 * Version : 1.0
 * 
 * History :
 *   - 2026-03-07 : Initial creation
 */

namespace App\Services;

use Illuminate\Support\Facades\DB;

class LoginStatsService 
{

    /**
     * method Name : getTodayLoginCount
     * Description : 당일 로그인 수
     * Created Date : 2026-03-07
     * Params : Params
     * History :
     *   - 2026-03-07 : Initial creation
     */
    public function getTodayLoginCount()
    {
        $result = DB::table('tb_login')
            ->whereDate('reg_date', today())
            ->whereIn('login_gubun', ['pc', 'mobile'])
            // ->distinct()
            ->count('ss_num');

        return $result;
    }    


    /**
     * method Name : getThisMonthLoginCount
     * Description : 당월 로그인 수
     * Created Date : 2026-03-07
     * Params : Params
     * History :
     *   - 2026-03-07 : Initial creation
     */
    public function getThisMonthLoginCount()
    {

        $result = DB::table('tb_login')
            ->whereYear('reg_date', now()->year)
            ->whereMonth('reg_date', now()->month)
            ->whereIn('login_gubun', ['pc', 'mobile'])
            ->count('ss_num');

        return $result;
    }        



}