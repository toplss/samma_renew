@extends('layouts.header')

@section('content')

<div class="sub-container {{ request()->path() === 'mall/shop/list_gubun_recipe' && empty(request()->query()) ? 'down' : '' }}">

    <!-- 카테고리 영역 -->
    <div class="sub-title-wrap">
        @include('layouts.category_recipe')
    </div>
    <!-- 카테고리 영역 -->

    <!-- 레시피 배너 영역 -->
    <div class="recipe-top-banner">
      <div class="hide-820">
        {!! $activeSubBanner['pc'] !!}
      </div>
      <div class="show-820">
        {!! $activeSubBanner['mobile'] !!}
      </div>
    </div>
    
    <div class="recipe-list">
      @foreach ($items as $key => $row)
      @php
      $p_id      = $row['p_id'];
      $image_url = 'images/recipe/'.$row['img1'];
      $tags      = explode(',', $row['tag']);
      @endphp
      <ul>
      @if(file_exists(public_path($image_url)) && $row['img1'])
        <li class="recipe-img" onclick="location.href='/mall/shop/list_gubun_recipe_view?p_id={{ $p_id }}';"><img src="{{ asset($image_url) }}" alt=""></li>
      @else
        <li class="recipe-img" onclick="location.href='/mall/shop/list_gubun_recipe_view?p_id={{ $p_id }}';"><img src="{{ asset('images/common/no_image.gif') }}" alt=""></li>
      @endif
        <li class="recipe-name">{{ $row['title'] }}</li>
        <li class="recipe-pin">
          @foreach ($tags as $key => $ta)
          @php
          $idx = $key + 1;
          $img = $row['chios_img'.$idx];
          @endphp
          <span onclick="location.href='/donwload?path=recipe&file_name={{ $img }}'">{{ $ta }}</span>
          @endforeach
        </li>
        <li class="recipe-btn"><input type="checkbox" name="print_chk_nums[]" class="print_chk_nums" value="{{ $row['p_id'] }}">
          <button type="button" class="btn2" onclick="window.open('/mall/shop/list_gubun_recipe_pop?print_select_idx={{ $row['p_id'] }}','레시피 프린트','width=900,height=700');">레시피보기</button>
        </li>
      </ul>
      @endforeach
    </div>
    
    {{ $items->links() }}
</div>

@endsection


