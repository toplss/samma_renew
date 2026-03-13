@extends('layouts.header')

@section('content')

@php
$ca_id  = request()->input('ca_id');
$type   = request()->input('type');

$isDown = false;
switch($ca_id) {
    case '20' : 
        if ($type == 'low_temp') $isDown = false;
        else {
            $isDown = true;
        }
        break;
    case '70' :
        if ($type === 'low_temp') $isDown = true;
        break;
}


if (request()->has('page'))  $isDown = false;

@endphp
<div class="sub-container {{ $isDown ? 'down' : '' }}">

    <!-- 카테고리 영역 -->
    <div class="sub-title-wrap">
    @if (request()->is('mall/shop/list_gubun_accessory*'))
        <div class="st-left">
            <h4>A/S부속품</h4>
        </div>
        <ul class="st-right">
            <li>
                <select name="desc" id="desc" onchange="ShopItemOrderByDesc(this.value)">
                    <option value="1">정렬</option>
                    <option value="2" {{ request('desc') == '2' ? 'selected' : '' }} >낮은가격순</option>
                    <option value="3" {{ request('desc') == '3' ? 'selected' : '' }} >높은가격순</option>
                    <option value="4" {{ request('desc') == '4' ? 'selected' : '' }} >판매량순</option>
                    <option value="5" {{ request('desc') == '5' ? 'selected' : '' }} >최신순</option>
                </select>
            </li>
            <li>
                <select name="scale" id="scale" onchange="ShopItemOrderByScale(this.value)">
                    <option value="60" {{ request('scale') == 60 ? 'selected' : '' }}>리스트 60개</option>
                    <option value="120" {{ request('scale') == 120 ? 'selected' : '' }}>리스트 120개</option>
                    <option value="180" {{ request('scale') == 180 ? 'selected' : '' }}>리스트 180개</option>
                    <option value="240" {{ request('scale') == 240 ? 'selected' : '' }}>리스트 240개</option>
                    <option value="300" {{ request('scale') == 300 ? 'selected' : '' }}>리스트 300개</option>
                </select>
            </li>
            <li id="gridType"><img src="{{ asset('images/icon/grid.svg') }}"></li>
            <li id="rowType"><img src="{{ asset('images/icon/list.svg') }}"></li>
        </ul>
    @else
        @include('layouts.category')
    @endif
    </div>
    <!-- 카테고리 영역 -->

    <!-- 배너 영역 -->
    <div class="sub-list-slide">
        {!! $items->other['banner'] !!}
    </div>
    <!-- 배너 영역 -->
    
    <div class="prd-row-head">
        <ul>
            <li>상품명</li>
            <li>판매가</li>
            <li class="hide-680">유형</li>
            <li class="hide-680">수량</li>
        </ul>
    </div>
    <div class="prd-list">
        @foreach ($items as $key => $row)

        <!-- 품절체크 -->
        @php
        $sold_out = $row['it_soldout'] == '1' || $row['it_force_soldout'] == '10' ? true : false;
        $it_id    = $row['it_id'];
        $min_cart_ct_qty = $box_min_qty = $max_cart_ct_qty = 0;

        
        if(isset($activeMember['mb_level']) && substr($activeMember['mb_level'], 0, 2) == '30' && $row['agency_it_buy_min_qty'] > 0) {
            $min_cart_ct_qty = $row['agency_it_buy_min_qty']; // 주문최소
            $box_min_qty     = ($row['it_gubun'] == 'pack') ? $row['it_box_sale_pack'] : $row['it_box_sale_tot']; // 박스구매
            $max_cart_ct_qty = $row['agency_it_buy_max_qty']; // 최대치 구매

        } else {
            $min_cart_ct_qty = $row['it_buy_min_qty'];
            $box_min_qty     = ($row['it_gubun'] == 'pack') ? $row['it_box_sale_pack'] : $row['it_box_sale_tot']; // 박스구매
            $max_cart_ct_qty = $row['it_buy_max_qty']; // 최대치 구매
        }

        $image_url = 'images/item/'.$row['it_img1'];
        
        @endphp
            <ul class="prd-box" data-item="{{ $it_id }}">
                @if(file_exists(public_path($image_url)) && $row['it_img1'])
                <li class="prd-img  {{ $sold_out ? 'sold-out' : '' }}" onclick="location.href = '/mall/shop/view?it_id={{ $it_id }}'; "><img src="{{ asset($image_url) }}"></li>
                @else
                <li class="prd-img  {{ $sold_out ? 'sold-out' : '' }}" onclick="location.href = '/mall/shop/view?it_id={{ $it_id }}'; "><img src="{{ asset('images/common/no_image.gif') }}"></li>
                @endif
                <li class="Qua {{ $sold_out ? 'sold-out' : '' }}">
                    @if($activeMember)
                    <p class="pm-wrap">
                        <button type="button" class="sit_qty_minus" ><img src="{{ asset('images/icon/minus.svg') }}"></button>
                        <span id="numberUpDown">
                            <input type="hidden" name="min_ct_qty" class="min_ct_qty"  value="{{ $min_cart_ct_qty }}" readonly>
                            <input type="text" name="ct_qty" class="ct_qty"  value="{{ $min_cart_ct_qty }}" readonly>
                        </span>
                        <button type="button" class="sit_qty_plus" ><img src="{{ asset('images/icon/plus.svg') }}"></button>
                    </p>
                    <button type="button" class="add-to-cart" >담기</button>
                    @else
                    <p class="Qua-login">로그인 후 이용가능합니다.</p>
                    @endif
                </li>
                <li class="prd-info">
                    @if($row['it_gubun'] !== 'box')
                    <input type="hidden" class="it_gubun" value="{{ $row['it_gubun'] }}" />
                    <input type="hidden" class="buy_box_qty" value="{{ $box_min_qty }}" />
                    <input type="hidden" class="it_box_sale_pcs" value="{{ $row['it_box_sale_pcs'] }}" />
                    <input type="hidden" class="it_box_sale_pack" value="{{ $row['it_box_sale_pack'] }}" />
                    <input type="hidden" class="it_box_sale_tot" value="{{ $row['it_box_sale_tot'] }}" />

                        @if ($row['it_box_sale_pcs'])
                        <button type="button" class="btn2" id="buy_box_btn" onclick="buy_box_qty(this)">박스구매</button>
                        @endif
                    @endif
                    <p class="pin">
                        @php
                        $timpArr = ['1' => 'room_temp', '3' => 'low_temp', '2' => 'frozen_temp', '4' => ''];
                        @endphp
                        <span class="{{ $timpArr[$row['it_storage']] }}">{{ $row['it_storage_label'] }}</span><!-- 상온:room_temp, 냉장:low_temp 냉동:frozen-temp  -->
                        <span class="{{ $row['it_return_label'] == '반품가능' ? 'return_o' : 'return_x' }}">{{ $row['it_return_label'] }}</span>
                    </p>
                </li>
                <li class="prd-name">
                    {{ $row['it_name'] }}
                    <p class="ea">({{ $row['it_basic'] }}*{{ $row['it_gubun_label'] }})</p>
                </li>

                @if($activeMember)
                <li class="prd-price">
                    <x-mall.item-price
                        :row="$row"
                        :member="$activeMember"
                        :qty="$min_cart_ct_qty"
                    />
                </li>
                @endif
            </ul>
        @endforeach
    </div>

    {{ $items->links() }}
</div>
<script>
function ShopItemOrderByScale(e) {
    var params = new URLSearchParams(window.location.search);

    params.set('scale', e);

    location.search = params.toString();
}

function ShopItemOrderByDesc(e) {
    var params = new URLSearchParams(window.location.search);

    params.set('desc', e);

    location.search = params.toString();
}    
</script>

@endsection


