<?php

namespace App\Traits;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;


trait RedisTrait
{
    public static function setRedis($redis_key_generate, $data)
    {
        // $ttl = 24 * 60 * 60;
        // $ttl = 60;  //전산재고 실시간 반영을 위해 1분으로 변경함 ( 전산재고 갱신 크론 주기가 1분임 )
        $ttl = 60 * 60; // 5분

        Redis::set($redis_key_generate, json_encode($data, JSON_UNESCAPED_UNICODE), 'EX', $ttl);
    }


    public static function getReids($redis_key_generate)
    {
        return json_decode(Redis::get($redis_key_generate), true);
    }
}