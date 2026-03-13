<?php
namespace App\Services;

use App\Models\ShopCart;
use App\Models\ShopOrderModel;
use Illuminate\Support\Facades\DB;
use App\Services\MallShopService;

class ShopOrderService
{
    public function execute($od_id, $move_type)
    {
        if (session('ss_mb_code')) {
            $mb_code = session('ss_mb_code');

            $member  = app(MallShopService::class)->getMemberInfo($mb_code);
            $payment = app(PaymentService::class);

            $cartList = ShopCart::where('od_id', $od_id)
            ->select(
                'ct_id', 
                'ct_cate', 
                'it_id',
                'ct_qty',
                'ct_qty_box',
                'ct_qty_pack',
                'ct_qty_pcs',
                'ct_qty_tot',
                'pt_sales'
            )
            ->get();

            $beforeCartShopping = ShopCart::where('mb_code', $mb_code)
                ->where('ct_status', '쇼핑')
                ->where('ct_cate', '!=', '충전금구매')
                ->latest('ct_id')
                ->select('od_id', 'od_group_code')
                ->first();

            if ($beforeCartShopping) {
                foreach ($cartList as $key => $row) {
                    $ct_id   = $row->ct_id;
                    $ct_cate = $row->ct_cate;

                    if ($ct_id && $ct_cate !== '충전금구매') 
                    {
                        $this->incrementCartSystemStock($ct_id);

                        if ($move_type == 'yes') 
                        {
                            $existItem = ShopCart::where('mb_code', $mb_code)
                            ->where('ct_status', '쇼핑')
                            ->where('it_id', $row->it_id)
                            ->where('ct_cate', '!=', '충전금구매')
                            ->select('ct_id')
                            ->first();

                            if ($existItem) 
                            {
                                ShopCart::where('ct_id', $existItem->ct_id)
                                // ->lockForUpdate()
                                ->update([
                                    'ct_qty'   => DB::raw('ct_qty + '.(int)$row->ct_qty),
                                    'ct_qty_box' => DB::raw('ct_qty_box + '.(int)$row->ct_qty_box),
                                    'ct_qty_pack' => DB::raw('ct_qty_pack + '.(int)$row->ct_qty_pack),
                                    'ct_qty_pcs' => DB::raw('ct_qty_pcs + '.(int)$row->ct_qty_pcs),
                                    'ct_qty_tot' => DB::raw('ct_qty_tot + '.(int)$row->ct_qty_tot),
                                    'pt_sales' => DB::raw('pt_sales + '.(int)$row->pt_sales),
                                ]);
                        
                                ShopCart::where('ct_id', $ct_id)->delete();
                            } 
                            else 
                            {
                                DB::table('g5_shop_cart')
                                ->where('ct_id', $row->ct_id)
                                ->where('od_id', $od_id)
                                ->update([
                                    'ct_status'             => '쇼핑',
                                    'od_id'                 => $beforeCartShopping->od_id,
                                    'od_id_org'             => $beforeCartShopping->od_id,
                                    'od_group_code'         => $beforeCartShopping->od_group_code,
                                    'od_group_code_org'     => $beforeCartShopping->od_group_code,
                                ]);
                            }
                        } 
                        else 
                        {
                            ShopCart::where('ct_id', $ct_id)->delete();
                        }
                    } else {
                        ShopCart::where('ct_id', $ct_id)->delete();
                    }
                }
            } else {
                DB::table('g5_uniqid')->where('uq_id', $od_id)->update(['uq_chk' => '']);
            
                foreach ($cartList as $key => $row) {
                    $ct_id = $row->ct_id;

                    if ($ct_id) 
                    {
                        $this->incrementCartSystemStock($ct_id);

                        if ($move_type == 'yes') 
                        { 
                            $existItem = ShopCart::where('mb_code', $mb_code)
                            ->where('ct_status', '쇼핑')
                            ->where('it_id', $row->it_id)
                            ->where('ct_cate', '!=', '충전금구매')
                            ->select('ct_id')
                            ->first();

                            if ($existItem) 
                            {
                                ShopCart::where('ct_id', $existItem->ct_id)
                                // ->lockForUpdate()
                                ->update([
                                    'ct_qty'   => DB::raw('ct_qty + '.(int)$row->ct_qty),
                                    'ct_qty_box' => DB::raw('ct_qty_box + '.(int)$row->ct_qty_box),
                                    'ct_qty_pack' => DB::raw('ct_qty_pack + '.(int)$row->ct_qty_pack),
                                    'ct_qty_pcs' => DB::raw('ct_qty_pcs + '.(int)$row->ct_qty_pcs),
                                    'ct_qty_tot' => DB::raw('ct_qty_tot + '.(int)$row->ct_qty_tot),
                                    'pt_sales' => DB::raw('pt_sales + '.(int)$row->pt_sales),
                                ]);
                        
                                ShopCart::where('ct_id', $ct_id)->delete();
                            } 
                            else 
                            {
                                ShopCart::where('ct_id', $ct_id)->where('od_id', $od_id)->update(['ct_status' => '쇼핑']);
                            }
                        } 
                        else 
                        {
                            ShopCart::where('ct_id', $ct_id)->where('od_id', $od_id)->delete();
                        }
                    }
                }                
            }

            /** 충전금 + 적립금 + 채권액 리턴 */
            $orderDt = $payment->resultData($od_id);
            if (isset($orderDt['level_ca_id2'])) {
                if (strlen($orderDt['level_ca_id2']) == 4) {
                    if (substr($orderDt['level_ca_id2'], -1) == '1') $payment_type = '선불';
                    if (substr($orderDt['level_ca_id2'], -1) == '2') $payment_type = '후불';
                }
            }

            $orderDt['payment_type'] = $payment_type;

            if ($orderDt['od_temp_point'] > 0 || $orderDt['od_temp_point_reserve'] > 0 || $orderDt['pt_subtotal'] > 0) {
                $payment->setOrderCancelMemberPoints($orderDt);
            }
            /** 충전금 + 적립금 + 채권액 리턴 */


            // 결제방법 -- 금융권가상계좌 초기화
            if (isset($member['mb_virtual_account']) && $member['mb_virtual_account'] && $orderDt['od_settle_case'] == '금융권가상계좌') {
                DB::table('TB_RVAS_MAST')->where('VACCT_NO', $member['mb_virtual_account'])
                ->where('CUST_CD', $member['mb_code'])
                ->where('STAT_CD', '1') // 사용중인 가상계좌
                ->update([
                    'PAY_AMT' => 0,
                    'modifydate' => now()
                ]);
            }

            return ShopOrderModel::where('od_id', $od_id)->delete();

        } else {
            return false;
        }
    }


    // 상품취소시 재고수량 되돌리기
    private function incrementCartSystemStock($ct_id)
    {
        $cart = DB::table('g5_shop_cart')
        ->where('ct_id', $ct_id)
        ->select('it_id', 'ct_qty_tot')
        // ->lockForUpdate()
        ->first();

        if ($cart->ct_qty_tot > 0 && $cart->it_id) {
            DB::table('g5_shop_item')
            ->where('it_id', $cart->it_id)
            ->increment('it_qty_system_stock', $cart->ct_qty_tot);
        }
    }


}