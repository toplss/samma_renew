@if ($category)
<div class="st-left recipe">
    <div class="category">
        <button type="button"class="btn3" id="cateBtn">레시피</button>
    </div>


    <ul class="cate-details">
        @foreach($category as $key => $row)
            <li>
                <a href="?opt={{ substr($row['category']['ca_id'], 0, 2) }}&opt2={{ $row['category']['ca_id'] }}" class="{{ request('opt2') == $row['category']['ca_id'] ? 'on' : '' }}">{{ $row['category']['ca_name'] }}</a>
                @if (request('opt2') == $row['category']['ca_id'])
                <ul class="cate-r-ul">
                    @foreach($row['category']['sub_category'] as $ckey => $crow)
                    <li>
                        <a href="?opt={{ substr($row['category']['ca_id'], 0, 2) }}&opt2={{ $row['category']['ca_id'] }}&opt3={{ $crow['ca_id'] }}">
                            {{ $crow['ca_name'] }}({{ $crow['cnt'] ?? 0 }})
                        </a>
                    </li>
                    @endforeach
                </ul>
                @endif
            </li>
        @endforeach
    </ul>

    
    <!-- 모바일일때 -->
    <div class="m-cate1">
        <button type="button" class="btn3" id="mcateBtn1">카테고리<img src="{{ asset('images/icon/down.svg') }}"></button>
        <ul class="cate-details2">
            @foreach($category as $key => $row)
            <li data-ca-id={{ $row['category']['ca_id'] }}><a href="?opt={{ substr($row['category']['ca_id'], 0, 2) }}&opt2={{ $row['category']['ca_id'] }}">{{ $row['category']['ca_name'] }}</a></li>
            @endforeach
        </ul>
    </div>
    <div class="m-cate2">
        <button type="button" class="btn2" id="mcateBtn2">세부<img src="{{ asset('images/icon/down.svg') }}"></button>
        <ul class="cate-r-ul2">
            @foreach($sub_category as $key => $row)
            @php
            if(request('opt2') !== substr($row['ca_id'], 0, 4)) continue;
            @endphp
            <li data-ca-id={{ $row['ca_id'] }}><a href="?opt={{ substr($row['ca_id'], 0, 2) }}&opt2={{ substr($row['ca_id'], 0, 4) }}&&opt3={{ $row['ca_id'] }}">{{ $row['ca_name'] }}({{ $row['cnt'] ?? 0 }})</a></li>
            @endforeach
        </ul>
    </div>

    <div class="recipe-search">
        <button type="button" class="btn3 hide-820" onclick="choiceRecipe()">선택한 레시피 프린트하기</button>
        <form id="recipe_search_frm" action="">
            <input type="hidden" name="opt" value="20" />
            <input type="search" id="recipe_skeyword" name="recipe_skeyword" value="{{ request('recipe_skeyword') }}" placeholder="검색어를 입력해 주세요">
            <img src="{{ asset('images/icon/search.svg') }}" alt="" onclick="searchKeywordRecipe()">
        </form>
    </div>
</div>

<ul class="st-right">
    <li>
        <select name="desc" id="desc" onchange="ShopItemOrderByDesc(this.value)">
            <option value="1">정렬</option>
            <option value="2" {{ request('desc') == '2' ? 'selected' : '' }}>등록순</option>
            <option value="3" {{ request('desc') == '3' ? 'selected' : '' }}>조회순</option>
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
function choiceRecipe() {
    var checked = $('.print_chk_nums:checked');

    if (checked.length == 0) {
        Swal.fire({
            toast: true,
            icon: 'error',
            title: '선택된 레시피가 존재하지 않습니다.',
            confirmButtonText: '확인'
        });
    } else {
        var print_select_idx = '';
        $('.print_chk_nums:checked').each(function() {
            print_select_idx += $(this).val()+ ",";
        });

        print_select_idx = print_select_idx.slice(0, -1);

        var url = new URLSearchParams();
        
        url.append('print_select_idx', print_select_idx);

        window.open('/mall/shop/list_gubun_recipe_pop?'+decodeURIComponent(url.toString()),'레시피 프린트','width=900,height=700')
    }
    
}


$(document).ready(function() {
    var url_params = new URLSearchParams(window.location.search);
    var ca_id = url_params.get('opt2') || '';
    var ca_id2 = url_params.get('opt3') || '';

    if (ca_id) {
        var targetLi = $('.cate-details2 li[data-ca-id="' + ca_id + '"]');
        
        if (targetLi.length > 0) {
            var text = targetLi.find('a').text().trim();

            $('#mcateBtn1').contents().filter(function() {
                return this.nodeType === 3; // 텍스트 노드만 선택
            }).first().replaceWith(text);
        }
    }


    if (ca_id2) {
        var targetLi = $('.cate-r-ul2 li[data-ca-id="' + ca_id2 + '"]');
        
        if (targetLi.length > 0) {
            var text = targetLi.find('a').text().trim();

            $('#mcateBtn2').contents().filter(function() {
                return this.nodeType === 3; // 텍스트 노드만 선택
            }).first().replaceWith(text);
        }
    }
})
</script>

<script>
function searchKeywordRecipe() {
	var frm = $('#recipe_search_frm')[0];
	if (!$('#recipe_skeyword').val().trim()) {
		swal.fire({
			toast: true,
      icon: 'error',
      title: '입력 오류',
      html: '검색 키워드를 입력하세요',
      confirmButtonText: '확인'
		});
		return;
	}
	frm.submit();
}
// 엔터 막기
$('#recipe_search_frm').on('submit', function(e) {
	if (!$('#recipe_skeyword').val().trim()) {
		e.preventDefault();
		searchKeywordRecipe();
	}
});
</script>
@endif