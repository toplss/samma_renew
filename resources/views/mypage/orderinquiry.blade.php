@extends('layouts.header')

@section('title', '주문내역 페이지')

@section('content')

<div class="sub-container">
  <div class="sub-title-wrap">
    <h4>주문내역</h4>  
  </div>

  <div class="order-list-wrap">

    @include('layouts.mypage_top')

    @foreach ($items['data'] as $key => $row)

    @php
      $image_url = 'images/item/'.$row->it_img1;
      $date_gubun = ($row->od_gubun == '매출') ? '배송일' : '등록일';
      $str_date = date("Y/m/d", strtotime($row->od_delivery_date));
      $days = ['일', '월', '화', '수', '목', '금', '토'];
      $str_week = $days[date("w", strtotime($row->od_delivery_date))];

      /*
      |--------------------------------------------
      | 좌측 주문상태값 가공 START
      |--------------------------------------------
      */      

      $arr_gubun = explode('#', $row->current_gubun);
      $current_gubun_cnt = !empty($arr_gubun) ? count($arr_gubun) : 0;
      //가장 처음배열의 상태값으로..
      $current_gubun = $arr_gubun[0];
      
      if ($current_gubun == '매출') {

        $delivery_step_status = [
          '0'  => '입금대기',
          '8'  => '완료',
          '10' => '배송대기',
          '85' => '배송대기',
          '90' => '배송완료',
          '99' => '배송완료',
        ];      

        $str_status = $delivery_step_status[$row->od_delivery_step] ?? '';

      } elseif ($current_gubun == '반품입금' || $current_gubun == '결품' || $current_gubun == '결품입금' || $current_gubun == '기사파손' || $current_gubun == '물류파손' || $current_gubun == '잔액이관') {
        
        $str_status = '';

      } else{

        $delivery_step_status = [
          '0'  => '입금대기',
          '8'  => '완료',
          '10' => '대기',
          '85' => '대기',
          '90' => '완료',
          '99' => '완료',
        ];

        $str_status = $delivery_step_status[$row->od_delivery_step] ?? '';

      }


      /*
      |--------------------------------------------
      | 좌측 주문상태값 가공 END
      |--------------------------------------------
      */            

      //상품갯수
      $except_ct_cnt = $row->ct_cnt - 1;
      $except_ct_cnt = ($except_ct_cnt > 0) ? $except_ct_cnt : '';

      /*
      |--------------------------------------------
      | 우측 입금 상태값 가공 START
      |--------------------------------------------
      */            

      //선불, 후불 주문 구분
      $payment_type = '';
      if (isset($row->level_ca_id2)) {
          if (strlen($row->level_ca_id2) == 4) {
              if (substr($row->level_ca_id2, -1) == '1') $payment_type = '선불';
              if (substr($row->level_ca_id2, -1) == '2') $payment_type = '후불';
          }
      }        

      $str_od_status = '';
      if (str_contains($current_gubun, '매출') || $current_gubun == '충전금구매') {

        if ($row->od_delivery_step > 0) {
          $str_od_status = '입금완료';
        } else {
          $str_od_status = '입금대기';
        }

        //후불주문인 경우 예금입금을 제외하고 모두 '여신' 으로 표기
        if ($payment_type == '후불') {
          $str_od_status = '여신';
        }

      } else {

        if ($current_gubun == '반품' || $current_gubun == '취소') {
            $str_od_status = $current_gubun . '완료';
        } else {
            $str_od_status = $current_gubun;
        }

      }

      /*
      |--------------------------------------------
      | 우측 입금 상태값 가공 END
      |--------------------------------------------
      */                  

    @endphp
    <div class="odr-content">
      <div class="odr-list-title">
        <h6>{{ $date_gubun }} : {{ $str_date }}({{ $str_week }}) 
          <i>{{ number_format($row->order_cnt) }}건</i>|
          <b>{{ $current_gubun }} {{ $str_status }} {{ ($current_gubun_cnt > 1) ? '외 ' . ($current_gubun_cnt - 1) . '건' : '' }}</b>
          @php
            if($row->od_delivery_step == 0) {
              $infoMsg = '<br class="show-1280"><span class="txt-blue">일부 또는 전체 취소 가능합니다.</span>';
            }elseif($row->od_delivery_step <> 8 && $row->od_delivery_step < 20) {
              $infoMsg = '<br class="show-1280"><span class="txt-red">일부 또는 전체 취소는 고객센터 (1899-3153) 로 연락주시기 바랍니다.</span>';
            }else {
              $infoMsg = '';
            }
          @endphp
            {!! $infoMsg !!}
        </h6>
        <p>
          <button type="button" class="btn2 hide-1024" onclick="window.open('/mypage/orderinquiry_pop?ogc={{ $row->od_group_code }}','주문상세','width=600,height=800');">상세보기</button>
          <button type="button" class="btn3" onclick="location.href='/mypage/orderinquiryview?oid={{ $row->od_id }}&ogc={{ $row->od_group_code }}';">주문정보</button>
          @if($row->od_delivery_step >= 90)
            @php 
              $url = "https://samma-erp.com/erp/sales/mall_sales_statement_order_transaction.html?view_type=group&mb_code=" . $activeMember['mb_code'] . "&od_group_code=" . $row->od_group_code;
            @endphp
            <button type="button" class="btn2 hide-1024" id="erpBtn" onclick="javascript:show_modal('{{ $url }}');">거래명세서 확인</button>
          @endif

        </p>
      </div>
      <div class="odr-list-item">
        <div>
          @if(file_exists(public_path($image_url)) && $row->it_img1)
          <img src="{{ asset($image_url) }}" alt="">
          @else
          <img src="{{ asset('images/common/no_image.gif') }}">
          @endif
          <p>{{ $row->it_name }} @if($except_ct_cnt > 0) <span>외 {{ $except_ct_cnt }}건</span> @endif</p>
        </div>
        <h6 class="od_status">{{ $str_od_status }}</h6>
      </div>

      <ul class="odr-list1">
        <li><u>이전충전금</u><span id="amt_pt_prev_charge">{{ ($row->pt_prev_charge) ? number_format($row->pt_prev_charge).'원' : '-' }}</span></li>
        <li><u>이전적립금</u><span id="amt_pt_prev_reserve">{{ ($row->pt_prev_reserve) ? number_format($row->pt_prev_reserve).'원' : '-' }}</span></li>
        <li><u>이전채권</u><span id="amt_pt_prev_balance">{{ ($row->pt_prev_balance) ? number_format($row->pt_prev_balance).'원' : '-' }}</span></li>
        <li><u>당일매출</u><span id="amt_pt_sales">{{ ($row->sum_pt_sales) ? number_format($row->sum_pt_sales).'원' : '-' }}</span></li>      
        <li><u>물류비</u><span id="amt_pt_delivery">{{ ($row->sum_pt_delivery) ? number_format($row->sum_pt_delivery).'원' : '-' }}</span></li>     
        <li><u>합계</u><span id="amt_pt_sales_delivery">{{ ($row->sum_pt_sales + $row->sum_pt_delivery) ? number_format($row->sum_pt_sales + $row->sum_pt_delivery).'원' : '-' }}</span></li>
      </ul>
      <ul class="odr-list2">
        @if(($row->sum_pt_buy_charge))          <li><u>충전금구매</u><span class="txt-red" id="amt_pt_buy_charge">{{ number_format($row->sum_pt_buy_charge).'원' }}</span></li>@endif
        @if(($row->sum_pt_buy_reserve))         <li><u>적립금충전</u><span id="amt_pt_buy_reserve">{{ number_format($row->sum_pt_buy_reserve).'원' }}</span></li>@endif
        @if(($row->sum_pt_charge))              <li><u>충전금입금</u><span class="txt-blue" id="amt_pt_charge">{{ number_format($row->sum_pt_charge).'원' }}</span></li>@endif
        @if(($row->sum_pt_reserve))             <li><u>적립금입금</u><span class="txt-blue" id="amt_pt_reserve">{{ number_format($row->sum_pt_reserve).'원' }}</span></li>@endif
        @if(($row->sum_pt_cash))                <li><u>현금입금</u><span class="txt-blue" id="amt_pt_cash">{{ number_format($row->sum_pt_cash).'원' }}</span></li>@endif
        @if(($row->sum_pt_bank))                <li><u>예금입금</u><span class="txt-blue" id="amt_pt_bank">{{ number_format($row->sum_pt_bank).'원' }}</span></li>@endif
        @if(($row->sum_pt_card))                <li><u>카드입금</u><span class="txt-blue" id="amt_pt_card">{{ number_format($row->sum_pt_card).'원' }}</span></li>@endif
        @if(($row->sum_pt_diff_pay))            <li><u>예금 차액금</u><span id="amt_pt_diff_pay">{{ number_format($row->sum_pt_diff_pay).'원' }}</span></li>@endif
        @if(($row->sum_pt_incentive))           <li><u>장려금</u><span class="txt-red" id="amt_pt_incentive">{{ number_format($row->sum_pt_incentive).'원' }}</span></li>@endif
        @if(($row->sum_pt_dc))                  <li><u>DC</u><span class="txt-red" id="amt_pt_dc">{{ number_format($row->sum_pt_dc).'원' }}</span></li>@endif
        @if(($row->sum_pt_discount))            <li><u>할인</u><span class="txt-red" id="amt_pt_discount">{{ number_format($row->sum_pt_discount).'원' }}</span></li>@endif
        @if(($row->sum_pt_support))             <li><u>상품지원금</u><span class="txt-red" id="amt_pt_support">{{ number_format($row->sum_pt_support).'원' }}</span></li>@endif
        @if(($row->sum_pt_cancel))              <li><u>취소</u><span class="txt-red" id="amt_pt_cancel">{{ number_format($row->sum_pt_cancel).'원' }}</span></li>@endif        
        @if(($row->sum_pt_return))              <li><u>반품</u><span class="txt-red" id="amt_pt_return">{{ number_format($row->sum_pt_return).'원' }}</span></li>@endif
        @if(($row->sum_pt_return_receivable))   <li><u>반품입금</u><span class="txt-blue" id="pt_return_receivable">{{ number_format($row->sum_pt_return_receivable).'원' }}</span></li>@endif
        @if(($row->sum_pt_outofstock))          <li><u>결품</u><span class="txt-red" id="amt_pt_outofstock">{{ number_format($row->sum_pt_outofstock).'원' }}</span></li>@endif
        @if(($row->sum_pt_outofstock_deposit))  <li><u>결품입금</u><span class="txt-blue" id="amt_pt_outofstock_deposit">{{ number_format($row->sum_pt_outofstock_deposit).'원' }}</span></li>@endif
        @if(($row->sum_pt_damage_staff))        <li><u>기사파손</u><span class="txt-red" id="amt_pt_damage_staff">{{ number_format($row->sum_pt_damage_staff).'원' }}</span></li>@endif
        @if(($row->sum_pt_damage_logistic))     <li><u>물류파손</u><span class="txt-red" id="amt_pt_damage_logistic">{{ number_format($row->sum_pt_damage_logistic).'원' }}</span></li>@endif
        @if(($row->pt_refund))                  <li><u>환불처리</u><span class="txt-red" id="amt_pt_refund">{{ number_format($row->pt_refund).'원' }}</span></li>@endif
        @if(($row->pt_refund_done))             <li><u>환불금액</u><span id="amt_pt_refund_done">{{ number_format($row->pt_refund_done).'원' }}</span></li>@endif
      </ul>
      <ul class="odr-list3">
         
      @if($row->od_delivery_step >= 90)
        @if($payment_type == '선불')
          <li><u>매출합계</u><span id="amt_pt_subtotal">{{ ($row->sum_pt_subtotal) ? number_format($row->sum_pt_subtotal).'원' : '0원' }}</span></li>
          <li><u>충전금잔액</u><span id="amt_pt_cur_charge">{{ ($row->pt_cur_charge) ? number_format($row->pt_cur_charge).'원' : '0원' }}</span></li>
          <li><u>적립금잔액</u><span id="amt_pt_cur_reserve">{{ ($row->pt_cur_reserve) ? number_format($row->pt_cur_reserve).'원' : '0원' }}</span></li>
        @endif
        <li><u>채권잔액</u><span id="amt_pt_cur_balance">{{ ($row->pt_cur_balance) ? number_format($row->pt_cur_balance).'원' : '0원' }}</span></li>
      @endif

      </ul>

    </div>
    @endforeach

    <!-- 없을때 -->
    @if($items->total() < 1)
      <table class="table1 point-table">
        <colgroup>
          <col style="width:auto;">
        </colgroup>
        <thead>
          <tr>
            <th height="29"></th>
          </tr>
        </thead>
        <tbody>
          <tr>
          <td height="100">내역이 없습니다.</td>
          </tr>        
        </tbody>
      </table>        
    @endif    

  </div>

  {{ $items->links() }}
</div>

<!-- 거래명세서 모달 -->
<div class="erp-modal">
  <div>
    <iframe id="iframe"></iframe>
    <button type="button" id="erpClose"><img src="{{ asset('images/icon/close-w.svg') }}"></button>
  </div>
</div>

<script>

  //조회날짜 초기 세팅
  $(document).ready(function() {

    const endOfMonth = new Date(new Date().getFullYear(), new Date().getMonth() + 1, 0);
    $('#end_date').datepicker('option', 'maxDate', endOfMonth);

    const start = "{{ request('start_date') }}";
    const end   = "{{ request('end_date') }}";

    if(start && end){
        $('#start_date').datepicker('setDate', new Date(start));
        $('#end_date').datepicker('setDate', new Date(end));
    }else{
        set_date();
    }

  });

  function set_date() {

    const now = new Date();

    // 시작일 (2개월 전 1일)
    const start = new Date(now.getFullYear(), now.getMonth() - 2, 1);

    // 종료일 (이달 마지막일)
    const end = new Date(now.getFullYear(), now.getMonth() + 1, 0);

    $('#start_date').datepicker('setDate', start);
    $('#end_date').datepicker('setDate', end);

  }  

function show_modal(url){
  $('#iframe').attr('src', url);
  $('.erp-modal').show();
}

$(function () {
  $('.erp-modal, .erp-modal #erpClose').on('click', function () {
    $('#iframe').attr('src', '');
    $('.erp-modal').hide();
  });
});
</script>

@endsection


