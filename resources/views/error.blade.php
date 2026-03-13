<!DOCTYPE html>
<html lang="ko">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>@yield('title', '삼마몰')</title>
	<link rel="stylesheet" href="{{ asset('css/slick.css') }}">
	<link rel="stylesheet" href="{{ asset('css/common.css') }}">
	<link rel="stylesheet" href="{{ asset('css/main.css') }}">
	<script src="{{ asset('js/jquery-3.7.1.min.js') }}"></script>
	<script src="{{ asset('js/slick.min.js') }}"></script>
	<script src="{{ asset('js/common.js') }}"></script>
	<script src="{{ asset('js/mall.js') }}"></script>
</head>
<body>
    {{ $message }}
</body>
</html>