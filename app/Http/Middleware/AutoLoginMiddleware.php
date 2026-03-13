<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;


class AutoLoginMiddleware
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
        if (!session()->has('ss_mb_id') && $request->hasCookie('remember_token')) {
            $token = $request->cookie('remember_token');

            $user = DB::table('tb_member')->where('remember_token', $token)->first();

            if ($user) {
                // 세션 복원
                session([
                    'ss_mb_id'   => $user->mb_id,
                    'ss_mb_code' => $user->mb_code,
                    'ss_hash'    => Str::random(40)
                ]);

                // 로그인 기록 업데이트 가능
                DB::table('tb_member')->where('mb_code', $user->mb_code)->update([
                    'login_ip'    => $request->ip(),
                    'login_count' => $user->login_count + 1,
                    'login_date'  => time()
                ]);
            }
        }

        return $next($request);
    }
}
