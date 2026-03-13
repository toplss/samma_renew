<?php
namespace App\Http\Controllers;

use App\Models\TbMember;
use App\Traits\CommonTrait;
use App\Services\SmsManagementService;
use App\Services\FranchiseCategoryService;
use App\Services\PointLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redis;

class JoinController extends Controller
{
    use CommonTrait;
    protected $CategoryService;

    /**
     * Constructor
     * Description : 가맹점 유형 분류
     * Author : Kim Hairyong 
     * Created Date : 2026-01-28
     * Params : Params
     * History :
     *   - 2026-01-28 : Initial creation
     */    
    public function __construct(FranchiseCategoryService $CategoryService)
    {
        $this->CategoryService = $CategoryService;
    }

    /**
     * method Name : MemberJoin
     * Description : 회원가입 페이지
     * Created Date : 2026-01-28
     * Params : Params
     * History :
     *   - 2026-01-28 : Initial creation
     */
    public function MemberJoin()
    {
        //로그인 상태로 가입페이지 진입 시 메인페이지로 이동
        if (session('ss_mb_code')) {
            return redirect('/')->with('info', '이미 로그인 상태 입니다.');
        }

        $ca_id_length = 2;
        $items = [
            'franchise_category' => $this->CategoryService->FranchiseCategory($ca_id_length),
            'site_info' => $this->getSiteInfo(),
        ];        


        return view('mypage.join', ['items' => $items]);
    }

    
    /**
     * method Name : CheckId
     * Description : 아이디 중복체크
     * Created Date : 2026-01-28
     * Params : Params
     * History :
     *   - 2026-01-28 : Initial creation
     */
    public function CheckId(Request $request)
    {

        $request->validate([
            'user_id' => 'required|string|min:4|max:20',
        ], [
            'user_id.required' => '아이디를 입력해주세요.',
            'user_id.min'      => '아이디는 최소 4자 이상 입력해주세요.',
            'user_id.max'      => '아이디는 최대 20자까지 가능합니다.',
        ]);

        $exists = TbMember::where('mb_id', $request->user_id)->exists();

        return response()->json([
            'exists' => $exists
        ]);
    }


    /**
     * method Name : CheckCompanyNo
     * Description : 사업자등록번호 중복체크
     * Created Date : 2026-02-02
     * Params : Params
     * History :
     *   - 2026-01-28 : Initial creation
     */
    public function CheckCompanyNo(Request $request)
    {

        $request->validate([
            'mb_company_no' => 'required|integer',
        ], [
            'mb_company_no.required' => '사업자등록번호를 입력해주세요.',
            'mb_company_no.integer'  => '사업자등록번호는 숫자만 입력해주세요.',
        ]);

        $company_no1 = substr($request->input('mb_company_no'), 0, 3);
        $company_no2 = substr($request->input('mb_company_no'), 3, 2);
        $company_no3 = substr($request->input('mb_company_no'), 5, 5);
        $mb_company_no = $company_no1.'-'.$company_no2.'-'.$company_no3;

        $exists = TbMember::where('mb_company_no', $mb_company_no)->exists();

        return response()->json([
            'exists' => $exists
        ]);
    }


    /**
     * method Name : CheckCouponNo
     * Description : 쿠폰번호 중복체크
     * Created Date : 2026-02-02
     * Params : Params
     * History :
     *   - 2026-01-28 : Initial creation
     */
    public function CheckCouponNo(Request $request)
    {
        $request->validate([
            'coupon_no' => 'required',
        ], [
            'coupon_no.required' => '쿠폰번호를 입력해주세요.',
        ]);

        $couponNo = $request->input('coupon_no');

        $coupon = DB::table('tb_coupon')
            ->where('c_id', $couponNo)
            ->where('opt', 1)
            ->where('opts', 1)
            ->first();        

        if (!$coupon) {
            return response()->json([
                'status'  => false,
                'message' => '[ ' . $couponNo . ' ] 은(는) <br>일치하지 않는 쿠폰번호 입니다.'
            ]);
        }

        if ($coupon->state != 1) {
            return response()->json([
                'status'  => false,
                'message' => '[ ' . $couponNo . ' ] 은(는) <br>사용 불가한 쿠폰번호 입니다.'
            ]);
        }

        return response()->json([
            'status'  => true,
            'message' => '[ ' . $couponNo . ' ] 은(는) <br>사용 가능한 쿠폰번호 입니다.'
        ]);
    }


    /**
     * method Name : CheckRecommedId
     * Description : 추천인 아이디 체크
     * Created Date : 2026-01-28
     * Params : Params
     * History :
     *   - 2026-01-28 : Initial creation
     */
    public function CheckRecommendId(Request $request)
    {

        $request->validate([
            'mb_recommend' => 'required',
        ], [
            'mb_recommend.required' => '추천인 아이디를 입력해주세요.',
        ]);

        $mb_recommend = $request->input('mb_recommend');

        $exists = TbMember::where('mb_id', $mb_recommend)->exists();

        $_deny_id = '';

        if($exists) {
            if ($mb_recommend == $_SESSION['ss_mb_id']) {
                return response()->json([
                    'status'  => false,
                    'message' => '[ ' . $mb_recommend . ' ] 본인 아이디로는 추천이 되지 않습니다.'
                ]);            
            } elseif ( preg_match("/[\,]?{$mb_recommend}/i", $_deny_id )) {
                return response()->json([
                    'status'  => false,
                    'message' => '[ ' . $mb_recommend . ' ] 은(는) 추천이 허용되지 않는 아이디 입니다.'
                ]);            
            } else {
                return response()->json([
                    'status'  => true,
                    'message' => '[ ' . $mb_recommend . ' ] 으로 추천인이 확인 되었습니다.'
                ]);            
            }
        }else{
            return response()->json([
                'status'  => false,
                'message' => '[ ' . $mb_recommend . ' ] 은(는) 존재하지 않는 아이디 입니다.'
            ]);            
        }
        
    }


    /**
     * method Name : FranchiseSubCategory
     * Description : 가맹점유형 하위분류
     * Created Date : 2026-01-28
     * Params : Params
     * History :
     *   - 2026-01-28 : Initial creation
     */
    public function FranchiseSubCategory(Request $request)
    {
        $request->validate([
            'ca_id' => 'required|integer',
            'ca_id_length' => 'required|integer',
        ]);

        $ca_id = $request->input('ca_id');
        $ca_id_length = $request->input('ca_id_length');

        $items = $this->CategoryService->FranchiseSubCategory($ca_id_length, $ca_id); 

        return response()->json($items);
    }


    /**
     * method Name : GeolocationKakao
     * Description : 카카오 지도 좌표 api
     * Created Date : 2026-01-28
     * Params : Params
     * History :
     *   - 2026-01-28 : Initial creation
     */
    private function GeolocationKakao($path, array $query = [], $apiKey)
    {
        return Http::withToken($apiKey, 'KakaoAK')
            ->get('https://dapi.kakao.com' . $path, $query)
            ->json();
    }    


    /**
     * method Name : MemberJoinSave
     * Description : 회원가입 처리
     * Created Date : 2026-02-03
     * Params : Params
     * History :
     *   - 2026-02-03 : Initial creation
     */
    public function MemberJoinSave(Request $request)
    {
        $request->validate([
            'user_id'    => 'required',
            'user_pass'  => 'required',
            'mb_name'    => 'required',
            'mb_company' => 'required',
            'mb_hp1'     => 'required',
            'mb_hp2'     => 'required',
            'mb_hp3'     => 'required',
        ],[
            'user_id.required'    => '아이디를 입력해주세요.',
            'user_pass.required'  => '비밀번호를 입력해주세요.',
            'mb_name.required'    => '이름을 입력해주세요.',
            'mb_company.required' => '상호명을 입력해주세요.',
            'mb_hp1.required'     => '휴대폰번호를 입력해주세요.',
            'mb_hp2.required'     => '휴대폰번호를 입력해주세요.',
            'mb_hp3.required'     => '휴대폰번호를 입력해주세요.',
        ]);

        //사이트 설정 정보
        $site_info = $this->getSiteInfo();

        //비밀번호 MD5
        $user_pass = $request->input('user_pass');
        $user_pass_md5   = md5($user_pass);

        //기본 데이터 정리
        $nowTime = time();
        $nowDate = date('Y-m-d H:i:s');
        $mb_company_no = $request->input('mb_company_no');
        $company_no1 = substr($mb_company_no, 0, 3);
        $company_no2 = substr($mb_company_no, 3, 2);
        $company_no3 = substr($mb_company_no, 5, 5);
        $mb_company_no_str = $company_no1.'-'.$company_no2.'-'.$company_no3;        
        if($mb_company_no_str == '--') $mb_company_no_str = '';

        $mb_email = implode('@', [
            $request->input('email1'),
            $request->input('email2'),
        ]);        

        $mb_hp = implode('-', [
            $request->input('mb_hp1'),
            $request->input('mb_hp2'),
            $request->input('mb_hp3'),
        ]);

        $mb_hp_num = implode('', [
            $request->input('mb_hp1'),
            $request->input('mb_hp2'),
            $request->input('mb_hp3'),
        ]);

        $mb_tel = implode('-', [
            $request->input('mb_tel1'),
            $request->input('mb_tel2'),
            $request->input('mb_tel3'),
        ]);

        $manager_tel2 = implode('-', [
            $request->input('manager_tel21'),
            $request->input('manager_tel22'),
            $request->input('manager_tel23'),
        ]);

        if($mb_hp=='--') $mb_hp='';
        if($mb_tel=='--') $mb_tel='';
        if($manager_tel2=='--') $manager_tel2='';

        //나이
        $mb_age = '';
        $mb_birth = $request->input('mb_birth');
        if($mb_birth != '') {
            $arr_birth = explode("-",$mb_birth);
            $mb_age = (date("Y")-$arr_birth[0])+1;
        }

        //은행
        $mb_bank_code = '';
        $mb_bank_name = '';
        $mb_bank = $request->input('mb_bank');      
        if($mb_bank != '') {
            $arr_mb_bank = explode('/', $mb_bank);
            $mb_bank_code = $arr_mb_bank[0];
            $mb_bank_name = $arr_mb_bank[1];
        }

        //주소가공 및 위,경도 정보 처리
        $arr_mb_addr1 = explode(" ", $request->input('mb_addr1'));
        $mb_addr_sido = $arr_mb_addr1[0];   //시도
        $mb_addr_gugun = $arr_mb_addr1[1];  //구군

        $api_key = $site_info['daum_appkey'];
        $data = $this->GeolocationKakao(
            '/v2/local/search/address',
            ['query' => $request->input('mb_addr1')],
            $api_key
        );

        $mb_addr_lat = $data['documents'][0]['y'];   //위도 
        $mb_addr_lng = $data['documents'][0]['x'];   //경도


        //회원등급명 조회
        $level_ca = DB::table('tb_level_category')
            ->select('ca_id', 'ca_name')
            ->where('ca_id', $site_info['join_level'])
            ->first();
        $level_ca_id_name = $level_ca->ca_name;


        //가맹점유형명 조회
        $franchise_ca = DB::table('tb_franchise_category')
            ->select('ca_id', 'ca_name')
            ->where('ca_id', $request->input('franchise_ca_id'),)
            ->first();
        $franchise_ca_name = $franchise_ca->ca_name;

        
        // 아이디 중복 체크
        $exists = DB::table('tb_member')
            ->where('mb_id', $request->mb_id)
            ->exists();

        if ($exists) {
            return back()->withErrors('이미 사용 중인 아이디입니다.');
        }


        // 회원가입 처리시작
        $insert_id = DB::table('tb_member')->insertGetId([

            'mb_login_type'         => $request->input('mb_login_type'),
            'mb_level_type'         => $request->input('mb_level_type'),
            'mb_id'                 => $request->input('user_id'),
            'mb_password'           => $user_pass_md5,
            'mb_password_text'      => $request->input('user_pass'),
            'mb_mypage_password'    => $request->input('mb_mypage_password'),
            'mb_name'               => $request->input('mb_name'),  
            'level_ca_id'           => $site_info['join_level'],
            'level_ca_id_name'      => $level_ca_id_name,
            'mb_level'              => $site_info['join_level'],
            'mb_gubun'              => 1,
            'mb_company_real'       => $request->input('mb_company'),
            'mb_company'            => $request->input('mb_company'),
            'mb_company_no'         => $mb_company_no_str,
            'mb_state'              => $site_info['join_state'],
            'mb_cs_bill'            => $request->input('mb_cs_bill'),
            'franchise_ca_id'       => $request->input('franchise_ca_id'),
            'franchise_ca_id2'      => $request->input('franchise_ca_id2')??'',
            'franchise_ca_id_name'  => $franchise_ca_name,
            'mb_sex'                => $request->input('mb_sex'),
            'mb_zip1'               => $request->input('mb_zip1'),
            'mb_addr1'              => $request->input('mb_addr1'),
            'mb_addr2'              => $request->input('mb_addr2'),
            'mb_addr_jibeon'        => $request->input('mb_addr_jibeon') ?? '',
            'mb_addr_sido'          => $mb_addr_sido,
            'mb_addr_gugun'         => $mb_addr_gugun,
            'mb_addr_lng'           => $mb_addr_lng,
            'mb_addr_lat'           => $mb_addr_lat,
            'mb_email'              => $mb_email,
            'mb_tel'                => $mb_tel,
            'mb_hp'                 => $mb_hp,
            'mb_hp_num'             => $mb_hp_num,
            'manager_name'          => $request->input('manager_name') ?? '',
            'manager_tel2'          => $manager_tel2,
            'mb_birth'              => $request->input('mb_birth') ?? '',
            'mb_age'                => $mb_age,
            'mb_job'                => $request->input('mb_job') ?? '',
            'mb_product'            => $request->input('mb_product') ?? '',
            'mb_bank'               => $mb_bank_name,
            'mb_bank_code'          => $mb_bank_code,
            'mb_account'            => $request->input('mb_account') ?? '',
            'mb_account_holder'     => $request->input('mb_account_holder') ?? '',
            'mb_account_files1'     => serialize(false),
            'mb_mailling'           => $request->input('mb_mailling') ?? '',
            'mb_sms'                => $request->input('mb_sms') ?? '',
            'mb_notice_talk'        => $request->input('mb_notice_talk') ?? '',
            'mb_notice_receive'     => $request->input('mb_notice_receive') ?? '',
            'mb_add_channel'        => $request->input('mb_add_channel') ?? '',
            'mb_introduce'          => $request->input('mb_introduce') ?? '',
            'join_date'             => $nowTime,
            'mb_datetime'           => $nowDate,
            'join_ip'               => $request->ip(),
            'modify_date'           => $nowTime,
            'secession_reason'      => '',
        ]);

        // 회원코드 생성
        $mb_num = $insert_id;
        $alphabet_prefix = ["", "M", "MM", "MMM", "MMMM", "MMMMM"];
        $share     = intdiv($mb_num, 9999);    //몫
        $remainder = $mb_num % 9999;           //나머지
        if ($remainder === 0) {
            $remainder = 9999;
            $share--;
        }
        $mb_code = ($alphabet_prefix[$share] ?? '') . sprintf('%04d', $remainder);

        //회원코드 업데이트
        DB::table('tb_member')
            ->where('mb_num', $mb_num)
            ->update([
                'mb_code' => $mb_code,
            ]);

        
        // 통장사본 첨부파일 처리
        $folder = '/common_data/member/';
        $files = $request->file('mb_account_files1');        
        $mb_company = $request->input('mb_company');

        // 첨부파일 없음 → 기존 테이블 규칙 유지
        if (!$files) {

            $mb_account_files1 =  serialize(false); // b:0;
            
        } else {

            $data = [];

            foreach ($files as $key => $file) {
                $data[$key] = [
                    'name'  => $mb_company. '_' . $file->getClientOriginalName(),
                    'type'  => $file->getMimeType(),
                    'size'  => $file->getSize(),
                    'hits'  => 0,
                    'sname' => sprintf('%04d', $mb_num) . '_' . mt_rand(100, 999) . "_{$key}.file",
                ];

                Storage::disk('sftp_remote')
                    ->put($folder.$data[$key]['sname'], file_get_contents($file));                        
            }

            //첨부파일 정보 serialize (이전 쇼핑몰의 회원가입 시 진행됐던 파일정보 serialize 처리 로직을 승계함)
            $mb_account_files1 = serialize($data);        

        }

        //통장사본 첨부파일 업데이트
        DB::table('tb_member')
            ->where('mb_num', $mb_num)
            ->update([
                'mb_account_files1' => $mb_account_files1,
            ]);


        // 신규회원 쿠폰처리 START
        $coupon_no = implode('-', [
            $request->input('coupon_no1'),
            $request->input('coupon_no2'),
            $request->input('coupon_no3'),
            $request->input('coupon_no4'),
        ]);        

        $count = DB::table('tb_coupon')->where('c_id', $coupon_no)->count();

        if ($site_info['new_member_coupon_use'] == '1' && $count > 0) {

            // tb_coupon 테이블에서 필요한 컬럼 조회
            $coupon_info = DB::table('tb_coupon')
                ->select('id', 'opt', 'opts', 'gubun', 'c_id', 'c_price', 'c_term')
                ->where('c_id', $coupon_no)
                ->first();

            // tb_coupon_member 테이블에 insert
            DB::table('tb_coupon_member')->insert([
                'cp_opt'        => $coupon_info->opt,
                'cp_opts'       => $coupon_info->opts,            
                'cp_gubun'      => $coupon_info->gubun,
                'cp_idx'        => $coupon_info->id,
                'cp_number'     => $coupon_info->c_id,
                'cp_price'      => $coupon_info->c_price,
                'cp_term'       => $coupon_info->c_term,
                'mb_num'        => $mb_num,
                'mb_id'         => $request->input('user_id'),
                'mb_name'       => $request->input('mb_name'),
                'mb_company'    => $request->input('mb_company'),
                'cp_subject'    => '',
                'cp_content'    => '',
                'cp_datetime'   => $nowDate,
                'state'         => '1',
            ]);        

            // tb_coupon 테이블 업데이트
            DB::table('tb_coupon')
                ->where('id', $coupon_info->id)
                ->update([
                    'mb_num'     => $mb_num,
                    'mb_id'      => $request->input('user_id'),
                    'state'      => '2',
                    'modifydate' => $nowDate,
                ]);

        }
        // 신규회원 쿠폰처리 END


        // 회원가입 카카오 알림톡 START
        $sms = new SmsManagementService();
        $templat_code = 'samma_1';
        $phone = $mb_hp;
        $name = $request->input('mb_company');

        $sms->kakaoAlrimTok($templat_code, '', $phone, $name);
        // 회원가입 카카오 알림톡 END

        return redirect()->route('/')->with('success', '회원가입이 정상적으로 완료되었습니다.<br>관리자 승인 후 로그인이 가능합니다.');

    }


    //임시 테스트
    public function dev_func ()
    {

        // // 포인트로그 테스트
        // $mbCode = 4518;
        // $oid = '20260223100107719468969';
        // $poGubun = 'CHARGE';
        // // $poGubun = 'RESERVE';
        // // $poType = 'increase';
        // $poType = 'decrease';
        // $amount = 34470;

        // $pointLog = new PointLogService();
        // $pointLog->CreatePointLog($mbCode, $oid, $poGubun, $poType, $amount);


        // 알림톡 START
        $sms = new SmsManagementService();
        $templat_code = 'samma_3';
        // $phone = '01042246970';
        // $name = '테스트업체이름';
        $as_idx = '13';

        // $result = $sms->kakaoAlrimTok($templat_code, '', '', '', $as_idx);
        // 알림톡 END


        // return 'ok';
        return view('dev_test');
    }

    
}