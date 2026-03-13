<!DOCTYPE html>
<html lang="ko">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="csrf-token" content="{{ csrf_token() }}">
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

	<link rel="stylesheet" href="{{ asset('css/common.css') }}?v={{ config('asset.version') }}">
	<link rel="stylesheet" href="{{ asset('css/slick.css') }}?v={{ config('asset.version') }}">
	<link rel="stylesheet" href="{{ asset('css/sweetalert2.min.css') }}?v={{ config('asset.version') }}">
	<link rel="stylesheet" href="{{ asset('css/select2.min.css') }}?v={{ config('asset.version') }}">
	<link rel="stylesheet" href="{{ asset('css/jquery-ui.min.css') }}?v={{ config('asset.version') }}">
	<link rel="stylesheet" href="{{ asset('css/main.css') }}?v={{ config('asset.version') }}">
	<link rel="stylesheet" href="{{ asset('css/sub.css') }}?v={{ config('asset.version') }}">
	
	<script src="{{ asset('js/jquery-3.7.1.min.js') }}?v={{ config('asset.version') }}"></script>
	<script src="{{ asset('js/slick.min.js') }}?v={{ config('asset.version') }}"></script>
	<script src="{{ asset('js/readmore.js') }}?v={{ config('asset.version') }}"></script>
	<script src="{{ asset('js/common.js') }}?v={{ config('asset.version') }}"></script>
	<script src="{{ asset('js/mall.js') }}?v={{ config('asset.version') }}"></script>
	<script src="{{ asset('js/cart_reponse.js') }}?v={{ config('asset.version') }}"></script>
	<script src="{{ asset('js/fly.js') }}?v={{ config('asset.version') }}"></script>
	<script src="{{ asset('js/YoutubeManager.js') }}?v={{ config('asset.version') }}"></script>
	<script src="{{ asset('js/cookie.js') }}?v={{ config('asset.version') }}"></script>
	<script src="{{ asset('js/popup.js') }}?v={{ config('asset.version') }}"></script>
	<script src="{{ asset('js/sweetalert2.min.js') }}?v={{ config('asset.version') }}"></script>
	<script src="{{ asset('js/select2.min.js') }}?v={{ config('asset.version') }}"></script>
	<script src="{{ asset('js/postcode.v2.js') }}?v={{ config('asset.version') }}"></script>	
	<script src="{{ asset('js/kakao.min.js') }}?v={{ config('asset.version') }}"></script>
	<script src="{{ asset('js/jquery-ui.min.js') }}?v={{ config('asset.version') }}"></script>
	<script src="{{ asset('js/library.js') }}?v={{ config('asset.version') }}"></script>
	<script src="{{ asset('js/compression.js') }}?v={{ config('asset.version') }}"></script>

	<script>
	$.ajaxSetup({
	headers: {
		'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	}
	});
	</script>
</head>
<body>
<header>
	<div class="top-notice">
		<img class="mqt1" src="{{ asset('images/icon/bell.gif') }}">
		"배송요일 하루전" 오후 2시이전 주문시 원활한 납품을 받으실 수 있습니다.
	</div>
	<div class="top">
		@php
		$home_path = '/';
		@endphp
		<h1><a href="{{ $home_path }}"><img src="{{ asset('images/common/logo.png') }}"></a></h1>
		<div class="m-top">
			<a href="{{ $home_path }}"><img src="{{ asset('images/common/logo.png') }}"></a>
			<p class="flex">
				<a id="mSearchBtn"><img src="{{ asset('images/icon/search.svg') }}"></a>
				<a class="m_hamburger"><span></span><span></span><span></span></a>
			</p>
		</div>
		<div class="top-info1">
			<div class="top-search">
				<form id="seach_frm" action="{{ route('list_all') }}" method="GET">
					<input type="search" name="skeyword" id="skeyword" value="{{  (isset($items->other) && isset($items->other['skeyword'])) ? $items->other['skeyword'] : '' }}" placeholder="검색어를 입력해 주세요">
					<img src="{{ asset('images/icon/search.svg') }}" alt="" onclick="javascript:$('#seach_frm')[0].submit()">
				</form>
				@if($activeMember && $activeMember['mb_virtual_bank'])
				<p class="virtual_account_info">[{{ $activeMember['mb_virtual_bank'] }} {{ $activeMember['vr_account_info'] }}]</p>				
				@endif
			</div>
			@if(session('ss_mb_code'))
			<ul class="top-money">
				@if($activeMember['level_ca_id2_name'] == '선불')
				<li><span>충전금</span>{{ number_format($activeMember['mb_point']) }}원</li>
				<li><span>적립금</span>{{ number_format($activeMember['mb_point_reserve']) }}원</li>
				@endif
				<li><span class="bond">채권</span>{{ number_format($activeMember['mb_point_balance']) }}원</li>
				<li>
					<span>배송요일</span>
					@if($activeMember['mb_cs_mon'] == 'y')<u class="{{ $activeMember['delivery_info']['ship_date'] == '월' ? 'cs_delivery_on' : 'cs_delivery' }}">월</u>@endif
					@if($activeMember['mb_cs_tue'] == 'y')<u class="{{ $activeMember['delivery_info']['ship_date'] == '화' ? 'cs_delivery_on' : 'cs_delivery' }}">화</u>@endif
					@if($activeMember['mb_cs_wed'] == 'y')<u class="{{ $activeMember['delivery_info']['ship_date'] == '수' ? 'cs_delivery_on' : 'cs_delivery' }}">수</u>@endif
					@if($activeMember['mb_cs_thu'] == 'y')<u class="{{ $activeMember['delivery_info']['ship_date'] == '목' ? 'cs_delivery_on' : 'cs_delivery' }}">목</u>@endif
					@if($activeMember['mb_cs_fri'] == 'y')<u class="{{ $activeMember['delivery_info']['ship_date'] == '금' ? 'cs_delivery_on' : 'cs_delivery' }}">금</u>@endif
					@if($activeMember['mb_cs_sat'] == 'y')<u class="{{ $activeMember['delivery_info']['ship_date'] == '토' ? 'cs_delivery_on' : 'cs_delivery' }}">토</u>@endif
					@if($activeMember['mb_cs_sun'] == 'y')<u class="{{ $activeMember['delivery_info']['ship_date'] == '일' ? 'cs_delivery_on' : 'cs_delivery' }}">일</u>@endif
				</li>
			</ul>
			@endif
		</div>
		<div class="top-info2">
			@if(session('ss_mb_code'))
			<p>{{ $activeMember['mb_company'] }}  @if($activeMember['mb_gubun_type'] === '') 점주님 환영합니다! @endif</p>
			@endif
			<div class="top-btns">
				@if(session('ss_mb_code'))
				<div>
					<button type="button" class="btn1">마이페이지</button>
					<ul>
						<li><a href="/mypage/orderinquiry">주문내역</a></li>
						<li><a href="/mypage/my_equipment_list">보유장비</a></li>
						<li><a href="/mypage/cart">장바구니</a></li>
						<li><a href="/mypage/my_point">충전금내역</a></li>
						<li><a href="/mypage/my_point_reserve">적립금내역</a></li>
						<li><a href="/mypage/deposit_history">입금내역</a></li>
						<li><a href="/mypage/register_access">회원정보수정</a></li>
					</ul>
				</div>
				<div>
					<button type="button" class="btn1">고객센터</button>
					<ul>
						<li><a href="/customer_service/user_guide">이용안내</a></li>
						<li><a href="/customer_service/my_qa_list">상담문의</a></li>
						<li><a href="/customer_service/return_reception">반품접수</a></li>
						<li><a href="/customer_service/as_reception">A/S접수</a></li>
						<li><a href="/customer_service/claim">클레임접수</a></li>
						<li><a href="/customer_service/alliance?gubun=alliance">제휴문의</a></li>
						<li><a href="/customer_service/alliance?gubun=store">입점문의</a></li>
						<li><a href="/common_board/list">공지사항</a></li>
					</ul>
				</div>

				<button type="button" class="btn2" onclick="logoutBtn()">로그아웃</button>
				<button type="button" class="btn3" onclick="location.href = '/mypage/cart';">
					장바구니
					<u class="top_cart_cnt">0</u>
				</button>
				@else
				<button type="button" class="btn2" onclick="location.href = '/customer_service/alliance?gubun=alliance';">제휴문의</button>
				<button type="button" class="btn2" onclick="location.href = '/customer_service/alliance?gubun=store';">입점문의</button>
				<button type="button" class="btn1" onclick="location.href = '/login';">로그인</button>
				<button type="button" class="btn3" onclick="location.href = '/join_step_agree';">회원가입</button>
				@endif
			</div>

		</div>
		
	</div>

	<nav>
		<ul class="menu">
			<li class="allmenu_box">
				<div class="allmenu_icon"><span></span><span></span><span></span></div>
				<ul class="allmenu_bar">
					@foreach($activeCategory['mobile'] as $key => $row)
					<li class="main_go">
						<a href="/mall/shop/list?ca_id={{ $row['ca_id'] }}">
							<img src="https://samma-erp.com/mall/data/category/{{ $row['ca_icon1'] }}" alt="">{{ $row['ca_name'] }}
						</a>
						<ul class="sub_go">
							@if(isset($row['sub_category']))
								@foreach($row['sub_category'] as $skey => $srow)
								<li><a href="/mall/shop/list?ca_id={{ $srow['ca_id'] }}">{{ $srow['ca_name'] }}</a></li>
								@endforeach
							@endif
						</ul>
					</li>
					@endforeach
				</ul>
			</li>

			<li class="{{ request()->is('mall/shop/list') && request()->query('type') != 'low_temp' ? 'active' : '' }}">
				<a href="/mall/shop/list?ca_id=20">전체상품</a>
			</li>
			<li class="{{ request()->is('mall/shop/list') && request()->query('type') == 'low_temp' ? 'active' : '' }}">
				<a href="/mall/shop/list?type=low_temp&ca_id=70">저온상품</a>
			</li>
			<li class="{{ request()->is('mall/shop/list_gubun') && request()->query('type') == 'vivacook' ? 'active' : '' }}">
				<a href="/mall/shop/list_gubun?type=vivacook">비바쿡</a>
			</li>
			<li class="{{ request()->is('mall/shop/list_gubun') && request()->query('type') == 'mygrang' ? 'active' : '' }}">
				<a href="/mall/shop/list_gubun?type=mygrang">마이그랑</a>
			</li>
			<li class="{{ request()->is('mall/shop/list_gubun') && request()->query('type') == 'sale' ? 'active' : '' }}">
				<a href="/mall/shop/list_gubun?type=sale">이달의행사</a>
			</li>
			<li class="{{ request()->is('mall/shop/list_gubun_equipment') ? 'active' : '' }}">
				<a href="/mall/shop/list_gubun_equipment">장비구매</a>
			</li>
			<li class="{{ request()->is('mall/shop/list_gubun_accessory') ? 'active' : '' }}">
				<a href="/mall/shop/list_gubun_accessory">A/S부속품</a>
			</li>
			<li class="{{ request()->is('mall/shop/list_gubun_chain') ? 'active' : '' }}">
				<a href="/mall/shop/list_gubun_chain">체인점전용</a>
			</li>
			<li class="{{ request()->is('mall/shop/list_gubun_simple') ? 'active' : '' }}">
				<a href="/mall/shop/list_gubun_simple">간편주문</a>
			</li>
			<li class="{{ request()->is('mall/shop/list_gubun_recipe') ? 'active' : '' }}">
				<a href="/mall/shop/list_gubun_recipe">레시피보기</a>
			</li>
			<li class="{{ request()->is('mall/shop/list_charge') ? 'active' : '' }}">
				<a href="/mall/shop/list_charge">충전금구매</a>
			</li>
			<li class="{{ request()->is('public/sub/*') ? 'active' : '' }}">
				<a href="/public/sub/kitchen_system_01">키친시스템</a>
			</li>
		</ul>
		
	</nav>

	<!-- 헤더 슬라이드 패널 -->
	@include('layouts.nav_slide')

</header>

<!-- 모바일 메뉴 -->
<div class="m-menu">
	@if (session('ss_mb_code'))
	<div class="mm1">
		<div class="flex-between">
			<p>{{ $activeMember['mb_company'] }}  @if($activeMember['mb_gubun_type'] === '') 점주님 환영합니다! @endif</p>
			<button type="button" class="btn2" onclick="logoutBtn()">로그아웃</button>
		</div>
		<div class="flex">
			@if($activeMember['level_ca_id2_name'] == '선불')
			<p>적립금: <b class="txt-blue">{{ number_format($activeMember['mb_point_reserve']) }}</b>원</p>
			<p>충전금: <b class="txt-blue">{{ number_format($activeMember['mb_point']) }}</b>원</p>
			@endif
			<!-- <p>채권: <b class="txt-blue">{{ number_format($activeMember['mb_point_balance']) }}</b>원</p> -->
		</div>
		<dl>
			<dt>배송요일</dt>
			<dd>
				@if($activeMember['mb_cs_mon'] == 'y')<u class="{{ $activeMember['delivery_info']['ship_date'] == '월' ? 'cs_delivery_on' : 'cs_delivery' }}">월</u>@endif
				@if($activeMember['mb_cs_tue'] == 'y')<u class="{{ $activeMember['delivery_info']['ship_date'] == '화' ? 'cs_delivery_on' : 'cs_delivery' }}">화</u>@endif
				@if($activeMember['mb_cs_wed'] == 'y')<u class="{{ $activeMember['delivery_info']['ship_date'] == '수' ? 'cs_delivery_on' : 'cs_delivery' }}">수</u>@endif
				@if($activeMember['mb_cs_thu'] == 'y')<u class="{{ $activeMember['delivery_info']['ship_date'] == '목' ? 'cs_delivery_on' : 'cs_delivery' }}">목</u>@endif
				@if($activeMember['mb_cs_fri'] == 'y')<u class="{{ $activeMember['delivery_info']['ship_date'] == '금' ? 'cs_delivery_on' : 'cs_delivery' }}">금</u>@endif
				@if($activeMember['mb_cs_sat'] == 'y')<u class="{{ $activeMember['delivery_info']['ship_date'] == '토' ? 'cs_delivery_on' : 'cs_delivery' }}">토</u>@endif
				@if($activeMember['mb_cs_sun'] == 'y')<u class="{{ $activeMember['delivery_info']['ship_date'] == '일' ? 'cs_delivery_on' : 'cs_delivery' }}">일</u>@endif
			</dd>
			<dt>관리담당자</dt>
			<dd>{{ $activeMember['mb_manager_staff'] }} @if($activeMember['mb_tel'])({{ $activeMember['mb_tel'] }})@endif</dd>
		</dl>
		<div class="flex-center">
			<img src="{{ asset('images/icon/bell.gif') }}">
			<p class="txt-red">배송요일 하루전 오후 2시이전 주문시 원활한 납품을 받으실 수 있습니다.</p>
		</div>
	</div>
	@else 

	<div class="flex-end" style="gap:0.5rem;">
		<button type="button" class="btn1" onclick="location.href='/login';">로그인</button>
		<button type="button" class="btn2" onclick="location.href='/join_step_agree';">회원가입</button>
	</div>


	@endif

	<div class="mm2">
		<table class="mm-table">
			<tr>
				<th colspan="4">메뉴</th>
			</tr>
			<tr>
				<td><a href="/mall/shop/list?type=low_temp&ca_id=70">저온상품</a></td>
				<td><a href="/mall/shop/list_gubun?type=vivacook">비바쿡</a></td>
				<td><a href="/mall/shop/list_gubun?type=mygrang">마이그랑</a></td>
				<td><a href="/mall/shop/list_gubun?type=sale">이달의행사</a></td>
				
			</tr>
			<tr>
				<td><a href="/mall/shop/list_gubun_equipment">장비구매</a></td>
				<td><a href="/mall/shop/list_gubun_accessory">A/S부속품</a></td>
				<td><a href="/mall/shop/list_gubun_chain">체인점전용</a></td>
				<td><a href="/mall/shop/list_gubun_simple">간편주문</a></td>
			</tr>
			<tr>
				<td><a href="/mall/shop/list_gubun_recipe">레시피보기</a></td>
				<td><a href="/mall/shop/list_charge">충전금 구매</a></td>
				<td><a href="/public/sub/kitchen_system_01">키친시스템</a></td>
				<td></td>
			</tr>
		</table>
		<table class="mm-table">
			<tr>
				<th colspan="4">카테고리</th>
			</tr>
			<tr>
				<td><a href="/mall/shop/list?ca_id=20">음료류</a></td>
				<td><a href="/mall/shop/list?ca_id=30">커피/드링크</a></td>
				<td><a href="/mall/shop/list?ca_id=40">라면류</a></td>
				<td><a href="/mall/shop/list?ca_id=50">스낵류</a></td>
			</tr>
			<tr>
				<td><a href="/mall/shop/list?ca_id=60">간식류</a></td>
				<td><a href="/mall/shop/list?ca_id=70">저온상품류</a></td>
				<td><a href="/mall/shop/list?ca_id=80">유제품</a></td>
				<td><a href="/mall/shop/list?ca_id=90">신선상품</a></td>
			</tr>
			<tr>
				<td><a href="/mall/shop/list?ca_id=a0">잡화류</a></td>
				<td><a href="/mall/shop/list?ca_id=b0">식자재</a></td>
				<td><a href="/mall/shop/list?ca_id=c0">주방용품</a></td>
				<td><a href="/mall/shop/list?ca_id=e0">배달메뉴</a></td>
			</tr>
			<tr>
				<td><a href="/mall/shop/list?ca_id=f0">디자인지원</a></td>
				<td><a href="/mall/shop/list?ca_id=h0">프로모션</a></td>
				<td></td>
				<td></td>
			</tr>
		</table>
	</div>

	<div class="mm3">
		@if(session('ss_mb_code'))
		<table class="mm-table">
			<tr>
				<th colspan="4">마이페이지</th>
			</tr>
			<tr>
				<td><a href="/mypage/orderinquiry">주문내역</a></td>
				<td><a href="/mypage/my_equipment_list">보유장비</a></td>
				<td><a href="/mypage/my_point">충전금내역</a></td>
				<td><a href="/mypage/my_point_reserve">적립금내역</a></td>
				
			</tr>
			<tr>
				<td><a href="/mypage/deposit_history">입금내역</a></td>
				<td><a href="/mypage/cart">장바구니</a></td>
				<td><a href="/mypage/register_access">회원정보수정</a></td>
				<td></td>
			</tr>
		</table>
		@endif

		<table class="mm-table">
			<tr>
				<th colspan="4">고객센터</th>
			</tr>
			<tr>
				<td colspan="4" class="ob">
					<p>1899-3153 📢 토,일요일 / 공휴일 휴무</p>
					<p>월-금 09:00 ~ 18:00 ⏰ 점심시간 12:30 ~ 13:30</p>
				</td>
			</tr>
			<tr>
				@if(session('ss_mb_code'))
				<td><a href="/customer_service/my_qa_list">상담문의</a></td>
				<td><a href="/customer_service/return_reception">반품접수</a></td>
				<td><a href="/customer_service/as_reception">A/S접수</a></td>
				<td><a href="/customer_service/claim">클레임접수</a></td>
				@endif
			</tr>
			<tr>
				<td><a href="/customer_service/user_guide">이용안내</a></td>
				<td><a href="/common_board/list">공지사항</a></td>
				<td><a href="/customer_service/alliance?gubun=alliance">제휴문의</a></td>
				<td><a href="/customer_service/alliance?gubun=store">입점문의</a></td>
			</tr>
		</table>
	</div>

	<div class="mm4">
		<div class="m-menu-banner">
			{!! $activeMobileFooterBanner !!}
		</div>
	</div>

</div>

<!-- 모바일 검색 -->
<div class="m-search-modal">
	<div class="ms-box">
		<img class="ms-close" src="{{ asset('images/icon/close.svg') }}" alt="">
		<img src="{{ asset('images/common/ex-img3.jpg') }}" style="border-radius:0.5rem;">
		<form id="seach_frm2" action="{{ route('list_all') }}" method="GET">
			<input type="search" name="skeyword" id="skeyword2" value="{{  (isset($items->other) && isset($items->other['skeyword'])) ? $items->other['skeyword'] : '' }}" placeholder="검색어를 입력해 주세요">
			<img src="{{ asset('images/icon/search.svg') }}" alt="" onclick="searchKeywordMobile()">
		</form>
		<ul>
			<li style="color:#8ec251; margin-bottom:0.3rem">고객센터</li>
			<li>1899-3153 📢 토,일요일/공휴일 휴무</li>
			<li>월-금 09:00 ~ 18:00 ⏰ 점심시간 12:30 ~ 13:30</li>
		</ul>
	</div>
</div>

<form id="log_out" method="post" action="{{ route('logout') }}">
	@csrf
</form>

<!-- 모바일 검색 -->
<script>
function searchKeywordMobile() {
	var frm = $('#seach_frm2')[0];
	if (!$('#skeyword2').val().trim()) {
		swal.fire({
			toast: true,
      icon: 'error',
      title: '입력 오류',
      html: '검색 키워드를 입력하세요',
      confirmButtonText: '확인'
		});
		return;
	}
	frm.submit();
}
// 엔터 막기
$('#seach_frm2').on('submit', function(e) {
	if (!$('#skeyword2').val().trim()) {
		e.preventDefault();
		searchKeywordMobile();
	}
});
</script>

<section>
	@yield('content')
</section>



@include('error-alert')

@include('layouts.footer') <!-- footer를 여기서 불러오면 항상 아래로 나옴 -->
