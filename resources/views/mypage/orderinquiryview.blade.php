@extends('layouts.header')

@section('content')

<div class="sub-container">
  <div class="sub-title-wrap">
    <h4>주문번호 [ {{ request('oid') }} ]</h4>
  </div>

  <div class="order-info-wrap">
    <table class="table1 odr-table1">
      <thead>
        <tr>
          <th class="hide-680">No</th>
          <th>배송일</th>
          <th>주문일시</th>
          <th>결제수단</th>
          <th>주문금액</th>
          <th>상태</th>
        </tr>
      </thead>
      <tbody>
       @foreach($items['order_list'] as $key => $row)

        @php 

      /*
      |--------------------------------------------
      | 좌측 주문상태값 가공 START
      |--------------------------------------------
      */      

      $arr_gubun = explode('#', $row->current_gubun);
      $current_gubun_cnt = !empty($arr_gubun) ? count($arr_gubun) : 0;
      //가장 처음배열의 상태값으로..
      $current_gubun = $arr_gubun[0];

      //결품채권, 반품채권은 >>>>  결품입금, 반품입금으로 표기 변경
      if ($current_gubun == '결품채권') {
          $current_gubun = '결품입금';
      }

      if ($current_gubun == '반품채권') {
          $current_gubun = '반품입금';
      }

      //str_status 가공
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


      
      /*
      |--------------------------------------------
      | 우측 입금 상태값 가공 START
      |--------------------------------------------
      */            

      $color = '';

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
          $color = 'txt-red';
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

      $days = ['일', '월', '화', '수', '목', '금', '토'];
      $str_week = $days[date("w", strtotime($row->od_delivery_date))];      

        @endphp

       <tr data-oid="{{ $row->od_id }}" class="{{ ($row->od_id == request('oid')) ? 'active' : '' }}" style="cursor: pointer;">
          <td class="hide-680">{{ $row->row_num }}</td>
          {{-- <td>{{ $row->od_delivery_date }} ({{ $str_week }})</td> --}}
          <td>{{ date('Y/m/d', strtotime($row->od_delivery_date)) }}({{ $str_week }})</td>
          <td>{{ $row->order_date }}</td>
          {{-- <td>{{ date('Y/m/d h:i:s', strtotime($row->order_date)) }}</td> --}}
          <td>{{ (substr($row->level_ca_id2, -1) == '2') ? '여신' : $row->od_settle_case }}</td>
          <td>{{ number_format($row->pt_sales_delivery) }}</td>
          <td><button type="button" class="btn2 {{ $color }}">{{ $str_od_status }}</button></td>
        </tr>
        @endforeach
      </tbody>
    </table>
    <!-- 클릭이벤트 -->
    <script>
      $(document).ready(function() {
        $(document).on('click', '.odr-table1 > tbody > tr', function() {
          let oid = $(this).data('oid');
          const url = new URL(window.location.href);

          url.searchParams.set("oid", oid);

          location.href = url.toString();
        })
      });
    </script>

    {{-- @php
			$delivery_step_status = [
				'0'  => '입금대기',
				'8'  => '배송완료',
				'10' => '배송대기',
				'85' => '배송대기',
				'90' => '배송완료',
				'99' => '배송완료',
			];
    @endphp --}}
    <h5 class="odr-title">주문내역<span>{{ $current_gubun }} {{ $str_status }} {{ ($current_gubun_cnt > 1) ? '외 ' . ($current_gubun_cnt - 1) . '건' : '' }}</span></h5>

    <table class="table1 odr-table2">
			<thead>
				<tr>
          <th>No</th>
          <th>이미지</th>
					<th>상품명</th>
					<th>수량</th>
					<th>판매가</th>
					<th class="hide-820">보관 / 반품유형</th>
					<th>소계</th>
					<th>상태</th>
				</tr>
      </thead>
			<tbody>
        <!-- 반복 -->
        @foreach($items['cart_list'] as $key => $row)
        @php
          $image_url = 'images/item/'.$row->it_img1;
        @endphp
        <tr>
          <td>{{ $loop->iteration }}</td>
          <td>
            @if(file_exists(public_path($image_url)) && $row->it_img1)
              <img src="{{ asset($image_url) }}" alt="">
            @else
              <img src="{{ asset('images/common/no_image.gif') }}">
            @endif
          </td>
          <td class="od-td1">{{ $row->it_name }}
            <span>({{ $row->it_basic }})</span>
          </td>
          <td>{{ $row->ct_qty }}</td>
          <td>{{ number_format($row->ct_price) }}원</td>
          <td class="od-td2 hide-820">
            @php
              $it_storage_str = array(
                "1"=>"<span class='room_temp'>상온</span>",
                "2"=>"<span class='frozen_temp'>냉동</span>",
                "3"=>"<span class='low_temp'>냉장</span>",
                "4"=>""
              );

              if ($row->it_return_use == '1') {
                $it_return_str = array(
                  "1"=>"<span class='return_o'>반품가능</span>",
                  "2"=>"<span class='return_x'>반품불가</span>"
                );

              } else {
                $it_return_str = array(
                  "1"=>"",
                  "2"=>""
                );
              }
              
            @endphp
            {!! $it_storage_str[$row->it_storage] !!}{!! $it_return_str[$row->it_return] !!}
          </td>
          <td>{{ number_format($row->ct_price * $row->ct_qty) }}</td>

          @php
            $ct_cate = $row->ct_cate;

            if ($ct_cate != '납품') {
              $color = 'txt-blue';
            } else {
              $color = '';
            }

            //결품채권, 반품채권은 >>>>  결품입금, 반품입금으로 표기 변경
            if ($ct_cate == '결품채권') {
                $ct_cate = '결품입금';
            }

            if ($ct_cate == '반품채권') {
                $ct_cate = '반품입금';
            }
          @endphp
          <td class="{{ $color }}">{{ $ct_cate }}</td>
        </tr>
        @endforeach
        <!-- 반복 끝 -->
      </tbody>
      <tfoot>
        <tr>
        @if ( count($items['cart_list']) > 0)        
          <th colspan="8">주문총액 <strong>{{ number_format($items['order_info']->od_cart_price + $items['order_info']->pt_delivery) }}</strong></th>
        @else
          <td colspan="8" height="50">상품 정보가 없습니다.</td>
        @endif
        </tr>
      </tfoot>
		</table>
  </div>

  <div class="flex-center" style="margin-bottom:1.5rem; gap:0.5rem;">
      <button type="button" class="btn1 big-btn" onclick="location.href='/mypage/orderinquiry';">목록 보기</button>

    @php
    $card_custom_cancel = false;

    $od_status = $items['order_info']->od_status;

    switch ($items['order_info']->od_settle_case) {
      case '신용카드': $card_custom_cancel = false; break;
      case '계좌이체': $card_custom_cancel = true; break;
      case '가상계좌': $card_custom_cancel = true; break;
    }

    $payment_type = '';
    if (isset($items['order_info']->level_ca_id2)) {
        if (strlen($items['order_info']->level_ca_id2) == 4) {
            if (substr($items['order_info']->level_ca_id2, -1) == '1') $payment_type = '선불';
            if (substr($items['order_info']->level_ca_id2, -1) == '2') $payment_type = '후불';
        }
    }

    @endphp
    @if ($card_custom_cancel && ($od_status == '주문' || $od_status == '입금'))
    <div class="sod_fin_cancelfrm">
      <input type="hidden" id="hide_od_id" value="{{ request('oid') }}" />
      <button type="button" class="btn1 big-btn" onclick="orderCancel('all')">주문 취소</button>
    </div>
    @endif

    @if (!$card_custom_cancel && ($od_status == '주문'))
    <div class="sod_fin_cancelfrm">
      <input type="hidden" id="hide_od_id" value="{{ request('oid') }}" />
      <button type="button" class="btn1 big-btn" onclick="orderCancel('all')">주문 취소</button>
    </div>
    @endif
  </div>


  <div class="pay-info-wrap">
    <div class="pay-result-table">
      <!-- 주문취소 사용 -->
      <input type="hidden" id="bf_od_delivery_date" value="{{ $items['order_info']->od_delivery_date }}" />
      <input type="hidden" id="bf_od_delivery_day" value="{{ $items['order_info']->od_delivery_day }}" />
      <table>
        <tr>
          <th>주문번호</th>
          <td>{{ $items['order_info']->od_id }}</td>
        </tr>
        
        <tr>
          <th>주문일</th>
          <td>{{ $items['order_info']->od_time }} ({{ \Carbon\Carbon::parse($items['order_info']->od_time)->locale('ko')->dayName }})</td>
        </tr>
        
        <tr>
          <th>배송일</th>
          <td>{{ $items['order_info']->od_delivery_date }} ({{ \Carbon\Carbon::parse($items['order_info']->od_delivery_date)->locale('ko')->dayName }})</td>
        </tr>
        
        <tr>
          <th>상호/이름</th>
          <td>{{ $items['order_info']->od_company }} / {{ $items['order_info']->od_name }}</td>
        </tr>
        
        <tr>
          <th>휴대폰번호</th>
          <td>{{ $items['order_info']->od_tel }}</td>
        </tr>
        
        <tr>
          <th>전화번호</th>
          <td>{{ $items['order_info']->od_hp }}</td>
        </tr>
        <tr>
          <th>이메일</th>
          <td>{{ $items['order_info']->od_email }}</td>
        </tr>
        <tr>
          <th>전용계좌</th>
          <td>{{ $activeMember['mb_virtual_bank']. ' '.$activeMember['mb_virtual_account'] }}</td>
        </tr>
        <tr>
          <th>배송주소</th>
          <td>{{ $items['order_info']->od_addr1 }} {{ $items['order_info']->od_addr2 }}</td>
        </tr>
        <tr>
          <th>요청사항</th>
          <td>{{ $items['order_info']->od_memo }}</td>
        </tr>
      </table>

      <table>
        <tr>
          <th>주문금액</th>
          <td>{{ number_format($items['order_info']->od_cart_price) }}원</td>
        </tr>
        <tr>
          <th>배송비</th>
          <td>{{ number_format($items['order_info']->pt_delivery) }}원</td>
        </tr>

        <tr>
          <th>합계금액</th>
          <td class="blue">{{ number_format($items['order_info']->od_cart_price + $items['order_info']->pt_delivery) }}원</td>
        </tr>


        @if ($payment_type == '선불')

          @if ( $items['order_info']->od_temp_point )
          <tr>
            <th>충전금사용</th>
            <td>{{ number_format($items['order_info']->od_temp_point) ?? '0' }}원</td>
          </tr>
          @endif
          @if ( $items['order_info']->od_temp_point_reserve )
          <tr>
            <th>적립금사용</th>
            <td>{{ number_format($items['order_info']->od_temp_point_reserve) ?? '0' }}원</td>
          </tr>
          @endif
          @if ($items['order_info']->od_settle_case == '금융권가상계좌' || $items['order_info']->od_settle_case == '신용카드' || $items['order_info']->od_settle_case == '무통장입금')
          <tr>
            <th>{{ $items['order_info']->od_settle_case }}</th>
            <td>{{ number_format($items['order_info']->od_receipt_price) ?? '0' }}원</td>
          </tr>
          @endif

          <tr>
            <th>총결제액</th>
            @php
              $od_temp_point          = $items['order_info']->od_temp_point ?? 0;
              $od_temp_point_reserve  = $items['order_info']->od_temp_point_reserve ?? 0;
              $od_receipt_price       = $items['order_info']->od_receipt_price ?? 0;
              $total_pay = $od_temp_point + $od_temp_point_reserve + $od_receipt_price;
            @endphp
            <td class="blue">{{ number_format($total_pay) }}원</td>
          </tr>
          <tr>
            <th>미결제액</th>
            <td class="red">{{ number_format($items['order_info']->od_misu) }}원</td>
          </tr>

          <tr>
            <th>결제수단</th>
            @php
            $settleLabel = null;
            if ($items['order_info']->od_settle_case == '충전금' || $items['order_info']->od_settle_case == '적립금' || $items['order_info']->od_settle_case == '충전금+적립금') {
                $settleLabel = '';
            } else {
                $settleLabel = $items['order_info']->od_settle_case;
            }
            @endphp
            <td>
              {{ implode(' / ', array_filter([
                  $items['order_info']->od_temp_point ? '충전금' : null,
                  $items['order_info']->od_temp_point_reserve ? '적립금' : null,
                  $settleLabel,
              ])) }}
            </td>
          </tr>
          <tr>
            <th>{{ $items['order_info']->od_settle_case == '신용카드' ? '결제자명' : '입금자명' }}</th>
            <td>{{ $items['order_info']->od_deposit_name }}</td>
          </tr>
          <tr>
            <th>{{ $items['order_info']->od_settle_case == '신용카드' ? '결제카드' : '입금계좌' }}</th>
            <td>{{ $items['order_info']->od_bank_account }}</td>
          </tr>

        @endif

      </table>
    </div>
  </div>
</div>


<script>
let cancelLock = false;

function orderCancel(e) {
  const od_id = $('#hide_od_id').val();

  let bf_od_delivery_date = $('#bf_od_delivery_date').val();
  let bf_od_delivery_day = $('#bf_od_delivery_day').val();

  let d_order_day = $('#d_order_day').val();
  let d_order_date = $('#d_order_date').val();

  let bf_concat_delivery = bf_od_delivery_date + '(' +bf_od_delivery_day+')';
  let d_concat_delivery = d_order_date + '(' +d_order_day+')';


  if (!od_id) {
    validationAlertMessage('취소할 주문을 선택 하세요.');
    return false;
  }

  if (e == 'all') {
    var message = `<p style="font-weight:bold;">
        주문하신 상품을 어떻게 처리하시겠습니까?
      </p>
      <p style="color:#d33; font-size:15px;">
        선택 후에는 변경할 수 없습니다.
      </p>`;

    if (bf_concat_delivery != d_concat_delivery) {
      message += `
      <p style="font-weight:bold; color:#d33; font-size:13px;">
      주문 시 기존 배송일이 ${bf_concat_delivery} 에서 ${d_concat_delivery} 으로 변경됩니다.
      </p>
      `;
    }

    Swal.fire({
      toast: false,
      title: '주문 취소 후 처리 방법',
      html: message,
      icon: 'warning',

      showConfirmButton: true,
      showDenyButton: true,
      showCancelButton: true,

      confirmButtonText: '장바구니로 이동',
      denyButtonText: '상품 삭제',
      cancelButtonText: '닫기',

      confirmButtonColor: '#3085d6',
      denyButtonColor: '#d33',
      cancelButtonColor: '#333'

    }).then((result) => {
      // 장바구니 이동
      if (result.isConfirmed) {
        requestCancel(od_id, 'yes', '/mypage/cart');
      }

      // 상품 삭제
      else if (result.isDenied) {
        requestCancel(od_id, 'no', '/mypage/orderinquiry');
      }

      else if (result.isDismissed) {
        cancelLock = false;
      }
    });
  }
}

function validationAlertMessage(message)  {
  Swal.fire({
      toast: false,
      icon: 'warning',
      title: '알림',
      html: message,
      confirmButtonText: '확인'
  });
}

function requestCancel(od_id, move_type, redirectUrl) {
  $.post('/mall/order/order_cancel', {
    inbound_path: 'mall',
    od_id: od_id,
    move_type: move_type
  }).done(function(res) {
    if (res.status === 'success') {
      Swal.fire({
        title: '알림',
        icon: 'success',
        html: '주문이 취소되었습니다.',
        confirmButtonText: '확인'
      }).then(() => {
        location.href = redirectUrl;
      });
    } else {
      alert(res.message);
    }
  }).fail(function(xhr) {
    const res = xhr.responseJSON;
    alert(res?.message || '서버 오류 발생');
  }).always(function() {
    cancelLock = false;
  });
}

</script>
@endsection