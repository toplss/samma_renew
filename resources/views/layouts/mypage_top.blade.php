<div class="mypage-top">
  <form id="search_form" method="GET">
  <ul class="mypage-date">
    <li><button type="button" class="btn3">조회기간</button></li>
    <li><button type="button" class="btn2" onclick="javascript:setDateMonth();">이번달</button></li>
    <br class="show-680">
    <li class="flex-center" style="gap:0.3rem;">
      <input type="text" name="start_date" id="start_date" value="{{ (request('start_date')) ? request('start_date') : ''  }}" class="datepicker" readonly> ~ 
      <input type="text" name="end_date" id="end_date" value="{{ (request('end_date')) ? request('end_date') : ''  }}" class="datepicker" readonly>
    </li>
    <li><button type="submit" class="btn1">조회하기</button></li>
  </ul>
  </form>
  <script>
    function setDateMonth() {
      $("#start_date").val("{{ date('Y-m-01') }}");
      $("#end_date").val("{{ date('Y-m-t') }}");
    }    
  </script>
  <ul class="mypage-badge">
    <li onclick="location.href='/mypage/cart'">
      <h5>장바구니</h5>
      <p><b style="color:#008000;">{{ $activeMyPageTop['cart_cnt'] }}</b>개</p>
    </li>
    <li onclick="location.href='/mypage/orderinquiry'">
      <h5>주문내역</h5>
      <p><b style="color:#1cc88a;">{{ $activeMyPageTop['order_cnt'] }}</b>건</p>
    </li>
    <li onclick="location.href='/mypage/my_point'">
      <h5>충전금</h5>
      <p><b style="color:#36b9cc;">{{ number_format($activeMember['mb_point']) }}</b>원</p>
    </li>
    <li onclick="location.href='/mypage/my_point_reserve'">
      <h5>적립금</h5>
      <p><b style="color:#f6c23e;">{{ number_format($activeMember['mb_point_reserve']) }}</b>원</p>
    </li>
    <li onclick="location.href='/mypage/deposit_history'">
      <h5>채권</h5>
      <p><b style="color:#e74a3b;">{{ number_format($activeMember['mb_point_balance']) }}</b>원</p>
    </li>
    <li onclick="location.href='/mypage/my_equipment_list'">
      <h5>보유장비</h5>
      <p><b style="color:#4e73df;">{{ $activeMyPageTop['equip_cnt'] }}</b>건</p>
    </li>
  </ul>
</div>




<script>
  $(function() {
    var start_date = $('#start_date').val();
    var end_date = $('#end_date').val();

    $( ".datepicker" ).datepicker({
      dateFormat: 'yy-mm-dd',
      showMonthAfterYear:true,
      monthNamesShort: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
      monthNames: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
      dayNamesMin: ['일','월','화','수','목','금','토'],
      dayNames: ['일요일','월요일','화요일','수요일','목요일','금요일','토요일'],
      changeYear: true,
      changeMonth: true,
      showButtonPanel: true,
      closeText: "닫기",
      currentText: '오늘',
      minDate: "-9Y",
      maxDate: "+0Y",
    });
    // 월 패널 생성
    if (start_date || end_date) {
      $('#start_date').datepicker('setDate', start_date);
      $('#end_date').datepicker('setDate', end_date);
    } else {
      $('.datepicker').datepicker('setDate', 'today'); //오늘날짜 기본설정
    };

  });
</script>