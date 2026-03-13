@extends('layouts.header')

@section('content')

<style>
#layer {z-index: 1000 !important; /* 필요한 높이에 따라 조절 */}
</style>

<div id="layer" style="margin-top:5px; display:none;position:fixed;overflow:hidden;z-index:1;-webkit-overflow-scrolling:touch;">
	<img src="//i1.daumcdn.net/localimg/localimages/07/postcode/320/close.png" id="btnCloseLayer" style="width:18px; cursor:pointer;position:absolute;right:-3px;top:-3px;z-index:1" onclick="closeDaumPostcode()" alt="닫기 버튼">
</div>

<div class="sub-container">
  <div class="sub-title-wrap">
    <h4>회원가입</h4>
  </div>

  <div class="regi-wrap">

    <form name="member_form" id="member_form" method="post" enctype="multipart/form-data" action="{{ route('join_save') }}">
    @csrf

      <input type="hidden" name="mb_num" value="{{ $activeMember['mb_code'] ?? '' }}">
      <input type="hidden" name="mode" value="member_join_ok" />
      <input type="hidden" name="mb_level" id="mb_level" value="22" />
      <input type="hidden" name="mb_sms" id="mb_sms" value="1" />
      <input type="hidden" name="mb_mailling" id="mb_mailling" value="1" />
      <input type="hidden" name="mb_notice_talk" id="mb_notice_talk" value="1" />
      <input type="hidden" name="mb_cs_bill" value="3" />
      <input type="hidden" name="de_type2_list_use" id="de_type2_list_use" value="1" />
      <input type="hidden" name="de_type4_list_use" id="de_type4_list_use" value="1" />
      <input type="hidden" name="company_no_ok" id="company_no_ok" value="" />
      <input type="hidden" name="company_no_check" id="company_no_check" value="" />
      <input type="hidden" name="recom_check" id="recom_check" value="" />
      <input type="hidden" name="coupon_no_check" id="coupon_no_check" value="" />
      <input type="hidden" name="mb_login_type" value="mall">
      <input type="hidden" name="mb_level_type" value="1">    
      <input type="hidden" name="id_checked" id="id_checked" value="0">
      <input type="hidden" name="company_num_checked" id="company_num_checked" value="0">
      <input type="hidden" name="coupon_checked" id="coupon_checked" value="0">      

      <p>&#42; 필수 입력사항 입니다.</p>

      <table class="table1 regi-table1">
        <tr>
          <th><sup>&#42;</sup>아이디</th>
          <td>
            <input type="text" name="user_id" id="user_id" placeholder="아이디" maxlength="20">
            <button type="button" class="btn1" id="id_check" onclick="chkId();">중복체크</button>
          </td>
        </tr>
        <tr>
          <th><sup>&#42;</sup>비밀번호</th>
          <td>
            <input type="password" name="user_pass" id="user_pass" placeholder="비밀번호" maxlength="20" autocomplete="new-password">
            <span class="txt-red">&#42;영문, 숫자, 특수문자를 혼용하여 4~20자 이내</span>
          </td>
        </tr>
        <tr>
          <th><sup>&#42;</sup>비밀번호 확인</th>
          <td>
            <input type="password" name="user_pass_confirm" id="user_pass_confirm" placeholder="비밀번호 확인" maxlength="20">
          </td>
        </tr>
        <tr>
          <th><sup>&#42;</sup>이름</th>
          <td>
            <input type="text" name="mb_name" id="mb_name" value="" placeholder="이름" maxlength="10">
          </td>
        </tr>
        <tr>
          <th><sup>&#42;</sup>성별</th>
          <td>
            <label for="">
              <input type="radio" name="mb_sex" id="mb_sex" class="sex1" value="M" checked>
              남
            </label>
            <label for="">
              <input type="radio" name="mb_sex" id="mb_sex" class="sex2" value="F">
              여
            </label>
          </td>
        </tr>
        <tr>
          <th><sup>&#42;</sup>생년월일</th>
          <td>
            <input type="text" name="mb_birth" id="mb_birth" class="datepicker">
          </td>
        </tr>
        <tr>
          <th><sup>&#42;</sup>이메일</th>
          <td>
            <input type="text" name="email1" id="email1" value="" maxlength="20"> &#64; 
            <input type="text" name="email2" id="email2" value="" maxlength="20">
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
            <input type="text" name="mb_zip1" id="mb_zip1" onclick="javascript:openDaumPostcode_layer();" placeholder="우편번호" readonly>
            <button type="button" class="btn1" onclick="javascript:openDaumPostcode_layer();">주소검색</button>
            <br>
            <input type="text" name="mb_addr1" id="mb_addr1" onclick="javascript:openDaumPostcode_layer();" placeholder="주소" readonly>
						<input type="text" name="mb_addr2" id="mb_addr2" placeholder="상세 주소">
            <input type="hidden" name="mb_addr_jibeon" id="mb_addr_jibeon">
          </td>
        </tr>
        <tr>
          <th><sup>&#42;</sup>휴대폰번호</th>
          <td>
            <input type="text" name="mb_hp1" id="mb_hp1" class="mb_hp_inputs" value="" maxlength="3"> &#45;
            <input type="text" name="mb_hp2" id="mb_hp2" class="mb_hp_inputs" value="" maxlength="4"> &#45;
            <input type="text" name="mb_hp3" id="mb_hp3" class="mb_hp_inputs" value="" maxlength="4">
          </td>
        </tr>
        <tr>
          <th>전화번호</th>
          <td>
            <input type="text" name="mb_tel1" id="mb_tel1" class="mb_tel_inputs" value="" maxlength="3"> &#45;
            <input type="text" name="mb_tel2" id="mb_tel2" class="mb_tel_inputs" value="" maxlength="4"> &#45;
            <input type="text" name="mb_tel3" id="mb_tel3" class="mb_tel_inputs" value="" maxlength="4">
          </td>
        </tr>
        <tr>
          <th><sup>&#42;</sup>사업자 등록번호</th>
          <td>
            <input type="text" name="mb_company_no" id="mb_company_no" placeholder="사업자등록번호(숫자만 입력)" maxlength="12">
					<button type="button" id="chk_company_no" class="btn1" onclick="chkCompanyNo();">중복체크</button>
          </td>
        </tr>
        <tr>
          <th><sup>&#42;</sup>상호명</th>
          <td>
            <input type="text" name="mb_company" id="mb_company" placeholder="상호명" maxlength="20">
            <span>상호명 예시 : 홍길동PC방(수원.인계점)</span>
          </td>
        </tr>
        <tr>
          <th>업태</th>
          <td>
            <input type="text" name="mb_job" id="mb_job" value="" placeholder="업태" maxlength="20">
          </td>
        </tr>
        <tr>
          <th>종목</th>
          <td>
            <input type="text" name="mb_product" id="mb_product" value="" placeholder="종목" maxlength="20">
          </td>
        </tr>
        <tr>
          <th><sup>&#42;</sup>마이페이지 비밀번호</th>
          <td>
            <input type="password" name="mb_mypage_password" id="mb_mypage_password" value="" placeholder="마이페이지 비밀번호" maxlength="20">
            <input type="checkbox" id="chk_password" value="1" onchange="show_password(this);">
            <label id="show_text" for="chk_password">보이기</label>
            <span> (매출데이터 및 관리자 보안사항 열람을 위한 비밀번호입니다.)</span>
          </td>
        </tr>
        <tr>
          <th><sup>&#42;</sup>가맹점유형</th>
          <td>
            <select name="franchise_ca_id" id="franchise_ca_id" onchange="javascript:franchise_sub_category('4', $('#franchise_ca_id').val());">
              <option value="">1차 분류</option>
              @foreach($items['franchise_category'] as $key => $row)
              <option value="{{ $row->ca_id }}">{{ $row->ca_name }}</option>
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
            <input type="text" name="manager_name" id="manager_name" value="" placeholder="이름" maxlength="10">
            <label><input type="checkbox" name="sel_same_info" id="sel_same_info" value="1">회원정보와 동일</label>
          </td>
        </tr>
        <tr>
          <th><sup>&#42;</sup>문자 수신자</th>
          <td>
            <label><input type="radio" name="mb_notice_receive" id="mb_notice_receive" value="1" checked>점주</label>
            <label><input type="radio" name="mb_notice_receive" id="mb_notice_receive" value="2">점장</label>
          </td>
        </tr>
        <tr>
          <th><sup>&#42;</sup>점장 휴대폰번호</th>
          <td>
            <input type="text" name="manager_tel21" id="manager_tel21" class="manager_tel_inputs" value="" maxlength="3"> &#45;
            <input type="text" name="manager_tel22" id="manager_tel22" class="manager_tel_inputs" value="" maxlength="4"> &#45;
            <input type="text" name="manager_tel23" id="manager_tel23" class="manager_tel_inputs" value="" maxlength="4">
          </td>
        </tr>
        <tr>
          <th>비바쿡 채널 추가</th>
          <td>
            <label><input type="radio" name="mb_add_channel" id="mb_add_channel" class="add_channel1" value="1" checked>
            추가</label>
            <label><input type="radio" name="mb_add_channel" id="mb_add_channel" class="add_channel2" value="0">
            미추가</label>
            {{-- 카카오 채널버튼 --}}
            <div id="add-channel-button"></div>
            <span class="txt-blue">(비바쿡 광고와 마케팅 메시지를 카카오톡으로 받아 볼 수 있습니다.)</span>
          </td>
        </tr>
        <tr>
          <th>쿠폰번호</th>
          <td>
            <input type="text" name="coupon_no1" id="coupon_no1" class="coupon_inputs" value="" maxlength="4"> -
            <input type="text" name="coupon_no2" id="coupon_no2" class="coupon_inputs" value="" maxlength="4"> -
            <input type="text" name="coupon_no3" id="coupon_no3" class="coupon_inputs" value="" maxlength="4"> -
            <input type="text" name="coupon_no4" id="coupon_no4" class="coupon_inputs" value="" maxlength="4">
            <button type="button" id="chk_coupon_no" class="btn1" onclick="chkCouponNo();">중복체크</button>
            <span class="block txt-red" style="margin-top: 0.5rem;">영문 대소문자 구별하여 입력해 주세요.</span>
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
                <option value="{{ $bank_key }}/{{ $bank_name }}">{{ $bank_name }}</option>
              @endforeach
            </select>
          </td>
        </tr>
        <tr>
          <th><sup>&#42;</sup>계좌번호</th>
          <td>
            <input type="text" name="mb_account" id="mb_account" value="" placeholder="계좌번호" maxlength="50">
          </td>
        </tr>
        <tr>
          <th><sup>&#42;</sup>예금주</th>
          <td>
            <input type="text" name="mb_account_holder" id="mb_account_holder" value="" placeholder="예금주" maxlength="20">
          </td>
        </tr>
        @php 
          $account_file_k = "1";
        @endphp
        <tr>
          <th><sup>&#42;</sup>통장사본</th>
          <td>
            <input type="file" 
                  name="mb_account_files{{ $account_file_k }}[]" 
                  id="account_file_info{{ $account_file_k }}" 
                  style="display:none;" 
                  onchange="document.getElementById('account_file_info_txt{{ $account_file_k }}').value=this.value; compressReturnImage(this, '#account_file_info_txt{{ $account_file_k }}');"
                  accept="image/*">
            <input type="text" id="account_file_info_txt{{ $account_file_k }}" placeholder="통장사본" readonly>
            <button type="button" class="btn1" onclick="document.getElementById('account_file_info{{ $account_file_k }}').click();">파일선택</button>
            <label><input type="checkbox" class="reset_account_file" id="reset_account_file" data-key="{{ $account_file_k }}">선택초기화</label>
          </td>
        </tr>
      </table>

      <table class="table1 regi-table3">
        <tr>
          <th>남기는 말씀</th>
          <td>
            <textarea name="mb_introduce" id="mb_introduce" rows="10" placeholder="내용을 입력해주세요"></textarea>
          </td>
        </tr>
      </table>

      <div class="regi-btn">
        <button type="button" class="btn3 big-btn" onclick="javascript:chkForm(); return false;">가입하기</button>
      </div>

    </form>
  </div>

</div>
<script>
  $(document).ready(function(){
    
    if (!getCookie('joinAgree')) {
      Swal.fire({
        toast: true,
        icon: 'warning',
        title: '알림',
        text: '가입약관 동의가 필요합니다.',
        confirmButtonText: '확인'
      }).then((result) => {
        if (result.isConfirmed) {
          location.href = '/join_step_agree';
        }
      });
    } else {
      // deleteCookie('joinAgree')
    }

    //한글 아이디 제한
    $("#user_id").on("input", function() {
        const value = this.value;
        // 영문 + 숫자만 허용
        if (!/^[a-zA-Z0-9]*$/.test(value)) {
            validationAlertMessage('아이디는 영문과 숫자만 입력 가능합니다.');
        }
    });  

    //사업자등록번호 숫자체크
    $("#mb_company_no").on("input", function () {
        const value = this.value;

        // 숫자만 허용
        if (!/^[0-9]*$/.test(value)) {
            validationAlertMessage('사업자등록번호는 숫자만 입력 가능합니다.');
            this.value = value.replace(/[^0-9]/g, '');
        }
    });    

    //아이디 중복체크 후 아이디 변경 시 인증 초기화
    $('#user_id').on('input', function() {
      $('#id_checked').val(0);
    });

    // //사업자등록번호 중복체크 후 번호 변경 시 인증 초기화
    // $('#mb_company_no').on('input', function() {
    //   $('#company_num_checked').val(0);
    // });    

    //쿠폰번호 중복체크 후 번호 변경 시 인증 초기화
    $('.coupon_inputs').on('input', function() {
      $('#coupon_checked').val(0);
    });

    //이메일 도메인 처리
    $('#email_select').on('change', function() {
        const value = $(this).val();

        if (value === 'direct' || value === '') {
            $('#email2').val('').prop('readonly', false).focus();
        } else {
            $('#email2').val(value).prop('readonly', true);
        }
    });

    //점장 정보 동기화 처리
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

    //포커스 이동
    $(".mb_hp_inputs").keyup(function () {
      if (this.value.length == this.maxLength) {
        $(this).next('.mb_hp_inputs').focus();
      }
    });

    $(".mb_tel_inputs").keyup(function () {
      if (this.value.length == this.maxLength) {
        $(this).next('.mb_tel_inputs').focus();
      }
    });

    $(".manager_tel_inputs").keyup(function () {
      if (this.value.length == this.maxLength) {
        $(this).next('.manager_tel_inputs').focus();
      }
    });

    $(".coupon_inputs").keyup(function () {
      if (this.value.length == this.maxLength) {
        $(this).next('.coupon_inputs').focus();
      }
    });


    //통장사본 파일첨부 초기화
    $('.reset_account_file').on('change', function () {
        const key = $(this).data('key');

        $('#account_file_info' + key).val('');
        $('#account_file_info_txt' + key).val('');

        // 체크박스 버튼처럼 사용
        $(this).prop('checked', false);
    });


    
    //비바쿡 카카오 관련
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


  //아이디 중복체크
  function chkId() {

    if ($("#user_id").val() == "") {
      validationAlertMessage('아이디를 입력해주세요.');
      $("#user_id").focus().select();
      // $("#contact_company").focus().select();
      return false;
    }    

    if (!/^[a-zA-Z0-9]*$/.test($("#user_id").val())) {
        validationAlertMessage('아이디는 영문과 숫자만 입력 가능합니다.');
        return false;
    }

    $.ajax({
      url : "{{ route('check_id') }}",
      type : "POST",
      data: {
        'user_id' : $("#user_id").val()
      },
      dataType : "json",
      success : function(result) {
        if(result.exists) {
          validationAlertMessage('이미 사용중인 아이디 입니다');
        }else{
          validationAlertMessage('사용 가능한 아이디 입니다');
          $("#id_checked").val(1);
        }
      },
       error: function(e) {
        let errors = e.responseJSON.errors;
        if(errors) {
          validationAlertMessage(errors.user_id[0]);
        }
      }
    });

  }

  //사업자등록번호 중복체크
  function chkCompanyNo() {
    $.ajax({
      url : "{{ route('check_company_no') }}",
      type : "POST",
      data: {
        'mb_company_no' : $("#mb_company_no").val()
      },
      dataType : "json",
      success : function(result) {
        if(result.exists) {
          validationAlertMessage('이미 등록 된 사업자 등록번호 입니다');
        }else{
          validationAlertMessage('등록 가능한 사업자 등록번호 입니다');
          $("#company_num_checked").val(1);
        }
      },
       error: function(e) {
        let errors = e.responseJSON.errors;
        if(errors) {
          validationAlertMessage(errors.mb_company_no[0]);
        }
      }
    });
  }  


  //쿠폰번호 중복체크
  function chkCouponNo() {

    if ($("#coupon_no1").val() == "") {
        validationAlertMessage('쿠폰번호를 입력해주세요.');
        $('#coupon_no1').focus();
        return false;
    }

    if ($("#coupon_no2").val() == "") {
        validationAlertMessage('쿠폰번호를 입력해주세요.');
        $('#coupon_no2').focus();
        return false;
    }

    if ($("#coupon_no3").val() == "") {
        validationAlertMessage('쿠폰번호를 입력해주세요.');
        $('#coupon_no3').focus();
        return false;
    }

    if ($("#coupon_no4").val() == "") {
        validationAlertMessage('쿠폰번호를 입력해주세요.');
        $('#coupon_no4').focus();
        return false;
    }

    $.ajax({
      url: "{{ route('check_coupon_no') }}",
      type: "POST",
      data: {
        coupon_no:
          $("#coupon_no1").val() + '-' +
          $("#coupon_no2").val() + '-' +
          $("#coupon_no3").val() + '-' +
          $("#coupon_no4").val()
      },
      dataType: "json",
      success: function (result) {
        
        validationAlertMessage(result.message);

        if (result.status === true) {
          $("#coupon_checked").val(1);
        } else {
          $("#coupon_checked").val(0);
        }
      },
      error: function (e) {
        if (e.responseJSON && e.responseJSON.errors) {
          validationAlertMessage(e.responseJSON.errors.coupon_no[0]);
        } else {
          validationAlertMessage('쿠폰번호 확인 중 오류가 발생했습니다.');
        }
      }
    });
  }


  //가맹점유형 하위분류
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
            options += '<option value="' + item.ca_id + '">' + item.ca_name + '</option>';
        });
        $('#franchise_ca_id2').html(options);      
      },
      error: function(e) {
            console.log('에러 발생:', e);
      }
    });
  }


  //추천인 체크
  function chkRecommendId(){

    $.ajax({
      url : "{{ route('check_recommend_id') }}",
      type : "POST",
      data: {
        'mb_recommend' : $("#mb_recommend").val()
      },
      dataType : "json",
      success : function(result) {

        validationAlertMessage(result.message);
        
      },
       error: function(e) {
        let errors = e.responseJSON.errors;
        if(errors) {
          validationAlertMessage(errors.mb_recommend[0]);
        }
      }
    });

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

    if ($("#user_id").val() == "") {
      validationAlertMessage("아이디를 입력해 주세요.", function() {
        $("#user_id").focus().select();
      });
      return false;
    }

    if ($("#user_id").val().length < 4 || $("#user_id").val().length > 20) {
      $("#user_id").blur();
      validationAlertMessage("아이디는 4~20자 이내 입력되어야 합니다.", function() {
        $("#user_id").focus().select();
      });
      return false;
    }

    if ($("#id_checked").val() != 1) {
      validationAlertMessage("아이디 중복체크를 완료해주세요.", function() {
        $("#id_check").focus().select();
      });      
      return false;
    }

    if ($("#user_pass").val() == "") {
      validationAlertMessage("비밀번호를 입력해주세요.", function() {
        $("#user_pass").focus().select();
      });            
      return false;
    }

    if ($("#user_pass").val().length < 4 || $("#user_pass").val().length > 20) {
      validationAlertMessage("비밀번호는 4~20자 이내 입력되어야 합니다.", function() {
        $("#user_pass").focus().select();
      });            
      return false;
    }

    if ($("#user_pass_confirm").val() == "") {
      validationAlertMessage("비밀번호 확인란을 입력해주세요.", function() {
        $("#user_pass_confirm").focus().select();
      });                  
      return false;
    }

    if ($("#user_pass").val() != $("#user_pass_confirm").val()) {
      validationAlertMessage("입력하신 비밀번호와 <br>비밀번호 확인이 일치하지 않습니다.", function() {
        $("#user_pass_confirm").focus().select();
      });                        
      return false;
    }

    if ($("#mb_name").val() == "") {
      validationAlertMessage("이름을 입력해주세요.", function() {
        $("#mb_name").focus().select();
      }); 
      return false;
    }

    // if (!$('input[id="mb_sex"]:checked').length) {
    //   validationAlertMessage('성별을 선택해주세요.');
    //   return false;
    // }

    // if ($("#mb_birth").val() == "") {
    //   validationAlertMessage('생년월일을 선택해주세요.');
    //   $("#mb_birth").focus();
    //   return false;
    // }

    if ($("#email1").val() == "") {
        validationAlertMessage('이메일 아이디를 입력해주세요.', function() {
          $('#email1').focus().select();
        });
        return false;
    }

    if ($("#email2").val() == "") {
        validationAlertMessage('이메일 도메인을 입력하거나 선택해주세요.', function() {
          $('#email2').focus().select();
        });
        return false;
    }

    const email = $("#email1").val() + '@' + $("#email2").val();
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (!emailRegex.test(email)) {
        validationAlertMessage('올바른 이메일 형식이 아닙니다.', function() {
          $('#email1').focus().select();
        });
        return false;
    }    

    if ($("#mb_addr1").val() == "") {
      validationAlertMessage("주소를 입력해 주세요.", function() {
        $("#mb_addr1").focus().select();
      });
      return false;
    }

    if($("#mb_addr2").val() == ""){
      validationAlertMessage("상세주소를 입력해 주세요.", function() {
        $("#mb_addr2").focus().select();
      });
      return false;
    }

    if ($("#mb_hp1").val() == "") {
      validationAlertMessage("휴대폰번호를 입력해 주세요.", function() {
        $("#mb_hp1").focus().select();
      });
      return false;
    }

    if ($("#mb_hp2").val() == "") {
      validationAlertMessage("휴대폰번호를 입력해 주세요.", function() {
        $("#mb_hp2").focus().select();
      });
      return false;
    }

    if ($("#mb_hp3").val() == "") {
      validationAlertMessage("휴대폰번호를 입력해 주세요.", function() {
        $("#mb_hp3").focus().select();
      });
      return false;
    }

    /*
    if ($("#mb_tel1").val() == "") {
      validationAlertMessage("전화번호를 입력해 주세요.");
      $("#mb_tel1").focus();
      return false;
    }

    if ($("#mb_tel2").val() == "") {
      validationAlertMessage("전화번호를 입력해 주세요.");
      $("#mb_tel2").focus();
      return false;
    }

    if ($("#mb_tel3").val() == "") {
      validationAlertMessage("전화번호를 입력해 주세요.");
      $("#mb_tel3").focus();
      return false;
    }
    */    

    // if ($("#mb_company_no").val() == "") {
    //   validationAlertMessage("사업자등록번호를 입력해 주세요.");
    //   $("#mb_company_no").focus();
    //   return false;
    // }


    // // 사업자등록번호 체크
    // var sum = 0;
    // var chkValue = new Array(10);
    // var businessNo = $("#mb_company_no").val();
    
    // for (var i = 0; i < 10; i++) {
    // 	chkValue[i] = parseInt(businessNo.charAt(i));
    // }

    // var multipliers = [1, 3, 7, 1, 3, 7, 1, 3];
    // for (i = 0; i < 8; i++) {
    // 	sum += (chkValue[i] *= parseInt(multipliers[i]));
    // }
    // var chkTemp = chkValue[8] * 5 + '0';
    // chkValue[8] = parseInt(chkTemp.charAt(0)) + parseInt(chkTemp.charAt(1));
    // var chkLastid = (10 - (((sum % 10) + chkValue[8]) % 10)) % 10;
    
    // if (chkValue[9] != chkLastid) {
    // 	validationAlertMessage("사업자등록번호가 올바르지 않습니다.");
    // 	return false;
    // }


    // if ($("#mb_company_no").val().length < 10 || $("#mb_company_no").val().length > 10) {
    //   alert("사업자등록번호가 올바르지 않습니다2.");
    //   $("#mb_company_no").focus();
    //   return false;
    // }

    // if ($("#company_num_checked").val() != 1) {
    //   validationAlertMessage('사업자등록번호 중복체크를 해주세요.');
    //   $("#mb_company_no").focus();
    //   return false;
    // }    

    if ($("#mb_company").val() == "") {
      validationAlertMessage("상호명을 입력해 주세요.", function() {
        $("#mb_company").focus().select();
      });
      return false;
    }

    /*
    if ($("#txt2").val() == ""){
      alert("사업자등록증을 업로드해 주세요.");
      $("#txt2").focus();
      return false;
    }
    */

    /*
    if ($("#mb_business_no").val() == "") {
      alert("통신판매업신고번호를 입력해 주세요.");
      $("#mb_business_no").focus();
      return false;
    }

    if ($("#txt3").val() == ""){
      alert("통신판매업신고증을 업로드해 주세요.");
      $("#txt3").focus();
      return false;
    }
    */

    /*
    if ($("#mb_job").val() == "") {
      validationAlertMessage("업태를 입력해 주세요.");
      $("#mb_job").focus();
      return false;
    }    

    if ($("#mb_product").val() == "") {
      validationAlertMessage("종목을 입력해 주세요.");
      $("#mb_product").focus();
      return false;
    }        

    */    

    if ($("#mb_mypage_password").val() == "") {
      validationAlertMessage("마이페이지 비밀번호를 입력해 주세요.", function() {
        $("#mb_mypage_password").focus().select();
      });
      return false;
    }

    if($("#franchise_ca_id").val()==""){
      validationAlertMessage("가맹점유형을 선택해 주세요.", function() {
        $("#franchise_ca_id").focus().select();
      });
      return false;
    }

    /*
    if($("#franchise_ca_id2").val()==""){
      alert("가맹점 2차 분류를 선택해 주세요.");
      $("#franchise_ca_id2").focus();
      return false;
    }

    if($("#franchise_ca_id3").val()==""){
      alert("가맹점 3차 분류를 선택해 주세요.");
      $("#franchise_ca_id3").focus();
      return false;
    }

    if($("#franchise_ca_id4").val()==""){
      alert("가맹점 4차 분류를 선택해 주세요.");
      $("#franchise_ca_id4").focus();
      return false;
    }
    */

    if($('input[id="mb_notice_receive"]:checked').val() == "2") {
      if($("#manager_name").val() == ""){
        validationAlertMessage("점장 이름을 입력해 주세요.", function() {
          $("#manager_name").focus().select();
        });
        return false;
      }

      if($("#manager_tel21").val() == ""){
        validationAlertMessage("점장 휴대폰번호를 선택해 주세요.", function() {
          $("#manager_tel21").focus().select();
        });
        return false;
      }

      if($("#manager_tel22").val() == ""){
        validationAlertMessage("점장 휴대폰번호를 입력해 주세요.", function() {
          $("#manager_tel22").focus().select();
        });
        return false;
      }

      if($("#manager_tel23").val() == ""){
        validationAlertMessage("점장 휴대폰번호를 입력해 주세요.", function() {
          $("#manager_tel23").focus().select();
        });
        return false;
      }
    }

			/*
			if ($("#mb_bank").val() == "") {
				validationAlertMessage("은행을 선택해 주세요.");
				$("#mb_bank").focus();
				return false;
			}

			if ($("#mb_account").val() == "") {
        validationAlertMessage("계좌번호를 입력해 주세요.");        
				$("#mb_account").focus();
				return false;
			}

			if ($("#mb_account_holder").val() == "") {
        validationAlertMessage("예금주를 입력해 주세요.");        
				$("#mb_account_holder").focus();
				return false;
			}

			if ($("#txt1").val() == ""){
				alert("통장사본을 업로드해 주세요.");
				$("#txt1").focus();
				return false;
			}
			*/

			/*
			var chks = $('input[type="checkbox"][name="mb_purpose[]"]:checked');
			if(chks.length==0) {
				alert("관심사를 하나 이상 선택해 주세요.");
				$("#mb_purpose[]").focus();
				return false;
			} else {
				//alert("선택된 체크갯수 : "+chks.length);
			}
			*/

			/*
			if ($("#mb_recommend").val() != "") {
				if ($("#recom_check").val() == "") {
					alert("추천인 확인을 해주세요.")
					return false;
				}
			}
			*/
      
      let new_member_coupon_use = "{{ $items['site_info']['new_member_coupon_use'] }}";
      if(new_member_coupon_use == '1') {
        if (
          $("#coupon_no1").val() !== "" ||
          $("#coupon_no2").val() !== "" ||
          $("#coupon_no3").val() !== "" ||
          $("#coupon_no4").val() !== "") {          

          if($("#coupon_checked").val() != 1) {
            validationAlertMessage("쿠폰번호 중복체크를 해주세요.", function() {
              $("#coupon_checked").focus().select();
            });
            return false;
          }
        }      
      }

      Swal.fire({
          title: '회원가입',
          text: '회원가입을 완료하시겠습니까?',
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
  


  function validationAlertMessage(message, callback = null) {
    Swal.fire({
      toast: true,
      icon: 'warning',
      title: '알림',
      html: message,
      confirmButtonText: '확인'
    }).then(() => {
      if (callback) callback();
    });
  }			  

  //DAUM 주소창  
  var themeObj = {
    //bgColor: "", //바탕 배경색
    //searchBgColor: "#0B65C8", //검색창 배경색
    //contentBgColor: "", //본문 배경색(검색결과,결과없음,첫화면,검색서제스트)
    //pageBgColor: "", //페이지 배경색
    //textColor: "", //기본 글자색
    //queryTextColor: "#FFFFFF" //검색창 글자색
    //postcodeTextColor: "", //우편번호 글자색
    //emphTextColor: "", //강조 글자색
    //outlineColor: "", //테두리
  };

  // 주소검색 layer
  var element_layer = document.getElementById('layer');

  function closeDaumPostcode() {
    element_layer.style.display = 'none';
  }

  function openDaumPostcode_layer() {
    new daum.Postcode({
      oncomplete: function(data) {
        var fullAddr = data.address;
        var extraAddr = '';
        if (data.addressType === 'R') {
          if (data.bname !== '') {
            extraAddr += data.bname;
          }
          if (data.buildingName !== '') {
            extraAddr += (extraAddr !== '' ? ', ' + data.buildingName : data.buildingName);
          }
          fullAddr += (extraAddr !== '' ? ' (' + extraAddr + ')' : '');
        }
        
        document.getElementById('mb_zip1').value = data.zonecode;
        document.getElementById('mb_addr1').value = fullAddr;
        document.getElementById('mb_addr_jibeon').value = data.jibunAddress;
        document.getElementById('mb_addr2').focus();
        element_layer.style.display = 'none';

      },
      width: '100%',
      height: '100%',
      theme: themeObj
    }).embed(element_layer);

    element_layer.style.display = 'block';

    initLayerPosition();
  }


  function initLayerPosition() {
    
    //디바이스 체크
    @php
        $isMobile = preg_match('/iphone|ipod|ipad|android|blackberry|webos|opera mini|windows phone/i', request()->userAgent());
    @endphp

    @if($isMobile)
      var width = 300;
      var height = 400;
      var borderWidth = 1;
    @else
      var width = 500;
      var height = 600;
      var borderWidth = 2;
    @endif

    element_layer.style.width = width + 'px';
    element_layer.style.height = height + 'px';
    element_layer.style.border = borderWidth + 'px solid';
    element_layer.style.left = (((window.innerWidth || document.documentElement.clientWidth) - width) / 2 - borderWidth) + 'px';
    element_layer.style.top = (((window.innerHeight || document.documentElement.clientHeight) - height) / 2 - borderWidth) + 'px';
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

</script>

@endsection


