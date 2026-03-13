<?php
namespace App\Services;


use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class InicisService
{

    public function approvePayment(array $requestData)
    {
        require_once app_path('Libraries/Inicis/libs/INIStdPayUtil.php');

        $mid     = config('inicis.mid');
        $signKey = config('inicis.signkey');

        $util = new \INIStdPayUtil();
        $timestamp = $util->getTimestamp();

        // 승인 signature 생성
        $signature = $util->makeSignature([
            'authToken' => $requestData['authToken'],
            'timestamp' => $timestamp,
        ]);

        $params = [
            'mid'       => $mid,
            'authToken' => $requestData['authToken'],
            'signature' => $signature,
            'timestamp' => $timestamp,
            'charset'   => 'UTF-8',
            'format'    => 'JSON',
        ];

        $client = new Client([
            'timeout' => 10,
            'verify'  => false, // 이니시스 테스트환경 대응
        ]);

        try {
            /**
             * ★★★ 이 줄에서 실제 카드 승인 발생 ★★★
             */
            $response = $client->post($requestData['authUrl'], [
                'form_params' => $params,
            ]);

            $result = json_decode($response->getBody()->getContents(), true);

            // 승인 실패 → 즉시 망취소
            if (!isset($result['resultCode']) || $result['resultCode'] !== '0000') {
                $this->netCancel($requestData);
                throw new \Exception($result['resultMsg'] ?? '결제 승인 실패');
            }

            return [
                'status' => 'success',
                'tid'    => $result['tid'],      // ← 드디어 여기서 TID 생김
                'data'   => $result,
            ];

        } catch (\Exception $e) {
            // 통신 에러도 망취소
            $this->netCancel($requestData);
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }


    public function approvePaymentMobile($request)
    {
        $client = new Client();
        $params = [
            'P_MID' => config('inicis.mid'),
            'P_TID'     => $request->P_TID,
            'P_OID'     => $request->P_OID,
            'P_AMT' => $request->P_AMT,
        ];

        $response = $client->request('POST', $request->P_REQ_URL, [
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded; charset=EUC-KR'
            ],
            'body' => mb_convert_encoding(
                http_build_query($params, '', '&'),
                'EUC-KR',
                'UTF-8'
            )
        ]);

        $body = $response->getBody()->getContents();

        // 응답 EUC-KR → UTF-8 변환
        $body = mb_convert_encoding($body, 'UTF-8', 'EUC-KR');
    
        parse_str($body, $result);

        return (object) $result;
    }


    public function makePaymentData($params)
    {
        require_once app_path('Libraries/Inicis/libs/INIStdPayUtil.php');

        $oid = $params['od_id'];
        $mid = config('inicis.mid');
        $signKey = config('inicis.signkey');

        
        $cardNoInterestQuota = '';  // 카드 무이자 여부 설정(가맹점에서 직접 설정)
        $cardQuotaBase = '2:3:4:5:6:7:8:9:10:11:12';  


        $mKey = hash("sha256", $signKey);


        $util = new \INIStdPayUtil();
        $timestamp = $util->getTimestamp();
        $signature = $util->makeSignature([
            'oid'       => $params['od_id'],
            'price'     => $params['price'],
            'timestamp' => $timestamp,
        ]);

        $hashData = '';
        if ($params['is_mobile']) {
            $amount = $params['price'];
            $plain = $amount . $oid . $timestamp . $signKey;
            $hashData = base64_encode(
                hash('sha512', $plain, true)
            );        
        }

        $data = [
            'is_mobile'  => $params['is_mobile'],
            'payUrl'     => $params['is_mobile']
                ? config('inicis.mobile_url')
                : config('inicis.pc_url'),

            'mid'        => $mid,
            'mKey'       => $mKey,
            'oid'        => $params['od_id'],
            'price'      => $params['price'],
            'timestamp'  => $timestamp,
            'signature'  => $signature,
            'buyername'  => $params['buyer_name'],
            'buyeremail' => $params['buyer_email'],
            'buyermobile' => $params['buyer_mobile'],
            'goodname'   => $params['good_name'],
            
            'returnUrl'  => $params['is_mobile'] 
                ? route('payment.noti_result') 
                : route('payment.result'),

            'cardNoInterestQuota' => $cardNoInterestQuota,
            'cardQuotaBase' => $cardQuotaBase,
            'orderInfo'  => $params['order_info'],
            'hashData'   => $hashData,
        ];


        if ($params['is_mobile']) {
            $data['popupUrl']    = $params['popupUrl'];
            $data['closeUrl']    = $params['closeUrl'];
            $data['payViewType'] = 'popup';
        } else {
            $data['payViewType'] = 'overlay';
        }

        return $data;
    }




    /**
     * 망취소 (결재요청은 했지만 아직 승인은 안난경우)
     *
     * @param array $payResult
     * @return array
     */
    public function netCancel(array $payResult)
    {
        require_once app_path('Libraries/Inicis/libs/INIStdPayUtil.php');

        $mid     = config('inicis.mid');
        $signKey = config('inicis.signkey');

        $util = new \INIStdPayUtil();
        $timestamp = $util->getTimestamp();

        $signature = $util->makeSignature([
            'authToken' => $payResult['authToken'],
            'timestamp' => $timestamp,
        ]);

        $params = [
            'mid'       => $mid,
            'authToken' => $payResult['authToken'],
            'timestamp' => $timestamp,
            'signature' => $signature,
        ];

        $client = new Client([
            'timeout' => 10,
            'verify'  => true,
        ]);

        $response = $client->post($payResult['netCancelUrl'], [
            'form_params' => $params,
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }



    public function cancelPayment($tid, $amt, $mid, $oid)
    {
        $client = new Client();

        $timestamp = date('YmdHis');
        $data = [
            'tid' => $tid,
            'msg' => '전체 환불 사유' // 반드시 금액 제거
        ];

        $type = 'refund';
        $dataJson = json_encode($data, JSON_UNESCAPED_UNICODE); // hash 생성용

        $plain = config('inicis.apikey') . $mid . $type . $timestamp . $dataJson;
        $hashData = hash('sha512', $plain);

        $params = [
            'mid' => $mid,
            'type' => $type,
            'timestamp' => $timestamp,
            'clientIp' => request()->ip(),
            'hashData' => $hashData,
            'data' => $data // 배열 그대로! 문자열 X
        ];
        
        $response = $client->post('https://iniapi.inicis.com/v2/pg/refund', [
            'headers' => ['Content-Type' => 'application/json'],
            'body' => json_encode($params, JSON_UNESCAPED_UNICODE)
        ]);

        $body = $response->getBody()->getContents();

        // JSON 디코딩
        $result = json_decode($body, true); // 배열로 받기
        // 또는 객체로 받고 싶으면
        // $result = json_decode($body);

        Log::info('Refund Response', $result);

        // 접근 예시
        if ($result['resultCode'] === '00') {
            return true;
        } else {
            return false;
        }
    }


    public function refreshSignature($request)
    {
        require_once app_path('Libraries/Inicis/libs/INIStdPayUtil.php');
        
        $timestamp = $request->timestamp;
        $signKey = config('inicis.signkey');

        $oid = $request->od_id;
        $payable = trim($request->price);


        $util = new \INIStdPayUtil();
        $signature = $util->makeSignature([
            'oid'       => $oid,
            'price'     => $payable,
            'timestamp' => $timestamp,
        ]);

        $plain = $payable . $oid . $timestamp . $signKey;
        $hashData = base64_encode(
            hash('sha512', $plain, true)
        );    

        return [
            'signature' => $signature,
            'hashData'  => $hashData
        ];
    }
}