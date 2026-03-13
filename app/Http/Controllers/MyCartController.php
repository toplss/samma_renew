<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\ShopCartApi;
use App\Models\ShopCart;
use App\Models\ShopOrderModel;
use App\Services\ShopOrderService;
use Debugbar;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class MyCartController extends Controller
{
    //

    public function MyCartList(Request $request)
    {
        $request->merge(['mode' => 'show', 'path' => '/mypage/cart']);

        $items = json_decode(app(ShopCartApi::class)->execute($request), true)['data'];

        $itemsAll = [];

        foreach ($items['cart_items'] as $key => $row) {
            foreach ($items['cart_items'][$key] as $ikey => $irow) {
                $itemsAll[] = $irow;
            }
        }

        $itemsAll = collect($items['cart_items'])
        ->flatMap(function ($group) {
            return $group;
        })

        // ->sortBy('it_storage')   // 정렬 기준 컬럼

        ->sortBy(function ($item) {
            return [
                $item['it_force_soldout'],
                $item['it_soldout'],
                $item['it_storage']
            ];
        })

        ->values()          // index 재정렬
        ->toArray();

        $items['cartAllItems'] = $itemsAll;

        return view('mypage.cart', compact('items'));
    }


    public function MyOrderCancel(Request $request)
    {
        $request->validate([
            'od_id' => 'required|exists:g5_shop_order,od_id',
            'move_type' => 'required'
        ], [
            'od_id.required' => '주문번호가 필요합니다.',
            'od_id.exists' => '유효하지 않은 주문번호입니다.',

            'move_type.required' => '취소유형이 옳바르지 않습니다.',
        ]);

        try {
            $od_id      = $request->od_id;
            $move_type  = $request->move_type;

            $data = DB::transaction(function () use ($od_id, $move_type) {

                $service = app(ShopOrderService::class);

                $order = ShopOrderModel::where('od_id', $od_id)
                    // ->lockForUpdate()
                    ->select('od_id', 'od_delivery_step', 'od_status')
                    ->first();
            
                if (!$order) {
                    throw new \Exception('주문이 존재하지 않습니다.');
                }
            
                if ($order->od_delivery_step > 0 || $order->od_status !== '주문') {
                    throw new \Exception('포장 준비중입니다. 고객센터 1588-3153 문의 바랍니다.');
                }

                $cart_cnt = ShopCart::where('od_id', $od_id)
                    ->whereNotIn('ct_status', ['쇼핑'])
                    // ->lockForUpdate()
                    ->count();
            
                if ($cart_cnt == 0) {
                    throw new \Exception('상품정보가 존재하지 않습니다.');
                }

                if (!$service->execute($od_id, $move_type)) {
                    throw new \Exception('취소중 오류가 발생 하였습니다. 다시 시도해 주세요.');
                }
            
                return $order->toArray();
            });

            Redis::del(session('ss_mb_code').':member');

            return response()->json(['status' => 'success', 'data' => $data], 200);

        } catch (\Exception $e) {

            return response()->json(['status' => 'fail', 'message' => $e->getMessage()], 500);
        }
    }
}
