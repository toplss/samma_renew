<?php
namespace App\Traits;


use Illuminate\Support\Facades\Log;

trait InicisTrait
{
    private function getBankCode($e)
    {
        $BANK_CODE = array(
            '03' => '기업은행',
            '04' => '국민은행',
            '05' => '외환은행',
            '07' => '수협중앙회',
            '11' => '농협중앙회',
            '20' => '우리은행',
            '23' => 'SC 제일은행',
            '31' => '대구은행',
            '32' => '부산은행',
            '34' => '광주은행',
            '37' => '전북은행',
            '39' => '경남은행',
            '53' => '한국씨티은행',
            '71' => '우체국',
            '81' => '하나은행',
            '88' => '신한은행',
            'D1' => '동양종합금융증권',
            'D2' => '현대증권',
            'D3' => '미래에셋증권',
            'D4' => '한국투자증권',
            'D5' => '우리투자증권',
            'D6' => '하이투자증권',
            'D7' => 'HMC 투자증권',
            'D8' => 'SK 증권',
            'D9' => '대신증권',
            'DA' => '하나대투증권',
            'DB' => '굿모닝신한증권',
            'DC' => '동부증권',
            'DD' => '유진투자증권',
            'DE' => '메리츠증권',
            'DF' => '신영증권'
        );

        if (isset($BANK_CODE[$e])) {
            return $BANK_CODE[$e];
        }
    }


    private function getCardCode($e)
    {
        $CARD_CODE = array(
            '01' => '외환',
            '03' => '롯데',
            '04' => '현대',
            '06' => '국민',
            '11' => 'BC',
            '12' => '삼성',
            '14' => '신한',
            '15' => '한미',
            '16' => 'NH',
            '17' => '하나 SK',
            '21' => '해외비자',
            '22' => '해외마스터',
            '23' => 'JCB',
            '24' => '해외아멕스',
            '25' => '해외다이너스'
        );

        if (isset($CARD_CODE[$e])) {
            return $CARD_CODE[$e];
        }
    }


    private function getPayMethod($e)
    {
        $PAY_METHOD = array(
            'VCard'      => '신용카드',
            'Card'       => '신용카드',
            'CARD'       => '신용카드',
            'DirectBank' => '계좌이체',
            'HPP'        => '휴대폰',
            'VBank'      => '가상계좌'
        );

        if (isset($PAY_METHOD[$e])) {
            return $PAY_METHOD[$e];
        }
    }



    public function resultMap($resultMap)
    {
        if ($resultMap['resultCode'] == '0000') {
            $tno        = $resultMap['tid'];
            $amount     = $resultMap['TotPrice'];
            $app_time   = $resultMap['applDate'].$resultMap['applTime'];
            $pay_method = $resultMap['payMethod'];
            $pay_type   = $this->getPayMethod($pay_method);
            $commid     = '';
            $mobile_no  = '';
            $app_no     = $resultMap['applNum'];
            $card_name  = $this->getCardCode($resultMap['CARD_Code']);
            
            $extraDt = [];
            switch($pay_type) {
                case '가상계좌':
                    $bankname  = $this->getBankCode($resultMap['VACT_BankCode']);
                    $account   = $resultMap['VACT_Num'].' '.$resultMap['VACT_Name'];
                    $app_no    = $resultMap['VACT_Num'];

                    $extraDt['bankname'] = $bankname;
                    $extraDt['account']  = $account;
                    break;
                default:
                    break;
            }


            return [
                'tno'       => $tno,
                'amount'    => $amount,
                'app_time'  => $app_time,
                'pay_type'  => $pay_type,
                'app_no'    => $app_no,
                'card_name' => $card_name,
                'extra'     => $extraDt
            ];
        }
    }



    public function resultMapMobile($resultMap)
    {
        if ($resultMap['P_STATUS'] == '00') {
            $tno        = $resultMap['P_TID'];
            $amount     = $resultMap['P_AMT'];
            $app_time   = $resultMap['P_AUTH_DT'];
            $pay_method = $resultMap['P_TYPE'];
            $pay_type   = $this->getPayMethod($pay_method);
            $commid     = '';
            $mobile_no  = '';
            $app_no     = $resultMap['P_AUTH_NO'];
            $card_name  = $this->getCardCode($resultMap['P_CARD_PURCHASE_CODE']);
            
            $extraDt = [];
            // 가상계좌 정보 없음
            // switch($pay_type) {
            //     case '가상계좌':
            //         $bankname  = $this->getBankCode($resultMap['VACT_BankCode']);
            //         $account   = $resultMap['VACT_Num'].' '.$resultMap['VACT_Name'];
            //         $app_no    = $resultMap['VACT_Num'];

            //         $extraDt['bankname'] = $bankname;
            //         $extraDt['account']  = $account;
            //         break;
            //     default:
            //         break;
            // }


            return [
                'tno'       => $tno,
                'amount'    => $amount,
                'app_time'  => $app_time,
                'pay_type'  => $pay_type,
                'app_no'    => $app_no,
                'card_name' => $card_name,
                'extra'     => $extraDt
            ];
        }
    }
}