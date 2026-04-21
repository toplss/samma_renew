@php
$sold_out = false;
if ($row['event_status'] == 'SOLD_OUT' || $row['event_status'] == 'END') {
    $sold_out = true;
}
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

<div class="sale-item-list">
    <ul class="{{ $sold_out ? 'end' : '' }}">
        @if(file_exists(public_path($image_url)) && $row['it_img1'])
        <li class="si-img" onclick="location.href = '/mall/shop/view?it_id={{ $it_id }}'; "><img src="{{ asset($image_url) }}"></li>
        @else
        <li class="si-img" onclick="location.href = '/mall/shop/view?it_id={{ $it_id }}'; "><img src="{{ asset('images/common/no_image.gif') }}"></li>
        @endif
        <li class="si-name">{{ $row['it_name'] }}</li>
        <!-- 기간설정일 경우 -->
        @if ($row['it_event_type'] == '1') 
        <li class="si-terms"><span>{{ $row['it_event_start'] }} ~ {{ $row['it_event_end'] }}<i class="hide-1280">까지</i></span></li>
        @endif

        <!-- 갯수설정일 경우 -->
        @if ($row['it_event_type'] == '2') 
        <li class="si-terms"><span>{{ $row['it_event_start2'] }} {{ $row['it_qty_event_stock'] }}개 한정</span></li>
        @endif
    </ul>
</div>