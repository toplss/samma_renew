@extends('layouts.header')

@section('content')


<script src="/js/gsap.min.js"></script>
<script src="/js/ScrollTrigger.min.js"></script>
<script src="/js/ScrollToPlugin.min.js"></script>
<script src="https://unpkg.com/split-type"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/SplitText.min.js"></script>
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
        initialSlide: 3,
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
        <button type="button" class="btn3 kitchen-btn" id="cateBtn">수유식튀김기<img src="{{ asset('images/icon/down.svg') }}"></button>
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
  
			<div class="tab02 system04">
                <div class="bg-1"><img src="{{ asset('images/kitchen/kitchen_bg1.svg') }}"></div>
                <div class="mw-1080">
                    <h4 class="red">FOOD SYSTEM 04</h4>
                    <h1>수유식튀김기</h1>

                    <div class="img-section"><img src="{{ asset('images/kitchen/kitchen_txt_06.png') }}"><img src="{{ asset('images/kitchen/kitchen_section_59.png') }}"></div>
                </div>
</div>

            <div class="tab19">
                <h3>검증<span>받은</span> 가성비 <span>튀김기로</span><br>운영비용<span>을 절감하세요.</span></h3>
                <div class="frame image-gallery">
                    <div class="top">
                        <span><img src="{{ asset('images/kitchen/kitchen_section_64.jpg') }}" data-index="1" class="zoomable"></span>
                        <span><img src="{{ asset('images/kitchen/kitchen_section_60.jpg') }}" data-index="2" class="zoomable"></span>
                    </div>
                    
                    <div class="bottom">
                        <span><img src="{{ asset('images/kitchen/kitchen_section_61.jpg') }}" data-index="3" class="zoomable"></span>
                        <span><img src="{{ asset('images/kitchen/kitchen_section_62.jpg') }}" data-index="4" class="zoomable"></span>
                        <span><img src="{{ asset('images/kitchen/kitchen_section_63.jpg') }}" data-index="5" class="zoomable"></span>
                    </div>
                </div>

                <div class="zoom-overlay">
                    <div class="zoom-set">
                        <img src="" alt="Zoomed Image">
                        <div class="image-info"></div>
                        <div class="image-x"></div>
                    </div>
                    
                    <div class="btn-set">
                        <button class="nav-button prev">&larr;</button>
                        <button class="nav-button next">&rarr;</button>
                    </div>
                </div>
</div>

            <div class="tab20">
                <img class="oil" src="{{ asset('images/kitchen/kitchen_section_65.png') }}">

                <p class="point-box">Point. 1</p>
                <h3>이보다<p> 알뜰</p>할 순 없다</h3>

                <div class="flex f-direction-col f-align-center txt">
                    <span>수유식 구조로 알뜰하게! 기름 산화의 원인! 튀김 찌꺼기, 부산물은 아래로</span>
                    <p>식용유 50% 절감</p>

                    <span class="box"></span>

                    <span>정제기로 한번 더 알뜰하게! 한번 쓴 기름은 정제하여 재 사용</span>
                    <p>식용유 25% 절감</p>
                </div>

                <div class="flex f-align-center img-box">
                    <div>
                        <img src="{{ asset('images/kitchen/kitchen_section_66.png') }}">
                        <p class="point-box">일반튀김기 원리</p>
                    </div>

                    <div>
                        <img src="{{ asset('images/kitchen/kitchen_section_67.png') }}">
                        <p class="point-box">수유식 튀김기 원리</p>
                    </div>
                </div>
                
                <div class="img-box model">
                    <img src="{{ asset('images/kitchen/kitchen_section_68.png') }}">

                    <div class="flex justify-center">
                        <div class="graph left ani">
                            <div class="rod"><p><span class="counter" data-target="250">250</span>만원</p></div>
                            <p class="point-box">일반튀김기</p>
                        </div>

                        <div class="graph right ani">
                            <div class="rod"><p><span class="counter" data-target="100">100</span>만원</p></div>
                            <p class="point-box">수유식튀김기</p>
                        </div>
                    </div>
                </div>

                <p class="txt-p">연간 식용유 사용량 비교</p>
</div>

            <div class="tab21">
                <p class="point-box">Point. 2</p>
                <h3>이보다 <span>깨끗</span>할 순 없다</h3>

                <div class="img-set">
                    <div class="in-img">
                        <img src="{{ asset('images/kitchen/kitchen_section_69.jpg') }}">
                    </div>

                    <div class="in-txt">
                        <p>물과 함께 튀겨 유증기 감소</p>
                        <span>깨끗한 조리 환경</span>
                        <span>식용유 냄새 감소</span>
                    </div>
                    
                </div>

                <div class="img-set">
                    <div class="in-img">
                        <img src="{{ asset('images/kitchen/kitchen_section_70.jpg') }}">
                    </div>

                    <div class="in-txt">
                        <p>지방과 찌꺼기가 타지 않아<br>건강한 요리</p>
                        <span>담백한 튀김 맛</span>
                        <span>발암물질 발생 최소화</span>
                    </div>
                </div>
</div>

            <div class="tab22">
                <p class="point-box">Point. 3</p>
                <h3>이보다<p> 간편</p>할 순 없다</h3>

                <div class="img-box flex twin ani">
                    <div class="point">
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" id="svg-img" data-name="svg-img" viewBox="0 0 155.57 78.87">
                            <defs>
                                <clipPath id="clip-path" transform="translate(0 -1.04)">
                                    <path class="cls-1" d="M0,0V78.87l155.57,1V1ZM116.13,79.79A39.38,39.38,0,1,1,155.5,40.41,39.37,39.37,0,0,1,116.13,79.79Z"/>
                                </clipPath>
                                <linearGradient id="gr_33" y1="39.44" x2="87.39" y2="39.44" gradientUnits="userSpaceOnUse"><stop offset="0" stop-color="#eda000" stop-opacity="0.2"/>
                                    <stop offset="0.07" stop-color="#eda000" stop-opacity="0.33"/>
                                    <stop offset="0.15" stop-color="#eda000" stop-opacity="0.47"/>
                                    <stop offset="0.25" stop-color="#eda000" stop-opacity="0.59"/>
                                    <stop offset="0.35" stop-color="#eda000" stop-opacity="0.68"/>
                                    <stop offset="0.46" stop-color="#eda000" stop-opacity="0.75"/>
                                    <stop offset="0.59" stop-color="#eda000" stop-opacity="0.79"/>
                                    <stop offset="0.77" stop-color="#eda000" stop-opacity="0.8"/>
                                </linearGradient>
                            </defs>
                            <path class="cls-2" d="M116.13,6A34.44,34.44,0,1,1,81.7,40.47,34.47,34.47,0,0,1,116.13,6m0-5a39.44,39.44,0,1,0,39.44,39.43A39.43,39.43,0,0,0,116.13,1Z" transform="translate(0 -1.04)"/>
                            <g class="cls-3">
                                <polygon class="cls-4" points="0 42.91 116.13 78.75 116.13 0.12 0 42.91 0 42.91"/>
                            </g>
                        </svg>
                    </div>
                    <img src="{{ asset('images/kitchen/kitchen_section_71.png') }}">
                    <img src="{{ asset('images/kitchen/kitchen_section_72.png') }}">
                </div>

                <div class="txt">
                    <p>쉬운 설치와 유지관리</p>
                    <span class="ani">열선 리프트 열선으로 <span>쉬운 청소</span></span>
                    <span class="ani">220V 정격 전압으로 <span>쉬운 설치</span></span>
                </div>

                <div class="img-box flex absol">
                    <img src="{{ asset('images/kitchen/kitchen_section_73.jpg') }}">
                    <img src="{{ asset('images/kitchen/kitchen_section_74.png') }}">
                </div>

                <div class="txt">
                    <p>다양한 기능으로 간편하게</p>
                    <span class="ani">한가한 시간에는 <span>대기모드</span>로 <span>안전</span>하게</span>
                    <span class="ani">시간, 온도 버튼 셋팅으로 <span>원터치 조리</span></span>
                </div>
</div>

            <div class="tab18 system04">
                <div class="product">
                    <img src="{{ asset('images/kitchen/round-ball.svg') }}">
                    <img src="{{ asset('images/kitchen/kitchen_section_75.png') }}">
                </div>

                <h3>제품 및 사양</h3>

                <ul class="grid grid-table">
                    <li>소비전력</li>
                    <li class="r-none">3.500W</li>
                    <li>유조용량</li>
                    <li class="r-none">물 : 약2.5L, 기름 : 15-16L</li>
                    <li>외형치수</li>
                    <li class="r-none">350X480X1020mm</li>
                    <li>무게</li>
                    <li class="r-none">약35kg</li>
                    <li>사용전압</li>
                    <li class="r-none">220V(단상), 50~60Hz</li>
                    <li>작동방식</li>
                    <li class="r-none">디지털 온도조절 방식(최고 설정 온도 199℃)</li>
                    <li>안전장치</li>
                    <li class="r-none">1차 : PCB HIGH-CUT(199℃) / 2차 : BIMETAL(200℃)</li>
                    <li>전기안전인증</li>
                    <li class="r-none">HIM070157-22001A</li>
                </ul>
</div>

            <div class="bottom">
                <p>모두가 만족한 비바쿡</p>
                <span>비바쿡은 압도적인 경쟁력을 뒷받침하는 체계적인 본사 시스템이 있습니다.</span>
</div>
			
		</div>
	</div>



<script>
    $(document).ready(function(){
         //이미지 유출을 막기 위한 오른쪽 클릭 금지
        document.addEventListener('contextmenu', (event) => {
            if (event.target.classList.contains('zoomable')) {
                event.preventDefault();
            }
        });
        
            // 스크롤 차단 함수
        function disableScroll() {
            window.addEventListener("wheel", preventScroll, { passive: false });
            window.addEventListener("touchmove", preventScroll, { passive: false });
        }

        // 스크롤 허용 함수
        function enableScroll() {
            window.removeEventListener("wheel", preventScroll);
            window.removeEventListener("touchmove", preventScroll);
        }

        function preventScroll(e) {
            e.preventDefault();
        }

        //이미지 zoom 스크립트
        var currentIndex = 0;
        var images = $(".zoomable");

        // 이미지 클릭 시 줌 동작
        $(".zoomable").on("click", function () {
            currentIndex = images.index(this); // 클릭한 이미지 인덱스를 설정
            updateZoomImage();
            $(".zoom-overlay").addClass("active");
            disableScroll();
        });

        // 줌 상태에서 닫기
        $(".zoom-overlay").on("click", function (e) {
            if (!$(e.target).is("img, .nav-button, .image-info")) {
                $(this).removeClass("active");
                enableScroll();
            }
        });

        // 이전 이미지로 이동
        $(".prev").on("click", function (e) {
            e.stopPropagation();
            currentIndex = (currentIndex - 1 + images.length) % images.length;
            updateZoomImage();
        });

        // 다음 이미지로 이동
        $(".next").on("click", function (e) {
            e.stopPropagation();
            currentIndex = (currentIndex + 1) % images.length;
            updateZoomImage();
        });

        // 줌 이미지 업데이트 함수
        function updateZoomImage() {
            var src = images.eq(currentIndex).attr("src");
            var index = images.eq(currentIndex).data("index"); // data-index 값 가져오기
            $(".zoom-overlay img").attr("src", src);
            $(".zoom-overlay .image-info").text(`${index} / ${images.length}`);
        }

    });
</script>

@endsection


