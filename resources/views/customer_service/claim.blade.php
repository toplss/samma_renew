@extends('layouts.header')

@section('title', '클레임접수 페이지')

@section('content')

<div class="sub-container">
  <div class="sub-title-wrap">
    <h4>클레임접수</h4>  
  </div>

  <div class="terms-box">
    <h5>접수 유의사항 <button type="button" class="terms-toggle btn1">보기</button></h5>
    <div class="terms-note" style="display:none;">
      <img class="hide-680" src="{{ asset('images/common/img_claim.png') }}" alt="">
      <img class="show-680" src="{{ asset('images/common/img_claim01.png') }}">
      <img class="show-680" src="{{ asset('images/common/img_claim02.png') }}">
      <p style="color:#da2727; font-size:1.1rem;">※ 냉장.냉동제품의 보관 부주의로 인한 문제가 발생시 고객님의 책임 유.무가 발생할 수 있습니다.</p>
    </div>
  </div>

  <form name="claim_frm" id="claim_frm" method="post" action="{{ route('claim_save') }}">
    @csrf
    <table class="ask-table claim-table">
      <tr>
        <th>분류</th>
        <td>
          <label>
            <input type="radio" name="gubun" id="gubun" class="gubun1" value="product_claim" checked>상품클레임
          </label>
          <label>
            <input type="radio" name="gubun" id="gubun" class="gubun2" value="delivery_claim">배송클레임
          </label>
          <label>
            <input type="radio" name="gubun" id="gubun" class="gubun3" value="customer_claim">고객센터클레임
          </label>
          <label>
            <input type="radio" name="gubun" id="gubun" class="gubun4" value="etc">기타
          </label>
        </td>
      </tr>
      <tr>
        <th>업체명</th>
        <td><input type="text" name="company" id="company" value="{{ $activeMember['mb_company'] }}" style="max-width: 250px;" placeholder="업체명"></td>
      </tr>
      <tr style="display:none;">
        <th>담당자명</th>
        <td><input type="text" name="name" id="name" value="{{ $activeMember['mb_name'] }}" style="max-width: 250px;" placeholder="담당자명"></td>
      </tr>
      <tr style="display:none;">
        <th>담당자 연락처</th>
        <td>
          <input type="text" name="phone1" id="phone1" value="{{ isset($activeMember['mb_hp']) ? substr($activeMember['mb_hp'], 0, 3) : '' }}"> -
          <input type="text" name="phone2" id="phone2" value="{{ isset($activeMember['mb_hp']) ? substr($activeMember['mb_hp'], 4, 4) : '' }}"> -
          <input type="text" name="phone3" id="phone3" value="{{ isset($activeMember['mb_hp']) ? substr($activeMember['mb_hp'], 9, 4) : '' }}">
        </td>
      </tr>
      <tr style="display:none;">
        <th>주소</th>
        <td><input type="text" name="addr" id="addr" value="{{ $activeMember['mb_addr1'].' '.$activeMember['mb_addr2'] }}" style="width: 100%;" placeholder="주소입력"></td>
      </tr>
      <tr>
        <th>제목</th>
        <td><input type="text" name="title" id="title" style="width:100%;" placeholder="제목"></td>
      </tr>
      <tr>
        <th>클레임 내용</th>
        <td><textarea name="contents" id="contents" placeholder="정확한 클레임 내용을 입력해 주세요"></textarea></td>
      </tr>

    </table>

    <div class="ask-btn">
      <button type="button" class="btn1 big-btn" onclick="javascript:chkform_claim();">접수하기</button>
      <button type="button" class="btn2 big-btn">취소</button>
    </div>

  </form>

</div>
<script>
function chkform_claim() {
  var frm = $('#claim_frm')[0];

  var company = $('#company').val();
  if (!company) {
    var message = '업체명을 입력 하세요.';

    validationAlertMessage(message);

    return false;
  }

  var name = $('#name').val();
  if (!name) {
    var message = '담당자를 입력 하세요.';

    validationAlertMessage(message);

    return false;
  }


  if (!$('#phone1').val() || !$('#phone2').val() || !$('#phone3').val()) {
    var message = '연락처를 입력 하세요.';

    validationAlertMessage(message);

    return false;
  }


  var title = $('#title').val();
  var contents = $('#contents').val();

  if (!title || !contents) {
    var message = '제목 또는 내용을 입력 하세요.';

    validationAlertMessage(message);

    return false;
  }

  frm.submit();
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


</script>
@endsection