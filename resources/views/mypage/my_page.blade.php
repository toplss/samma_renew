@extends('layouts.header')

@section('content')

<div class="sub-container">
  <div class="sub-title-wrap">
      <h4>마이페이지</h4>  
  </div>

    @include('layouts.mypage_top')
    
  <div class="mmy-wrap">
    <button class="btn1">마이페이지</button>
    <ul>
      <li><a href="/mypage/orderinquiry">주문내역</a></li>
      <li><a href="/mypage/my_equipment_list">보유장비</a></li>
      <li><a href="/mypage/cart">장바구니</a></li>
      <li><a href="/mypage/my_point">충전금내역</a></li>
      <li><a href="/mypage/my_point_reserve">적립금내역</a></li>
      <li><a href="/mypage/register_access">회원정보수정</a></li>
    </ul>

    <button class="btn1">고객센터</button>
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


</div>

<style>
  .mypage-top #search_form{display: none;}
</style>

@endsection




