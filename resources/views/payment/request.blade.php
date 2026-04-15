@extends('layouts.header')

@section('content')

@php
$payment_type = '';
if (isset($activeMember['level_ca_id2'])) {
    if (strlen($activeMember['level_ca_id2']) == 4) {
        if (substr($activeMember['level_ca_id2'], -1) == '1') $payment_type = '선불';
        if (substr($activeMember['level_ca_id2'], -1) == '2') $payment_type = '후불';
    }
}

if (isset($activeMember['mb_level_type'])) {
    if ($activeMember['mb_level_type'] == '2' && $activeMember['mb_gubun_type'] == 'employee') {
        $payment_type = '후불';
    }
}

@endphp

<form id="payForm" name="payForm" method="POST" >

    <div class="sub-container">
        <div class="sub-title-wrap">
            <h4>주문/결제</h4>  
        </div>

        <!-- 결재정보 시작 -->
        @if($is_mobile)
        <!-- <input type="hidden" name="inipaymobile_type" value="web"> -->
        <input type="hidden" name="P_INI_PAYMENT" value="CARD">
        <input type="hidden" name="P_MID" value="{{ $mid }}">
        <input type="hidden" name="P_OID" value="{{ $oid }}">
        <input type="hidden" name="P_AMT" value="{{ $price }}">
        <input type="hidden" name="P_GOODS" value="{{ $goodname }}">
        <input type="hidden" name="P_UNAME" value="{{ $buyername }}">
        <input type="hidden" name="P_MOBILE" value="{{ $buyermobile }}">
        <input type="hidden" name="P_EMAIL" value="{{ $activeMember['mb_email'] }}">
        <input type="hidden" name="P_NEXT_URL" value="{{ route('payment.approve') }}">
        <input type="hidden" name="P_RETURN_URL" value="{{ route('payment.noti_result') }}"> 
        <input type="hidden" name="P_NOTI_URL" value="{{ route('payment.noti') }}">
        <input type="hidden" name="P_NOTI" value="{{ $oid }}">
        <input type="hidden" name="P_CHKFAKE" value="{{ $hashData }}">
        <input type="hidden" name="P_TIMESTAMP" value="{{ $timestamp }}">
        <input type="hidden" name="P_RESERVED" value="twotrs_isp=Y&block_isp=Y&twotrs_isp_noti=N&ismart_use_sign=Y&vbank_receipt=Y&bank_receipt=N&apprun_check=Y">
        <input type="hidden" name="P_CHARSET" value="EUC-KR">
        <input type="hidden" name="P_HPP_METHOD" value="2">
        @else
        <input type="hidden" name="version" value="1.0">
        <input type="hidden" name="mid" value="{{ $mid }}">
        <input type="hidden" name="oid" value="{{ $oid }}">
        <input type="hidden" name="goodname" value="{{ $goodname }}">
        <input type="hidden" name="price" value="{{ $price }}">
        <input type="hidden" name="currency" value="WON">
        <input type="hidden" name="buyername"   value="{{ $buyername }}">
        <input type="hidden" name="buyeremail"  value="{{ $buyeremail }}">


        <!-- 이니시스가 JS로 채울 값 -->
        <input type="hidden" name="timestamp" value="{{ $timestamp }}">
        <input type="hidden" name="signature" value="{{ $signature }}">
        <input type="hidden" name="mKey" value="{{ $mKey }}">

        <!-- 옵션이지만 사실상 필수 -->
        <input type="hidden" name="gopaymethod" value="">  
        <input type="hidden" name="acceptmethod" value="HPP(1):Card(0)">
        <input type="hidden" name="payViewType" value="{{ $payViewType }}">
        <input type="hidden" name="charset" value="UTF-8">

        <input type="hidden" name="nointerest"  value="{{ $cardNoInterestQuota }}">
        <input type="hidden" name="quotabase"   value="{{ $cardQuotaBase }}">
        <input type="hidden" name="returnUrl" value="{{ $returnUrl }}">
        @endif
        <!-- 결재정보 종료 -->

        <!-- 그외 필요한 데이터 -->
        <input type="hidden" name="od_id" value="{{ $oid }}">
        <input type="hidden" name="order_type" value="{{ request('order_type') }}">
        <input type="hidden" name="deilivery_cost" value="{{ $orderInfo['deilivery_cost'] }}">
        <input type="hidden" name="payment_type" value="{{ $payment_type }}">
        <input type="hidden" name="paytimestamp" value="{{ $timestamp }}">

        <!-- 결제요청금액 원금 -->
        <input type="hidden" name="payment" value="{{ $price }}">
        <!-- 그외 처리후 금액 -->
        <input type="hidden" name="payable" id="payable" value="{{ $price }}">

        <div class="pay-wrap">
            <h4>배송 정보</h4>
            <table class="pay-delivery-table">
                <tr>
                    <th>상호</th>
                    <td><input type="text" name="od_company" value="{{ $activeMember['mb_company'] }}" id="od_company" readonly></td>
                    <th>휴대폰번호</th>
                    <td><input type="text" name="od_hp" value="{{ $activeMember['mb_hp'] }}" id="od_hp" readonly></td>
                </tr>
                <tr style="display:none;">
                    <th>이름</th>
                    <td><input type="text" name="od_name" value="{{ $activeMember['mb_name'] }}" id="od_name" readonly></td>
                    <th>전화번호</th>
                    <td><input type="text" name="od_tel" value="{{ $activeMember['mb_hp'] }}" id="od_tel" readonly></td>
                    <th>이메일</th>
                    <td><input type="text" name="od_email" value="{{ $activeMember['mb_email'] }}" id="od_email" readonly></td>
                </tr>
                <tr>
                    <th>주소</th>
                    <td>
                        <p>
                            <span id="od_addr_jibeon_txt">{{ $activeMember['mb_addr1'] }}</span>
                            <span id="od_addr2_txt">{{ $activeMember['mb_addr2'] }}</span>
                        </p>
                        <p class="txt-red">※ 주소 변경시 고객센터 (031-238-9661) 로 연락주시기 바랍니다.</p>
                    </td>
                    <th>배송요일</th>
                    <td>
                        @php
                            $days = [
                                '월' => 'mb_cs_mon',
                                '화' => 'mb_cs_tue',
                                '수' => 'mb_cs_wed',
                                '목' => 'mb_cs_thu',
                                '금' => 'mb_cs_fri',
                                '토' => 'mb_cs_sat',
                                '일' => 'mb_cs_sun',
                            ];

                            $selectedDay = $activeMember['delivery_info']['ship_date'] ?? '';
                        @endphp
                        <ul class="delivery-days">
                        @foreach ($days as $day => $flag)
                            @if(($activeMember[$flag] ?? 'n') === 'y')
                                <li class="{{ $selectedDay === $day ? 'on' : '' }}">
                                    {{ $day }}
                                </li>
                            @endif
                        @endforeach
                        </ul>
                        @if(!$selectedDay)<span class="txt-red">배송요일이 없습니다</span>@endif
                        <p class="txt-red">※ 배송요일 하루전 오후 2시이전 주문시 원활한 납품을 받으실 수 있습니다.</p>
                        <p class="near_delivery_info">납품 : <span >{{ $orderInfo['header_into']['delivery_day'] }}</span></p>
                    </td>
                </tr>
            </table>
            
            <h4>상품 정보</h4>
            <div class="pay-item-wrap">
                <!-- 없을때 -->
                <!-- <p class="empty">장바구니에 담긴 상품이 없습니다</p> -->
                
                @if(isset($orderInfo['cart_items'][1]))
                <!-- 상온 -->
                <div>
                <table class="pay-item-table">
                    <thead>
                        <tr>
                            <th colspan="5">상온제품</th>
                        </tr>
                        <tr>
                            <th>No</th>
                            <th>이미지</th>
                            <th>상품명</th>
                            <th>수량</th>
                            <th>금액</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orderInfo['cart_items'][1] as $key => $row)
                        @php
                        $image_url  = 'images/item/'.$row['it_img1'];
                        @endphp
                        <tr>
                            <td>{{ ++$key }}</td>
                            <td>
                                @if(file_exists(public_path($image_url)) && $row['it_img1'])
                                <img src="{{ asset($image_url) }}" alt="">
                                @else
                                <img src="{{ asset('images/common/no_image.gif') }}" alt="">
                                @endif
                            </td>
                            <td class="pi-name">{{ $row['it_name'] }}<span>{{ $row['it_basic'] }}</span></td>

                            
                            
                            @if($row['it_soldout'] == '1' || $row['it_force_soldout'] == '10')
                                <td class="pi-soldout"><span class="txt-red">품절</span></td>
                                <td>-</td>
                            @else
                                <td>{{ $row['ct_qty'] }}</td>
                                <td>{{ number_format($row['ct_price'] * $row['ct_qty']) }}원</td>
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3">합계</th>
                            @php
                            $storage_1_total_cnt = collect($orderInfo['cart_items'][1])
                                ->where('it_soldout', '!=', 1)
                                ->where('it_force_soldout', '!=', '10')                                
                                ->sum(function ($row) {
                                    return $row['ct_qty'];
                                })                            
                            @endphp
                            <th>{{ $storage_1_total_cnt }}</th>
                            @php
                            $storage_1_total = collect($orderInfo['cart_items'][1])
                                ->where('it_soldout', '!=', 1)
                                ->where('it_force_soldout', '!=', '10')                                                                
                                ->sum(function ($row) {
                                    return $row['ct_price'] * $row['ct_qty'];
                                })
                            @endphp
                            <th>{{ number_format($storage_1_total) }}원</th>
                        </tr>
                    </tfoot>
                </table>
                </div>
                @endif

                @if(isset($orderInfo['cart_items'][2]))
                <!-- 저온 -->
                <div>
                <table class="pay-item-table">
                    <thead>
                        <tr>
                            <th colspan="5">저온제품</th>
                        </tr>
                        <tr>
                            <th>No</th>
                            <th>이미지</th>
                            <th>상품명</th>
                            <th>수량</th>
                            <th>금액</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orderInfo['cart_items'][2] as $key => $row)
                        @php
                        $image_url  = 'images/item/'.$row['it_img1'];
                        @endphp
                        <tr>
                            <td>{{ ++$key }}</td>
                            <td>
                                @if(file_exists(public_path($image_url)) && $row['it_img1'])
                                <img src="{{ asset($image_url) }}" alt="">
                                @else
                                <img src="{{ asset('images/common/no_image.gif') }}" alt="">
                                @endif
                            </td>
                            <td class="pi-name">{{ $row['it_name'] }}<span>{{ $row['it_basic'] }}</span></td>

                            @if($row['it_soldout'] == '1' || $row['it_force_soldout'] == '10')
                                <td class="pi-soldout"><span class="txt-red">품절</span></td>
                                <td>-</td>
                            @else
                                <td>{{ $row['ct_qty'] }}</td>
                                <td>{{ number_format($row['ct_price'] * $row['ct_qty']) }}원</td>
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>

                        <tr>
                            <th colspan="3">합계</th>
                            @php
                            $storage_2_total_cnt = collect($orderInfo['cart_items'][2])
                                ->where('it_soldout', '!=', 1)
                                ->where('it_force_soldout', '!=', '10')                                                                
                                ->sum(function ($row) {
                                    return $row['ct_qty'];
                                })                            
                            @endphp
                            <th>{{ $storage_2_total_cnt }}</th>
                            @php
                            $storage_2_total = collect($orderInfo['cart_items'][2])
                                ->where('it_soldout', '!=', 1)
                                ->where('it_force_soldout', '!=', '10')                                                                
                                ->sum(function ($row) {
                                    return $row['ct_price'] * $row['ct_qty'];
                                })
                            @endphp
                            <th>{{ number_format($storage_2_total) }}원</th>
                        </tr>

                    </tfoot>
                </table>
                </div>
                @endif
            </div>

            <h4>총 주문금액</h4>
            <table class="pay-price-table">
                <tr>
                    <th>상온 합계</th>
                    <td>{{ number_format($storage_1_total ?? 0) }}원</td>
                    <th>저온 합계</th>
                    <td>{{ number_format($storage_2_total ?? 0) }}원</td>
                </tr>
                <tr>
                    <th>배송비 합계</th>
                    <td>{{ number_format($orderInfo['deilivery_cost']) }}원</td>
                    <th>총 주문합계</th>
                    <td class="total">{{ number_format($price) }}원</td>
                </tr>
            </table>

            @if (request('order_type') == 'items' && $payment_type == '선불')
            <table class="pay-price-table2">
                <tr>
                    <th>배송비 안내</th>
                    <td>
                        @if($activeMember['mb_sendcost'] == '6' || $activeMember['mb_sendcost'] == '7')
                            <span class="txt-red">총 주문금액이 {{ number_format($orderInfo['min_order_amount']) }}원 이상시 배송비 무료</span>
                        @endif
                        @if($activeMember['mb_sendcost'] == '1')
                            <span class="txt-blue">설정된 배송비가 없습니다.</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>요청사항</th>
                    <td><textarea name="od_memo" id=""></textarea></td>
                </tr>
            </table>

            <h4>충전금/적립금</h4>
            <table class="pay-point-table">
                <tr>
                    <th>충전금</th>
                    <td>
                        <p class="my-point">
                            <input type="hidden" value="{{ $activeMember['mb_point'] }}" id="od_tmp_point"> 
                            <input type="text" name="input_od_temp_point" value="" id="input_od_temp_point"> 
                            <u>원</u>
                        </p>
                        <label><input type="checkbox" name="all_use_temp_point" id="all_use_temp_point">충전금 전액</label>
                        <span>(보유충전금 : <b>{{ number_format($activeMember['mb_point']) }}원</b>)</span>
                        @if($activeMember['mb_point'] < 1)<span class="no-point">사용 가능한 적립금이 없습니다.</span>@endif
                    </td>
                    <th>적립금</th>
                    <td>
                        <p class="my-point">
                            <input type="hidden" value="{{ $activeMember['mb_point_reserve'] }}" id="od_tmp_reserve"> 
                            <input type="text" name="input_od_temp_point_reserve" value="" id="input_od_temp_point_reserve">
                            <u>원</u>
                        </p>
                        <label><input type="checkbox" name="all_use_temp_point_reserve" id="all_use_temp_point_reserve">적립금 전액</label>
                        <span>(보유적립금 : <b>{{ number_format($activeMember['mb_point_reserve']) }}원</b>)</span>

                        @if($activeMember['mb_point_reserve'] < 1)<span class="no-point">사용 가능한 적립금이 없습니다.</span>@endif
                    </td>
                </tr>
                <tr>
                    <th>사용합계</th>
                    <td>
                        <span id="temp_point_txt">충전금 사용 <label id="temp_point_label">0</label>원</span>
                        <span id="temp_point_reserve_txt">적립금 사용 <label id="temp_reserve_label">0</label>원</span>
                        <span id="total_use_temp_point_txt">합계 <label id="temp_total_label">0</label>원</span>
                    </td>
                    <th>결제금액</th>
                    <td id="payment_amount_txt">{{ number_format($price) }}원</td>
                </tr>
            </table>
            @endif

            @if($activeMember['mb_point_balance'] > 0)
            <h4>당월 잔액</h4>
            <table class="pay-balance-table">
                <tr>
                    <th>당월 잔액</th>
                    <td>
                    <p>{{ number_format($activeMember['mb_point_balance']) }}원</p>
                    <span>[입금계좌 : 기업은행 {{ $orderInfo['header_into']['mb_virtual_account'] }} 예금주: {{ $activeMember['mb_company'] }}</span>
                    </td>
                </tr>
            </table>
            @endif

            <div class="payment_bot">
                <div class="pay_how">
                    <h4>결제방법</h4>
                    <ul>
                        <li class="od_settle_finance_vbank">
                            <label>
                                <input type="radio" id="od_settle_finance_vbank" class="od_settle_case" name="od_settle_case" value="금융권가상계좌">금융권 가상계좌
                            </label>
                        </li>
                        @if (request('order_type') == 'items' && $payment_type == '선불')
                        @php 
                        $payments = !empty($activeMember['mb_payment'])
                                        ? explode('||', $activeMember['mb_payment']) 
                                        : [];
                        @endphp
                        @if (in_array("3", $payments))
                        <li class="od_settle_card">
                            <label>
                                <input type="radio" id="od_settle_card" class="od_settle_case" name="od_settle_case" value="신용카드">신용카드
                            </label>
                        </li>
                        @endif
                        <!-- <li class="od_settle_iche">
                            <label>
                                <input type="radio" id="od_settle_iche" class="od_settle_case" name="od_settle_case" value="계좌이체">계좌이체
                            </label>
                        </li> -->
                        <!-- <li class="od_settle_vbank">
                            <label>
                                <input type="radio" id="od_settle_vbank" class="od_settle_case" name="od_settle_case" value="PG사가상계좌">PG사 가상계좌
                            </label>
                        </li> -->
                        <li class="od_settle_bank">
                            <label>
                                <input type="radio" id="od_settle_bank" name="od_settle_case" value="무통장입금">무통장입금
                            </label>
                        </li>
                        <li class="od_settle_charge" style="display: none;">
                            <label>
                                <input type="radio" id="od_settle_charge" class="od_settle_case" name="od_settle_case" value="충전금">충전금결제
                            </label>
                        </li>
                        <li class="od_settle_reserve" style="display: none;">
                            <label>
                                <input type="radio" id="od_settle_reserve" class="od_settle_case" name="od_settle_case" value="적립금">
                                적립금결제
                            </label>
                        </li>
                        <li class="od_settle_charge_reserve" style="display: none;">
                            <label>
                                <input type="radio" id="od_settle_charge_reserve" class="od_settle_case" name="od_settle_case" value="충전금+적립금">충전금+적립금결제</label>
                        </li>
                        <!-- <li class="od_settle_kakaopay">
                            <label>
                                <input type="radio" id="od_settle_kakaopay" class="od_settle_case" name="od_settle_case" value="카카오페이">카카오페이</label>
                        </li> -->
                        <!-- <li class="od_settle_easy_pay">
                            <label>
                                <input type="radio" id="od_settle_easy_pay" class="od_settle_case" name="od_settle_case" value="간편결제">간편결제</label>
                        </li> -->
                        @endif
                    </ul>
                    <dl id="virtual_account_info_zone">
                        <dt>입금계좌</dt>
                        <dd id="settle_finance_vbank">기업은행 {{ $orderInfo['header_into']['mb_virtual_account'] }} <br class="show-680">[ 예금주: {{ $activeMember['mb_company'] }} ]</dd>
                        <dt>입금자명</dt>
                        <dd><input type="text" name="od_finance_deposit_name" id="od_finance_deposit_name" value="{{ $activeMember['mb_company'] }}" readonly></dd>
                    </dl>						
                </div>

                <div class="pay_btns">
                    <p class="flex-center"><img src="{{ asset('images/icon/bell.gif') }}">배송요일 하루전 오후 2시이전 주문시 원활한 납품을 받으실 수 있습니다.</p>
                    <ul>
                        <li><button type="button" class="od_btn_cancel btn2" onclick="location.href='/mypage/cart'">장바구니</button></li>
                        @if ($payment_type == '선불')
                        <li><button type="button" class="od_btn btn3" onclick="requestPay()">결제하기</button></li>
                        @else
                        <li><button type="button" class="od_btn btn3" onclick="requestPay()">주문하기</button></li>
                        @endif
                    </ul>
                </div>
            </div>

        </div>
    </div>

</form>

<!-- 결제방법 선택 이벤트 -->
<script>
$(document).ready(function() {
    let order_type   = $('input[name="order_type"]').val();
    let payment_type = $('input[name="payment_type"]').val();

    if (order_type == 'charge' || payment_type == '후불') {
        $('#od_settle_finance_vbank').prop('checked', true);

        if (payment_type == '후불') {
            $('.pay_how').hide();
        }
    }

    $(document).on('change', '.od_settle_case', function() {
        if ($(this).val() == '금융권가상계좌' && $(this).is(':checked')) {
            $('#virtual_account_info_zone').show();
        } else {
            $('#virtual_account_info_zone').hide();
        }
    });
});
</script>
<!-- 결제방법 선택 이벤트 -->


<!-- 충전금 + 적립금 실행 스크립트 -->
<script>
let is_mobile   = `{{ $is_mobile }}`;

$(document).ready(function() {
    let payment = $('input[name="payment"]').val() * 1;
    let point   = $('#od_tmp_point').val() * 1;
    let reserve = $('#od_tmp_reserve').val() * 1;

    // 충전금 사용 (체크된 경우만)
    $(document).on('change', '#all_use_temp_point', function() {
        if ($(this).is(':checked')) {
            let usedReserve = $('#input_od_temp_point_reserve').val() * 1;
            let remaining = payment - usedReserve;  // 이미 적립금 사용됐다면 나머지 금액만 차감
            let usePoint = Math.min(point, remaining);
            $('#input_od_temp_point').val(usePoint);

        } else {
            $('#input_od_temp_point').val('');
        }

        updateSettleView();
    });

    // 적립금 사용 (체크된 경우만)
    $(document).on('change', '#all_use_temp_point_reserve', function() {
        if ($(this).is(':checked')) {
            let usedPoint = $('#input_od_temp_point').val() * 1;
            let remaining = payment - usedPoint;  // 이미 충전금 사용됐다면 나머지 금액만 차감
            let useReserve = Math.min(reserve, remaining);
            $('#input_od_temp_point_reserve').val(useReserve);
        } else {
            $('#input_od_temp_point_reserve').val('');
        }

        updateSettleView();
    });


    $(document).on('input', '#input_od_temp_point', function() {
        let input_amt = $(this).val();
        let reserve   = $('#input_od_temp_point_reserve').val() * 1;
        if (!/^\d*$/.test(input_amt)) {
            validationAlertMessage('숫자만 입력 가능합니다.');
            $(this).val(''); // 입력 초기화
            return false;
        }

        if (input_amt.length > 0 && input_amt.charAt(0) === '0') {
            validationAlertMessage('첫 번째 숫자는 0이 될 수 없습니다.');
            $(this).val(''); // 입력 초기화
            return false;
        }

        if (($('#od_tmp_point').val() * 1) < 1 ) {
            validationAlertMessage('보유한 충전금액 잔액이 존재하지 않습니다.');
            $(this).val(''); // 입력 초기화
            return false;
        }

        let payment = $('input[name="payment"]').val() * 1;
        if (((input_amt * 1) + reserve) > payment) {
            validationAlertMessage('결제예정 금액보다 입력값이 큽니다.');
            $(this).val(input_amt.slice(0, -1));
            return false;
        }
        updateSettleView();
    });

    $(document).on('input', '#input_od_temp_point_reserve', function() {
        let input_amt = $(this).val();
        let point   = $('#input_od_temp_point').val() * 1;
        if (!/^\d*$/.test(input_amt)) {
            validationAlertMessage('숫자만 입력 가능합니다.');
            $(this).val(''); // 입력 초기화
            return false;
        }

        if (input_amt.length > 0 && input_amt.charAt(0) === '0') {
            validationAlertMessage('첫 번째 숫자는 0이 될 수 없습니다.');
            $(this).val(''); // 입력 초기화
            return false;
        }

        if (($('#od_tmp_reserve').val() * 1) < 1 ) {
            validationAlertMessage('보유한 적립금 잔액이 존재하지 않습니다.');
            $(this).val(''); // 입력 초기화
            return false;
        }

        let payment = $('input[name="payment"]').val() * 1;
        if (((input_amt * 1) + point) > payment) {
            validationAlertMessage('결제예정 금액보다 입력값이 큽니다.');
            $(this).val(input_amt.slice(0, -1));
            return false;
        }

        updateSettleView();
    });
});


function updateSettleView() {
    $('.od_settle_charge_reserve, .od_settle_charge, .od_settle_reserve, .od_settle_bank').hide();
    
    $('#virtual_account_info_zone').hide();

    $('input[name="od_settle_case"]').prop('checked', false);

    updatePayment();
}


function updatePayment() {
    let usedPoint = $('#input_od_temp_point').val() * 1;
    let usedReserve = $('#input_od_temp_point_reserve').val() * 1;
    let originalPayment = $('input[name="payment"]').val() * 1;
    let remaining = originalPayment - (usedPoint + usedReserve);

    if (remaining < 0) {
        remaining = 0;
        return false;
    }

    if (is_mobile == '1') {
        $('input[name="P_AMT"]').val(remaining);   // 잔액 적용
    } else {
        $('input[name="price"]').val(remaining);   // 잔액 적용
    }

    $('#temp_point_label').text(usedPoint.toLocaleString());
    $('#temp_reserve_label').text(usedReserve.toLocaleString());
    $('#temp_total_label').text((usedPoint + usedReserve).toLocaleString());

    $('#payable').val(remaining); // 카드결제 외 결제예정 금액 활용
    $('#payment_amount_txt').html(remaining.toLocaleString() + '원');

    if (remaining == 0) {
        $('.od_settle_finance_vbank, .od_settle_card, .od_settle_bank').hide();
    } else {
        $('.od_settle_finance_vbank, .od_settle_card, .od_settle_bank').show();
    }

    readioBtnEvent();
}


function readioBtnEvent() {
    let payment_type = $('input[name="payment_type"]').val();
    let payable = $('#payable').val() * 1;

    if (payment_type == '선불') {
        if ($('#input_od_temp_point').val() * 1 > 0 && $('#input_od_temp_point_reserve').val() * 1 > 0 && payable == 0) {
            $('.od_settle_charge_reserve').show();
            $('.od_settle_charge_reserve').find('input[name="od_settle_case"]').prop('checked', true);
        } else if ($('#input_od_temp_point').val() * 1 > 0 && payable == 0) {
            $('.od_settle_charge').show();
            $('.od_settle_charge').find('input[name="od_settle_case"]').prop('checked', true);
        } else if ($('#input_od_temp_point_reserve').val() * 1 > 0 && payable == 0) {
            $('.od_settle_reserve').show();
            $('.od_settle_reserve').find('input[name="od_settle_case"]').prop('checked', true);
        }
    }

}
</script>
<!-- 충전금 + 적립금 실행 스크립트 종료 -->


<!-- 결제 스크립트 -->
@if (!$is_mobile)
<script src="https://stdpay.inicis.com/stdjs/INIStdPay.js"></script>
@endif

<script>

function requestPay() {
    let is_mobile   = `{{ $is_mobile }}`;
    let payment_type = $('input[name="payment_type"]').val().trim();
    let title   = '결제 확인';
    let message = '결제를 진행하시겠습니까?';
    let btnTit  = '결제하기';

    if (payment_type == '후불') {
        title   = '주문 확인';
        message = '주문상품을 구매하시겠습니까?';
        btnTit  = '주문하기';
    }

    const od_settle_case = $('input[name="od_settle_case"]:checked').val();
    const payable = $('input[name="payable"]').val();

    if (!payment_type) {
        validationAlertMessage('결제유형이 존재하지 않는 회원 입니다. 관리자에게 문의 바랍니다.');
        return false;
    }

    if (!od_settle_case) {
        validationAlertMessage('원하시는 결제 방법을 선택해주세요.');
        return false;
    }

    if (payment_type === '선불' && payable == 0 && (od_settle_case === '신용카드' || od_settle_case === '금융권가상계좌')) {
            validationAlertMessage('결제할 금액이 없습니다. <br>다른 결제 수단을 선택해 주세요.');
            return false;
    }

    //품절체크    
    if (is_mobile == '1') {
        od_id = $('input[name="P_OID"]').val();
    } else {
        od_id = $('input[name="oid"]').val();
    }    

    $.post({
        url  : '/payment/checkSoldout',
        data : {
            oid : od_id,
        }
    })
    .done(function (res) {

        if (res.status == 'success') {

            Swal.fire({
                title: title,
                text: message,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: btnTit,
                cancelButtonText: '취소'
            }).then((result) => {
                if (result.isConfirmed) {

                    $.get({
                        url  : '/payment/refresh',
                        data : {
                            od_id : $('input[name="od_id"]').val(),
                            price : $('input[name="payable"]').val(),
                            is_mobile : is_mobile,
                            timestamp : $('input[name="paytimestamp"]').val(),
                        },
                        dataType : 'JSON'
                    }).done(function(res) {

                        if (res.status == 'success') {
                            if (is_mobile == '1') {
                                $('input[name="P_CHKFAKE"]').val(res.hashData);
                            } else {
                                $('input[name="signature"]').val(res.signature);
                            }

                            payment_act()

                        } else if (res.status == 'logout') {

                            validationAlertMessage('로그인 후 이용해주세요.','/');

                        } else {
                            validationAlertMessage('결제 대행사의 통신장애가 발생하였습니다.');
                        }

                    });


                }
            });

            
        } else {
            CustomizingAlertMessage(res.title, res.message, '/mypage/cart');
            return false;
        }

    })    
    

}

function payment_act() {
    let od_settle_case = $('input[name="od_settle_case"]:checked').val();
    const payment_type = $('input[name="payment_type"]').val();
    const payable = $('input[name="payable"]').val();

    if (!payment_type) {
        validationAlertMessage('결제유형이 존재하지 않는 회원 입니다. 관리자에게 문의 바랍니다.');
        return false;
    }

    if (!od_settle_case) {
        validationAlertMessage('원하시는 결제 방법을 선택해주세요.');
        return false;
    }

    if (payment_type === '선불' && payable == 0 && (od_settle_case === '신용카드' || od_settle_case === '금융권가상계좌')) {
            validationAlertMessage('결제할 금액이 없습니다. <br>다른 결제 수단을 선택해 주세요.');
            return false;
    }


    if (od_settle_case == '신용카드' || od_settle_case == 'PG사가상계좌') {
        if (od_settle_case == '신용카드') {
            $('input[name="gopaymethod"]').val('Card');
        }
        if (od_settle_case == 'PG사가상계좌') {
            $('input[name="gopaymethod"]').val('VBank');
        }


        const data = {};
        $('#payForm').serializeArray().forEach(function (item) {
            data[item.name] = item.value;
        });

        let is_mobile   = `{{ $is_mobile }}`;
        let od_id;

        if (is_mobile == '1') {
            od_id = $('input[name="P_OID"]').val();
        } else {
            od_id = $('input[name="oid"]').val();
        }


        $.post({
            url  : '/payment/orderdata',
            data : {
                oid : od_id,
                form_data : data
            }
        })
        .done(function (res) {

            if (res.status == 'success') {
                if (is_mobile == '1') {
                    $('#payForm')
                    .attr('type', 'POST')
                    .attr('accept-charset', 'EUC-KR')
                    .attr('action', 'https://mobile.inicis.com/smart/payment/')
                    .submit();
                } else {
                    INIStdPay.pay('payForm');
                }
            }else if (res.status == 'reject') {
                CustomizingAlertMessage(res.title, res.message, '/mypage/cart')
            } else {
                validationAlertMessage(res.message);
            }

        })
        .fail(function (xhr) {
            validationAlertMessage('결제 준비 중 오류가 발생했습니다.');
        });
    } else {
        $('#payForm').attr('action', '/payment/result').submit();
    }
}
</script>

<!-- 결제 스크립트 종료 -->


<!-- 좌측 장바구니 숨기기 -->
<script>
function validationAlertMessage(message)  {
  Swal.fire({
      toast: false,
      icon: 'warning',
      title: '알림',
      html: message,
      confirmButtonText: '확인'
  });
}


function CustomizingAlertMessage(title, message, redirectUrl = null) {
  Swal.fire({
      toast: false,
      icon: 'warning',
      title: title,
      html: message,
      confirmButtonText: '확인'
  }).then((result) => {
      if (result.isConfirmed && redirectUrl) {
          window.location.href = redirectUrl;
      }
  });
}

$(function(){
    $('#cartSidebar').hide();
})
</script>

<!-- 요청후 상태값 -->
@if($status === 'reject')
<script>
let message = `{!! $message !!}`;
Swal.fire({
    title: '알림',
    html: message,
    icon: 'question',
    showCancelButton: false,
    confirmButtonText: '확인',
    cancelButtonText: '취소',
    allowOutsideClick: false
}).then((result) => {
    if (result.isConfirmed) {
        location.href = '/mypage/cart';
    }
}); 
</script>
@endif

<style>
    header{border-bottom: 1px solid #ddd;}
    nav{display: none;}
</style>
@endsection

