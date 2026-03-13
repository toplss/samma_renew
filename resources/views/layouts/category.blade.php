@if ($activeCategory)
<div class="st-left">
    @if(!request('skeyword'))
    <div class="category">
        <button type="button" class="btn3" id="cateBtn">전체<img src="{{ asset('images/icon/down.svg') }}"></button>
        <ul id="cateList">
            <li><a href="?ca_id=20{{ request('type') ? '&type='.request('type') : '' }}"><span class="rank_ico_img"><img src="https://samma-erp.com/img/menu19_ico.png" alt=""></span>전체</a></li>
            @foreach($activeCategory['mobile'] as $key => $row)
            <li data-ca-id="{{ $row['ca_id'] }}">
                @if (request('type'))
                <a href="?ca_id={{ $row['ca_id'] }}&type={{ request('type') }}">
                @else
                <a href="?ca_id={{ $row['ca_id'] }}">
                @endif
                    <span class="rank_ico_img">
                        <img src="https://samma-erp.com/mall/data/category/{{ $row['ca_icon1'] }}" alt="">
                    </span>
                    {{ $row['ca_name'] }}
                </a>
            </li>
            @endforeach
        </ul>
    </div>

    <!-- pc일 때 -->
    <ul class="cate-details">
        @foreach($activeCategory['left'] as $key => $row)
            @if ($row['ca_id'] == mb_substr(request('ca_id'), 0, 2) || $row['ca_id'] == mb_substr(request('category'), 0, 2))
                @if(isset($row['sub_category']))
                    @foreach($row['sub_category'] as $cKey => $cRow)
                        @php
                            $isActive = request('ca_id') == $cRow['ca_id'];
                        @endphp
                        @if (request('type'))
                            <li><a href="?ca_id={{ $cRow['ca_id'] ?? '' }}&type={{ request('type') }}" class="{{ $isActive ? 'on' : '' }}">{{ $cRow['ca_name'] ?? '' }}</a></li>
                        @else
                            <li><a href="?ca_id={{ $cRow['ca_id'] ?? '' }}&type={{ request('type') }}" class="{{ $isActive ? 'on' : '' }}">{{ $cRow['ca_name'] ?? '' }}</a></li>
                        @endif
                    @endforeach
                @endif
            @endif
        @endforeach
    </ul>

    <!-- 모바일일때 -->
    <div class="m-cate1">
        <button type="button" class="btn3" id="mcateBtn1">카테고리<img src="{{ asset('images/icon/down.svg') }}"></button>
        <ul class="cate-details2">
            @foreach($activeCategory['mobile'] as $key => $row)
                @if ($row['ca_id'] == mb_substr(request('ca_id'), 0, 2) || $row['ca_id'] == mb_substr(request('category'), 0, 2))
                    @if(isset($row['sub_category']))
                        @foreach($row['sub_category'] as $cKey => $cRow)
                        <li data-ca-id="{{ $cRow['ca_id'] ?? '' }}"><a href="?ca_id={{ $cRow['ca_id'] ?? '' }}&type={{ request('type') }}">{{ $cRow['ca_name'] ?? '' }}</a></li>
                        @endforeach
                    @endif
                @endif
            @endforeach
        </ul>
    </div>

    
    @else 
    <div>
        <b>{{ request('skeyword') }}</b> 에 대한 검색결과 총 ({{ $items->total() }}) 개의 상품이 있습니다.
    </div>
    
    @endif
</div>
<ul class="st-right">
    <li>
        <select name="desc" id="desc" onchange="ShopItemOrderByDesc(this.value)">
            <option value="1">정렬</option>
            <option value="2" {{ request('desc') == '2' ? 'selected' : '' }} >낮은가격순</option>
            <option value="3" {{ request('desc') == '3' ? 'selected' : '' }} >높은가격순</option>
            <option value="4" {{ request('desc') == '4' ? 'selected' : '' }} >판매량순</option>
            <option value="5" {{ request('desc') == '5' ? 'selected' : '' }} >최신순</option>
        </select>
    </li>
    <li>
        <select name="scale" id="scale" onchange="ShopItemOrderByScale(this.value)">
            <option value="60" {{ request('scale') == 60 ? 'selected' : '' }}>리스트 60개</option>
            <option value="120" {{ request('scale') == 120 ? 'selected' : '' }}>리스트 120개</option>
            <option value="180" {{ request('scale') == 180 ? 'selected' : '' }}>리스트 180개</option>
            <option value="240" {{ request('scale') == 240 ? 'selected' : '' }}>리스트 240개</option>
            <option value="300" {{ request('scale') == 300 ? 'selected' : '' }}>리스트 300개</option>
        </select>
    </li>
    <li id="gridType"><img src="{{ asset('images/icon/grid.svg') }}"></li>
    <li id="rowType"><img src="{{ asset('images/icon/list.svg') }}"></li>
</ul>

<script>
function ShopItemOrderByScale(e) {
    var params = new URLSearchParams(window.location.search);

    params.set('scale', e);

    location.search = params.toString();
}

function ShopItemOrderByDesc(e) {
    var params = new URLSearchParams(window.location.search);

    params.set('desc', e);

    location.search = params.toString();
}




$(document).ready(function() {
    var url_params = new URLSearchParams(window.location.search);
    var ca_id = url_params.get('ca_id') || '';

    if (ca_id) {
        var targetLi = $('.cate-details2 li[data-ca-id="' + ca_id + '"]');
        
        if (targetLi.length > 0) {
            var text = targetLi.find('a').text().trim();

            $('#mcateBtn1').contents().filter(function() {
                return this.nodeType === 3; // 텍스트 노드만 선택
            }).first().replaceWith(text);
        }
    }

})
</script>
@endif