<?php

namespace App\Traits;


use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;

Trait CommonTrait
{
    private $unit = [
        'box'  => 'it_qty_box',
        'pack' => 'it_qty_pack',
        'pcs'  => 'it_qty_pcs',
    ];

    /**
     * method Name : ProductUnit
     * Description : 상품수량 단위변환
     * Created Date : 2026-01-16
     * Params : Params
     * History :
     *   - 2026-01-16 : Initial creation
     */
    public function ProductUnit(string $it_id,  int $ct_qty)
    {
        if ($it_id && $ct_qty) {
            $item = (array) DB::table('g5_shop_item')
            ->selectRaw("it_gubun, it_qty_box, it_qty_pack, it_qty_pcs")
            ->where('it_id', $it_id)
            ->first();
            
            if ($item) {
                $it_gubun = $item['it_gubun'];
                if (!$it_gubun) {
                    throw new \Exception('구분이 존재하지 않습니다.');
                }
                $data = [
                    'box'   => '',
                    'pack'  => '',
                    'pcs'   => '',
                    'piece' => $item[$this->unit[$it_gubun]],
                ];

                $data[$it_gubun] = intdiv($ct_qty, $item[$this->unit[$it_gubun]]);
                
                return $data;
            } else {
                return [];
            }
        } else {
            return [];
        }
    }


    /**
     * method Name : make_od_id
     * Description : od_id 유니크키 생성
     * Created Date : 2026-01-16
     * Params : Params
     * History :
     *   - 2026-01-16 : Initial creation
     */
    public function make_od_id()
    {
        do {
            $micro = microtime(true);
            $base  = date('YmdHis') . sprintf('%06d', ($micro - floor($micro)) * 1000000);
            $od_id = $base . mt_rand(100, 999);


            $exists = DB::table('g5_uniqid')->where('uq_id', $od_id)->count();
        } while ($exists > 0);

        return $od_id;
    }


    public function make_group_code() { return uniqid('', true); }


    /**
     * method Name : getSiteInfo
     * Description : 사이트정보 조회
     * Created Date : 2026-01-16
     * Params : Params
     * History :
     *   - 2026-01-16 : Initial creation
     */
    public function getSiteInfo()
    {
        $info = DB::table('tb_setup')->select(['ss_content'])->where('ss_name', 'site_info')->first();

        $ss_content = unserialize($info->ss_content);

        return $ss_content;
    }



    /**
     * method Name : getSmsSetupInfo
     * Description : sms setup 정보
     * Created Date : 2026-02-06
     * Params : Params
     * History :
     *   - 2026-02-06 : Initial creation
     */
    public function getSmsSetupInfo()
    {
        $info = DB::table('tb_setup')->select(['ss_content'])->where('ss_name', 'sms_info')->first();

        $ss_content = unserialize($info->ss_content);

        return $ss_content;
    }


    /**
     * method Name : 카카오 알림톡 사용여부
     * Description : Description
     * Created Date : 2026-02-06
     * Params : Params
     * History :
     *   - 2026-02-06 : Initial creation
     */    
    public function getAlrimTokInfo()
    {
        $info = DB::table('tb_setup')->select(['ss_content'])->where('ss_name', 'sms_info')->first();

        $ss_content = unserialize($info->ss_content);

        return $ss_content['bizmsg_use'];
    }


    public function encrypted($od_id)
    {
        $key = "samma!1@2#3$4%5!6"; 

        // 임시 키 생성 (GET으로 보내기 위해 짧게)
        $temp_key = bin2hex(random_bytes(4)); // 8바이트 → 16문자

        // AES 암호화 (디버그용 / 선택 사항)
        $payload_str = $od_id . ":|" . $temp_key;
        $method = "AES-256-CBC";
        $iv = random_bytes(openssl_cipher_iv_length($method));
        $encrypted = openssl_encrypt($payload_str, $method, $key, 0, $iv);
        return base64_encode($iv . $encrypted); // 내부 사용용
    }



    /**
     * 배송욜일 날짜로 변환
     *
     * @param [type] $od_delivery_date
     * @return mixed
     */
    public function getDeliveryDateIncoding($od_delivery_date)
    {
        $dateObj = \DateTime::createFromFormat('Y-m-d', date('Y') . '-' . str_replace(['월','일',' '], ['-','',''], preg_replace('/\s*\(.*\)/', '', $od_delivery_date)));

        return $dateObj->format('Y-m-d');
    }



    public function isMobile($request)
    {
        $isMobile = (empty($request->header('User-Agent')) || preg_match('/Android|iPhone|iPad/i', $request->header('User-Agent'))) ? 1 : 0;

        return $isMobile;
    }
}