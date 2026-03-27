@extends('layouts.header')

@section('content')

<div class="sub-container">
  <div class="sub-title-wrap">
    <h4>장바구니</h4>  
  </div>
  
  @include('layouts.mypage_top')

  <div class="cart-wrap">
    <table class="cart-table">
      <thead>
        <tr>
          <th>
            <input type="checkbox" name="ct_chkall" value="1" id="ct_chkall">
          </th>
          <th class="hide-820">번호</th>
          <th>상품명</th>
          <th class="hide-820">유형</th>
          <th class="hide-820">판매가</th>
          <th class="hide-820">수량</th>
          <th>소계</th>
          <th></th>
        </tr>
      </thead>
      <tbody class="my_cart_tbody">

        @foreach ($items['cartAllItems'] as $key => $row)
        @php
        $image_url = 'images/item/'.$row['it_img1'];

        $min_cart_ct_qty = $box_min_qty = $max_cart_ct_qty = 0;

        if(isset($activeMember['mb_level']) && substr($activeMember['mb_level'], 0, 2) == '30' && $row['agency_it_buy_min_qty'] > 0 || $activeMember['mb_branch_gubun_type'] == '3') {
            $min_cart_ct_qty = $row['agency_it_buy_min_qty']; // 주문최소
            $box_min_qty     = ($row['it_gubun'] == 'pack') ? $row['it_box_sale_pack'] : $row['it_box_sale_tot']; // 박스구매
            $max_cart_ct_qty = $row['agency_it_buy_max_qty']; // 최대치 구매

        } else {
            $min_cart_ct_qty = $row['it_buy_min_qty'];
            $box_min_qty     = ($row['it_gubun'] == 'pack') ? $row['it_box_sale_pack'] : $row['it_box_sale_tot']; // 박스구매
            $max_cart_ct_qty = $row['it_buy_max_qty']; // 최대치 구매
        }
        @endphp
        <tr data-it_id="{{ $row['it_id'] }}">
          <td class="cart-table-check">
            <label for="ct_chk_0">
              <input type="checkbox" name="ct_chk[]" value="{{ $row['it_id'] }}" class="ct_chk">
            </label>
          </td>
          <td class="cart-table-num hide-820">{{ ++$key }}</td>

          @php
          $timpArr = ['1' => 'room_temp', '3' => 'low_temp', '2' => 'frozen_temp', '4' => ''];
          $it_id   = $row['it_id'];

          //상품이벤트 라벨
          $item_label = ['초특가' => 'bargain', '이달의행사' => 'event', '신상품' => '_new', '베스트' => 'best', '비바쿡' => 'vivacook', '마이그랑' => 'mygrang'];
          $arr_item_label = [];
          if($row['it_type_label']){
            $arr_item_label = explode('#', $row['it_type_label']);
          }

          @endphp

          @php
          if ($row['it_price_piece_use']) {
              $it_price_piece = $row[$activeMember['field_it_price_unit']];
          } else {
              $it_price_piece = 0;
          }
          @endphp

          <td class="cart-table-title">
            <input type="hidden" class="it_gubun" value="{{ $row['it_gubun'] }}" />
            <input type="hidden" class="it_box_sale_pcs" value="{{ $row['it_box_sale_pcs'] }}" />
            <input type="hidden" class="it_box_sale_pack" value="{{ $row['it_box_sale_pack'] }}" />
            <input type="hidden" class="it_box_sale_tot" value="{{ $row['it_box_sale_tot'] }}" />
            <input type="hidden" class="it_price_piece" value="{{ $it_price_piece }}" />

            <div>
              @if(file_exists(public_path($image_url)) && $row['it_img1'])
              <img src="{{ asset($image_url) }}">
              @else 
              <img src="{{ asset('images/common/no_image.gif') }}">
              @endif
              <h6 class="my_cart_it_name">
                <span class="my_cart_it_name_short">{{ $row['it_name'] }}</span>
                <i>({{ $row['it_basic'] }})
                  @if($it_price_piece > 0 && $row['it_price_piece_use'])
                  <s>/ 개당 {{ number_format($it_price_piece) }}원</s>
                  @endif
                </i>

                <!-- 상품이벤트 라벨 -->
                @foreach($arr_item_label as $key => $label)
                  @if($label)<span class="hide-820 {{ $item_label[$label] }}">{{ $label }}</span>@endif
                @endforeach

                <p class="show-820">
                  <span class="{{ $timpArr[$row['it_storage']] }}">{{ $row['it_storage_label'] }}</span>
                  @if($row['it_return_use'] == '1')
                  <span class="{{ $row['it_return_label'] == '반품가능' ? 'return_o' : 'return_x' }}">{{ $row['it_return_label'] }}</span>
                  @endif
                </p> 
              </h6>           
            </div>  
          </td>

          <td class="cart-table-type hide-820">
            <span class="{{ $timpArr[$row['it_storage']] }}">{{ $row['it_storage_label'] }}</span>
            @if($row['it_return_use'] == '1')
            <span class="{{ $row['it_return_label'] == '반품가능' ? 'return_o' : 'return_x' }}">{{ $row['it_return_label'] }}</span>
            @endif
          </td>

          <td class="cart-table-price hide-820">{{ number_format($row['ct_price']) }}원</td>
          <!-- 수량 (모바일 O) -->
          <td class="cart-table-ea">
            {{-- <span class="show-820">{{ number_format($row['ct_price']) }}원</span> --}}
            <span class="show-820" id="mobile_pt_sales_{{ $it_id }}">
            @if($row['it_soldout'] == '1' || $row['it_force_soldout'] == '10')
            @else
              {{ number_format($row['pt_sales']) }}원
            @endif
            </span>
              @if($row['it_soldout'] == '1' || $row['it_force_soldout'] == '10')
                <div class="flex-center">
                  <span class="ct-soldout">품절</span>
                </div>
              @else                
              <div class="flex-between">
                <p class="Qua wish-num">
                <input type="hidden" class="min_ct_qty" value="{{ $min_cart_ct_qty }}" />
                <button type="button" class="sit_qty_minus cart_qty_minus" data-it_id="{{ $it_id }}">
                  <img src="{{ asset('images/icon/minus.svg') }}">
                </button> 
                
                <input type="text" name="" id="ct_qty_{{ $it_id }}" value="{{ $row['ct_qty'] }}" class="cart_ct_qty" readonly>
                
                <button type="button" class="sit_qty_plus cart_qty_plus" data-it_id="{{ $it_id }}">
                  <img src="{{ asset('images/icon/plus.svg') }}">
                </button>
                </p>

                @if($row['it_gubun'] !== 'box')
                  <input type="hidden" class="cart_buy_box_qty" value="{{ $box_min_qty }}" />
                  <button id="buy_box_btn" class="btn2" onclick="cartbuy_box_qty(this); return false;">박스구매</button>
                @endif

              @endif
            </div>
          </td>
          <!-- 소계 -->
          <td class="hide-820" id="pt_sales_{{ $it_id }}">
            @if($row['it_soldout'] == '1' || $row['it_force_soldout'] == '10')
              -
            @else
              {{ number_format($row['pt_sales']) }}원
            @endif
          </td>
          <!-- 삭제 -->
          <td>
            <a href="javascript:;" class="sit_delete_btn" data-it_id="{{ $it_id }}"><img src="{{ asset('images/icon/trash.svg') }}"></a>
          </td>
        </tr>
        @endforeach

        @if(count($items['cartAllItems']) == 0)
        <tr><td colspan="6" height="70">장바구니에 담긴 상품이 존재하지 않습니다.</td></tr>
        @endif
      </tbody>
      <tfoot>
        <tr>
          <th colspan="8">
            <div>
              <button type="button" class="btn3" onclick="checked_del()">선택삭제</button>
              <button type="button" class="btn1 all_delete_btn" >전체삭제</button>              
            </div>
          </th>
        </tr>
      </tfoot>
    </table>

    <div class="odr-cart">
      <h3>결제금액</h3>
      <ul class="odr-payment">
          <li class="total-each">
            <p>
              <span>상온제품 합계</span>
              <span><b id="_prd_type_1_price" class="reload_cart_price_1">0</b>원</span>
            </p>
            <p>
              <span>저온제품 합계</span>
              <span><b id="_prd_type_2_price" class="reload_cart_price_2">0</b>원</span>
            </p>
          </li>
          <li class="icon2">
            <img src="{{ asset('images/icon/plus.svg') }}">
          </li>
          <li class="delivery">
            <p>
              <span>배송비</span>
              <span><b id="_delivery_price" class="reload_total_send_cost">0</b>원</span>
            </p>
          </li>
          <li class="icon2"><img src="{{ asset('images/icon/equals.svg') }}"></li>
          <li class="total">
            <p><span id="_price_sum" class="reload_total_cart_price">0</span>원</p>
            <button type="button" class="btn1" onclick="common_chkform_cart('common_buy');">구매하기</button>
          </li>
      </ul>
    </div>
  </div>
</div>
<script>
$(document).ready(function() {
	$(document).on('click', '.sit_delete_btn', function(){

    Swal.fire({
        toast: false,
        title: '삭제 확인',
        text: '선택하신 상품을 삭제 하시겠습니까?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: '확인',
        cancelButtonText: '취소'
    }).then((result) => {

        if (result.isConfirmed) {

            var it_id = $(this).data('it_id');

            $.post("/mall/proc_query_cart", {
                mode: "cart_delete",
                it_id: it_id,
                path: window.location.pathname
            }, function(result){

                cart_res(result);
                setting_table(result.data);

            }, 'json');

        }

    });

  });


    $(document).on('click', '.cart_qty_plus', function(){

        var $btn = $(this);   // 클릭한 버튼 캐싱
        if ($btn.prop('disabled')) return; // 중복 클릭 방지

        $btn.prop('disabled', true);

        var it_id  = $btn.data('it_id');
        var min_ct_qty = $btn.closest('td').find('.min_ct_qty').val() * 1;
        var ct_qty = $('#ct_qty_'+it_id).val() * 1;

        if (min_ct_qty) {
          ct_qty += min_ct_qty;
        } else {
          ct_qty = 1;
        }

        $.post("/mall/proc_query_cart",{
            mode:"cart_update",
            action :'plus',
            it_id  : it_id,
            ct_qty : ct_qty,
            path : window.location.pathname
        },function(result){
            var data = JSON.parse(result);

            if (data.status == 'success') {
              cart_res(data);

  // console.log(data);

              let item = Object.values(data.data.cart_items).flat().find(v => v.it_id == it_id);
              if(item){
                  $('#pt_sales_'+it_id).html(item.pt_sales.toLocaleString()+'원');
                  $('#mobile_pt_sales_'+it_id).html(item.pt_sales.toLocaleString()+'원');
              }

              $('#ct_qty_'+it_id).val(ct_qty);
            } else {
              Swal.fire({
                toast: false,
                icon: 'warning',
                title: '알림',
                html: JSON.parse(result).message,
                confirmButtonText: '확인'
              });
            }

        }).always(function(){
            // 성공/실패 상관없이 다시 활성화
            $btn.prop('disabled', false);
        });
    });


    $(document).on('click', '.cart_qty_minus', function(){

        var $btn = $(this);
        if ($btn.prop('disabled')) return;

        $btn.prop('disabled', true);

        var it_id  = $btn.data('it_id');
        var min_ct_qty = $btn.closest('td').find('.min_ct_qty').val() * 1;
        var ct_qty = $('#ct_qty_'+it_id).val() * 1;

        if (min_ct_qty) {
          ct_qty -= min_ct_qty;
        } else {
          ct_qty -= 1;
        }

        $.post("/mall/proc_query_cart",{
            mode:"cart_update",
            action :'minus',
            it_id  : it_id,
            ct_qty : ct_qty,
            path : window.location.pathname
        },function(result){
          var data = JSON.parse(result);
          if (data.status == 'success') {
              cart_res(data);

  // console.log(data);

              let item = Object.values(data.data.cart_items).flat().find(v => v.it_id == it_id);
              if(item){
                  $('#pt_sales_'+it_id).html(item.pt_sales.toLocaleString()+'원');
                  $('#mobile_pt_sales_'+it_id).text(item.pt_sales.toLocaleString()+'원');
              }

              if ((ct_qty) >= 1) {
                  $('#ct_qty_'+it_id).val(ct_qty);
              }
            } else {
              Swal.fire({
                toast: false,
                icon: 'warning',
                title: '알림',
                html: JSON.parse(result).message,
                confirmButtonText: '확인'
              });
            }

          }).always(function(){
              $btn.prop('disabled', false);
          });
    });



    $(document).on('click', '#ct_chkall', function() {
        if ($(this).is(":checked")) {
            $(".ct_chk").prop("checked", true);
        } else {
            $(".ct_chk").prop("checked", false);
        }
    });
});


function cartbuy_box_qty(e) {
  var tr = $(e).closest('tr');
  var it_id = tr.data('it_id');
  // var it_name = tr.find('.my_cart_it_name').text();
  var it_name = tr.find('.my_cart_it_name_short').text();
  var it_gubun = tr.find('.it_gubun').val();
  var qty = tr.find('.cart_buy_box_qty').val();

  var message = '';
  if (it_gubun == 'pcs') {
    var pcs = tr.find('.it_box_sale_tot').val();
    message = `<span style="color:#e02f30">${it_name}</span><br>${pcs}입*1박스로 구매 하시겠습니까?`;
  }
  if (it_gubun == 'pack') {
    var pcs = tr.find('.it_box_sale_pcs').val();
    var pack = tr.find('.it_box_sale_pack').val();
    var total = tr.find('.it_box_sale_tot').val();
    message = `<span style="color:#e02f30">${it_name}</span><br>${pcs}입*${pack}팩*${total}개*1박스로 구매 하시겠습니까?`;
  }


  Swal.fire({
    toast: false,
    title: '구매 확인',
    // text: `${it_name}을(를) ${qty}개 구매하시겠습니까?`,
    html: message,
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: '구매',
    cancelButtonText: '취소'
  }).then((result) => {
    if (result.isConfirmed) {
      $.post('/mall/proc_query_cart', {
        mode: 'cart_insert',
        it_id: it_id,
        ct_qty: qty,
        path : window.location.pathname
      }, function(res) {
        basket_count();
        cart_res(res); 
        setting_table(res.data);
      }, 'json');
    }
  });

}


function checked_del() {
    Swal.fire({
        toast: false,
				title: '삭제 확인',
				text: `선택하신 상품을 삭제 하시겠습니까?`,
				icon: 'question',
				showCancelButton: true,
				confirmButtonText: '확인',
				cancelButtonText: '취소'
    }).then((result) => {
      if (result.isConfirmed) {
        var checkedItems = [];
        $("input[name='ct_chk[]']:checked").each(function() {
            checkedItems.push($(this).val());
        });

        if (checkedItems.length === 0) {
            alert("삭제할 상품을 선택해주세요.");
            return;
        }

        $.post("/mall/proc_query_cart",{
            mode   : "cart_selected_delete",
            it_ids : checkedItems,
            path : window.location.pathname
        },function(result){
            if (result.status == 'success') {
                cart_res(result); 
                setting_table(result.data);
            } else {
                alert(result.message);
            }
        }, 'json');

      } 
    });
}


function setting_table(data) {
  var items = JSON.parse(JSON.stringify(data));

  var item_label = {
    '초특가': 'bargain',
    '이달의행사': 'event',
    '신상품': '_new',
    '베스트': 'best',
    '비바쿡': 'vivacook',
    '마이그랑': 'mygrang'
  };

  if (Object.keys(items.cartList).length > 0) {

      let tbody_html = '';
      let img_root   = '/images/item/';
      let no_img_path = '/images/common/no_image.gif';
      const activeMember = <?= json_encode($activeMember) ?>;

      let index = 0;
      var cartList = items.cartList;

      $.each(cartList, function(i, val) {
          arr_item_label = [];
          if (val.it_type_label) {
              arr_item_label = val.it_type_label.split('#');
          }
        
          // tbody_html += '<tr>';
          tbody_html += `<tr data-it_id="${val.it_id}">`;

          index++;
          let image_url = val.it_img1 ? img_root + val.it_img1 : no_img_path;
          let min_cart_ct_qty = 0;
          let box_min_qty = 0;
          let max_cart_ct_qty = 0;
    
          if (
              activeMember &&
              activeMember.mb_level &&
              activeMember.mb_level.substring(0, 2) === '30' &&
              val.agency_it_buy_min_qty > 0
          ) {

              min_cart_ct_qty = val.agency_it_buy_min_qty; // 주문최소
              box_min_qty = (val.it_gubun === 'pack')
                  ? val.it_box_sale_pack
                  : val.it_box_sale_tot; // 박스구매
              max_cart_ct_qty = val.agency_it_buy_max_qty; // 최대 구매

          } else {

              min_cart_ct_qty = val.it_buy_min_qty;
              box_min_qty = (val.it_gubun === 'pack')
                  ? val.it_box_sale_pack
                  : val.it_box_sale_tot; // 박스구매
              max_cart_ct_qty = val.it_buy_max_qty; // 최대 구매

          }

          var tempArr = {'1' : 'room_temp', '3' : 'low_temp', '2' : 'frozen_temp', '4' : ''};
          var it_id   = val.it_id;
          var tempClass = (val.it_return_label == '반품가능') ? 'return_o' : 'return_x';
          var it_return_use = val.it_return_use;
          var unit_price_html = extra_item_label = '';

          if (val.it_price_piece > 0) {
            var it_price_piece = val.it_price_piece * 1;
            unit_price_html = `<s>/ 개당 ${it_price_piece.toLocaleString()}원</s>`;
          }

          arr_item_label.forEach(label => {
              if (label) {
                extra_item_label += `<span class="hide-820 ${item_label[label]}">${label}</span>`;
              }
          });



          if (it_return_use == '1') {
              tbody_html += `
            <td class="cart-table-check">
              <label for="ct_chk_0">
                <input type="checkbox" name="ct_chk[]" value="${val.it_id}" class="ct_chk">
              </label>
            </td>
            <td class="cart-table-num hide-820">${index}</td>
            <td class="cart-table-title">
              <input type="hidden" class="it_gubun" value="${val.it_gubun}" />
              <input type="hidden" class="it_box_sale_pcs" value="${val.it_box_sale_pcs}" />
              <input type="hidden" class="it_box_sale_pack" value="${val.it_box_sale_pack}" />
              <input type="hidden" class="it_box_sale_tot" value="${val.it_box_sale_tot}" />                
              <input type="hidden" class="it_price_piece" value="${val.it_price_piece}" />                

              <div>
                  <img src="${image_url}">
                  <h6>
                    <span class="my_cart_it_name_short">${val.it_name}</span>
                    <i>(${val.it_basic})
                    ${unit_price_html}
                    </i>
                    
                    ${extra_item_label}

                    <p class="show-820">
                      <span class="${tempArr[val.it_storage]}">${val.it_storage_label}</span>
                      <span class="${tempClass}">${val.it_return_label}</span>
                    </p>
                  </h6>              
              </div>
            </td>
            `;

          } else {
            tbody_html += `
            <td class="cart-table-check">
              <label for="ct_chk_0">
                <input type="checkbox" name="ct_chk[]" value="${val.it_id}" class="ct_chk">
              </label>
            </td>
            <td class="cart-table-num hide-820">${index}</td>
            <td class="cart-table-title">
              <input type="hidden" class="it_gubun" value="${val.it_gubun}" />
              <input type="hidden" class="it_box_sale_pcs" value="${val.it_box_sale_pcs}" />
              <input type="hidden" class="it_box_sale_pack" value="${val.it_box_sale_pack}" />
              <input type="hidden" class="it_box_sale_tot" value="${val.it_box_sale_tot}" />                

              <div>
                  <img src="${image_url}">
                  <h6>
                    <span class="my_cart_it_name_short">${val.it_name}</span>
                    <i>(${val.it_basic})
                    ${unit_price_html}
                    </i>

                    ${extra_item_label}

                    <p class="show-820">
                      <span class="${tempArr[val.it_storage]}">${val.it_storage_label}</span>
                    </p>
                  </h6>              
              </div>
            </td>
            `;
          }
          
        
          if (it_return_use == '1') {
            tbody_html += `
            <td class="cart-table-type hide-820">
              <span class="${tempArr[val.it_storage]}">${val.it_storage_label}</span>
              <span class="${tempClass}">${val.it_return_label}</span>
            </td>
            <td class="cart-table-price hide-820">${val.ct_price.toLocaleString()}원</td>
            <td class="cart-table-ea">
              <span class="show-820" id="mobile_pt_sales_${it_id}">
            `;

          } else {
            tbody_html += `
            <td class="cart-table-type hide-820">
              <span class="${tempArr[val.it_storage]}">${val.it_storage_label}</span>
            </td>
            <td class="cart-table-price hide-820">${val.ct_price.toLocaleString()}원</td>
            <td class="cart-table-ea">
              <span class="show-820" id="mobile_pt_sales_${it_id}">
            `;
          }
          

            if(val.it_soldout == '1' || val.it_force_soldout == '10') {
              tbody_html += ``;
            }else{
              tbody_html += `${val.pt_sales.toLocaleString()}원`;
            }

          tbody_html += `
            </span>
          `;

          if(val.it_soldout == '1' || val.it_force_soldout == '10') {
            tbody_html += `<div class="flex-center"><span class="ct-soldout">품절</span></div>`;
          }else{
            tbody_html += `
              <div class="flex-between">
                <p class="Qua wish-num">
                  <input type="hidden" class="min_ct_qty" value="${min_cart_ct_qty}" />
                  <button type="button" class="sit_qty_minus cart_qty_minus" data-it_id="${it_id}">
                    <img src="/images/icon/minus.svg">
                  </button> 
                  <input type="text" name="" id="ct_qty_${it_id}" value="${val.ct_qty}" class="cart_ct_qty" readonly>
                  <button type="button" class="sit_qty_plus cart_qty_plus" data-it_id="${it_id}">
                    <img src="/images/icon/plus.svg">
                  </button>
                </p>
            `;
          
            if(val.it_gubun !== 'box') {
              tbody_html += `
                <input type="hidden" class="cart_buy_box_qty" value="${box_min_qty}" />
                <button id="buy_box_btn" class="btn2" onclick="cartbuy_box_qty(this); return false;">박스구매</button>
              `;
            } //박스구매

          } //품절

          tbody_html += `
              </div>
            </td>
            <td class="hide-820" id="pt_sales_${it_id}">
          `;

          if(val.it_soldout == '1' || val.it_force_soldout == '10') {

            tbody_html += `-`;

          }else{
            tbody_html += `${val.pt_sales.toLocaleString()}원`;
          }

          tbody_html += `                
            </td>
            <td>
              <a href="javascript:;" class="sit_delete_btn" data-it_id="${it_id}"><img src="/images/icon/trash.svg"></a>
            </td>
          `;
          tbody_html += '</tr>';
      });

      $('.my_cart_tbody').html(tbody_html);
  } else {
      $('.my_cart_tbody').empty().html('<tr><td colspan="8" height="70">장바구니에 담긴 상품이 존재하지 않습니다.</td></tr>');
  }

}

</script>

<script>
// 사이드 장바구니 닫힘
$(function(){
    const target = document.getElementById('cartSidebar');
    const observer = new MutationObserver(function(){
      if ($(window).width() >= 1024) {
        $('#cartSidebar').removeClass('open');
      }
    });
    observer.observe(target, { attributes: true });
});
</script>

<style>
  .mypage-date{display: none;}
</style>

@endsection


