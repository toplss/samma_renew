<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class KakaoAlrimTokLogModel extends Model
{
    protected $table = 'kakao_alrimtok_logs';
    protected $primaryKey = 'idx';
    public $timestamps = false;


    protected $fillable = [
        'template_code',
        'mb_hp',
        'message',
    ];

    /**
     * 알림톡 로그 저장
     */
    public static function createLog($templateCode, $mbHp, $message): bool
    {
        return self::insert([
            'template_code' => $templateCode,
            'mb_hp'         => $mbHp,
            'message'       => $message,
        ]);
    }

    /**
     * 이미 발송된 메신저 체크
     */
    public static function sendCheck($templateCode, $mbHp): int
    {
        return self::where('template_code', $templateCode)
            ->where('mb_hp', $mbHp)
            ->count();
    }

    /**
     * 주문취소 발송 여부 체크 (JSON od_id 기준)
     */
    public static function orderCancelCheck($templateCode, $mbHp, $jsonKey): int
    {
        return self::where('template_code', $templateCode)
            ->where('mb_hp', $mbHp)
            ->whereRaw(
                "JSON_EXTRACT(message, '$.od_id') = ?",
                [$jsonKey]
            )
            ->count();
    }

    /**
     * ERP 관리자페이지 알림 발송 조회
     */
    public static function findKakaoLog($templateCode, $jsonKey)
    {
        return self::selectRaw("
                COUNT(*) as cnt,
                REPLACE(JSON_EXTRACT(message, '$.mb_id'), '\"', '') AS mb_id
            ")
            ->where('template_code', $templateCode)
            ->whereRaw(
                "JSON_EXTRACT(message, '$.idx') = ?",
                [$jsonKey]
            )
            ->first();
    }
}