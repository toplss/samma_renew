@extends('layouts.header')

@section('content')

<div class="sub-container">
  <div class="sub-title-wrap">
      <h4>충전금내역</h4>  
  </div>
  
  <div class="point-wrap">

    @include('layouts.mypage_top')

    <!-- <p class="mypoint">보유충전금 <b>{{ number_format($activeMember['mb_point']) }}</b><small>원</small></p> -->

    <table class="table1 point-table">
      <colgroup>
        <col style="width:10%;" class="hide-820">
        <col style="width:15%;">
        <col style="width:22%;">
        <col style="width:22%;">
        <col style="width:22%;">
        {{-- <col style="width:auto;"> --}}

      </colgroup>
      <thead>
        <tr>
          <th class="hide-820">번호</th>
          <th>날짜</th>
          <th>구분</th>
          <th>적립</th>
          <th>사용</th>
          {{-- <th>충전금잔액</th> --}}
        </tr>
      </thead>
      <tbody>

        @foreach($items as $key => $row)
          @php
            if ($row->od_delivery_step == 8) {  //잔액조정
              $gubun = '잔액';
            } elseif ($row->po_point_type == 'increase'){
              $gubun = '적립';
            } elseif ($row->po_point_type == 'decrease'){
              $gubun = '사용';
            }

            $arr_po_action = [
              "od_use"=>"주문",
              "od_cancel"=>"주문취소",
              "pt_charge"=>"주문",
              "pt_buy_charge"=>"충전",
              ];

            $po_action = $arr_po_action[$row->po_action] ?? '';
            $od_id = $row->od_id;
          @endphp
        <tr>
          <td class="hide-820">{{ $items->firstItem() + $key }}</td>
          <td>{{ \Carbon\Carbon::parse($row->reg_date)->format('y/m/d') }}</td>
          <td>{{ $po_action }} {{ $gubun }}</td>
          <td class="{{ ($gubun == '적립' or $gubun == '잔액') ? 'txt-red' : '' }}">{{ ($gubun == '적립' or $gubun == '잔액') ? number_format($row->change_point) . '원' : '-' }}</td>
          <td class="{{ ($gubun == '사용') ? 'txt-blue' : '' }}">{{ ($gubun == '사용') ? number_format($row->change_point) . '원' : '-' }}</td>
          {{-- <td>{{ number_format($row->current_point) }}원</td> --}}
        </tr>
        @endforeach

        <!-- 이월 -->
        {{-- @if($items->total() > 0)
        <tr class="txt-bold" style="background-color:#f5f5f5;">
          <td class="hide-820"></td>
          <td>이월</td>
          <td colspan="3"></td>
          <td>
            @if($carry_balance > 0)
                {{ number_format($carry_balance) }}원
            @else
                -
            @endif            
          </td>
        </tr>
        @endif --}}


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
  
</script>


@endsection