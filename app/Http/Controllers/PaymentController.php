<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\ShopCartApi;
use App\Models\ShopCart;
use Illuminate\Http\Request;
use App\Traits\CommonTrait;
use App\Services\InicisService;
use App\Services\MallShopService;
use App\Services\SmsManagementService;
use Illuminate\Validation\Rule;
use Debugbar;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Services\PaymentService;
use Exception;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use App\Traits\InicisTrait;
use Illuminate\Support\Carbon;


class PaymentController extends Controller
{
    use CommonTrait, InicisTrait;



    public function orderformview(Request $request)
    {
        $request->validate([
            'od_id' => 'required|exists:g5_shop_order,od_id'
        ], [
            'od_id.required' => '주문번호가 존재하지 않습니다.',
            'od_id.exists'   => '해당 주문번호는 존재하지 않습니다.'
        ]);

        $orderDt  = app(PaymentService::class)->resultData($request->od_id);
        $memberDt = app(MallShopService::class)->getMemberInfo(session('ss_mb_code'));

        // $payment_type = '';
        // if (isset($orderDt->level_ca_id2)) {
        //     if (strlen($orderDt->level_ca_id2) == 4) {
        //         if (substr($orderDt->level_ca_id2, -1) == '1') $payment_type = '선불';
        //         if (substr($orderDt->level_ca_id2, -1) == '2') $payment_type = '후불';
        //     }
        // }

        // // 선불거래처 주문접수 samma_102 : 당일출고, samma_103 : 익일출고
        // // 후불거래처 주문접수 samma_201 : 당일출고, samma_202 : 익일출고
        // if ($payment_type == '선불') {

        //     if (!empty($orderDt->od_delivery_date)) {
        
        //         $delivery = Carbon::parse($orderDt->od_delivery_date);
        //         $today = today();

        //         if ($orderDt->od_settle_case == '금융가상계좌' || $orderDt->od_settle_case == '무통장입금') {
        //             $code = 'samma_101'; // 미입금
        //         } else {
        //             if ($delivery->isSameDay($today)) {
        //                 $code = 'samma_102'; // 당일출고
        //             } elseif ($delivery->gt($today)) {
        //                 $code = 'samma_103'; // 익일출고
        //             }
        //         }
        //     }
        // } else {
        
        //     if (!empty($orderDt->od_delivery_date)) {
        
        //         $delivery = Carbon::parse($orderDt->od_delivery_date);
        //         $today = today();
        
        //         if ($delivery->isSameDay($today)) {
        //             $code = 'samma_201';
        //         } elseif ($delivery->gt($today)) {
        //             $code = 'samma_202';
        //         }
        
        //     }
        // }

        app(SmsManagementService::class)->kakaoAlrimTok('samma_201', $request->od_id);


        return view('payment.result', [
            'status'     => 'success',
            'resultData' => $orderDt,
            'data' => ['buyername' => $memberDt['mb_company']] 
        ]);
    }


    public function request(Request $request)
    {
        $request->merge(['uq_id' => $request->od_id]);
        
        $validator = Validator::make($request->all(), [
            'uq_id' => [
                'required',
                Rule::exists('g5_uniqid', 'uq_id')
                    ->where('uq_mb_code', session('ss_mb_code'))
                    ->where('uq_chk', ''),
            ],
            'order_type' => 'required|in:items,charge'
        ], [
            'uq_id.required' => '주문번호는 필수입니다.',
            'uq_id.exists'   => '존재하지 않는 주문번호입니다.',
            'order_type.in'  => '잘못된 주문 타입입니다.',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('/')
                ->withErrors($validator)
                ->withInput();
        }

        $isMobile   = self::isMobile($request);
        $orderInfo  = json_decode(app(ShopCartApi::class)->execute($request->merge(['mode' => 'show'])), true)['data'] ?? [];

        if (empty($orderInfo)) {
            return redirect()->back()->with('error', '주문상품이 존재하지 않습니다.');
        }

        $memberInfo = app(MallShopService::class)->getMemberInfo(session('ss_mb_code'));
        $orderNo    = $request->od_id;

        $items = DB::table('g5_shop_cart')
        ->where('od_id', $orderNo)
        ->where('ct_status', '쇼핑')
        ->select('it_name')
        ->get();

        if ($items->count() == 0) {
            return redirect()->route('/')->with('error', '주문상품이 존재하지 않습니다.');
        }

        $firstName = $items->first()->it_name ?? null;
        $otherCount = max($items->count() - 1, 0);
        if ($otherCount !== 0) {
            $goodname = $firstName.' 외 '.$otherCount.'건';
        } else {
            $goodname = $firstName;
        }

        // 주문번호로 총 결제금액 조회
        $total = DB::table('g5_shop_cart')
        ->where('od_id', $orderNo)
        ->selectRaw('SUM(ct_price * ct_qty) as total')
        ->value('total');

        if ($total <= 0) {
            return redirect()->route('/')->with('error', '주문금액이 올바르지 않습니다.');
        }

        
        $inicis = new InicisService();
        $payData = $inicis->makePaymentData([
            'od_id'    => $orderNo,
            'price'    => $total,
            'is_mobile'=> $isMobile,
            'buyer_name'  => $memberInfo['mb_company'],
            'buyer_email' => $memberInfo['mb_email'],
            'buyer_mobile' => $memberInfo['mb_hp'],
            'good_name' => $goodname,
            'order_info'  => $orderInfo,
            'popupUrl'  => !$isMobile ? route('payment.popup') : null,
            'closeUrl'  => !$isMobile ? route('payment.close') : null,
        ]);

        $payData['status']  = $request->status  ?? '';
        $payData['message'] = $request->message ?? '';

        return view('payment.request', $payData);
    }



    public function result(Request $request)
    {
        $service = app(PaymentService::class);

        $oid = $request->has('orderNumber') ? $request->orderNumber : $request->od_id;

        try {
            // 카드로 결재 진행시
            if ($request->exists('orderNumber')) {
                // 결재정보
                $dt_data = json_decode(DB::table('g5_shop_order_data')
                    ->where('od_id', $oid)
                    ->where('mb_id', session('ss_mb_id'))
                    ->value('dt_data'), true);

                if (!$dt_data) {
                    throw new Exception('결재정보가 존재하지 않습니다');
                }

                foreach ($dt_data as $key => $row) {
                    $request->merge([$key => $row]);
                }

                // 이니시스 인증 결과 코드
                $resultCode = $request->input('resultCode');
                $resultMsg  = $request->input('resultMsg');

                if ($resultCode === '0000') {
                    $reqData = $request->all();
                    $inicis_service = app(InicisService::class);

                    // 주문데이터 등록 실행
                    $excute = $service->execute($request->merge(['oid' => $oid]));

                    if ($excute['code'] == '409') {
                        throw new Exception($excute['message'], 409);
                    }

                    // 망 요청후 실결제
                    if ($request->od_settle_case == '신용카드' && $excute['status'] == 'success') {
                        $payInfo = $inicis_service->approvePayment($reqData);

                        if ($payInfo['status'] == 'error') {
                            throw new Exception($payInfo['message']);
                        } else {
                            $cardRet = $this->resultMap($payInfo['data']);
                            $cardRet['od_id'] = $oid;
                            $cardRet['price'] = $cardRet['amount'];
                            $service->setShopOrderData($cardRet);
                        }
                    } else {
                        if (!isset($reqData['authUrl'])) {
                            $inicis_service->netCancel((array) $reqData);
                        }

                        throw new Exception($excute['message']);
                    }

                    return redirect()->route('payment.view', ['od_id' => $oid]);
                    
                } else {
                    throw new Exception($resultMsg);
                }
            } else {
                // 결재정보 저장
                $res = $service->execute($request);

                if ($res['code'] == '409') {
                    throw new Exception($res['message'], 409);
                }

                if ($res['status'] == 'error') {
                    throw new Exception($res['message']);
                }

                return redirect()->route('payment.view', ['od_id' => $oid]);
            }

        } catch (\Exception $e) {
            if ($e->getCode() == 409) {
                return redirect()
                ->route('payment.noti', [
                    'od_id'   => $oid, 
                    'message' => $e->getMessage(), 
                    'status'  => 'reject',
                    'order_type' => $request->order_type
                ]);
            } else {
                return redirect()
                ->route('/')
                ->with('error', $e->getMessage());
            }
        }
    }


    public function noti_result(Request $request)
    {
        try {
            if ($request->oid) {
                $oid = $request->oid;
                $dt_data = json_decode(DB::table('g5_shop_order_data')->where('od_id', $oid)->value('dt_data'), true);
    
                if (!$dt_data) {
                    throw new Exception('결제요청정보가 존재하지 않습니다. 관리자에게 문의 바랍니다.');
                }
                
                return redirect()->route('payment.view', ['od_id' => $oid]);

            } else {
                throw new Exception('결제가 실패 하였습니다. 다시 시도해 주세요.');
            }
        } catch (\Exception $e) {
            return redirect()->route('/')->with('error', $e->getMessage());
        }
    }


    public function noti(Request $request)
    {
        Log::info('noti');
        return redirect()->route('payment.request', [
            'od_id'   => $request->od_id,
            'message' => $request->message,
            'status'  => $request->status,
            'order_type' => $request->order_type
        ]);
    }


    public function approve(Request $request)
    {
        $oid    = '';
        if ($request->P_STATUS == '00') {
            $oid   = $request->P_NOTI;
            $price = $request->P_AMT;

            $dt_data = json_decode(DB::table('g5_shop_order_data')->where('od_id', $oid)->value('dt_data'));

            if (!$dt_data) {
                return redirect()->route('/')->with('error', '결제정보가 존재하지 않습니다. 관리자에게 문의 바랍니다.');
            }

            $dt_data = (object) $dt_data;
            $dt_data->oid   = $oid;
            $dt_data->price = $price;

            foreach ($dt_data as $key => $row) {
                $request->merge([$key => $row]);
            }

            $service = app(PaymentService::class);
            
            $res = $service->execute($request);

            if ($res['code'] == '409') {
                return redirect()
                ->route('payment.noti', [
                    'od_id'   => $oid, 
                    'message' => $res['message'], 
                    'status'  => 'reject',
                    'order_type' => 'items'
                ]);
            }

            if ($res['status'] == 'error') {
                return redirect()->route('/')->with('error', $res['message']);
            } else {
                $result = app(InicisService::class)->approvePaymentMobile($request);

                $cardRet = $this->resultMapMobile((array) $result);
                $cardRet['od_id'] = $oid;
                $cardRet['price'] = $price;
                $service->setShopOrderData($cardRet);
            }
        }

        if (!$oid) {
            return redirect()->route('/')->with('error', '주문이 취소 되었습니다.');
        }

        return redirect()->route('payment.noti_result', ['oid' => $oid, 'price' => $price]);
    }



    /**
     * 팝업 내부에서 결제 완료 후 호출
     */
    public function popup(Request $request)
    {
        return view('payment.popup');
    }


    /**
     * 사용자가 팝업 닫을 때
     */
    public function close(Request $request)
    {
        return view('payment.close');
    }


    public function refreshSignature(Request $request)
    {
        $dt  = app(InicisService::class)->refreshSignature($request);

        $ret = app(PaymentService::class)->checkSystemStockAjaxDt($request->od_id);

        $dt['status']  = $ret['status'];
        $dt['message'] = $ret['message'];

        return response()->json($dt);
    }
}
