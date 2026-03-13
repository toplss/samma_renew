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
		initialSlide: 1,
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
        <button type="button" class="btn3 kitchen-btn" id="cateBtn">캡슐커피머신<img src="{{ asset('images/icon/down.svg') }}"></button>
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

			<div class="tab02 system02">
				<div class="bg-1"><img src="{{ asset('images/kitchen/kitchen_bg1.svg') }}"></div>
				<div class="mw-1080">
					<h4 class="red">FOOD SYSTEM 02</h4>
					<h1>캡슐커피머신</h1>

					<div class="img-section"><img src="{{ asset('images/kitchen/kitchen_txt_02.png') }}"><img src="{{ asset('images/kitchen/kitchen_section_16.png') }}"></div>
				</div>
</div>

			<div class="tab09">
				<div class="mw-1080">
					<h3>쉬워도 너무 쉽다!</h3>
					<p class="sub">마이그랑 캡슐커피머신이 승승장구하는 이유</p>

					<div class="box point1">
						<div class="flex f-align-center">
							<div class="flex f-direction-col">
								<div class="flex f-align-center">
									<div class="icon flex justify-center"><img src="{{ asset('images/kitchen/kitchen_icon_01.png') }}"></div>
									
									<div class="in-txt">
										<span>Point. 1</span>
										<p>한국인에 딱 맞는 용량</p>
									</div>
								</div>

								<span class="hr"></span>

								<p class="in-p">13oz부터 32oz 대용량 까지 캡슐 한개로 완벽하게 추출</p>
							</div>

							<img class="in-img" src="{{ asset('images/kitchen/kitchen_section_17.png') }}">
						</div>
					</div>

					<div class="box point2">
						<div class="flex f-align-center">
							<div class="flex f-direction-col">
								<div class="flex f-align-center">
									<div class="icon flex justify-center"><img src="{{ asset('images/kitchen/kitchen_icon_02.png') }}"></div>
									
									<div class="in-txt">
										<span>Point. 2</span>
										<p>재고관리부터 위생까지 걱정 끝</p>
									</div>
								</div>

								<span class="hr"></span>

								<p class="in-p">캡슐 형태로 재고관리와 청결한 머신관리까지 한번에</p>
							</div>

							<img class="in-img" src="{{ asset('images/kitchen/kitchen_section_18.png') }}">
						</div>
					</div>

					<div class="box point3">
						<div class="flex f-align-center">
							<div class="flex f-direction-col">
								<div class="flex f-align-center">
									<div class="icon flex justify-center"><img src="{{ asset('images/kitchen/kitchen_icon_02.png') }}"></div>
									
									<div class="in-txt">
										<span>Point. 3</span>
										<p>레시피가 필요없다!</p>
									</div>
								</div>

								<span class="hr"></span>

								<p class="in-p">20가지 캡슐만 넣으면 20가지 메뉴로 탄생</p>
							</div>

							<img class="in-img" src="{{ asset('images/kitchen/kitchen_section_19.png') }}">
						</div>
					</div>

					<div class="box point4">
						<div class="flex f-align-center">
							<div class="flex f-direction-col">
								<div class="flex f-align-center">
									<div class="icon flex justify-center"><img src="{{ asset('images/kitchen/kitchen_icon_03.png') }}"></div>
									
									<div class="in-txt">
										<span>Point. 4</span>
										<p>믿고 마시는 국내생산·유통</p>
									</div>
								</div>

								<span class="hr"></span>

								<p class="in-p">엄선된 원료로 직접 생산하여<br>가격의 거품을 확 줄였습니다.</p>
							</div>

							<img class="in-img" src="{{ asset('images/kitchen/kitchen_section_20.png') }}">
						</div>
					</div>

					<div class="box point5">
						<div class="flex f-align-center">
							<div class="flex f-direction-col">
								<div class="flex f-align-center">
									<div class="icon flex justify-center"><img src="{{ asset('images/kitchen/kitchen_icon_04.png') }}"></div>
									
									<div class="in-txt">
										<span>Point. 5</span>
										<p>관리는 스마트하게</p>
									</div>
								</div>

								<span class="hr"></span>

								<p class="in-p">자동절전모드, 자동청소모드로 기기관리를 수월하게</p>
							</div>

							<img class="in-img" src="{{ asset('images/kitchen/kitchen_section_21.png') }}">
						</div>
					</div>

					<div class="box point6">
						<div class="flex f-align-center">
							<div class="flex f-direction-col">
								<div class="flex f-align-center">
									<div class="icon flex justify-center"><img src="{{ asset('images/kitchen/kitchen_icon_05.png') }}"></div>
									
									<div class="in-txt">
										<span>Point. 6</span>
										<p>공간 제약 없는 쉬운 설치</p>
									</div>
								</div>

								<span class="hr"></span>

								<p class="in-p">직수와 콘센트만 연결하면 설치 끝</p>
							</div>

							<img class="in-img" src="{{ asset('images/kitchen/kitchen_section_22.png') }}">
						</div>
					</div>

				</div>
			</div>

			<div class="tab10">
				<div class="mw-1080">
					<h3 class="flex f-align-baseline ani"><p>캡슐 <span class="point">한</span><span class="point">개</span></p>로 끝내는 <p>다양한 메뉴</p>라인업</h3>

					<ul class="grid grid-sys2">
						<li>
							<div class="in-img">
								<img src="{{ asset('images/kitchen/kitchen_section_23.png') }}">
							</div>
							<div class="flex f-direction-col">
								<p>아메리카노</p>
								<span class="hr"></span>
								<p>원두 · 용량에 따라 즐기는<br>5가지 다양한 아메리카노</p>
							</div>
						</li>
						<li>
							<div class="in-img">
								<img src="{{ asset('images/kitchen/kitchen_section_24.png') }}">
							</div>
							<div class="flex f-direction-col">
								<p>디카페인 아메리카노</p>
								<span class="hr"></span>
								<p>카페인은 줄이고 커피 본연의 깊은 맛을<br>전하는 깔끔한 아메리카노</p>
							</div>
						</li>
						<li>
							<div class="in-img">
								<img src="{{ asset('images/kitchen/kitchen_section_25.png') }}">
							</div>
							<div class="flex f-direction-col">
								<p>카라멜마끼아또</p>
								<span class="hr"></span>
								<p>깊고 진한 카라멜의 풍미를<br>가득담은 부드러운 라떼</p>
							</div>
						</li>
						<li>
							<div class="in-img">
								<img src="{{ asset('images/kitchen/kitchen_section_26.png') }}">
							</div>
							<div class="flex f-direction-col">
								<p>초코라떼</p>
								<span class="hr"></span>
								<p>최상의 코코아빈으로 만들어낸<br>깊고 진한 초코라떼</p>
							</div>
						</li>

						<li>
							<div class="in-img">
								<img src="{{ asset('images/kitchen/kitchen_section_27.png') }}">
							</div>
							<div class="flex f-direction-col">
								<p>바나나라떼</p>
								<span class="hr"></span>
								<p>바나나향을 가득 머금은<br>달콤한 라떼</p>
							</div>
						</li>
						<li>
							<div class="in-img">
								<img src="{{ asset('images/kitchen/kitchen_section_28.png') }}">
							</div>
							<div class="flex f-direction-col">
								<p>카페라떼</p>
								<span class="hr"></span>
								<p>고소한 밀크폼과 향긋한 커피가<br>만난 달콤한 카페라떼</p>
							</div>
						</li>
						<li>
							<div class="in-img">
								<img src="{{ asset('images/kitchen/kitchen_section_29.png') }}">
							</div>
							<div class="flex f-direction-col">
								<p>다방커피</p>
								<span class="hr"></span>
								<p>한국인이 사랑하는<br>달달 시원한 K-라떼</p>
							</div>
						</li>
						<li>
							<div class="in-img">
								<img src="{{ asset('images/kitchen/kitchen_section_30.png') }}">
							</div>
							<div class="flex f-direction-col">
								<p>복숭아아이스티</p>
								<span class="hr"></span>
								<p>부드러운 폼이 매력인<br>상큼한 복숭아 아이스티</p>
							</div>
						</li>

						<li>
							<div class="in-img">
								<img src="{{ asset('images/kitchen/kitchen_section_31.png') }}">
							</div>
							<div class="flex f-direction-col">
								<p>오미자아이스티</p>
								<span class="hr"></span>
								<p>다섯가지 오묘한 맛이<br>매력인 아이스티</p>
							</div>
						</li>
						<li>
							<div class="in-img">
								<img src="{{ asset('images/kitchen/kitchen_section_32.png') }}">
							</div>
							<div class="flex f-direction-col">
								<p>레몬아이스티</p>
								<span class="hr"></span>
								<p>새콤달콤한 레몬을<br>가득 머금은 아이스티</p>
							</div>
						</li>
						<li>
							<div class="in-img">
								<img src="{{ asset('images/kitchen/kitchen_section_33.png') }}">
							</div>
							<div class="flex f-direction-col">
								<p>청포도아이스티</p>
								<span class="hr"></span>
								<p>신선한 청포도향을 가득<br>머금은 아이스티</p>
							</div>
						</li>
						<li>
							<div class="in-img">
								<img src="{{ asset('images/kitchen/kitchen_section_34.png') }}">
							</div>
							<div class="flex f-direction-col">
								<p>자몽아이스티</p>
								<span class="hr"></span>
								<p>자몽 특유의 스트러스<br>향이 일품인 아이스티</p>
							</div>
						</li>
					</ul>
				</div>
			</div>

			<div class="tab11">
				<div class="mw-1080">
					<img class="in-img1" src="{{ asset('images/kitchen/kitchen_section_35_bg.png') }}">
					<img class="in-img2" src="{{ asset('images/kitchen/kitchen_section_35_1.png') }}">
					<img class="in-img3" src="{{ asset('images/kitchen/kitchen_section_35_2.png') }}">
					<div class="in-img4">
						<img src="{{ asset('images/kitchen/kitchen_section_35_3.png') }}">
					</div>
				</div>
			</div>

			<div class="tab12">
				<p class="text-center">캡슐커피머신 + 반자동머신 두 가지 기능을 겸비한</p>
				<h3 class="flex justify-center"><p>국내최초 하이브리드형 커피머신, 마이그랑</p></h3>

				<div class="mw-1080">
					<img src="{{ asset('images/kitchen/kitchen_section_36.png') }}">
					<div class="box">
						<h3>작아도 필요한 건 다 있다!</h3>
						<ul class="grid grid-sys2">
							<li>
								<div class="in-img">
									<img src="{{ asset('images/kitchen/kitchen_section_37.png') }}">
								</div>
								<div class="flex f-direction-col">
									<p>핫 워터 디스펜서</p>
									<span class="hr"></span>
									<p>정수필터를 거친<br>100℃의 뜨거운 물 공급</p>
								</div>
							</li>
							<li>
								<div class="in-img">
									<img src="{{ asset('images/kitchen/kitchen_section_38.png') }}">
								</div>
								<div class="flex f-direction-col">
									<p>밀크 스티머</p>
									<span class="hr"></span>
									<p>우유를 빠르게<br>데워주는 스티머</p>
								</div>
							</li>
							<li>
								<div class="in-img">
									<img src="{{ asset('images/kitchen/kitchen_section_39.png') }}">
								</div>
								<div class="flex f-direction-col">
									<p>머그 워머</p>
									<span class="hr"></span>
									<p>머그를 따뜻하게 데워<br>음료의 온도 유지</p>
								</div>
							</li>
							<li>
								<div class="in-img">
									<img src="{{ asset('images/kitchen/kitchen_section_40.png') }}">
								</div>
								<div class="flex f-direction-col">
									<p>자동절전모드</p>
									<span class="hr"></span>
									<p>장시간 미사용 시<br>효율적인 전력 관리</p>
								</div>
							</li>
							<li>
								<div class="in-img">
									<img src="{{ asset('images/kitchen/kitchen_section_41.png') }}">
								</div>
								<div class="flex f-direction-col">
									<p>원터치 3종 추출버튼</p>
									<span class="hr"></span>
									<p>다양한 메뉴를<br>버튼 하나로 간편하게 추출</p>
								</div>
							</li>
							<li>
								<div class="in-img">
									<img src="{{ asset('images/kitchen/kitchen_section_42.png') }}">
								</div>
								<div class="flex f-direction-col">
									<p>LED모니터</p>
									<span class="hr"></span>
									<p>기능설정과<br>추출 잔 수 확인</p>
								</div>
							</li>
							<li>
								<div class="in-img">
									<img src="{{ asset('images/kitchen/kitchen_section_43.png') }}">
								</div>
								<div class="flex f-direction-col">
									<p>3L 온수탱크</p>
									<span class="hr"></span>
									<p>넉넉한 양의<br>정수 탱크</p>
								</div>
							</li>
							<li>
								<div class="in-img">
									<img src="{{ asset('images/kitchen/kitchen_section_44.png') }}">
								</div>
								<div class="flex f-direction-col">
									<p>홀 거치대</p>
									<span class="hr"></span>
									<p>컵과 샷잔 모두 사용 가능한<br>구멍이 뚫려있는 위생적인 거치대</p>
								</div>
							</li>
						</ul>
					</div>
				</div>
			</div>

			<div class="tab13 mw-1080">
				<div class="img-set">
					<img src="{{ asset('images/kitchen/kitchen_section_45.png') }}">
					<img src="{{ asset('images/kitchen/kitchen_section_46.png') }}">
					<img src="{{ asset('images/kitchen/kitchen_section_47.png') }}">
					<img src="{{ asset('images/kitchen/kitchen_section_48.png') }}">
				</div>

				<h3 class="flex f-align-baseline justify-center"><p>마이그랑 머신</p> 제원</h3>

				<ul class="grid grid-table">
					<li>기기명</li>
					<li>프로페셔널 마이아 머신</li>

					<li>제품소개</li>
					<li>소규모 공간에 적합한 고압추출 캡슐 커피머신</li>

					<li>사이즈<span>(mm)</span></li>
					<li>프540(M) x 480(D) x 430(H)</li>

					<li>소비전력</li>
					<li>2.6kW</li>

					<li>무게</li>
					<li>24kg</li>

					<li>저수용량</li>
					<li>3L</li>

					<li>급수방법</li>
					<li>카페전용 고급필터 설치 후 직수 연결</li>

					<li>추출압력</li>
					<li>20BAR</li>

					<li>추출헤드</li>
					<li>2개</li>
				</ul>
</div>

			<div class="bottom">
				<p>모두가 만족한 비바쿡</p>
				<span>비바쿡은 압도적인 경쟁력을 뒷받침하는 체계적인 본사 시스템이 있습니다.</span>
</div>
			
		</div>
	</div>




@endsection


