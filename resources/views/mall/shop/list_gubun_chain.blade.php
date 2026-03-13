@extends('layouts.header')

@section('content')


<div class="sub-container {{ request()->path() === 'mall/shop/list_gubun_chain' && empty(request()->query()) ? 'down' : '' }}">

    <!-- 카테고리 영역 -->
    <div class="sub-title-wrap">
        @include('layouts.category')
    </div>
    <!-- 카테고리 영역 -->

    <!-- 배너 영역 -->
    <div class="sub-list-slide">

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
                    <x-mall.item-price :row="$row" :member="$activeMember" :qty="$min_cart_ct_qty"/>
                </li>
                @endif
            </ul>
        @endforeach
    </div>

    {{ $items->links() }}
</div>


@endsection


