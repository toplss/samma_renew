<?php
/**
 * Class Name : ShopCartApi
 * Description : 장바구니 담기, 삭제, 조회
 * Author : Lee Sangseung
 * Created Date : 2026-01-14
 * Version : 1.0
 * 
 * History :
 *   - 2026-01-14 : Initial creation
 */
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ShopCart;
use App\Models\ShopItem;
use App\Services\MallMainServices;
use App\Services\ShopCartService;
use App\Traits\CommonTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Debugbar;
use Illuminate\Database\QueryException;


class ShopCartApi extends Controller
{
    use CommonTrait;


    public function execute(Request $request)
    {
        try {
            $service = app(\App\Services\MallShopService::class);
            $member  = $service->getMemberInfo(session('ss_mb_code'));

            $mode  = $request->input('mode');
            $it_id = $request->input('it_id');

            if (!$member) throw new \Exception("회원정보가 존재하지 않습니다.");


            /***** 장바구니 검수  *****/
            if ($mode !== 'all_cart_delete' && $mode !== 'cart_delete' && $mode !== 'cart_selected_delete'  && $mode !== 'show') 
            {
                if (!$it_id) {
                    throw new \Exception("상품이 존재하지 않습니다.");
                }

                if ($request->input('it_id')) {
                    $cnt_exists = ShopItem::where('it_id', $it_id)
                    ->where(function($query) {
                        $query->where('it_soldout', '1')
                            ->orWhere('it_force_soldout', 10);
                    })
                    ->exists();

                    if ($cnt_exists) {
                        throw new \Exception("품절된 상품 입니다.");
                    }
                    

                    $it_price = ShopItem::where('it_id', $it_id)
                    ->value('it_price1');
                    
                    if (!$it_price || $it_price <= 0) {
                        throw new \RuntimeException("구매할 수 없는 상품입니다.");
                    }

                    if (trim($member['mb_gubun_type']) == 'employee') {
                        throw new \Exception("직원 계정은 장바구니 사용이 불가능합니다.");
                    }
                }
            }
            /***** 장바구니 검수  *****/



            /***** 장바구니 실행  *****/
            switch ($mode) {
                case 'cart_insert' : 
                    $res = $this->cart_insert($request, $member);
                    if (!$res) throw new \Exception("재고가 부족합니다.");
                break;
                case 'charge_insert' : 
                    $res = $this->charge_insert($request, $member);
                    if (!$res) {
                        throw new \Exception("충전금 구매에 실패했습니다.");
                    }
                    break;
                case 'cart_update' : 
                    $res = $this->cart_update($request, $member);
                    if (!$res) throw new \Exception("수량을 확인 하세요.");
                break;
                case 'cart_delete' : 
                    $res = $this->cart_delete($request, $member);
                    if (!$res) throw new \Exception("삭제에 실패했습니다. 다시 시도해 주세요.");
                break;
                case 'cart_selected_delete' : 
                    $res = $this->cart_selected_delete($request, $member);
                    if (!$res) throw new \Exception("삭제에 실패했습니다. 다시 시도해 주세요.");
                break;
                case 'all_cart_delete' : 
                    $mb_code    = $member['mb_code'];
                    if (!$mb_code) throw new \Exception("로그인 후 이용 가능합니다.");
                    ShopCart::where('mb_code', $mb_code)->where('ct_status', '쇼핑')->delete();
                break;
            }
            /***** 장바구니 실행  *****/


            return $this->convertResponseData([
                'status'  => 'success', 
                'message' => '',
                'data' => [
                    'cartList'       => $cartList = $this->getCartList($request, $member),
                    'member'         => $member,
                    'infomation'     => $service->payInfomation($member),
                    'deilivery_cost' => $this->deliveryCost(
                        $member, 
                        array_sum(array_column($cartList, 'pt_sales'))
                    ),
                    'waitInfo' => $this->wait_infos($member),
                    'siteInfo' => $this->getSiteInfo(),
                ],
                'res' => $res ?? ''
            ]);
        
        } catch (QueryException $e) {
            Log::error('Database Query Error: ' . $e->getMessage());

            return $this->convertResponseData([
                'status'=> 'fail', 
                'message' => '상품 담기에 실패했습니다. 잠시 후 다시 시도하시거나 장바구니를 초기화해주세요.',
                'data' => [
                    'cartList'       => $cartList = $this->getCartList($request, $member),
                    'member'         => $member,
                    'infomation'     => $service->payInfomation($member),
                    'deilivery_cost' => $this->deliveryCost(
                        $member, 
                        array_sum(array_column($cartList, 'pt_sales'))
                    ),
                    'waitInfo' => $this->wait_infos($member),
                    'siteInfo' => $this->getSiteInfo(),
                ],
                'res' => ''
            ]);

        } catch (\Exception $e) {
            return $this->convertResponseData([
                'status'=> 'fail', 
                'message' => $e->getCode() == '' ? $e->getMessage() : '상품 담기가 실패 하였습니다. 다시 시도해 주세요.',
                'data' => [
                    'cartList'       => $cartList = $this->getCartList($request, $member),
                    'member'         => $member,
                    'infomation'     => $service->payInfomation($member),
                    'deilivery_cost' => $this->deliveryCost(
                        $member, 
                        array_sum(array_column($cartList, 'pt_sales'))
                    ),
                    'waitInfo' => $this->wait_infos($member),
                    'siteInfo' => $this->getSiteInfo(),
                ],
                'res' => ''
            ]);
        }
    }



    # 배송비 조회
    private function deliveryCost($member, $cartPrice)
    {
        $delivery_cost = 0;

        switch($member['mb_sendcost']) {
            case '1' : 
                $delivery_cost = 0;
                break;
            case '6' :
                $config = DB::table('tb_config_sendcost')->select(['send_cost_limit', 'send_cost_list'])
                ->where('opt', $member['level_ca_id'])
                ->where('gubun_type', '2')
                ->where('state', '2')
                ->get()
                ->map(function($row) {
                    return (array) $row;
                })
                ->toArray()[0];

                if ($cartPrice < $config['send_cost_limit']) {
                    $delivery_cost = $config['send_cost_list'];
                } else {
                    $delivery_cost = 0;
                }
                break;
            case '7' :
                if ($cartPrice < $member['mb_order_amount_select']) {
                    $delivery_cost = $member['mb_sendcost_amount_select'];
                } else {
                    $delivery_cost = 0;
                }
                break;
        }

        return $delivery_cost;
    }


    # 카트목록 조회
    private function getCartList($request, $member)
    {
        $mb_code = $member['mb_code'];
        $path    = $request->input('path', '');

        if ($path == '/mypage/cart') {
            $selected = [
                'g5_shop_cart.ct_id',
                'g5_shop_cart.it_name',
                'g5_shop_cart.it_id',
                'g5_shop_cart.ct_qty',
                'g5_shop_cart.ct_price',
                DB::raw('g5_shop_cart.ct_price * g5_shop_cart.ct_qty AS pt_sales'),
                'si.it_img1',
                'si.it_storage',
                'si.it_gubun1',
                'si.it_soldout',
                'si.it_basic',
                'si.it_type3',
                'si.it_type4',
                'si.it_type5',

                DB::raw("
                    CONCAT_WS('#',
                        IF(si.it_type1 = '1', '초특가', NULL),
                        IF(si.it_type3 = '1', '신상품', NULL),
                        IF(si.it_type4 = '1', '베스트', NULL),
                        IF(si.it_type5 = '1', '이달의행사', NULL),
                        IF(si.it_type8 = '1', '비바쿡', NULL),
                        IF(si.it_type9 = '1', '마이그랑', NULL)
                    ) as it_type_label
                "),

                'si.it_force_soldout',
                'si.it_gubun',
                'si.it_buy_max_qty',
                'si.it_buy_min_qty',
                'si.it_box_sale_pcs',
                'si.it_box_sale_pack',
                'si.it_box_sale_tot',
                'si.it_return_use',
                DB::raw("CONCAT('') as it_price_piece_use"),
                DB::raw("CONCAT('') as it_cust_price"),
                DB::raw("
                    CASE 
                        WHEN si.it_return = '1' THEN '반품가능'
                        WHEN si.it_return = '2' THEN '반품불가'
                    END AS it_return_label
                "),
                DB::raw("
                    CASE 
                        WHEN si.it_storage = '1' THEN '상온'
                        WHEN si.it_storage = '2' THEN '냉동'
                        WHEN si.it_storage = '3' THEN '냉장'
                    END AS it_storage_label
                "),
            ];

        } else {
            $selected = [
                'g5_shop_cart.ct_id',
                'g5_shop_cart.it_name',
                'g5_shop_cart.it_id',
                'g5_shop_cart.ct_qty',
                'g5_shop_cart.ct_price',
                DB::raw('g5_shop_cart.ct_price * g5_shop_cart.ct_qty AS pt_sales'),
                'si.it_img1',
                'si.it_storage',
                'si.it_gubun1',
                'si.it_soldout',
                'si.it_force_soldout',
                'si.it_basic',
            ];
        }

        if ($mb_code) {
            return ShopCart::join('g5_shop_item AS si', 'g5_shop_cart.it_id', '=', 'si.it_id')
            ->where('mb_code', $mb_code)
            ->when($request->input('order_type'), function($query) use($request) {
                if ($request->input('order_type') == 'items') {
                    $query->where('ca_id', '!=', '10');
                }
                if ($request->input('order_type') == 'charge') {
                    $query->where('ca_id', '10');
                }
            }, function($query) {
                $query->where('ca_id', '!=', '10');
            })
            ->where('ct_status', '쇼핑')
            ->select($selected)

            ->orderBy('si.it_force_soldout', 'DESC')
            ->orderBy('si.it_soldout', 'DESC')
            ->orderBy('si.it_storage')
            ->orderBy('g5_shop_cart.ct_id', 'DESC')

            // ->orderByRaw('it_soldout = 1 DESC ')
            // ->orderByRaw('ct_id DESC ')

            ->get()->toArray();
            
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } else {
            return [];
        }
    }


    # 구매 대기중 조회
    private function wait_infos($member)
    {
        $mb_code = $member['mb_code'];

        $first = DB::table('g5_shop_order as so')
        ->selectRaw("
            COUNT(*) AS cnt,
            MAX(order_date) AS order_date,
            SUM(pt_subtotal) AS pt_subtotal,
            CASE DAYOFWEEK(MAX(order_date))
                WHEN 1 THEN '일요일'
                WHEN 2 THEN '월요일'
                WHEN 3 THEN '화요일'
                WHEN 4 THEN '수요일'
                WHEN 5 THEN '목요일'
                WHEN 6 THEN '금요일'
                WHEN 7 THEN '토요일'
            END AS yoil,
            (
                SELECT it_name
                FROM g5_shop_cart
                WHERE od_id = so.od_id
                AND mb_code = so.mb_code
                AND ct_status IN ('쇼핑', '주문')
                ORDER BY ct_id DESC
                LIMIT 1
            ) AS it_name
        ")
        ->where('od_delivery_step', '0')
        ->where('mb_code', $mb_code)
        ->where('od_settle_case', '=', '금융권가상계좌') // 금융권 가상계좌 주문만 조회 (임시)
        ->get()->toArray();

        if (isset($first[0])) {
            // if (isset($member['mb_virtual_account']) && $member['mb_virtual_account']) {
            //     $first[0]->pt_subtotal = DB::table('TB_RVAS_MAST')->where('VACCT_NO', $member['mb_virtual_account'])
            //     ->where('STAT_CD', '1')
            //     ->where('PAY_AMT', '>', 0)
            //     ->value('PAY_AMT') ?? 0;
            // }
        }


        return json_decode(json_encode($first[0]), true);
    }



    private function cart_insert($request, $member)
    {
        $service    = app(ShopCartService::class);
        $pay_fields = $service->getMemberCostField($member);

        $mb_code    = $member['mb_code'];
        $mb_id      = $member['mb_id'];
        $it_id      = $request->input('it_id');

        if ($request->input('it_id')) {

            return DB::transaction(function() use ($request, $member, $pay_fields, $it_id, $mb_id, $mb_code) {
                $cnt_exists = ShopCart::where('mb_code', $mb_code)
                ->where('it_id', $it_id)
                ->where('ct_status', '쇼핑')
                // ->lockForUpdate()
                ->exists();

                // 해당 상품이 존재하면 업데이트
                if ($cnt_exists) {

                    $item = ShopCart::select([
                        'g5_shop_cart.ct_qty',
                        'g5_shop_cart.ct_price',
                        'g5_shop_cart.ct_qty_tot',
                        'si.it_gubun',
                        'si.it_qty_box',
                        'si.it_qty_pack',
                        'si.it_qty_pcs',
                        'si.it_qty_system_stock',
                        'si.agency_it_buy_min_qty',
                        'si.it_buy_min_qty',
                        'si.it_buy_max_qty',
                    ])
                    ->join('g5_shop_item as si', 'g5_shop_cart.it_id', '=', 'si.it_id')
                    ->where('g5_shop_cart.mb_code', $mb_code)
                    ->where('g5_shop_cart.it_id', $it_id)
                    ->where('g5_shop_cart.ct_status', '쇼핑')
                    ->where('g5_shop_cart.ct_cate', '납품')
                    ->first()->toArray();

                    $tot =  $item['it_qty_'.$item['it_gubun']] * $request->input('ct_qty');

                    $tot += $item['ct_qty_tot'];

                    // 전산재고 보다 많이 담으면
                    if ($tot > $item['it_qty_system_stock']) {
                        return false;
                    }

                    $qtyGroup = $this->ProductUnit($it_id, $tot);

                    $cart = ShopCart::where('it_id', $it_id)->where('mb_code', $mb_code)
                    ->where('ct_status', '쇼핑')
                    ->where('ct_cate', '납품')
                    ->first();

                    if ($cart) {
                        // 기존 수량에 더하기
                        $cart->ct_qty     += $request->input('ct_qty');
                        $cart->ct_qty_box  = $qtyGroup['box'];
                        $cart->ct_qty_pack = $qtyGroup['pack'];
                        $cart->ct_qty_pcs  = $qtyGroup['pcs'];
                        $cart->ct_qty_tot  = $tot;
                        $cart->pt_sales    = $cart->ct_price * ($request->input('ct_qty') + $cart->ct_qty); // 기존 + 추가

                        return $cart->save();
                    }

                } else {
                    // 상품정보
                    $cols = [
                            'it_id',
                            'it_mb_num', 'it_mb_id', 'it_name', 'it_gubun', 'it_gubun1', 'it_gubun2',
                            'it_gubun3', 'it_gubun4', 'it_gubun5', 'it_storage', 'it_sc_type', 'it_sc_method',
                            'it_sc_price', 'it_sc_minimum', 'it_sc_qty',  'it_qty_system_stock',
                            'it_product_location1', 'it_product_location2', 'it_product_location3', 'it_product_location4', 'it_product_location5',
                            'it_qty_box', 'it_qty_pack', 'it_qty_pcs', 'it_point',
                            'it_notax', 'agency_it_buy_min_qty', 'it_buy_min_qty', 'it_buy_max_qty',
                            'it_price_piece',
                            

                            $pay_fields['field_it_price'], 
                            $pay_fields['field_it_price_unit'],
                    ];

                    $item = ShopItem::select($cols)->where('it_id', $it_id)->where('ca_id', '!=', '10')->first();
                    
                    $item = $item ? $item->toArray() : null;

                    // 주문번호 생성
                    $od_id = $this->uniqID($request, $member);

                    // 그룹코드 생성
                    $od_group_code = str_replace("-","", date("Ymd"))."_".$mb_code;

                    $tot = $item['it_qty_'.$item['it_gubun']] * $request->input('ct_qty');

                    $qtyGroup = $this->ProductUnit($it_id, $tot);

                    if ($tot > $item['it_qty_system_stock']) {
                        return false;
                    }


                    $add_cart = [
                        'od_group_code'     => $od_group_code,
                        'od_group_code_org' => $od_group_code,
                        'od_id'     => $od_id,
                        'od_id_org' => $od_id,
                        'od_date'   => date("Ymd"),
                        'mb_code'   => $mb_code,
                        'mb_id'     => $mb_id,
                        'mb_branch_code' => $member['mb_branch_code'],
                        'mb_branch_gubun_type' => $member['mb_branch_gubun_type'],
                        'level_ca_id'  => $member['level_ca_id'],
                        'level_ca_id2' => $member['level_ca_id2'],
                        'level_ca_id3' => $member['level_ca_id3'],
                        'level_ca_id4' => $member['level_ca_id4'],
                        'level_ca_id5' => $member['level_ca_id5'],
                        'mb_level'     => $member['mb_level'],
                        'it_id'        => $it_id,
                        'it_mb_num'    => $item['it_mb_num'],
                        'it_mb_id'     => $item['it_mb_id'],
                        'it_name'      => $item['it_name'],
                        'it_gubun'     => $item['it_gubun'],
                        'it_gubun1'    => $item['it_gubun1'],
                        'it_gubun2'    => $item['it_gubun2'],
                        'it_gubun3'    => $item['it_gubun3'],
                        'it_gubun4'    => $item['it_gubun4'],
                        'it_gubun5'    => $item['it_gubun5'],
                        'it_storage'   => $item['it_storage'],
                        'it_sc_type'   => $item['it_sc_type'],
                        'it_sc_method' => $item['it_sc_method'],
                        'it_sc_price'  => $item['it_sc_price'],
                        'it_sc_minimum'  => $item['it_sc_minimum'],
                        'it_sc_qty'    => $item['it_sc_qty'],
                        'ct_status'    => '쇼핑',
                        'ct_price'     => $item[$pay_fields['field_it_price']],
                        'ct_point'     => $item['it_point'],
                        'ct_point_use' => '0',
                        'ct_stock_use' => '0',
                        'ct_option'    => $item['it_name'],
                        'ct_qty'       => $request->input('ct_qty'),
                        'ct_qty_order' => $request->input('ct_qty'),
                        'ct_cate'      => '납품',
                        'supply_price' => $item[$pay_fields['field_it_price']],
                        'supply_price_unit' => $item[$pay_fields['field_it_price_unit']],
                        'price_unit'   => round($item['it_price_piece']),
                        'pt_sales'     => $item[$pay_fields['field_it_price']] * $request->input('ct_qty'),
                        'ct_qty_box'   => $qtyGroup['box'],
                        'ct_qty_pack'  => $qtyGroup['pack'],
                        'ct_qty_pcs'   => $qtyGroup['pcs'],
                        'ct_qty_tot'   => $item['it_qty_'.$item['it_gubun']] * $request->input('ct_qty'),
                        'ct_notax'     => $item['it_notax'],
                        'ct_tax_mny'   => '',
                        'ct_vat_mny'   => '',
                        'ct_free_mny'  => '',
                        'io_id'        => '',
                        'io_type'      => '0',
                        'io_price'     => '0',
                        'ct_time'      => date('Y-m-d H:i:s'),
                        'ct_ip'        => $_SERVER['REMOTE_ADDR'],
                        'ct_send_cost' => $item['it_sc_type'] == '1' ? '2' : '1',
                        'ct_direct'    => '0',
                        'ct_select'    => '0',
                        'ct_select_time' => date('Y-m-d H:i:s'),
                        'ct_admin_data'=> 'n',
                    ];

                    return ShopCart::create($add_cart);
                }
            });
            
        }
    }



    // 충전금 구매
    private function charge_insert($request, $member)
    {
        $service    = app(ShopCartService::class);
        $pay_fields = $service->getMemberCostField($member);

        $mb_code    = $member['mb_code'];
        $mb_id      = $member['mb_id'];
        $it_id      = $request->input('it_id');

        if ($request->input('it_id')) {

            return DB::transaction(function () use ($mb_code, $mb_id, $it_id, $pay_fields, $request, $member) {
                $cart_exists = ShopCart::where('mb_code', $mb_code)
                ->where('ct_cate', '충전금구매')
                ->where('ct_status', '쇼핑')
                // ->lockForUpdate()
                ->exists();

                if ($cart_exists) {
                    ShopCart::where('mb_code', $mb_code)
                    ->where('ct_status', '쇼핑')
                    ->where('ct_cate', '충전금구매')
                    ->delete();
                }

                // 해당 상품이 존재하면 입력방지
                // 상품정보
                $cols = [
                    'it_id',
                    'it_mb_num', 'it_mb_id', 'it_name', 'it_gubun', 'it_gubun1', 'it_gubun2',
                    'it_gubun3', 'it_gubun4', 'it_gubun5', 'it_storage', 'it_sc_type', 'it_sc_method',
                    'it_sc_price', 'it_sc_minimum', 'it_sc_qty', 'it_price',
                    'it_product_location1', 'it_product_location2', 'it_product_location3', 'it_product_location4', 'it_product_location5',
                    'it_qty_box', 'it_qty_pack', 'it_qty_pcs', 'it_point',
                    'it_notax', 'it_price_piece',
                    
                    $pay_fields['field_it_price'], 
                    $pay_fields['field_it_price_unit'],
                ];

                $item = ShopItem::select($cols)->where('it_id', $it_id)->first();
                
                $item = $item ? $item->toArray() : null;

                // 주문번호 생성
                $od_id = $this->uniqChargeID($request, $member);

                // 그룹코드 생성
                $od_group_code = str_replace("-","", date("Ymd"))."_".$mb_code;


                $add_cart = [
                    'od_group_code'     => $od_group_code,
                    'od_group_code_org' => $od_group_code,
                    'od_id'     => $od_id,
                    'od_id_org' => $od_id,
                    'od_date'   => date("Ymd"),
                    'mb_code'   => $mb_code,
                    'mb_id'     => $mb_id,
                    'mb_branch_code' => $member['mb_branch_code'],
                    'mb_branch_gubun_type' => $member['mb_branch_gubun_type'],
                    'level_ca_id'  => $member['level_ca_id'],
                    'level_ca_id2' => $member['level_ca_id2'],
                    'level_ca_id3' => $member['level_ca_id3'],
                    'level_ca_id4' => $member['level_ca_id4'],
                    'level_ca_id5' => $member['level_ca_id5'],
                    'mb_level'     => $member['mb_level'],
                    'it_id'        => $it_id,
                    'it_mb_num'    => $item['it_mb_num'],
                    'it_mb_id'     => $item['it_mb_id'],
                    'it_name'      => $item['it_name'],
                    'it_gubun'     => $item['it_gubun'],
                    'it_gubun1'    => $item['it_gubun1'],
                    'it_gubun2'    => $item['it_gubun2'],
                    'it_gubun3'    => $item['it_gubun3'],
                    'it_gubun4'    => $item['it_gubun4'],
                    'it_gubun5'    => $item['it_gubun5'],
                    'it_storage'   => $item['it_storage'],
                    'it_sc_type'   => $item['it_sc_type'],
                    'it_sc_method' => $item['it_sc_method'],
                    'it_sc_price'  => $item['it_sc_price'],
                    'it_sc_minimum'  => $item['it_sc_minimum'],
                    'it_sc_qty'    => $item['it_sc_qty'],
                    'ct_status'    => '쇼핑',
                    'ct_price'     => $item['it_price'],
                    'ct_point'     => $item['it_point'],
                    'ct_point_use' => '0',
                    'ct_stock_use' => '0',
                    'ct_option'    => $item['it_name'],
                    'ct_qty'       => $request->input('ct_qty'),
                    'ct_qty_order' => $request->input('ct_qty'),
                    'ct_cate'      => '충전금구매',
                    'supply_price' => $item[$pay_fields['field_it_price']],
                    'supply_price_unit' => $item[$pay_fields['field_it_price_unit']],
                    'price_unit'   => round($item['it_price_piece']),
                    'pt_sales'     => $item[$pay_fields['field_it_price']] * $request->input('ct_qty'),
                    // 'ct_qty_box'   => $qtyGroup['box'],
                    // 'ct_qty_pack'  => $qtyGroup['pack'],
                    // 'ct_qty_pcs'   => $qtyGroup['pcs'],
                    'ct_qty_tot'   => $item['it_qty_'.$item['it_gubun']] * $request->input('ct_qty'),
                    'ct_notax'     => $item['it_notax'],
                    'ct_tax_mny'   => '',
                    'ct_vat_mny'   => '',
                    'ct_free_mny'  => '',
                    'io_id'        => '',
                    'io_type'      => '0',
                    'io_price'     => '0',
                    'ct_time'      => date('Y-m-d H:i:s'),
                    'ct_ip'        => $_SERVER['REMOTE_ADDR'],
                    'ct_send_cost' => $item['it_sc_type'] == '1' ? '2' : '1',
                    'ct_direct'    => '0',
                    'ct_select'    => '1',
                    'ct_select_time' => date('Y-m-d H:i:s'),
                    'ct_admin_data'=> 'n',
                ];

                if (ShopCart::create($add_cart)) {
                    return $od_id;

                } else {

                    return false;
                }
            });
        }
    }




    private function convertResponseData($cartList)
    {
        
        $cartArr    = $cartList['data']['cartList'];
        $memberInfo = $cartList['data']['member'];
        $topINfo    = $cartList['data']['infomation'];
        $cost       = $cartList['data']['deilivery_cost'];
        $waitInfo   = $cartList['data']['waitInfo'];
        $siteInfo   = $cartList['data']['siteInfo'];

        $wait_order_cnt   = $waitInfo['cnt'] ?? 0;
        $wait_order_date  = $waitInfo['order_date'];
        $wait_order_yoil  = $waitInfo['yoil'];
        $wait_it_name     = $waitInfo['it_name'];
        $wait_order_amt   = $waitInfo['pt_subtotal'];
        if (isset($siteInfo['amount_min_'.$memberInfo['level_ca_id']])) {
            $min_order_amount = $siteInfo['amount_min_'.$memberInfo['level_ca_id']];
        } else {
            $min_order_amount = 0;
        }
        

        $cartGubun   = [];
        $cart_sold_out = 0 ;
        foreach ($cartArr as $key => $row) {
            
            $storage = ($row['it_gubun1'] == '1') ? '1' : '2';   // 창고 기준 키            

            // 해당 창고 그룹이 없으면 초기화
            if (!isset($cartGubun[$storage])) {
                $cartGubun[$storage] = [];
            }

            if ($row['it_soldout'] == '1' || $row['it_force_soldout'] == '10') {
                $cart_sold_out++;
            }

            // 해당 창고 배열에 push
            $cartGubun[$storage][] = $row;
        }

        return json_encode([
            'status'    => $cartList['status'],
            'message'   => $cartList['message'],
            'data'      => [
                'total_amount'      => (int) array_sum(array_column($cartArr, 'pt_sales')),
                'cart_items'        => $cartGubun,
                'wait_order_cnt'    => (int) $wait_order_cnt,
                'wait_order_date'   => $wait_order_date,
                'wait_order_yoil'   => $wait_order_yoil,
                'wait_it_name'      => $wait_it_name,
                'wait_order_amount' => (int) $wait_order_amt,
                'min_order_amount'  => (int) $min_order_amount,
                'sold_out'          => $cart_sold_out,
                'deilivery_cost'    => (int) $cost,
                'header_into'       => $topINfo,
                'cartList'          => $cartArr
            ],
            'res' => $cartList['res']
        ]);
    }



    private function uniqID($request, $member)
    {
        $mb_code = $member['mb_code'];
        $mb_id   = $member['mb_id'];

        $uniq = DB::table('g5_uniqid')
        ->where('uq_mb_code', $mb_code)
        ->where('uq_mb_id', $mb_id)
        ->where('uq_gubun', '')
        ->where('uq_chk', '')
        ->orderByDesc('uq_id')
        ->first();

        if ($uniq) {
            $od_id = $uniq->uq_id;
        } else {
            $od_id = $this->make_od_id(); // 기존 od_id 생성 함수

            DB::table('g5_uniqid')->insert([
                'uq_id'       => $od_id,
                'uq_mb_num'   => $mb_code,
                'uq_mb_code'  => $mb_code,
                'uq_mb_id'    => $mb_id,
                'uq_ip'       => $request->ip(),
                'uq_datetime' => Carbon::now()
            ]);
        }

        return $od_id;
    }



    private function uniqChargeID($request, $member)
    {
        $mb_code = $member['mb_code'];
        $mb_id   = $member['mb_id'];

        $uniq = DB::table('g5_uniqid')
        ->where('uq_mb_code', $mb_code)
        ->where('uq_mb_id', $mb_id)
        ->where('uq_gubun', '충전금구매')
        ->where('uq_chk', '')
        ->orderByDesc('uq_id')
        ->first();

        if ($uniq) {
            $od_id = $uniq->uq_id;
        } else {
            $od_id = $this->make_od_id(); // 기존 od_id 생성 함수

            DB::table('g5_uniqid')->insert([
                'uq_id'       => $od_id,
                'uq_mb_num'   => $mb_code,
                'uq_mb_code'  => $mb_code,
                'uq_mb_id'    => $mb_id,
                'uq_gubun'    => '충전금구매',
                'uq_ip'       => $request->ip(),
                'uq_datetime' => Carbon::now()
            ]);
        }

        return $od_id;
    }


    private function cart_update($request, $member)
    {
        $mb_code = $member['mb_code'];
        $it_id   = $request->input('it_id');
        $action  = $request->input('action');

        if (!in_array($action, ['plus', 'minus'])) {
            return false;
        }

        return DB::transaction(function() use ($mb_code, $it_id, $action, $member) {

            $cartItem = DB::table('g5_shop_cart as sc')
            ->join('g5_shop_item as si', 'sc.it_id', '=', 'si.it_id')
            ->where('sc.it_id', $it_id)
            ->where('sc.mb_code', $mb_code)
            ->where('sc.ct_status', '쇼핑')
            ->where('sc.ct_cate', '납품')
            ->select(
                'sc.*',
                'si.it_gubun',
                'si.it_qty_box',
                'si.it_qty_pack',
                'si.it_qty_pcs',
                'si.it_qty_system_stock',
                'si.agency_it_buy_min_qty',
                'si.it_buy_min_qty',
                'si.it_buy_max_qty',
            )
            // ->lockForUpdate()
            ->first();

            if (!$cartItem) {
                return false; // 장바구니에 상품이 없으면 종료
            }

            // 현재 총 수량 계산
            $tot = $cartItem->ct_qty_tot;

            $min_cart_ct_qty = 0;
            if(isset($member['mb_level']) && substr($member['mb_level'], 0, 2) == '30' && $cartItem->agency_it_buy_min_qty > 0) {
                $min_cart_ct_qty = $cartItem->agency_it_buy_min_qty ?? 1; // 주문최소
    
            } else {
                $min_cart_ct_qty = $cartItem->it_buy_min_qty ?? 1;
            }

            switch ($action) {
                case 'plus':
                    $tot += $cartItem->{'it_qty_' . $cartItem->it_gubun} * $min_cart_ct_qty;
                    $newQty  = $cartItem->ct_qty + $min_cart_ct_qty;

                    // 전산재고 보다 많이 담으면
                    if ($tot > $cartItem->it_qty_system_stock) {
                        return false;
                    }
                    break;

                case 'minus':
                    $tot -= $cartItem->{'it_qty_' . $cartItem->it_gubun} * $min_cart_ct_qty;
                    $newQty = $cartItem->ct_qty - $min_cart_ct_qty;

                    if ($tot <= 0 || $newQty <= 0) {
                        return false; // 수량이 0 이하이면 처리 중단
                    }
                    break;

                default:
                    return false; // 정의되지 않은 action
            }

            // 단위 계산 (기존 ProductUnit 함수 재사용)
            $qtyGroup = $this->ProductUnit($it_id, $newQty);

            // 장바구니 업데이트
            return DB::table('g5_shop_cart')
                ->where('it_id', $it_id)
                ->where('mb_code', $mb_code)
                ->where('ct_status', '쇼핑')
                ->where('ct_cate', '납품')
                ->update([
                    'ct_qty'      => $newQty,
                    'ct_qty_box'  => $qtyGroup['box'],
                    'ct_qty_pack' => $qtyGroup['pack'],
                    'ct_qty_pcs'  => $qtyGroup['pcs'],
                    'ct_qty_tot'  => $tot,
                    'pt_sales'    => $cartItem->ct_price * $newQty
                ]);
        });
    }


    private function cart_delete($request, $member)
    {
        $it_id = $request->input('it_id');

        if ($it_id) {
            $mb_code = $member['mb_code'];

            return ShopCart::where(['it_id' => $it_id, 'mb_code' => $mb_code, 'ct_status' => '쇼핑', 'ct_cate' => '납품'])
            ->delete();

        } else {
            return false;
        }
    }


    public function cart_selected_delete($request, $member)
    {
        $it_ids = $request->input('it_ids');

        if ($it_ids && is_array($it_ids)) {
            $mb_code = $member['mb_code'];

            return ShopCart::whereIn('it_id', $it_ids)
            ->where('mb_code', $mb_code)
            ->where('ct_status', '쇼핑')
            ->where('ct_cate', '납품')
            ->delete();

        } else {
            return false;
        }
    }
}