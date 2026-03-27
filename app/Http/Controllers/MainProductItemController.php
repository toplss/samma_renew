<?php

namespace App\Http\Controllers;

use App\Services\ShopItemService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ShopItem;
use App\Services\MallShopService;

class MainProductItemController extends Controller
{
    //

    public function show(Request $request)
    {
        try {
            $request->validate([
                'it_id' => 'required|exists:g5_shop_item,it_id'
            ], [
                'it_id.required' => '상품코드가 존재하지 않습니다.',
                'it_id.exists' => '존재하지 않는 상품입니다.'
            ]);

            if (!session('ss_mb_code')) {
                throw new Exception("로그인 후 이용 가능합니다.");
            }
    
            $it_id = $request->input('it_id');


            $member = app(MallShopService::class)->getMemberInfo(session('ss_mb_code'));
    
            $items = ShopItem::stockLeftJoinSub()
            ->leftJoin('tb_tmp_selection_chain_product as tcp', function ($join) {
                $join->on('g5_shop_item.it_id', '=', 'tcp.it_id');
            })
            ->where('g5_shop_item.it_id', $it_id)
            ->select(
                app(ShopItemService::class)::selected()
            )
            ->first();

            $sold_out = $items->it_soldout == '1' || $items->it_force_soldout == '10' ? true : false;
            
            $min_cart_ct_qty = $box_min_qty = $max_cart_ct_qty = 0;

            if(isset($member['mb_level']) && substr($member['mb_level'], 0, 2) == '30' && $items->agency_it_buy_min_qty > 0 || $member['mb_branch_gubun_type'] == '3') {
                $min_cart_ct_qty = $items->agency_it_buy_min_qty; // 주문최소
                $box_min_qty     = ($items->it_gubun == 'pack') ? $items->it_box_sale_pack : $items->it_box_sale_tot; // 박스구매
                $max_cart_ct_qty = $items->agency_it_buy_max_qty;
    
            } else {
                $min_cart_ct_qty = $items->it_buy_min_qty;
                $box_min_qty     = ($items->it_gubun == 'pack') ? $items->it_box_sale_pack : $items->it_box_sale_tot; // 박스구매
                $max_cart_ct_qty = $items->it_buy_max_qty; // 최대치 구매
            }
    
            $image_url = $items->it_img1 ? '/images/item/'.$items->it_img1 : '/images/common/no_image.gif';

            // 보관유형
            $timpArr   = ['1' => 'room_temp', '3' => 'low_temp', '2' => 'frozen_temp', '4' => ''];

            $cust_price_field = str_replace('it_', 'cust_', $member['field_it_price']);
            $cust_price = (int) str_replace(',', '', $items->{$cust_price_field} ?? 0);
            $price = (int) str_replace(',', '', $items->{$member['field_it_price']} ?? 0);
            $qty   = (int) $min_cart_ct_qty;

            $row_list_field_it_price = $price * $qty;
            $row_list_cust_it_price  = $cust_price * $qty;


            $response = [
                'login'   => (session('ss_mb_code')) ? true : false,
                'img_url' => $image_url,
                'it_id'   => $it_id,
                'it_storage' => $timpArr[$items->it_storage],
                'it_storage_label' => $items->it_storage_label,
                'it_return_use'    => $items->it_return_use,
                'it_return_label'  => $items->it_return_label,
                'it_price_piece_use' => $items->it_price_piece_use,
                'it_price_piece'     => $items->{$member['field_it_price_unit']},
                'it_basic'=> '<s>'.$items->it_basic.'*'.$items->it_gubun_label.'</s>',
                'it_name' => $items->it_name,
                'it_gubun'=> $items->it_gubun,
                'sold_out'=> $sold_out,

                'min_ct_qty'  => $min_cart_ct_qty,
                'ct_qty'      => $min_cart_ct_qty,
                'buy_box_qty' => $box_min_qty,
                'it_box_sale_pcs' => $items->it_box_sale_pcs,
                'it_box_sale_pack'=> $items->it_box_sale_pack,
                'it_box_sale_tot' => $items->it_box_sale_tot,

                'price'        => $price,
                'cust_price'   => $cust_price, 
                'it_cust_rate' => $items->it_cust_rate,
                'row_list_cust_it_price' => $row_list_cust_it_price,
                'row_list_field_it_price'=> $row_list_field_it_price,
            ];

            return response()->json(['status' => 'success', 'message' => '', 'data' => $response]);
    
        } catch (Exception $e) {
            
            return response()->json(['status' => 'fail', 'message' => $e->getMessage(), 'data' => []]);
        }
    }
}
