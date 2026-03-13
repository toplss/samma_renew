<?php
/**
 * Class Name : PointLogService
 * Description : 충전금, 적립금 Log 서비스
 * Author : Kim Hairyong
 * Created Date : 2026-02-20
 * Version : 1.0
 * 
 * History :
 *   - 2026-02-20 : Initial creation
 */


namespace App\Services;

use Illuminate\Support\Facades\DB;

class PointLogService {


    /**
     * method Name : CreatePointLog
     * Description : 로그 insert
     * Created Date : 2026-02-20
     * Params : Params
     * History :
     *   - 2026-02-20 : Initial creation
     */
    public function CreatePointLog ($mbCode, $oid, $poGubun, $poType, $amount) 
    {

        if (!$mbCode || !$oid || !$poGubun || !$poType || $amount === null) {
            throw new \Exception('충전금 또는 적립금 구매시도 정보가 누락되었습니다.');
        }

        $service = app(MallShopService::class);
        $member = $service->getMemberInfo(session('ss_mb_code'));

        switch ($poGubun) {
            case 'CHARGE':  //충전금
                
                $str_gubun = ' 충전금';
                $current_point = $member['mb_point'];

                if ($poType == 'increase') {
                    $po_action = 'od_cancel';
                    $str_type = ' 주문 취소';                    
                    $change_point = $current_point + $amount;
                } elseif ($poType == 'decrease') {
                    $po_action = 'od_use';
                    $str_type = ' 사용';
                    $change_point = $current_point - $amount;
                } else {
                    throw new \Exception('poType 정보가 누락되었습니다.');
                }

                break;

            case 'RESERVE':  //적립금

                $str_gubun = ' 적립금';
                $current_point = $member['mb_point_reserve'];

                if ($poType == 'increase') {
                    $po_action = 'od_cancel';
                    $str_type = ' 주문 취소';
                    $change_point = $current_point + $amount;
                } elseif ($poType == 'decrease') {
                    $po_action = 'od_use';
                    $str_type = ' 사용';
                    $change_point = $current_point - $amount;
                } else {
                    throw new \Exception('poType 정보가 누락되었습니다.');
                }

                break;

        }        

        //po_comment 가공
        $po_comment = '주문번호:'. $oid . $str_gubun . $str_type ;


        $result =  DB::table('tb_member_point_logs')->insert([
                    'po_gubun'          => $poGubun,
                    'po_mb_id'          => $member['mb_id'],
                    'po_mb_code'        => $mbCode,
                    'po_point'          => $amount,
                    'po_point_type'     => $poType,
                    'po_current_point'  => $change_point,
                    'po_fk'             => $oid,
                    'po_action'         => $po_action,
                    'po_comment'        => $po_comment,
                    'po_path'           => 'mall',
                ]);
    
        return $result;

    }

}