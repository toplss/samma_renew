<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>점검 중</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 96vh;
            background-color: #f5f5f5;
            color: #333;
            text-align: center;
        }
    </style>
</head>
<body>
    <div>
        <img src="{{ asset('images/icon/flag.svg') }}" width="120px">
        <h1>일시적으로 접속이 지연되고 있습니다</h1>
        <p>이용자가 많아 서비스 이용이 원활하지 않을 수 있습니다.<br>잠시 후 다시 시도해 주세요.</p>
    </div>
</body>
</html>
