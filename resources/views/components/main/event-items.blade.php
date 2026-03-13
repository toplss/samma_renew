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

<div class="sale-item-list">
    <ul>
        @if(file_exists(public_path($image_url)) && $row['it_img1'])
        <li class="si-img  {{ $sold_out ? 'sold-out' : '' }}" onclick="location.href = '/mall/shop/view?it_id={{ $it_id }}'; "><img src="{{ asset($image_url) }}"></li>
        @else
        <li class="si-img  {{ $sold_out ? 'sold-out' : '' }}" onclick="location.href = '/mall/shop/view?it_id={{ $it_id }}'; "><img src="{{ asset('images/common/no_image.gif') }}"></li>
        @endif
        <li class="si-name">{{ $row['it_name'] }}</li>
        <li class="si-ea">({{ $row['it_basic'] }}*{{ $row['it_gubun_label'] }})</li>

        @if($activeMember)
            @php
            $cust_price_field = str_replace('it_', 'cust_', $activeMember['field_it_price']);
            $cust_price = (int) str_replace(',', '', $row[$cust_price_field] ?? 0);


            $price = (int) str_replace(',', '', $row[$activeMember['field_it_price']] ?? 0);
            $qty   = (int) $min_cart_ct_qty;

            $row_list_field_it_price = $price * $qty;
            $row_list_cust_it_price  = $cust_price * $qty;
            @endphp
        
            @if($cust_price > 0)
            <li class="si-price">
                <del>{{ number_format($row_list_cust_it_price) }}원</del>
                <p><b>{{ $row['it_cust_rate'] }}%</b>{{ number_format($row_list_field_it_price) }}원</p>
            </li>
            @else
            <li class="si-price"><span>{{ number_format($row_list_field_it_price) }}원</span></li>
            @endif
        @else

        <li class="si-price"><span>{{ number_format($row['it_price2']) }}원</span></li>
        @endif
    </ul>
</div>