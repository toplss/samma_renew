@php

$cust_price_field = str_replace('it_', 'cust_', $activeMember['field_it_price']);
$cust_price = (int) str_replace(',', '', $row[$cust_price_field] ?? 0);


$price = (int) str_replace(',', '', $row[$activeMember['field_it_price']] ?? 0);
$qty   = (int) $min_cart_ct_qty;

$row_list_field_it_price = $price * $qty;
$row_list_cust_it_price  = $cust_price * $qty;



@endphp

@php
if ($row['it_price_piece_use']) {
    $it_price_piece = $row[$activeMember['field_it_price_unit']];
} else {
    $it_price_piece = 0;
}
@endphp

@if($cust_price > 0)
<input type="hidden" class="it_price" value="{{ $price }}" />
<input type="hidden" class="org_it_price" value="{{ $cust_price }}" />
<p class="price-dis"><del>{{ number_format($row_list_cust_it_price) }}원</del><b>{{ $row['it_cust_rate'] }}%</b></p>
<h5 class="discount">
    <span class="field_it_price_">{{ number_format($row_list_field_it_price) }}원</span>
    @if($it_price_piece > 0 && $row['it_price_piece_use'])
    <u>개당 {{ number_format($it_price_piece) }}원</u>
    @endif
</h5>
@else

<input type="hidden" class="it_price" value="{{ $price }}" />

<h5 class="price">
    <span class="field_it_price_">{{ number_format($row_list_field_it_price) }}원</span>
    @if($it_price_piece > 0 && $row['it_price_piece_use'])
    <u>개당 {{ number_format($it_price_piece) }}원</u>
    @endif
</h5>
@endif