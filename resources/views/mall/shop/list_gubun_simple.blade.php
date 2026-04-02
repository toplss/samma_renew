@extends('layouts.header')

@section('content')


<div class="sub-container">

    <!-- 카테고리 영역 -->
    <div class="sub-title-wrap">
        @include('layouts.category_simple')
    </div>
    <!-- 카테고리 영역 -->

    <!-- 배너 영역 -->
    <div class="sub-list-slide">
        {!! $banner !!}
    </div>
    <!-- 배너 영역 -->
    
    <div class="prd-row-head simple">
        <ul>
            <li><input type="checkbox" name="" id="simple_all"></li>
            <li>상품명</li>
            <li>판매가</li>
            <li class="hide-680">유형</li>
            <li class="hide-680">수량</li>
        </ul>
    </div>

    <div class="flex sab">
        <label class="simple-all-btn"><input type="checkbox" name="" id="simple_all2">전체 선택</label>
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
            <ul class="prd-box simple" data-item="{{ $it_id }}">
                <li class="prd-check"><input type="checkbox" name="cart_it_id[]" class="cart_it_id" value="{{ $row['it_id'] }}"></li>
                @php
                    $timpArr = ['1' => 'room_temp', '3' => 'low_temp', '2' => 'frozen_temp', '4' => ''];
                @endphp
                @if(file_exists(public_path($image_url)) && $row['it_img1'])
                <li class="prd-img  {{ $sold_out ? 'sold-out' : '' }}" onclick="location.href = '/mall/shop/view?it_id={{ $it_id }}'; ">
                    <img src="{{ asset($image_url) }}">
                    <span class="{{ $timpArr[$row['it_storage']] }}"><img src="{{ asset('images/icon/snow.svg') }}">{{ $row['it_storage_label'] }}</span>
                </li>
                @else
                <li class="prd-img  {{ $sold_out ? 'sold-out' : '' }}" onclick="location.href = '/mall/shop/view?it_id={{ $it_id }}'; ">
                    <img src="{{ asset('images/common/no_image.gif') }}">
                </li>
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
                        <span class="{{ $timpArr[$row['it_storage']] }}">{{ $row['it_storage_label'] }}</span>
                        @if($row['it_return_use'] == '1')
                        <span class="{{ $row['it_return_label'] == '반품가능' ? 'return_o' : 'return_x' }}">{{ $row['it_return_label'] }}</span>
                        @endif
                    </p>
                </li>
                <li class="prd-name">
                    {{ $row['it_name'] }}

                    @php
                    if ($row['it_price_piece_use']) {
                        $it_price_piece = $row[$activeMember['field_it_price_unit']];
                    } else {
                        $it_price_piece = 0;
                    }
                    @endphp
                    <p class="ea">({{ $row['it_basic'] }}*{{ $row['it_gubun_label'] }})
                        @if($it_price_piece > 0 && $row['it_price_piece_use'])
                        <i>/ 개당 {{ number_format($it_price_piece) }}원</i>
                        @endif
                    </p>
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

    @if ($items->total() < 1)
        <div style="margin-top: 50px;">
            <ul>
                <li>상품이 없습니다.</li>
            </ul>
        </div>
    @endif
    
</div>

<script>
$(document).ready(function(){
    $('#simple_all,#simple_all2').change(function() {
        if ($(this).is(':checked')) {
            $('.cart_it_id:not(:disabled)').prop('checked', true);
        } else {
            $('.cart_it_id:not(:disabled)').prop('checked', false);
        }
    });
});
</script>

@endsection


