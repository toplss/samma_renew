<?php
/**
 * Class Name  : PaymentService
 * Description : 결재관련 서비스
 * Author : Lee Sangseung
 * Created Date : 2026-02-09
 * Version : 1.0
 * 
 * History :
 *   - 2026-02-09 : Initial creation
 */

namespace App\Services;

use App\Http\Controllers\Api\ShopCartApi;
use App\Models\ShopCart;
use App\Models\ShopItem;
use App\Models\ShopOrderModel;
use App\Models\TbMember;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use App\Traits\CommonTrait;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use App\Services\PointLogService;

class PaymentService
{
    use CommonTrait;


    public function execute($request)
    {
        try {
            $data = DB::transaction(function() use ($request) {
                $od_id    = $request->oid ?? $request->od_id;
                $od_receipt_point   = $request->input('input_od_temp_point', 0) + $request->input('input_od_temp_point_reserve', 0);
                $payment  = (integer) $request->input('payment', 0);
                $od_price = (integer) $payment - $request->input('deilivery_cost', 0); // 결제금액 에서 배송비 차감(상품금액 만 추출)

                $service = app(MallShopService::class);
                $member  = $service->getMemberInfo(session('ss_mb_code'));
                $delivery_date = $service->payInfomation($member)['d_od_delivery_date'];


                if (!$delivery_date) {
                    throw new \Exception('배송일이 설정되지 않았습니다. 관리자에게 문의 부탁드립니다.');
                }


                if ($od_receipt_point > $payment) {
                    throw new \Exception('충전금 또는 적립금 사용 금액은 결제 예정 금액을 초과할 수 없습니다.');
                }


                if ($request->input('input_od_temp_point', 0) < 0) {
                    throw new \Exception('충전금 사용 금액은 0원 미만일 수 없습니다.');
                }


                if ($request->input('input_od_temp_point_reserve', 0) < 0) {
                    throw new \Exception('적립금 사용 금액은 0원 미만일 수 없습니다.');
                }


                if ($request->input('payment', 0) <= 0) {
                    throw new \Exception('구매 금액이 0원 이하일 수 없습니다.');
                }


                if ($request->input('payable', 0) < 0) {
                    throw new \Exception('결제 금액이 0원 이하일 수 없습니다.');
                }


                if ($od_price != $this->validate_cart_price($od_id)) {
                    throw new \Exception('결제 금액과 상품 금액이 일치하지 않습니다. 다시 확인해 주세요.');
                }


                // 상품이 없는 경우
                $cart_exists = DB::table('g5_shop_cart')
                ->where('od_id', $od_id)
                ->whereRaw("TRIM(ct_status) = ?", ['쇼핑'])
                ->exists();

                if (!$cart_exists) {
                    throw new \Exception('상품이 존재하지 않습니다. 다시 시도해 주세요.');
                }

                if (!$request->filled('od_settle_case')) {
                    throw new \Exception('주문 데이터가 올바르지 않습니다. 다시 시도해 주세요.');
                }

                // 충전금 사용액이 존재하면 기록
                if ($request->input_od_temp_point) {
                    $res = $this->setMakePointLog($request);
                    if(!$res) {
                        throw new \Exception('충전금 등록이 실패 하였습니다. 다시 시도해 주세요');
                    }
                }

                // 적립금 사용액이 존재하면 기록
                if ($request->input_od_temp_point_reserve) {
                    $res = $this->setMakeReserveLog($request);
                    if(!$res) {
                        throw new \Exception('적립금 등록이 실패 하였습니다. 다시 시도해 주세요');
                    }
                }

                // 매출데이터 생성
                if (!$this->setMakeOrderData($request)) {
                    throw new \Exception('주문 데이터 등록이 실패 하였습니다. 관리자에게 문의 부탁드립니다.');
                }

                // 재고량 체크 + 카트상태 업데이트 + 재고량 차감
                $resDt = $this->checkSystemStock($od_id);

                if ($resDt['status'] == 'reject') {
                    throw new \Exception($resDt['message'], 409);
                }

                // 유니크 테이블 업데이트
                if (!$this->setUniqData($od_id, session('ss_mb_code'))) {
                    throw new \Exception('주문데이터 요청이 잘못된 정보입니다. 관리자에게 문의 부탁드립니다.');
                }

                // 가상계좌 업데이트
                if ($request->od_settle_case == '금융권가상계좌' && $request->input('payment_type') == '선불') {

                    $member = app(MallShopService::class)->getMemberInfo(session('ss_mb_code'));

                    if ($member) {
                        $res = $this->setVirtuaBank(
                            $member['mb_virtual_account'], 
                            $member['mb_code'], 
                            $request->payable
                        );
                        if (!$res) {
                            throw new \Exception('가상계좌로 금액 등록이 실패하였습니다. 관리자에게 문의 부탁드립니다.');
                        }
                    } else {
                        throw new \Exception('회원 데이터가 존재하지 않습니다. 관리자에게 문의 부탁드립니다.');
                    }                    
                }

                // 충전금 + 적립금 + 채권액 처리
                $this->setOrderMemberPoints($request, app(MallShopService::class)->getMemberInfo(session('ss_mb_code')));

                return $this->resultData($od_id);
            });


            return [
                'status'  => 'success',
                'resultData' => $data,
                'message' => '주문완료',
                'code'    => '00'
            ];

        } catch (QueryException $e) {
            Log::error('Database error during payment processing: ' . $e->getMessage(), ['exception' => $e]);
            return [
                'status'  => 'error',
                'resultData' => [],
                'message' => '데이터베이스 오류가 발생했습니다. 잠시 후 다시 시도해 주세요.',
                'code'    => $e->getCode()
            ];

        } catch (\Exception $e) {
            return [
                'status'  => 'error',
                'resultData' => [],
                'message' => $e->getMessage(),
                'code'    => $e->getCode()
            ];
        }
    }



    // 매출데이터 생성
    private function setMakeOrderData($request)
    {
        return $this->setOrderDataForm(
            $request, 
            app(MallShopService::class)->getMemberInfo(session('ss_mb_code'))
        );
    }


    // 전산재고량 체크 + 전산재고 차감 + 카트 상태변환
    public function checkSystemStock($oid)
    {
        $od_group_code = DB::table('g5_shop_order')->where('od_id', $oid)->value('od_group_code');

        $cartItems = DB::table('g5_shop_cart')
        ->leftJoin('g5_shop_item', 'g5_shop_cart.it_id', '=', 'g5_shop_item.it_id')
        ->where('ct_status', '쇼핑')
        ->where('od_id', $oid)
        ->select(
            'ct_id', 
            'g5_shop_cart.it_id', 
            'g5_shop_cart.ct_qty', 
            'g5_shop_cart.ct_qty_tot', 
            'g5_shop_item.it_qty_system_stock',
            'g5_shop_item.it_name',
            'g5_shop_item.it_gubun',
            'g5_shop_item.it_qty_box', 
            'g5_shop_item.it_qty_pack', 
            'g5_shop_item.it_qty_pcs',
        )
        ->get()
        ->map(function($row) {
            return (array) $row;
        });

        $gubunArr = ['box' => '박스', 'pack' => '팩', 'pcs' => '낱개'];

        foreach ($cartItems as $key => $row) {
            if ($row['it_qty_system_stock'] <= 0) {
                $message = '['.$row['it_name'].'] 상품은 현재 품절입니다.';

            } else if ($row['ct_qty_tot'] > $row['it_qty_system_stock']) {
                $buyGroup = $this->ProductUnit($row['it_id'], $row['it_qty_'.$row['it_gubun']] * $row['ct_qty']);
                $stockGroup = $this->ProductUnit($row['it_id'], $row['it_qty_system_stock']);

                $message  = '<b>['.$row['it_name'].']</b>';
                $message .= '<br>요청하신 수량은 '.$buyGroup[$row['it_gubun']].$gubunArr[$row['it_gubun']].' 총 (<span style="color:red;">'.number_format($row['ct_qty_tot']) .'</span>개)입니다. ';
                $message .= '<br>현재 구매 가능한 재고는 낱개 기준<span style="color:red;">'.number_format($row['it_qty_system_stock']) .'</span>개 입니다. ';
                $message .= '<br><br>구매 가능 수량으로 조정하신 후 다시 주문해 주시기 바랍니다.';

                $ret = [
                    'status'  => 'reject',
                    'message' => $message
                ];
                return $ret;

            } else {
                ShopItem::where('it_id', $row['it_id'])->decrement('it_qty_system_stock', $row['ct_qty_tot']);
            }
        }

        ShopCart::where('od_id', $oid)->update(['ct_status' => '주문', 'od_group_code' => $od_group_code]);

        return [
            'status'  => 'success',
            'message' => ''
        ];
    }


    private function setMakePointLog($request)
    {
        $od_id   = $request->oid ?? $request->od_id;

        return app(PointLogService::class)->CreatePointLog(session('ss_mb_code'), $od_id, 'CHARGE', 'decrease', $request->input_od_temp_point);
    }


    private function setMakeReserveLog($request)
    {
        $od_id   = $request->oid ?? $request->od_id;

        return app(PointLogService::class)->CreatePointLog(session('ss_mb_code'), $od_id, 'RESERVE', 'decrease', $request->input_od_temp_point_reserve);
    }


    private function setOrderDataForm($request, $member)
    {
        $od_id = $request->oid ?? $request->od_id;
        
        if (!$od_id) {
            return false;
        }

        $deliveryInfo      = app(MallShopService::class)->payInfomation($member);
        $delivery_date     = $deliveryInfo['d_od_delivery_date'];

        $od_date            = $delivery_date ? date('Ymd', strtotime($delivery_date)) : date('Ymd');
        $od_date_time       = date('Y-m-d H:i:s');
        
        $od_group_code      = $request->order_type == 'items' 
            ? $od_date.'_'.$member['mb_code']
            : $this->make_group_code();

        $od_delivery_step   = $request->payment_type == '후불' || $request->od_settle_case == '신용카드' 
            ? '10' 
            : '0';

        $od_settle_case     = $request->payment_type == '후불' 
            ? '금융권가상계좌' 
            : $request->od_settle_case;

        $od_gubun           = $request->order_type == 'charge' 
            ? '충전금구매' 
            : '매출';
        
        $od_gubun_type      = 'product';
        $od_status          = $request->od_settle_case == '신용카드' || $request->payment_type == '후불'
            ? '입금' 
            : '주문';

        $od_receipt_price   = 0;
        
        $isMobile           = self::isMobile($request);
        $od_receipt_point   = $request->input('input_od_temp_point', 0) + $request->input('input_od_temp_point_reserve', 0);

        // 배송일 구하기
        $yoil = $deliveryInfo['ship_date'];
        
        $od_delivery_date = $delivery_date;

        // 배송사원 정보 조회
        $deliverMember = DB::table('tb_employee')->where('mb_code', $member['delivery_mb_code'])
        ->select('mb_work_delivery_car', 'mb_work_staff_name')
        ->first();

        // 호차 글자제거 후 숫자만 추출
        $car_code = '';
        if ($deliverMember) {
            $car_code = preg_replace('/\D/', '', $deliverMember->mb_work_delivery_car) ?? ''; 
        }

        $sub_total = (integer) $request->input('payment', 0);
        $od_misu   = (integer) $request->input('payable', 0);

        // 결제금액이 0원 이면 입금처리
        if ($od_misu == 0) {
            $od_status        = '입금'; 
            $od_delivery_step = '10';
        }

        // 직원인 경우 입금처리
        if (isset($member['mb_level_type'])) {
            if (trim($member['mb_level_type']) == '2' && trim($member['mb_gubun_type']) == 'employee') {
                $member['level_ca_id'] = '10';
                $od_status        = '입금'; 
                $od_delivery_step = '90';
                $od_group_code = $this->make_group_code();
            }
        } 

        // 회원 레벨이 없는 경우
        if (!$member['level_ca_id2']) {
            $member['level_ca_id2'] = '1002'; // 본사후불
        }


        if ($request->payment_type == '선불' && $request->payable > 0 && ($request->od_settle_case == '충전금' || $request->od_settle_case == '적립금' || $request->od_settle_case == '충전금+적립금') ) {
            return false;
        }

        $bank_account = '';
        switch($request->od_settle_case) {
            case '금융권가상계좌' : 
                $bank_account = $member['mb_virtual_bank'].' '.$member['mb_virtual_account']. ' ['. $member['mb_company'].']';
                break;
            case '무통장입금' : 
                $bank_account = $this->getSiteInfo()['account'];
                break;
        }

        $pt_sale = $pt_sales_delivery = 0;

        switch($od_gubun) {
            case '충전금구매' : 
                $sub_total = 0;
                break;
            case '매출' : 
                $pt_sale = (integer) ($request->payment - $request->deilivery_cost);
                $pt_sales_delivery = (integer) $request->payment;
                break;
        }


        // 배송정보 조회
        $deliveryInfo = DB::table('tb_member_delivery')
        ->where('mem_mb_code', session('ss_mb_code'))
        ->select('delivery_order1', 'delivery_order2', 'delivery_order3')
        ->first();

        
        $add_data = [
            'od_id' => $od_id,
            'od_id_org' => $od_id,
            'od_date'   => date('Ymd'),
            'od_group_code' => $od_group_code,
            'od_group_code_org' => $od_group_code,
            'mb_code'   => $member['mb_code'],
            'mb_id'     => $member['mb_id'],
            'mb_branch_code' => $member['mb_branch_code'],
            'mb_branch_gubun_type' => $member['mb_branch_gubun_type'],
            'level_ca_id'  => $member['level_ca_id'],
            'level_ca_id2' => $member['level_ca_id2'],
            'level_ca_id2_name' => $request->payment_type,
            'level_ca_id3' => $member['level_ca_id3'],
            'level_ca_id4' => $member['level_ca_id4'],
            'level_ca_id5' => $member['level_ca_id5'],
            'chain_ca_id'  => $member['chain_ca_id'],
            'chain_ca_id2' => $member['chain_ca_id2'],
            'chain_ca_id3' => $member['chain_ca_id3'],
            'chain_ca_id4' => $member['chain_ca_id4'],
            'chain_ca_id5' => $member['chain_ca_id5'],
            'mb_level' => $member['mb_level'],
            'mb_sendcost' => $member['mb_sendcost'],
            'mb_order_amount_select' => '',
            'mb_order_amount' => 0,
            'mb_sendcost_amount_select' => '',
            'mb_sendcost_amount' => 0,
            'mb_cs_delivery' => '',
            'mb_cs_mon' => $member['mb_cs_mon'],
            'mb_cs_tue' => $member['mb_cs_tue'],
            'mb_cs_wed' => $member['mb_cs_wed'],
            'mb_cs_thu' => $member['mb_cs_thu'],
            'mb_cs_fri' => $member['mb_cs_fri'],
            'mb_cs_sat' => $member['mb_cs_sat'],
            'mb_cs_sun' => $member['mb_cs_sun'],
            'od_gubun'  => $od_gubun,
            'od_gubun_type'     => $od_gubun_type,
            'od_delivery_step'  => $od_delivery_step,
            'od_delivery_step_chk'  => '',
            'od_delivery_day'       => $yoil,
            'od_delivery_date'      => $od_delivery_date,
            'mb_od_delivery_day'    => $yoil,
            'mb_od_delivery_date'   => $od_delivery_date,
            'delivery_car_code' => $car_code,
            'delivery_mb_code' => $member['mb_delivery_mb_code'],
            'delivery_mb_name' => $member['delivery_mb_name'],
            'mb_delivery_car_code' => $car_code,
            'mb_delivery_mb_code' => $member['delivery_mb_code'],
            'mb_delivery_mb_name' => $member['delivery_mb_name'],
            'delivery_order1' => $deliveryInfo->delivery_order1 ?? '',
            'delivery_order2' => $deliveryInfo->delivery_order2 ?? '',
            'delivery_order3' => $deliveryInfo->delivery_order3 ?? '',
            'mb_sales_staff_code' => $member['mb_sales_staff_code'],
            'mb_sales_staff_name' => $member['mb_sales_staff'],
            'mb_manager_staff_code' => $member['mb_manager_staff_code'],
            'mb_manager_staff_name' => $member['mb_manager_staff'],
            'od_pwd' => '',
            'od_company' => $member['mb_company'],
            'od_name' => $member['mb_name'],
            'od_email' => $member['mb_email'],
            'od_tel' => $member['mb_tel'],
            'od_hp' => $member['mb_hp'],
            'od_zip1' => $member['mb_zip1'],
            'od_zip2' => $member['mb_zip2'],
            'od_addr1' => $member['mb_addr1'],
            'od_addr2' => $member['mb_addr2'],
            'od_addr3' => $member['mb_addr3'],
            'od_addr_jibeon' => $member['mb_addr_jibeon'],
            'od_b_company'  => $member['mb_company'],
            'od_b_name'     => $member['mb_name'],
            'od_b_tel'      => $member['mb_tel'],
            'od_b_hp'       => $member['mb_hp'],
            'od_b_zip1'     => $member['mb_zip1'],
            'od_b_zip2'     => $member['mb_zip2'],
            'od_b_addr1'    => $member['mb_addr1'],
            'od_b_addr2'    => $member['mb_addr2'],
            'od_b_addr3'    => $member['mb_addr3'],
            'od_b_addr_jibeon' => $member['mb_addr_jibeon'],
            'od_b_addr_sido'   => $member['mb_addr_sido'],
            'od_b_addr_gugun' => $member['mb_addr_gugun'],
            'od_deposit_name' => $member['mb_company'],
            'od_memo'       => $request->od_memo ?? '',
            'od_status'     => $od_status,
            'is_admin_data' => 'n',
            
            // 사용안하는 필드
            'od_cart_count' => 0,
            'od_cart_price' => $od_gubun == '충전금구매' ? $od_misu : (integer) $pt_sale,
            'od_cart_price_change' => (integer) $pt_sale,
            'od_cart_coupon' => 0,
            'od_send_cost'  => $request->deilivery_cost,
            'od_send_cost2' => 0,
            'od_send_coupon'=> 0,
            'od_coupon'     => 0,
            'od_receipt_price' => (integer) $od_receipt_price,
            'od_credit_receipt_price' => (integer) $od_receipt_price,
            'od_temp_point' => $request->input('input_od_temp_point', 0),
            'od_temp_point_reserve' => $request->input('input_od_temp_point_reserve', 0),
            'od_receipt_point' => $od_receipt_point,
            'od_bank_account' => $bank_account,
            'bank_account' => $bank_account,
            'od_receipt_time' => $od_date_time,
            'od_misu' => (integer) $od_misu,
            'od_pg' => '',
            'od_tno' => '',
            'od_app_no' => '',
            'od_escrow' => '',
            'od_tax_flag' => '',
            'od_tax_mny' => '',
            'od_vat_mny' => '',
            'od_free_mny' => '',
            'od_shop_memo' => '',
            'od_hope_date' => '',
            'od_tax_type' => '',
            'od_tax_use' => '',
            'od_protect_service' => '',
            'od_cash_hp' => '',
            'od_cash_company_no' => '',
            'od_tax_company_no' => '',
            'od_tax_company' => '',
            'od_tax_name' => '',
            'od_tax_zip' => '',
            'od_tax_addr1' => '',
            'od_tax_addr2' => '',
            'od_tax_addr3' => '',
            'od_tax_addr_jibeon' => '',
            'od_tax_job' => '',
            'od_tax_product' => '',
            'od_credit_case' => '',
            'od_is_charge' => 'n',
            // 사용안하는 필드


            'od_time'   => $od_date_time,
            'od_mobile' => $isMobile,
            'od_ip'     => $request->ip(),
            'od_settle_case' => $od_settle_case,
            'pt_prev_charge'  => $member['mb_point'],
            'pt_prev_reserve' => $member['mb_point_reserve'],
            'pt_prev_balance' => $member['mb_point_balance'],
            'pt_buy_charge'   => $od_gubun == '충전금구매' ? $od_misu : 0,
            'pt_sales'    => $pt_sale,
            'pt_delivery' => (integer) $request->deilivery_cost,
            'pt_sales_delivery' => $pt_sales_delivery,
            'pt_subtotal'    => $sub_total,
            'pt_charge'     => $request->input('input_od_temp_point', 0),
            'pt_reserve'     => $request->input('input_od_temp_point_reserve', 0),
            'pt_cur_charge'  => $member['mb_point'] - $request->input('input_od_temp_point', 0),
            'pt_cur_reserve' => $member['mb_point_reserve'] - $request->input('input_od_temp_point_reserve', 0),
            'pt_cur_balance' => $member['mb_point_balance'] + ($request->payment_type === '후불' ? $request->payment : 0),
            'od_test' => 0,
        ];


        if ($od_group_code) {
            $this->make_lock_date(['od_group_code' => $od_group_code, 'mb_code' => $member['mb_code']]);
        }


        return ShopOrderModel::create($add_data);
    }



    /**
     * 회원 충전금 + 적립금 + 채권액 업데이트
     *
     * @param [type] $request, $member
     * @return void
     */
    private function setOrderMemberPoints($request, $member)
    {
        $usePoint        = (int) $request->input('input_od_temp_point', 0);
        $useReservePoint = (int) $request->input('input_od_temp_point_reserve', 0);
        $payable         = (int) $request->input('payable', 0);

        $updateData = [];

        if ($usePoint > 0) {
            $updateData['mb_point'] = DB::raw("mb_point - {$usePoint}");
        }

        if ($useReservePoint > 0) {
            $updateData['mb_point_reserve'] = DB::raw("mb_point_reserve - {$useReservePoint}");
        }

        // 직원인 경우 입금처리
        if (isset($member['mb_level_type'])) {
            if (trim($member['mb_level_type']) == '2' && trim($member['mb_gubun_type']) == 'employee') {
                $updateData['mb_point_balance'] = DB::raw("mb_point_balance + {$payable}");
            }
        } 

        // if ($request->input('payment_type') === '후불') {
        //     $updateData['mb_point_balance'] = DB::raw("mb_point_balance + {$payable}");
        // }

        if (!empty($updateData)) {
            DB::table('tb_member')
                ->where('mb_code', session('ss_mb_code'))
                // ->lockForUpdate()
                ->update($updateData);
        }
    }


    /**
     * 가상계좌 결재금액 등록
     *
     * @param [type] $mb_virtual_account
     * @param [type] $mb_code
     * @param [type] $pay_amount
     * @return boolean
     */
    public function setVirtuaBank($mb_virtual_account, $mb_code, $pay_amount)
    {
        // 1 : 자유, 2 : 정액
        return DB::table('TB_RVAS_MAST')
        ->where('VACCT_NO', $mb_virtual_account)
        ->where('CUST_CD', $mb_code)
        ->update([
            'PAY_AMT' => DB::raw("
                CASE 
                    WHEN IN_GB = '1' THEN 0
                    WHEN IN_GB = '2' THEN {$pay_amount}
                END
            "),
            'modifydate' => now()
        ]);
    }



    /**
     * 유니크 테이블 업데이트
     *
     * @param [type] $oid
     * @param [type] $mb_code
     * @return boolean
     */
    public function setUniqData($oid, $mb_code)
    {
        $exists = DB::table('g5_uniqid')
        ->where('uq_id', $oid)
        ->where('uq_mb_code', $mb_code)
        ->where('uq_chk', '')->exists();

        if (!$exists) {
            return false;
        } else {
            return DB::table('g5_uniqid')
            ->where('uq_id', $oid)
            ->where('uq_mb_code', $mb_code)
            ->where('uq_chk', '')
            ->update(['uq_chk' => '1']);
        }
    }



    public function setOrderCancelMemberPoints($orderDt)
    {
        $usePoint        = (int) $orderDt['od_temp_point'];
        $useReservePoint = (int) $orderDt['od_temp_point_reserve'];
        $payable         = (int) $orderDt['pt_subtotal'];

        $updateData = [];

        // 충전금 리턴
        if ($usePoint > 0) {
            $updateData['mb_point'] = DB::raw("mb_point + {$usePoint}");

            app(PointLogService::class)->CreatePointLog(
                session('ss_mb_code'), 
                $orderDt['od_id'], 
                'CHARGE', 
                'increase', 
                $usePoint
            );
        }

        // 적립금 리턴
        if ($useReservePoint > 0) {
            $updateData['mb_point_reserve'] = DB::raw("mb_point_reserve + {$useReservePoint}");

            app(PointLogService::class)->CreatePointLog(
                session('ss_mb_code'), 
                $orderDt['od_id'], 
                'RESERVE', 
                'increase', 
                $useReservePoint
            );
        }

        // 채권액 리턴
        // if ($orderDt['payment_type'] === '후불') {
        //     $updateData['mb_point_balance'] = DB::raw("mb_point_balance - {$payable}");
        // }

        if (!empty($updateData)) {
            DB::table('tb_member')
                ->where('mb_code', session('ss_mb_code'))
                // ->lockForUpdate()
                ->update($updateData);
        }
    }


    public function resultData($oid)
    {
        return ShopOrderModel::where('od_id', $oid)
        ->select(
            'od_id',
            'od_delivery_date',
            'od_date',
            'od_time',
            'mb_code',
            'od_company',
            'od_name',
            'od_tel',
            'od_hp',
            'od_email',
            'od_delivery_day',
            'level_ca_id2',

            'od_misu',
            'od_cart_price',
            'pt_sales',
            'pt_delivery',
            'pt_sales_delivery',
            'pt_subtotal',
            'od_settle_case',
            'od_receipt_point',
            'od_receipt_price',
            'od_temp_point',
            'od_temp_point_reserve',
            'od_addr1',
            'od_addr2',
            'od_addr3',
            'od_email',
            'od_b_tel',
            'od_b_hp',
            'od_bank_account',
            'bank_account',
            'od_memo',

            'level_ca_id2',
        )
        ->first();
    }



    /**
     * 카드 결제시 업데이트정보
     *
     * @param [type] $data
     * @return boolean
     */
    public function setShopOrderData($data)
    {
        return DB::transaction(function() use($data) {
            $order = ShopOrderModel::where('od_id', $data['od_id'])
            ->where('mb_code', session('ss_mb_code'))
            // ->lockForUpdate()
            ->firstOrFail();

            $order->increment('od_receipt_price', $data['price']);
            $order->increment('pt_card', $data['price']);
            // $order->decrement('pt_subtotal', $data['price']);

            $order->update([
                'od_misu' => 0,
                'od_pg'   => 'inicis',
                'od_tno'  => $data['tno'],
                'od_app_no'  => $data['app_no'],
                'od_bank_account'  => $data['card_name'],
                'od_delivery_step' => '10',
                'od_status'  => '입금'
            ]);
        });
    }



    public function checkSystemStockAjaxDt($oid)
    {
        $cartItems = DB::table('g5_shop_cart')
        ->leftJoin('g5_shop_item', 'g5_shop_cart.it_id', '=', 'g5_shop_item.it_id')
        ->where('ct_status', '쇼핑')
        ->where('od_id', $oid)
        ->select(
            'ct_id', 
            'g5_shop_cart.it_id', 
            'g5_shop_cart.ct_qty', 
            'g5_shop_cart.ct_qty_tot', 
            'g5_shop_item.it_qty_system_stock',
            'g5_shop_item.it_name',
            'g5_shop_item.it_gubun',
            'g5_shop_item.it_qty_box', 
            'g5_shop_item.it_qty_pack', 
            'g5_shop_item.it_qty_pcs',
            'g5_shop_item.it_soldout',
            'g5_shop_item.it_force_soldout',
        )
        ->get()
        ->map(function($row) {
            return (array) $row;
        });

        $gubunArr = ['box' => '박스', 'pack' => '팩', 'pcs' => '낱개'];

        $isIssue = false;
        $message = '';

        foreach ($cartItems as $key => $row) {
            if ($row['it_qty_system_stock'] <= 0 || $row['it_soldout'] == '1' || $row['it_force_soldout'] == '10') {

                $isIssue = true;
                $title = '품절상품이 포함되어 있습니다.';
                $message .= '<span class="txt-red">'.$row['it_name'].'</span> <br>';
            } 
            
            if ($row['ct_qty_tot'] > $row['it_qty_system_stock']) {
                
                $isIssue = true;
                $title = '알림';

                $buyGroup = $this->ProductUnit($row['it_id'], $row['it_qty_'.$row['it_gubun']] * $row['ct_qty']);
                $stockGroup = $this->ProductUnit($row['it_id'], $row['it_qty_system_stock']);

                $message  = '<b>['.$row['it_name'].']</b>';
                $message .= '<br>요청하신 수량은 '.$buyGroup[$row['it_gubun']].$gubunArr[$row['it_gubun']].' 총 (<span style="color:red;">'.number_format($row['ct_qty_tot']) .'</span>개)입니다. ';
                $message .= '<br>재고 수량은 '.$stockGroup[$row['it_gubun']].$gubunArr[$row['it_gubun']].' 총 (<span style="color:red;">'.number_format($row['it_qty_system_stock']) .'</span>개)입니다. ';
                $message .= '<br><br>구매 가능 수량으로 조정하신 후 다시 주문해 주시기 바랍니다.';

            }
            
        }

        if ($isIssue === true) {
            $ret = [
                'iconType' => 'warning',
                'title' => $title,
                'status'  => 'reject',
                'message' => $message
            ];
            return $ret;            
        }

        return [
            'status'  => 'success',
            'message' => ''
        ];
    }

    

    /**
     * 매출자료 입력 정합성시 잠금데이터 생성
     *
     * @param [type] $data
     * @return void
     */
    private function make_lock_date($data)
    {
        DB::table('g5_shop_order_charge_lock')->insert([
            'od_group_code' => $data['od_group_code'],
            'mb_code'       => $data['mb_code'],
            'pt_charge'     => 0,
            'pt_reserve'    => 0,
            'pt_balance'    => 0,
            'created_at'    => now(),
        ]);
    }



    // 결제금액 = 카트상품 금액 비교
    private function validate_cart_price($oid)
    {
        return DB::table('g5_shop_cart')
        ->where('ct_status', '쇼핑')
        ->where('od_id', $oid)
        ->selectRaw('SUM(ct_price * ct_qty) as total_price')
        ->value('total_price');
    }
}
