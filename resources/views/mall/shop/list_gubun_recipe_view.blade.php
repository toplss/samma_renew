@extends('layouts.header')

@section('content')

<div class="sub-container">

  <div class="recipe-view-wrap">
    <div class="recipe-title">
      {{ $items->title }}
      <p>
        <button type="button" class="btn3" onclick="window.open('/mall/shop/list_gubun_recipe_pop?print_select_idx={{ $items->p_id }}','레시피 프린트','width=900,height=700');">프린트하기</button>
        <button type="button" class="btn2" onclick="location.href='/mall/shop/list_gubun_recipe'">목록으로</button>
      </p>
    </div>

    <div class="recipe-detail">

      <div class="recipe-view-img">
        @php
        $content = $items->contents;

        $content = str_replace(
            'src="/smarteditor/upload',
            'src="https://samma-erp.com/smarteditor/upload',
            $content
        );

        echo $content;
        @endphp
      </div>

      <div class="recipe-table-box">

        <ul class="sort-icons">
            @for($i = 1; $i < 11; $i++)
            @php
            $image_url = 'images/recipe/cook/'.$items->{'cook_img'.$i};

            @endphp
            @if (file_exists(public_path($image_url)) && $items->{'cook_img'.$i})

            <li><img src="{{ asset($image_url) }}" alt=""></li>

            @endif
            @endfor
        </ul>
        <table class="rd-table1">
          <thead>
            <tr>
              <th>원료</th>
              <th>용량</th>
              <th>원가(원)</th>
            </tr>
          </thead>
          <tbody>
            @for($i = 1; $i < 11; $i++)
            @php
            $infomation = explode('||', $items->{'etc'.$i});

            @endphp
            @if (count($infomation) == 3)
            @php
              if (preg_match('/[^0-9,]/', $infomation[2])) {
                $clean_price = preg_replace('/[^0-9]/', '', $infomation[2]);
              } else {
                $clean_price = $infomation[2];
              }
            @endphp
            <tr>
              <td>{{ $infomation[0] ? $infomation[0] : '' }}</td>
              <td>{{ $infomation[1] ? $infomation[1] : '' }}</td>
              <td>{{ $infomation[2] ? number_format($clean_price) : '' }}</td>
            </tr>
            @endif
            @endfor
          </tbody>
          <tfoot>
            <tr>
              <td>소계</td>
              <td></td>
              <td>{{ number_format(preg_replace('/[^0-9]/', '', $items->etc_sub)) }}</td>
            </tr>
          </tfoot>
        </table>

        <table class="rd-table2">
          <tr>
            <th>원가</th>
            <th>소비판매가(원)</th>
            <th>수익율(%)</th>
          </tr>
          <tr>
            <td>{{ number_format($items->cost) }}</td>
            <td>{{ number_format($items->price) }}</td>
            <td>{{ number_format($items->profit_rate) }}</td>
          </tr>
        </table>

        <p style="color:#da2727; text-align:right;">※ 일부 금액은 상황에 따라 변경될 수 있습니다.</p>
      </div>


      <ul class="rd-table3">
        <li>레시피</li>
        <li>
          {!! nl2br(e($items->etc)) !!}
        </li>
      </ul>

    </div>

    <div class="related-goods-wrap">
      <h5>연관 상품</h5>
      <div class="prd-list">
      @foreach ($relation as $key => $row)
        @php
          $row = (array) $row;
          $sold_out = $row['it_soldout'] == '1' || $row['it_force_soldout'] == '10' ? true : false;
          $it_id    = $row['it_id'];
          $min_cart_ct_qty = $box_min_qty = $max_cart_ct_qty = 0;

          
          if(isset($activeMember['mb_level']) && substr($activeMember['mb_level'], 0, 2) == '30' && $row['agency_it_buy_min_qty'] > 0) {
              $min_cart_ct_qty = $row['agency_it_buy_min_qty']; // 주문최소
              $box_min_qty     = ($row['it_gubun'] == 'pack') ? $row['it_box_sale_pack'] : $row['it_box_sale_tot']; // 박스구매
              $max_cart_ct_qty = $row['agency_it_buy_max_qty']; // 최대치 구매

          } else {
              $min_cart_ct_qty = $row['it_buy_min_qty'];
              $box_min_qty     = ($row['it_gubun'] == 'pack') ? $row['it_box_sale_pack'] : $row['it_box_sale_tot']; // 박스구매
              $max_cart_ct_qty = $row['it_buy_max_qty']; // 최대치 구매
          }

          $image_url = 'images/item/'.$row['it_img1'];
        @endphp
        <ul class="prd-box" data-item="{{ $it_id }}">
            @if(!Storage::disk('public')->exists($image_url) && $row['it_img1'])
            <li class="prd-img  {{ $sold_out ? 'sold-out' : '' }}" onclick="location.href = '/mall/shop/view?it_id={{ $it_id }}'; "><img src="{{ asset($image_url) }}"></li>
            @else
            <li class="prd-img  {{ $sold_out ? 'sold-out' : '' }}" onclick="location.href = '/mall/shop/view?it_id={{ $it_id }}'; "><img src="{{ asset('images/common/no_image.gif') }}"></li>
            @endif
            <li class="Qua {{ $sold_out ? 'sold-out' : '' }}">
                @if($activeMember)
                <p class="pm-wrap">
                    <button type="button" class="sit_qty_minus" ><img src="{{ asset('images/icon/minus.svg') }}"></button>
                    <span id="numberUpDown">
                      <input type="hidden" name="min_ct_qty" class="min_ct_qty"  value="{{ $min_cart_ct_qty }}" readonly>
                      <input type="text" name="ct_qty" class="ct_qty"  value="{{ $min_cart_ct_qty }}" readonly>
                    </span>
                    <button type="button" class="sit_qty_plus" ><img src="{{ asset('images/icon/plus.svg') }}"></button>
                </p>
                <button type="button" class="add-to-cart" >담기</button>
                @else
                <p class="Qua-login">로그인 후 이용가능합니다.</p>
                @endif
            </li>
            <li class="prd-info">
                @if($row['it_gubun'] !== 'box')
                <input type="hidden" class="it_gubun" value="{{ $row['it_gubun'] }}" />
                <input type="hidden" class="buy_box_qty" value="{{ $box_min_qty }}" />
                <input type="hidden" class="it_box_sale_pcs" value="{{ $row['it_box_sale_pcs'] }}" />
                <input type="hidden" class="it_box_sale_pack" value="{{ $row['it_box_sale_pack'] }}" />
                <input type="hidden" class="it_box_sale_tot" value="{{ $row['it_box_sale_tot'] }}" />

                @if ($row['it_box_sale_pcs'])
                <button type="button" class="btn2" id="buy_box_btn" onclick="buy_box_qty(this)">박스구매</button>
                @endif
                
                @endif
                <p class="pin">
                    @php
                    $timpArr = ['1' => 'room_temp', '3' => 'low_temp', '2' => 'frozen_temp', '4' => ''];
                    @endphp
                    <span class="{{ $timpArr[$row['it_storage']] }}">{{ $row['it_storage_label'] }}</span><!-- 상온:room_temp, 냉장:low_temp 냉동:frozen-temp  -->
                    <span class="return_o">{{ $row['it_return_label'] }}</span>
                </p>
            </li>
            <li class="prd-name">
                {{ $row['it_name'] }}
                <p class="ea">({{ $row['it_basic'] }}*{{ $row['it_gubun_label'] }})</p>
            </li>

            @if($activeMember)
            <li class="prd-price">
                <x-mall.item-price
                    :row="$row"
                    :member="$activeMember"
                    :qty="$min_cart_ct_qty"
                />
              </li>
            @endif
        </ul>
      @endforeach
      </div>
    </div>

  </div>

</div>

@endsection

