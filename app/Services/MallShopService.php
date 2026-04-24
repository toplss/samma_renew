<?php
/**
 * Class Name : MallShopService
 * Description : 쇼핑몰관리 통합 서비스
 * Author : Lee Sangseung
 * Created Date : 2026-01-14
 * Version : 1.0
 * 
 * History :
 *   - 2026-01-14 : Initial creation
 */

namespace App\Services;

use App\Models\ShopCategoryModel;
use App\Models\ShopItem;
use App\Models\TbMember;
use App\Models\TbNewWin;
use Carbon\Carbon;
use Debugbar;
use Illuminate\Support\Facades\Redis;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use App\Services\ShopCartService;
use App\Traits\RedisTrait;
use App\Traits\CommonTrait;
use Barryvdh\Debugbar\Facades\Debugbar as FacadesDebugbar;
use DebugBar\DebugBar as DebugBarDebugBar;
use Illuminate\Support\Facades\DB;
use App\Models\ShopOrderModel;


class MallShopService 
{
    use RedisTrait, CommonTrait;

    // 회원정보 조회
    public function getMemberInfo($mb_code)
    {
        $arr_member = [];

        /**
         * Author : Lee Sangseung
         * Description : 회원정보 캐시제거 (중복 로그인 후 결제시 기존 채권액을 가지고 있어 a 유저 결제후 채권 또는 금액변동 , b 결제금액 변동 감지안됨 ? )
         * Created Date : 2026-03-18
         */

        if ($mb_code) {
            
            $arr_member = TbMember::where('mb_code', $mb_code)->get()->first();

            $reatimeBalance = ShopOrderModel::realTimeOrderTopPoints($mb_code);

            $none_pt_charge =ShopOrderModel::realTimeNonePaymentOrderTopChargePoints($mb_code);

            $payment_type = '';
            $od_misu  = 0;
            if (isset($arr_member['level_ca_id2'])) {
                if (strlen($arr_member['level_ca_id2']) == 4) {
                    if (substr($arr_member['level_ca_id2'], -1) == '1') $payment_type = '선불';
                    if (substr($arr_member['level_ca_id2'], -1) == '2') $payment_type = '후불';
                }
            }

            if ($payment_type == '선불') {
                $od_misu = ShopOrderModel::preparePaymentMemberMisu($mb_code);
            }

            $arr_member['mb_point'] = 
                (isset($reatimeBalance->put_charge) ? $reatimeBalance->put_charge : 0)
                - ($none_pt_charge ?? 0);

            $arr_member['mb_point_reserve'] = 
                isset($reatimeBalance->put_reserve) ? $reatimeBalance->put_reserve : 0;

            $arr_member['mb_point_balance'] = 
                isset($reatimeBalance->put_balance) ? $reatimeBalance->put_balance - $od_misu : 0;
        }


        if ($arr_member) {

            $vr_account_info = $arr_member['mb_virtual_account'] ?? '';

            $str = substr($vr_account_info,0,3) . '-' .
            substr($vr_account_info,3,6) . '-' .
            substr($vr_account_info,9,2) . '-' .
            substr($vr_account_info,11);

            $arr_member['vr_account_info'] = $str;

            $arr_member['delivery_info'] = $this->payInfomation($arr_member);

            $service = app(ShopCartService::class);
            
            $cost = $service->getMemberCostField($arr_member);
            $arr_member['field_it_price']       = $cost['field_it_price'];
            $arr_member['field_it_price_unit']  = $cost['field_it_price'];
        }

        return $arr_member ?? [];
    }



    /**
     * 회원 배송요일 조회
     *
     * @param [type] $member
     * @return array
     */
    public function payInfomation($member)
    {
        $siteInfo = $this->getSiteInfo();

        $now     = Carbon::now();
        $nowDay  = $now->dayOfWeekIso; // 1~7
        
        if ($member['mb_branch_code']) {
            $deliveryTimeLimitHour   = isset($siteInfo['de_order_hour_'.$member['mb_branch_code']]) 
                ? $siteInfo['de_order_hour_'.$member['mb_branch_code']] 
                : $now->hour;

            $deliveryTimeLimitMinute = isset($siteInfo['de_order_minute_'.$member['mb_branch_code']]) 
                ? $siteInfo['de_order_minute_'.$member['mb_branch_code']] 
                : $now->minute;

            $deliveryTimeLimitSecond = isset($siteInfo['de_order_second_'.$member['mb_branch_code']]) 
                ? $siteInfo['de_order_second_'.$member['mb_branch_code']] 
                : $now->second;
            
            $cutTime = Carbon::today()->setTime($deliveryTimeLimitHour, $deliveryTimeLimitMinute, $deliveryTimeLimitSecond);
        } else {
            $cutTime = Carbon::today()->setTime(12, 10);
        }
        

        $weekName = ['월','화','수','목','금','토','일'];

        $weekMap = [
            1 => 'mb_cs_mon',
            2 => 'mb_cs_tue',
            3 => 'mb_cs_wed',
            4 => 'mb_cs_thu',
            5 => 'mb_cs_fri',
            6 => 'mb_cs_sat',
            7 => 'mb_cs_sun'
        ];

            
        $startOffset = $now->lte($cutTime) ? 1 : 2;
        // 마감 전 → 내일(1)
        // 마감 후 → 모레(2)

        // ------------------------------------
        // 배송 날짜 계산
        // ------------------------------------
        $addDay = null;

        for ($i = $startOffset; $i <= 7 + $startOffset; $i++) {

            $checkDay = (($nowDay + $i - 1) % 7) + 1;

            if (
                isset($member[$weekMap[$checkDay]]) &&
                strtolower($member[$weekMap[$checkDay]]) === 'y'
            ) {
                $addDay = $i;
                break;
            }
        }


        // 예외 처리
        if ($addDay === null) {
            return [
                'ship_date'     => null,
                'delivery_day'  => null,
                'd_od_delivery_date' => null,
                'mb_virtual_account' => null
            ];
        }

        // 날짜 계산
        $shipDate = $now->copy()->addDays($addDay);
        $shipWeek = $weekName[$shipDate->dayOfWeekIso - 1];
        $result = $shipDate->format('m월 d일') . ' (' . $shipWeek . ')';
        
        $vr_account_info = $member['mb_virtual_account'];

        $str = '';
        
        if ($vr_account_info) {
            $str = substr($vr_account_info,0,3) . '-' .
            substr($vr_account_info,3,6) . '-' .
            substr($vr_account_info,9,2) . '-' .
            substr($vr_account_info,11);
        }

        

        return [
            'ship_date'     => $shipWeek,
            'delivery_day'  => $result,
            'd_od_delivery_date'  => $shipDate->format('Y-m-d'),
            'mb_virtual_account' => $str
        ];
    }



    /****************** 카테고리 생성 ******************/
    public function getCategoryList()
    {
        $ttl = 24 * 60 * 60;
        $redis_key_generate = 'mall:category';

        if (Redis::exists($redis_key_generate)) {
            
            $data = json_decode(Redis::get($redis_key_generate), true);

        } else {
            $data = [];
            $result     = ShopCategoryModel::getMainCategoryList();
            $sub_result = ShopCategoryModel::getMainCategorySubList();
            
            foreach ($result as $key => $row) {
                $subCategoryList = $this->getSubCategory($sub_result, $row['ca_id']);

                if (count($subCategoryList) > 0) {
                    $data['left'][$key] = $row;
                    $data['left'][$key]['sub_category'] = $subCategoryList;
                } else {
                    $data['right'][$key] = $row;
                }
            }

            foreach ($result as $key => $row) { 
                $data['mobile'][$key] = $row; 
                $subCategoryList = $this->getSubCategory($sub_result, $row['ca_id']);

                if (count($subCategoryList) > 0) {
                    $data['mobile'][$key]['sub_category'] = $subCategoryList;
                }
            }
            Redis::set($redis_key_generate, json_encode($data, JSON_UNESCAPED_UNICODE), 'EX', $ttl);
        }

        return $data;
    }


    private function getSubCategory(array $data, $ca_id) : array
    {
        $subData = [];
        foreach ($data as $key => $row) {
            $sub_ca_id = substr($row['ca_id'], 0, 2);
            if ($sub_ca_id == $ca_id) {
                $subData[] = $row;
            }
        }

        return $subData;
    }
    /****************** 카테고리 생성 ******************/




    /****************** 마이페이지 장바구니 정보 생성 ******************/
    public function mypageTopInfo($request, $mb_code)
    {
        $cart_cnt = DB::table('g5_shop_cart')->where('mb_code', $mb_code)
        ->where('ct_status', '쇼핑')
        ->where('ct_cate', '!=', '충전금구매')
        ->count();

        $order_cnt = DB::table('g5_shop_order')->where('mb_code', $mb_code)
        ->whereIn('od_gubun', ['매출', '충전금구매'])
        ->when($request->start_date && $request->end_date, function ($query) use ($request) {
            $query->whereBetween('order_date', [
                Carbon::parse($request->start_date)->startOfDay(),
                Carbon::parse($request->end_date)->endOfDay()
            ]);
        })
        ->count();


        $equip_cnt = DB::table('tb_tmp_choice_equipment')->where('t_no_mbcode', $mb_code)
        ->where('t_it_id_type', '5')
        ->count();


        return [
            'cart_cnt'  => $cart_cnt,
            'order_cnt' => $order_cnt,
            'equip_cnt' => $equip_cnt,
        ];
    }
    /****************** 마이페이지 장바구니 정보 생성 ******************/




    public function siteInfo()
    {
        $redis_key_generate = 'site_info';

        if (Redis::exists($redis_key_generate)) {
            $siteInfo = self::getReids($redis_key_generate);
        } else {
            $siteInfo = self::getSiteInfo();

            self::setRedis($redis_key_generate, $siteInfo);
        }

        return $siteInfo;
    }
}