@extends('layouts.header')

@section('title', '회사소개 페이지')

@section('content')

<div class="sub-container">
  <div class="sub-title-wrap">
    <h4>회사소개</h4>  
  </div>

  <div class="article-wrap">
    <article>
      <div class="hide-1024">
        <!-- pc content 영역 -->
        {!! $items->content !!}
      </div>

      <div class="show-1024 hide-820">
        <!-- 패드 content 영역 -->
        {!! $items->content2 !!}
      </div>

      <div class="show-820">
        <!-- 모바일 content 영역 -->
        {!! $items->content3 !!}
      </div>

    </article>

  </div>



</div>



@endsection