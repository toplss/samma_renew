@extends('layouts.header')

@section('content')

<div class="sub-container">
	<div class="sub-title-wrap">
		<h4>회원정보수정</h4>  
	</div>
	<div class="pw-check-box">
		<h2>비밀번호 확인</h2>
		<p>회원님의 정보를 안전하게 보호하기 위해 비밀번호를 다시 한번 확인합니다.</p>

		<form name="check_form" id="check_form" method="post" enctype="multipart/form-data" action="{{ route('check_pass') }}">
		@csrf
			<input type="password" name="mb_password" id="mb_password" placeholder="비밀번호 확인" autocomplete="new-password">
			<div class="flex-center">
				<button type="button" class="btn1" onclick="javascript:chkForm(); return false;">확인</button>
				<button type="button" class="btn2">취소</button>
			</div>
		</form>
	</div>
</div>

<script>
  //폼 체크
  function chkForm() {
    if ($("#mb_password").val() == "") {
      validationAlertMessage("비밀번호를 입력해 주세요.");
      $("#mb_password").focus();
      return false;
    }

	$("#check_form").submit();

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