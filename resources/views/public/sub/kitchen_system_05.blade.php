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
        initialSlide: 4,
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
        <button type="button" class="btn3 kitchen-btn" id="cateBtn">초음파식세기<img src="{{ asset('images/icon/down.svg') }}"></button>
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

			<div class="tab02 system05">
                <div class="bg-1"><img src="{{ asset('images/kitchen/kitchen_bg1.svg') }}"></div>
                <div class="mw-1080">
                    <h4 class="red">FOOD SYSTEM 05</h4>
                    <h1>초음파식세기</h1>

                    <div class="img-section"><img src="{{ asset('images/kitchen/kitchen_txt_07.png') }}"><img src="{{ asset('images/kitchen/kitchen_section_76.png') }}"></div>
                </div>
            </div>

            <div class="tab23 mw-1080">
                <h3>설거지, 아직도 <span>직접 하시나요?</span></h3>
                <p>초음파식세기에 넣었다가 빼기만 하면 설거지 끝!<br>평소 세척하기 어려웠던 부분까지 위생적으로 세척하세요</p>

                <div class="img-box ani">
                    <img src="{{ asset('images/kitchen/kitchen_section_77.jpg') }}">

                    <img src="{{ asset('images/kitchen/kitchen_section_78.png') }}">
                    <img src="{{ asset('images/kitchen/kitchen_section_79.png') }}">
                </div>
            </div>

            <div class="tab15 system05">
                <p class="point-box">Point. 1</p>
                <h3>비바쿡 초음파식세기를 선택해야하는 이유</h3>

                <div class="flex f-align-center box">
                    <div class="flex f-direction-col f-align-center">
                        <div class="img"><img src="{{ asset('images/kitchen/kitchen_section_80.png') }}"></div>
                        <p>친환경 1급세제</p>
                        <span>에너지 절감, 인체에 무해한<br>1급 세제 사용으로 친환경적입니다.</span>
                    </div>

                    <div class="flex f-direction-col f-align-center">
                        <div class="img"><img src="{{ asset('images/kitchen/kitchen_section_81.png') }}"></div>
                        <p>낮은 노동강도</p>
                        <span>수세미질이 필요 없습니다.<br>초음파세척 후 에벌세척만으로 충분합니다.</span>
                    </div>

                    <div class="flex f-direction-col f-align-center">
                        <div class="img"><img src="{{ asset('images/kitchen/kitchen_section_82.png') }}"></div>
                        <p>A/S 5년 무상</p>
                        <span>업계 최초5년간 무상 A/S를 지원합니다.<br><span style="color: #ff0000;">* 단, 사용자 부주의로 인한 A/S시 비용발생</span></span>
                    </div>
                </div>
            </div>

            <div class="tab16 system05">
                <p class="point-box">Point. 2</p>
                <h3>획기적인 <span>비용절감,</span> 직접 비교해보세요</h3>

                <ul class="grid table">
                    <li class="li-title">구분</li>
                    <li class="li-title">일반 수압식 세척기</li>
                    <li class="li-title">초음파 식기 세척기</li>
                    <li class="li-title">절감액</li>

                    <li>전기료</li>
                    <li>월90,000원</li>
                    <li>월 10,000원 이하</li>
                    <li>80,000원</li>

                    <li>가스비</li>
                    <li>월80,000원</li>
                    <li>없음</li>
                    <li>80,000원</li>

                    <li>수도료</li>
                    <li>월15,000원</li>
                    <li>월3,000원</li>
                    <li>12,000원</li>

                    <li>인건비</li>
                    <li>월1,000,000원(1인)</li>
                    <li>월500,000원(0.5인)</li>
                    <li>500,000원</li>

                    <li>세제</li>
                    <li>월110,000원(합성세제,린스)</li>
                    <li>월30,000원(친환경세제)</li>
                    <li>80,000원</li>

                    <li class="li-footer b-bor">월 사용</li>
                    <li class="li-footer b-bor">1,295,000원</li>
                    <li class="li-footer b-bor">543,000원</li>
                    <li class="li-footer b-bor">752,000원</li>

                    <li class="li-footer">1년사용</li>
                    <li class="li-footer">15,540,000원</li>
                    <li class="li-footer">6,516,000원</li>
                    <li class="li-footer">9,024,000원</li>
                </ul>

                <div class="img-set">
                    <img src="{{ asset('images/kitchen/kitchen_txt_08.png') }}">
                    <img src="{{ asset('images/kitchen/kitchen_txt_09.png') }}">
                </div>
            </div>

            <div class="tab17 system05">
                <p class="point-box">Point. 3</p>
                <h3><span>3단계</span>로 진행되는 <span>완.벽.세.척</span></h3>

                <ul class="grid box">
                    <li class="bg">
                        <p>STEP.1 히팅</p>
                        <span>세척에 최적화된 40℃ 미온수를 유지하며 식기살균, 세척에 추가 도움</span>
                    </li>
                    <li><img src="{{ asset('images/kitchen/kitchen_section_83.jpg') }}"></li>
                </ul>
                <ul class="grid box center">
                    <li><img src="{{ asset('images/kitchen/kitchen_section_84.jpg') }}"></li>
                    <li class="bg">
                        <p>STEP.2 버블</p>
                        <span>세척물을 뒤섞어주고 초음파 활성 세제를 지속 혼합하여 세척력 증대</span>
                    </li>
                </ul>
                <ul class="grid box">
                    <li class="bg">
                        <p>STEP.3 초음파</p>
                        <span class="pc-640">미세한 초음파 기포가 식기, 용기 사이 틈으로 침투하여 잔여 오염물질과 피세척물을 분리</span>
                        <span class="media-640">미세한 초음파 기포가 식기, 용기 사이 틈으로 침투하여 잔여 오염물질과 피세척물을 분리</span>
                    </li>
                    <li><img src="{{ asset('images/kitchen/kitchen_section_85.jpg') }}"></li>
                </ul>
            </div>

            <div class="tab24">
                <h3>이런분들에게 <span>추천</span>드립니다.</h3>

                <p><span class="square"></span><span class="s-txt">설거지 양이 많은 매장</span></p>
                <p><span class="square"></span><span class="s-txt">피크시간, 직원 한명은 싱크대에 붙어있어야 하는 매장</span></p>
                <p><span class="square"></span><span class="s-txt">인건비 걱정에서 벗어나고싶은 매장</span></p>
                <p><span class="square"></span><span class="s-txt">수압식 세척기 운영비용이 부담스러운 매장</span></p>

            </div>

            <div class="tab19 system05">
                <h3><span>비용 절감 / 살균 • 세척력 / 간편성</span><br>비바쿡의 검증된 기술력</h3>
                <div class="frame image-gallery">
                    <div class="top">
                        <span><img src="{{ asset('images/kitchen/kitchen_section_87.jpg') }}" data-index="1" class="zoomable"></span>
                        <span><img src="{{ asset('images/kitchen/kitchen_section_88.jpg') }}" data-index="2" class="zoomable"></span>
                        <span><img src="{{ asset('images/kitchen/kitchen_section_89.jpg') }}" data-index="3" class="zoomable"></span>

                        <span><img src="{{ asset('images/kitchen/kitchen_section_90.jpg') }}" data-index="4" class="zoomable"></span>
                        <span><img src="{{ asset('images/kitchen/kitchen_section_91.jpg') }}" data-index="5" class="zoomable"></span>
                        <span><img src="{{ asset('images/kitchen/kitchen_section_92.jpg') }}" data-index="6" class="zoomable"></span>
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
            </div>

            <div class="tab18 system05">
                <div class="product">
                    <img src="{{ asset('images/kitchen/round-ball.svg') }}">
                    <img src="{{ asset('images/kitchen/kitchen_section_93.png') }}">
                </div>

                <h3>제품별 상세 사이즈</h3>

                <ul class="grid grid-table">
                    <li>model_600</li>
                    <li class="r-none">가로:600 x 세로:700 x 폭850mm</li>
                    <li>model_700</li>
                    <li class="r-none">가로:700 x 세로:700 x 폭850mm</li>
                    <li>model_800</li>
                    <li class="r-none">가로:800 x 세로:700 x 폭850mm</li>
                    <li>model_900</li>
                    <li class="r-none">가로:900 x 세로:700 x 폭850mm</li>
                    <li>model_1000</li>
                    <li class="r-none">가로:1000 x 세로:700 x 폭850mm</li>
                    <li>model_1100</li>
                    <li class="r-none">가로:1100 x 세로:700 x 폭850mm</li>
                    <li>model_1200</li>
                    <li class="r-none">가로:1200 x 세로:700 x 폭850mm</li>
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

         //숫자 증가
        const startCounters = (counter) => {
            counter.textContent = "0";
        
            const targetNum = +counter.getAttribute("data-target");
        
            const updateCounter = () => {
                const count = +counter.textContent;
        
                const increment = targetNum / 100; // 증가 속도
                const nextCount = Math.ceil(count + increment);
        
                counter.textContent = nextCount > targetNum ? targetNum : nextCount;
        
                if (count < targetNum) {
                    requestAnimationFrame(updateCounter);
                }
            };
        
            updateCounter();
        };

        document.querySelectorAll(".tab20 .graph").forEach((tab20Graph) => {
            gsap.to(tab20Graph, {
                scrollTrigger: {
                    trigger: tab20Graph,
                    start: "top 100%",
                    end: "bottom bottom",
                },
                onStart: () => {
                    tab20Graph.classList.add("ani");
                    const counter = tab20Graph.querySelector(".counter"); // 각 graph 내부의 counter 찾기
                    if (counter) {
                        startCounters(counter); // 해당 counter 전달
                    }
                }
                //overwrite: true, //이전 애니메이션 무시
            });
        });

        gsap.set(".tab20 .graph .point-box", { opacity: 0, y: 100,});
        document.querySelectorAll(".tab20 .graph .point-box").forEach((tab20Point) => {
            gsap.to(tab20Point, {
                scrollTrigger: {
                    trigger: tab20Point,
                    start: "top 100%",
                    end: "bottom bottom",
                },
                opacity: 1,
                y: "40px",
                overwrite: true, //이전 애니메이션 무시
            });
        });
    });
</script>

@endsection


