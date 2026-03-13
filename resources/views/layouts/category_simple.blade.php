@if ($activeCategory)
<div class="st-left">
    @if(!request('skeyword'))
    <div class="category">
        <button type="button"class="btn3" id="cateBtn">전체<img src="{{ asset('images/icon/down.svg') }}"></button>
        <ul id="cateList">
            <li><a href="?ca_id=20"><span class="rank_ico_img"><img src="https://samma-erp.com/img/menu19_ico.png" alt=""></span>전체</a></li>
            @foreach($activeCategory['mobile'] as $key => $row)
            <li data-ca-id="{{ $row['ca_id'] }}">
                <a href="?ca_id={{ $row['ca_id'] }}">
                    <span class="rank_ico_img">
                        <img src="https://samma-erp.com/mall/data/category/{{ $row['ca_icon1'] }}" alt="">
                    </span>
                    {{ $row['ca_name'] }}
                </a>
            </li>
            @endforeach
        </ul>
    </div>
    <button type="button" class="btn3" onclick="choiceItem()">선택한 상품담기</button>
</div>



<ul class="st-right">
    <li>
        <select name="desc" id="desc" onchange="ShopItemOrderByDesc(this.value)">
            <option value="1">정렬</option>
            <option value="3" {{ request('desc') == '3' ? 'selected' : '' }} >MY 구매높은순</option>
            <option value="4" {{ request('desc') == '5' ? 'selected' : '' }} >MY 최신주문순</option>
            <option value="5" {{ request('desc') == '6' ? 'selected' : '' }} >MY 카테고리순</option>
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
@endif
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
</script>


<script>
function choiceItem() {
    var checked = $('.cart_it_id:checked');

    if (checked.length == 0) {
        Swal.fire({
            toast: true,
            icon: 'error',
            title: '선택된 상품이 존재하지 않습니다.',
            confirmButtonText: '확인'
        });
    } else {
        Swal.fire({
            title: '구매 확인',
            text: `상품을(를) 구매하시겠습니까?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: '구매',
            cancelButtonText: '취소'
        }).then((result) => {
            if (result.isConfirmed) {
                $('.cart_it_id:checked').each(function() {

                    var ul = $(this).closest('ul');
                    var it_id = ul.data('item');
                    var it_name = ul.find('.prd-name').text();
                    var qty = ul.find('.ct_qty').val();

                    $.post('/mall/proc_query_cart', {
                        mode: 'cart_insert',
                        it_id: it_id,
                        ct_qty: qty
                    }, function(res) {
                        basket_count();
                        cart_res(res); 
                    }, 'json');
                });
            }
        });
    }
}
</script>
@endif