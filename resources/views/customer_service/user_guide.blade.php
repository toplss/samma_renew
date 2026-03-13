@extends('layouts.header')

@section('title', '이용안내 페이지')

@section('content')

<div class="sub-container">
  <div class="sub-title-wrap">
    <h4>이용안내</h4>  
  </div>

  <div class="article-wrap">
    <h3 class="article-title"></h3>
    <article>
      <div class="hide-1024">
        <!-- pc content 영역 -->
        {!! str_replace('src="', 'src="'.asset('images'), $items->content) !!}
      </div>

      <div class="show-1024 hide-820">
        <!-- 패드 content 영역 -->
        {!! str_replace('src="', 'src="'.asset('images'), $items->content2) !!}
      </div>

      <div class="show-820">
        <!-- 모바일 content 영역 -->
        {!! str_replace('src="', 'src="'.asset('images'), $items->content3) !!}
      </div>

      <div>
        <!-- content 4 영역 -->
        {!! str_replace('src="', 'src="'.asset('images'), $items->content4) !!}
      </div>

      <div>
        <!-- content 5 영역 -->
        {!! str_replace('src="', 'src="'.asset('images'), $items->content5) !!}
      </div>
    </article>

  </div>





</div>

@endsection