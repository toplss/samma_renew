<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Redis;
use Illuminate\Http\Request;


class TrafficGate
{
    // 슬롯 키 Prefix
    private $prefix = 'traffic:gate:slot:';

    // 동시 허용 인원
    private $limit = 500;

    // TTL (초)
    private $ttl = 20;

    public function handle(Request $request, Closure $next)
    {
        if ($this->skip($request)) {
            return $next($request);
        }

        $token = uniqid('', true);
        $mySlot = null;

        // ---------------------------
        // 슬롯 풀 순회
        // ---------------------------

        for ($i = 1; $i <= $this->limit; $i++) {

            $slotKey = $this->prefix . $i;

            $lock = Redis::set(
                $slotKey,
                $token,
                'NX',
                'EX',
                $this->ttl
            );

            if ($lock) {
                $mySlot = $slotKey;
                break;
            }
        }

        // 모든 슬롯 사용중
        if (!$mySlot) {

            return response()
                ->view('waiting', [
                    'waiting' => $this->limit
                ])
                ->setStatusCode(503);
        }

        try {
            return $next($request);

        } finally {

            // ---------------------------
            // 자기 슬롯만 안전하게 반납
            // ---------------------------

            $lua = <<<LUA
if redis.call("GET", KEYS[1]) == ARGV[1] then
    return redis.call("DEL", KEYS[1])
else
    return 0
end
LUA;

            Redis::eval($lua, 1, $mySlot, $token);
        }
    }

    private function skip(Request $request)
    {
        $uri = $request->path();

        return
            str_starts_with($uri, 'css') ||
            str_starts_with($uri, 'js') ||
            str_starts_with($uri, 'images') ||
            $uri === 'health' ||
            $uri === 'waiting';
    }
}
