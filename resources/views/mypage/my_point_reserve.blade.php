@extends('layouts.header')

@section('content')

<div class="sub-container">
  <div class="sub-title-wrap">
      <h4>적립금내역</h4>  
  </div>
  
  <div class="point-wrap">
    
    @include('layouts.mypage_top')

    <p class="mypoint"><span>적립금 정산은 주문 배송일에 정산 예정입니다.</span></p>

    <table class="table1 point-table">
      <colgroup>
        <col style="width:10%;" class="hide-820">
        <col style="width:15%;">
        <col style="width:15%;">
        <col style="width:20%;">
        <col style="width:20%;">
        <col style="width:20%;">
      </colgroup>
      <thead>
        <tr>
          <th class="hide-820">번호</th>
          <th>날짜</th>
          <th>구분</th>
          <th>적립</th>
          <th>채권</th>
          <th>사용</th>
        </tr>
      </thead>
      <tbody>

        @foreach($items['data'] as $index => $row)

          @php
            if ($row->od_delivery_step == 8) {  //잔액조정
              $gubun = '잔액';
            } elseif ($row->po_point_type == 'increase'){
              $gubun = '적립';
            } elseif ($row->po_point_type == 'bond'){
              $gubun = '입금';
            } elseif ($row->po_point_type == 'decrease'){
              $gubun = '사용';              
            }

            //적립금 상세내역 가공
            $comment = '';
            $actions = explode('#', $row->po_action);
            $action_cnt = !empty($actions) ? count($actions) : 0;

            foreach ($actions as $key => $action) {
              
              $str_po_action = [
                "pt_reserve"=>"주문",
                "pt_buy_reserve"=>"관리자 충전",
                "pt_cancel"=>"주문취소",
                "pt_return"=>"반품",
                "pt_return_receivable"=>"반품입금",
                "pt_outofstock"=>"결품",
                "pt_outofstock_deposit"=>"결품입금",
                "pt_damage_staff"=>"기사파손",
                "pt_damage_logistic"=>"물류파손",
                "pt_incentive"=>"장려금",
                "pt_dc"=>"DC",                
                "modify"=>"잔액", //잔액조정
              ];              

              $amount_po_action = [
                "pt_reserve"=>$row->pt_reserve,
                "pt_cancel"=>$row->pt_cancel,
                "pt_return"=>$row->pt_return,
                "pt_return_receivable"=>$row->pt_return_receivable,
                "pt_outofstock"=>$row->pt_outofstock,
                "pt_outofstock_deposit"=>$row->pt_outofstock_deposit,
                "pt_damage_staff"=>$row->pt_damage_staff,
                "pt_damage_logistic"=>$row->pt_damage_logistic,
                "pt_incentive"=>$row->pt_incentive,
                "pt_dc"=>$row->pt_dc,                
                "pt_buy_reserve"=>$row->pt_buy_reserve,
                "modify"=>$row->pt_buy_reserve, //잔액조정
              ];

              //여러 건인 경우 제일 처음건으로 표기
              if ($key > 0) {
                  $po_action = $str_po_action[$actions[0]];
              } else {
                  $po_action = $str_po_action[$action] ?? '';
              }

              $comment .= "<li>
                  <span>" 
                  . $str_po_action[$action] . " " 
                  . (($gubun == '잔액' || $gubun == '입금') ? '' : $gubun)  //반품입금, 결품입금은 이미 입금이라는 단어가 포함되어 있으므로 $gubun값은 빈값으로 처리함
                  . "</span>
                  <b>" . number_format($amount_po_action[$action]) . "원</b>
              </li>";

            }

          @endphp
        <tr>
          <td class="hide-820">{{ $items->firstItem() + $index }}</td>
          <td>{{ \Carbon\Carbon::parse($row->od_delivery_date)->format('y/m/d') }}</td>
          
          <td>
            <span class="pt1" onclick="javascript:point_reserve_showDetail('all', '{{ $row->po_action }}', '{{ $row->od_id }}', '{{ $row->change_point }}');">
              {{ $po_action }} {{ ($action_cnt > 1) ? '외 ' . ($action_cnt - 1) . '건' : '' }}
            </span>
          </td>

          <td class="{{ ( $row->increase_point > 0 ) ? 'txt-red' : '' }}">

            @if($row->increase_point > 0)
              <span style="cursor:pointer"
                    onclick="point_reserve_showDetail('increase', '{{ $row->po_action }}', '{{ $row->od_id }}', '{{ $row->increase_point }}')">
                    {{ number_format($row->increase_point) }}원
              </span>
            @else
              -
            @endif

          </td>

          <td class="{{ ( $row->bond_point > 0 ) ? 'txt-blue' : '' }}">

            @if($row->bond_point > 0)
              <span style="cursor:pointer"
                    onclick="point_reserve_showDetail('bond', '{{ $row->po_action }}', '{{ $row->od_id }}', '{{ $row->bond_point }}')">
                    {{ number_format($row->bond_point) }}원
              </span>
            @else
              -
            @endif

          </td>

          <td class="{{ ( $row->decrease_point > 0 ) ? 'txt-blue' : '' }}">

            @if($row->decrease_point > 0)
              <span style="cursor:pointer"
                    onclick="point_reserve_showDetail('decrease', '{{ $row->po_action }}', '{{ $row->od_id }}', '{{ $row->decrease_point }}')">
                    {{ number_format($row->decrease_point) }}원
              </span>
            @else
              -
            @endif

          </td>

        </tr>
        @endforeach

        <!-- 없을때 -->
        @if($items->total() < 1)
        <tr>
          <td colspan="6" height="100">내역이 없습니다.</td>
        </tr>
        @endif

      </tbody>
    </table>

  </div>
    {{ $items->links() }}

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

  //구분 클릭 시 적립금 상세내역 
  function point_reserve_showDetail(mode, po_action, od_id, change_point) {

    po_action = po_action.replace(/#/g, '|');

    console.log(po_action);

    $.ajax({
      url : "{{ route('my_point_reserve_detail_pop') }}",
      type : "get",
      data : {
        "mode" : mode,
        "po_action" : po_action,
        "od_id" : od_id,
        "change_point" : change_point,
      },
      dataType : "html",
      success : function(result) {
        Swal.fire({
          html: result,
          icon: 'info',
          confirmButtonText: '확인',
          width: 'auto'
        });    

      },
      error: function(e) {
            console.log('에러 발생:', e);
      }
    });

  }

</script>
@endsection