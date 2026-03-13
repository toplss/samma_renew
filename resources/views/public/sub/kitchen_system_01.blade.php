@extends('layouts.header')

@section('content')

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
		responsive: [
			{breakpoint: 1024,
				settings: {
					slidesToShow: 2,
				}
			},     
			{breakpoint: 680,
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
        <button type="button" class="btn3 kitchen-btn" id="cateBtn">스마트조리기<img src="{{ asset('images/icon/down.svg') }}"></button>
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


			<div class="tab02 system01">
				<div class="bg-1"><img src="{{ asset('images/kitchen/kitchen_bg1.svg') }}"></div>
				<div class="mw-1080">
					<h4 class="red">FOOD SYSTEM 01</h4>
					<h1>스마트 조리기</h1>
					<div class="img-section">
						<img src="{{ asset('images/kitchen/kitchen_txt_01.png') }}">
						<img src="{{ asset('images/kitchen/kitchen_section_01.png') }}">
					</div>
				</div>
</div>

			<div class="tab03">
				<div class="machine_video mw-1080">
					<video id="hz" class="w-100" loop="" muted="" controls="" autoplay="" controlslist="nodownload" oncontextmenu="return false" playsinline="">
						<source src="{{ asset('images/kitchen/video_recipe.mp4') }}" type="video/mp4">
					</video>

					<h3>첫 출근한 알바도 할 수 있다!!</h3>
					<span>조리의 부담감을 싹 없앤 초 간편 조리기<br>원터치 조리방식으로 라면 수준의 난이도<br>혼자서 2~3가지 조리도 뚝딱! 인건비 50% 절감 효과</span>
				</div>
</div>

			<div class="tab04">
				<div class="round top">
					<div class="mask">
						<img src="{{ asset('images/kitchen/round_img_01.png') }}">
						<div class="effect">
							<span></span>
							<span></span>
							<span></span>
						</div>
					</div>
					<div class="txt">
						<h4>터치 한번이면 5분안에 뚝딱</h4>
						<p>재료 넣고 터치 한번이면 <span>모든 메뉴가 5분안에 뚝딱</span></p>
						<p>사용자의 99%가 만족한 <span>멀티쿠킹 기능</span></p>
						<p>다양한 <span>60여가지</span> 메뉴</p>
					</div>
				</div>

				<div class="round center">
					<div class="mask">
						<img src="{{ asset('images/kitchen/round_img_02.png') }}">
						<div class="effect">
							<span></span>
							<span></span>
							<span></span>
						</div>
					</div>
					<div class="txt">
						<h4>최적의 급수량, 시간, 온도</h4>
						<p>수많은 테스트를 통해 <span>검증된 설정값</span></p>
						<p><span>고객취향</span>에 따라 셋팅 변경 가능</p>
					</div>
				</div>

				<div class="round bottom">
					<div class="mask">
						<img src="{{ asset('images/kitchen/round_img_03.png') }}">
					</div>
					<div class="txt">
						<h4 style="color:#fff;">누가해도 한결같은 맛</h4>
						<p>원터치 조리방식으로 <span>라면 수준</span>의 난이도</p>
						<p>혼자서 2~3가지 조리도 뚝딱! <span>인건비 50% 절감효과</span></p>
					</div>
				</div>
</div>

			<div class="tab05">
				<h1>이제 <span>간단하게</span> 버튼<span>만 누르세요!</span></h1>
				<span class="circle">●</span><br><span class="circle">●</span>

				<div class="top">
					<img src="{{ asset('images/kitchen/kitchen_section_02.jpg') }}">
					<img src="{{ asset('images/kitchen/kitchen_section_03.png') }}">
				</div>

				<span class="circle">●</span><br><span class="circle">●</span>

				<div class="ani">
					<img src="{{ asset('images/kitchen/kitchen_section_04.png') }}">
					<img src="{{ asset('images/kitchen/kitchen_section_05.png') }}">
				</div>
				<div class="bg-2"><img src="{{ asset('images/kitchen/kitchen_bg2.svg') }}"></div>
</div>

			<div class="tab06">
				<div class="mw-1280">
					<h1><span>탁월한</span> 인건비 절감<span>효과</span></h1>

					<div class="round top">
						<div class="mask">
							<img src="{{ asset('images/kitchen/kitchen_section_06.png') }}">
						</div>

						<div class="txt">
							<h5>아찔하게 바빠질 때<br>화구 네 개 풀가동<span>!!</span></h5>
							<p>바쁜 시간과 단체주문에도 걱정 없는<br>비바쿡만의 간편 레시피로 효율적인 매출 상승을<br>도모합니다.</p>
						</div>
					</div>
					<div class="round center">
						<div class="mask">
							<img src="{{ asset('images/kitchen/kitchen_section_07.png') }}">
						</div>
						
						<div class="txt">
							<h5>혼자서도 충분한<br>멀티테스킹 조리</h5>
							<p>전문 인력으로 구성된 R&D팀에서 하나부터<br>열까지 직접 연구한 간단 레시피를 통해 누구나<br>쉽게 제조가 가능합니다.</p>
						</div>
					</div>
					<div class="round">
						<div class="mask">
							<img src="{{ asset('images/kitchen/kitchen_section_08.png') }}">
						</div>

						<div class="txt">
							<h5>최저시급 만원 시대<br>스마트조리기로 인건비 절감</h5>
							<p>조리기 하나로 인건비 절반 수준으로 줄이고<br>최소인력으로 매장운영이 가능합니다.</p>
						</div>
					</div>
				</div>
</div>

			<div class="bg-3"><img src="{{ asset('images/kitchen/kitchen_bg3.svg') }}"></div>

			<div class="tab07">
				<h1><span>아직!</span> 장점<span>이</span> 더<span> 있어요</span></h1>

				<span class="border ani"></span>

				<div class="point one">
					<p>Point.1</p>
					<span>위험하지 않은 인덕션 열원사용</span>
					<div class="point-img">
						<div class="point-bg"><img src="{{ asset('images/kitchen/kitchen_section_09.png') }}"></div>
						<img class="one-set" src="{{ asset('images/kitchen/kitchen_section_10.png') }}">
					</div>
				</div>

				<span class="border ani"></span>

				<div class="point two">
					<p>Point.2</p>
					<span>좁은 공간에도 충분한 사이즈</span>
					<div class="point-img">
						<div class="point-bg"><img src="{{ asset('images/kitchen/kitchen_section_11.png') }}"></div>
					</div>
				</div>

				<span class="border ani"></span>

				<div class="point three">
					<p>Point.3</p>
					<span>빠른 설치 빠른 A/S</span>
					<div class="point-img">
						<div class="point-bg"><img src="{{ asset('images/kitchen/kitchen_section_09.png') }}"></div>
						<img class="absolut-img" src="{{ asset('images/kitchen/kitchen_section_12.png') }}">
					</div>
				</div>

				<span class="border ani"></span>

				<div class="point four">
					<p>Point.4</p>
					<span>간편한 인수인계</span>
					<div class="point-img">
						<div class="point-bg"><img src="{{ asset('images/kitchen/kitchen_section_13.png') }}"></div>
					</div>
				</div>

				<span class="border ani"></span>

				<div class="point five">
					<p>Point.5</p>
					<span>다양한 전용 원팩상품</span>
					<div class="point-img">
						<div class="point-bg"><img src="{{ asset('images/kitchen/kitchen_section_09.png') }}"></div>
						<img class="absolut-img" src="{{ asset('images/kitchen/kitchen_section_14.png') }}">
					</div>
				</div>
</div>

			<div class="tab08">
				<h1>스마트하고 컴팩트한 사이즈</h1>

				<div class="product">
					<img class="ball" src="{{ asset('images/kitchen/round-ball.svg') }}">
					<img class="img" src="{{ asset('images/kitchen/kitchen_section_15.png') }}">
				</div>
</div>

			<div class="bottom">
				<p>모두가 만족한 비바쿡</p>
				<span>비바쿡은 압도적인 경쟁력을 뒷받침하는 체계적인 본사 시스템이 있습니다.</span>
			</div>
			

	</div>
</div>



@endsection


