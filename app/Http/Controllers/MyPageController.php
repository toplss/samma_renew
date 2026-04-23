<?php
namespace App\Http\Controllers;

use App\Models\TbMember;
use App\Models\ShopCart;
use App\Services\FranchiseCategoryService;
use App\Services\MallShopService;
use Illuminate\Pagination\Paginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use App\Traits\RedisTrait;

use Debugbar;
use Exception;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Redis;
use Predis\Command\Traits\BloomFilters\Items;

class MyPageController extends Controller
{
    use RedisTrait;
    protected $CategoryService;

    public function __construct(FranchiseCategoryService $CategoryService)
    {
        $this->CategoryService = $CategoryService;
    }


    /**
     * method Name : orderinquiry
     * Description : 주문내역조회
     * Author : Kim Hairyong 
     * Created Date : 2026-01-20
     * Params : Params
     * History :
     *   - 2026-01-20 : Initial creation
     */
    public function orderinquiry(Request $request)
    {
        $page    = $request->get('page', 1);
        $desc    = $request->get('desc', '');
        $perPage = $request->get('scale', 20);

        $start_date = $request->input('start_date', '');
        $end_date = $request->input('end_date', '');

        $subSo = DB::table('g5_shop_order')
            ->selectRaw("
                od_group_code,
                od_delivery_step,

                IFNULL(SUM(pt_sales), 0 ) as sum_pt_sales,
                IFNULL(SUM(pt_delivery), 0 ) as sum_pt_delivery,
                IFNULL(SUM(pt_sales_delivery), 0 ) as sum_pt_sales_delivery,
                IFNULL(SUM(pt_charge), 0 ) as sum_pt_charge,
                IFNULL(SUM(pt_reserve), 0 ) as sum_pt_reserve,
                IFNULL(SUM(pt_buy_charge), 0 ) as sum_pt_buy_charge,
                IFNULL(SUM(pt_buy_reserve), 0 ) as sum_pt_buy_reserve,
                IFNULL(SUM(pt_cash), 0 ) as sum_pt_cash,
                IFNULL(SUM(pt_bank), 0 ) as sum_pt_bank,
                IFNULL(SUM(pt_card), 0 ) as sum_pt_card,
                IFNULL(SUM(pt_diff_pay), 0 ) as sum_pt_diff_pay,
                IFNULL(SUM(pt_incentive), 0 ) as sum_pt_incentive,
                IFNULL(SUM(pt_dc), 0 ) as sum_pt_dc,
                IFNULL(SUM(pt_discount), 0 ) as sum_pt_discount,
                IFNULL(SUM(pt_support), 0 ) as sum_pt_support,
                IFNULL(SUM(pt_damage_staff), 0 ) as sum_pt_damage_staff,
                IFNULL(SUM(pt_damage_logistic), 0 ) as sum_pt_damage_logistic,
                IFNULL(SUM(pt_return), 0 ) as sum_pt_return,
                IFNULL(SUM(pt_return_receivable), 0 ) as sum_pt_return_receivable,
                IFNULL(SUM(pt_cancel), 0 ) as sum_pt_cancel,
                IFNULL(SUM(pt_outofstock), 0 ) as sum_pt_outofstock,
                IFNULL(SUM(pt_outofstock_deposit), 0 ) as sum_pt_outofstock_deposit,
                IFNULL(SUM(pt_subtotal), 0 ) as sum_pt_subtotal,
                IFNULL(SUM(pt_refund), 0 ) as sum_pt_refund,
                IFNULL(SUM(pt_refund_done), 0 ) as sum_pt_refund_done
            ")
            ->where('mb_code', session('ss_mb_code'))
            ->groupByRaw("
                od_group_code,
                CASE 
                    WHEN od_delivery_step IN (90, 99) THEN 90
                    ELSE od_delivery_step
                END
            ");


            $result = DB::table('g5_shop_order as so')

                ->selectRaw("
                    (SELECT it_name FROM g5_shop_item WHERE it_id = (
                        SELECT it_id FROM g5_shop_cart WHERE od_id = so.od_id ORDER BY it_id LIMIT 1
                    )) AS it_name,

                    (SELECT it_img1 FROM g5_shop_item WHERE it_id = (
                        SELECT it_id FROM g5_shop_cart WHERE od_id = so.od_id ORDER BY it_id LIMIT 1
                    )) AS it_img1,

                    so.mb_code,
                    so.level_ca_id2,
                    so.od_group_code,
                    COUNT(*) AS order_cnt,
                    so.od_id,

                    (SELECT COUNT(*) 
                    FROM g5_shop_cart 
                    WHERE od_id IN (SELECT od_id 
                                    FROM g5_shop_order 
                                    WHERE od_group_code = so.od_group_code 
                                        AND od_delivery_step = so.od_delivery_step
                                    )
                    ) AS ct_cnt,

                    (SELECT it_id FROM g5_shop_cart WHERE od_id = so.od_id ORDER BY it_id LIMIT 1) AS it_id,

                    so.od_delivery_step,
                    so.od_delivery_date,
                    so.od_gubun,

                    COALESCE(
                        NULLIF(
                            CONCAT_WS('#',
                                IF(so.pt_sales_delivery > 0, '매출', NULL),
                                IF(so.pt_cancel > 0, '취소', NULL),
                                IF(so.pt_return > 0, '반품', NULL),
                                IF(so.pt_return_receivable > 0, '반품채권', NULL),
                                IF(so.pt_outofstock > 0, '결품', NULL),
                                IF(so.pt_outofstock_deposit > 0, '결품채권', NULL),
                                IF(so.pt_damage_staff > 0, '기사파손', NULL),
                                IF(so.pt_damage_logistic > 0, '물류파손', NULL),
                                IF(so.od_delivery_step = 8, '잔액이관', NULL)
                            ), ''
                        ),
                        so.od_gubun
                    ) AS current_gubun,

                    sub_so.sum_pt_sales,
                    sub_so.sum_pt_delivery,
                    sub_so.sum_pt_sales_delivery,
                    sub_so.sum_pt_charge,
                    sub_so.sum_pt_reserve,
                    sub_so.sum_pt_buy_charge,
                    sub_so.sum_pt_buy_reserve,
                    sub_so.sum_pt_cash,
                    sub_so.sum_pt_bank,
                    sub_so.sum_pt_card,
                    sub_so.sum_pt_diff_pay,
                    sub_so.sum_pt_incentive,
                    sub_so.sum_pt_dc,
                    sub_so.sum_pt_discount,
                    sub_so.sum_pt_support,
                    sub_so.sum_pt_damage_staff,
                    sub_so.sum_pt_damage_logistic,
                    sub_so.sum_pt_return,
                    sub_so.sum_pt_return_receivable,
                    sub_so.sum_pt_cancel,
                    sub_so.sum_pt_outofstock,
                    sub_so.sum_pt_outofstock_deposit,
                    sub_so.sum_pt_subtotal,
                    sub_so.sum_pt_refund,
                    sub_so.sum_pt_refund_done
                ")

                /**
                 * JOIN sub_so
                 */
                ->joinSub($subSo, 'sub_so', function ($join) {
                    $join->on('so.od_group_code', '=', 'sub_so.od_group_code')
                        ->on('so.od_delivery_step', '=', 'sub_so.od_delivery_step');
                })

                ->where('so.mb_code', session('ss_mb_code'))

                ->when($start_date && $end_date, function ($query) use ($start_date, $end_date) {
                    $query->whereBetween(DB::raw('DATE(so.od_delivery_date)'), [$start_date, $end_date]);
                })

                /**
                 * GROUP BY
                 */
                ->groupByRaw("
                    so.od_group_code,
                    CASE 
                        WHEN so.od_delivery_step IN (90, 99) THEN 90
                        ELSE so.od_delivery_step
                    END
                ")

                /**
                 * ORDER BY
                 */
                ->orderByRaw("
                    CASE 
                        WHEN so.od_delivery_step = 8 THEN 2
                        WHEN so.od_delivery_step IN (90, 99) THEN 1
                        ELSE 0
                    END ASC
                ")
                ->orderByRaw("
                    CASE 
                        WHEN so.od_delivery_step IN (90, 99) THEN so.od_delivery_date
                    END DESC
                ")
                ->orderBy('so.od_delivery_step', 'ASC')
                ->orderByRaw("so.od_gubun IN ('기초잔액추가', '잔액조정') ASC")
                ->orderBy('so.od_delivery_date', 'DESC')
                ->orderBy('so.od_idx', 'DESC')    

                ->paginate($perPage);

        $items = new LengthAwarePaginator(
            $result,
            $result->total(),
            $perPage,
            $page,
            [
                'path'  => Paginator::resolveCurrentPath(),
                'query' => $request->query(),
            ]
        );

        return view('mypage.orderinquiry', ['items' => $items]);

    }



    /**
     * method Name : OrderInquiryPop
     * Description : 상세보기 팝업
     * Author : Kim Hairyong 
     * Created Date : 2026-01-22
     * Params : Params
     * History :
     *   - 2026-01-22 : Initial creation
     */
    public function OrderInquiryPop(Request $request)
    {
        $request->validate([
            'ogc' => 'required|exists:g5_shop_order,od_group_code',
            'ods' => 'required|exists:g5_shop_order,od_delivery_step',
        ], [
            'ogc.required' => '(:attribute) 필수 파라미터가 누락 되었습니다.',
            'ogc.exists' => '(:attribute) 조회 데이터가 없습니다.',
            'ods.required' => '(:attribute) 필수 파라미터가 누락 되었습니다.',
            'ods.exists' => '(:attribute) 조회 데이터가 없습니다.',
        ]);

        $ogc = $request->input('ogc');
        $ods = $request->input('ods');
        $mb_code = session('ss_mb_code');

        $sql = "
            SELECT
                (SELECT it_name FROM g5_shop_item WHERE it_id = (
                    SELECT it_id FROM g5_shop_cart WHERE od_id = so.od_id ORDER BY it_id LIMIT 1
                )) AS it_name,

                (SELECT it_img1 FROM g5_shop_item WHERE it_id = (
                    SELECT it_id FROM g5_shop_cart WHERE od_id = so.od_id ORDER BY it_id LIMIT 1
                )) AS it_img1,

                so.mb_code,
                so.level_ca_id2,
                so.od_group_code,
                COUNT(*) AS order_cnt,
                so.od_id,

                (
                    SELECT COUNT(*)
                    FROM g5_shop_cart
                    WHERE od_id IN (
                        SELECT od_id
                        FROM g5_shop_order
                        WHERE od_group_code = so.od_group_code
                        AND od_delivery_step = so.od_delivery_step
                    )
                ) AS ct_cnt,

                (SELECT it_id FROM g5_shop_cart WHERE od_id = so.od_id ORDER BY it_id LIMIT 1) AS it_id,

                so.od_delivery_step,
                so.od_delivery_date,
                so.od_gubun,
                so.od_settle_case,

                COALESCE(
                    NULLIF(
                        CONCAT_WS('#',
                            IF(so.pt_sales_delivery > 0, '매출', NULL),
                            IF(so.pt_cancel > 0, '취소', NULL),
                            IF(so.pt_return > 0, '반품', NULL),
                            IF(so.pt_return_receivable > 0, '반품채권', NULL),
                            IF(so.pt_outofstock > 0, '결품', NULL),
                            IF(so.pt_outofstock_deposit > 0, '결품채권', NULL),
                            IF(so.pt_damage_staff > 0, '기사파손', NULL),
                            IF(so.pt_damage_logistic > 0, '물류파손', NULL),
                            IF(so.od_delivery_step = 8, '잔액이관', NULL)
                        ), ''
                    ),
                    so.od_gubun
                ) AS current_gubun,

                sub_so.*

            FROM g5_shop_order so

            JOIN (
                SELECT
                    od_group_code,
                    CASE 
                        WHEN od_delivery_step IN (90, 99) THEN 90
                        ELSE od_delivery_step
                    END AS od_delivery_step,

                    IFNULL(SUM(od_temp_point), 0 ) as charge_sum,
                    IFNULL(SUM(od_temp_point_reserve), 0 ) as reserve_sum,

                    IFNULL(SUM(CASE 
                        WHEN od_settle_case IN ('금융권가상계좌','무통장입금') 
                        THEN od_receipt_price ELSE 0 END), 0 ) AS bank_sum,

                    IFNULL(SUM(CASE 
                        WHEN od_settle_case = '신용카드'
                        THEN od_receipt_price ELSE 0 END), 0 ) AS card_sum,

                    IFNULL(SUM(pt_sales), 0 ) as sum_pt_sales,
                    IFNULL(SUM(pt_delivery), 0 ) as sum_pt_delivery,
                    IFNULL(SUM(pt_sales_delivery), 0 ) as sum_pt_sales_delivery,
                    IFNULL(SUM(pt_charge), 0 ) as sum_pt_charge,
                    IFNULL(SUM(pt_reserve), 0 ) as sum_pt_reserve,
                    IFNULL(SUM(pt_buy_charge), 0 ) as sum_pt_buy_charge,
                    IFNULL(SUM(pt_buy_reserve), 0 ) as sum_pt_buy_reserve,
                    IFNULL(SUM(pt_cash), 0 ) as sum_pt_cash,
                    IFNULL(SUM(pt_bank), 0 ) as sum_pt_bank,
                    IFNULL(SUM(pt_card), 0 ) as sum_pt_card,
                    IFNULL(SUM(pt_diff_pay), 0 ) as sum_pt_diff_pay,
                    IFNULL(SUM(pt_incentive), 0 ) as sum_pt_incentive,
                    IFNULL(SUM(pt_dc), 0 ) as sum_pt_dc,
                    IFNULL(SUM(pt_discount), 0 ) as sum_pt_discount,
                    IFNULL(SUM(pt_support), 0 ) as sum_pt_support,
                    IFNULL(SUM(pt_damage_staff), 0 ) as sum_pt_damage_staff,
                    IFNULL(SUM(pt_damage_logistic), 0 ) as sum_pt_damage_logistic,
                    IFNULL(SUM(pt_return), 0 ) as sum_pt_return,
                    IFNULL(SUM(pt_return_receivable), 0 ) as sum_pt_return_receivable,
                    IFNULL(SUM(pt_cancel), 0 ) as sum_pt_cancel,
                    IFNULL(SUM(pt_outofstock), 0 ) as sum_pt_outofstock,
                    IFNULL(SUM(pt_outofstock_deposit), 0 ) as sum_pt_outofstock_deposit,
                    IFNULL(SUM(pt_subtotal), 0 ) as sum_pt_subtotal,
                    IFNULL(SUM(pt_refund), 0 ) as sum_pt_refund,
                    IFNULL(SUM(pt_refund_done), 0 ) as sum_pt_refund_done

                FROM g5_shop_order
                WHERE mb_code = ?
                GROUP BY od_group_code,
                    CASE 
                        WHEN od_delivery_step IN (90, 99) THEN 90
                        ELSE od_delivery_step
                    END
            ) sub_so
            ON so.od_group_code = sub_so.od_group_code
            AND so.od_delivery_step = sub_so.od_delivery_step

            WHERE so.od_group_code = ?
            AND so.od_delivery_step = ?

            LIMIT 1
        ";

        $items = DB::selectOne($sql, [$mb_code, $ogc, $ods]);

        return view('mypage.orderinquiry_pop', ['items' => $items]);
    }





    /**
     * method Name : OrderInquiryView
     * Description : 주문정보 View
     * Author : Kim Hairyong 
     * Created Date : 2026-01-22
     * Params : Params
     * History :
     *   - 2026-01-22 : Initial creation
     */
    public function OrderInquiryView(Request $request)
    {
        $request->validate([
            'ogc' => 'required|exists:g5_shop_order,od_group_code',
            'ods' => 'required|exists:g5_shop_order,od_delivery_step',
            'oid' => 'required|exists:g5_shop_order,od_id',
        ], [
            'ogc.required' => '(:attribute) 필수 파라미터가 누락 되었습니다.',
            'ogc.exists' => '(:attribute) 조회 데이터가 없습니다.',
            'ods.required' => '(:attribute) 필수 파라미터가 누락 되었습니다.',
            'ods.exists' => '(:attribute) 조회 데이터가 없습니다.',
            'oid.required' => '(:attribute) 필수 파라미터가 누락 되었습니다.',
            'oid.exists' => '(:attribute) 조회 데이터가 없습니다.',
        ]);

        $ogc = $request->input('ogc');
        $ods = $request->input('ods');
        $oid = $request->input('oid');


        $order_list = DB::table('g5_shop_order')
                ->selectRaw("ROW_NUMBER() OVER (ORDER BY order_date ASC) AS row_num")
                ->addSelect(
                    'od_id',
                    'od_gubun',
                    DB::raw("
                        COALESCE(
                            NULLIF(
                                CONCAT_WS('#',
                                    IF(pt_sales_delivery > 0, '매출', NULL),
                                    IF(pt_cancel > 0, '취소', NULL),
                                    IF(pt_return > 0, '반품', NULL),
                                    IF(pt_return_receivable > 0, '반품채권', NULL),
                                    IF(pt_outofstock > 0, '결품', NULL),
                                    IF(pt_outofstock_deposit > 0, '결품채권', NULL),
                                    IF(pt_damage_staff > 0, '기사파손', NULL),
                                    IF(pt_damage_logistic > 0, '물류파손', NULL),
                                    IF(od_delivery_step = 8, '잔액이관', NULL)
                                ), ''
                            ),
                            od_gubun
                        ) AS current_gubun
                    "),                               
                    'od_group_code',
                    'od_status',
                    'level_ca_id2',
                    'order_date',
                    'od_delivery_date',
                    'od_settle_case',
                    'pt_sales_delivery',
                    'od_delivery_step'
                )
                ->where('od_group_code', $ogc)
                ->where('od_delivery_step', $ods)
                ->get();

        $order_info = DB::table('g5_shop_order')
                    ->select(
                        'od_id',
                        'od_group_code',
                        'od_company',
                        'od_name',
                        'od_tel',
                        'od_hp',
                        'od_email',
                        'od_addr1',
                        'od_addr2',
                        'od_memo',
                        'od_gubun',
                        'od_status',
                        'od_delivery_step',
                        'pt_sales_delivery',
                        'od_cart_price',
                        'pt_delivery',
                        'od_temp_point',
                        'od_temp_point_reserve',
                        'od_receipt_price',
                        'od_misu',
                        'od_settle_case',                        
                        'od_deposit_name',
                        'od_bank_account',
                        'od_time',                        
                        'od_delivery_day',
                        'od_delivery_date',
                        'level_ca_id2',
                    )
                    ->where('od_id',$oid)
                    ->first();

        //카트정보 - 납품 / 취소 / 반품... 순으로 한다
        $cart_list = DB::table('g5_shop_cart as sc')
                ->join('g5_shop_item as si', 'sc.it_id', 'si.it_id')
                ->orderBy('sc.it_id', 'ASC')
                ->addSelect(
                    'sc.od_group_code',
                    'sc.od_id',
                    'sc.it_name',
                    'sc.ct_qty',
                    'sc.ct_price',
                    'sc.ct_cate',
                    'si.it_img1',
                    'si.it_storage',
                    'si.it_return',
                    'si.it_return_use',
                    'si.it_basic'
                )
                ->where('sc.od_id', $oid)
                ->get();

        $items = [
            'order_list' => $order_list,
            'order_info' => $order_info,
            'cart_list' => $cart_list
        ];

        return view('mypage.orderinquiryview', ['items' => $items]);
    }


    /**
     * method Name : MyEquipmentList
     * Description : 보유장비 리스트
     * Created Date : 2026-01-26
     * Params : Params
     * History :
     *   - 2026-01-26 : Initial creation
     */
    public function MyEquipmentList(Request $request)
    {
        $mb_code = session('ss_mb_code');

        if ( !$mb_code ) {
            throw new Exception('로그인 사용자가 아닙니다.');
        }

        $start_date = $request->input('start_date', '');
        $end_date = $request->input('end_date', '');

        $items = DB::table('tb_tmp_choice_equipment as a')
                ->selectRaw("
                    ROW_NUMBER() OVER(
                        ORDER BY 
                            a.t_it_id_type = '7' ASC,
                            a.t_specific_item DESC,
                            a.t_it_id_type DESC,
                            a.it_id ASC,
                            a.t_rank_order ASC,
                            a.idx DESC
                    ) as row_num
                ")
                ->selectRaw("COUNT(*) OVER (PARTITION BY it_id, t_it_id_type) AS it_cnt")
                ->addSelect([
                    'a.idx',
                    'a.it_id',
                    'a.t_p_code',
                    'a.t_specific_item',
                    'a.t_it_id_type',
                    'a.t_it_qty',
                    'a.t_receipt_date',
                    'a.t_return_date',
                    'b.it_name',
                    'b.it_img1',
                    'c.ca_name'
                ])
                ->join('g5_shop_item as b', 'a.it_id', '=', 'b.it_id')
                ->join('g5_shop_category as c', 'a.ca_id2', '=', 'c.ca_id')
                ->where('a.t_gubun', 'equipment')
                ->where('a.t_no', $mb_code)
                ->where('a.t_state', '2')
                ->whereIn('a.t_chk', ['', '1'])
                ->when($start_date && $end_date, function($query) use($start_date, $end_date) {
                    $query->whereBetween(DB::raw('DATE(a.t_receipt_date)'), [$start_date, $end_date]);
                })
                ->orderByRaw("
                    a.t_it_id_type = '7' ASC,
                    a.t_specific_item DESC,
                    a.t_it_id_type DESC,
                    a.it_id ASC,
                    a.t_rank_order ASC,
                    a.idx DESC
                ")
                ->get();

        return view('mypage.my_equipment_list', ['items' => $items]);
    }


     /**
     * method Name : MyPoint
     * Description : 충전금내역
     * Author : Kim Hairyong 
     * Created Date : 2026-01-26
     * Params : Params
     * History :
     *   - 2026-01-26 : Initial creation
     */
    public function MyPoint(Request $request)
    {
        $page    = $request->get('page', 1);
        $perPage = $request->get('scale', 20);

        $mb_code = session('ss_mb_code');

        if (!$mb_code) {
            throw new \Exception('로그인 사용자가 아닙니다.');
        }

        $start_date = $request->input('start_date');
        $end_date   = $request->input('end_date');

        if (!$start_date || !$end_date) {

            // 시작일 (2개월 전 1일)
            $start_date = \Carbon\Carbon::today()
                ->subMonths(2)
                ->startOfMonth()
                ->format('Y-m-d');

            // 종료일 (이달 마지막일)
            $end_date = \Carbon\Carbon::today()
                ->endOfMonth()
                ->format('Y-m-d');

        }

        $result = DB::table('g5_shop_order')
            ->selectRaw("
                od_idx,
                od_id,
                od_delivery_step,
                case
                    when pt_charge > 0 then 'decrease'
                    when pt_buy_charge > 0 then 'increase'
                end as po_point_type,
                CONCAT_WS('#',
                    IF(pt_charge > 0, 'pt_charge', NULL),
                    IF(pt_buy_charge > 0 AND od_delivery_step <> 8, 'pt_buy_charge', NULL),
                    IF(pt_buy_charge > 0 AND od_delivery_step = 8, 'modify', NULL)
                ) as po_action,
                pt_buy_charge,
                pt_charge,
                od_temp_point,
                case
                    when pt_buy_charge > 0 then pt_buy_charge
                    when pt_charge > 0 then pt_charge
                    when od_temp_point > 0 then od_temp_point
                end as change_point,
                pt_cur_charge,
                case
                    when pt_cur_charge > 0 then pt_cur_charge
                end as current_point,
                od_delivery_date
            ")
            ->where('mb_code', $mb_code)
            ->where(function($q) {
                $q->where(function($q2) {
                    $q2->where('pt_buy_charge', '>', 0)
                    ->where('od_delivery_step', '>=', 90);
                })
                ->orWhere('pt_charge', '>', 0);
            })
            ->when($start_date && $end_date, function($query) use($start_date, $end_date) {
                $query->whereBetween(DB::raw('od_delivery_date'), [$start_date, $end_date]);
            })
            ->orderByDesc('od_delivery_date')
            ->orderByDesc('od_id')
            ->paginate($perPage);

        $items = new LengthAwarePaginator(
            $result,
            $result->total(),
            $perPage,
            $page,
            [
                'path'  => Paginator::resolveCurrentPath(),
                'query' => $request->query(),
            ]
        );

    
        return view('mypage.my_point', [
            'items' => $items,
        ]);
    }



    /**
     * method Name : MyPointReserve
     * Description : 적립금내역
     * Author : Kim Hairyong 
     * Created Date : 2026-02-14
     * Params : Params
     * History :
     *   - 2026-02-14 : Initial creation
     */
    public function MyPointReserve(Request $request)
    {
        $page    = $request->get('page', 1);
        $perPage = $request->get('scale', 20);

        $mb_code = session('ss_mb_code');

        if (!$mb_code) {
            throw new \Exception('로그인 사용자가 아닙니다.');
        }

        $start_date = $request->input('start_date');
        $end_date   = $request->input('end_date');

        if (!$start_date || !$end_date) {

            // 시작일 (2개월 전 1일)
            $start_date = \Carbon\Carbon::today()
                ->subMonths(2)
                ->startOfMonth()
                ->format('Y-m-d');

            // 종료일 (이달 마지막일)
            $end_date = \Carbon\Carbon::today()
                ->endOfMonth()
                ->format('Y-m-d');

        }

        $result = DB::table('g5_shop_order')
            ->selectRaw("
                od_idx,
                od_id,
                od_delivery_step,
                case
                    when pt_reserve > 0 then 'decrease'
                    when pt_buy_reserve > 0 then 'increase'
                    when pt_cancel > 0 then 'increase'
                    when pt_return > 0 then 'increase'
                    when pt_outofstock > 0 then 'increase'
                    when pt_damage_staff > 0 then 'increase'
                    when pt_damage_logistic > 0 then 'increase'
                    when pt_incentive > 0 then 'increase'
                    when pt_dc > 0 then 'increase'
                    when pt_buy_reserve > 0 and od_delivery_step = 8 then 'increase'
                end as po_point_type,

                CONCAT_WS('#',
                    IF(pt_reserve > 0, 'pt_reserve', NULL),
                    IF(pt_buy_reserve > 0 AND od_delivery_step <> 8, 'pt_buy_reserve', NULL),
                    IF(pt_cancel > 0, 'pt_cancel', NULL),
                    IF(pt_return > 0, 'pt_return', NULL),
                    IF(pt_outofstock > 0, 'pt_outofstock', NULL),
                    IF(pt_damage_staff > 0, 'pt_damage_staff', NULL),
                    IF(pt_damage_logistic > 0, 'pt_damage_logistic', NULL),
                    IF(pt_incentive > 0, 'pt_incentive', NULL),
                    IF(pt_dc > 0, 'pt_dc', NULL),
                    IF(pt_buy_reserve > 0 AND od_delivery_step = 8, 'modify', NULL)
                ) as po_action,

                CASE
                    WHEN pt_reserve > 0 THEN pt_reserve                        
                    WHEN pt_buy_reserve > 0 THEN pt_buy_reserve
                    WHEN pt_cancel > 0 THEN pt_cancel                        
                    WHEN pt_incentive > 0 THEN pt_incentive
                    WHEN pt_outofstock > 0 THEN pt_outofstock
                    WHEN pt_dc > 0 THEN pt_dc
                    WHEN ABS(pt_subtotal) > 0 THEN ABS(pt_subtotal)
                END as change_point,    
                
                (pt_buy_reserve + pt_cancel + pt_return + pt_outofstock + pt_damage_staff + pt_damage_logistic + pt_incentive + pt_dc) as increase_point,
                pt_reserve as decrease_point,

                pt_reserve,
                pt_buy_reserve,
                pt_cancel,
                pt_return,
                pt_outofstock,
                pt_damage_staff,
                pt_damage_logistic,
                pt_incentive,
                pt_dc,
                od_temp_point_reserve,
                pt_subtotal,
                pt_cur_reserve,
                od_delivery_date
            ")
            ->where('mb_code', $mb_code)
            ->whereRaw("RIGHT(level_ca_id2, 1) = 1")
            ->where(function($q) {
                $q->where('pt_reserve', '>', 0)
                ->orWhere('pt_buy_reserve', '>', 0)
                ->orWhere('pt_cancel', '>', 0)
                ->orWhere('pt_return', '>', 0)
                ->orWhere('pt_outofstock', '>', 0)
                ->orWhere('pt_damage_staff', '>', 0)
                ->orWhere('pt_damage_logistic', '>', 0)
                ->orWhere('pt_incentive', '>', 0)
                ->orWhere('pt_dc', '>', 0);
            })

            ->when($start_date && $end_date, function($query) use($start_date, $end_date) {
                $query->whereBetween(DB::raw('od_delivery_date'), [$start_date, $end_date]);
            })

            ->orderByDesc('od_delivery_date')
            ->orderByDesc('od_id')
            ->paginate($perPage);

        $items = new LengthAwarePaginator(
            $result,
            $result->total(),
            $perPage,
            $page,
            [
                'path'  => Paginator::resolveCurrentPath(),
                'query' => $request->query(),
            ]
        );

        return view('mypage.my_point_reserve', [
            'items' => $items,
        ]);
    }




    /**
     * method Name : MyPointReserveDetailPop
     * Description : 적립금 상세내역 팝업
     * Author : Kim Hairyong 
     * Created Date : 2026-03-05
     * Params : Params
     * History :
     *   - 2026-03-05 : Initial creation
     */
    public function MyPointReserveDetailPop(Request $request)
    {
        $mode = $request->get('mode');
        $po_action = $request->get('po_action');
        $od_id = $request->get('od_id');
        $change_point = $request->get('change_point');

        $items = DB::table('g5_shop_order as so')
            ->leftJoin('g5_shop_cart as sc', 'so.od_id', '=', 'sc.od_id')
            ->leftJoin('g5_shop_item as si', 'sc.it_id', '=', 'si.it_id')
            ->select([
                DB::raw("'$po_action' as po_action"),
                'so.od_id',
                'so.pt_reserve',
                'so.pt_incentive',
                'so.pt_dc',
                'so.od_temp_point_reserve',
                'sc.pt_sales',
                'sc.it_id',
                'sc.it_name',
                'sc.ct_cate',
                'sc.it_gubun',
                'sc.ct_qty',
                'sc.ct_qty_tot',
                'si.it_img1',
                'si.it_basic',
            ])
            ->where('so.od_id', $od_id)
            ->whereRaw("IF(sc.ct_cate = '납품', so.od_temp_point_reserve > 0 or so.pt_reserve > 0 , 1=1)")
            ->orderByRaw("
                CASE sc.ct_cate
                    WHEN '납품'     THEN 1
                    WHEN '취소'     THEN 2
                    WHEN '반품'     THEN 3
                    WHEN '반품채권' THEN 4
                    WHEN '결품'     THEN 5
                    WHEN '결품입금' THEN 6
                    WHEN '기사파손' THEN 7
                    WHEN '물류파손' THEN 8
                    ELSE 99
                END ASC
            ")
            ->get();

            $groups = $items->groupBy('ct_cate')->map(function ($rows) use ($mode) {

                switch ($mode) {

                    case 'increase':
                        return $rows->filter(function ($row) {
                            return ($row->po_action == 'pt_buy_reserve')
                                || in_array($row->ct_cate, ['','취소','반품','결품','기사파손','물류파손']);    //장려금, DC 등이 포함되면 ct_cate 값이 없음
                        });


                    case 'bond':
                        return $rows->whereIn('ct_cate',['반품입금','반품채권','결품입금','결품채권']);

                    case 'decrease':
                        
                        return $rows->filter(function ($row) {
                            return ($row->po_action == 'od_use' || $row->po_action == 'pt_reserve')     //장려금, DC 등이 포함되면 ct_cate 값이 없음
                                || in_array($row->ct_cate, ['','납품']);
                        });

                    default:
                        return $rows;
                }

            })->filter();

        return view('mypage.my_point_reserve_detail_pop', [
            'mode' => $mode,
            'po_action' => $po_action,
            'od_id' => $od_id,
            'change_point' => $change_point,
            'items' => $items,
            'groups' => $groups,
        ]);        
    }


    /**
     * method Name : DepositHistory
     * Description : 입금내역
     * Author : Kim Hairyong 
     * Created Date : 2026-03-10
     * Params : Params
     * History :
     *   - 2026-03-10 : Initial creation
     */
    public function DepositHistory(Request $request)
    {
        $page    = $request->get('page', 1);
        $perPage = $request->get('scale', 20);

        $mb_code = session('ss_mb_code');

        if (!$mb_code) {
            throw new \Exception('로그인 사용자가 아닙니다.');
        }

        $start_date = $request->input('start_date');
        $end_date   = $request->input('end_date');

        if (!$start_date || !$end_date) {

            // 시작일 (2개월 전 1일)
            $start_date = \Carbon\Carbon::today()
                ->subMonths(2)
                ->startOfMonth()
                ->format('Y-m-d');

            // 종료일 (이달 마지막일)
            $end_date = \Carbon\Carbon::today()
                ->endOfMonth()
                ->format('Y-m-d');

        }

        $result = DB::table('g5_shop_order')
                    ->select([
                        'mb_code',
                        'mb_id',
                        'od_id',
                        'od_group_code',
                        'od_company',
                        'level_ca_id2',
                        'od_gubun',
                        'pt_cash',
                        'pt_bank',
                        'pt_card',
                        'pt_return_receivable',
                        'pt_outofstock_deposit',
                        'pt_return',
                        'pt_outofstock',
                        'pt_cancel',
                        'pt_incentive',
                        'pt_dc',
                        'pt_damage_staff',
                        'pt_damage_logistic',
                        'od_delivery_date'
                    ])

                    ->selectRaw("
                        IFNULL(pt_cash,0) +
                        IFNULL(pt_bank,0) +
                        IFNULL(pt_card,0) +
                        IFNULL(pt_return_receivable,0) +
                        IFNULL(pt_outofstock_deposit,0) +

                        (
                            CASE 
                                WHEN RIGHT(level_ca_id2, 1) = 2 THEN
                                    IFNULL(pt_incentive,0) +
                                    IFNULL(pt_dc,0) +
                                    IFNULL(pt_damage_staff,0) +
                                    IFNULL(pt_damage_logistic,0) +
                                    IFNULL(pt_return,0) +                
                                    IFNULL(pt_cancel,0) +
                                    IFNULL(pt_outofstock,0)
                                ELSE 0
                            END
                        ) as row_total
                    ")

                    ->where('mb_code', $mb_code)
                    ->where(function ($q) {
                        $q->where('pt_cash', '>', 0)
                        ->orWhere('pt_bank', '>', 0)
                        ->orWhere('pt_card', '>', 0)
                        ->orWhere('pt_return_receivable', '>', 0)
                        ->orWhere('pt_outofstock_deposit', '>', 0)
                        ->orWhere(function ($q2) {
                            $q2->whereRaw("RIGHT(level_ca_id2, 1) = '2'")
                                ->where(function ($q3) {
                                    $q3->where('pt_return', '>', 0)
                                        ->orWhere('pt_outofstock', '>', 0)
                                        ->orWhere('pt_cancel', '>', 0)
                                        ->orWhere('pt_incentive', '>', 0)
                                        ->orWhere('pt_dc', '>', 0)
                                        ->orWhere('pt_damage_staff', '>', 0)
                                        ->orWhere('pt_damage_logistic', '>', 0);
                                });
                        });
                    })

                    ->when($start_date && $end_date, function($query) use($start_date, $end_date) {
                        $query->whereBetween(DB::raw('od_delivery_date'), [$start_date, $end_date]);
                    })
                    ->orderBy('od_delivery_date', 'desc')
                    ->paginate($perPage);

        $items = new LengthAwarePaginator(
            $result,
            $result->total(),
            $perPage,
            $page,
            [
                'path'  => Paginator::resolveCurrentPath(),
                'query' => $request->query(),
            ]
        );

        return view('mypage.deposit_history', ['items' => $items]);

    }    



    /**
     * method Name : CheckPass
     * Description : 회원정보수정 비밀번호 및 회원 상태 검증
     * Author : Kim Hairyong 
     * Created Date : 2026-02-07
     * Params : Params
     * History :
     *   - 2026-02-07 : Initial creation
     */
    public function CheckPass(Request $request) 
    {
        $request->validate([
            'mb_password' => 'required',
        ],[
            'mb_password.required' => '비밀번호를 입력해주세요.',
        ]);

        $mb_pass = $request->input('mb_password');

        // 사용자 존재 확인
        $member = TbMember::select([
                'mb_num',
                'mb_code',
                'mb_id',
                'mb_password',
                'mb_state',
            ])
            ->where('mb_code', session('ss_mb_code'))
            ->first();

        // 비밀번호 확인
        if (md5($mb_pass) === $member->mb_password) {

            //회원 상태 확인
            $mb_state = $member->mb_state;  
            switch ($mb_state) {
                case 1: //승인된 아이디
                    break;
                case 0:
                    return redirect()->back()->with('info', '승인 대기중 입니다.');
                    break;
                case 2:
                    return redirect()->back()->with('info', '미승인된 아이디 입니다.');
                    break;
                case 3:
                    return redirect()->back()->with('info', '탈퇴 된 아이디 입니다.<br>재 가입을 원할 경우 <br>관리자에게 메일을 보내주세요.');
                    break;
                default:
                    return redirect()->back()->with('error', '알 수 없는 오류<br>관리자에게 연락 바랍니다.');
                    break;
            }

            //검증 완료 > 정보수정 페이지 이동
            return redirect()->route('register_edit');
        } else {
            return redirect()->back()->with('error', '비밀번호가 틀렸습니다.');
        }
    }


    /**
     * method Name : RegisterEdit
     * Description : 회원정보수정 페이지
     * Author : Kim Hairyong 
     * Created Date : 2026-02-07
     * Params : Params
     * History :
     *   - 2026-02-07 : Initial creation
     */
    public function RegisterEdit()
    {
        $mb_code = session('ss_mb_code');

        if (!$mb_code) {
            return redirect()->route('/')->with('info', '로그인 사용자가 아닙니다.');
        }

        $member = TbMember::select([
                    'mb_num',
                    'mb_code',
                    'mb_id',
                    'mb_name',
                    'mb_sex',
                    'mb_birth',
                    'mb_email',
                    'mb_addr1',
                    'mb_addr2',
                    'mb_hp',
                    'mb_tel',
                    'mb_company_no',
                    'mb_company_real',
                    'mb_job',
                    'mb_product',
                    'franchise_ca_id',
                    'franchise_ca_id2',
                    'manager_name',
                    'mb_notice_receive',
                    'manager_tel2',
                    'mb_add_channel',
                    'mb_account',
                    'mb_bank_code',
                    'mb_account_holder',
                    'mb_account_files1',
                    'mb_sms',
                    'mb_mailling',
                    'mb_introduce',
                ])
                ->where('mb_code', $mb_code)
                ->first();   
                

        //폼 세팅용 데이터 가공
        $form_data = [];
        // 이메일
        $form_data['mb_email'] = array_pad(explode('@', $member->mb_email ?? ''), 2, '');
        // 휴대폰번호
        $form_data['mb_hp'] = array_pad(explode('-', $member->mb_hp ?? ''), 3, '');
        // 전화번호
        $form_data['mb_tel'] = array_pad(explode('-', $member->mb_tel ?? ''), 3, '');
        // 점장 휴대폰번호
        $form_data['manager_tel2'] = array_pad(explode('-', $member->manager_tel2 ?? ''), 3, '');

        $ca_id_length = 2;
        $items = [
            'member' => $member,
            'franchise_category' => $this->CategoryService->FranchiseCategory($ca_id_length),
            'form_data' => $form_data,
        ];

        return view('mypage.register_edit', ['items' => $items]);
        
    }


     /**
     * method Name : DownloadAccountFile
     * Description : 회원정보수정 페이지 통장사본 파일 다운로드 (파일저장소가 SFTP 원격서버인 관계로 스트리밍방식 사용)
     * Author : Kim Hairyong 
     * Created Date : 2026-02-09
     * Params : Params
     * History :
     *   - 2026-02-09 : Initial creation
     */
    public function DownloadAccountFile(Request $request)
    {
        $request->validate([
            'mb_code' => 'required',
        ],[
            'mb_code.required' => '필수 정보가 없습니다.',
        ]);    

        $mb_code = $request->input('mb_code');

        //보안 이슈로 파일정보를 파라미터로 넘기지 않고 DB 직접조회 처리함
        $row = DB::table('tb_member')
                    ->select('mb_account_files1')
                    ->where('mb_code', $mb_code)
                    ->first();

        $folder = '/common_data/member/';
        $files = unserialize($row->mb_account_files1);
        

        $file = $files[0];
        $file_name  = $file['name'];
        $file_sname = $file['sname'];
        $filePath   = $folder . $file_sname;        

        if (!Storage::disk('sftp_remote')->exists($filePath)) {
            return redirect()->back()->with('error', $file_sname . ' 파일이 존재하지 않습니다.');
        }

        // 파일 다운로드 횟수 업데이트
        foreach ($files as $key => $file) {
            $files[$key]['hits'] = ($files[$key]['hits'] ?? 0) + 1;
        }
        unset($file);

        DB::table('tb_member')
            ->where('mb_code', $mb_code)
            ->update([
                'mb_account_files1' => serialize($files),
            ]);

        return response()->streamDownload(function () use ($filePath) {

            $stream = Storage::disk('sftp_remote')->readStream($filePath);

            try {
                fpassthru($stream); // 파일 스트림 전송
            } finally {
                fclose($stream);    // 리소스 누수 방지
            }

        }, $file_name);        

    }


    /**
     * method Name : RegisterEditSave
     * Description : 회원정보수정 처리
     * Author : Kim Hairyong 
     * Created Date : 2026-02-09
     * Params : Params
     * History :
     *   - 2026-02-07 : Initial creation
     */
    public function RegisterEditSave(Request $request)
    {  
        $mb_code = session('ss_mb_code');

        if (!$mb_code) {
            return redirect()->route('/')->with('info', '로그인 사용자가 아닙니다.');
        }        

        //기본 데이터 정리
        $nowTime = time();
        $nowDate = date('Y-m-d H:i:s');

        $mb_email = implode('@', [
            $request->input('email1'),
            $request->input('email2'),
        ]);        

        $mb_hp = implode('-', [
            $request->input('mb_hp1'),
            $request->input('mb_hp2'),
            $request->input('mb_hp3'),
        ]);

        $mb_hp_num = implode('', [
            $request->input('mb_hp1'),
            $request->input('mb_hp2'),
            $request->input('mb_hp3'),
        ]);

        $mb_tel = implode('-', [
            $request->input('mb_tel1'),
            $request->input('mb_tel2'),
            $request->input('mb_tel3'),
        ]);

        $manager_tel2 = implode('-', [
            $request->input('manager_tel21'),
            $request->input('manager_tel22'),
            $request->input('manager_tel23'),
        ]);

        if($mb_hp=='--') $mb_hp='';
        if($mb_tel=='--') $mb_tel='';
        if($manager_tel2=='--') $manager_tel2='';

        //나이
        $mb_age = '';
        $mb_birth = $request->input('mb_birth');
        if($mb_birth != '') {
            $arr_birth = explode("-",$mb_birth);
            $mb_age = (date("Y")-$arr_birth[0])+1;
        }

        //은행
        $mb_bank_code = '';
        $mb_bank = $request->input('mb_bank');      
        if($mb_bank != '') {
            $arr_mb_bank = explode('/', $mb_bank);
            $mb_bank_code = $arr_mb_bank[0];
            $mb_bank_name = $arr_mb_bank[1];
        }


        $updateData = [
            'mb_name' => $request->input('mb_name'),
            'mb_sex' => $request->input('mb_sex'),
            'mb_birth' => $request->input('mb_birth'),
            'mb_email' => $mb_email,
            'mb_hp' => $mb_hp,
            'mb_hp_num' => $mb_hp_num,
            'mb_tel' => $mb_tel,
            'manager_tel2' => $manager_tel2,
            'mb_job' => $request->input('mb_job'),
            'mb_product' => $request->input('mb_product'),
            'franchise_ca_id' => $request->input('franchise_ca_id'),
            'franchise_ca_id2' => $request->input('franchise_ca_id2'),
            'mb_notice_receive' => $request->input('mb_notice_receive'),
            'manager_name' => $request->input('manager_name'),
            'manager_tel2' => $manager_tel2,
            'mb_add_channel' => $request->input('mb_add_channel'),
            'mb_bank_code' => $mb_bank_code,
            'mb_bank' => $mb_bank_name,
            'mb_account' => $request->input('mb_account'),
            'mb_account_holder' => $request->input('mb_account_holder'),
            'mb_sms' => $request->input('mb_sms'),
            'mb_mailling' => $request->input('mb_mailling'),
            'mb_introduce' => $request->input('mb_introduce'),
        ];

        // 비밀번호 변경 시 적용
        $user_pass = $request->input('user_pass');
        if ($user_pass !== null && $user_pass !== '') {

            //비밀번호 MD5
            $user_pass_md5   = md5($user_pass);

            $updateData['mb_password'] = $user_pass_md5;
            $updateData['mb_password_text'] = $user_pass;

        }

        // 마이페이지 비밀번호 변경 시 적용
        $mb_mypage_password = $request->input('mb_mypage_password');
        if ($mb_mypage_password !== null && $mb_mypage_password !== '') {

            $updateData['mb_mypage_password'] = $mb_mypage_password;

        }

        // 통장사본 첨부파일 변경 시 적용
        $account_file_changed = $request->input('account_file_changed');
        if ($account_file_changed == 1) {

            $service = app(MallShopService::class);
            $member = $service->getMemberInfo(session('ss_mb_code'));

            // 통장사본 첨부파일 처리
            $folder = '/common_data/member/';
            $files = $request->file('mb_account_files1');        
            $mb_company = $member['mb_company'];

            // 첨부파일 없음 → 기존 테이블 규칙 유지
            if (empty($files)) {

                $mb_account_files1 = serialize(false); // b:0;

            } else {

                $data = [];
                foreach ($files as $key => $file) {
                    $data[$key] = [
                        'name'  => $mb_company. '_' . $file->getClientOriginalName(),
                        'type'  => $file->getMimeType(),
                        'size'  => $file->getSize(),
                        'hits'  => 0,
                        'sname' => sprintf('%04d', $mb_code) . '_' . mt_rand(100, 999) . "_{$key}.file",
                    ];

                    Storage::disk('sftp_remote')
                        ->put($folder.$data[$key]['sname'], file_get_contents($file));                        
                }

                //첨부파일 정보 serialize (이전 쇼핑몰의 회원가입 시 진행됐던 파일정보 serialize 처리 로직을 승계함)
                $mb_account_files1 = serialize($data);

            }

            $updateData['mb_account_files1'] = $mb_account_files1;

        }

        // 수정된 정보 저장
        DB::table('tb_member')
            ->where('mb_code', $mb_code)
            ->update($updateData);            

        // 회원 캐시 삭제
        Redis::del(session('ss_mb_code').':member');

        return redirect()->route('register_edit')->with('success', '회원정보가 정상적으로 수정되었습니다.');    

    }  


}