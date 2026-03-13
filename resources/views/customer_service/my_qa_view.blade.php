@extends('layouts.header')

@section('title', '상담문의 페이지')

@section('content')

<div class="sub-container">
  <div class="sub-title-wrap">
    <h4>상담문의</h4>  
  </div>
  

  <div class="qa-wrap">
    <form id="qa_form"  method="post" action="{{ route('my_qa_save') }}">
      @csrf
      @if (isset($idx))
        <input type="hidden" name="idx" value="{{ $idx }}">
        <input type="hidden" name="table" value="{{ $table }}">
      @endif

    <table class="ask-table">
      <tr>
        <th>문의종류</th>
        <td>
          <input type="radio" name="iq_gubun"  class="gubun" value="상품" {{ $info->iq_gubun == '상품' ? 'checked' : ''  }}><label onclick="javascript:$('.gubun_1').click();"> 상품</label>
          <input type="radio" name="iq_gubun"  class="gubun" value="배송" {{ $info->iq_gubun == '배송' ? 'checked' : ''  }}><label onclick="javascript:$('.gubun_2').click();"> 배송</label>
          <input type="radio" name="iq_gubun"  class="gubun" value="취소" {{ $info->iq_gubun == '취소' ? 'checked' : ''  }}><label onclick="javascript:$('.gubun_3').click();"> 취소</label>
          <input type="radio" name="iq_gubun"  class="gubun" value="반품/취소" {{ $info->iq_gubun == '반품/취소' ? 'checked' : ''  }}><label onclick="javascript:$('.gubun_4').click();"> 반품/취소</label>
          <input type="radio" name="iq_gubun"  class="gubun" value="교환" {{ $info->iq_gubun == '교환' ? 'checked' : ''  }}><label onclick="javascript:$('.gubun_5').click();"> 교환</label>
          <input type="radio" name="iq_gubun"  class="gubun" value="기타" {{ $info->iq_gubun == '기타' ? 'checked' : ''  }}><label onclick="javascript:$('.gubun_6').click();"> 기타</label>
        </td>
      </tr>
      <tr>
        <th>상품선택</th>
        <td>
          <select name="it_id" id="it_id" class="qa-select">
            <option value="">상품을 선택하세요.</option>
            @foreach ($items as $key => $row)
              <option value="{{ $row->it_id }}" {{ $row->it_id == ($info->it_id ?? '')  ? 'selected' : ''   }}>{{ $row->it_name }}</option>
            @endforeach
					</select>
        </td>
      </tr>
      <tr>
        <th>이름</th>
        <td>{{ $activeMember['mb_company'] }}</td>
      </tr>
      <!-- @if ($table == 'item_contact')
      <tr>
        <th>이메일</th>
        <td>{{ $info->iq_email }}</td>
      </tr>
      <tr>
        <th>연락처</th>
        <td>{{ $info->iq_hp }}</td>
      </tr>
      @endif -->
      <tr>
        <th>제목</th>
        <td>{{ $info->iq_subject }}</td>
      </tr>
      <tr>
        <th>내용</th>
        <td>
        {{ $info->iq_question }}
        </td>
      </tr>
    </table>
    </form>

    <div class="ask-btn">
        <button type="button" class="btn3 big-btn" onclick="javascript:Sendit_chk();">수정</button>
        <button type="button" class="btn1 big-btn" onclick="location.href='/customer_service/my_qa_list'">목록</button>
	</div>

  </div>
</div>
<script>
function Sendit_chk() {
    var table = $('input[name="table"]').val();
    var idx = $('input[name="idx"]').val();


    location.href = '/customer_service/my_qa_modify?table=' + table + '&idx=' + idx;
}
</script>
@endsection