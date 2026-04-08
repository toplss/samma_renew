@extends('layouts.header')

@section('content')

<div class="sub-container">
  <div class="sub-title-wrap">
      <h4>입금내역</h4>  
  </div>
  
  <div class="point-wrap">

    @include('layouts.mypage_top')

    <div style="width:100%; overflow-x:auto;">
      <table class="table1 point-table mobile-card" style="margin:auto">
        <thead>
          <tr>
            <th class="c-hide">번호</th>
            <th>날짜</th>
            <th>현금입금</th>
            <th>예금입금</th>
            <th>카드입금</th>
            <th>반품입금</th>
            <th>결품입금</th>

            <!-- 후불업체인 경우만 표기 -->
            @if( $activeMember['level_ca_id2_name'] == '후불' )
              <th>장려금</th>
              <th>DC</th>
              <th>기사파손</th>
              <th>물류파손</th>
              <th>반품</th>            
              <th>취소</th>
              <th>결품</th>            
            @endif
            
            <th>합계</th>
          </tr>
        </thead>
        <tbody>

          @foreach($items['data'] as $key => $row)
          <tr>
            <td class="c-hide" data-label="번호">{{ $items->firstItem() + $key }}</td>
            <td data-label="날짜">{{ \Carbon\Carbon::parse($row->od_delivery_date)->format('y/m/d') }}</td>
            <td class="txt-blue" data-label="현금입금">{{ ( $row->pt_cash > 0 ) ? number_format($row->pt_cash) . '원' : '-' }}</td>
            <td class="txt-blue" data-label="예금입금">{{ ( $row->pt_bank > 0 ) ? number_format($row->pt_bank) . '원' : '-' }}</td>
            <td class="txt-blue" data-label="카드입금">{{ ( $row->pt_card > 0 ) ? number_format($row->pt_card) . '원' : '-' }}</td>
            <td class="txt-blue" data-label="반품입금">{{ ( $row->pt_return_receivable > 0 ) ? number_format($row->pt_return_receivable) . '원' : '-' }}</td>
            <td class="txt-blue" data-label="결품입금">{{ ( $row->pt_outofstock_deposit > 0 ) ? number_format($row->pt_outofstock_deposit) . '원' : '-' }}</td>

            <!-- 후불업체인 경우만 표기 -->
            @if( $activeMember['level_ca_id2_name'] == '후불' )            
              <td class="txt-blue" data-label="장려금">{{ ( $row->pt_incentive > 0 ) ? number_format($row->pt_incentive) . '원' : '-' }}</td>
              <td class="txt-blue" data-label="DC">{{ ( $row->pt_dc > 0 ) ? number_format($row->pt_dc) . '원' : '-' }}</td>
              <td class="txt-blue" data-label="기사파손">{{ ( $row->pt_damage_staff > 0 ) ? number_format($row->pt_damage_staff) . '원' : '-' }}</td>
              <td class="txt-blue" data-label="물류파손">{{ ( $row->pt_damage_logistic > 0 ) ? number_format($row->pt_damage_logistic) . '원' : '-' }}</td>
              <td class="txt-blue" data-label="반품">{{ ( $row->pt_return > 0 ) ? number_format($row->pt_return) . '원' : '-' }}</td>            
              <td class="txt-blue" data-label="취소">{{ ( $row->pt_cancel > 0 ) ? number_format($row->pt_cancel) . '원' : '-' }}</td>
              <td class="txt-blue" data-label="결품">{{ ( $row->pt_outofstock > 0 ) ? number_format($row->pt_outofstock) . '원' : '-' }}</td>            
            @endif
            
            <td class="txt-blue" data-label="합계">{{ ( $row->row_total > 0 ) ? number_format($row->row_total) . '원' : '-' }}</td>
          </tr>
          @endforeach

          <!-- 없을때 -->
          @if($items->total() < 1)
          <tr>
          <td colspan="15" height="100">내역이 없습니다.</td>
          </tr>
          @endif

        </tbody>
      </table>
    </div>
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

<!-- 모바일에서 빈 항목 제거 -->
<script>
  $(function(){
    if (window.innerWidth <= 680) {
      $('.mobile-card td').each(function () {
        if ($(this).text().trim() === '-') {
          $(this).hide();
        }
      });
    }    
  })

</script>


@endsection