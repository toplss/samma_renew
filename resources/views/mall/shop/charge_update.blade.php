@if($od_id) 
@php
    header("Location: https://pay.sammamall.com/mall/shop/list_charge_orderform.php?od_id=" . urlencode($od_id));
    exit;
@endphp
<!DOCTYPE html>
<html>
<head>
    <title>결제 이동 중</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            text-align: center;
            padding-top: 80px;
            color: #333;
            background-color: #f9f9f9;
        }
        h1 {
            font-size: 18px;
            font-weight: normal;
            margin-bottom: 20px;
        }
        .loader {
            border: 6px solid #e0e0e0;
            border-top: 6px solid #3498db;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }
        @keyframes spin {
            0% { transform: rotate(0deg);}
            100% { transform: rotate(360deg);}
        }
    </style>
</head>
<body>
    <h1>결제 페이지로 이동 중입니다…</h1>
    <div class="loader"></div>
    <script>
        location.href = "https://pay.sammamall.com/mall/shop/list_charge_orderform.php?od_id=" + encodeURIComponent("{{ $od_id }}");
    </script>
</body>
</html>
@endif
