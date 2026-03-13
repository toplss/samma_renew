<?php

namespace App\Http\Middleware;

use App\Models\TbLogin;
use Closure;
use Debugbar;
use App\Models\TbMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class CheckLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $mb_id = $request->session()->get('ss_mb_id');
        $mb_code = $request->session()->get('ss_mb_code');
        $hash = $request->session()->get('ss_hash');

        if (!$mb_id) {
            return redirect()->route('/')->with('info', '로그인 후 이용 가능합니다.');
        }

        $user = TbMember::select('mb_duple_login_use')->where('mb_id', $mb_id)->first();

        // 중복 로그인 비허용 체크
        if ($user && $user->mb_duple_login_use == 'N') {
            $cnt = TbLogin::where('ss_num', $mb_code)
            ->where('ss_type', 'B')
            ->where('ss_hash', $hash)
            ->count();

            if ($cnt > 0) {
                $exists = DB::table('login_duplicate_check')->where('mb_code', $mb_code)->exists();
                if (!$exists) {
                    DB::table('login_duplicate_check')->insert(['mb_code' => $mb_code]);
                }

                session()->put('duplicate', 1);
            }
        }

        return $next($request);
    }
}
