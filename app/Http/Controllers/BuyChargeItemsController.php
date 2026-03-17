<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\ShopCartApi;
use App\Models\ShopItem;
use App\Services\MallShopService;
use Illuminate\Http\Request;
use App\Traits\RedisTrait;
use Barryvdh\Debugbar\Facades\Debugbar;
use Illuminate\Support\Facades\Redis;
use App\Facades\Sso;
use App\Models\ShopOrderModel;
use App\Services\BannerService;
use App\Traits\CommonTrait;
use Exception;

class BuyChargeItemsController extends Controller
{
    //
    use RedisTrait, CommonTrait;


    /**
     * method Name : ListCharege
     * Description : 충전금 구매
     * Created Date : 2026-01-19
     * Params : Params
     * History :
     *   - 2026-01-19 : Initial creation
     */
    public function ListCharege(Request $request)
    {
        $member = app(MallShopService::class)->getMemberInfo(session('ss_mb_code'));
        if(substr($member['level_ca_id2'], -1) == '2') {
            return redirect()->back()->with('info', '충전금 구매 권한이 없습니다.');
        }

        $redis_key_generate = 'shop:buy:charge';

        if (Redis::exists($redis_key_generate)) {
            
            $items = self::getReids($redis_key_generate);

        } else {
            $items = ShopItem::select('it_id', 'it_price', 'it_name', 'it_cust_rate', 'it_img1')
            ->where('ca_id', '10')
            ->where('it_use', '1')
            ->where('it_soldout', '0')
            ->where('it_force_soldout', '0')
            ->orderByRaw('it_price * 1 asc')
            ->orderBy('idx', 'asc')
            ->get()->toArray();

            self::setRedis($redis_key_generate, $items);
        }


        $redis_key_generate = 'charge:sub_banner';

        if (Redis::exists($redis_key_generate)) {
            $subBanner = self::getReids($redis_key_generate);
        } else {
            self::setRedis($redis_key_generate, $subBanner = app(BannerService::class)->getChargeSubBanner());
        }

        $wait_order_charge = 0;
        if (session('ss_mb_code')) {
            $wait_order_charge = ShopOrderModel::where('mb_code', session('ss_mb_code'))
            ->where('od_status', '주문')
            ->where('od_settle_case', '금융권가상계좌')
            ->where('od_delivery_step', '0')
            ->value('od_cart_price');
        }
        

        return view('mall.shop.list_charge', [
            'items' => $items, 
            'od_id' => '', 
            'wait_order_charge' => $wait_order_charge,
            'subBanner' => $subBanner
        ]);
    }



    public function BuyChareItem(Request $request)
    {
        $request->merge([
            'mode'   => 'charge_insert', 
            'ct_qty' => 1
        ]);

        if (!session('ss_mb_code')) {
            return redirect('/')->with('error', '로그인 후 이용 가능합니다.');
        }

        $service = app(MallShopService::class);
        $member  = $service->getMemberInfo(session('ss_mb_code'));

        if (!$member) {
            return redirect('/')->with('error', '회원정보가 존재하지 않습니다.');
        }

        $api = app(ShopCartApi::class);
        $res = json_decode($api->execute($request), true);

        if ($res['status'] == 'fail') {
            return redirect()
            ->route('list_charge')
            ->with('error', $res['message']);
        }

        // $redis_key_generate = 'shop:buy:charge';

        // $items = self::getReids($redis_key_generate);

        // $od_id = $this->encrypted($res['res']);
        
        // if (!Sso::setChareItem($od_id)) {
        //     throw new Exception('죄송합니다. 충전금 구매 신청이 실패되었습니다. 잠시 후 다시 시도해 주세요.');
        // }

        // return view('mall.shop.charge_update', [
        //     'items' => $items, 
        //     'od_id' => $od_id
        // ]);


        return redirect()->route('payment.request', ['od_id' => $res['res'], 'order_type' => 'charge']);
    }

}
