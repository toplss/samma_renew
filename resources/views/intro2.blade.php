<!DOCTYPE html>
<html lang="ko">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>@yield('title', '삼마몰')</title>

	<meta name="naver-site-verification" content=""/>
	<meta name="google-site-verification" content="" />
	<meta name="description" content="비바쿡, PC방, 스크린골프장, 키즈카페, 북카페, 호텔, 샵인샵 전문 먹거리쇼핑몰">
	<link rel="canonical" href="">
	<meta property="og:type" content="website">
	<meta property="og:title" content="삼마몰">
	<meta property="og:site_name" content="삼마몰">
	<meta property="og:description" content="비바쿡, PC방, 스크린골프장, 키즈카페, 북카페, 호텔, 샵인샵 전문 먹거리쇼핑몰">
	<meta property="og:image" content="{{ asset('images/common/ogimage.jpg') }}">
	<meta property="og:url" content="">

	<!-- favicon -->
	<link rel="shortcut icon" href="{{ asset('images/common/shortcut_icon.png') }}">
	<link rel="apple-touch-icon" href="{{ asset('images/common/shortcut_icon.png') }}">
	<link rel="apple-touch-startup-image" href="{{ asset('images/common/shortcut_icon.png') }}">
	<link rel="shortcut icon" href="{{ asset('images/common/favicon.ico') }}" type="image/x-icon" />
	<link rel="icon" href="{{ asset('images/common/favicon.ico') }}" type="image/x-icon" />

	<link rel="stylesheet" href="{{ asset('css/slick.css') }}">
	<link rel="stylesheet" href="{{ asset('css/common.css') }}">
	<link rel="stylesheet" href="{{ asset('css/main.css') }}">
	<script src="{{ asset('js/jquery-3.7.1.min.js') }}"></script>
	<script src="{{ asset('js/slick.min.js') }}"></script>
	<script src="{{ asset('js/common.js') }}"></script>
	<script src="{{ asset('js/mall.js') }}"></script>
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

	<div class="intro">

		<!-- 인트로 좌측 배너 -->
		<div class="left-box">
			 {!! $left_banner !!}
		</div>
		<!-- 인트로 좌측 배너 끝 -->

		<div class="right-box">
			<h1><img src="{{ asset('images/common/logo.png') }}" alt=""></h1>
			<ul class="intro-btns">
				<li><a href="/join_step_agree">회원가입</a></li>
				<li><a href="/mall/browse_products">둘러보기</a></li>
				<li><a href="/customer_service/alliance?gubun=alliance">제휴문의</a></li>
				<li><a href="/common_board/list">고객센터</a></li>
			</ul>

			<!-- 인트로 우측 배너 -->
			<div class="intro-banner">
				{!! $right_banner !!}
			</div>
			<!-- 인트로 우측 배너 끝 -->

			<form action="{{ route('login') }}" method="POST" >
				@csrf
				<div class="intro-login">
					<ul class="login-info">
						<li><input type="text" name="mb_id" value="{{ request()->cookie('remember_id') }}" placeholder="아이디" required><input type="checkbox" name="remember_id" id="idCheck" value="Y" {{ request()->cookie('remember_id') ? 'checked' : '' }}><label for="idCheck">ID 저장</label></li>
						<li><input type="password" name="mb_password" id="mb_password" placeholder="비밀번호" ><input type="checkbox" name="remember_me" id="remember_me" value="Y" {{ request()->cookie('remember_token') ? 'checked' : '' }}><label for="pwCheck">자동 로그인</label></li>
					</ul>
					<ul class="login-btn">
						<li><button type="button" onclick="location.href = '/mall/browse_products'; ">둘러보기</button></li>
						<li><button type="submit" style="background-color: #8ec251;">로그인</button></li>
					</ul>
				</div>
				<div class="login-num">
					<h4>로그인 회원</h4>
					<p><span><u>당월</u><b>2,840</b></span><span><u>당일</u><b>187</b></span></p>
				</div>
			</form>
			

			<!-- 우측하단 슬라이드 배너 -->
			<div class="intro-slide">
				{!! $right_bottom !!}
			</div>
			<!-- 우측하단 슬라이드 배너 끝 -->

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

</body>
</html>