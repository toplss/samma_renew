<?php

namespace App\Http\Middleware;

use Closure;

class RefreshLimit
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
        $isLogin = $request->session()->has('ss_hash');

        // 비로그인 사용자
        $ip = $request->ip();
        $ua = substr($request->userAgent(), 0, 50); // 너무 길면 잘라냄
        $sessionId = $request->session()->getId();
        $guestKey = md5($ip . '|' . $ua . '|' . $sessionId);

        $userKey = $isLogin
            ? 'user:' . $request->session()->get('user_id')
            : 'guest:'. $guestKey;


        $key = 'refresh_limit:' . $userKey . ':' . md5($request->path());;
        $limit = 50;
        $ttl = 60; // 2분 (초)
        $data = session()->get($key, [
            'count' => 0,
            'time'  => time(),
        ]);
                
        // 1분이 지났으면 초기화
        if (time() - $data['time'] > $ttl) {
            $data = [
                'count' => 1,
                'time'  => time(),
            ];
        } else {
            $data['count']++;
        }

        session()->put($key, $data);

        if ($data['count'] > $limit) {
            $request->session()->flush();

            return response(
                "<script>
                    alert('정상적인 이용이 아닌 것으로 판단되어 요청이 제한되었습니다.');
                    location.replace('/intro');
                </script>",
                429
            )->header('Content-Type', 'text/html; charset=UTF-8');
        }

        return $next($request);
    }
}
