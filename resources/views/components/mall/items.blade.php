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


$image_url  = 'images/item/'.$row['it_img1'];

if ($activeMember) {
    $price      = (int) str_replace(',', '', $row[$activeMember['field_it_price']] ?? 0);
    $qty        = (int) $min_cart_ct_qty;

    $row_list_field_it_price = $price * $qty;
}

@endphp
<div class="recom-item-list">
    <ul data-item="{{ $it_id }}">
        @if(!Storage::disk('public')->exists($image_url) && $row['it_img1'])
        <li class="recom-img  {{ $sold_out ? 'sold-out' : '' }}" onclick="location.href = '/mall/shop/view?it_id={{ $it_id }}'; "><img src="{{ asset($image_url) }}"></li>
        @else
        <li class="recom-img  {{ $sold_out ? 'sold-out' : '' }}" onclick="location.href = '/mall/shop/view?it_id={{ $it_id }}'; "><img src="{{ asset('images/common/no_image.gif') }}"></li>
        @endif
        <li class="recom-pin">
        <span class="{{ $timpArr[$row['it_storage']] }}">{{ $row['it_storage_label'] }}</span>
        <span class="{{ $row['it_return_label'] == '반품가능' ? 'return_o' : 'return_x' }}">{{ $row['it_return_label'] }}</span>
        </li>
        <li class="recom-name">{{ $row['it_name'] }}</li>
        <li class="recom-ea">{{ $row['it_basic'] }}</li>
        @if($activeMember)
        <li class="recom-price">{{ number_format($row_list_field_it_price) }}원</li>

        <li class="pm-wrap">
            <button type="button" class="sit_qty_minus" ><img src="{{ asset('images/icon/minus.svg') }}"></button>
            <span id="numberUpDown">
                <input type="hidden" name="min_ct_qty" class="min_ct_qty"  value="{{ $min_cart_ct_qty }}" readonly>
                <input type="text" name="ct_qty" class="ct_qty"  value="{{ $min_cart_ct_qty }}" readonly>
            </span>
            <button type="button" class="sit_qty_plus" ><img src="{{ asset('images/icon/plus.svg') }}"></button>
        </li>
        <li>
        @if($row['it_gubun'] !== 'box')
            <input type="hidden" class="it_gubun" value="{{ $row['it_gubun'] }}" />
            <input type="hidden" class="buy_box_qty" value="{{ $box_min_qty }}" />
            <input type="hidden" class="it_box_sale_pcs" value="{{ $row['it_box_sale_pcs'] }}" />
            <input type="hidden" class="it_box_sale_pack" value="{{ $row['it_box_sale_pack'] }}" />
            <input type="hidden" class="it_box_sale_tot" value="{{ $row['it_box_sale_tot'] }}" />
            @if($row['ca_id'] !== 'd0')
            <button type="button" class="btn2" id="buy_box_btn" onclick="buy_box_qty(this)">박스<span class="hide-680">구매</span></button>
            @endif
        @endif
            @if($row['ca_id'] !== 'd0')
                <button type="button" class="add-to-cart btn3" >담기</button>
            @else
                <button type="button" class="qa_btn btn3" >전화문의</button>
            @endif
        </li>
        
        @else
        <p class="Qua-login">로그인 후 이용가능합니다.</p>
        @endif
    </ul>
</div>