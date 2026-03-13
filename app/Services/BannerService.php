<?php
/**
 * Class Name : BannerService
 * Description : 배너팝업 서비스
 * Author : Lee Sangseung
 * Created Date : 2026-01-14
 * Version : 1.0
 * 
 * History :
 *   - 2026-01-14 : Initial creation
 */

namespace App\Services;

use App\Models\ShopCategoryModel;
use App\Models\TbMember;
use App\Models\TbNewWin;
use Carbon\Carbon;
use Debugbar;
use App\Traits\RedisTrait;
use App\Services\MallMainServices;
use App\Services\MallShopService;
use App\Models\TbChainCategoryModel;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BannerService
{
    use RedisTrait;

    // 레이아웃용 활성 배너 조회
    public function getActiveBanners()
    {
        // 
        $redis_member_key_generate = 'main_banner';

        if (Redis::exists($redis_member_key_generate)) {
            
            $resArr = json_decode(Redis::get($redis_member_key_generate), true);

        } else {
            $now = Carbon::now(); // 현재 시간, config/app.php timezone 기준
            $columns = Schema::getColumnListing('tb_new_win'); // 모든 컬럼 가져오기
            $columns = array_diff($columns, [
                'nw_division', 
                'nw_device',
                'nw_gubun_code',
                'nw_gubun',
                'nw_gubun2_code',
                'nw_gubun2',
                'nw_begin_time',
                'nw_end_time',
                'nw_open_name',
                'nw_type',
                'state',
            ]); // 제외할 컬럼 제거

            $pc_result = TbNewWin::where('nw_begin_time', '<=', $now)
                        ->where('nw_end_time', '>=', $now)
                        ->whereIn('nw_division', ['both'])
                        ->whereIn('nw_device', ['both', 'pc'])
                        ->whereIn('state', ['2'])
                        ->select($columns)
                        ->orderBy('rank_order', 'asc')
                        ->orderBy('nw_id', 'asc')
                        ->get()->toArray();

            $mobile_result = TbNewWin::where('nw_begin_time', '<=', $now)
                        ->where('nw_end_time', '>=', $now)
                        ->whereIn('nw_division', ['both'])
                        ->whereIn('nw_device', ['both', 'mobile'])
                        ->whereIn('state', ['2'])
                        ->select($columns)
                        ->orderBy('rank_order', 'asc')
                        ->orderBy('nw_id', 'asc')
                        ->get()->toArray();

            $resArr = [
                'pc'        => $pc_result,
                'mobile'    => $mobile_result,
            ];

            $ttl = 24 * 60 * 60;

            Redis::set($redis_member_key_generate, json_encode($resArr, JSON_UNESCAPED_UNICODE), 'EX', $ttl);
        }

        

        return $resArr;
    }


    public function getActiveMenuBanner()
    {
        $service = app(MallMainServices::class);
        $member  = app(MallShopService::class)->getMemberInfo(session('ss_mb_code'));

        $redis_key_generate = 'menu_banner';

        $menu_banners = [];
        
        if (Redis::exists($redis_key_generate)) {
            
            $menu_banners = $this->getReids($redis_key_generate);

        } else {
            
            $menu_banners['all'] = $service->getBannerFactory(
                $service->getBanner('renew_menu_right', 1),
                'grid-item grid-item--width1,grid-item grid-item--width3', 
                'renew_menu_right'
            );
    
            $menu_banners['renew_menu_low_temp'] = $service->getBannerFactory(
                $service->getBanner('renew_menu_low_temp', 1),
                'grid-item',
                'renew_menu_low_temp'
            );
    
            $menu_banners['renew_menu_vivacook'] = $service->getBannerFactory(
                $service->getBanner('renew_menu_vivacook', 1),
                'grid-item',
                'renew_menu_vivacook'
            );
    
            $menu_banners['renew_menu_mygrang'] = $service->getBannerFactory(
                $service->getBanner('renew_menu_mygrang', 1),
                'grid-item',
                'renew_menu_mygrang'
            );


            $menu_banners['renew_menu_best'] = $service->getBannerFactory(
                $service->getBanner('renew_menu_best', 1),
                'grid-item',
                'renew_menu_best'
            );


            $menu_banners['renew_menu_new'] = $service->getBannerFactory(
                $service->getBanner('renew_menu_new', 1),
                'grid-item',
                'renew_menu_new'
            );


            $menu_banners['renew_menu_event'] = $service->getBannerFactory(
                $service->getBanner('renew_menu_event', 1),
                'grid-item',
                'renew_menu_event'
            );
            
            $menu_banners['recipe_menu'] = $this->getRecipeSubCategory($member);


            $menu_banners['all_menu_sub_category'] = $this->getAllMenuSubCategory();


            $this->setRedis($redis_key_generate, $menu_banners);
        }



        $menu_banners['chain_menu'] = $this->getChainSubCategoryList($member);


        return $menu_banners;
    }



    public function getActiveLeftBanner()
    {
        $redis_key_generate = 'left_pc_banner';

        if (Redis::exists($redis_key_generate)) {

            $banners = self::getReids($redis_key_generate);

        } else {
            $banners = DB::table('tb_banner')->where('group_id', 'common_left')
                ->where('banner_display', 'Y')
                ->where('state', '2')
                ->select('banner_img1', 'banner_url', 'banner_display', 'state', 'rank_order', 'url_target')
                ->get()
                ->map(function($row) {
                    return (array) $row;
                });

            self::setRedis($redis_key_generate, $banners);
        }

        return $banners;
    }



    private function getChainSubCategoryList($member = [])
    {
        $categories = json_decode(
            json_encode(
                TbChainCategoryModel::getMenuBannerList()
            ),
            true
        );

        $chain_menu = [];

        if ($member) {
            foreach ($categories as $key => $row) {
                $img_root = '/images/common_data/chain_category/'.$row['ca_img1'];
    
                $thumb_chain_category = '<span><img src="'.$img_root.'" alt=""></span>';
    
                // 클릭 이벤트 결정
                if ($member['chain_ca_id2'] && $row['ca_id'] == $member['chain_ca_id2']) {
                    $chain_href = "location.href='/mall/shop/list_gubun_chain'";
                } else {
                    $chain_href = "alert('체인점 권한이 없습니다.')";
                }
    
                // 메뉴 HTML 추가
                $chain_menu[] = '<div class="c-card" onclick="'.htmlspecialchars($chain_href, ENT_QUOTES).'">
                    '.$thumb_chain_category.'
                    <p>'.$row['ca_name'].'</p>
                </div>';
            }
        }
        
        return $chain_menu;
    }



    private function getRecipeSubCategory($member = [])
    {
        $recipe_menu = [];

        if ($member) {
            if(substr($member['mb_level'],0,2) >= '_70_' && substr($member['mb_level'],0,2) <= '_90_' || $member['mb_gubun_type'] == "employee") {
                $recipe_opt = "10";
            } else {
                $recipe_opt = $member['mb_launching'];
            }

            $recipes = DB::table('tb_recipe as rec')
            ->join('tb_recipe_category as rec_ct', 'rec.opt2', '=', 'rec_ct.ca_id')
            ->select(
                'rec.id',
                'rec.opt',
                'rec.opt2',
                'rec.opt2_name',
                'rec_ct.ca_img1 as img1',
                DB::raw('MAX(rec.rank_order) as rank_order')
            )
            ->where('rec.state', 2)
            ->where(DB::raw('SUBSTRING(rec_ct.ca_id, 1, 2)'), '=', $recipe_opt)
            ->whereRaw('LENGTH(rec_ct.ca_id) = 4')
            ->where('rec_ct.ca_use', 1)
            ->groupBy('rec.opt2')
            ->get()
            ->map(function($item){
                return (array) $item;  // stdClass → 연관 배열
            })
            ->toArray();


            // $query = DB::table('tb_recipe as rec')
            // ->join('tb_recipe_category as rec_ct', 'rec.opt2', '=', 'rec_ct.ca_id')
            // ->select(
            //     'rec.id',
            //     'rec.opt',
            //     'rec.opt2',
            //     'rec.opt2_name',
            //     'rec_ct.ca_img1 as img1',
            //     DB::raw('MAX(rec.rank_order) as rank_order')
            // )
            // ->where('rec.state', 2)
            // ->where(DB::raw('SUBSTRING(rec_ct.ca_id, 1, 2)'), '=', $recipe_opt)
            // ->whereRaw('LENGTH(rec_ct.ca_id) = 4')
            // ->where('rec_ct.ca_use', 1)
            // ->groupBy('rec.opt2');

            // $sql = vsprintf(
            //     str_replace('?', '%s', $query->toSql()),
            //     $query->getBindings()
            // );
            

            // Log::info($sql);


            $chunkSize   = 6;
            $totalCount  = count($recipes);
            $range       = round($totalCount / $chunkSize);
            $loopCount   = $range * 2;


            /** 레시피 접근 허용 옵션 */
            $recipe_auth_opt = [
                '10','20','30','40','50','60','70','80','90',
                'a0','b0','c0','d0','e0','f0','g0'
            ];

            /** 회원 권한 체크 */
            $memberLevel = substr($member['mb_level'], 0, 2);

            $isAuthMember =
                (  $memberLevel >= '_70_' && $memberLevel <= '_90_')
                || $member['mb_gubun_type'] === 'employee'
                || in_array($member['mb_launching'], $recipe_auth_opt);

            for ($i = 0; $i < $loopCount; $i++) {
                $html  = '';
                $index = 0;

                /** 6개씩 자르기 */
                $sliceRecipes = array_slice($recipes, $i * $chunkSize, $chunkSize);

                foreach ($sliceRecipes as $row) {

                    /** 클래스 처리 */
                    $class = '';
                    if ($index === 0 || $index === 3) $class = 'radiusTop';
                    if ($index === 1 || $index === 4) $class = 'radiusL';

                    /** 링크 분기 */
                    if ($isAuthMember) {

                        $link = '/mall/shop/list_gubun_recipe?opt=' . $row['opt'] . '&opt2=' . $row['opt2'];

                        $html .= '
                            <div class="r-card">
                                <a href="' . $link . '">
                                    <img src="/images/common_data/recipe_category/' . $row['img1'] . '">
                                    <p>' . $row['opt2_name'] . '</p>
                                </a>
                            </div>';
                    } else {

                        $html .= '
                            <div class="r-card">
                                <a class="re-toggle h-bot-on" onclick="alert(\'레시피보기 권한이 없습니다.\');">
                                    <img src="/images/common_data/recipe_category/' . $row['img1'] . '">
                                    <p>' . $row['opt2_name'] . '</p>
                                </a>
                            </div>';
                    }

                    $index++;
                }

                $recipe_menu[] = $html;
            } 
        }


        return $recipe_menu;
    }




    private function getAllMenuSubCategory()
    {
        $result     = ShopCategoryModel::getMainCategoryList();
        $sub_result = ShopCategoryModel::getMainCategorySubList();

        $data = [];
        foreach ($result as $key => $row) {
            $subCategoryList = $this->getSubCategory($sub_result, $row['ca_id']);
            if (count($subCategoryList) > 0) {
                $data['PC'][$key] = $row;   // left
                $data['PC'][$key]['sub_category'] = $subCategoryList;
            } else {
                $data['PC'][$key] = $row;   // right
            }
        }

        foreach ($result as $key => $row) {
            $data['mobile'][$key] = $row;
        }
        
        $category = [];
        foreach ($data['PC'] as $key => $row) {
            $html = '<dl><dt><a class="ul-item" href="/mall/shop/list?ca_id='.$row['ca_id'].'">'.$row['ca_name'].'</a></dt>';
            
            if (isset($row['sub_category'])) {
                foreach ($row['sub_category'] as $skey => $srow) {
                    $html .= '<dd><a href="/mall/shop/list?ca_id='.$srow['ca_id'].'">'.$srow['ca_name'].'</a></dd>';
                }
            }
            $html .= '</dl>';

            $category['PC'][] = $html;
        }

        
        return $category;
    }


    private function getSubCategory(array $data, $ca_id) : array
    {
        $subData = [];
        foreach ($data as $key => $row) {
            $sub_ca_id = substr($row['ca_id'], 0, 2);
            if ($sub_ca_id == $ca_id) {
                $subData[] = $row;
            }
        }

        return $subData;
    }



    public function getRecipeSubBanner($member)
    {
        $data = ['pc' => '', 'mobile' => ''];


        if ($member) {
            if(substr($member['mb_level'],0,2) >= '70' && substr($member['mb_level'],0,2) <= '90' || $member['mb_launching'] == '10') { // 에스엠
                $banner_opt = "10";
            } else if($member['mb_launching'] == '20') { // 비바쿡
                $banner_opt = "20";
            } else if($member['mb_launching'] == '30') { // 카르포스
                $banner_opt = "30";
            } else {
                $banner_opt = "";
            }
    
            
            $res = DB::table('tb_banner')
            ->where('group_id', 'shop_recipe')
            ->where('banner_display', 'Y')
            ->where('state', '2')
            ->whereRaw('date_end >= now()')
            ->when($banner_opt, function($query) use ($banner_opt) {
                $query->where('ca_id', $banner_opt);
            })
            ->select('idx', 'banner_url', 'link_type', 'view_count', 'banner_name', 'banner_img1', 'ca_id')
            ->orderBy('rank_order', 'asc')
            ->limit(1)
            ->get();
    
            foreach ($res as $key => $row) {
                $data['pc'] = '<img src="'.asset('images/common_data/banner/'.$row->banner_img1).'" alt="'.$row->banner_name.'" >';
            }


            $res = DB::table('tb_banner')
            ->where('group_id', 'm_shop_recipe')
            ->where('banner_display', 'Y')
            ->where('state', '2')
            ->whereRaw('date_end >= now()')
            ->when($banner_opt, function($query) use ($banner_opt) {
                $query->where('ca_id', $banner_opt);
            })
            ->select('idx', 'banner_url', 'link_type', 'view_count', 'banner_name', 'banner_img1', 'ca_id')
            ->orderBy('rank_order', 'asc')
            ->limit(1)
            ->get();
    
            foreach ($res as $key => $row) {
                $data['mobile'] = '<img src="'.asset('images/common_data/banner/'.$row->banner_img1).'" alt="'.$row->banner_name.'" >';
            }
        }

        return $data;
    }



    public function getChargeSubBanner()
    {
        $data = ['pc' => '', 'mobile' => ''];

        $res = DB::table('tb_banner')
        ->where('group_id', 'charge_amount')
        ->where('banner_display', 'Y')
        ->where('state', '2')
        ->whereRaw('date_end >= now()')
        ->select('idx', 'banner_url', 'link_type', 'view_count', 'banner_name', 'banner_img1', 'ca_id')
        ->orderBy('rank_order', 'asc')
        ->limit(1)
        ->get();

        foreach ($res as $key => $row) {
            $data['pc'] = '<img src="'.asset('images/common_data/banner/'.$row->banner_img1).'" alt="'.$row->banner_name.'" >';
        }


        $res = DB::table('tb_banner')
        ->where('group_id', 'm_charge_amount')
        ->where('banner_display', 'Y')
        ->where('state', '2')
        ->whereRaw('date_end >= now()')
        ->select('idx', 'banner_url', 'link_type', 'view_count', 'banner_name', 'banner_img1', 'ca_id')
        ->orderBy('rank_order', 'asc')
        ->limit(1)
        ->get();

        foreach ($res as $key => $row) {
            $data['mobile'] = '<img src="'.asset('images/common_data/banner/'.$row->banner_img1).'" alt="'.$row->banner_name.'" >';
        }

        return $data;
    }



    public function getMobileLeftSlideMenuFooterBanner()
    {
        $service = app(MallMainServices::class);

        $redis_key_generate = 'mobile_slide_menu_footer_banner';

        if (Redis::exists($redis_key_generate)) {
            $mobileFooterBanner = self::getReids($redis_key_generate);
        } else {
            $mobileFooterBanner = $service->makeBannerDiv(
                $service->getBanner('renew_m_category_bottom', '1'),
                'N'
            );

            self::setRedis($redis_key_generate, $mobileFooterBanner);
        }

        return $mobileFooterBanner;
    }
}
