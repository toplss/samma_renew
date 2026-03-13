@extends('layouts.header')

@section('content')


<div class="sub-container">

    <!-- 카테고리 영역 -->
    <div class="sub-title-wrap">
      <div class="st-left">
        <h4>A/S부속품</h4>
      </div>
    </div>
    <!-- 카테고리 영역 -->

    <!-- 배너 영역 -->
    <div class="sub-list-slide">

    </div>
    <!-- 배너 영역 -->
    
    <div class="prd-row-head">
        <ul>
          <li>상품명</li>
          <li>판매가</li>
          <li class="hide-680">유형</li>
          <li class="hide-680">수량</li>
        </ul>
    </div>

    <div class="prd-list">
      <ul class="prd-box" data-item="">
        <li class="prd-img" onclick="location.href = ''; "><img src="https://pay.sammamall.com/mall/data/item/F00650/1590538870902l0.jpg"></li>
        <li class="Qua ">
          <p class="pm-wrap">
            <button type="button" class="sit_qty_minus"><img src="https://sammamall.com/images/icon/minus.svg"></button>
            <span id="numberUpDown">
              <input type="text" name="ct_qty" class="ct_qty" value="1" readonly="">
            </span>
            <button type="button" class="sit_qty_plus"><img src="https://sammamall.com/images/icon/plus.svg"></button>
          </p>
          <button type="button" class="add-to-cart">담기</button>
        </li>
        <li class="prd-info">
          <button type="button" class="btn2" id="buy_box_btn" onclick="buy_box_qty(this)">박스구매</button>
          <p class="pin">
            <span class="room_temp">상온</span>
            <span class="return_o">반품가능</span>
          </p>
        </li>
        <li class="prd-name">
          [머신기/동구전자] 쿨 노즐
            <p class="ea">(4J6001*낱개)</p>
        </li>
        <li class="prd-price">
          <p class="price">20,000원</p>
        </li>

      </ul>

    </div>

</div>

<script>
  $('.Qua-call').on('click', function () {
  Swal.fire({
    title: '장비구매',
    html: '<b>1899-3153</b><br>고객센터로 문의바랍니다.',
    icon: 'question'
  });
});
</script>


@endsection


