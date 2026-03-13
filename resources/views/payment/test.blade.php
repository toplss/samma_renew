<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>결제 테스트 시작</title>
</head>
<body>

<h2>결제 모듈 동작 테스트</h2>

<p>
    버튼을 누르면 이니시스 결제 요청 페이지로 이동합니다.
</p>

<a href="{{ route('payment.request') }}">
    <button>결제 요청</button>
</a>

</body>
</html>
