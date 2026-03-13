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
use Illuminate\Support\Facades\DB;


class MallShopService 
{
    use RedisTrait, CommonTrait;

    // 회원정보 조회
    public function getMemberInfo($mb_code)
    {
        $arr_member = [];

        if ($mb_code) {
            
            $redis_member_key_generate = $mb_code.':member';

            if (Redis::exists($redis_member_key_generate)) {
            
                $arr_member = $this->getReids($redis_member_key_generate);

            } else {
                
                $arr_member = TbMember::where('mb_code', $mb_code)->get()->toArray()[0];


                $this->setRedis($redis_member_key_generate, $arr_member);
            }
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

        // ------------------------------------
        // 배송 날짜 계산
        // ------------------------------------

        $addDay = null;

        // 오늘 가능 + 마감 전
        for ($i = 1; $i <= 7; $i++) {

            $checkDay = (($nowDay + $i - 1) % 7) + 1;
        
            if (strtolower($member[$weekMap[$checkDay]]) === 'y') {
                $addDay = $i;
                break;
            }
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