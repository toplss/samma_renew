
@if(!empty($duplicate))
<script>
    const message = '중복 로그인이 감지되었습니다.<br>중복 로그인이 계속 감지될 경우 비밀번호를 변경해 주세요.';
    const mb_code = "{{ session('ss_mb_code') }}"; 

    Swal.fire({
        icon: 'info',
        title: '알림',
        html: message,
        confirmButtonText: '확인'
    }).then(function(result){
        if (result.isConfirmed) {
            $.post("{{ route('duplicate_login_check') }}", {
                check : true,
                mb_code : mb_code,
            });
        }
    });
</script>
@endif


@if(session('duplicate_alert'))
<script>
    const message = {!! json_encode(session('duplicate_alert')) !!};
    Swal.fire({
        icon: 'info',
        title: '알림',
        html: message,
        confirmButtonText: '확인'
    });
</script>
@endif


@if(session('error'))
<script>
    const message = {!! json_encode(session('error')) !!};
    Swal.fire({
		toast: true,
        icon: 'info',
        title: '오류',
        html: message,
        confirmButtonText: '확인'
    });
</script>
@endif

@if(session('success'))
<script>
    const message = {!! json_encode(session('success')) !!};
    Swal.fire({
		toast: true,
        icon: 'success',
        title: '성공',
        html: message,
        confirmButtonText: '확인'
    });
</script>
@endif

@if(session('info'))
<script>
    const message = {!! json_encode(session('info')) !!};
    Swal.fire({
		toast: true,
        icon: 'info',
        title: '알림',
        html: message,
        confirmButtonText: '확인'
    });
</script>
@endif

@if($errors->any())
<script>
    const errorHtml = {!! json_encode(implode('<br>', $errors->all())) !!};

    Swal.fire({
		toast: true,
        icon: 'info',
        title: '입력 오류',
        html: errorHtml,
        confirmButtonText: '확인'
    });
</script>
@endif
