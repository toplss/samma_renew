@extends('layouts.header')

@section('title', '제휴문의 입점문의 페이지')

@section('content')

<div class="sub-container">
  <div class="sub-title-wrap">
    <h4>제휴 및 입점문의</h4>  
  </div>

  <div class="terms-box">
    <h5>정보제공약관</h5>
    <div class="terms-scroll">
      <p>삼마몰(이하 "회사"라 함)은 개인정보보호법, 정보통신망 이용촉진 및 정보보호 등에 관한 법률등 관련 법령상의 개인정보보호 규정을 준수하며, 삼마몰의 파트너의 개인정보 보호에 최선을 다하고 있습니다.</p>
      <p>1. 개인정보 수집 및 이용주체<br>
        제휴문의 및 입점문의 상담신청을 통해 제공하신 정보는 "회사"가 직접 접수하고 관리합니다. <br>수집정보는 담당자 이외에는 권한없이 열람할 수 없습니다.
      </p>
      <p>2. 동의를 거부할 권리 및 동의 거부에 따른 불이익<br>
        신청자는 개인정보제공 등에 관해 동의하지 않을 권리가 있습니다. <br>이 경우 제휴문의 및 입점문의 상담신청이 불가능합니다.
      </p>
      <p>3. 수집하는 개인정보 항목<br>
        신청자의 업체명, 업체주소, 담당자명, 담당자 연락처, 담당자 이메일 등 제휴문의 진행을 위한 사항
      </p>
      <p>4. 수집 및 이용목적<br>
        제휴사 검토, 제휴사 관리시스템의 운용, 만족도 조사, 공지사항의 전달 등
      </p>
      <p>5. 보유기간 및 이용기간<br>
        수집된 정보는 제휴문의 및 상담서비스가 종료되는 시점까지 보관됩니다.
      </p>
      <p>삼마몰은 서비스 제공을 위하여 필요한 개인정보를 최소한으로 수집하고 있으며 서비스 제공에 필요한 최소한의 정보 이 외의 개인정보 수집에 <br>대하여는 동의하지 않을 수 있습니다. <br>단, 정보 제공을 거부하는 경우 해당 정보가 필요한 일부 서비스의 제공이 제한될 수 있습니다.</p>
    </div>
    <label><input type="checkbox" name="check_1" id="check_1" value="1">제휴 및 입점문의 상담신청을 위한 개인정보 수집에 동의합니다.</label>
  </div>
  

  <form name="alliance_frm" id="alliance_frm" method="post" enctype="multipart/form-data" action="{{ route('alliance_save') }}">
    @csrf
    <table class="ask-table alliance-table">
      <tr>
        <th>분류</th>
        <td>
          <label>
            <input type="radio" name="gubun" id="gubun" class="gubun2" value="alliance" {{ request('gubun') == 'alliance' ? 'checked' : '' }}>제휴문의
          </label>
          <label>
            <input type="radio" name="gubun" id="gubun" class="gubun1" value="store" {{ request('gubun') == 'store' ? 'checked' : '' }}>입점문의
          </label>
        </td>
      </tr>
      <tr>
        <th>업체명</th>
        <td><input type="text" name="company" id="company" value="{{ (isset($activeMember['mb_company'])) ? $activeMember['mb_company'] : '' }}" placeholder="업체명"></td>
      </tr>
      <tr>
        <th>담당자명</th>
        <td><input type="text" name="name" id="name" value="{{ (isset($activeMember['mb_name'])) ? $activeMember['mb_name'] : '' }}" placeholder="담당자명"></td>
      </tr>
      <tr>
        <th>담당자 연락처</th>
        <td>
          <input type="text" name="phone1" id="phone1" value="{{ isset($activeMember['mb_hp']) ? substr($activeMember['mb_hp'], 0, 3) : '' }}"> -
          <input type="text" name="phone2" id="phone2" value="{{ isset($activeMember['mb_hp']) ? substr($activeMember['mb_hp'], 4, 4) : '' }}"> -
          <input type="text" name="phone3" id="phone3" value="{{ isset($activeMember['mb_hp']) ? substr($activeMember['mb_hp'], 9, 4) : '' }}">
        </td>
      </tr>
      <tr>
        <th>이메일</th>
        <td>
          @php
          $email1 = $email2 = '';

          if (!empty($activeMember['mb_email'])) {
            if (strpos($activeMember['mb_email'], '@') !== false) {
              $mailArr = explode('@', $activeMember['mb_email']);

              $email1 = $mailArr[0];
              $email2 = $mailArr[1];
            }
          }
          @endphp
          <input type="text" name="email1" id="email1" value="{{ $email1 }}"> @
					<input type="text" name="email2" id="email2" value="{{ $email2 }}">
          <select name="email_select" id="email_select" onchange="checkemailaddy(this.value);">
            <option value="">선택</option>
            <option value="naver.com">naver.com</option>
            <option value="hanmail.net">hanmail.net</option>
            <option value="nate.com">nate.com</option>
            <option value="gmail.com">gmail.com</option>
            <option value="1">직접입력</option>
          </select>
        </td>
      </tr>
      <tr>
        <th>제목</th>
        <td><input type="text" name="title" id="title" style="width:100%;" placeholder="제목"></td>
      </tr>
      <tr>
        <th>내용</th>
        <td><textarea name="contents" id="contents" placeholder="내용을 입력해 주세요"></textarea></td>
      </tr>
      <tr>
        <th>홈페이지</th>
        <td><input type="text" name="url" id="url" style="width: 100%;"></td>
      </tr>
      <tr>
        <th>첨부파일</th>
        <td>
          <input type="file" name="userfile" id="userfile1" style="display:none;" onchange="document.getElementById('userfile_txt1').value=this.value;" accept="*">
					<input type="text" id="userfile_txt1" placeholder="첨부파일" readonly>
					<button type="button" class="btn3" style="cursor:pointer;" onclick="document.getElementById('userfile1').click();">파일선택</button>
        </td>
      </tr>

    </table>

    <div class="ask-btn">
      <button type="button" class="btn1 big-btn" onclick="chkform_alliance();">문의하기</button>
      <button type="button" class="btn2 big-btn">취소</button>
    </div>
  </form>




<script>
function chkform_alliance() {
  var form = $('#alliance_frm')[0];
  var check_1 = $('#check_1').is(':checked');

  if (!check_1) {
    var message = '제휴 및 입점 문의 상담 신청을 위해 개인정보 수집·이용에 동의해 주세요.';

    validationAlertMessage(message);

    return false;
  }

  var company = $('#company').val();

  if (!company) {
    var message = '업체명을 입력 하세요.';

    validationAlertMessage(message);

    return false;
  }


  var name = $('#name').val();

  if (!name) {
    var message = '담당자명을 입력 하세요.';

    validationAlertMessage(message);

    return false;
  }


  var phone1 = $('#phone1').val().trim();
  var phone2 = $('#phone2').val().trim();
  var phone3 = $('#phone3').val().trim();

  if (!phone1 || !phone2 || !phone3) {
    validationAlertMessage('담당자 연락처를 입력하세요.');
    return false;
  }

  // 숫자만 허용
  var phone1Reg = /^\d{2,3}$/;
  var phone2Reg = /^\d{3,4}$/;
  var phone3Reg = /^\d{4}$/;

  if (!phone1Reg.test(phone1) ||
      !phone2Reg.test(phone2) ||
      !phone3Reg.test(phone3)) {

    validationAlertMessage('연락처 형식이 올바르지 않습니다.');
    return false;
  }


  var email1 = $('#email1').val();

  if (!email1) {
    var message = '이메일 을 입력 하세요.';

    validationAlertMessage(message);

    return false;
  }

  var email2 = $('#email2').val().trim();

  if (!email2) {
    var message = '이메일 을 입력 하세요.';

    validationAlertMessage(message);

    return false;
  }


  let regex = /^[a-z0-9-]+\.(com|net|co\.kr|org)$/i;

  if (!regex.test(email2)) {
    var message = '이메일 도메인 형식이 올바르지 않습니다.';

    validationAlertMessage(message);

    return false;
  }

  var title = $('#title').val();
  var contents = $('#contents').val();

  if (!title || !contents) {
    var message = '제목 또는 내용을 입력 하세요.';

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


function checkemailaddy(e) {
  if (e !== '' && e != 1) {
    $('#email2').val(e);
  }

  if (e == 1) {
    $('#email2').val('');
  }
}
</script>
</div>

@endsection


