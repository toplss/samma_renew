	<!-- 헤더 슬라이드 패널 -->
	@if (request()->is('mall/shop/list') && request()->get('type') !== 'low_temp') <!-- 전체상품 -->
    @php
    $all_on = $_REQUEST['ca_id'] == '20' && !request()->has('page') ? 'on' : '';
    @endphp
		<div class="nav-slide-panel {{ $all_on }}">
			<div class="panel-inner" id="pi1">
				<div class="_all">
					@foreach($activeMenuBanner['all_menu_sub_category']['PC'] as $key => $row)
						{!! $row !!}
					@endforeach
				</div>
				<div class="_right">
					{!! $activeMenuBanner['all'] !!}
				</div>
			</div>
		</div>
	@elseif (request()->is('mall/shop/list_gubun_chain')) <!-- 체인점전용상품 -->
		<div class="nav-slide-panel {{ request()->path() === 'mall/shop/list_gubun_chain' && empty(request()->query()) ? 'on' : '' }}">
			<div class="panel-inner" id="pi2">
				<div class="_chain">
					@foreach($activeMenuBanner['chain_menu'] as $key => $row)
						{!! $row !!}
					@endforeach
				</div>
				<div class="_right">
					{!! $activeMenuBanner['all'] !!}
				</div>
			</div>
		</div>
	@elseif (request()->is('mall/shop/list_gubun_recipe')) <!-- 레시피보기 -->
		<div class="nav-slide-panel {{ request()->path() === 'mall/shop/list_gubun_recipe' && empty(request()->query()) ? 'on' : '' }}">
			<div class="panel-inner" id="pi3">
				<div class="_recipe">
            @foreach ($activeMenuBanner['recipe_menu'] as $key => $row)
              {!! $row !!}
            @endforeach
				</div>
				<div class="_right">
					{!! $activeMenuBanner['all'] !!}
				</div>
			</div>
		</div>
    <img class="panel-close-btn" src="{{ asset('images/icon/circle-x.svg') }}" alt="">
    
	@endif

	@php
		$type = request()->get('type');
	@endphp

  @switch($type)
    @case('low_temp') <!-- 저온상품 -->
      <div class="nav-slide-panel {{ request('type') === 'low_temp' && request('ca_id') == 70 && !request()->has('page') ? 'on' : '' }}">
        <div class="panel-inner" id="pi4">
          <div class="_lowtemp">
            {!! $activeMenuBanner['renew_menu_low_temp'] !!}
          </div>
          <div class="_right">
            {!! $activeMenuBanner['all'] !!}
          </div>
        </div>
      </div>
    @break

    @case('vivacook') <!-- 비바쿡 -->
      @php
      $ca_id = $_REQUEST['ca_id'] ?? '';
      @endphp
      <div class="nav-slide-panel {{ request()->is('mall/shop/list_gubun') && !$ca_id ? 'on' : '' }}">
        <div class="panel-inner" id="pi5">
          <div class="_vivacook">
            {!! $activeMenuBanner['renew_menu_vivacook'] !!}
          </div>
        </div>
      </div>
      @break		

    @case('mygrang') <!-- 마이그랑 -->
      @php
      $ca_id = $_REQUEST['ca_id'] ?? '';
      @endphp
      <div class="nav-slide-panel {{ request()->is('mall/shop/list_gubun') && !$ca_id ? 'on' : '' }}">
        <div class="panel-inner" id="pi6">
          <div class="_mygrang">
            {!! $activeMenuBanner['renew_menu_mygrang'] !!}
          </div>
        </div>
      </div>
    @break

		@case('best') <!-- 베스트 -->
      @php
      $ca_id = $_REQUEST['ca_id'] ?? '';
      @endphp
      <div class="nav-slide-panel {{ request()->is('mall/shop/list_gubun') && !$ca_id ? 'on' : '' }}">
        <div class="panel-inner" id="pi7">
          <div class="_best">
            {!! $activeMenuBanner['renew_menu_best'] !!}
          </div>
          <div class="_right">
            {!! $activeMenuBanner['all'] !!}
          </div>
        </div>
      </div>
    @break

		@case('new') <!-- 신상품 -->
      @php
        $ca_id = $_REQUEST['ca_id'] ?? '';
      @endphp
      <div class="nav-slide-panel {{ request()->is('mall/shop/list_gubun') && !$ca_id ? 'on' : '' }}">
        <div class="panel-inner" id="pi8">
          <div class="_new">
            {!! $activeMenuBanner['renew_menu_new'] !!}
          </div>
          <div class="_right">
            {!! $activeMenuBanner['all'] !!}
          </div>
        </div>
      </div>
    @break

		@case('sale') <!-- 이달의행사 -->
      @php
        $ca_id = $_REQUEST['ca_id'] ?? '';
      @endphp
      <div class="nav-slide-panel {{ request()->is('mall/shop/list_gubun') && !$ca_id ? 'on' : '' }}">
        <div class="panel-inner" id="pi9">
          <div class="_sale">
            {!! $activeMenuBanner['renew_menu_event'] !!}
          </div>
          <div class="_right">
            {!! $activeMenuBanner['all'] !!}
          </div>
        </div>
      </div>
    @break
	@endswitch
	<!-- 헤더 슬라이드 패널 끝 -->