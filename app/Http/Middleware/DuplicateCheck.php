<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class DuplicateCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (session()->get('duplicate')) {

            Redis::del(session('ss_mb_code').':member');

            session()->forget(['ss_mb_id', 'ss_mb_code', 'ss_hash', 'duplicate']); // 체크 후 제거
            
            return redirect('/')->with('duplicate_alert', '다른 기기에서 로그인되어 자동으로 로그아웃되었습니다.<br>중복 로그인이 계속 감지될 경우 비밀번호를 변경해 주세요.');
        }

        return $next($request);
    }
}
