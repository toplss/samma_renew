@extends('layouts.header')

@section('content')

<style>
  /* 키친시스템 */
  .ks-title{text-align: left; position: relative; width: 82%; margin: 2rem auto;}
  .ks-title small{position: absolute; top: 0; right: 0; font-size: 1.2rem; letter-spacing: -1px;}
  .ks-title span{font-size: 1.4rem;}
  .ks-title span>b{font-weight: 600;}
  .ks-title h3{font-size: 2.1rem; font-weight: 700; margin-top: 0.5rem;}
  .ks-title h3>i{font-style: normal; color: #8ec251;}

  .kitchen-slide{visibility: hidden; margin: auto; width: 83%;}
  .kitchen-slide.slick-initialized{visibility: visible;}
  .kitchen-slide .slick-slide{margin:0 5px;}
  .kitchen-slide .slick-track{padding-top: 50px;}
  .kitchen-slide .slick-arrow{background-color: transparent; font-size: 0; width: 36px; height: 36px; position: absolute; top: 50%; transform: translateY(-50%); z-index: 2;}
  .kitchen-slide>.slick-prev{background-image: url(/images/sub/ks-arrow.svg);background-repeat: no-repeat; transform: scaleX(-1); left: -7%;}
  .kitchen-slide>.slick-next{background-image: url(/images/sub/ks-arrow.svg);background-repeat: no-repeat; right: -7%;}
  .kitchen-card{background-color: #8ec251; box-shadow: 0 0 6px #8ec251; border-radius: 1.5rem; overflow: hidden; filter: grayscale(1); transition: 0.4s;}
  .kitchen-card.slick-current{filter: grayscale(0); scale: 1.2; position: relative; z-index: 2;}
  .kitchen-card>img{width: 80%; margin: auto; position: absolute; top: 0; left: 50%; transform: translateX(-50%);}
  .kitchen-card p{background-color: #fff; border: 1px solid #ddd; padding: 1.5rem 0.5rem; letter-spacing: -1px; border-radius: 1.5rem 1.5rem 0 0; margin-top: 150px;}
  .kitchen-card i{font-style: normal; font-size: 1rem;}
  .kitchen-card strong{font-size: 1.9rem; display: block;}
  .kitchen-card span{font-size: 1.2rem;}

</style>

<script>
$(function(){
	$(".kitchen-slide").slick({
		dots: false,
		arrows: true,
		autoplay: false,
		infinite: true,
		slidesToShow: 5,
		speed: 300,
		pauseOnHover : false,
		pauseOnFocus: false,
		centerMode: true,
    centerPadding: '0px',
		responsive: [
			{breakpoint: 1024,
				settings: {
					slidesToShow: 3,
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
          <li><a href="/public/sub/kitchen_system_06" data-name="리프트튀김기">리프트튀김기</a></li>
        </ul>
      </div>
    </div>
  </div>

  <div class="kitchen-wrap">


    <div class="ks-title">
      <span>근무강도는 <b>낮게</b> 비용절감은 <b>높게</b></span>
      <h3>비바쿡의 6가지 <i>푸드시스템</i></h3>
      <small>Kitchen System</small>
    </div>
    <div class="kitchen-slide">
			<a class="kitchen-card" href="/public/sub/kitchen_system_01">
				<img src="{{ asset('images/sub/ks1.png') }}">
        <p>
          <i>Kitchen System 01</i>
          <strong>스마트조리기</strong>
          <span>터치 한번으로 <br>60여가지 메뉴를 빠르게</span>
        </p>
			</a>
			<a class="kitchen-card" href="/public/sub/kitchen_system_02">
				<img src="{{ asset('images/sub/ks2.png') }}">
        <p>
          <i>Kitchen System 02</i>
          <strong>캡슐커피머신</strong>
          <span>1L 아메리카노를 <br>단 한 개의 캡슐로</span>
        </p>
				
			</a>
			<a class="kitchen-card" href="/public/sub/kitchen_system_03">
				<img src="{{ asset('images/sub/ks3.png') }}">
        <p>
          <i>Kitchen System 03</i>
          <strong>탄산디스펜서</strong>
          <span>업계최초 <br>코카콜라 단독계약</span>
        </p>
			</a>
			<a class="kitchen-card" href="/public/sub/kitchen_system_04">
				<img src="{{ asset('images/sub/ks4.png') }}">
        <p>
          <i>Kitchen System 04</i>
          <strong>수유식튀김기</strong>
          <span>고유가시대, <br>돈 버는 튀김기</span>
        </p>
			</a>
			<a class="kitchen-card" href="/public/sub/kitchen_system_05">
				<img src="{{ asset('images/sub/ks5.png') }}">
        <p>
          <i>Kitchen System 05</i>
          <strong>초음파식세기</strong>
          <span>설거지 끝판왕 <br>담그고 빼면 설거지 끝</span>
        </p>
			</a>
      <a class="kitchen-card" href="/public/sub/kitchen_system_06">
				<img src="{{ asset('images/sub/ks6.png') }}">
        <p>
          <i>Kitchen System 06</i>
          <strong>리프트튀김기</strong>
          <span>버튼만 누르면 알아서 조리하는 스마트 튀김기</span>
        </p>
				
				
			</a>
		</div>



	</div>
</div>



@endsection


