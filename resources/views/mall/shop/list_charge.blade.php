@extends('layouts.header')

@section('content')

<div class="sub-container">

  <div class="sub-title-wrap">
    <div class="st-left">
      <h4>충전금</h4>
    </div>
      
  </div>



  <!-- 충전금 배너 영역 -->
  <div class="charge-top-banner">
    <div class="hide-820">
      {!! $subBanner['pc'] !!}
    </div>
    <div class="show-820">
    {!! $subBanner['mobile'] !!}
    </div>
  </div>

  <div class="charge-list">
    @foreach ($items as $key => $row)
    <div class="charge-coupon" onclick="buy_coupon(this)">
      <input type="hidden" class="it_id" value="{{ $row['it_id'] }}" />
      <input type="hidden" class="it_price" value="{{ $row['it_price'] }}" />
      <input type="hidden" class="it_name" value="{{ $row['it_name'] }}" />
    @php
    $image_url = 'images/item/'.$row['it_img1'];

    @endphp
    @if(file_exists(public_path($image_url)) && $row['it_img1'])
      <img src="{{ asset($image_url) }}" alt="">
    @else
      <img src="{{ asset('images/common/no_image.gif') }}" alt="">
    @endif
      <p>{{ $row['it_name'] }}</p>
    </div>
    @endforeach
  </div>
</div>

<form id="frm" method="post" action="{{ route('buy_charge') }}">
@csrf
</form>
<script>
function buy_coupon(e) {
  var frm = $('#frm');
  var self = $(e).closest('.charge-coupon');
  var it_id = self.find('.it_id').val();
  var it_name = self.find('.it_name').val();
  var wait_order_charge   = '{{ $wait_order_charge }}';
  var mb_virtual_account  = $('#virtual_account').val();

  if (wait_order_charge > 0) {
    wait_order_charge = parseInt(wait_order_charge);

    confirm_content = `
            <span style="line-height:2; color:#e02f30; font-weight:bold;">미입금 주문이 존재합니다.</span><br>
						<span style="color:#000; font-weight:bold;">입금금액 : ${wait_order_charge.toLocaleString()}원</span><br>
						<span style="color:#0d6efd; font-weight:normal;">입금계좌 : ${mb_virtual_account}</span><br>
						<span style="color:#000; font-weight:normal;">가상계좌로 입금 후 추가구매 부탁드립니다.<br>반영까지 약 1~2분 소요됩니다.<br>미입금 주문건은 마이페이지에서 확인 가능합니다.</span>`;


    Swal.fire({
      title: '<i class="fas fa-solid fa-plus"></i> 상품 구매하기',
      html: confirm_content,
      icon: 'warning',
      confirmButtonText: '닫기',
      focusConfirm: false
    });
  } else {
    Swal.fire({
        title: '구매 확인',
        text: `상품 ${it_name}을(를) 구매하시겠습니까?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: '구매',
        cancelButtonText: '취소'
      }).then((result) => {
        if (result.isConfirmed) {
          frm.find('input[name="it_id"]').remove();
          frm.append('<input type="hidden" name="it_id" value="'+it_id+'">');
          frm.submit();
        }
    });
  }

}
</script>

@if($od_id) 
<script>
    setTimeout(function(){
        window.open(
            "https://pay.sammamall.com/mall/shop/list_charge_orderform.php?od_id={{ $od_id }}",
            "_blank"
        );
        window.history.back();
    }, 300); // 0.8초 후
</script>
@endif

@endsection


