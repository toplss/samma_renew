@php

$cust_price_field = str_replace('it_', 'cust_', $activeMember['field_it_price']);
$cust_price = (int) str_replace(',', '', $row[$cust_price_field] ?? 0);


$price = (int) str_replace(',', '', $row[$activeMember['field_it_price']] ?? 0);
$qty   = (int) $min_cart_ct_qty;

$row_list_field_it_price = $price * $qty;
$row_list_cust_it_price  = $cust_price * $qty;

@endphp

@if($cust_price > 0)
<input type="hidden" class="it_price" value="{{ $price }}" />
<input type="hidden" class="org_it_price" value="{{ $row_list_cust_it_price }}" />
<p class="price-dis"><del>{{ number_format($row_list_cust_it_price) }}원</del><b>{{ $row['it_cust_rate'] }}%</b></p>
<h5 class="discount"><span class="field_it_price_">{{ number_format($row_list_field_it_price) }}원</span><u>개당 2,300원</u></h5>
@else

<input type="hidden" class="it_price" value="{{ $price }}" />
<h5 class="price"><span class="field_it_price_">{{ number_format($row_list_field_it_price) }}원</span><u>개당 2,300원</u></h5>
@endif