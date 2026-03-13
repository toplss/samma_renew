<?php
namespace App\Services;

use App\Traits\CommonTrait;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;
use Predis\Command\Traits\Replace;

class AlrimTokService
{
    use CommonTrait;

    private $real_url = 'https://alimtalk-api.bizmsg.kr';
    private $dev_url = 'https://dev-alimtalk-api.bizmsg.kr:1443';
    private $sms_template;
    private $pattern = '/#\{([^}]+)\}/';  // 정규 표현식

    public $params = [];

    public function __construct()
    {
        $this->sms_template = DB::table('tb_sms_templates');
    }


    /**
     * 알림톡 발송하기
     *
     * @param string $templat_code
     * @param array $setup
     * @return array
     */
    public function sendSms(string $templat_code, array $setup)
    {

        try {
            
            $templat_info = ['templat_contetns' => '', 'button_info' => ''];
            if ($templat_code) {
                $templat_info = (array) $this->sms_template->where('templat_type', 'alrimtok')->where('templat_code', $templat_code)->first();
            }

            $message = $templat_info['templat_contetns'];
            $buttons = $templat_info['button_info'];
            $use_yn = $templat_info['use_yn'];

            // #{} 패턴을 가진 문자열 추출
            preg_match_all($this->pattern, $message, $matches);

            $metchformat = $matches[1];
            // #[shop_name] 키는 $params 와 비교할 필요가 없으므로 제거
            unset($metchformat[0]);


            // 비교를 위한 배열 추가
            $filter['phn'] = '수신자 연락처';
            foreach ($metchformat as $key => $row) {
                $filter[$row] = "#{".$row."}";
            }


            // 템플릿에 설정된 문자열을 replace 처리후 바인딩 될 값이 배열로 넘어 오지 않을경우 리턴
            $diff = array_diff_key($filter, $this->params);
            if (count($diff) > 0) {
                return [
                    'status' => 'fail',
                    'array_keys' => $diff,
                    'response' => 'setParams([])->send(템플릿코드) 방식으로 선언후 setParams() 에 array_keys 리턴 값을 [key => value] 선언후 사용하세요.'
                ];
            }


            foreach ($matches[1] as $key => $row) {
                if ($row == 'shop_name') {
                    $message = str_replace('[#{shop_name}]', $setup['bizmsg_site_name'], $message);
                } else {
                    $message = str_replace('#{'.$row.'}', $this->params[$row], $message);
                }
            }

            $phone = str_replace('-', '', $this->params['phn']);

// $phone = '01042246970'; // 개발중 받는 연락처 고정
// $phone = '01035404849'; // 개발중 받는 연락처 고정

            $bizmsg_use = $this->getAlrimTokInfo();

            $base_url = '';
            // 알림톡 기능 on / off
			if ($bizmsg_use == '1') {
                $base_url = $this->real_url;
			} else {
                 return false;
                // $base_url = $this->dev_url;
            }


            // 텝플릿별 알림톡 기능 on / off
			if ($use_yn == 'N') {
                return false;
			}            

            // request body 셋팅
            $string = $this->getBody($templat_info, $setup, $phone, $message, $buttons);

            // Guzzle 클라이언트 인스턴스 생성
            $client = new Client(['base_uri' => $base_url]);

            $response = $client->request('POST', '/v2/sender/send', [
                'body' => $string,
                'headers' => [
                    'Content-Type'  => 'application/json',
                    'userId' => $setup['bizmsg_user_id']
                ], 
            ]);

            // JSON 응답을 PHP 배열로 디코딩
            // $result = json_decode($response->getBody(), true);

        } catch (\Exception  $e) {
            return redirect()->back()->with('error', '알림톡 발송이 실패 하였습니다. error');
        }

    }

    
    public function getBody(array $templat_info, array $setup, string $phone, string $message, $buttons) : string
    {
        $string = '[{
            "tmplId" : "'.$templat_info['templat_code'].'",
            "message_type" : "'.$setup['bizmsg_message_type'].'",
            "phn" : "'.$phone.'",
            "profile" : "'.$setup['bizmsg_profile_key'].'",
            "reserveDt" : "00000000000000",
            "msg" : '.$message.',
            '.$buttons.'
        }]';

        return $string;
    }
}