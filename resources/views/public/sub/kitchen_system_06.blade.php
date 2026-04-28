@extends('layouts.header')

@section('content')

<script>
$(function(){
	$(".kitchen-slide").slick({
		dots: true,
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
			{breakpoint: 1280,
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
				<img src="{{ asset('images/sub/ks2.png') }}" class="kci2">
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
          <span>버튼만 누르면 알아서 <br>조리하는 스마트 튀김기</span>
        </p>
				
				
			</a>
		</div>



	</div>
</div>



@endsection


