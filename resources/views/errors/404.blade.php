<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>페이지 오류</title>
    <style>
        @font-face {
            font-family: 'Pretendard Variable';
            font-weight: 45 920;
            font-style: normal;
            font-display: swap;
            src: url('/fonts/PretendardVariable.woff2') format('woff2-variations');
        }
        .error-wrap{display: flex; flex-direction: column; gap: 2rem; align-items: center; justify-self: center; margin-top: 10vh; text-align: center; font-family: 'Pretendard Variable';}
        .error-ballon{position: relative;}
        .error-ballon img{width: 340px;}
        .error-ballon h1{position: absolute; top: 25%; left: 50%; transform: translate(-50%,-50%); color: #fff; font-size: 6rem;}
        .error-ballon span{display: block; font-size: 1.8rem;}
        .error-wrap p>img{width: 1.5rem; vertical-align: middle; margin-right: 3px;}
        .error-wrap p{color: #999; margin: auto;}
        .error-wrap a{font-size: 1.2rem; background-color: #333; color: #fff; padding: 0.8rem 4rem; border-radius: 0.8rem; text-decoration: none;}

        @media screen and (max-width: 680px){
            .error-ballon img{width: 260px;}
            .error-ballon h1{font-size:4.8rem; top: 22%;}
            .error-ballon span{font-size: 1.5rem;}
            .error-wrap a{font-size: 1.1rem;}
        }
    </style>
</head>
<body>

    <div class="error-wrap">
        <div class="error-ballon">
            <img src="{{ asset('images/icon/error.png') }}">
            <h1>404<span>ERROR</span></h1>
        </div>

        <p>
            <img src="{{ asset('images/icon/tool.svg') }}">
            존재하지 않는 페이지 입니다.<br>다시 확인해 주세요.
        </p>

        <a href="{{ url('/') }}">홈으로 이동</a>
    </div>

</body>
</html>