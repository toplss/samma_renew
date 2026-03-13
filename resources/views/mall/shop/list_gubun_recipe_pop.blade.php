<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="{{ asset('css/common.css') }}">
  <link rel="stylesheet" href="{{ asset('css/sub.css') }}">
	<script src="{{ asset('js/jquery-3.7.1.min.js') }}"></script>
  <style>
    @media print {.flex-center { display: none !important;}}
  </style>
</head>
<body>
  <div class="pop-wrap" style="margin-bottom:1rem;">
    
  @foreach ($items as $key => $row)
    <div class="recipe-view-wrap">
      <div class="recipe-title">{{ $row->title }}</div>
      <div class="recipe-detail">
        <!-- img -->
        <div class="recipe-view-img">
        @php
        $content = $row->contents;

        $content = str_replace(
            'src="/smarteditor/upload',
            'src="https://samma-erp.com/smarteditor/upload',
            $content
        );

        echo $content;
        @endphp
        </div>
        <!-- img -->

        <!-- recipe-table-box -->
        <div class="recipe-table-box">
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
              $infomation = explode('||', $row->{'etc'.$i});
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
                <td>{{ number_format(preg_replace('/[^0-9]/', '', $row->etc_sub)) }}</td>
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
              <td>{{ number_format($row->cost) }}</td>
              <td>{{ number_format($row->price) }}</td>
              <td>{{ number_format($row->profit_rate) }}</td>
            </tr>
          </table>

          <p style="color:#da2727; text-align:right;">※ 일부 금액은 상황에 따라 변경될 수 있습니다.</p>
        </div>
        <!-- recipe-table-box -->

        <ul class="rd-table3">
          <li>레시피</li>
          <li>
            {!! nl2br(e($row->etc)) !!}
          </li>
        </ul>

      </div>
      <!-- end detail -->
    </div>
    <!-- end recipe-view-wrap -->

  @endforeach
    <div class="flex-center" style="gap:0.5rem; margin-top:1rem;">
      <button class="btn3 big-btn" onclick="window.print()">프린트</button>
      <button class="btn1 big-btn" onclick="window.close();">닫기</button>
    </div>
  </div>
</body>
</html>





