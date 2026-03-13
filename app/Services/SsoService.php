<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redis;

class SsoService
{
    public function setChareItem($od_id, $data = null)
    {
        $hash_key = $od_id;

        $data = [
            'mb_id' => session('ss_mb_id'),
            'od_id' => $od_id,
            'data'  => $data ?? [],
            'return_url' => config('sso.url')
        ];

        $json = json_encode($data, JSON_UNESCAPED_UNICODE);

        $lua = "
            redis.call('DEL', KEYS[1])
            redis.call('SET', KEYS[1], ARGV[1], 'EX', ARGV[2])
            return 1
        ";

        $result = Redis::eval($lua, 1, $hash_key, $json, 300); // 60초로 변경

        if ($result) {
            return true;
        } else {
            return false;
        }
    }
}