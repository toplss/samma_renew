<?php
namespace App\Services;

use Illuminate\Support\Facades\DB;

class ShopItemService
{

    public static function selected()
    {
        return [
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
        ];
    }
}