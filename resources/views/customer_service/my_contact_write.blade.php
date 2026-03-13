@extends('layouts.header')

@section('title', '1:1문의 페이지')

@section('content')

<div class="sub-container">
  <div class="sub-title-wrap">
    <h4>1:1문의</h4>  
  </div>

  <div class="qa-wrap">
    <form id="qa_form"  method="post" action="{{ route('my_contact_save') }}">
      @csrf
      @if (isset($idx))
        <input type="hidden" name="idx" value="{{ $idx }}">
      @endif
    <table class="ask-table">
      <tr>
        <th>문의종류</th>
        <td>
        <input type="radio" name="iq_gubun"  class="gubun" value="상품" {{ isset($idx) && $info->iq_gubun == '상품' ? 'checked' : ''  }}><label onclick="javascript:$('.gubun_1').click();"> 상품</label>
          <input type="radio" name="iq_gubun"  class="gubun" value="배송" {{ isset($idx) && $info->iq_gubun == '배송' ? 'checked' : ''  }}><label onclick="javascript:$('.gubun_2').click();"> 배송</label>
          <input type="radio" name="iq_gubun"  class="gubun" value="취소" {{ isset($idx) && $info->iq_gubun == '취소' ? 'checked' : ''  }}><label onclick="javascript:$('.gubun_3').click();"> 취소</label>
          <input type="radio" name="iq_gubun"  class="gubun" value="반품/취소" {{ isset($idx) && $info->iq_gubun == '반품/취소' ? 'checked' : ''  }}><label onclick="javascript:$('.gubun_4').click();"> 반품/취소</label>
          <input type="radio" name="iq_gubun"  class="gubun" value="교환" {{ isset($idx) && $info->iq_gubun == '교환' ? 'checked' : ''  }}><label onclick="javascript:$('.gubun_5').click();"> 교환</label>
          <input type="radio" name="iq_gubun"  class="gubun" value="기타" {{ isset($idx) && $info->iq_gubun == '기타' ? 'checked' : ''  }}><label onclick="javascript:$('.gubun_6').click();"> 기타</label>
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
        <th>업체명</th>
        <td>{{ $activeMember['mb_company'] }}</td>
      </tr>
      <tr style="display:none;">
        <th>이메일</th>
        <td><input type="text" name="iq_email" id="iq_email" value="{{ $activeMember['mb_email'] }}"></td>
      </tr>
      <tr style="display:none;">
        <th>연락처</th>
        <td>
          <input type="text" name="iq_hp1" id="iq_hp1" value="{{ isset($activeMember['mb_hp']) ? substr($activeMember['mb_hp'], 0, 3) : '' }}"> - 
					<input type="text" name="iq_hp2" id="iq_hp2" value="{{ isset($activeMember['mb_hp']) ? substr($activeMember['mb_hp'], 4, 4) : '' }}"> - 
					<input type="text" name="iq_hp3" id="iq_hp3" value="{{ isset($activeMember['mb_hp']) ? substr($activeMember['mb_hp'], 9, 4) : '' }}">
        </td>
      </tr>
      <tr>
        <th>제목</th>
        <td><input type="text" name="iq_subject" id="iq_subject" value="{{ isset($idx) ? $info->iq_subject : '' }}" style="width:100%;"></td>
      </tr>
      <tr>
        <th>내용</th>
        <td>
          <textarea name="iq_question" id="ir1">{{ isset($idx) ? $info->iq_question : '' }}</textarea>
          <!-- <iframe frameborder="0" scrolling="no" src="/smarteditor/SmartEditor2Skin.html" style="width: 100%; height: 403px;"></iframe> -->
        </td>
      </tr>
    </table>
    </form>

    <div class="ask-btn">
		  <button type="button" class="btn3 big-btn" onclick="javascript:Sendit_chk();">문의 등록</button>
			<button type="button" class="btn1 big-btn" onclick="location.href='/customer_service/my_qa_list'">취소</button>
		</div>


  </div>
</div>
<script>
function Sendit_chk() {
  var form = $('#qa_form')[0];

  if (form.iq_gubun.value == '') {
    validationAlertMessage('문의종류를 선택해 주세요.');
    form.iq_gubun.focus();
    return false;
  }

  if (form.it_id.value == '') {
    validationAlertMessage('상품을 선택해 주세요.');
    form.it_id.focus();
    return false;
  }

  if (form.iq_subject.value.trim() == '') {
    validationAlertMessage('제목을 입력해 주세요.');
    form.iq_subject.focus();
    return false;
  }
  if (form.iq_question.value.trim() == '') {
    validationAlertMessage('내용을 입력해 주세요.');
    form.iq_question.focus();
    return false;
  }

  let regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

  if (!regex.test(form.iq_email.value.trim())) {
    var message = '이메일 도메인 형식이 올바르지 않습니다.';

    validationAlertMessage(message);

    return false;
  }

  form.submit();
}


function validationAlertMessage(message)  {
  Swal.fire({
      toast: true,
      icon: 'warning',
      title: '알림',
      html: message,
      confirmButtonText: '확인'
  });
}

</script>
@endsection