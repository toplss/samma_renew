@extends('layouts.header')

@section('content')

<div class="sub-container">
  <div class="sub-title-wrap">
    <h4>회원정보수정</h4>
  </div>

  <div class="regi-wrap">

    <form name="member_form" id="member_form" method="post" enctype="multipart/form-data" action="{{ route('register_edit_save') }}">      
    @csrf
      <input type="hidden" name="account_file_changed" id="account_file_changed" value="0">

      <p>&#42; 필수 입력사항 입니다.</p>

      <table class="table1 regi-table1">
        <tr>
          <th><sup>&#42;</sup>아이디</th>
          <td>{{ $items['member']->mb_id }}</td>
        </tr>
        <tr>
          <th><sup>&#42;</sup>비밀번호</th>
          <td>
            <input type="password" name="user_pass" id="user_pass" placeholder="비밀번호 변경시 입력" maxlength="25" autocomplete="new-password">
            <span class="txt-red">&#42;영문, 숫자, 특수문자를 혼용하여 4~20자 이내</span>
          </td>
        </tr>
        <tr>
          <th><sup>&#42;</sup>비밀번호 확인</th>
          <td>
            <input type="password" name="user_pass_confirm" id="user_pass_confirm" placeholder="비밀번호 변경시 재입력">
          </td>
        </tr>
        <tr>
          <th><sup>&#42;</sup>이름</th>
          <td>
            <input type="text" name="mb_name" id="mb_name" value="{{ $items['member']->mb_name }}" placeholder="이름">
          </td>
        </tr>
        <tr>
          <th><sup>&#42;</sup>성별</th>
          <td>
            <label>
              <input type="radio" name="mb_sex" id="mb_sex" class="sex1" value="M" {{ $items['member']->mb_sex === 'M' ? 'checked' : '' }}>남
            </label>
            <label>
              <input type="radio" name="mb_sex" id="mb_sex" class="sex2" value="F" {{ $items['member']->mb_sex === 'F' ? 'checked' : '' }}>여
            </label>
          </td>
        </tr>
        <tr>
          <th><sup>&#42;</sup>생년월일</th>
          <td>
            <input type="text" name="mb_birth" id="mb_birth" value="{{ $items['member']->mb_birth }}" class="datepicker" readonly>
          </td>
        </tr>
        <tr>
          <th><sup>&#42;</sup>이메일</th>
          <td>
            <input type="text" name="email1" id="email1" value="{{ $items['form_data']['mb_email'][0] }}"> &#64; 
            <input type="text" name="email2" id="email2" value="{{ $items['form_data']['mb_email'][1] }}">
            <select name="email_select" id="email_select">
              <option value="">선택</option>
              <option value="naver.com">naver.com</option>
              <option value="hanmail.net">hanmail.net</option>
              <option value="nate.com">nate.com</option>
              <option value="gmail.com">gmail.com</option>
              <option value="direct">직접입력</option>
            </select>
          </td>
        </tr>
        <tr>
          <th><sup>&#42;</sup>주소</th>
          <td>
            <p>{{ $items['member']->mb_addr1 }} {{ $items['member']->mb_addr2 }}</p>
            <span class="txt-red">원활한 배송을 위해 주소 변경시 고객센터 (1899-3153)로 연락주시기 바랍니다.</span>
          </td>
        </tr>
        <tr>
          <th><sup>&#42;</sup>휴대폰번호</th>
          <td>
            <input type="text" name="mb_hp1" id="mb_hp1" value="{{ $items['form_data']['mb_hp'][0] }}"> &#45;
            <input type="text" name="mb_hp2" id="mb_hp2" value="{{ $items['form_data']['mb_hp'][1] }}"> &#45;
            <input type="text" name="mb_hp3" id="mb_hp3" value="{{ $items['form_data']['mb_hp'][2] }}">
          </td>
        </tr>
        <tr>
          <th><sup>&#42;</sup>전화번호</th>
          <td>
            <input type="text" name="mb_tel1" id="mb_tel1" maxlength="4" class="middle" value="{{ $items['form_data']['mb_tel'][0] }}"> &#45;
            <input type="text" name="mb_tel2" id="mb_tel2" maxlength="4" class="middle" value="{{ $items['form_data']['mb_tel'][1] }}"> &#45;
            <input type="text" name="mb_tel3" id="mb_tel3" maxlength="4" class="middle" value="{{ $items['form_data']['mb_tel'][2] }}">
          </td>
        </tr>
        <tr>
          <th><sup>&#42;</sup>사업자 등록번호</th>
          <td>
            {{ $items['member']->mb_company_no }}
          </td>
        </tr>
        <tr>
          <th><sup>&#42;</sup>상호명</th>
          <td>
            <p>{{ $items['member']->mb_company_real }}</p>
            <span class="txt-red">상호명 변경시 고객센터 (1899-3153) 로 연락주시기 바랍니다.</span> 
          </td>
        </tr>
        <tr>
          <th>업태</th>
          <td>
            <input type="text" name="mb_job" id="mb_job" value="{{ $items['member']->mb_job }}" placeholder="업태">
          </td>
        </tr>
        <tr>
          <th>종목</th>
          <td>
            <input type="text" name="mb_product" id="mb_product" value="{{ $items['member']->mb_product }}" placeholder="종목">
          </td>
        </tr>
        <tr>
          <th><sup>&#42;</sup>마이페이지 비밀번호</th>
          <td>
            <input type="password" name="mb_mypage_password" id="mb_mypage_password" value="" placeholder="마이페이지 비밀번호 변경시 입력">
            <input type="checkbox" id="chk_password" value="1" onchange="show_password(this);">
            <label for="chk_password">보이기</label>
            <span> (매출데이터 및 관리자 보안사항 열람을 위한 비밀번호입니다.)</span>
          </td>
        </tr>
        <tr>
          <th><sup>&#42;</sup>가맹점유형</th>
          <td>
            <select name="franchise_ca_id" id="franchise_ca_id" onchange="javascript:franchise_sub_category('4', $('#franchise_ca_id').val());">
              <option value="">1차 분류</option>
              @foreach($items['franchise_category'] as $key => $row)
              <option value="{{ $row->ca_id }}" {{ $row->ca_id == $items['member']->franchise_ca_id ? 'selected' : '' }}>{{ $row->ca_name }}</option>
              @endforeach
            </select>
            <select name="franchise_ca_id2" id="franchise_ca_id2">
              <option value="">2차 분류</option>
            </select>
          </td>
        </tr>
        <tr>
          <th><sup>&#42;</sup>점장 이름</th>
          <td>
            <input type="text" name="manager_name" id="manager_name" value="{{ $items['member']->manager_name }}" placeholder="이름">
            <label><input type="checkbox" name="sel_same_info" id="sel_same_info" value="1">회원정보와 동일</label>
          </td>
        </tr>
        <tr>
          <th><sup>&#42;</sup>문자 수신자</th>
          <td>
            <label><input type="radio" name="mb_notice_receive" id="mb_notice_receive" value="1" {{ $items['member']->mb_notice_receive == 1 ? 'checked' : '' }}>점주</label>
            <label><input type="radio" name="mb_notice_receive" id="mb_notice_receive" value="2" {{ $items['member']->mb_notice_receive == 2 ? 'checked' : '' }}>점장</label>
          </td>
        </tr>
        <tr>
          <th><sup>&#42;</sup>점장 휴대폰번호</th>
          <td>
            <input type="text" name="manager_tel21" id="manager_tel21" value="{{ $items['form_data']['manager_tel2'][0] }}"> &#45;
            <input type="text" name="manager_tel22" id="manager_tel22" value="{{ $items['form_data']['manager_tel2'][1] }}"> &#45;
            <input type="text" name="manager_tel23" id="manager_tel23" value="{{ $items['form_data']['manager_tel2'][2] }}">
          </td>
        </tr>
        <tr>
          <th>비바쿡 채널 추가</th>
          <td>
            <label><input type="radio" name="mb_add_channel" id="mb_add_channel" class="add_channel1" value="1" {{ $items['member']->mb_add_channel == 1 ? 'checked' : '' }}>
            추가</label>
            <label><input type="radio" name="mb_add_channel" id="mb_add_channel" class="add_channel2" value="0" {{ $items['member']->mb_add_channel == 0 ? 'checked' : '' }}>
            미추가</label>
            {{-- 카카오 채널버튼 --}}
            <div id="add-channel-button"></div>
            <span class="txt-blue">(비바쿡 광고와 마케팅 메시지를 카카오톡으로 받아 볼 수 있습니다.)</span>
          </td>
        </tr>
      </table>

      <table class="table1 regi-table2">
        <tr>
          <th colspan="2">환급계좌 정보</th>
        </tr>
        <tr>
          <th><sup>&#42;</sup>은행</th>
          <td>
            @php
              $bank_codes = config('constants.bank_codes');
            @endphp                        
            <select name="mb_bank" id="mb_bank">
              <option value="">선택</option>
              @foreach($bank_codes as $bank_key => $bank_name)
                <option value="{{ $bank_key }}/{{ $bank_name }}" {{ $bank_key == $items['member']->mb_bank_code ? 'selected' : '' }}>{{ $bank_name }}</option>
              @endforeach              
            </select>
          </td>
        </tr>
        <tr>
          <th><sup>&#42;</sup>계좌번호</th>
          <td>
            <input type="text" name="mb_account" id="mb_account" value="{{ $items['member']->mb_account }}" placeholder="계좌번호">
          </td>
        </tr>
        <tr>
          <th><sup>&#42;</sup>예금주</th>
          <td>
            <input type="text" name="mb_account_holder" id="mb_account_holder" value="{{ $items['member']->mb_account_holder }}" placeholder="예금주">
          </td>
        </tr>
        @php 
          $account_file_k = "1";
          $mb_account_files1 = unserialize($items['member']->mb_account_files1);
          $account_file_info_txt1 = $mb_account_files1[0]['name'];
          $account_file_hits = $mb_account_files1[0]['hits'];
        @endphp        
        <tr>
          <th><sup>&#42;</sup>통장사본</th>
          <td>
            <input 
              type="file" 
              name="mb_account_files{{ $account_file_k }}[]" 
              id="account_file_info{{ $account_file_k }}" 
              style="display:none;" 
              onchange="document.getElementById('account_file_info_txt{{ $account_file_k }}').value=this.value; $('#account_file_changed').val(1);" 
              accept="image/*">            
            <input type="text" id="account_file_info_txt{{ $account_file_k }}" value="{{ $account_file_info_txt1 }}" placeholder="통장사본" readonly>
            <button type="button" class="btn3" onclick="document.getElementById('account_file_info{{ $account_file_k }}').click();">파일선택</button>
            <label><input type="checkbox" class="reset_account_file" id="reset_account_file" data-key="{{ $account_file_k }}">선택초기화</label>
            <button type="button" class="btn2" onclick="DownloadAccountFile({{ $items['member']->mb_code }})">다운로드({{ $account_file_hits }}회)</button>
          </td>
        </tr>
      </table>

      <table class="table1 regi-table3">
        {{-- <tr>
          <th>프로필 사진</th>
          <td>
            <input type="file" name="userfile[]" id="mb_file1" style="display:none;">
            <input type="text" id="mb_txt1" placeholder="프로필 사진" readonly>
            <button type="button" class="btn3">파일선택</button>
            <label><input type="checkbox">선택초기화</label>
          </td>
        </tr> --}}
        <tr>
          <th>SMS 수신</th>
          <td>
            <label><input type="radio" name="mb_sms" id="mb_sms" class="sms1" value="1" {{ $items['member']->mb_sms == 1 ? 'checked' : '' }}>수신</label>
            <label><input type="radio" name="mb_sms" id="mb_sms" class="sms2" value="0" {{ $items['member']->mb_sms == 0 ? 'checked' : '' }}>미수신</label>
          </td>
        </tr>
        <tr>
          <th>메일 수신</th>
          <td>
            <label><input type="radio" name="mb_mailling" id="mb_mailling" class="mailling1" value="1" {{ $items['member']->mb_mailling == 1 ? 'checked' : '' }}>수신</label>
            <label><input type="radio" name="mb_mailling" id="mb_mailling" class="mailling2" value="0" {{ $items['member']->mb_mailling == 0 ? 'checked' : '' }}>미수신</label>
          </td>
        </tr>
        <tr>
          <th>남기는 말씀</th>
          <td>
            <textarea name="mb_introduce" id="mb_introduce" placeholder="내용을 입력해주세요">{{ $items['member']->mb_introduce }}</textarea>
          </td>
        </tr>
      </table>

      <div class="regi-btn">
        <button type="button" class="btn3 big-btn" onclick="javascript:chkForm(); return false;">수정완료</button>
        <button type="button" class="btn1 big-btn" onclick="location.href='/'">취소</button>
      </div>

    </form>
  </div>

</div>


<script>
  $(document).ready(function(){

    // 이메일 도메인 처리
    $('#email_select').on('change', function() {

        const value = $(this).val();

        if (value === 'direct' || value === '') {
            $('#email2').val('').prop('readonly', false).focus();
        } else {
            $('#email2').val(value).prop('readonly', true);
        }
    });


    // 가맹점유형 세팅
    franchise_sub_category('4', $('#franchise_ca_id').val());


    // 점장 정보 동기화 처리
    $("#sel_same_info").on("change", function() {
      if ($(this).is(":checked")) {
          $("#manager_name").val($("#mb_name").val());
          $("#manager_tel21").val($("#mb_hp1").val());
          $("#manager_tel22").val($("#mb_hp2").val());
          $("#manager_tel23").val($("#mb_hp3").val());
      } else {
          $("#manager_name").val("");
          $("#manager_tel21").val("");
          $("#manager_tel22").val("");
          $("#manager_tel23").val("");
      }
    });    


    // 통장사본 파일첨부 초기화
    $('.reset_account_file').on('change', function () {
        const key = $(this).data('key');

        $('#account_file_info' + key).val('');
        $('#account_file_info_txt' + key).val('');

        // 
        $('#account_file_changed').val(1);

        // 체크박스 버튼처럼 사용
        $(this).prop('checked', false);
    });    


    // 비바쿡 카카오 관련
    const kakaoKey = '63ad8e87a6c8ebf15e9c781dc56fb094';
    Kakao.init(kakaoKey);

    Kakao.Channel.createAddChannelButton({
      container: '#add-channel-button',
      channelPublicId: '_xcXasT',
    });

    function addPlusFriend() {
      Kakao.PlusFriend.addFriend({
        plusFriendId: '_xcXasT'
      });
    }    







  }); 


  // 가맹점유형 하위분류
  function franchise_sub_category(ca_id_length, ca_id){
    $.ajax({
      url : "{{ route('franchise_sub_category') }}",
      type : "GET",
      data : {
        "ca_id_length" : ca_id_length,
        "ca_id" : ca_id
      },
      dataType : "json",
      success : function(result) {

        var options = '<option value="">2차 분류</option>';

        $.each(result, function(index, item) {

          const selectedCaId2 = "{{ $items['member']->franchise_ca_id2 ?? '' }}";
          const selected = (item.ca_id == selectedCaId2) ? ' selected' : '';

          options += '<option value="' + item.ca_id + '"' + selected + '>'
                  + item.ca_name +
                  '</option>';
        });

        $('#franchise_ca_id2').html(options);      

      },
      error: function(e) {
            console.log('에러 발생:', e);
      }
    });
  }

  // 통장사본 파일 다운로드
  function DownloadAccountFile(mbCode) {
    if (!mbCode) {
        alert('잘못된 접근입니다.');
        return false;
    }

    // GET 라우터 호출
    location.href = 'download_account_file?mb_code=' + mbCode;    

  }



  // 마이페이지 비밀번호 보이기
  function show_password(el) {
    var checked = el.checked;
    if(checked) {
      document.getElementById("mb_mypage_password").type = "text";
      document.getElementById("show_text").textContent = "감추기";
    } else {
      document.getElementById("mb_mypage_password").type = "password";
      document.getElementById("show_text").textContent = "보이기";
    }
  }  
  
  //폼 체크
  function chkForm() {

    if ($("#user_pass").val()) {

      if ($("#user_pass").val().length < 4 || $("#user_pass").val().length > 20) {
        validationAlertMessage('비밀번호는 4~20자 이내 입력되어야 합니다.');
        $("#user_pass").focus();
        return false;
      }      

      if ($("#user_pass_confirm").val() == "") {
        validationAlertMessage('비밀번호 확인란을 입력해주세요.');
        $("#user_pass_confirm").focus();
        return false;
      }

      if ($("#user_pass").val() != $("#user_pass_confirm").val()) {
        validationAlertMessage('입력하신 비밀번호와 <br>비밀번호 확인이 일치하지 않습니다.');
        $("#user_pass_confirm").focus();
        return false;
      }    

    }



    if ($("#mb_name").val() == "") {
      validationAlertMessage('이름을 입력해주세요.');
      $("#mb_name").focus();
      return false;
    }

    if ($("#email1").val() == "") {
        validationAlertMessage('이메일 아이디를 입력해주세요.');
        $('#email1').focus();
        return false;
    }

    if ($("#email2").val() == "") {
        validationAlertMessage('이메일 도메인을 입력하거나 선택해주세요.');
        $('#email2').focus();
        return false;
    }

    const email = $("#email1").val() + '@' + $("#email2").val();
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (!emailRegex.test(email)) {
        validationAlertMessage('올바른 이메일 형식이 아닙니다.');
        $('#email1').focus();
        return false;
    }    

    if ($("#mb_hp1").val() == "") {
      validationAlertMessage("휴대폰번호를 입력해 주세요.");
      $("#mb_hp1").focus();
      return false;
    }

    if ($("#mb_hp2").val() == "") {
      validationAlertMessage("휴대폰번호를 입력해 주세요.");
      $("#mb_hp2").focus();
      return false;
    }

    if ($("#mb_hp3").val() == "") {
      validationAlertMessage("휴대폰번호를 입력해 주세요.");
      $("#mb_hp3").focus();
      return false;
    }

    if ($("#mb_company").val() == "") {
      validationAlertMessage("상호명을 입력해 주세요.");
      $("#mb_company").focus();
      return false;
    }

    if($("#franchise_ca_id").val()==""){
      validationAlertMessage("가맹점유형을 선택해 주세요.");
      $("#franchise_ca_id").focus();
      return false;
    }

    if($('input[id="mb_notice_receive"]:checked').val() == "2") {
      
      if($("#manager_name").val() == ""){
        validationAlertMessage("점장 이름을 입력해 주세요.");
        $("#manager_name").focus();
        return false;
      }

      if($("#manager_tel21").val() == ""){
        validationAlertMessage("점장 휴대폰번호를 선택해 주세요.");
        $("#manager_tel21").focus();
        return false;
      }

      if($("#manager_tel22").val() == ""){
        validationAlertMessage("점장 휴대폰번호를 입력해 주세요.");
        $("#manager_tel22").focus();
        return false;
      }

      if($("#manager_tel23").val() == ""){
        validationAlertMessage("점장 휴대폰번호를 입력해 주세요.");
        $("#manager_tel23").focus();
        return false;
      }

    }


    Swal.fire({
        title: '회원정보 수정',
        text: '회원정보를 수정 하시겠습니까?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: '확인',
        cancelButtonText: '취소'
    }).then((result) => {
        if (result.isConfirmed) {
            $("#member_form").submit();
        }
    });      

  }  



  function validationAlertMessage(message)  {
    Swal.fire({
        toast: true,
        icon: 'warning',
        title: '알림',
        html: message,
        confirmButtonText: '확인'
    });
  }

  $( ".datepicker" ).datepicker({
    dateFormat: 'yy-mm-dd',
    showMonthAfterYear:true,
    monthNamesShort: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
    monthNames: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
    dayNamesMin: ['일','월','화','수','목','금','토'], //달력의 요일 텍스트
    dayNames: ['일요일','월요일','화요일','수요일','목요일','금요일','토요일'],
    changeYear: true,
    changeMonth: true,
    showButtonPanel: true,
    closeText: "닫기",
    yearRange: "c-100:c",
    maxDate: 0
  });

  // $('.datepicker').datepicker('setDate', 'today'); //오늘날짜 기본설정


</script>

@endsection
