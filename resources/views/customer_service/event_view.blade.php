@extends('layouts.header')

@section('content')

<div class="sub-container">

	<div class="prd-view-wrap" data-item="{{ $items['it_id'] }}">

    <div class="prd-view-img">
    @php
      $sold_out = false;
      if ($event_status == 'SOLD_OUT' || $event_status == 'END') {
        $sold_out = true;
      }

      $it_id    = $items['it_id'];
      $min_cart_ct_qty = $box_min_qty = $max_cart_ct_qty = 0;

      
      if(isset($activeMember['mb_level']) && substr($activeMember['mb_level'], 0, 2) == '30' && $items['agency_it_buy_min_qty'] > 0 || (isset($activeMember['mb_branch_gubun_type']) && $activeMember['mb_branch_gubun_type'] == '3')) {
          $min_cart_ct_qty = 1; // 주문최소
          $box_min_qty     = ($items['it_gubun'] == 'pack') ? $items['it_box_sale_pack'] : $items['it_box_sale_tot']; // 박스구매
          $max_cart_ct_qty = 1; // 최대치 구매

      } else {
          $min_cart_ct_qty = 1;
          $box_min_qty     = ($items['it_gubun'] == 'pack') ? $items['it_box_sale_pack'] : $items['it_box_sale_tot']; // 박스구매
          $max_cart_ct_qty = 1; // 최대치 구매
      }


      $image_url = 'images/item/'.$items['it_img1'];
      @endphp

      @php
      $timpArr = ['1' => 'room_temp', '3' => 'low_temp', '2' => 'frozen_temp', '4' => ''];
      @endphp

      @if(file_exists(public_path($image_url)) && $items['it_img1'])
      <div class="pv-img {{ $sold_out ? 'end' : '' }}">
        <img src="{{ asset($image_url) }}">
        <!-- <span class="{{ $timpArr[$items['it_storage']] }}"><img src="{{ asset('images/icon/snow.svg') }}">{{ $items['it_storage_label'] }}</span> -->
      </div>
      @else
      <div class="pv-img {{ $sold_out ? 'end' : '' }}">
        <img src="{{ asset('images/common/no_image.gif') }}">
      </div>
      @endif
    </div>

    <div class="prd-view-info">
      <div class="prd-view-title">
        <h3 id="sit_title" class="sit_title02">{{ $items['it_name'] }}</h3>
        @if ($items['it_event_type'] == '1') 
				<i>기간 : {{ $items['it_event_start'] }} ~ {{ $items['it_event_end'] }}</i>
        @endif

        @if ($items['it_event_type'] == '2') 
        <i>기간 : {{ $items['it_event_start2'] }}</i>
				<i>{{ $items['it_qty_event_stock']}}개 한정 ( {{ $items['it_qty_event_stock'] - $sales_count }} )</i>
        @endif
			</div>

      @if($activeMember)
      <div class="prd-view-price" style="display:none;">
        @php
        $cust_price_field = str_replace('it_', 'cust_', $activeMember['field_it_price']);
        $cust_price = (int) str_replace(',', '', $items[$cust_price_field] ?? 0);

        $price = (int) str_replace(',', '', $items[$activeMember['field_it_price']] ?? 0);
        $qty   = (int) $min_cart_ct_qty;

        $row_list_field_it_price = $price * $qty;
        $row_list_cust_it_price  = $cust_price * $qty;
        @endphp

        @if ($cust_price > 0)
        <input type="hidden" class="it_price" value="{{ $price }}" />
        <input type="hidden" class="org_it_price" value="{{ $row_list_cust_it_price }}" />
        <p class="view-price-dis">{{ number_format($row_list_cust_it_price) }}원</p>
        <p class="view-discount"><b class="d-rate">{{ $items['it_cust_rate'] }}%</b><span class="field_it_price_">{{ number_format($row_list_field_it_price) }}원</span></p>
        @else
        <input type="hidden" class="it_price" value="{{ $price }}" />
        <p class="view-price ">{{ number_format($row_list_field_it_price) }}원</p>
        @endif
        


        @php
        if ($items['it_price_piece_use']) {
            $it_price_piece = $items[$activeMember['field_it_price_unit']];
        } else {
            $it_price_piece = 0;
        }
        @endphp

        <table>
        @if($it_price_piece > 0 && $items['it_price_piece_use'])
          <tr>
            <th><i>개당단가</i></th>
            <td>{{ number_format($it_price_piece) }}원</td>
          </tr>
        @endif

        @if($items['it_cust_price'] > 0 && $items['it_cust_price_use'])
          <tr>
            <th><i>소비판매가</i></th>
            <td>{{ number_format($items['it_cust_price']) }}원</td>
          </tr>
        @endif
        </table>

      </div>
      @endif
      
      @if($activeMember)
      @php
        $read_only = 'readonly'; 
        $onkey_press_event = '';
        
        if($min_cart_ct_qty < 2) {
            $read_only = '';
            
            $onkey_press_event = 'oninput="isNumberKeyView(this)" inputmode="numeric"';
        }
      @endphp
      <div class="price-view-opt">
        <div class="sit_opt">
              
          <div class="sit_opt_g" style="display:none;">

            <input type="hidden" name="min_ct_qty" class="min_ct_qty"  value="{{ $min_cart_ct_qty }}">
            <input type="hidden" name="max_ct_qty" class="max_ct_qty"  value="{{ $max_cart_ct_qty }}">
            <input type="hidden"  id="ct_qty" class="ct_qty" value="{{ $min_cart_ct_qty }}" {{ $read_only }} {!! $onkey_press_event !!}>

          </div>
          <!-- <div id="sit_tot_price">총 금액<span class="sit_tot_price_view">{{ number_format($row_list_field_it_price) }}원</span></div> -->
        </div>
        
        <div class="sit_btn">
          @if($items['it_gubun'] !== 'box')
          <input type="hidden" class="it_gubun" value="{{ $items['it_gubun'] }}" />
          <input type="hidden" class="buy_box_qty" value="{{ $box_min_qty }}" />
          <input type="hidden" class="it_box_sale_pcs" value="{{ $items['it_box_sale_pcs'] }}" />
          <input type="hidden" class="it_box_sale_pack" value="{{ $items['it_box_sale_pack'] }}" />
          <input type="hidden" class="it_box_sale_tot" value="{{ $items['it_box_sale_tot'] }}" />
          @endif

          <button type="button" id="sit_btn_cart" class="add-to-cart-view btn3" >장바구니 담기</button>
        </div>
      </div>
      @endif
      

      <div class="price-view-detail">
        <!-- table>
          <tr>
            <th>제조사</th>
            <td>{{ $items['it_maker'] }}</td>
          </tr>            
          <tr>
            <th>상품구분</th>
            <td><span class="{{ $timpArr[$items['it_storage']] }}">{{ $items['it_storage_label'] }}제품</span></td>
          </tr>
          <tr>
            @if($items['it_return_use'] == '1')
            <th>보관/반품유형</th>
            @else
            <th>보관</th>
            @endif
            <td>
              <span class="{{ $timpArr[$items['it_storage']] }}">{{ $items['it_storage_label'] }}</span>
              
              @if($items['it_return_use'] == '1')
              <span class="{{ $items['it_return_label'] == '반품가능' ? 'return_o' : 'return_x' }}">{{ $items['it_return_label'] }}</span>
              @endif
            </td>
          </tr>
          <tr>
            <th>낱개바코드</th>
            <td></td>
          </tr>
        </table -->
        <p>
          + 여러상품을 이용하실 경우 장바구니를 이용하시면 묶음배송이 됩니다.<br>
					+ 본 사이트의 이미지와 컨텐츠의 불법사용을 금합니다.
        </p>
      </div>
    </div>

  </div>


	<div class="event-content">
			{!! $items['it_explan'] !!}
	</div>

</div>

<script>
$(document).ready(function() {

      // 담기기능
      $(document).off('click', '.add-to-cart-view').on('click', '.add-to-cart-view', function(){
        var ul = $(this).closest('.prd-view-wrap');
        var it_id = ul.data('item');
        var it_name = ul.find('.prd-name').text();
        var qty = ul.find('.ct_qty').val();
        var min_qty = ul.find('.min_ct_qty').val();
        var it_price = ul.find('.it_price').val() * 1;
        var isDiscount = ul.find('.d-rate').length > 0;

        if (qty < 1) {
          Swal.fire({
            toast : false,
            icon : 'info',
            html: `최소 주문 수량은 <span style="color:red;">${min_qty}개</span>입니다. <br>해당 수량 미만은 주문할 수 없습니다.`
          });
          ul.find('.ct_qty').val(min_qty);

          it_price = it_price * min_qty;
						
          if (isDiscount) {
            let org_it_price = ul.find('.org_it_price').val() * 1;
            let org_price = org_it_price * min_qty;

            ul.find('.view-price').text(org_price.toLocaleString() + '원');
            ul.find('.field_it_price_').text(it_price.toLocaleString() + '원');
          } else {
            ul.find('.view-price').text(it_price.toLocaleString() + '원');
          }

          ul.find('.ct_qty').val(min_qty);
          ul.find('.sit_tot_price_view').text(it_price.toLocaleString() + '원');

          return false;
        }


        $.post('/mall/proc_query_cart', {
          mode: 'cart_insert',
          it_id: it_id,
          ct_qty: qty
        }, function(res) {
          basket_count();
          cart_res(res); 

          it_price = it_price * min_qty;
						
          if (isDiscount) {
            let org_it_price = ul.find('.org_it_price').val() * 1;
            let org_price = org_it_price * min_qty;

            ul.find('.view-price').text(org_price.toLocaleString() + '원');
            ul.find('.field_it_price_').text(it_price.toLocaleString() + '원');
          } else {
            ul.find('.view-price').text(it_price.toLocaleString() + '원');
          }

          ul.find('.ct_qty').val(min_qty);
          ul.find('.sit_tot_price_view').text(it_price.toLocaleString() + '원');
          
        }, 'json');
      });


      // 수량증가
      $(document).off('click', '.sit_qty_plus_view').on('click', '.sit_qty_plus_view', function(){
        var ul = $(this).closest('.prd-view-wrap');
        var it_id = ul.data('item');
        var it_name = ul.find('.prd-name').text();
        var qty = ul.find('.ct_qty').val() * 1;
        var min_qty = ul.find('.min_ct_qty').val() * 1;
        var max_qty = ul.find('.max_ct_qty').val() * 1;
        var it_price = ul.find('.it_price').val() * 1;
        var isDiscount = ul.find('.d-rate').length > 0;


        if (min_qty) {
          qty += min_qty;
        } else {
          qty += 1;
        }

        if (qty > max_qty) {
          Swal.fire({
            toast : false,
            icon : 'info',
            html: `최대 주문 수량은 <span style="color:red;">${max_qty}개</span>입니다. <br>해당 수량 초과는 주문할 수 없습니다.`
          });
          qty = max_qty;
        }

        it_price = it_price * qty;

        if (isDiscount) {
          let org_it_price = ul.find('.org_it_price').val() * 1;
          let org_price = org_it_price * qty;

          ul.find('.view-price').text(org_price.toLocaleString() + '원');
          ul.find('.field_it_price_').text(it_price.toLocaleString() + '원');
        } else {
          ul.find('.view-price').text(it_price.toLocaleString() + '원');
        }

        ul.find('.ct_qty').val(qty);
        ul.find('.sit_tot_price_view').text(it_price.toLocaleString() + '원');
      });



      // 수량차감
      $(document).off('click', '.sit_qty_minus_view').on('click', '.sit_qty_minus_view', function(){
        var ul = $(this).closest('.prd-view-wrap');
        var it_id = ul.data('item');
        var it_name = ul.find('.prd-name').text();
        var qty = ul.find('.ct_qty').val() * 1;
        var min_qty = ul.find('.min_ct_qty').val() * 1;
        var it_price = ul.find('.it_price').val() * 1;
        var isDiscount = ul.find('.dis').length > 0;


        if (min_qty == qty) {
          Swal.fire({
            toast : true,
            icon : 'info',
            html: `최소 주문 수량은 <span style="color:red;">${min_qty}개</span>입니다. <br>해당 수량 미만은 주문할 수 없습니다.`
          });
          return false;
        }

        if (qty < 2) return false;

        // 묶음판매 수량
        if (min_qty) {
          qty -= min_qty;
        } else {
          qty -= 1;
        }

        it_price = it_price * qty;

        if (isDiscount) {
          let org_it_price = ul.find('.org_it_price').val() * 1;
          let org_price = org_it_price * qty;

          ul.find('.view-price').text(org_price.toLocaleString() + '원');
          ul.find('.field_it_price_').text(it_price.toLocaleString() + '원');
        } else {
          ul.find('.view-price').text(it_price.toLocaleString() + '원');
        }

        ul.find('.ct_qty').val(qty);
        ul.find('.sit_tot_price_view').text(it_price.toLocaleString() + '원');
      });
})


// 박스구매 
function buy_box_qty_view(e) {
    var ul = $(e).closest('.prd-view-wrap');
    var it_id = ul.data('item');
    var it_name = ul.find('.sit_title02').text();
    var it_gubun = ul.find('.it_gubun').val();
    var qty = ul.find('.buy_box_qty').val();


    if (it_gubun == 'pcs') {
				var pcs = ul.find('.it_box_sale_tot').val();
				message = `<span style="color:#e02f30">${it_name}</span><br>${pcs}입*1박스로 구매 하시겠습니까?`;
			}
			if (it_gubun == 'pack') {
				var pcs = ul.find('.it_box_sale_pcs').val();
				var pack = ul.find('.it_box_sale_pack').val();
				var total = ul.find('.it_box_sale_tot').val();
				message = `<span style="color:#e02f30">${it_name}</span><br>${pcs}입*${pack}팩*${total}개*1박스로 구매 하시겠습니까?`;
			}

    Swal.fire({
      title: '구매 확인',
      html: message,
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: '구매',
      cancelButtonText: '취소'
    }).then((result) => {
      if (result.isConfirmed) {
        $.post('/mall/proc_query_cart', {
          mode: 'cart_insert',
          it_id: it_id,
          ct_qty: qty
        }, function(res) {
          basket_count();
          cart_res(res); 
        }, 'json');
      }
    });
}

// 상품수량 수동입력
function isNumberKeyView(el) {
    el.value = el.value.replace(/[^0-9]/g, '');
    let qty = parseInt(el.value) || 0;

    // if (qty < 1) qty = 1;

    var ul = $(el).closest('.prd-view-wrap');
    var min_qty = ul.find('.min_ct_qty').val() * 1;
    var max_qty = ul.find('.max_ct_qty').val() * 1;
    var it_price = ul.find('.it_price').val() * 1;
    var isDiscount = ul.find('.price-dis').length > 0;

    if (min_qty) {
      qty = Math.round(qty / min_qty) * min_qty;
    }

    if (qty > max_qty) {
      Swal.fire({
        toast : false,
        icon : 'info',
        html: `최대 주문 수량은 <span style="color:red;">${max_qty}개</span>입니다. <br>해당 수량 초과는 주문할 수 없습니다.`
      });
      qty = max_qty;

      it_price = it_price * qty;

      if (isDiscount) {
        let org_it_price = ul.find('.org_it_price').val() * 1;
        let org_price = org_it_price * qty;

        ul.find('.view-price').text(org_price.toLocaleString() + '원');
        ul.find('.field_it_price_').text(it_price.toLocaleString() + '원');
      } else {
        ul.find('.view-price').text(it_price.toLocaleString() + '원');
      }

      ul.find('.ct_qty').val(qty);
      ul.find('.sit_tot_price_view').text(it_price.toLocaleString() + '원');

      return false;
    }

    it_price = it_price * qty;

    if (isDiscount) {
      let org_it_price = ul.find('.org_it_price').val() * 1;
      let org_price = org_it_price * qty;

      ul.find('.view-price').text(org_price.toLocaleString() + '원');
      ul.find('.field_it_price_').text(it_price.toLocaleString() + '원');
    } else {
      ul.find('.view-price').text(it_price.toLocaleString() + '원');
    }

    ul.find('.ct_qty').val(qty);
    ul.find('.sit_tot_price_view').text(it_price.toLocaleString() + '원');
}

</script>

@endsection