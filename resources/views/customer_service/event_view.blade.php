@extends('layouts.header')

@section('content')

<div class="sub-container">

	<div class="prd-view-wrap">

    <div class="prd-view-img">
      <div class="pv-img ">
        <img src="https://sammamall.com/images/item/C00248/4246.png">
      </div>
    </div>

    <div class="prd-view-info">
      <div class="prd-view-title">
        <h3 id="sit_title" class="sit_title02">[롯데칠성]점보캔 490ml/펩시콜라 ZERO슈거-라임</h3>
				<i>기간 : 2026-04-20 ~ 2026-04-20</i>
				<i>200개 한정</i>
			</div>

      <div class="prd-view-price">
				<input type="hidden" class="it_price" value="28800">
				<p class="view-price ">28,800원</p>
      </div>
      
      <div class="price-view-opt">

        <div class="sit_opt">
          <div class="sit_opt_g">
            <button type="button" class="btn2 sit_qty_minus sit_qty_minus_view">
              <img src="{{ asset('images/icon/minus.svg') }}">
            </button>
            <input type="hidden" name="min_ct_qty" class="min_ct_qty" value="1">
            <input type="hidden" name="max_ct_qty" class="max_ct_qty" value="9999">
            <input type="text" id="ct_qty" class="ct_qty" value="1" oninput="isNumberKeyView(this)" inputmode="numeric">
            <button type="button" class="btn2 sit_qty_plus sit_qty_plus_view">
              <img src="{{ asset('images/icon/plus.svg') }}">
            </button>
          </div>
          <div id="sit_tot_price">총 금액<span class="sit_tot_price_view">28,800원</span></div>
        </div>
        
        <div class="sit_btn">
          <button type="button" id="sit_btn_cart" class="add-to-cart-view btn3">장바구니 담기</button>
        </div>

      </div>
      

      <div class="price-view-detail">
        <table>
          <tbody>
						<tr>
							<th>제조사</th>
							<td>롯데칠성/신유통남부지점</td>
						</tr>            
						<tr>
							<th>상품구분</th>
							<td><span class="room_temp">상온제품</span></td>
						</tr>
						<tr>
							<th>보관</th>
							<td><span class="room_temp">상온</span></td>
						</tr>
						<tr>
							<th>낱개바코드</th>
							<td></td>
						</tr>
        	</tbody>
				</table>
        <p>
          + 여러상품을 이용하실 경우 장바구니를 이용하시면 묶음배송이 됩니다.<br>
					+ 본 사이트의 이미지와 컨텐츠의 불법사용을 금합니다.
        </p>
      </div>
    </div>

  </div>

  <div class="event-wrap">
  	<div class="event-view">
			<div class="board-content">
				<img src="https://samma-erp.com/smarteditor/upload/2512/3dc24063fba6ec2cbaf0c55769916dfe_1766713936_2811.jpg" alt="">
			</div>
		</div>

  </div>






</div>

@endsection