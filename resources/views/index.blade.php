@extends('layouts.header')

@section('content')

<main>
  <!-- 메인 상단 배너 renew_main_top -->
  <div class="main-full-slide">
     {!! $main_top !!}
  </div>
  <!-- 메인 상단 배너 끝 -->

  <!-- 메인 상단 배너 (모바일) renew_m_main_top -->
  <div class="m-main-full-slide">
    {!! $m_main_top !!}
  </div>
  <!-- 메인 상단 배너 (모바일) 끝 -->
</main>

<div class="main-wrap">
  
  <div class="main-container" id="mc8">
    <ul class="sale-item-tab">
      <li class="active" data-tab="si1">초특가상품</li>
      <li data-tab="si2">비바쿡 할인상품</li>
      <li data-tab="si3">마이그랑 할인상품</li>
    </ul>

    <!-- 초특가상품 -->
    @php 
      //초특가상품 순서 램덤
      shuffle($MainSlideSuperSale_product);

      // $MainSlideSuperSale_product = [];
    @endphp
    @if(count($MainSlideSuperSale_product) > 0)
      <div class="sale-item-slide active" id="si1">
        <!-- 반복 -->
        @foreach($MainSlideSuperSale_product as $key => $row)
        <x-main.popular-items  
          :row="$row"
          :member="$activeMember"
        />
        @endforeach
        <!-- 반복 끝 -->
      </div>
    @else
      <div class="no-item" id="si1">
        <img src="{{ asset('images/icon/box.svg') }}">
        <p>상품이 존재하지 않습니다</p>
      </div>
    @endif


    <!-- 비바쿡 할인상품 -->
    @php 
      //비바쿡 할인상품 순서 램덤
      shuffle($MainSlideVivacook_product);

      // $MainSlideVivacook_product = [];
    @endphp    
    @if(count($MainSlideVivacook_product) > 0)
      <div class="sale-item-slide" id="si2">
        <!-- 반복 -->
        @foreach($MainSlideVivacook_product as $key => $row)
          <x-main.vivacook-sale
            :row="$row"
            :member="$activeMember"
          />
        @endforeach
      <!-- 반복 끝 -->
      </div>
    @else
      <div class="no-item" id="si2">
        <img src="{{ asset('images/icon/box.svg') }}">
        <p>상품이 존재하지 않습니다</p>
      </div>
    @endif

    <!-- 마이그랑 할인상품 -->
    @php 
      //마이그랑 할인상품 순서 램덤
      shuffle($MainSlideMygrang_product);

      // $MainSlideMygrang_product = [];
    @endphp        
    @if(count($MainSlideMygrang_product) > 0)
      <div class="sale-item-slide" id="si3">
        <!-- 반복 -->
        @foreach($MainSlideMygrang_product as $key => $row)
          <x-main.mygrang-sale
            :row="$row"
            :member="$activeMember"
          />
        @endforeach
        <!-- 반복 끝 -->
      </div>
    @else
      <div class="no-item" id="si3">
        <img src="{{ asset('images/icon/box.svg') }}">
        <p>상품이 존재하지 않습니다</p>
      </div>
    @endif

  </div>
  
  <div class="main-container" id="mc9">
    <ul class="sale-item-tab">
      <li class="active" data-tab="si4">지난주 베스트</li>
      <li data-tab="si5">이번주 추천상품</li>
      <li data-tab="si6">출시 예정 상품</li>
    </ul>

    <!-- 지난주 베스트 -->
    @if(count($MainSlideBest_product) > 0)
      <div class="sale-item-slide active" id="si4">
        <!-- 반복 -->
        @foreach($MainSlideBest_product as $key => $row)
        <x-main.best-items  
          :row="$row"
          :member="$activeMember"
        />
        @endforeach
        <!-- 반복 끝 -->
      </div>
    @else
      <div class="no-item" id="si4">
        <img src="{{ asset('images/icon/box.svg') }}">
        <p>상품이 존재하지 않습니다</p>
      </div>
    @endif


    <!-- 이번주 추천상품 -->
    @if(count($MainSlideNew_product) > 0)
      <div class="sale-item-slide" id="si5">
        <!-- 반복 -->
        @foreach($MainSlideNew_product as $key => $row)
        <x-main.slide-new-product 
          :row="$row"
          :member="$activeMember"
        />
        @endforeach
        <!-- 반복 끝 -->
      </div>
    @else
      <div class="no-item" id="si5">
        <img src="{{ asset('images/icon/box.svg') }}">
        <p>상품이 존재하지 않습니다</p>
      </div>
    @endif


    <!-- 출시 예정 상품 -->
    <!-- 상품이 없을 때 -->
    <div class="no-item" id="si6">
      <img src="{{ asset('images/icon/box.svg') }}">
      <p>상품이 존재하지 않습니다</p>
    </div>

  </div>
  
<!-- 메인 중간 배너 renew_main_middle -->
  <div class="main-container" id="mc10">
    <div class="event-banner-wrap">

      {{-- <a href=""><img src="https://samma-erp.com/common_data/banner/4ee8e7da9a60b7734a0511745cfba5bc.jpg" alt=""></a>
      <a href=""><img src="https://samma-erp.com/common_data/banner/7a42f84057b32d7c70bab44f0ca917c4.jpg" alt=""></a>
      <a href=""><img src="https://samma-erp.com/common_data/banner/b708b06e0e986ed560efc29191407b00.jpg" alt=""></a>
      <a href=""><img src="https://samma-erp.com/common_data/banner/4ee8e7da9a60b7734a0511745cfba5bc.jpg" alt=""></a>
      <a href=""><img src="https://samma-erp.com/common_data/banner/7a42f84057b32d7c70bab44f0ca917c4.jpg" alt=""></a>
      <a href=""><img src="https://samma-erp.com/common_data/banner/b708b06e0e986ed560efc29191407b00.jpg" alt=""></a> --}}

      {!! $main_middle !!}
    </div>
  </div>
<!-- 메인 중간 배너 끝 -->

  <div class="main-container" id="mc1">
    <h2>자주 주문한 상품<small onclick="location.href = '/mall/shop/list_gubun_simple';" style="cursor: pointer;">더보기+</small></h2>
    <ul>
      @foreach($rank_order as $key => $row)
      <li><a href="/mall/shop/view?it_id={{ $row['it_id'] }}"><b>{{ $key + 1 }}</b>{{ $row['it_name'] }}</a></li>
      @endforeach
    </ul>
  </div>

  <!-- 메인 중앙 배너 renew_main_middle_center -->
  <div class="main-container" id="mc2">
    {!! $main_center !!}
  </div>
  <!-- 메인 중앙 배너 끝 -->

  <div class="main-container" id="mc3">
    <h2>공지사항<small onclick="location.href = '/common_board/list?bbs_code=notice';" style="cursor: pointer;">더보기+</small></h2>
    <ul>
{{-- @dd($notice); --}}

      @foreach($notice as $key => $row)
      <li>
        <a href="/common_board/view?bbs_code=notice&bd_num={!! $row['bd_num'] !!}">
          <span style="color:{!! $row['bd_ext1'] !!}">
            {{ $row['bd_subject'] }}
          </span>
          @if(\Carbon\Carbon::parse($row['write_time'])->diffInHours(now()) < 24)
            <b class="new-icon"><img src="/images/icon/ico_new.png"></b>
          @endif  
        </a>
      </li>
      @endforeach
    </ul>

  </div>

  <div class="main-container" id="mc4">
    <!-- 유튜브 동영상 연결 -->
     <figure class="youtube">

		</figure>
  </div>

  <div class="main-container" id="mc5">
    <h2>베스트 리뷰<small onclick="vivacook_review()" style="cursor: pointer;">더보기+</small></h2>
    <!-- 메인 리뷰 슬라이드 -->    
    <div class="review-slide">
      @foreach($vivacook as $key => $row)
      <div>
        <img src="{{ $row['file'] }}">
        <h6>{{ $row['wr_subject'] }}</h6>
        <p>{{ $row['wr_content'] }}</p>
      </div>
      @endforeach
    </div>
    <!-- 메인 리뷰 슬라이드 끝 -->

  </div>

  <!-- 레시피 -->
  @if(count($recipe) > 0)
  @php
	$recipe_opt_auth = [
    '10', '20', '30', '40', '50', '60',
    '70', '80', '90', 'a0', 'b0', 'c0',
    'd0', 'e0', 'f0', 'g0'
  ];

  $view_auth = false;
  @endphp

  <div class="main-container" id="mc6">
    <h2>
      추천 레시피 
      @if(substr($activeMember['mb_level'],0,2) >= '70' && substr($activeMember['mb_level'],0,2) <= '90' || in_array($activeMember['mb_launching'], $recipe_opt_auth))
        @php 
          $view_auth = true; 
        @endphp
        <small onclick="location.href = '/mall/shop/list_gubun_recipe';" style="cursor: pointer;">더보기+</small>
      @endif
    </h2>

    <div class="recipe-slide">
      @foreach($recipe as $key => $row)
        @if ($view_auth)
        <a href="/mall/shop/list_gubun_recipe_view?p_id={{ $row['p_id'] }}">
        @else
        <a href="#" onclick="alert('레시피보기 권한이 없습니다.');">
        @endif
          <img src="https://samma-erp.com/common_data/recipe/{{ $row['img1'] }}"><p>{{ $row['title'] }}</p>
        </a>
      @endforeach
    </div>

  </div>
  @endif
  <!-- 레시피 -->

  <!-- 메인 하단 배너 renew_main_bottom -->
  <div class="main-container" id="mc7">
    @foreach($main_bottom['images'] as $key => $row)
      @php
      $image_url = 'https://samma-erp.com' . $main_bottom['images'][$key];
      $headers = @get_headers($image_url);

      $images_visiable = false;
      if ($headers && strpos($headers[0], '200') !== false) {
          $images_visiable = true;
      } else {
          $images_visiable = false;
      }
      @endphp

      @if ($images_visiable)
      <a href="{{ $main_bottom['urlLink'][$key] }}"><img src="{!! $image_url !!}"></a>
      @endif
    @endforeach
  </div>
  <!-- 메인 하단 배너 끝 -->
  
</div>

<!-- 팝업 -->
<div class="main-popup" style="display:none;">
  <div class="m-pop-content">
    <div class="m-pop-slide">
      @foreach($activeWins['pc'] as $key => $row)
      @php
      $image_url = 'https://samma-erp.com/common_data/new_win/' . $row['img1'];
      $headers = @get_headers($image_url);

      $images_visiable = false;
      if ($headers && strpos($headers[0], '200') !== false) {
          $images_visiable = true;
      } else {
          $images_visiable = false;
      }
      @endphp

      @if ($images_visiable)
        <a href="{{ $row['url1'] }}"><img src="{{ $image_url }}"></a>
      @endif
      @endforeach
    </div>
    <div class="m-pop-btn">
      <label for="main_popup"><input type="checkbox" name="" id="main_popup"  value="Y">오늘 하루 보지 않기</label>
      <button type="button" class="close-btn main_popup" >닫기</button>
    </div>
  </div>
</div>



<script type="text/javascript">
// 유튜브 호출
$(document).ready(function() {
	const youtubeInstance = new YoutubeManager('main');
	youtubeInstance.execute();
});


function vivacook_review() {
  window.open('https://vivacook.kr/bbs/board.php?bo_table=snsinstagram', '_blank');
}
</script>

@endsection


