@extends('layouts.header')

@section('content')

<div class="sub-container">
	<div class="sub-title-wrap">
    <h4>공지사항</h4>  
  </div>

	<div class="board-wrap">
		<div class="board-view">
			<div class="board-title">
				<h6><p style="color: {{ $item->bd_ext1 }};">{{ $item->bd_subject }}</p></h6>
				<ul class="flex-end">
					<li>작성자 : {{ $item->bd_name }}</li>
					<li>작성일 : {{ $item->write_date }}</li>
					<li class="hide-680">조회수 : {{ $item->bd_view_count }}</li>
				</ul>
			</div>

			<div class="board-content">
				{!! str_replace('src="/', 'src="https://samma-erp.com/', $item->bd_content) !!}
			</div>
			<ul class="board-attachment">
				@if(isset($item->files) && $item->files->count() > 0)

				@foreach ($item->files as $index => $file)
				<li><span>첨부</span><a href="/common_board/file_download?f_k={{ $file->idx }}" >{{ $file->file_orgin_name }}</a></li>
				@endforeach

				@endif
			</ul>
			<div class="flex-end" style="gap:0.5rem;">
				@if ($access_level < 3) 	
					<button type="button" class="btn3" onclick="location.href='/common_board/write?bd_num={{ request('bd_num') }}'">수정</button>
					<button type="button" class="btn3" onclick="delete_form()">삭제</button>
				@endif
				<button type="button" class="btn1" onclick="location.href='/common_board/list'">목록</button>
			</div>

		</div>

		<form name="search_form" id="search_form" method="get" action="{{ route('notice') }}"  accept-charset="utf-8">
			<select name="ss" id="ss">
				<option value="1" {{ request('ss') == '1' ? 'selected' : '' }}>전체</option>
				<option value="4" {{ request('ss') == '4' ? 'selected' : '' }}>제목</option>
				<option value="6" {{ request('ss') == '6' ? 'selected' : '' }}>제목+내용</option>
				<option value="3" {{ request('ss') == '3' ? 'selected' : '' }}>작성자</option>
			</select>
			<input type="text" name="search_kw" id="search_kw" value="{{ request('search_kw') }}" >
			<input type="submit" value="검색" class="btn3">
			<input type="button" value="취소" class="btn2" onclick="location.href='/common_board/list'">
		</form>

	</div>
		
</div>
@if ($access_level < 3) 
<form id="board_frm" action="{{ route('notice_delete') }}" method="post">
	@csrf
	<input type="hidden" name="bd_num" id="bd_num" value="{{ $item->bd_num }}">
</form>

<script>
function delete_form() {
	Swal.fire({
		title: '삭제',
		html: '정말 삭제하시겠습니까?',
		icon: 'question',
		showCancelButton: true,
		confirmButtonText: '확인',
		cancelButtonText: '취소'
	}).then((result) => {
		if (result.isConfirmed) {
			document.getElementById('board_frm').submit();
		}
	});
}	
</script>
@endif
@endsection


