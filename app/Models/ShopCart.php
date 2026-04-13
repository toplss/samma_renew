<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class ShopCart extends Model
{
    //
    protected $table = 'g5_shop_cart';
    protected $primaryKey = 'ct_id';

    public $timestamps = false;

    protected $fillable = [
        'od_group_code',
        'od_group_code_org',
        'od_id',
        'od_id_org',
        'od_date',
        'mb_code',
        'mb_id',
        'mb_branch_code',
        'mb_branch_gubun_type',
        'level_ca_id',
        'level_ca_id2',
        'level_ca_id3',
        'level_ca_id4',
        'level_ca_id5',
        'mb_level',
        'it_id',
        'it_mb_num',
        'it_mb_id',
        'it_name',
        'it_gubun',
        'it_gubun1',
        'it_gubun2',
        'it_gubun3',
        'it_gubun4',
        'it_gubun5',
        'it_storage',
        'it_sc_type',
        'it_sc_method',
        'it_sc_price',
        'it_sc_minimum',
        'it_sc_qty',
        'ct_status',
        'ct_price',
        'ct_point',
        'ct_point_use',
        'ct_stock_use',
        'ct_option',
        'ct_qty',
        'ct_qty_order',
        'ct_cate',
        'supply_price',
        'supply_price_unit',
        'price_unit',
        'pt_sales',
        'ct_qty_box',
        'ct_qty_pack',
        'ct_qty_pcs',
        'ct_qty_tot',
        'ct_notax',
        'ct_tax_mny',
        'ct_vat_mny',
        'ct_free_mny',
        'io_id',
        'io_type',
        'io_price',
        'ct_time',
        'ct_ip',
        'ct_send_cost',
        'ct_direct',
        'ct_select',
        'ct_select_time',
        'ct_admin_data',
    ];




    /**
     * 메인 > 자주하는 질문
     *
     * @return array
     */
    public static function rankOrderList()
    {
        $rank_fr_date = Carbon::now()
            ->subDays(3)
            ->format('Ymd');

        $rank_to_date = Carbon::now()
            ->subDays(1)
            ->format('Ymd');

        $result = self::selectRaw("
            g5_shop_cart.it_id,
            si.it_name,
            si.it_price,
            si.it_img1,
            SUM(
                IF(
                    g5_shop_cart.ct_status IN ('_입금', '준비', '배송', '완료'),
                    g5_shop_cart.ct_qty,
                    0
                )
            ) as ct_status_sum
        ")
        ->join('g5_shop_item as si', 'g5_shop_cart.it_id', '=', 'si.it_id')
        ->where('g5_shop_cart.ct_cate', '납품')
        ->where('si.it_use', '1')
        ->whereIn('g5_shop_cart.ct_status', ['입금', '준비', '배송', '완료'])
        ->whereBetween('g5_shop_cart.od_date', [$rank_fr_date, $rank_to_date])
        ->groupBy('g5_shop_cart.it_id')
        ->orderByDesc('ct_status_sum')
        ->limit(6)
        ->get()->toArray();

        return $result;
    }
}
