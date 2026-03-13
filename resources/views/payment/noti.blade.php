<h3>{{ $message }}</h3>
<p>결제 완료 후 자동으로 결과 페이지로 이동합니다.</p>



<script>
    // 5초 후 사용자 화면을 주문/결과 페이지로 이동
    setTimeout(function() {
        window.location.href = "{{ route('/') }}";
    }, 5000);
</script>
