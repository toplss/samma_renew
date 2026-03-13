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
@endphp

<div class="sub-container">

@if($status == 'success')
    <div class="sub-title-wrap">
        <h4>주문완료</h4>  
    </div>

    <img class="pr-img" src="{{ asset('images/icon/circle-success.svg') }}">
    <h5 class="pr-txt">마이페이지 주문내역에서 확인 하실 수 있습니다.</h5>
    <div class="pay-result-table">
        <table>
            <tr>
                <th>주문번호</th>
                <td>{{ $resultData->od_id }}</td>
            </tr>
            <tr>
                <th>주문일</th>
                <td>{{ $resultData->od_time }} ({{ \Carbon\Carbon::parse($resultData->od_time)->locale('ko')->dayName }})</td>
            </tr>
            <tr>
                <th>배송일</th>
                <td>{{ $resultData->od_delivery_date }} ({{ \Carbon\Carbon::parse($resultData->od_delivery_date)->locale('ko')->dayName }})</td>
            </tr>
            <tr>
                <th>상호/이름</th>
                <td>{{ $resultData->od_company }} / {{ $resultData->od_name }}</td>
            </tr>
            <tr>
                <th>휴대폰번호</th>
                <td>{{ $resultData->od_tel }}</td>
            </tr>
            <tr>
                <th>전화번호</th>
                <td>{{ $resultData->od_hp }}</td>
            </tr>
            <tr>
                <th>이메일</th>
                <td>{{ $resultData->od_email }}</td>
            </tr>
            <tr>
                <th>전용계좌</th>
                <td>{{ $activeMember['mb_virtual_bank']. ' '.$activeMember['mb_virtual_account'] }}</td>
            </tr>
            <tr>
                <th>배송주소</th>
                <td>{{ $resultData->od_addr1 }} {{ $resultData->od_addr2 }}</td>
            </tr>
            <tr>
                <th>요청사항</th>
                <td>{{ $resultData->od_memo }}</td>
            </tr>
        </table>
        <table>
            <tr>
                <th>주문금액</th>
                <td>{{ number_format($resultData->pt_sales) }}원</td>
            </tr>
            <tr>
                <th>배송비</th>
                <td>{{ number_format($resultData->pt_delivery) }}원</td>
            </tr>
            <!-- <tr>
                <th>개별상품 할인쿠폰</th>
                <td>0원</td>
            </tr>
            <tr>
                <th>주문금액 할인쿠폰</th>
                <td>0원</td>
            </tr>
            <tr>
                <th>배송비 할인쿠폰</th>
                <td>0원</td>
            </tr>
            <tr>
                <th>추가배송비</th>
                <td>0원</td>
            </tr>
            <tr>
                <th>취소금액</th>
                <td>0원</td>
            </tr> -->
            <tr>
                <th>합계금액</th>
                <td class="blue">{{ number_format($resultData->pt_sales_delivery) }}원</td>
            </tr>
            @if($payment_type == '선불')
            <tr>
                <th>충전금사용</th>
                <td>{{ number_format($resultData->od_temp_point) ?? '0' }}원</td>
            </tr>
            <tr>
                <th>적립금사용</th>
                <td>{{ number_format($resultData->od_temp_point_reserve) ?? '0' }}원</td>
            </tr>
            <tr>
                <th>총결제액</th>
                @php
                    $od_temp_point          = $resultData->od_temp_point ?? 0;
                    $od_temp_point_reserve  = $resultData->od_temp_point_reserve ?? 0;
                    $od_receipt_price       = $resultData->od_receipt_price ?? 0;
                    $total_pay = $od_temp_point + $od_temp_point_reserve + $od_receipt_price;
                @endphp                
                {{-- <td class="blue">{{ number_format($resultData->pt_sales_delivery) }}원</td> --}}
                <td class="blue">{{ number_format($total_pay) }}원</td>
            </tr>
            <tr>
                <th>미결제액</th>
                <td class="red">{{ number_format($resultData->od_misu) }}원</td>
            </tr>
            <tr>
                <th>결제수단</th>
                <td>{{ $resultData->od_settle_case }}</td>
            </tr>
            <tr>
                <th>입금자명</th>
                <td>{{ $data['buyername'] }}</td>
            </tr>
            <tr>
                <th>입금계좌</th>
                <td>{{ $resultData->od_bank_account }}</td>
            </tr>
            @endif
        </table>
    </div>

    <div class="flex-center" style="gap:0.5rem;">
        <button type="button" class="btn3 big-btn" onclick="location.href='/'">메인으로</button>
        <button type="button" class="btn1 big-btn" onclick="location.href='/mypage/orderinquiry'">주문내역</button>
    </div>

@else
    <!-- 실패 -->
    <div class="sub-title-wrap">
        <h4>주문실패</h4>  
    </div>

    <img class="pr-img" src="{{ asset('images/icon/circle-fail.svg') }}">
    <h5 class="pr-txt">주문에 실패했습니다. 잠시 후 다시 시도해주세요.</h5>

    <div class="flex-center" style="gap:0.5rem;">
        <button type="button" class="btn3 big-btn" onclick="location.href='/'">메인으로</button>
        <button type="button" class="btn1 big-btn" onclick="location.href='/mypage/cart'">장바구니</button>
    </div>
    <!-- 실패 끝 -->
@endif

</div>

<style>
    body{background-color: #f9f9f9;}
</style>


<!-- @if($status == 'success')
    <h2 style="color:green;">✅ 결제 인증 성공</h2>
@else
    <h2 style="color:red;">❌ 결제 인증 실패</h2>
@endif -->

<!-- 좌측 장바구니 숨기기 -->
<script>
$(function(){
    $('#cartSidebar').hide();
})
</script>

@endsection
