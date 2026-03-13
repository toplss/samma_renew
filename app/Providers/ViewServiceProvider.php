<?php
/**
 * Class Name : ViewServiceProvider
 * Description : 전역 데이터 처리
 * Author : Lee Sangseung
 * Created Date : 2026-01-14
 * Version : 1.0
 * 
 * History :
 *   - 2026-01-14 : Initial creation
 */
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Http\Request;

use App\Services\BannerService;


class ViewServiceProvider extends ServiceProvider
{
    public function boot(Request $request): void
    {
        /*
        |--------------------------------------------------------------------------
        | 공통 데이터 싱글톤 등록 (요청당 1회만 실행)
        |--------------------------------------------------------------------------
        */

        $this->app->singleton('activeWins', function () {
            return app(\App\Services\BannerService::class)
                ->getActiveBanners();
        });

        $this->app->singleton('activeMenuBanner', function () {
            return app(\App\Services\BannerService::class)
                ->getActiveMenuBanner();
        });

        $this->app->singleton('activeLeftBanner', function () {
            return app(\App\Services\BannerService::class)
                ->getActiveLeftBanner();
        });

        $this->app->singleton('activeMobileFooterBanner', function () {
            return app(\App\Services\BannerService::class)
                ->getMobileLeftSlideMenuFooterBanner();
        });

        $this->app->singleton('activeCategory', function () {
            return app(\App\Services\MallShopService::class)
                ->getCategoryList();
        });

        $this->app->singleton('activeSiteInfo', function () {
            return app(\App\Services\MallShopService::class)
                ->siteInfo();
        });

        /*
        |--------------------------------------------------------------------------
        | 모든 View에 공유 (정적 데이터 → share 사용)
        |--------------------------------------------------------------------------
        */

        View::share([
            'activeWins' => app('activeWins'),
            // 'activeMenuBanner' => app('activeMenuBanner'),
            'activeLeftBanner' => app('activeLeftBanner'),
            'activeMobileFooterBanner' => app('activeMobileFooterBanner'),
            'activeCategory' => app('activeCategory'),
            'activeSiteInfo' => app('activeSiteInfo'),
        ]);
    }


    public function register(): void
    {
        // 서비스 컨테이너에 바인딩
        $this->app->bind(BannerService::class, function ($app) {
            return new BannerService();
        });
    }
}
