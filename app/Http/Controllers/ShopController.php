<?php

namespace App\Http\Controllers;

use App\Models\ShopCart;
use App\Services\ShopCartService;
use App\Services\MallMainServices;
use App\Services\MallShopService;
use App\Services\ShopItemService;
use App\Models\ShopItem;
use App\Models\TbRecipe;
use App\Models\TbRecipeCategory;
use App\Traits\RedisTrait;
use App\Traits\CommonTrait;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Validation\Rule;
use App\Facades\Sso;
use App\Services\BannerService;
use App\Services\RecipeService;
use Debugbar;
use Exception;
use Illuminate\Support\Facades\Redirect;
use Symfony\Component\CssSelector\Node\FunctionNode;
use Illuminate\Support\Facades\Log;

class ShopController extends Controller
{
    use RedisTrait, CommonTrait;

    public function ShopList(Request $request)
    {
        $request->validate([
                'ca_id' => 'required'
            ],
            [
                'ca_id.required' => '비정상 접근 입니다.',
                // 'ca_id.digits_between' => '옳바른 데이터 형식이 아닙니다.'
            ]
        );
        

        $service = app(ShopCartService::class);
        $mall    = app(MallMainServices::class);
        $shop    = app(MallShopService::class);

        $items = $service->shopItemList($request);
        
        $redis_key_generate = 'mall:items:'.$request->get('ca_id', '20');

        if (Redis::exists($redis_key_generate)) {
            $other = self::getReids($redis_key_generate);
            $items->other = $other;
            
        } else {
            // 배너생성
            $banner = $mall->getBanner('renew_item_page', 1);
            $other['banner']   = $mall->makeBannerDiv($banner, 'N');
            $items->other = $other;

            self::setRedis($redis_key_generate, $other);
        }

        return view('mall.shop.list', compact('items'));
        
    }



    /**
     * method Name : ShopItemEquipList
     * Description : 장비구매
     * Created Date : 2026-02-06
     * Params : Params
     * History :
     *   - 2026-02-06 : Initial creation
     */
    public function ShopItemEquipList(Request $request)
    {
        $allows = [
            'mall/shop/list_gubun_equipment' => [
                'ca_id' => 'd0',
                'view'  => 'mall.shop.list_gubun_equipment',
            ],
            'mall/shop/list_gubun_accessory' => [
                'ca_id' => 'g0',
                'view'  => 'mall.shop.list',
            ],
        ];

        if (!isset($allows[$request->path()])) {
            return redirect()->back()->with('error', '존재하지 않는 요청입니다.');
        }

        $items = app(ShopCartService::class)->shopItemEquipList(
            $request->merge([
                'ca_id' => $allows[$request->path()]['ca_id']
            ])
        );

        $items->other = ['banner' => ''];

        return view($allows[$request->path()]['view'], compact('items'));
    }
    


    public function ShopDetail(Request $request)
    {
        $request->validate([
            'it_id' => [
                'required',
                Rule::exists('g5_shop_item', 'it_id')
                    ->where(function ($query) {
                        $query->where('it_use', '!=', 0);
                    }),
            ],
        ], [
            'it_id.exists' => '현재 판매가능한 상품이 아닙니다.',
            'it_id.required' => '상품코드가 옳바르지 않습니다.',
        ]);

        $service = app(ShopCartService::class);

        $items = $service->itemDetail($request->input('it_id'));


        if (!$items) {
            throw new Exception('존재하지 않는 상품 입니다.');
        }

        $items = $items->toArray();

        $recommended_products = ShopItem::getRelationItems($request->merge(['not_it_id' => $items['it_id']]));

        $related_products = ShopItem::getRelationItems($request->merge(['ca_id2' => $items['ca_id2']]));

            
        return view('mall.shop.view', [
            'items' => $items,
            'recommended_products' => $recommended_products,
            'related_products' => $related_products,
        ]);
    }



    /**
     * method Name : ShopListAll
     * Description : 싱픔감섹
     * Created Date : 2026-01-17
     * Params : Params
     * History :
     *   - 2026-01-17 : Initial creation
     */
    public function ShopListAll(Request $request)
    {
        $perPage = $request->get('scale', 60);

        $request->validate([
            'skeyword' => 'required'
        ], [
            'skeyword.required' => '검색 키워드를 입력하세요.'
        ]);

        $it_name = $request->input('skeyword');

        $service = app(MallMainServices::class);

        $it_price = 'it_price';
        if (session('ss_mb_code')) {
            $member = app(MallShopService::class)->getMemberInfo(session('ss_mb_code'));
            $it_price = $member['field_it_price'];
        }
        
        // 판매량순 구하기
        $saleJoin = '';
        if ($request->filled('desc') && $request->desc == '4') {
            $saleJoin = ShopCart::selectRaw('COUNT(*) as cnt, it_id')->where('ct_status', '완료')->groupBy('it_id');
        }

        $shopGroup = ShopItem::selectRaw('it_qty_system_stock AS cnt, stock_it_id')
        ->groupBy('stock_it_id');

        $items = ShopItem::stockLeftJoinSub()
        ->when($request->filled('desc') && $request->desc == '4' && $saleJoin, function($query) use ($saleJoin) {
            $query->leftJoinSub($saleJoin, 'shop_sales', function($join) {
                $join->on('g5_shop_item.it_id', '=', 'shop_sales.it_id');
            });
        })
        ->select(
            app(ShopItemService::class)::selected()
        )
        ->when($it_name, function($query) use ($it_name) {
            $query->where('g5_shop_item.it_name', 'LIKE', '%' . $it_name . '%');
        })
        ->when($request->filled('desc'), function($query) use ($request, $it_price) {
            if ($request->desc == '2' && $it_price) {
                $query->orderByRaw("g5_shop_item.$it_price * g5_shop_item.it_buy_min_qty ASC");
            }
            if ($request->desc == '3' && $it_price) {
                $query->orderByRaw("g5_shop_item.$it_price * g5_shop_item.it_buy_min_qty DESC");
            }
            if ($request->desc == '4') {
                $query->orderBy('shop_sales.cnt', 'DESC');
            }
            if ($request->desc == '5') {
                $query->orderBy('g5_shop_item.it_insert_time', 'DESC');
            }
        })
        ->whereNotIn('it_id_type', [4, 6, 8])
        ->where('g5_shop_item.it_use', '1')
        ->paginate($perPage)->withQueryString();


        $banner = $service->getBanner('renew_item_page', 1);
        $other['banner']   = $service->makeBannerDiv($banner, 'N');
        $other['skeyword'] = $it_name;
        $items->other = $other;


        return view('mall.shop.list_all', compact('items'));
    }



    /**
     * method Name : ShopCookList
     * Description : 비바쿡 상품목록
     * Created Date : 2026-01-17
     * Params : Params
     * History :
     *   - 2026-01-17 : Initial creation
     */
    public function ShopCookList(Request $request)
    {
        $request->validate([
            'type' => 'required|in:mygrang,vivacook,hit,recom,new,best,sale,free,auction,vivacook,etc'
        ], [
            'type.required' => 'type 값이 필요합니다.',
            'type.in' => '옳바른 type 값이 아닙니다.'
        ]);

        if ($request->has('ca_id')) {
            $request->merge([
                'category' => $request->input('ca_id'), 
                'ca_id' => $request->input('type')
            ]);
        } else {
            $request->merge(['ca_id' => $request->input('type')]);
        }

        $service = app(ShopCartService::class);
        $mall    = app(MallMainServices::class);
        $shop    = app(MallShopService::class);

        $items = $service->shopItemList($request);
        
        $redis_key_generate = 'mall:items:'.$request->input('type');

        if (Redis::exists($redis_key_generate)) {
            $other = self::getReids($redis_key_generate);
            $items->other = $other;
            
        } else {
            // 배너생성
            $banner = $mall->getBanner('renew_item_page', 1);
            $other['banner']   = $mall->makeBannerDiv($banner, 'N');

            $items->other = $other;
            self::setRedis($redis_key_generate, $other);
        }

        return view('mall.shop.list_gubun', compact('items'));
    }



    /**
     * method Name : ShopRecipeList
     * Description : 레시피 리스트
     * Created Date : 2026-01-18
     * Params : Request $request
     * History :
     *   - 2026-01-18 : Initial creation
     */
    public function ShopRecipeList(Request $request)
    {
        $page    = $request->get('page', 1);
        $desc    = $request->get('desc', '');
        $perPage = $request->get('scale', 60);

        $service = app(MallShopService::class);
        $mb_code = session('ss_mb_code');

        if (!$mb_code) {
            return redirect('/')->with('error', '비회원은 이용할 수 없습니다.');
        }

        $member = $service->getMemberInfo($mb_code);

        $recipe_auth_opt = [
            '10', '20', '30', '40', '50', '60', '70', '80', 
            '90', 'a0', 'b0', 'c0', 'd0', 'e0', 'f0', 'g0'
        ];

        if( $member['level_ca_id'] != '70' && 
            $member['level_ca_id'] != '80' && 
            $member['level_ca_id'] != '90' && 
            $member['mb_gubun_type'] != "employee" && 
            !in_array($member['mb_launching'], $recipe_auth_opt)
        ) {
            return redirect('/')->with('error', '레시피보기 권한이 없습니다.');
        }

        if(substr($member['mb_level'],0,2) >= '70' && substr($member['mb_level'],0,2) <= '90' || $member['mb_gubun_type'] == "employee") {
            if($request->input('opt')) {
                $recipe_opt = $request->input('opt');
            } else {
                $recipe_opt = "10";
            }
        } else {
            $recipe_opt = $member['mb_launching'];
        }

        $idxList = TbRecipe::getRecipeItems($request, $recipe_opt);
        $total = count($idxList);

        $offset = ($page - 1) * $perPage;
        $pagedIdx = array_slice($idxList , $offset, $perPage);

        if ($total == 0) {
            $items = new LengthAwarePaginator([], $total, $perPage, $page, [
                'path'  => Paginator::resolveCurrentPath(), 
                'query' => $request->query(),  
            ]);
        }

        $orderField = implode(',', array_map(function($i) {
            return "'$i'";
        }, $pagedIdx));


        $result = TbRecipe::select([
            'id', 
            'opt', 
            'opt2', 
            'p_id', 
            'title', 
            'tag', 
            'img1', 
            'chios_img1', 
            'chios_img2', 
            'chios_img3'
        ])
        ->whereIn('id', $pagedIdx)
        ->when($recipe_opt, function($query) use ($recipe_opt) {
            $query->where('opt', $recipe_opt);
        })
        ->when($request->filled('opt2'), function($query) use ($request) {
            $query->where('opt2', $request->input('opt2'));
        })
        ->when($request->filled('opt3'), function($query) use ($request) {
            $query->where('opt3', $request->input('opt3'));
        })
        ->when($request->filled('best_state') && $request->input('bast_state') == 'y', function($query) {
            $query->where('best_state', 'y');
        })
        ->when($request->filled('recipe_skeyword'), function($query) use ($request) {
            $query->where('title', 'LIKE', '%'.$request->recipe_skeyword.'%');
        })
        ->when($orderField, function($query) use ($orderField, $desc) {
            if ($desc == '2') {
                $query->orderBy('regdate', 'desc');
            } else if ($desc == '3') {
                $query->orderBy('view_count', 'desc');
            } else {
                $query->orderByRaw("FIELD(id, $orderField)");
            }
            
        })
        ->get();

        $items = new LengthAwarePaginator(
            $result,
            $total,
            $perPage,
            $page,
            [
                'path'  => Paginator::resolveCurrentPath(),
                'query' => $request->query(),
            ]
        );

        // $redis_key_generate     = 'recipe:category';
        // $redis_key_generate_sub = 'recipe:sub_category';

        // if (Redis::exists($redis_key_generate) || Redis::exists($redis_key_generate_sub)) {
        //     $category = self::getReids($redis_key_generate);
        //     $sub_category = self::getReids($redis_key_generate_sub);
        // } else {
        //     // $category     = TbRecipeCategory::getRecipeCategory()->toArray();
        //     // $sub_category = TbRecipeCategory::getSubRecipeCategory()->toArray();

        //     $category = app(RecipeService::class)->getRecipeCategory($member, $request);
        //     $sub_category = app(RecipeService::class)->getSubRecipeCategory($member, $request);            

        //     self::setRedis($redis_key_generate, $category);
        //     self::setRedis($redis_key_generate_sub, $sub_category);
        // }


        $category = app(RecipeService::class)->getRecipeCategory($member, $request);
        $sub_category = app(RecipeService::class)->getSubRecipeCategory($member, $request);                    

        $data = [];
        foreach ($category as $row) {

            $item = [];
            $item['category'] = $row;

            foreach ($sub_category as $sRow) {
                if (
                    substr($sRow['ca_id'], 0, 2) === substr($row['ca_id'], 0, 2) &&
                    substr($sRow['ca_id'], 0, 4) === substr($row['ca_id'], 0, 4)
                ) {
                    $item['category']['sub_category'][] = $sRow;
                }
            }

            $data[] = $item;
        }

        $subBanner = app(BannerService::class)->getRecipeSubBanner($member, $request);

        return view('mall.shop.list_gubun_recipe', [
            'items' => $items, 
            'category' => $data, 
            'sub_category' => $sub_category, 
            'activeSubBanner' => $subBanner
        ]);
    }



    /**
     * method Name : ShopRecipeView
     * Description : 레시피 상세보기
     * Created Date : 2026-01-18
     * Params : Params
     * History :
     *   - 2026-01-18 : Initial creation
     */
    public function ShopRecipeView(Request $request)
    {
        $p_id = $request->input('p_id', '');

        $request->validate([
            'p_id' => 'required'
        ], [
            'p_id.required' => '필수 파라미터가 누락되었습니다.',
            
        ]);

        $select = [
            'id',
            'p_id',
            'title',
            'tag',
            'etc',
            'etc_sub',
            'contents',
            'cost',
            'price',
            'profit_rate',
        ];

        $etc = $choice = [];
        for ($i = 1; $i < 21; $i++) {
            $etc[] = 'etc'.$i;

            if ($i < 11) {
                $choice[] = 'img'.$i;
                $choice[] = 'chios_img'.$i;
                $choice[] = 'cook_img'.$i;
                $choice[] = 'cook_img'.$i.'_chk';
            }
        }

        $select = array_merge($select, $choice);
        $select = array_merge($select, $etc);


        $items = TbRecipe::select($select)->where('p_id', $p_id)->whereIn('state', ['1', '2'])->first();

        if (!$items) {
            return redirect()->route('list_gubun_recipe')->with('error', '존재하지 않는 상품입니다.');
        }

        if ($items->id) {
            $shopGroup = ShopItem::selectRaw('it_qty_system_stock AS cnt, stock_it_id')
            ->groupBy('stock_it_id');

            $relation = DB::table('tb_tmp_choice_relation_product')
            ->join('g5_shop_item', 'tb_tmp_choice_relation_product.it_id', 'g5_shop_item.it_id')
            ->leftJoinSub($shopGroup, 'shop_group', function($join){
                $join->on('g5_shop_item.stock_it_id', '=', 'shop_group.stock_it_id');
            })
            ->leftjoin('g5_shop_item_cust_log', function($join) {
                $join->on('g5_shop_item.it_id', '=', 'g5_shop_item_cust_log.it_id')
                ->where('g5_shop_item_cust_log.it_cust_rate_chk', '1');
            })
            ->where('tb_tmp_choice_relation_product.t_gubun', 'recipe_relation')
            ->where('tb_tmp_choice_relation_product.t_no', $items->id)
            ->where('t_state', '2')
            ->whereIn('t_chk', ['', '1'])
            ->select(
                app(ShopItemService::class)::selected()
            )
            ->orderBy('t_rank_order', 'asc')
            ->orderBy('tb_tmp_choice_relation_product.idx', 'desc')
            ->get();
        }


        return view('mall.shop.list_gubun_recipe_view', [
            'items' => $items,
            'relation' => $relation
        ]);
    }




    /**
     * method Name : ShopRecipePop
     * Description : 레시피프린트 팝업
     * Created Date : 2026-01-19
     * Params : Params
     * History :
     *   - 2026-01-19 : Initial creation
     */
    public function ShopRecipePop(Request $request)
    {
        $p_id = $request->input('print_select_idx', '');

        $request->validate([
            'print_select_idx' => 'required'
        ], [
            'print_select_idx.required' => '필수 파라미터가 누락되었습니다.',
            
        ]);

        $select = [
            'id',
            'title',
            'tag',
            'etc',
            'etc_sub',
            'contents',
            'cost',
            'price',
            'profit_rate',
        ];

        $etc = $choice = [];
        for ($i = 1; $i < 21; $i++) {
            $etc[] = 'etc'.$i;

            if ($i < 11) {
                $choice[] = 'img'.$i;
                $choice[] = 'chios_img'.$i;
                $choice[] = 'cook_img'.$i;
                $choice[] = 'cook_img'.$i.'_chk';
            }
        }

        $select = array_merge($select, $choice);
        $select = array_merge($select, $etc);
        $pidArr = explode(',', $p_id);

        $items = TbRecipe::select($select)->whereIn('p_id', $pidArr)->whereIn('state', ['1', '2'])->get();

        return view('mall.shop.list_gubun_recipe_pop', compact('items'));
    }

    


    // 체인점 전용 상품
    public function ShopChainList(Request $request)
    {
        $page    = $request->get('page', 1);
        $desc    = $request->get('desc', '');
        $perPage = $request->get('scale', 60);

        $service = app(MallShopService::class);
        $member  = $service->getMemberInfo(session('ss_mb_code'));


        if($member['chain_ca_id2'] && $member['chain_ca_id2'] != '1001') {
            $chain_ca_id2 = $member['chain_ca_id2'];
        } else {
            $chain_ca_id2 = '';
        };

        
        $it_price = 'it_price';
        if (session('ss_mb_code')) {
            $member = app(MallShopService::class)->getMemberInfo(session('ss_mb_code'));
            $it_price = $member['field_it_price'];
        }


        // 정렬 : 판매기준
        $saleJoin = '';
        if ($request->filled('desc') && $request->desc == '4') {
            $saleJoin = ShopCart::selectRaw('COUNT(*) as cnt, it_id')->where('ct_status', '완료')->groupBy('it_id');
        }

        $items = ShopItem::stockLeftJoinSub()
        ->when($request->filled('desc') && $request->desc == '4' && $saleJoin, function($query) use ($saleJoin) {
            $query->leftJoinSub($saleJoin, 'shop_sales', function($join) {
                $join->on('g5_shop_item.it_id', '=', 'shop_sales.it_id');
            });
        })
        ->when($chain_ca_id2, function($query) use ($chain_ca_id2) {
            $query->where('it_multi_chain_cate_code', 'LIKE', '%'.$chain_ca_id2.'%');
        }, function($query) {
            $query->whereRaw('0 = 1');
        })
        ->select(
            app(ShopItemService::class)::selected()
        )
        ->when($request->filled('desc'), function($query) use ($request, $it_price) {
            if ($request->desc == '2' && $it_price) {
                $query->orderByRaw("g5_shop_item.$it_price * g5_shop_item.it_buy_min_qty ASC");
            }
            if ($request->desc == '3' && $it_price) {
                $query->orderByRaw("g5_shop_item.$it_price * g5_shop_item.it_buy_min_qty DESC");
            }
            if ($request->desc == '4') {
                $query->orderBy('shop_sales.cnt', 'DESC');
            }
            if ($request->desc == '5') {
                $query->orderBy('g5_shop_item.it_insert_time', 'DESC');
            }
        })
        ->paginate($perPage);

        return view('mall.shop.list_gubun_chain', compact('items'));
    }



    // 간편주문 
    public function ShopItemSimple(Request $request)
    {
        if (!session('ss_mb_code')) {
            throw new Exception('로그인 후 이용 가능합니다.');
        }

        $page = $request->input('page', '1');
        $desc = $request->input('desc', '');
        $perPage = $request->input('scale', '60');

        $shopGroup = ShopItem::selectRaw('it_qty_system_stock AS cnt, stock_it_id')
        ->groupBy('stock_it_id');

        $items = ShopCart::leftjoin('g5_shop_item', 'g5_shop_cart.it_id', '=', 'g5_shop_item.it_id')
        ->leftJoinSub($shopGroup, 'shop_group', function($join) {
            $join->on('g5_shop_item.stock_it_id', '=', 'shop_group.stock_it_id');
        })
        ->leftjoin('g5_shop_item_cust_log', function($join) {
            $join->on('g5_shop_item.it_id', '=', 'g5_shop_item_cust_log.it_id')
            ->where('g5_shop_item_cust_log.it_cust_rate_chk', '1');
        })
        ->select(
            app(ShopItemService::class)::selected()
        )
        ->where('g5_shop_cart.mb_code', session('ss_mb_code'))
        ->where('g5_shop_cart.ct_status', '!=', '쇼핑')
        ->where('g5_shop_cart.ct_imsi', 'n')
        ->where('g5_shop_item.it_use', '1')
        ->where('g5_shop_item.ca_id', '!=', '10')
        ->where(function ($query) {
            $query->whereIn('g5_shop_cart.ct_status', ['배송', '완료'])
                  ->orWhereIn('g5_shop_cart.ct_cate', ['납품']);
        })
        ->groupBy('g5_shop_cart.it_id')
        ->when($desc, function($query) use ($desc){
            if ($desc == '3') {
                $query->orderBy('g5_shop_item.it_sum_qty', 'desc');
            }
            if ($desc == '5') {
                $query->orderBy('g5_shop_item.it_order', 'asc')
                ->orderBy('g5_shop_item.it_time', 'desc')
                ->orderBy('g5_shop_item.idx', 'desc');
            }
            if ($desc == '6') {
                $query->orderBy('g5_shop_item.it_time', 'asc');
            }
        }, function($query) {
            $query->orderBy('g5_shop_item.it_order', 'asc')
                ->orderBy('g5_shop_item.it_time', 'desc')
                ->orderBy('g5_shop_item.idx', 'desc');
        })
        ->paginate($perPage);

        
        $redis_key_generate = 'mall:items:20';
        if (Redis::exists($redis_key_generate)) {
            $banner = self::getReids($redis_key_generate);
        } else {
            $mall   = app(MallMainServices::class);
            $banner = $mall->getBanner('renew_item_page', 1);
            $banner['banner'] = $mall->makeBannerDiv($banner, 'N');

            self::setRedis($redis_key_generate, $banner);
        }


        return view('mall.shop.list_gubun_simple', [
            'items' => $items, 'banner' => $banner['banner']
        ]);
    }


    // 카트구매
    public function cartupdate(Request $request)
    {
        $messages = [
            'it_id.required' => '상품 ID 배열은 필수입니다.',
            'it_id.array' => '상품 ID는 배열이어야 합니다.',
            'it_id.min' => '최소 :min개의 상품 ID가 필요합니다.',
            'it_id.*.required' => '각 상품 ID는 필수입니다.',
            'it_id.*.string' => '각 상품 ID는 문자열이어야 합니다.',
        ];

        $request->validate([
            'it_id' => 'required|array|min:1', 
            'it_id.*' => 'required|string', 
        ], $messages);

        $it_ids = $request->input('it_id');

        $od_id = ShopCart::where('it_id', $it_ids[0])->where('mb_code', session('ss_mb_code'))
        ->where('ct_status', '쇼핑')->value('od_id');

        if (!$od_id) {
            return redirect()->route('/')->with('error', '주문번호가 존재하지 않습니다. 상품 삭제후 다시 시도 해주세요.');
        }

        // $hash_key = $this->encrypted($od_id);

        // if (!Sso::setChareItem($hash_key, $request->all())) {
        //     throw new Exception('죄송합니다. 상품 구매 신청이 실패되었습니다. 잠시 후 다시 시도해 주세요.');
        // }


        // return view('mall.shop.cart_update', ['od_id' => $od_id]);


        //품절상품 자동삭제
        $soldOut = DB::table('g5_shop_cart as sc')
            ->leftJoin('g5_shop_item as si', 'sc.it_id', '=', 'si.it_id')
            ->where('sc.mb_code', session('ss_mb_code'))
            ->where('sc.od_id', $od_id)
            ->where(function ($q) {
                $q->where('si.it_soldout', 1)
                ->orWhere('si.it_force_soldout', 10);
            })
            ->pluck('sc.ct_id');

        DB::table('g5_shop_cart')
            ->whereIn('ct_id', $soldOut)
            ->delete();        

        return redirect()->route('payment.request', ['od_id' => $od_id, 'order_type' => 'items']);
    }
}
