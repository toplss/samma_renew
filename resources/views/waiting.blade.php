<!doctype html>
<html lang="ko">
<head>
<meta charset="utf-8">
<title>접속 대기중</title>

<meta name="viewport" content="width=device-width, initial-scale=1">

<style>
body {
    font-family: Arial, sans-serif;
    text-align: center;
    padding-top: 120px;
}
</style>

<script>
setTimeout(function () {
    location.reload();
}, 3000);
</script>

</head>
<body>

<h2>접속 대기중</h2>

<p>현재 약 {{ $waiting }} 명이 대기중입니다</p>

<p>잠시 후 자동으로 다시 시도됩니다.</p>


</body>
</html>
