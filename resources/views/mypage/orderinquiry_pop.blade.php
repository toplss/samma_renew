@php

  $image_url = 'images/item/'.$items->it_img1;

  $date_gubun = ($items->od_gubun == '매출') ? '배송일' : '등록일';
  $str_date = date("Y/m/d", strtotime($items->od_delivery_date));
  $days = ['일', '월', '화', '수', '목', '금', '토'];
  $str_week = $days[date("w", strtotime($items->od_delivery_date))];

      /*
      |--------------------------------------------
      | 좌측 주문상태값 가공 START
      |--------------------------------------------
      */      

      $arr_gubun = explode('#', $items->current_gubun);
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

        $str_status = $delivery_step_status[$items->od_delivery_step] ?? '';

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

        $str_status = $delivery_step_status[$items->od_delivery_step] ?? '';

      }


      /*
      |--------------------------------------------
      | 좌측 주문상태값 가공 END
      |--------------------------------------------
      */            

      //상품갯수
      $except_ct_cnt = $items->ct_cnt - 1;
      $except_ct_cnt = ($except_ct_cnt > 0) ? $except_ct_cnt : '';

      /*
      |--------------------------------------------
      | 우측 입금 상태값 가공 START
      |--------------------------------------------
      */            

      //선불, 후불 주문 구분
      $payment_type = '';
      if (isset($items->level_ca_id2)) {
          if (strlen($items->level_ca_id2) == 4) {
              if (substr($items->level_ca_id2, -1) == '1') $payment_type = '선불';
              if (substr($items->level_ca_id2, -1) == '2') $payment_type = '후불';
          }
      }        

      $str_od_status = '';
      if (str_contains($current_gubun, '매출') || $current_gubun == '충전금구매') {

        if ($items->od_delivery_step > 0) {
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
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="{{ asset('css/common.css') }}">
  <link rel="stylesheet" href="{{ asset('css/sub.css') }}">
	<script src="{{ asset('js/jquery-3.7.1.min.js') }}"></script>
</head>
<body>
  <div class="pop-wrap">
    <div class="pop-title">주문 상세</div>

    <div class="order-pop-wrap">
      <p class="odr-day">{{ $date_gubun }} : 
        <span class="day-1">{{ $str_date }}({{ $str_week }})</span>
        <span class="day-2">{{ number_format($items->order_cnt) }}건</span>
        <span class="day-3">{{ $current_gubun }} {{ $str_status }} {{ ($current_gubun_cnt > 1) ? '외 ' . ($current_gubun_cnt - 1) . '건' : '' }}</span>
      </p>

      <ul class="odr-pop1">
        <li>
          @if(file_exists(public_path($image_url)) && $items->it_img1)
            <img src="{{ asset($image_url) }}" alt="">
          @else
            <img src="{{ asset('images/common/no_image.gif') }}">
          @endif
          <p id="it_name">{{ $items->it_name }}
            @if($except_ct_cnt > 0) <span>외 {{ $except_ct_cnt }}건</span> @endif
          </p>
        </li>
        <li><h6 class="od_status">{{ $str_od_status }}</h6></li>   
      </ul>
      @if($items->od_delivery_step >= 90)
        <div class="odr-pop2">
          <p class="total">매출합계 : <span>{{ ($items->sum_pt_subtotal) ? number_format($items->sum_pt_subtotal).'원' : '0원' }}</span></p>
          <p class="balance">채권잔액 : <span>{{ ($items->pt_cur_balance) ? number_format($items->pt_cur_balance).'원' : '0원' }}</span></p>
        </div>
      @endif
      <table class="table1 odr-pop3">
        <tr>
          <th>이전충전금</th>
          <td id="amt_pt_prev_charge">{{ ($items->pt_prev_charge) ? number_format($items->pt_prev_charge).'원' : '-' }}</td>
          <th>이전적립금</th>
          <td id="amt_pt_prev_reserve">{{ ($items->pt_prev_reserve) ? number_format($items->pt_prev_reserve).'원' : '-' }}</td>
        </tr>
        <tr>
          <th>이전채권</th>
          <td id="amt_pt_prev_balance">{{ ($items->pt_prev_balance) ? number_format($items->pt_prev_balance).'원' : '-' }}</td>
          <th>당일매출</th>
          <td>{{ ($items->sum_pt_sales) ? number_format($items->sum_pt_sales).'원' : '-' }}</td>
        </tr>
        <tr>
          <th>물류비</th>
          <td id="amt_pt_delivery">{{ ($items->sum_pt_delivery) ? number_format($items->sum_pt_delivery).'원' : '-' }}</td>
          <th>합계</th>
          <td id="amt_pt_sales_delivery">{{ ($items->sum_pt_sales + $items->sum_pt_delivery) ? number_format($items->sum_pt_sales + $items->sum_pt_delivery).'원' : '-' }}</td>
        </tr>
        <tr>
          <th>충전금구매</th>
          <td class="txt-red" id="amt_pt_buy_charge">{{ ($items->sum_pt_buy_charge) ? number_format($items->sum_pt_buy_charge).'원' : '-' }}</td>
          <th>적립금충전</th>
          <td class="txt-red" id="amt_pt_buy_reserve">{{ ($items->sum_pt_buy_reserve) ? number_format($items->sum_pt_buy_reserve).'원' : '-' }}</td>
        </tr>
        <tr>
          <th>충전금입금</th>
          <td class="txt-blue" id="amt_pt_charge">{{ ($items->charge_sum) ? number_format($items->charge_sum).'원' : '-' }}</td>
          <th>적립금입금</th>
          <td class="txt-blue" id="amt_pt_reserve">{{ ($items->reserve_sum) ? number_format($items->reserve_sum).'원' : '-' }}</td>
        </tr>
        <tr>
          <th>현금입금</th>
          <td class="txt-blue" id="amt_pt_cash">{{ ($items->sum_pt_cash) ? number_format($items->sum_pt_cash).'원' : '-' }}</td>
          <th>예금입금</th>
          <td class="txt-blue" id="amt_pt_bank">{{ ($items->bank_sum) ? number_format($items->bank_sum).'원' : '-' }}</td>
        </tr>
        <tr>
          <th>카드입금</th>
          <td class="txt-blue" id="amt_pt_card">{{ ($items->card_sum) ? number_format($items->card_sum).'원' : '-' }}</td>
          <th>예금 차액금</th>
          <td id="amt_pt_diff_pay">{{ ($items->sum_pt_diff_pay) ? number_format($items->sum_pt_diff_pay).'원' : '-' }}</td>
        </tr>
        <tr>
          <th>할인</th>
          <td class="txt-red" id="amt_pt_discount">{{ ($items->sum_pt_discount) ? number_format($items->sum_pt_discount).'원' : '-' }}</td>
          <th>DC</th>
          <td class="txt-red" id="amt_pt_dc">{{ ($items->sum_pt_dc) ? number_format($items->sum_pt_dc).'원' : '-' }}</td>          
        </tr>
        <tr>
          <th>장려금</th>
          <td class="txt-red" id="amt_pt_incentive">{{ ($items->sum_pt_incentive) ? number_format($items->sum_pt_incentive).'원' : '-' }}</td>
          <th>취소</th>
          <td class="txt-red" id="amt_pt_cancel">{{ ($items->sum_pt_cancel) ? number_format($items->sum_pt_cancel).'원' : '-' }}</td>
        </tr>        
        <tr>
          <th>반품</th>
          <td class="txt-red" id="amt_pt_return">{{ ($items->sum_pt_return) ? number_format($items->sum_pt_return).'원' : '-' }}</td>          
          <th>반품입금</th>
          <td class="txt-blue" id="pt_return_receivable">{{ ($items->sum_pt_return_receivable) ? number_format($items->sum_pt_return_receivable).'원' : '-' }}</td>          
        </tr>
        <tr>
          <th>결품</th>
          <td class="txt-red" id="amt_pt_outofstock">{{ ($items->sum_pt_outofstock) ? number_format($items->sum_pt_outofstock).'원' : '-' }}</td>
          <th>결품입금</th>
          <td class="txt-blue" id="amt_pt_outofstock_deposit">{{ ($items->sum_pt_outofstock_deposit) ? number_format($items->sum_pt_outofstock_deposit).'원' : '-' }}</td>
        </tr>
        <tr>
          <th>기사파손</th>
          <td class="txt-red" id="amt_pt_damage_staff">{{ ($items->sum_pt_damage_staff) ? number_format($items->sum_pt_damage_staff).'원' : '-' }}</td>
          <th>물류파손</th>
          <td class="txt-red" id="amt_pt_damage_logistic">{{ ($items->sum_pt_damage_logistic) ? number_format($items->sum_pt_damage_logistic).'원' : '-' }}</td>
        </tr>
        <tr>
          <th>환불처리</th>
          <td id="amt_pt_refund">{{ ($items->pt_refund) ? number_format($items->pt_refund).'원' : '0원' }}</td>            
          <th>환불금액</th>
          <td id="amt_pt_refund_done">{{ ($items->pt_refund_done) ? number_format($items->pt_refund_done).'원' : '0원' }}</td>
        </tr>

        @if($items->od_delivery_step >= 90)
          <tr>
            <th>매출합계</th>
            <td id="amt_pt_subtotal">{{ ($items->sum_pt_subtotal) ? number_format($items->sum_pt_subtotal).'원' : '0원' }}</td>
            <th>채권잔액</th>
            <td id="amt_pt_cur_balance">{{ ($items->pt_cur_balance) ? number_format($items->pt_cur_balance).'원' : '0원' }}</td>
          </tr>
          @if($payment_type == '선불')          
            <tr>
              <th>충전금잔액</th>
              <td id="amt_pt_cur_charge">{{ ($items->pt_cur_charge) ? number_format($items->pt_cur_charge).'원' : '0원' }}</td>                        
              <th>적립금잔액</th>
              <td id="amt_pt_cur_reserve">{{ ($items->pt_cur_reserve) ? number_format($items->pt_cur_reserve).'원' : '0원' }}</td>
            </tr>
          @endif
        @endif

        
      </table>
      
    </div>

  </div>

</body>
</html>

