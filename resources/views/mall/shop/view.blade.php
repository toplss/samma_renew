@extends('layouts.header')

@section('content')

<div class="sub-container">

  <div class="prd-view-wrap" data-item="{{ $items['it_id'] }}">
    <div class="prd-view-img">
      @php
      $sold_out = $items['it_soldout'] == '1' || $items['it_force_soldout'] == '10' ? true : false;
      $it_id    = $items['it_id'];
      $min_cart_ct_qty = $box_min_qty = $max_cart_ct_qty = 0;

      
      if(isset($activeMember['mb_level']) && substr($activeMember['mb_level'], 0, 2) == '30' && $items['agency_it_buy_min_qty'] > 0) {
          $min_cart_ct_qty = $items['agency_it_buy_min_qty']; // 주문최소
          $box_min_qty     = ($items['it_gubun'] == 'pack') ? $items['it_box_sale_pack'] : $items['it_box_sale_tot']; // 박스구매
          $max_cart_ct_qty = $items['agency_it_buy_max_qty']; // 최대치 구매

      } else {
          $min_cart_ct_qty = $items['it_buy_min_qty'];
          $box_min_qty     = ($items['it_gubun'] == 'pack') ? $items['it_box_sale_pack'] : $items['it_box_sale_tot']; // 박스구매
          $max_cart_ct_qty = $items['it_buy_max_qty']; // 최대치 구매
      }


      $image_url = 'images/item/'.$items['it_img1'];
      $images_visiable = file_exists($image_url);
      @endphp
      @if(file_exists(public_path($image_url)) && $items['it_img1'])
      <div class="pv-img {{ $sold_out ? 'sold-out' : '' }}"><img src="{{ asset($image_url) }}"></div>
      @else
      <div class="pv-img {{ $sold_out ? 'sold-out' : '' }}"><img src="{{ asset('images/common/no_image.gif') }}"></div>
      @endif
    </div>

    <div class="prd-view-info">

      <div class="prd-view-title">
        <h3 id="sit_title" class="sit_title02">{{ $items['it_name'] }}</h3>
        <p id="sit_desc">{{ $items['it_basic'] }}</p>
			</div>

      @if($activeMember)
      <div class="prd-view-price">
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
        <p class="view-price dis">{{ number_format($row_list_cust_it_price) }}원</p>
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

        @if($it_price_piece > 0 && $items['it_price_piece_use'])
        <table>
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
        </table>
        @endif

      </div>
      @endif

      @if($activeMember)
      <div class="price-view-opt">
        <div class="sit_opt">
              
          <div class="sit_opt_g">
            <button type="button" class="btn2 sit_qty_minus sit_qty_minus_view">
              <img src="{{ asset('images/icon/minus.svg') }}">
            </button>
            <input type="hidden" name="min_ct_qty" class="min_ct_qty"  value="{{ $min_cart_ct_qty }}" readonly>
            <input type="text"  id="ct_qty" class="ct_qty" value="{{ $min_cart_ct_qty }}" readonly>
            <button type="button" class="btn2 sit_qty_plus sit_qty_plus_view">
              <img src="{{ asset('images/icon/plus.svg') }}">
            </button>
          </div>
          <div id="sit_tot_price">총 금액<span class="sit_tot_price_view">{{ number_format($row_list_field_it_price) }}원</span></div>
        </div>
        
        <div class="sit_btn">
          @if($items['it_gubun'] !== 'box')
          <input type="hidden" class="it_gubun" value="{{ $items['it_gubun'] }}" />
          <input type="hidden" class="buy_box_qty" value="{{ $box_min_qty }}" />
          <input type="hidden" class="it_box_sale_pcs" value="{{ $items['it_box_sale_pcs'] }}" />
          <input type="hidden" class="it_box_sale_pack" value="{{ $items['it_box_sale_pack'] }}" />
          <input type="hidden" class="it_box_sale_tot" value="{{ $items['it_box_sale_tot'] }}" />
          
            @if ($items['it_box_sale_pcs'])
            <button type="button" class="btn2" onclick="buy_box_qty_view(this); return false;">박스구매</button>
            @endif
          @endif

          @if($items['ca_id'] !== 'd0')
          <button type="button" id="sit_btn_cart" class="add-to-cart-view btn3" >장바구니 담기</button>
          @else
          <button type="button" id="" class="btn3 qa_btn" >전화문의</button>
          @endif
        </div>
      </div>
      @endif

      @php
      $timpArr = ['1' => 'room_temp', '3' => 'low_temp', '2' => 'frozen_temp', '4' => ''];
      @endphp
      <div class="price-view-detail">
        <table>
          <tr>
            <th>제조사</th>
            <td>{{ $items['it_maker'] }}</td>
          </tr>            
          <tr>
            <th>상품구분</th>
            <td><span class="{{ $timpArr[$items['it_storage']] }}">{{ $items['it_storage_label'] }}제품</span></td>
          </tr>
          <tr>
            <th>보관/반품유형</th>
            <td><span class="{{ $timpArr[$items['it_storage']] }}">{{ $items['it_storage_label'] }}</span><span class="{{ $items['it_return_label'] == '반품가능' ? 'return_o' : 'return_x' }}">{{ $items['it_return_label'] }}</span></td>
          </tr>
          <tr>
            <th>낱개바코드</th>
            <td></td>
          </tr>
        </table>
        <p>
          + 여러상품을 이용하실 경우 장바구니를 이용하시면 묶음배송이 됩니다.<br>
					+ 본 사이트의 이미지와 컨텐츠의 불법사용을 금합니다.
        </p>
      </div>
    </div>

  </div>

	<!-- 상품 상세 -->
	<!-- 이미지가 없으면 아예 안보이게 부탁드립니다! -->
  @if($items['it_explan'])
  <div class="prd-view-more">
		{!! $items['it_explan'] !!}
		<button type="button"><span>상세정보 펼쳐보기</span><img src="{{ asset('images/icon/down.svg') }}" alt=""></button>
  </div>
  @endif
	<!-- 상품 상세 끝-->

  <div class="recom-wrap">
    <h3>고객님을 위한 추천상품</h3>
    <div class="recom-item-slide1">
      @foreach ($recommended_products as $key => $row)
        <x-mall.items
        :row="$row"
        :activeMember="$activeMember"
        :timpArr="$timpArr"
        />
      @endforeach
    </div>
  </div>

  <div class="recom-wrap">
    <h3>함께 구매하면 좋은 제품</h3>
    <div class="recom-item-slide2">
      @foreach ($related_products as $key => $row)
        <x-mall.items
        :row="$row"
        :activeMember="$activeMember"
        :timpArr="$timpArr"
        />
      @endforeach
    </div>

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
        var it_price = ul.find('.it_price').val() * 1;
        var isDiscount = ul.find('.d-rate').length > 0;


        if (min_qty) {
          qty += min_qty;
        } else {
          qty += 1;
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
    var qty = ul.find('.buy_box_qty').val();

    Swal.fire({
      title: '구매 확인',
      text: `상품 ${it_name}을(를) ${qty}개 구매하시겠습니까?`,
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
</script>


@endsection


