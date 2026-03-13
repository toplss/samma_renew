@php

$cust_price_field = str_replace('it_', 'cust_', $activeMember['field_it_price']);
$cust_price = (int) str_replace(',', '', $row[$cust_price_field] ?? 0);


$price = (int) str_replace(',', '', $row[$activeMember['field_it_price']] ?? 0);
$qty   = (int) $min_cart_ct_qty;

$row_list_field_it_price = $price * $qty;
$row_list_cust_it_price  = $cust_price * $qty;

@endphp

@if($cust_price > 0)
<input type="hidden" class="it_price" value="{{ $row_list_field_it_price }}" />
<input type="hidden" class="org_it_price" value="{{ $row_list_cust_it_price }}" />
<p class="price-dis">{{ number_format($row_list_cust_it_price) }}원</p>
<p class="discount"><b class="d-rate">{{ $row['it_cust_rate'] }}%</b><span class="field_it_price_">{{ number_format($row_list_field_it_price) }}원</span></p>
@else

<input type="hidden" class="it_price" value="{{ $row_list_field_it_price }}" />
<p class="price field_it_price_">{{ number_format($row_list_field_it_price) }}원</p>
@endif