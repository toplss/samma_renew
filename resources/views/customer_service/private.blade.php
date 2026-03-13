@extends('layouts.header')

@section('title', '개인정보보호방침 페이지')

@section('content')

<div class="sub-container">
  <div class="sub-title-wrap">
    <h4>개인정보보호방침</h4>  
  </div>

  <div class="article-wrap">
    <article>
      <!-- pc content 영역 -->
      {!! $items->content !!}
      {!! $items->content2 !!}
      {!! $items->content3 !!}
      {!! $items->content4 !!}
      {!! $items->content5 !!}
    </article>

  </div>





</div>

@endsection