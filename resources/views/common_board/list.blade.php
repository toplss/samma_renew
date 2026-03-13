@extends('layouts.header')

@section('content')

<div class="sub-container">
  <div class="sub-title-wrap">
    <h4>공지사항</h4>  
  </div>

  <!-- 배너 영역 -->
  <div class="sub-list-slide">
        {!! $items->other['banner'] !!}
  </div>
  <!-- 배너 영역 -->

  <div class="board-wrap">
    <table class="table1 board-list">
      <thead>
        <tr>
          <th>번호</th>
          <th>제목</th>
          <th class="hide-680">작성자</th>
          <th class="hide-680">작성일</th>
          <th>조회수</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($items as $key => $row)
        <tr onclick="location.href='/common_board/view?bd_num={{ $row->bd_num }}'">
          <td class="num">{!! $row->bd_notice == 1 ? '<span>공지</span>' : $row->row_num !!}</td>
          <td class="tit"><span style="color:{{ $row->bd_ext1 }}; cursor:pointer;" >{{ $row->bd_subject }}</span></td>
          <td class="writer hide-680">{{ $row->bd_name }}</td>
          <td class="date hide-680">{{ $row->write_date }}</td>
          <td class="hit">{{ $row->bd_view_count }}</td>
        </tr>
        @endforeach
        @if ($items->total() == 0)
        <tr><td colspan="5">게시글이 존재하지 않습니다.</td></tr>
        @endif
      </tbody>
    </table>

    <!-- 관리자일때만 보이는 버튼 -->
    <div class="flex-end">
      @if (isset($activeMember) && $access_level < 3)
      <button type="button" class="btn1" onclick="location.href='/common_board/write'">글쓰기</button>
      @endif
    </div>

    <form name="search_form" id="search_form" method="get" action="{{ route('notice') }}" accept-charset="utf-8">
      <select name="ss" id="ss">
        <option value="1" {{ request('ss') == '1' ? 'selected' : '' }}>전체</option>
        <option value="4" {{ request('ss') == '4' ? 'selected' : '' }}>제목</option>
        <option value="6" {{ request('ss') == '6' ? 'selected' : '' }}>제목+내용</option>
        <option value="3" {{ request('ss') == '3' ? 'selected' : '' }}>작성자</option>
      </select>
      <input type="text" name="search_kw" id="search_kw" value="{{ request('search_kw') }}" >
      <input type="submit" value="검색" class="btn3">
      <input type="button" value="취소" class="btn2" onclick="location.href='?'">
    </form>
  </div>

{{ $items->links() }}

</div>




@endsection


