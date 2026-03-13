@extends('layouts.header')

@section('content')

<div class="sub-container">
  <div class="sub-title-wrap">
    <h4>보유장비</h4>  
  </div>
  
  @include('layouts.mypage_top')

  <table class="equip-list">
    <thead>
      <tr>
        <th class="hide-820">번호</th>
        <th class="hide-820">장비유형</th>
        <th class="hide-820">이미지</th>
        <th>분류명</th>
        <th>장비명<span class="show-820">입고일 | 회수일</span></th>
        <th class="hide-1280">장비번호</th>
        <th class="hide-820">수량</th>
        <th>합계</th>
        <th>상태</th>
        <th class="hide-820">입고일<br>회수일</th>
        <th class="hide-820">A/S요청</th>
      </tr>
    </thead>
    <tbody>
      @php
        $specific_item_str = array("0"=>"일반","1"=>"<span style='color:#da2727;'>특정</span>");
        $it_id_type_str = array("4"=>"<span style='color:#0000ff;'>판매</span>","5"=>"임대","7"=>"<span style='color:#e02f30;'>회수</span>");
        $prev_it_id = null;
      @endphp
      @foreach($items as $key => $row)
      <tr data-group="{{ $row->it_cnt }}" class="{{ $row->t_it_id_type == 7 ? 'withdraw' : '' }}">
        <td class="hide-820">{{ $row->row_num }}</td>
        <td class="hide-820">{!! $specific_item_str[$row->t_specific_item] !!}</td>
        <td class="hide-820"><img src="{{ asset('images/item/'.$row->it_img1) }}" alt=""></td>
        <td>
          <img class="show-820" src="{{ asset('images/item/'.$row->it_img1) }}" alt="">
          {{ $row->ca_name }}
          <span class="show-820">{!! $specific_item_str[$row->t_specific_item] !!}</span>
        </td> <!-- 분류명 -->
        <td>
          {{ $row->it_name }}
          <span class="show-820">{{ date('Y-m-d', strtotime($row->t_receipt_date)) }} | {{ $row->t_return_date }}</span>
        </td> <!-- 장비명 -->
        <td class="hide-1280">{{ $row->t_p_code }}</td>
        <td class="hide-820">{{ $row->t_it_qty }}</td>
        @if ($prev_it_id !== $row->it_id)
          <td rowspan="{{ $row->it_cnt }}">{{ $row->it_cnt }}</td> <!-- 합계 -->
        @endif
        <td>
          {!! $it_id_type_str[$row->t_it_id_type] !!}
          @if($row->t_it_id_type != 7)<span class="show-820"><button type="button" class="btn3" onclick="location.href='/customer_service/as_reception?idx={{ $row->idx }}'; ">요청</button></span>@endif
        </td> <!-- 상태 -->
        <td class="hide-820">
          {{ date('Y-m-d', strtotime($row->t_receipt_date)) }}<br>
          @if ($row->t_return_date)
            <span style="color:#e02f30;">{{ $row->t_return_date }}</span>
          @else
              -
          @endif
        </td>
        <td class="hide-820">@if($row->t_it_id_type != 7)<button type="button" class="btn3" onclick="location.href='/customer_service/as_reception?idx={{ $row->idx }}'; ">요청</button>@endif</td>
      </tr>
      @php
        $prev_it_id = $row->it_id;
      @endphp
      @endforeach

      <!-- 없을때 -->
      @if($items->count() < 1)
      <tr>
      <td colspan="11" height="100">내역이 없습니다.</td>
      </tr>
      @endif

    </tbody>
  </table>
</div>

@endsection


