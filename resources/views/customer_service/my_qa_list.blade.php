@extends('layouts.header')

@section('title', '상담문의 페이지')

@section('content')

<div class="sub-container">
  <div class="sub-title-wrap">
    <h4>상담문의</h4>  
  </div>
  
  @include('layouts.mypage_top')

  <div class="qa-wrap">
    <div class="qbtns">
      <button type="button"class="btn1" onclick="location.href='/customer_service/my_qa_write'">상품 문의하기</button>
      <button type="button" class="btn3" onclick="location.href='/customer_service/my_contact_write'">1:1 문의하기</button>
    </div>
    <table class="table1 qa-table">
      <thead>
        <tr>
          <th class="hide-1024">번호</th>
          <th>상품정보</th>
          <th>문의종류</th>
          <th>작성일</th>
          <th>상태</th>
          <th>관리</th>
        </tr>
      </thead>
      <tbody>
        @foreach($items as $key => $row)
        <tr>
          <td class="hide-1024" data-label="번호">{{ $row->row_num }}</td>
          <td class="qa1" data-label="상품정보" onclick="view_qa(`{{ $row->table_name }}`, {{ $row->iq_id }})" style="cursor: pointer;">
            @php
            $image_url = 'images/item/'.$row->it_img1;
            @endphp
            <div class="flex items-center">
              @if(file_exists(public_path($image_url)) && $row->it_img1)
              <img src="{{ asset($image_url) }}"/>
              @else
              <img src="{{ asset('images/common/no_image.gif') }}"/>
              @endif
              <p>{{ $row->it_name }}</p>
            </div>
          </td>
          <td data-label="문의종류">{{ $row->iq_gubun }}</td>
          <td data-label="작성일">{{ date('Y/m/d', strtotime($row->iq_time)) }}</td>
          <td data-label="상태">
            @if ($row->iq_answer)
            <span>답변완료</span>
            @else
            <span>답변대기</span>
            @endif
          </td>
          <td class="qa2" data-label="관리">
            <div class="flex-center">
              <button type="button" class="btn1 flex-center" onclick="modi_qa(`{{ $row->table_name }}`, {{ $row->iq_id }})"><img src="{{ asset('images/icon/edit.svg') }}" alt="icon"><span>수정</span></button>
              <button type="button" class="btn2 flex-center" onclick="del_qa(`{{ $row->table_name }}`, {{ $row->iq_id }})"><img src="{{ asset('images/icon/trash.svg') }}" alt="icon"><span>삭제</span></button>              
            </div>

          </td>
        </tr>
        @endforeach
        @if ($items->total() == 0)
        <tr class="qa-0">
          <td colspan="6">문의내역이 없습니다</td>
        </tr>
        @endif
      </tbody>
    </table>
  </div>

  {{ $items->links() }}

</div>
<script>
function del_qa(table, idx) {
  Swal.fire({
    toast: false, 
    icon: 'info',
    title: '삭제 확인',
    html: '정말 삭제하시겠습니까?',

    showCancelButton: true,     
    confirmButtonText: '확인',
    cancelButtonText: '취소',

    reverseButtons: true        
  }).then((result) => {

      if (result.isConfirmed) {
          location.href = '/customer_service/my_qa_delete?table=' + table + '&idx=' + idx;
      }
  });
}


function modi_qa(table, idx) {
  location.href = '/customer_service/my_qa_modify?table=' + table + '&idx=' + idx;
}

function view_qa(table, idx) {
  location.href = '/customer_service/my_qa_view?table=' + table + '&idx=' + idx;
}

</script>
@endsection