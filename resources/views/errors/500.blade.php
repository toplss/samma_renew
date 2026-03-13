<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>서버 오류</title>
    <style>
        body{
            font-family: Arial;
            text-align: center;
            margin-top: 100px;
        }
        h1{
            font-size: 60px;
        }
    </style>
</head>
<body>

<h1>500</h1>
<p>서버에서 문제가 발생했습니다.</p>
<p>잠시 후 다시 시도해주세요.</p>

<a href="{{ url('/') }}">홈으로 이동</a>

</body>
</html>