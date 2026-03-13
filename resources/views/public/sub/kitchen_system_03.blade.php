@extends('layouts.header')

@section('content')


<!-- 탄산 이벤트 -->
<script src="https://sammamall.com/public/_js/bubble.js"></script>
<script>
$(function(){
	$(".kitchen-slide").slick({
		dots: false,
		arrows: true,
		autoplay: false,
		infinite: false,
		slidesToShow: 3,
		speed: 500,
		autoplaySpeed: 3000,
		pauseOnHover : false,
		pauseOnFocus: false,
		centerMode: true,
		initialSlide: 2,
		responsive: [
			{
			breakpoint: 1024,
			settings: {
				slidesToShow: 2,
			}
			},
			{
			breakpoint: 680,
			settings: {
				slidesToShow: 1,
			}
			},      
		]
	});
})
</script>

<link rel="stylesheet" href="{{ asset('css/kitchen.css') }}?v={{ config('asset.version') }}">

<div class="sub-container">

	<div class="sub-title-wrap">
    <div class="st-left">
      <h4>키친시스템</h4>  
      <div class="category">
        <button type="button" class="btn3 kitchen-btn" id="cateBtn">탄산디스펜서<img src="{{ asset('images/icon/down.svg') }}"></button>
        <ul id="cateList" class="kitchen-list">
          <li><a href="/public/sub/kitchen_system_01" data-name="스마트조리기">스마트조리기</a></li>
          <li><a href="/public/sub/kitchen_system_02" data-name="캡슐커피머신">캡슐커피머신</a></li>
          <li><a href="/public/sub/kitchen_system_03" data-name="탄산디스펜서">탄산디스펜서</a></li>
          <li><a href="/public/sub/kitchen_system_04" data-name="수유식튀김기">수유식튀김기</a></li>
          <li><a href="/public/sub/kitchen_system_05" data-name="초음파식세기">초음파식세기</a></li>
        </ul>
      </div>
    </div>
  </div>
<div class="kitchen-wrap">
  <div class="tab01">
    <div class="tab-title">
      <a href="https://vivacook.kr/" target="_blank"><img src="{{ asset('images/icon/viva_icon.png') }}"></a>
      <h4>Kitchen System.</h4>
      <h3>비바쿡의 5가지 푸드시스템</h3>
    </div>
		<div class="kitchen-slide">
			<a class="kitchen-card" href="/public/sub/kitchen_system_01">
				<img src="{{ asset('images/kitchen/kitchen_03.png') }}">
				<h6>스마트 조리기</h6>
				<p>터치 한번으로 60여가지 메뉴를 뚝딱</p>
			</a>
			<a class="kitchen-card" href="/public/sub/kitchen_system_02">
				<img src="{{ asset('images/kitchen/kitchen_02.png') }}">
				<h6>대용량캡슐 커피머신</h6>
				<p>1L 아메리카노를 단 한 개의 캡슐로</p>
			</a>
			<a class="kitchen-card" href="/public/sub/kitchen_system_03">
				<img src="{{ asset('images/kitchen/kitchen_05.png') }}">
				<h6>탄산 디스펜서</h6>
				<p>업계최초 코카콜라 단독계약</p>
			</a>
			<a class="kitchen-card" href="/public/sub/kitchen_system_04">
				<img src="{{ asset('images/kitchen/kitchen_04.png') }}">
				<h6>수유식 튀김기</h6>
					<p>고유가시대, 돈 버는 튀김기</p>
			</a>
			<a class="kitchen-card" href="/public/sub/kitchen_system_05">
				<img src="{{ asset('images/kitchen/kitchen_01.png') }}">
				<h6>초음파 식세기</h6>
				<p>설거지 끝판왕! 담그고 빼면 설거지 끝</p>
			</a>
		</div>
    <!-- 슬라이드 -->
  </div>

			<div class="tab02 system03">
				<div class="bg-1">
					<img src="{{ asset('images/kitchen/kitchen_bg1.svg') }}">
				</div>
				<div class="mw-1080">
					<h4 class="red">FOOD SYSTEM 03</h4>
					<h1>탄산디스펜서</h1>
					<div class="img-section">
						<img src="{{ asset('images/kitchen/kitchen_txt_03.png') }}">
						<img src="{{ asset('images/kitchen/kitchen_05.png') }}">
					</div>
				</div>
			</div>

			<div class="tab14">
				<div class="title" id="quote">
					<h3>코카콜라 X 비바쿡</h3>
					<h3>업계최초 독점계약</h3>
				</div>

				<div class="band">
					<img src="{{ asset('images/kitchen/kitchen_section_49.png') }}">
					<div class="x-span"><span></span><span></span></div>
					<img src="{{ asset('images/kitchen/header_logo_w.svg') }}">
				</div>

				<div class="photo">
					<img src="{{ asset('images/kitchen/kitchen_section_50.png') }}">
				</div>
			</div>

			<div class="tab15">
				<p class="point-box">Point. 1</p>
				<h3>고객을 위해 깐깐하게 관리하는 탄산디스펜서</h3>

				<div class="box flex">
					<div class="flex f-direction-col f-align-center">
						<div class="img"><img src="{{ asset('images/kitchen/kitchen_section_51.png') }}" style="filter: brightness(1.05)"></div>

						<p>정수필터</p>
						<span>깨끗한 정수를 사용하여<br>음료를 만듭니다.</span>
					</div>

					<div class="flex f-direction-col f-align-center">
						<div class="img"><img src="{{ asset('images/kitchen/kitchen_section_52.png') }}"></div>

						<p>벨브소독</p>
						<span>연 2회 벨브소독으로<br>미생물 번식을 방지합니다.</span>
					</div>

					<div class="flex f-direction-col f-align-center">
						<div class="img"><img src="{{ asset('images/kitchen/kitchen_section_53.png') }}"></div>

						<p>정기소독</p>
						<span>연 2회 정기소독으로<br>기계의 내, 외부를 정밀소독 합니다.</span>
					</div>
				</div>
			</div>

			<div class="tab16">
				<p class="point-box">Point. 2</p>
				<h3><p>파격적인 마진율</p>왜 탄산디스펜서를 선택해야 할까요?</h3>

				<ul class="grid table">
					<li class="li-title">구분</li>
					<li class="li-title">뚱캔(355ml)판매</li>
					<li class="li-title r-none">탄산디스펜서(24oz)</li>

					<li>월 평균 판매량</li>
					<li class="col2-2 r-none">960잔</li>

					<li>판매가</li>
					<li class="col2-2 r-none">2,000원</li>

					<li>평균마진율</li>
					<li>55%</li>
					<li class="r-none">82%</li>

					<li>원가</li>
					<li>864,000원</li>
					<li class="r-none">355,200원</li>

					<li>수익</li>
					<li>1,056,000원</li>
					<li class="r-none">1,564,800원</li>

					<li class="li-footer">연간수익</li>
					<li class="li-footer">12,672,000원</li>
					<li class="li-footer r-none">18,777,600원</li>
				</ul>

				<div class="img-set">
					<img src="{{ asset('images/kitchen/kitchen_txt_04.png') }}">
					<img src="{{ asset('images/kitchen/kitchen_txt_05.png') }}">
				</div>
			</div>

			<div class="tab16 clone">
				<p class="point-box">Point. 3</p>
				<h3>고수들만 아는 탄산디스펜서<br>200% 활용방법</h3>

				<ul class="grid table">
					<li class="li-title">구분</li>
					<li class="li-title">탄산디스펜서 일반 판매</li>
					<li class="li-title r-none">탄산 무한리필 존 구비</li>

					<li>판매형태</li>
					<li class="c10f27">16,24oz 판매</li>
					<li class="r-none c10f27">16,24oz 판매와 함께<br>무한리필 컵을 판매하여 탄산음료를 셀프로 이용</li>

					<li>고객편의</li>
					<li>없 음</li>
					<li class="r-none">다양한 맛을 가성비 있게 즐길 수 있음.</li>

					<li>점주편의</li>
					<li>없 음</li>
					<li class="r-none">1. 컵만 제공하는 셀프 시스템으로 인건비 부담이 적음<br>2. 주변 매장과 차별화된 경쟁력 확보<br>3. 단골고객 형성<br>4. 고객의 체류시간 증가</li>

					<li>수익성</li>
					<li>음료판매</li>
					<li class="r-none">음료판매 + PC이용시간 증가 + 단골형성</li>
				</ul>
				<div class="img-set">
					<img src="{{ asset('images/kitchen/kitchen_txt_04_1.png') }}">
					<img src="{{ asset('images/kitchen/kitchen_txt_05_1.png') }}">
					<img src="{{ asset('images/kitchen/kitchen_txt_05.png') }}">
				</div>
			</div>

			<div class="tab17">
				<p class="point-box">Point. 4</p>
				<h3><span>마케팅에 최적화된 탄산디스펜서</span><br>이렇게 활용해보세요</h3>

				<ul class="grid box">
					<li class="bg">
						<p>MARKETING.1</p>
						<span>일정금액 이상 충전 시 음료 서비스</span>
					</li>
					<li><img src="{{ asset('images/kitchen/kitchen_section_54.jpg') }}"></li>
				</ul>
				<ul class="grid box center">
					<li><img src="{{ asset('images/kitchen/kitchen_section_55.jpg') }}"></li>
					<li class="bg">
						<p>MARKETING.2</p>
						<span>방학기간 고객확보를 위한 음료 제공 이벤트</span>
					</li>
				</ul>
				<ul class="grid box">
					<li class="bg">
						<p>MARKETING.3</p>
						<span>단골고객 확보를 위해 1회 무료리필 서비스 제공</span>
					</li>
					<li><img src="{{ asset('images/kitchen/kitchen_section_56.jpg') }}"></li>
				</ul>
			</div>

			<div class="tab18">
				<div class="product">
					<img src="{{ asset('images/kitchen/round-ball.svg') }}">
					<img src="{{ asset('images/kitchen/kitchen_section_57.png') }}">
				</div>

				<h3>제품 및 사양</h3>

				<ul class="grid grid-table">
					<li>전력량</li>
					<li class="r-none">1.2kw</li>
					<li>정격전압</li>
					<li class="r-none">230V/60Hz</li>
					<li>무게</li>
					<li class="r-none">60kg</li>
					<li>정수용량</li>
					<li class="r-none">36L</li>
					<li>추출구</li>
					<li class="r-none">4개</li>
				</ul>
			</div>

			<div class="bottom">
				<p>모두가 만족한 비바쿡</p>
				<span>비바쿡은 압도적인 경쟁력을 뒷받침하는 체계적인 본사 시스템이 있습니다.</span>
			</div>
			
		</div>
	</div>


@endsection


