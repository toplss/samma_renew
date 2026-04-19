@extends('layouts.header')

@section('content')

<div class="sub-container">
  <div class="sub-title-wrap">
    <div class="flex">
      <h4>이벤트</h4>
      <ul class="e-tab">
        <li data-tab="ing" class="active">진행중인 이벤트</li>
        <li data-tab="end">종료된 이벤트</li>
      </ul>
    </div>
  </div>

  <div class="event-wrap">
  	<div class="board-view">
      
			<div class="board-title">
				<h6>이벤트 제목입니다.</h6>
				<!-- <ul class="flex-end">
					<li>작성자 : 관리자</li>
					<li>작성일 : 2026-04-01 17:25:59</li>
					<li class="hide-680">조회수 : 1000</li>
				</ul> -->
			</div>

			<div class="board-content">
				<img src="https://samma-erp.com/smarteditor/upload/2512/3dc24063fba6ec2cbaf0c55769916dfe_1766713936_2811.jpg" alt="">
			</div>
			<!-- <ul class="board-attachment">
				<li><span>첨부</span><a href="" ></a></li>
			</ul> -->
			<div class="flex-end" style="gap:0.5rem;">
				<!-- <button type="button" class="btn3">수정</button>
				<button type="button" class="btn3">삭제</button> -->
				<button type="button" class="btn1" onclick="location.href='/customer_service/event'">목록</button>
			</div>

		</div>

  </div>






</div>

@endsection