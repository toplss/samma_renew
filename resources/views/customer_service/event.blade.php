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

    <ul class="event-board active" data-ul="ing">
      <li onclick="location.href='/customer_service/event_view'">
        <a><img src="https://xn--hz2bqq88l.com/images/common_data/banner/b1c5a122369a137720ab8da3f7efa725.jpg" alt=""></a>
        <p>
          <b>이벤트 제목</b>
          <u>2026.04.20~2026.04.20</u>
        </p>
      </li>
      <li onclick="location.href='/customer_service/event_view'">
        <a><img src="https://xn--hz2bqq88l.com/images/common_data/banner/531ef4c640e6b435cc678957afb2e37d.jpg" alt=""></a>
        <p>
          <b>이벤트 제목</b>
          <u>2026.04.20~2026.04.20</u>
        </p>
      </li>
      <li onclick="location.href='/customer_service/event_view'">
        <a><img src="https://xn--hz2bqq88l.com/images/common_data/banner/1ae974d0b10903022f175039a1f690ca.jpg" alt=""></a>
        <p>
          <b>이벤트 제목</b>
          <u>2026.04.20~2026.04.20</u>
        </p>
      </li>
      <li onclick="location.href='/customer_service/event_view'">
        <a><img src="https://xn--hz2bqq88l.com/images/common_data/banner/f3735da308f61cace4c2fa8c17c56196.jpg" alt=""></a>
        <p>
          <b>이벤트 제목</b>
          <u>2026.04.20~2026.04.20</u>
        </p>
      </li>
      <li onclick="location.href='/customer_service/event_view'">
        <a><img src="https://xn--hz2bqq88l.com/images/common_data/banner/1ae974d0b10903022f175039a1f690ca.jpg" alt=""></a>
        <p>
          <b>이벤트 제목</b>
          <u>2026.04.20~2026.04.20</u>
        </p>
      </li>
      <li onclick="location.href='/customer_service/event_view'">
        <a><img src="https://xn--hz2bqq88l.com/images/common_data/banner/b1c5a122369a137720ab8da3f7efa725.jpg" alt=""></a>
        <p>
          <b>이벤트 제목</b>
          <u>2026.04.20~2026.04.20</u>
        </p>
      </li>
    </ul>

    <ul class="event-board" data-ul="end">
      <li onclick="location.href='/customer_service/event_view'" class="end">
        <a href=""><img src="https://xn--hz2bqq88l.com/images/common_data/banner/f3735da308f61cace4c2fa8c17c56196.jpg" alt=""></a>
        <p>
          <b>이벤트 제목</b>
          <u>2026.04.20~2026.04.20</u>
        </p>
      </li>
      <li onclick="location.href='/customer_service/event_view'" class="end">
        <a href=""><img src="https://xn--hz2bqq88l.com/images/common_data/banner/531ef4c640e6b435cc678957afb2e37d.jpg" alt=""></a>
        <p>
          <b>이벤트 제목</b>
          <u>2026.04.20~2026.04.20</u>
        </p>
      </li>
    </ul>

  </div>






</div>

@endsection