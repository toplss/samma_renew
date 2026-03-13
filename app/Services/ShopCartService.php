<?php
/**
 * Class Name : ShopCartService
 * Description : 카트공통 서비스
 * Author : Lee Sangseung
 * Created Date : 2026-01-14
 * Version : 1.0
 * 
 * History :
 *   - 2026-01-14 : Initial creation
 */

namespace App\Services;

use App\Models\ShopCart;
use App\Models\ShopItem;
use Debugbar;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Log;

class ShopCartService
{
    /**
     * method Name : shopItemList
     * Description : 상품목록 조회
     * Created Date : 2026-01-14
     * Params : ca_id
     * History :
     *   - 2026-01-14 : Initial creation
     */
    public function shopItemList($request)
    {
        $page    = $request->get('page', 1);
        $perPage = $request->get('scale', 60);

        $it_price = 'it_price';
        if (session('ss_mb_code')) {
            $member = app(MallShopService::class)->getMemberInfo(session('ss_mb_code'));
            $it_price = $member['field_it_price'];
        }

        $idxList = ShopItem::getShopItems(
            $request
        );

        $total = count($idxList);

        $offset = ($page - 1) * $perPage;
        $pagedIdx = array_slice($idxList , $offset, $perPage);

        if ($total == 0) {
            return new LengthAwarePaginator([], $total, $perPage, $page, [
                'path'  => Paginator::resolveCurrentPath(), 
                'query' => $request->query(),  
            ]);
        }

        $orderField = implode(',', array_map(function($i) {
            return "'$i'";
        }, $pagedIdx));

        
        // 판매량순 구하기
        $saleJoin = '';
        if ($request->input('desc') == '4') {
            $saleJoin = ShopCart::selectRaw('COUNT(*) as cnt, it_id')->where('ct_status', '완료')->groupBy('it_id');
        }

        $shopGroup = ShopItem::selectRaw('it_qty_system_stock AS cnt, stock_it_id')
        ->groupBy('stock_it_id');

        $items = ShopItem::whereIn('idx', $pagedIdx)
        ->leftJoinSub($shopGroup, 'shop_group', function($join) {
            $join->on('g5_shop_item.stock_it_id', '=', 'shop_group.stock_it_id');
        })
        ->leftjoin('g5_shop_item_cust_log', function($join) {
            $join->on('g5_shop_item.it_id', '=', 'g5_shop_item_cust_log.it_id')
            ->where('g5_shop_item_cust_log.it_cust_rate_chk', '1');
        })
        ->when($request->input('desc') == 4, function($query) use ($saleJoin) {
            $query->leftJoinSub($saleJoin, 'shop_sales', function($join) {
                $join->on('g5_shop_item.it_id', '=', 'shop_sales.it_id');
            });
        })
        ->select(
            'g5_shop_item.idx',
            'g5_shop_item.it_id',
            'g5_shop_item.it_use',
            'g5_shop_item.it_name',
            'g5_shop_item.it_basic',
            'g5_shop_item.it_gubun',
            'g5_shop_item.it_qty_box', 
            'g5_shop_item.it_qty_pack', 
            'g5_shop_item.it_qty_pcs',
            DB::raw("
                CASE 
                    WHEN g5_shop_item.it_gubun = 'box' THEN '박스'
                    WHEN g5_shop_item.it_gubun = 'pack' THEN '팩'
                    WHEN g5_shop_item.it_gubun = 'pcs' THEN '낱개'
                END AS it_gubun_label
            "),
            'g5_shop_item.it_return',
            DB::raw("
                CASE 
                    WHEN g5_shop_item.it_return = '1' THEN '반품가능'
                    WHEN g5_shop_item.it_return = '2' THEN '반품불가'
                END AS it_return_label
            "),
            'g5_shop_item.it_soldout',
            'g5_shop_item.it_storage',
            DB::raw("
                CASE 
                    WHEN g5_shop_item.it_storage = '1' THEN '상온'
                    WHEN g5_shop_item.it_storage = '2' THEN '냉동'
                    WHEN g5_shop_item.it_storage = '3' THEN '냉장'
                END AS it_storage_label
            "),
            'g5_shop_item.stock_it_id',
            'g5_shop_item.it_special_explan',
            'g5_shop_item.it_force_soldout',
            'g5_shop_item.it_buy_min_qty',
            'g5_shop_item.it_buy_max_qty',
            'g5_shop_item.it_price_piece_use',
            'g5_shop_item.it_increase_type',
            'g5_shop_item.it_increase_date',
            'g5_shop_item.it_type3',
            'g5_shop_item.it_type4',
            'g5_shop_item.it_type5',
            'g5_shop_item.it_cust_rate',
            'g5_shop_item.it_cust_type',

            'g5_shop_item.it_box_sale_pcs',
            'g5_shop_item.it_box_sale_pack',
            'g5_shop_item.it_box_sale_tot',

            'g5_shop_item.it_price',
            'g5_shop_item.it_price_purchase',
            'g5_shop_item.it_price_piece',
            'g5_shop_item.it_price_piece_use',
            'g5_shop_item.it_price_box_unit',
            'g5_shop_item.it_price_pack_unit',
            'g5_shop_item.it_price_piece_unit',
            'g5_shop_item.it_price_unit',
            'g5_shop_item.it_price1',
            'g5_shop_item.it_price_rate1',
            'g5_shop_item.it_price_unit1',
            'g5_shop_item.it_price2',
            'g5_shop_item.it_price_rate2',
            'g5_shop_item.it_price_unit2',
            'g5_shop_item.it_price3',
            'g5_shop_item.it_price_rate3',
            'g5_shop_item.it_price_unit3',
            'g5_shop_item.it_price4',
            'g5_shop_item.it_price_rate4',
            'g5_shop_item.it_price_unit4',
            'g5_shop_item.it_price5',
            'g5_shop_item.it_price_rate5',
            'g5_shop_item.it_price_unit5',
            'g5_shop_item.it_price6',
            'g5_shop_item.it_price_rate6',
            'g5_shop_item.it_price_unit6',
            'g5_shop_item.it_price7',
            'g5_shop_item.it_price_rate7',
            'g5_shop_item.it_price_unit7',
            'g5_shop_item.it_price8',
            'g5_shop_item.it_price_rate8',
            'g5_shop_item.it_price_unit8',
            'g5_shop_item.it_price9',
            'g5_shop_item.it_price_rate9',
            'g5_shop_item.it_price_unit9',
            'g5_shop_item.it_price10',
            'g5_shop_item.it_price_rate10',
            'g5_shop_item.it_price_unit10',
            'g5_shop_item.agency_it_price1',
            'g5_shop_item.agency_it_price_rate1',
            'g5_shop_item.agency_it_price_unit1',
            'g5_shop_item.agency_it_price2',
            'g5_shop_item.agency_it_price_rate2',
            'g5_shop_item.agency_it_price_unit2',
            'g5_shop_item.agency_it_price3',
            'g5_shop_item.agency_it_price_rate3',
            'g5_shop_item.agency_it_price_unit3',
            'g5_shop_item.agency_it_price4',
            'g5_shop_item.agency_it_price_rate4',
            'g5_shop_item.agency_it_price_unit4',
            'g5_shop_item.agency_it_price5',
            'g5_shop_item.agency_it_price_rate5',
            'g5_shop_item.agency_it_price_unit5',


            'g5_shop_item.it_cust_price',
            'g5_shop_item.it_cust_price_use',
            'g5_shop_item.agency_it_buy_min_qty',
            'g5_shop_item.agency_it_buy_max_qty',
            'g5_shop_item.it_qty_system_stock',
            'shop_group.cnt AS it_group_qty_system_stock',
            DB::raw('IFNULL(shop_group.cnt, 0) + IFNULL(g5_shop_item.it_qty_system_stock, 0) AS it_group_sum_system_stock'),
            'shop_group.stock_it_id AS it_group_stock_it_id',
            'g5_shop_item.it_img1',
            'g5_shop_item.it_img1_url',

            'g5_shop_item_cust_log.it_price1 as cust_price1',
            'g5_shop_item_cust_log.it_price2 as cust_price2',
            'g5_shop_item_cust_log.it_price3 as cust_price3',
            'g5_shop_item_cust_log.it_price4 as cust_price4',
            'g5_shop_item_cust_log.it_price5 as cust_price5',
            'g5_shop_item_cust_log.it_price6 as cust_price6',
            'g5_shop_item_cust_log.it_price7 as cust_price7',
            'g5_shop_item_cust_log.it_price8 as cust_price8',
            'g5_shop_item_cust_log.it_price9 as cust_price9',
            'g5_shop_item_cust_log.it_price10 as cust_price10'
        )
        ->when(($request->input('it_id')), function($query) use ($request) {
            $query->where('g5_shop_item.it_id', $request->input('it_id'));
        })
        ->when($request->filled('desc'), function($query) use ($request, $orderField, $it_price) {

            if ($request->desc == '1') {
                $query->orderByRaw("FIELD(idx, $orderField)");
            }

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
            
        }, function($query) use ($orderField) {
            $query->orderByRaw("FIELD(idx, $orderField)");
        })
        ->get()->toArray();



        return new LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $page,
            [
                'path'  => Paginator::resolveCurrentPath(),
                'query' => $request->query(),
            ]
        );
    }



    public function shopItemEquipList($request)
    {
        $page    = $request->get('page', 1);
        $perPage = $request->get('scale', 60);

        // 최소주문 금액 구하기
        $it_price = 'it_price';
        if (session('ss_mb_code')) {
            $member = app(MallShopService::class)->getMemberInfo(session('ss_mb_code'));
            $it_price = $member['field_it_price'];
        }



        $idxList = ShopItem::getShopItemEquips(
            $request
        );

        $total = count($idxList);

        $offset = ($page - 1) * $perPage;
        $pagedIdx = array_slice($idxList , $offset, $perPage);

        if ($total == 0) {
            return new LengthAwarePaginator([], $total, $perPage, $page, [
                'path'  => Paginator::resolveCurrentPath(), 
                'query' => $request->query(),  
            ]);
        }

        $orderField = implode(',', array_map(function($i) {
            return "'$i'";
        }, $pagedIdx));


        // 판매량순 구하기
        $saleJoin = '';
        if ($request->filled('desc') && $request->desc == '4') {
            $saleJoin = ShopCart::selectRaw('COUNT(*) as cnt, it_id')->where('ct_status', '완료')->groupBy('it_id');
        }

        $shopGroup = ShopItem::selectRaw('it_qty_system_stock AS cnt, stock_it_id')
        ->groupBy('stock_it_id');

        $items = ShopItem::whereIn('idx', $pagedIdx)
        ->leftJoinSub($shopGroup, 'shop_group', function($join) {
            $join->on('g5_shop_item.stock_it_id', '=', 'shop_group.stock_it_id');
        })
        ->leftjoin('g5_shop_item_cust_log', function($join) {
            $join->on('g5_shop_item.it_id', '=', 'g5_shop_item_cust_log.it_id')
            ->where('g5_shop_item_cust_log.it_cust_rate_chk', '1');
        })
        ->when($request->filled('desc') && $request->desc == '4' && $saleJoin, function($query) use ($saleJoin) {
            $query->leftJoinSub($saleJoin, 'shop_sales', function($join) {
                $join->on('g5_shop_item.it_id', '=', 'shop_sales.it_id');
            });
        })
        ->select(
            'g5_shop_item.idx',
            'g5_shop_item.it_id',
            'g5_shop_item.it_use',
            'g5_shop_item.it_name',
            'g5_shop_item.it_basic',
            'g5_shop_item.it_gubun',
            'g5_shop_item.it_qty_box', 
            'g5_shop_item.it_qty_pack', 
            'g5_shop_item.it_qty_pcs',
            DB::raw("
                CASE 
                    WHEN g5_shop_item.it_gubun = 'box' THEN '박스'
                    WHEN g5_shop_item.it_gubun = 'pack' THEN '팩'
                    WHEN g5_shop_item.it_gubun = 'pcs' THEN '낱개'
                END AS it_gubun_label
            "),
            'g5_shop_item.it_return',
            DB::raw("
                CASE 
                    WHEN g5_shop_item.it_return = '1' THEN '반품가능'
                    WHEN g5_shop_item.it_return = '2' THEN '반품불가'
                END AS it_return_label
            "),
            'g5_shop_item.it_soldout',
            'g5_shop_item.it_storage',
            DB::raw("
                CASE 
                    WHEN g5_shop_item.it_storage = '1' THEN '상온'
                    WHEN g5_shop_item.it_storage = '2' THEN '냉동'
                    WHEN g5_shop_item.it_storage = '3' THEN '냉장'
                END AS it_storage_label
            "),
            'g5_shop_item.stock_it_id',
            'g5_shop_item.it_special_explan',
            'g5_shop_item.it_force_soldout',
            'g5_shop_item.it_buy_min_qty',
            'g5_shop_item.it_buy_max_qty',
            'g5_shop_item.it_price_piece_use',
            'g5_shop_item.it_increase_type',
            'g5_shop_item.it_increase_date',
            'g5_shop_item.it_type3',
            'g5_shop_item.it_type4',
            'g5_shop_item.it_type5',
            'g5_shop_item.it_cust_rate',
            'g5_shop_item.it_cust_type',

            'g5_shop_item.it_box_sale_pcs',
            'g5_shop_item.it_box_sale_pack',
            'g5_shop_item.it_box_sale_tot',

            'g5_shop_item.it_price',
            'g5_shop_item.it_price_purchase',
            'g5_shop_item.it_price_piece',
            'g5_shop_item.it_price_piece_use',
            'g5_shop_item.it_price_box_unit',
            'g5_shop_item.it_price_pack_unit',
            'g5_shop_item.it_price_piece_unit',
            'g5_shop_item.it_price_unit',
            'g5_shop_item.it_price1',
            'g5_shop_item.it_price_rate1',
            'g5_shop_item.it_price_unit1',
            'g5_shop_item.it_price2',
            'g5_shop_item.it_price_rate2',
            'g5_shop_item.it_price_unit2',
            'g5_shop_item.it_price3',
            'g5_shop_item.it_price_rate3',
            'g5_shop_item.it_price_unit3',
            'g5_shop_item.it_price4',
            'g5_shop_item.it_price_rate4',
            'g5_shop_item.it_price_unit4',
            'g5_shop_item.it_price5',
            'g5_shop_item.it_price_rate5',
            'g5_shop_item.it_price_unit5',
            'g5_shop_item.it_price6',
            'g5_shop_item.it_price_rate6',
            'g5_shop_item.it_price_unit6',
            'g5_shop_item.it_price7',
            'g5_shop_item.it_price_rate7',
            'g5_shop_item.it_price_unit7',
            'g5_shop_item.it_price8',
            'g5_shop_item.it_price_rate8',
            'g5_shop_item.it_price_unit8',
            'g5_shop_item.it_price9',
            'g5_shop_item.it_price_rate9',
            'g5_shop_item.it_price_unit9',
            'g5_shop_item.it_price10',
            'g5_shop_item.it_price_rate10',
            'g5_shop_item.it_price_unit10',
            'g5_shop_item.agency_it_price1',
            'g5_shop_item.agency_it_price_rate1',
            'g5_shop_item.agency_it_price_unit1',
            'g5_shop_item.agency_it_price2',
            'g5_shop_item.agency_it_price_rate2',
            'g5_shop_item.agency_it_price_unit2',
            'g5_shop_item.agency_it_price3',
            'g5_shop_item.agency_it_price_rate3',
            'g5_shop_item.agency_it_price_unit3',
            'g5_shop_item.agency_it_price4',
            'g5_shop_item.agency_it_price_rate4',
            'g5_shop_item.agency_it_price_unit4',
            'g5_shop_item.agency_it_price5',
            'g5_shop_item.agency_it_price_rate5',
            'g5_shop_item.agency_it_price_unit5',


            'g5_shop_item.it_cust_price',
            'g5_shop_item.it_cust_price_use',
            'g5_shop_item.agency_it_buy_min_qty',
            'g5_shop_item.agency_it_buy_max_qty',
            'g5_shop_item.it_qty_system_stock',
            'shop_group.cnt AS it_group_qty_system_stock',
            DB::raw('IFNULL(shop_group.cnt, 0) + IFNULL(g5_shop_item.it_qty_system_stock, 0) AS it_group_sum_system_stock'),
            'shop_group.stock_it_id AS it_group_stock_it_id',
            'g5_shop_item.it_img1',
            'g5_shop_item.it_img1_url',

            'g5_shop_item_cust_log.it_price1 as cust_price1',
            'g5_shop_item_cust_log.it_price2 as cust_price2',
            'g5_shop_item_cust_log.it_price3 as cust_price3',
            'g5_shop_item_cust_log.it_price4 as cust_price4',
            'g5_shop_item_cust_log.it_price5 as cust_price5',
            'g5_shop_item_cust_log.it_price6 as cust_price6',
            'g5_shop_item_cust_log.it_price7 as cust_price7',
            'g5_shop_item_cust_log.it_price8 as cust_price8',
            'g5_shop_item_cust_log.it_price9 as cust_price9',
            'g5_shop_item_cust_log.it_price10 as cust_price10'
        )
        ->when(($request->input('it_id')), function($query) use ($request) {
            $query->where('g5_shop_item.it_id', $request->input('it_id'));
        })
        ->when($request->filled('desc'), function($query) use ($request, $orderField, $it_price) {

            if ($request->desc == '1') {
                $query->orderByRaw("FIELD(idx, $orderField)");
            }

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
            
        }, function($query) use ($orderField) {
            $query->orderByRaw("FIELD(idx, $orderField)");
        })
        ->get()->toArray();



        return new LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $page,
            [
                'path'  => Paginator::resolveCurrentPath(),
                'query' => $request->query(),
            ]
        );
    }


    public function itemDetail($it_id) 
    {
        $shopGroup = ShopItem::selectRaw('it_qty_system_stock AS cnt, stock_it_id')
        ->groupBy('stock_it_id');

        return  ShopItem::where('g5_shop_item.it_id', $it_id)
        ->leftJoinSub($shopGroup, 'shop_group', function($join) {
            $join->on('g5_shop_item.stock_it_id', '=', 'shop_group.stock_it_id');
        })
        ->leftjoin('g5_shop_item_cust_log', function($join) {
            $join->on('g5_shop_item.it_id', '=', 'g5_shop_item_cust_log.it_id')
            ->where('g5_shop_item_cust_log.it_cust_rate_chk', '1');
        })
        ->select(
            'g5_shop_item.idx',
            'g5_shop_item.it_id',
            'g5_shop_item.it_use',
            'g5_shop_item.ca_id',
            'g5_shop_item.ca_id2',
            'g5_shop_item.it_name',
            'g5_shop_item.it_basic',
            'g5_shop_item.it_maker',
            'g5_shop_item.it_gubun',
            'g5_shop_item.it_qty_box', 
            'g5_shop_item.it_qty_pack', 
            'g5_shop_item.it_qty_pcs',
            DB::raw("
                CASE 
                    WHEN g5_shop_item.it_gubun = 'box' THEN '박스'
                    WHEN g5_shop_item.it_gubun = 'pack' THEN '팩'
                    WHEN g5_shop_item.it_gubun = 'pcs' THEN '낱개'
                END AS it_gubun_label
            "),
            'g5_shop_item.it_return',
            DB::raw("
                CASE 
                    WHEN g5_shop_item.it_return = '1' THEN '반품가능'
                    WHEN g5_shop_item.it_return = '2' THEN '반품불가'
                END AS it_return_label
            "),
            'g5_shop_item.it_soldout',
            'g5_shop_item.it_storage',
            DB::raw("
                CASE 
                    WHEN g5_shop_item.it_storage = '1' THEN '상온'
                    WHEN g5_shop_item.it_storage = '2' THEN '냉동'
                    WHEN g5_shop_item.it_storage = '3' THEN '냉장'
                END AS it_storage_label
            "),
            'g5_shop_item.stock_it_id',
            'g5_shop_item.it_special_explan',
            'g5_shop_item.it_force_soldout',
            'g5_shop_item.it_buy_min_qty',
            'g5_shop_item.it_buy_max_qty',
            'g5_shop_item.it_price_piece_use',
            'g5_shop_item.it_increase_type',
            'g5_shop_item.it_increase_date',
            'g5_shop_item.it_type3',
            'g5_shop_item.it_type4',
            'g5_shop_item.it_type5',
            'g5_shop_item.it_cust_rate',
            'g5_shop_item.it_cust_type',

            'g5_shop_item.it_box_sale_pcs',
            'g5_shop_item.it_box_sale_pack',
            'g5_shop_item.it_box_sale_tot',

            'g5_shop_item.it_price',
            'g5_shop_item.it_price_purchase',
            'g5_shop_item.it_price_piece',
            'g5_shop_item.it_price_piece_use',
            'g5_shop_item.it_price_box_unit',
            'g5_shop_item.it_price_pack_unit',
            'g5_shop_item.it_price_piece_unit',
            'g5_shop_item.it_price_unit',
            'g5_shop_item.it_price1',
            'g5_shop_item.it_price_rate1',
            'g5_shop_item.it_price_unit1',
            'g5_shop_item.it_price2',
            'g5_shop_item.it_price_rate2',
            'g5_shop_item.it_price_unit2',
            'g5_shop_item.it_price3',
            'g5_shop_item.it_price_rate3',
            'g5_shop_item.it_price_unit3',
            'g5_shop_item.it_price4',
            'g5_shop_item.it_price_rate4',
            'g5_shop_item.it_price_unit4',
            'g5_shop_item.it_price5',
            'g5_shop_item.it_price_rate5',
            'g5_shop_item.it_price_unit5',
            'g5_shop_item.it_price6',
            'g5_shop_item.it_price_rate6',
            'g5_shop_item.it_price_unit6',
            'g5_shop_item.it_price7',
            'g5_shop_item.it_price_rate7',
            'g5_shop_item.it_price_unit7',
            'g5_shop_item.it_price8',
            'g5_shop_item.it_price_rate8',
            'g5_shop_item.it_price_unit8',
            'g5_shop_item.it_price9',
            'g5_shop_item.it_price_rate9',
            'g5_shop_item.it_price_unit9',
            'g5_shop_item.it_price10',
            'g5_shop_item.it_price_rate10',
            'g5_shop_item.it_price_unit10',
            'g5_shop_item.agency_it_price1',
            'g5_shop_item.agency_it_price_rate1',
            'g5_shop_item.agency_it_price_unit1',
            'g5_shop_item.agency_it_price2',
            'g5_shop_item.agency_it_price_rate2',
            'g5_shop_item.agency_it_price_unit2',
            'g5_shop_item.agency_it_price3',
            'g5_shop_item.agency_it_price_rate3',
            'g5_shop_item.agency_it_price_unit3',
            'g5_shop_item.agency_it_price4',
            'g5_shop_item.agency_it_price_rate4',
            'g5_shop_item.agency_it_price_unit4',
            'g5_shop_item.agency_it_price5',
            'g5_shop_item.agency_it_price_rate5',
            'g5_shop_item.agency_it_price_unit5',


            'g5_shop_item.it_cust_price',
            'g5_shop_item.it_cust_price_use',
            'g5_shop_item.agency_it_buy_min_qty',
            'g5_shop_item.agency_it_buy_max_qty',
            'g5_shop_item.it_qty_system_stock',
            'shop_group.cnt AS it_group_qty_system_stock',
            DB::raw('IFNULL(shop_group.cnt, 0) + IFNULL(g5_shop_item.it_qty_system_stock, 0) AS it_group_sum_system_stock'),
            'shop_group.stock_it_id AS it_group_stock_it_id',
            'g5_shop_item.it_img1',
            'g5_shop_item.it_img1_url',
            'g5_shop_item.it_explan',

            'g5_shop_item_cust_log.it_price1 as cust_price1',
            'g5_shop_item_cust_log.it_price2 as cust_price2',
            'g5_shop_item_cust_log.it_price3 as cust_price3',
            'g5_shop_item_cust_log.it_price4 as cust_price4',
            'g5_shop_item_cust_log.it_price5 as cust_price5',
            'g5_shop_item_cust_log.it_price6 as cust_price6',
            'g5_shop_item_cust_log.it_price7 as cust_price7',
            'g5_shop_item_cust_log.it_price8 as cust_price8',
            'g5_shop_item_cust_log.it_price9 as cust_price9',
            'g5_shop_item_cust_log.it_price10 as cust_price10'
        )->first();
    }




    // 회원별 판매금액 필드
    public function getMemberCostField($member)
    {
        $data = DB::table('tb_config_memcost')->select(['gubun', 'p_id'])
        ->where('state', '2')
        ->orderBy('id', 'asc')
        ->orderBy('gubun', 'asc')
        ->get()->toArray();


        $field_it_price = $field_it_price_unit = '';
        foreach ($data as $key => $row) {
            if ($row->p_id == $member['mb_memcost']) {
                $memcost        = substr($row->p_id, -1);
                
                $field_it_price = "it_price".$memcost;
                $field_it_price_unit = "it_price_unit".$memcost;
            }
        }

        if($member['mb_memcost'] == "MC0010") { // J단가
            $field_it_price = "it_price10";
            $field_it_price_unit = "it_price_unit10";
        }

        if($member['mb_memcost'] == "MC0011") { // 대리점 A단가
            $field_it_price = "agency_it_price1";
            $field_it_price_unit = "agency_it_price_unit1";
        }

        if($member['mb_memcost'] == "MC0012") { // 대리점 B단가
            $field_it_price = "agency_it_price2";
            $field_it_price_unit = "agency_it_price_unit2";
        }

        if($member['mb_memcost'] == "MC0013") { // 대리점 C단가
            $field_it_price = "agency_it_price3";
            $field_it_price_unit = "agency_it_price_unit3";
        }

        if($member['mb_memcost'] == "MC0014") { // 대리점 D단가
            $field_it_price = "agency_it_price4";
            $field_it_price_unit = "agency_it_price_unit4";
        }

        if($member['mb_memcost'] == "MC0015") { // 대리점 E단가
            $field_it_price = "agency_it_price5";
            $field_it_price_unit = "agency_it_price_unit5";
        }

        if($member['mb_level_type'] == "2" && $member['mb_gubun_type'] == "employee") { // 사원단가
            $field_it_price = "it_price_purchase";
            $field_it_price_unit = "it_price_piece";
        }

        return [
            'field_it_price' => $field_it_price,
            'field_it_price_unit' => $field_it_price_unit,
        ];
    }


    public function MyRecetOrderItems($request)
    {
        $mb_code = session('ss_mb_code');

        $latestOdId = DB::table('g5_shop_order')
        ->where('mb_code', $mb_code)
        ->where('od_delivery_step', '>', 85)->where('od_gubun', '!=', '충전금구매')
        ->orderByDesc('od_delivery_date')
        ->value('od_id');

        $cancelSub = DB::table('g5_shop_cart as sc')
        ->selectRaw('sc.it_id, SUM(sc.ct_qty) as cnt')
        ->join('g5_shop_order as so', 'sc.od_id', '=', 'so.od_id')
        ->where('sc.mb_code', $mb_code)
        ->where('sc.ct_cate', '!=', '납품')
        ->where('so.od_id', $latestOdId)
        ->groupBy('sc.it_id');


        $result = DB::table('g5_shop_cart as sc')
        ->selectRaw("
            sc.od_id,
            sc.it_id,
            sc.it_name,
            sc.ct_status,
            sc.ct_cate,
            sc.ct_price,

            SUM(sc.ct_qty) AS ct_qty,
            IFNULL(cancel.cnt, 0) AS cancel_cnt,
            SUM(sc.ct_qty) - IFNULL(cancel.cnt, 0) AS real_cnt,

            CONCAT('images/item/', si.it_img1) AS it_img1,
            si.it_basic,

            CASE 
                WHEN si.it_gubun = 'box' THEN '박스'
                WHEN si.it_gubun = 'pack' THEN '팩'
                WHEN si.it_gubun = 'pcs' THEN '낱개'
            END AS it_gubun_label,

            si.it_return,

            CASE 
                WHEN si.it_return = '1' THEN '반품가능'
                WHEN si.it_return = '2' THEN '반품불가'
            END AS it_return_label,

            si.it_storage,
            
            CASE 
                WHEN si.it_storage = '1' THEN '상온'
                WHEN si.it_storage = '2' THEN '냉동'
                WHEN si.it_storage = '3' THEN '냉장'
            END AS it_storage_label,

            so.od_delivery_date,
            so.od_delivery_step
        ")

        ->join('g5_shop_item as si', 'sc.it_id', '=', 'si.it_id')
        ->leftJoin('g5_shop_order as so', 'sc.od_id', '=', 'so.od_id')

        ->leftJoinSub($cancelSub, 'cancel', function ($join) {
            $join->on('sc.it_id', '=', 'cancel.it_id');
        })

        ->where('sc.mb_code', $mb_code)
        ->where('si.it_use', '1')
        ->when($request->return_skeyword, function($query) use ($request) {
            $query->where('sc.it_name', 'LIKE', '%'.$request->return_skeyword.'%');
        })
        ->whereNotIn('sc.ct_status', ['쇼핑', '취소', '삭제'])
        ->where('so.od_delivery_step', '>', 85)

        ->where('sc.od_id', $latestOdId)

        ->groupBy('sc.it_id')
        ->orderByDesc('so.od_delivery_date')

        ->get();

        return $result ?? [];
    }
}