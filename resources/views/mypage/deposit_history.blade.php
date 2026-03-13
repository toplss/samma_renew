@extends('layouts.header')

@section('content')

<div class="sub-container">
  <div class="sub-title-wrap">
      <h4>입금내역</h4>  
  </div>
  
  <div class="point-wrap">

    @include('layouts.mypage_top')

    <!-- <p class="mypoint">보유충전금 <b>{{ number_format($activeMember['mb_point']) }}</b><small>원</small></p> -->

    <table class="table1 point-table">
      <colgroup>
        <col style="width:5%;" class="hide-820">
        <col style="width:5%;">
        <col style="width:15%;">
        <col style="width:15%;">
        <col style="width:15%;">
        <col style="width:15%;">
        <col style="width:15%;">
        <col style="width:15%;">
      </colgroup>
      <thead>
        <tr>
          <th class="hide-820">번호</th>
          <th>날짜</th>
          <th>현금입금</th>
          <th>예금입금</th>
          <th>카드입금</th>
          <th>장려금</th>
          <th>DC</th>
          <th>합계</th>
        </tr>
      </thead>
      <tbody>

{{-- @dd($items['data']); --}}

        @foreach($items['data'] as $key => $row)
        <tr>
          <td class="hide-820">{{ $items->firstItem() + $key }}</td>
          <td>{{ \Carbon\Carbon::parse($row->od_delivery_date)->format('y/m/d') }}</td>
          <td class="txt-blue">{{ ( $row->pt_cash > 0 ) ? number_format($row->pt_cash) . '원' : '-' }}</td>
          <td class="txt-blue">{{ ( $row->pt_bank > 0 ) ? number_format($row->pt_bank) . '원' : '-' }}</td>
          <td class="txt-blue">{{ ( $row->pt_card > 0 ) ? number_format($row->pt_card) . '원' : '-' }}</td>
          <td class="txt-blue">{{ ( $row->pt_incentive > 0 ) ? number_format($row->pt_incentive) . '원' : '-' }}</td>
          <td class="txt-blue">{{ ( $row->pt_dc > 0 ) ? number_format($row->pt_dc) . '원' : '-' }}</td>
          <td class="txt-blue">{{ ( $row->row_total > 0 ) ? number_format($row->row_total) . '원' : '-' }}</td>
        </tr>
        @endforeach

        <!-- 없을때 -->
        @if($items->total() < 1)
        <tr>
        <td colspan="8" height="100">내역이 없습니다.</td>
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