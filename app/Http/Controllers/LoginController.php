<?php

namespace App\Http\Controllers;

use App\Models\TbMember;
use App\Models\TbLogin;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cookie;
use Carbon\Carbon;
use Debugbar;
use Jenssegers\Agent\Agent;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class LoginController extends Controller
{
    //
    public function login(Request $request)
    {
        $autoLogin   = $this->autoLogin($request);

        // 자동 로그인 성공 시 메인 페이지로 리다이렉트
        if ($autoLogin) {
            return redirect()->route('/');
        }

        $request->validate([
            'mb_id' => 'required|string',
            'mb_password' => 'required|string',
        ]);

        // 사용자 조회
        $user = TbMember::select(
            'mb_id', 
            'mb_name',
            'mb_code', 
            'mb_state',
            'mb_password', 
            'mb_duple_login_use',
            'login_count',
            'mb_login_type',
            'level_ca_id',
            'level_ca_id3'
        )->where('mb_id', $request->mb_id)->first();

        $ip = $request->ip(); // 클라이언트 IP
        $currentDomain = $request->getHost();
        $userAgent = $request->header('User-Agent');
        $hash = Str::random(40);


        if (!$user) {
            return redirect()->route('login_intro')->with('login', '존재하지 않는 사용자 입니다. ID를 확인해주세요.');
        }

        // 승인회원 체크
        if ($user->mb_state != '1') {
            $state_message = [
                ''  => '회원상태가 정의되지 않은 회원입니다. 관리자에게 문의 바랍니다.',
                '2' => '미승인 회원입니다. 관리자에게 문의 바랍니다.',
                '3' => '탈퇴 회원입니다. 관리자에게 문의 바랍니다.',
                '4' => '폐업 회원입니다. 관리자에게 문의 바랍니다.',
                '5' => '휴업 회원입니다. 관리자에게 문의 바랍니다.',
                '6' => '종료 회원입니다. 관리자에게 문의 바랍니다.',
            ];

            return redirect()->route('login_intro')->with('login', $state_message[$user->mb_state]);
        }
        

        if (!$user || $user->mb_password !== md5($request->mb_password)) {
            return redirect()->route('login_intro')->with('login', '아이디 또는 비밀번호가 옳바르지 않습니다.');
        }


        /**
         * Author : Lee Sangseung
         * Description : 아이디 저장
         * Created Date : 2026-01-24
         */
        if ($request->remember_id) {

            Cookie::queue(
                'remember_id',
                $request->mb_id,
                60 * 24 * 7 // 7일
            );
    
        } else {
    
            Cookie::queue(Cookie::forget('remember_id'));
        }


        /**
         * Author : Lee Sangseung
         * Description :  자동 로그인
         * Created Date : 2026-01-24
         */
        if ($request->remember_me) {
            // 랜덤 토큰 생성
            $token = Str::random(60);

            // DB에 토큰 저장
            TbMember::where('mb_code', $user->mb_code)->update([
                'remember_token' => $token
            ]);

            Cookie::queue(
                'remember_token',
                $token,
                60 * 24 * 30 // 30일
            );        
        } else {
            Cookie::queue(Cookie::forget('remember_token'));
        }


        session([
            'ss_mb_id'   => $user->mb_id,
            'ss_mb_code' => $user->mb_code,
            'ss_hash'    => $hash
        ]);

        TbMember::where('mb_code', $user->mb_code)->update([
            'login_ip'      => $ip,
            'login_count'   => $user->login_count + 1,
            'login_date'    => time()
        ]);

        // 중복 로그인 허용 여부에 따라 session_id 저장
        if ($user->mb_duple_login_use == 'N') {
            

            $agent = new Agent();

            $device = $agent->isMobile() ? 'mobile' : 'pc';

            TbLogin::where('ss_num', $user->mb_code)->where('ss_type', 'A')->update(['ss_type' => 'B', 'logout_chk' => 'Y']);

            $now = Carbon::now();

            TbLogin::create([
                'login_tel'     => '',
                'ss_pwd'        => '',
                'login_gubun'   => $device,
                'ss_type'       => 'A',
                'ss_num'        => $user->mb_code,
                'ss_id'         => $user->mb_id,
                'ss_nick'       => $user->mb_name,
                'ss_level'      => $user->level_ca_id3,
                'ss_hash'       => $hash,
                'login_date'    => time(),
                'reg_date'      => $now->format('Y-m-d H:i:s'),
                'login_ip'      => $ip,
                'logout_chk'    => 'N',
                'logout_date'   => $now->format('Y-m-d H:i:s'),
                'logout_ip'     => '',
                'login_referer' => $currentDomain,
                'login_agent' => $userAgent,
                'login_browser' => '',
                'login_os' => '',
                'login_device' => '',
            ]);
        }

        return redirect()->route('/');
    }



    public function logout(Request $request) 
    {
        $hash = $request->session()->get('ss_hash');
        $mb_code = $request->session()->get('ss_mb_code');
        
        TbLogin::where('ss_hash', $hash)->where('ss_num', $mb_code)->update([
            'ss_type' => 'B',
            'logout_chk' => 'Y',
            'logout_ip' => $request->ip(),
        ]);

        // 세션 전체 삭제도 가능
        $request->session()->forget([
            'ss_mb_id',
            'ss_mb_code',
            'ss_hash' 
        ]);

        Redis::del($mb_code.':member');

        // 로그아웃 후 intro 페이지로 이동
        return redirect()->route('/')->with('message', '로그아웃 되었습니다.');
    }


    private function autoLogin($request)
    {
        $request->validate([
            'mb_id' => 'required|string',
        ]);

        $mbid = $request->mb_id;
        $pass = $request->input('mb_password', '');

        // 명시적 로그인 시도인 경우 자동 로그인 처리하지 않음
        if ($mbid && $pass) {
            return false;
        }
        

        if ($request->hasCookie('remember_token') && $request->remember_me == 'Y') {

            $token = $request->cookie('remember_token');

            $user = TbMember::where('remember_token', $token)->first();

            if ($user) {

                $hash = Str::random(40);
                session([
                    'ss_mb_id'   => $user->mb_id,
                    'ss_mb_code' => $user->mb_code,
                    'ss_hash'    => $hash
                ]);

                // 로그인 기록 업데이트
                $user->update([
                    'login_ip'    => $request->ip(),
                    'login_count' => $user->login_count + 1,
                    'login_date'  => time()
                ]);

                if ($user->mb_duple_login_use == 'N') {
                
                    $agent = new Agent();
        
                    $device = $agent->isMobile() ? 'mobile' : 'pc';
        
                    TbLogin::where('ss_num', $user->mb_code)->where('ss_type', 'A')->update(['ss_type' => 'B', 'logout_chk' => 'Y']);
        
                    $now = Carbon::now();
        
                    TbLogin::create([
                        'login_tel'     => '',
                        'ss_pwd'        => '',
                        'login_gubun'   => $device,
                        'ss_type'       => 'A',
                        'ss_num'        => $user->mb_code,
                        'ss_id'         => $user->mb_id,
                        'ss_nick'       => $user->mb_name,
                        'ss_level'      => $user->level_ca_id3,
                        'ss_hash'       => $hash,
                        'login_date'    => time(),
                        'reg_date'      => $now->format('Y-m-d H:i:s'),
                        'login_ip'      => $request->ip(),
                        'logout_chk'    => 'N',
                        'logout_date'   => $now->format('Y-m-d H:i:s'),
                        'logout_ip'     => '',
                        'login_referer' => $request->getHost(),
                        'login_agent' => $request->header('User-Agent'),
                        'login_browser' => '',
                        'login_os' => '',
                        'login_device' => '',
                    ]);
                }

                return true;

            } else {

                return false;
            }
        } else {
            
            Cookie::queue(Cookie::forget('remember_token'));

            return false;
        }
    }


    public function duplicateLoginCheck(Request $request)
    {
        if ($request->filled('check')) {

            DB::table('login_duplicate_check')->where('mb_code', $request->input('mb_code'))->delete();
        }
    }
}

