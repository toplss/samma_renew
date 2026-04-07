@extends('layouts.header')

@section('title', 'AS 접수 페이지')

@section('content')

<div class="sub-container">
  <div class="sub-title-wrap">
    <h4>A/S접수</h4>  
  </div>

  <div class="terms-box">
    <h5>접수 유의사항 <button type="button" class="terms-toggle btn1">보기</button></h5>
    <div class="terms-note" style="display:none;">
      <img class="hide-680" src="{{ asset('images/common/img_as_reception.png') }}" alt="">
      <img class="show-680" src="{{ asset('images/common/img_as_reception01.png') }}" alt="">
      <img class="show-680" src="{{ asset('images/common/img_as_reception02.png') }}" alt="">

      <p>
        - A/S 접수시 24시간이내 출동을 원칙으로 합니다. <br>
        - 본사에서 지원하지 않은 장비는 A/S처리가 불가합니다. <br>
        - 지원된 장비와 관련된 상품을 구매하지 않거나 이중 거래시 장비철수 및 유상 A/S처리 됩니다. <br>
        - 동일한 장비가 있을 경우 하나를 선택 후 접수합니다. <br>
        - 예) 냉장고가 2개일때 둘 중 하나를 선택합니다.
      </p>
    </div>
  </div>

  <form name="as_reception_frm" id="as_reception_frm" method="post" enctype="multipart/form-data" action="{{ route('as_save') }}">
    @csrf
    <table class="ask-table return-table">
      <tr>
        <th>분류</th>
        <td>
          <label onclick="javascript:$('.gubun1').click();">
            <input type="radio" name="gubun" id="gubun" class="gubun1" value="as" checked> A/S접수
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
        <td colspan="2">
          <table class="claim-item-table">
            <tr class="hide-680">
              <th class="hide-820">번호</th>
              <th>이미지</th>
              <th>분류명</th>
              <th>장비명</th>
              <th>장비번호</th>
              <th>상태</th>
              <th>A/S접수</th>
            </tr>
            <!-- 반복 -->
            @php
            $it_id_type_str = [
                4 => "<span style='color:#0000ff;'>판매</span>",
                5 => "임대",
                7 => "<span style='color:#e02f30;'>회수</span>",
            ];
            @endphp
            @foreach ($items as $key => $row)
            <tr>
              <td class="hide-820">{{ $row->row_num }}</td>
              <td class="ci1"><img src="{{ asset('images/item/'.$row->it_img1) }}" alt=""></td>
              <td class="hide-680">{{ $row->ca_name }}</td>
              <td class="ci2"><span class="show-680 txt-blue">{{ $row->ca_name }}</span>{{ $row->it_name }} </td>
              <td class="hide-680">{{ $row->t_p_code }}</td>
              <td>{!! $it_id_type_str[$row->t_it_id_type] !!}</td>
              <td><input type="radio" name="title" id="" class="it_id" value="{{ $row->it_id.'||'.$row->it_name }}" {{ ($row->idx == request('idx')) ? 'checked' : '' }}></td>
            </tr>
            @endforeach
            <!-- 반복 끝 -->

            <!-- 없을때 -->
            @if($items->count() < 1)
            <tr>
            <td colspan="11" height="100">내역이 없습니다.</td>
            </tr>
            @endif

          </table>
        </td>
      </tr>


      <tr>
        <th>사진첨부</th>
        <td>
          <input type="file" 
                name="userfile" 
                id="userfile1" 
                style="display:none;" 
                onchange="document.getElementById('userfile_txt1').value=this.value; compressReturnImage(this, '#userfile_txt1');" 
                accept="image/*">
					<input type="text" id="userfile_txt1" placeholder="사진첨부" readonly>
          <button type="button" class="btn3" onclick="document.getElementById('userfile1').click();">파일선택</button>
          <span class="txt-red">*정확한 확인을 위해 사진 첨부는 필수입니다.</span>
        </td>
      </tr>
      <tr>
        <th>증상</th>
        <td><textarea name="contents" id="contents"></textarea></td>
      </tr>
    </table>

    <div class="ask-btn">
      <button type="button" class="btn1 big-btn" onclick="javascript:chkform_as_reception();">접수하기</button>
      <button type="button" class="btn2 big-btn" onclick="location.href = '{{ route('/') }}';">취소</button>
    </div>

  </form>
</div>

<script>
function chkform_as_reception() {
  var frm = $('#as_reception_frm')[0];

  var it_id = $('.it_id').is(':checked');
  if (!it_id) {
    var message = '장비를 선택 하세요.';

    validationAlertMessage(message);

    return false;
  }


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


  var name = $('#contents').val();
  if (!name) {
    var message = '내용을 입력 하세요.';

    validationAlertMessage(message);

    return false;
  }


  var name = $('#userfile1').val();
  if (!name) {
    var message = '파일은 필수 입력입니다.';

    validationAlertMessage(message);

    return false;
  }

  frm.submit();
}


function validationAlertMessage(message)  {
  Swal.fire({
      toast: false,
      icon: 'warning',
      title: '알림',
      html: message,
      confirmButtonText: '확인'
  });
}
</script>

@endsection