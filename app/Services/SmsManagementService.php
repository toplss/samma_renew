<?php
namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Services\AlrimTokService;
use App\Traits\CommonTrait;
use App\Models\KakaoAlrimTokLogModel;

class SmsManagementService
{
    use CommonTrait;

    private $setup; // 관리자 페이지 > 기본설정 > sms설정 환경설정 정보
    private $params = [];

    public function __construct()
    {
        $this->setup = $this->getSmsSetupInfo();
    }


    public function kakaoAlrimTok($template_code, $od_id = null, $phone = null, $name = null, $as_idx = null)
    {
        $service = app(AlrimTokService::class);

        if ($template_code == 'samma_1') {  //회원가입 - 승인 전

            $params = [
                'phn' => $phone,
                'name' => $name,
            ];

        } elseif ($template_code == 'samma_3') {  // A.S접수

            if (!$as_idx) {
                throw new \Exception('a/s idx정보가 누락되었습니다.');
            }

            $as_info = DB::table('tb_as_reception as ar')
                ->leftJoin('tb_member as tm', 'ar.mb_num', '=', 'tm.mb_num')
                ->where('ar.idx', 13)
                ->select([
                    'ar.mb_num',
                    'ar.company',
                    'ar.phone',
                    'ar.title',
                    'ar.contents',
                    DB::raw('(select mb_hp from tb_member where mb_num = tm.mb_manager_staff_code) as manager_hp'),
                    DB::raw('(select mb_business_hp from tb_employee where mb_code = tm.mb_manager_staff_code) as manager_business_hp')
                ])
                ->first();


            //증상 문자열 가공
            $as_contents = $as_info->contents;
            $as_contents_length = mb_strlen($as_contents);

            if ($as_contents_length > 30) {
                $as_contents = mb_substr($as_contents, 0, 30, "UTF-8") . '...';
            }
            $as_contents = $as_contents . '\n';

            //A.S 신청자 수신번호
            $as_hp = $as_info->phone;

            //관리사원 수신번호 세팅 (업무폰 우선, 없으면 개인폰)
            if ($as_info->manager_business_hp) {
                $manager_hp = $as_info->manager_business_hp;
            } else {
                $manager_hp = $as_info->manager_hp;
            }

            //A.S 알림톡의 경우 관리사원도 수신자에 포함한다.
            $receiver = [$as_hp, $manager_hp];
            foreach ($receiver as $key => $hp) {

// if( $key < 1) {
//     $as_contents = '고객 수신내용';
// } else {
//     $as_contents = '관리사원 수신내용';
// }

                $params = [
                    'phn' => $hp,
                    'company' => $as_info->company,
                    '장비명' => $as_info->title,
                    '증상' => $as_contents,
                ];

                $service->params = $params;
                $service->sendSms($template_code, $this->setup);
            }

            return;

        } elseif ($template_code == 'samma_101') {

        
            if (!$od_id) {
                throw new \Exception('주문번호 정보가 누락되었습니다.');
            }

            $order_info = DB::table('g5_shop_order')
                ->select(
                    'od_company',
                    'od_hp',
                    'od_cart_price',
                    'od_date',
                    'od_receipt_time',
                )
                ->where('od_id', $od_id)
                ->first();        

            $params = [
                'phn' => $order_info->od_hp,
                'company' => $order_info->od_company,
                '주문날짜' => date('Y년 m월 d일',strtotime($order_info->od_date)),
                '주문번호' => $od_id,
                '결제금액' => number_format($order_info->od_cart_price).'원',
                '마감시간' => $order_info->od_receipt_time,
                '상호' => $order_info->od_receipt_time,
                '보관기간' => $order_info->od_receipt_time,
            ];        


        }elseif ($template_code == 'samma_102' || $template_code == 'samma_103') {  // 선불거래처 주문접수 samma_102 : 당일출고, samma_103 : 익일출고

            if (!$od_id) {
                throw new \Exception('주문번호 정보가 누락되었습니다.');
            }

            $order_info = DB::table('g5_shop_order')
                ->select(
                    'od_company',
                    'od_hp',
                    'od_cart_price',
                    'od_date',
                    'od_receipt_time',
                )
                ->where('od_id', $od_id)
                ->first();        

            $params = [
                'phn' => $order_info->od_hp,
                'company' => $order_info->od_company,
                '주문날짜' => date('Y년 m월 d일',strtotime($order_info->od_date)),
                '주문번호' => $od_id,
                '결제금액' => number_format($order_info->od_cart_price).'원',
                '입금시간' => $order_info->od_receipt_time,
            ];

            
        } elseif ($template_code == 'samma_105' || $template_code == 'samma_106') {  // 선불거래처 주문취소 samma_105 : 입금 전, samma_106 : 입금 완료

            if (!$od_id) {
                throw new \Exception('주문번호 정보가 누락되었습니다.');
            }

            $order_info = DB::table('g5_shop_order')
                ->select(
                    'od_company',
                    'od_hp',
                    'od_cancel_price',
                    'od_receipt_time',
                )
                ->where('od_id', $od_id)
                ->first();                

            $params = [
                'phn' => $order_info->od_hp,
                'company' => $order_info->od_company,
                '주문번호' => $od_id,
                '취소금액' => number_format($order_info->od_cancel_price).'원',
                '취소날짜' => $order_info->od_receipt_time,
            ];                

        } elseif ($template_code == 'samma_201') {  // 모든 주문, 취소 관련 공통으로 이 템플릿으로 사용

            if (!$od_id) {
                throw new \Exception('주문번호 정보가 누락되었습니다.');
            }

            $order_info = DB::table('g5_shop_order')
                ->select(
                    'od_company',
                    'od_hp',
                    'od_cart_price',
                    'od_date',
                    'od_time',
                )
                ->where('od_id', $od_id)
                ->first();                

            $params = [
                'phn' => $order_info->od_hp,
                'company' => $order_info->od_company,
                '주문날짜' => date('Y년 m월 d일',strtotime($order_info->od_date)),
                '주문시간' => date('H시 i분 s초',strtotime($order_info->od_time)),
                '주문금액' => number_format($order_info->od_cart_price).'원',
                '주문번호' => $od_id,
                '당일출고' => '금일포장 후 명일배송 될 예정 입니다.',
            ];                        

        } elseif ($template_code == 'samma_202') {  // 주문 접수 - 후불거래처(익일출고)

            if (!$od_id) {
                throw new \Exception('주문번호 정보가 누락되었습니다.');
            }

            $order_info = DB::table('g5_shop_order')
                ->select(
                    'od_company',
                    'od_hp',
                    'od_cart_price',
                    'od_date',
                    'od_time',
                )
                ->where('od_id', $od_id)
                ->first();                

            $params = [
                'phn' => $order_info->od_hp,
                'company' => $order_info->od_company,
                '주문날짜' => date('Y년 m월 d일',strtotime($order_info->od_date)),
                '주문시간' => date('H시 i분 s초',strtotime($order_info->od_time)),
                '주문금액' => number_format($order_info->od_cart_price).'원',
                '주문번호' => $od_id,
                '익일출고' => '익일출고되어 배송 될 예정 입니다.',
            ];                        

         } else {

            throw new \Exception('템플릿이 없습니다.');

        }

        $service->params = $params;
        return $service->sendSms($template_code, $this->setup);

    }

}