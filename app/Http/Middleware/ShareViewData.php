<?php

namespace App\Http\Middleware;

use Closure;
use Debugbar;

class ShareViewData
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
        $member = [];
        $mypage = [];
        $hash   = null;

        if (session()->has('ss_mb_code')) {

            $mbCode = session('ss_mb_code');
            $mall   = app(\App\Services\MallShopService::class);

            $member = $mall->getMemberInfo($mbCode);
            $mypage = $mall->mypageTopInfo($request, $mbCode);
            $hash   = hash('sha256', $mbCode);
        }

        $banner = app(\App\Services\BannerService::class)->getActiveMenuBanner();

        view()->share([
            'activeMenuBanner' => $banner,
            'activeMember' => $member,
            'activeMyPageTop' => $mypage,
            'ss_hash' => $hash,
        ]);

        return $next($request);
    }
}
