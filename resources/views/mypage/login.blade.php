@extends('layouts.header')

@section('content')


<div class="sub-container">
  <div class="sub-title-wrap">
      <h4>로그인</h4>  
  </div>

  <div class="login-wrap">
    <form action="{{ route('login') }}" method="POST">
				@csrf
      <ul>
        <li class="lg1">
          <img src="{{ asset('images/icon/name_icon.jpg') }}" alt="">
          <input type="text" name="mb_id" value="{{ request()->cookie('remember_id') }}" placeholder="아이디">
        </li>
        <li class="lg1">
          <img src="{{ asset('images/icon/pass_icon.jpg') }}" alt="">
          <input type="password" name="mb_password" id="mb_password" placeholder="비밀번호">
        </li>
        <li class="lg2">
          <input type="checkbox" name="idsave" id="idCheck" value="Y" {{ request()->cookie('remember_id') ? 'checked' : '' }}><label for="ch_id">아이디 저장</label>
          <input type="checkbox" name="remember_me" id="remember_me" value="Y" {{ request()->cookie('remember_token') ? 'checked' : '' }}><label for="pwCheck">자동로그인</label>
        </li>
        <li>
          <button type="submit" class="btn3">로그인</button>
        </li>
        <li>
          회원이 아니신가요?
        </li>
        <li>
        <button type="button" onclick="location.href='/join_step_agree';" class="btn1">회원가입</button>
        </li>
      </ul>
    </form>   
  </div>

</div>

<script>
const rememberMe = document.getElementById('remember_me');
const mbPassword = document.getElementById('mb_password');
const mbId = document.getElementById('mb_id');

// 체크박스 상태가 바뀔 때마다 required 속성 변경
rememberMe.addEventListener('change', () => {
    if (rememberMe.checked) {
        // 체크되어 있으면 비밀번호 required 제거
        mbPassword.removeAttribute('required');
        mbId.removeAttribute('required');
    } else {
        // 체크 안되어 있으면 비밀번호 required 적용
        mbPassword.setAttribute('required', 'required');
        mbId.setAttribute('required', 'required');
    }
});

// 페이지 로드 시 초기 상태 적용 (필요시)
if (!rememberMe.checked) {
    mbPassword.setAttribute('required', 'required');
    mbId.setAttribute('required', 'required');
}
</script>

@if(session('login'))
<script>
	Swal.fire({
		toast: true,
        title: '오류',
        text: "{{ session('login') }}",
        confirmButtonText: '확인'
    });
</script>
@endif

@if(session('error'))
<script>
	Swal.fire({
		toast: true,
        title: '오류',
        text: "{{ session('error') }}",
        confirmButtonText: '확인'
    });
</script>
@endif

@if(session('success'))
<script>
	Swal.fire({
		toast: true,
        title: '성공',
        text: "{{ session('success') }}",
        confirmButtonText: '확인'
    });
</script>
@endif


@endsection


