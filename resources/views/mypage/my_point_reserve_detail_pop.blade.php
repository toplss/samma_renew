<!DOCTYPE html>
<html lang="ko">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<title>@yield('title', '삼마몰')</title>
	<style>
        .point-pop{border-radius: 0.5rem; overflow: hidden; font-size: 0.95rem; width: 70vw; max-width: 640px;}
        .point-pop h6{background-color: #4cc2e9; border-bottom: none; padding: 0.7rem; font-weight: 500; font-size: 1.1rem; color: #fff;}
        .pp-table{max-height: 420px; overflow: auto; padding: 1rem; border: 1px solid #e9e9e9; border-radius: 0 0 0.5rem 0.5rem;}
        .pp-table table{border: 1px solid #e9e9e9; color: #545454;}
        .pp-table th{background-color: #f2f3f8; border-bottom: 1px solid #e9e9e9; padding: 6px 2px;}
        .pp-table td{border-bottom: 1px solid #e9e9e9; padding: 2px;}
        .pp-table img{width: 32px;}
        .pp-table .bg{background-color: #f2f3f8; font-weight: 500;}
        .pp-table .txt-right{height: 32px; text-align: right !important;}
        .pp-table .txt-center{height: 32px;}
        .pp-table tr td:last-of-type{font-weight: 500; white-space: nowrap;}
        .pp-table tr td:nth-last-of-type(2){white-space: nowrap;}
        .pp-table .h40{height: 40px;}
        .pp-flex{display: flex; align-items: center; gap: 5px; text-align: left;}

        @media screen and (max-width: 680px){
            .point-pop{width: 100%; font-size: 0.85rem;}
            .point-pop h6{font-size: 1rem; padding: 0.6rem;}
            .pp-table{padding: 0; border: none; min-width: 78vw;}
            .pp-table td{padding: 7px 3px; border: 1px solid #e9e9e9;}
            .pp-table img{display: none;}
            .pp-table tr td:last-of-type{font-weight: 400;}
            .pp-table .h40{height: 32px;}
            div:where(.swal2-container) div:where(.swal2-popup){max-width: 96vw;}
        }
    </style>
</head>
<body>

    <div class="point-pop">

        <h6>적립금 상세내역</h6>

        <div class="pp-table">
            <table>
                <tr>
                    <th>구분</th>
                    <th>상품정보</th>
                    <th>수량</th>
                    <th>금액</th>
                </tr>

                @if ($po_action == 'pt_buy_reserve')
                    <tr>
                        <td class="bg">관리자 충전</td>
                        <td class="h40">관리자 충전</td>
                        <td>1</td>
                        <td>{{ number_format($change_point) }}원</td>
                    </tr>
                @elseif ($po_action == 'od_use' || $po_action == 'pt_reserve')
                    <tr>
                        <td class="bg">주문</td>
                        <td class="h40">적립금 사용</td>
                        <td>-</td>
                        <td>{{ number_format($change_point) }}원</td>
                    </tr>
                @else
                    @foreach($groups as $ct_cate => $rows)
                        @php
                            //납품은 주문으로 표기 변경
                            if ($ct_cate == '납품') {
                                $ct_cate = '주문';
                                $list = $rows->take(1);   // 주문은 적립금사용 이라고 하고 금액만 표기
                            } else {
                                $list = $rows->take(3);
                            }

                            //취소는 주문취소로 표기 변경
                            if ($ct_cate == '취소') {
                                $ct_cate = '주문취소';
                            }                            

                            //결품채권, 반품채권은 >>>>  결품입금, 반품입금으로 표기 변경
                            if ($ct_cate == '결품채권') {
                                $ct_cate = '결품입금';
                            }

                            if ($ct_cate == '반품채권') {
                                $ct_cate = '반품입금';
                            }

                            
                            $rowspan = $list->count() + ($rows->count() > 3 ? 1 : 0);
                        @endphp

                        @foreach($list as $loopIndex => $row)

                            <tr>

                            @if($loop->first)
                                <td rowspan="{{ $rowspan }}" class="bg">{{ $ct_cate }}</td>
                            @endif

                            {{-- 주문은 적립금사용 이라고 하고 금액만 표기 --}}
                            @if($ct_cate == '주문')
                                <td class="h40">적립금 사용</td>
                                <td>-</td>
                                <td>{{ number_format($row->pt_reserve) }}원</td>
                            @else
                                <td>
                                    <div class="pp-flex">
                                        <img src="/images/item/{{ $row->it_img1 }}" alt="">
                                        <p>{{ $row->it_name }} <span class="hide-680">({{ $row->it_basic }})</span></p>
                                    </div>
                                </td>
                                <td>{{ $row->ct_qty }}</td>
                                <td>{{ number_format($row->pt_sales) }}원</td>
                            @endif

                            </tr>

                        @endforeach

                        @if($rows->count() > 3 && $ct_cate != '주문')

                            <tr>
                                <td class="txt-center">외 {{ $rows->count()-3 }}건</td>
                                <td></td>
                                <td>{{ number_format($rows->skip(3)->sum('pt_sales')) }}원</td>
                            </tr>

                        @endif

                    @endforeach
                @endif

            </table>
        </div>
    </div>


</body>
</html>