<?php

namespace App\Http\Controllers;

use App\Models\ShopCart;
use App\Models\TbBbsBody;
use App\Models\TbRecipe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use App\Services\MallMainServices;
use App\Services\MallShopService;
use App\Services\ShopCartService;
use App\Traits\RedisTrait;
use Illuminate\Support\Facades\Cookie;
use Debugbar;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MainController extends Controller
{
    use RedisTrait;


    public function index(Request $request)
    {
        $banner_service = new MallMainServices;

        // 배너관련 목록 start
        $main_top       = $banner_service->getBanner('renew_main_top', 1);
        $m_main_top     = $banner_service->getBanner('renew_m_main_top', 1);
        $main_middle    = $banner_service->getBanner('renew_main_middle', 1);
        $main_center    = $banner_service->getBanner('renew_main_middle_center', 1);
        $main_bottom    = $banner_service->getBanner('renew_main_bottom', 1);
        // 배너관련 목록 end

        $redis_member_key_generate = 'main_page';

        // Redis::del($redis_member_key_generate);

        if (Redis::exists($redis_member_key_generate)) {

            $resArr = self::getReids($redis_member_key_generate);

        } else {

            /******* 비바쿡 *******/
            $instagram = DB::connection('vivacook')->select('SELECT wr_id, wr_subject, wr_content FROM g5_write_snsinstagram WHERE 1 ORDER BY rand() limit 20');
            $vivacook = [];

            $img_root = 'https://vivacook.kr//data/file/snsinstagram';
            foreach ($instagram as $key => $row) {
                $file = DB::connection('vivacook')->table('g5_board_file')
                ->where('bo_table', 'snsinstagram')
                ->where('wr_id', $row->wr_id)
                ->select('bf_file')
                ->first();
                
                $vivacook[] = [
                    'wr_id' => $row->wr_id,
                    'wr_subject' => $row->wr_subject,
                    'wr_content' => $row->wr_content,
                    'file' => $img_root.'/'.$file->bf_file,
                ];
            }
            /******* 비바쿡 *******/

            $resArr = [
                'rank_order'  => ShopCart::rankOrderList(), # 자주 주문한 상품                
                'vivacook'    => $vivacook, # 베스트 리뷰,
                'popular_product' => app(ShopCartService::class)->shopItemList($request->merge(['ca_id' => 'hit', 'scale' => '50']))->items(), # 인기상품
                'event_product' => app(ShopCartService::class)->shopItemList($request->merge(['ca_id' => 'event', 'scale' => '50']))->items(), # 이달의행사                
            ];

            $resArr['MainSlideSuperSale_product'] = app(ShopCartService::class)->shopItemList($request->merge(['ca_id' => 'MainSlideSuperSale', 'scale' => '20']))->items(); # 메인 슬라이드 - 초특가상품
            $resArr['MainSlideVivacook_product'] = app(ShopCartService::class)->shopItemList($request->merge(['ca_id' => 'MainSlideVivacook', 'scale' => '20']))->items(); # 메인 슬라이드 - 비바쿡 할인상품
            $resArr['MainSlideMygrang_product'] = app(ShopCartService::class)->shopItemList($request->merge(['ca_id' => 'MainSlideMygrang', 'scale' => '20']))->items(); # 메인 슬라이드 - 마이그랑 할인상품
            $resArr['MainSlideBest_product'] = app(ShopCartService::class)->shopItemList($request->merge(['ca_id' => 'MainSlideBest', 'scale' => '20']))->items(); # 메인 슬라이드 - 지난주 베스트
            $resArr['MainSlideNew_product'] = app(ShopCartService::class)->shopItemList($request->merge(['ca_id' => 'MainSlideNew', 'scale' => '20']))->items(); # 메인 슬라이드 - 이번주 추천상품

            $resArr['main_top']    = $banner_service->makeBannerDiv($main_top, 'grid-item');
            $resArr['m_main_top']  = $banner_service->makeBannerDiv($m_main_top, 'grid-item');
            $resArr['main_middle'] = $banner_service->makeBannerDiv($main_middle, 'N');
            $resArr['main_center'] = $banner_service->makeBannerDiv($main_center, 'N');
            $resArr['main_bottom'] = $main_bottom;

            self::setRedis($redis_member_key_generate, $resArr);
        }


        $service = app(MallShopService::class);
        if (session()->has('ss_mb_code')) {
            $memberInfo = $service->getMemberInfo(session('ss_mb_code'));

            if(substr($memberInfo['mb_level'],0,2) >= '_70_' && substr($memberInfo['mb_level'],0,2) <= '_90_') {
                $recipe_opt = "10";
            } else {
                $recipe_opt = $memberInfo['mb_launching'];
            }

            $resArr['ss_hash'] = session('ss_hash');
            $resArr['recipe']  = TbRecipe::getRecipeBestProduct($recipe_opt);
        } else {
            $resArr['recipe']  = [];
            $resArr['ss_hash'] = '';
        }
        


        // ------- 인트로 관련 -------- //
        $redis_key_generate = 'intro';

        if (Redis::exists($redis_key_generate)) {

            $data = self::getReids($redis_key_generate);

        } else {
            $service = new MallMainServices;

            $left_banner 	= $service->getBanner('renew_intro', 1);
            $right_banner 	= $service->getBanner('renew_intro_right', 2);
            $right_bottom 	= $service->getBanner('renew_intro_bottom', 1);

            $data = [
                'left_banner'   => $service->makeBannerDiv($left_banner, 'N'),
                'right_banner'  => $service->makeBannerDiv($right_banner, 'grid-item grid-item--width1'),
                'right_bottom'  => $service->makeBannerDiv($right_bottom, 'N'),
            ];

            self::setRedis($redis_key_generate, $data);
        }
        // ------- 인트로 관련 -------- //


        if (session('ss_mb_code') || $request->path() === '/') {

            $resArr['duplicate'] = false;

            if (session('ss_mb_code')) {
                $exists = DB::table('login_duplicate_check')->where('mb_code', session('ss_mb_code'))->exists();

                if ($exists) {
                    $resArr['duplicate'] = true;
                }
            }

            $resArr['notice'] = TbBbsBody::getMainNoticeList();  # 공지사항

            return view('index', $resArr);
        } else {
            return view('intro', $data);
        }
    }

}
