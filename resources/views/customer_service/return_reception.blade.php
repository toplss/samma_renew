@extends('layouts.header')

@section('content')

<div class="sub-container">
  <div class="sub-title-wrap">
    <h4>반품 접수</h4>  
  </div>

  <div class="terms-box">
    <h5>접수 유의사항 <button type="button" class="terms-toggle btn1">보기</button></h5>
    <div class="terms-note" style="display:none;">
      <img class="hide-680" src="{{ asset('images/common/img_return_reception.png') }}" alt="">
      <img class="show-680" src="{{ asset('images/common/img_return_reception01.png') }}" alt="">
      <img class="show-680" src="{{ asset('images/common/img_return_reception02.png') }}" alt="">
      <p>
        - 당사에서 납품되지 않은 상품은 반품되지 않습니다. <br>
        - 냉장,냉동제품은 반품 되지 않으므로 주문시 꼼꼼한 검수 발주 부탁드립니다. <br>
        - 매장에서 선입선출 부주의로 인한 반품은 일부 고객님의 손실로 처리됨을 알려드립니다. <br>
        - 반품시 부과세를 제외한 공급가로 반품 됩니다. <br>
        - 반품불가 상품 및 주문 착오에 따른 반품 접수는 고객센터로 문의주시기 바랍니다. (고객센터 1899-3153)
      </p>
    </div>

  </div>

  <form name="return_reception_frm" id="return_reception_frm" method="post" enctype="multipart/form-data" action="{{ route('return_save') }}">
    @csrf
    <table class="ask-table return-table">
      <tr>
        <th>분류</th>
        <td>
          <label>
            <input type="radio" name="gubun" id="gubun" class="gubun1" value="return" checked>반품접수
          </label>
        </td>
      </tr>
      <tr>
        <th>업체명</th>
        <td><input type="text" name="company" id="company" value="{{ (isset($activeMember['mb_company'])) ? $activeMember['mb_company'] : '' }}" style="max-width: 250px;" placeholder="업체명"></td>
      </tr>
      <tr style="display:none;">
        <th>담당자명</th>
        <td><input type="text" name="name" id="name" value="{{ (isset($activeMember['mb_name'])) ? $activeMember['mb_name'] : '' }}" style="max-width: 250px;" placeholder="담당자명"></td>
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
        <th>상품명 검색</th>
        <td>

{{-- @dd($result); --}}
{{-- @dd($activeMember); --}}

          <select name="return_skeyword" id="return_skeyword">
            <option value="" selected>검색어를 입력해 주세요</option>
            @foreach($result as $key => $row)
            <option value="{{ $key }}">{{ $row->it_name }}</option>
            @endforeach
          </select>           
        </td>
      </tr>

      <!-- 상품 검색 결과 -->
      <tr class="search_result hide">
        <td colspan="2">
          <table class="return-item-table sr">
            <colgroup>
              <col style="width:auto;">
              <col style="width:80px;">
              <col style="width:100px;">
              <col style="width:24%;">
              <col style="width:26%;">
              <col style="width:80px;">
            </colgroup>
            <thead>
              <tr>
                <th>상품정보</th>
                <th>유형</th>
                <th>수량</th>
                <th>사진</th>
                <th>메모</th>
                <th></th>
              </tr>
            </thead>
            <tbody id="request_item"></tbody>            
          </table>
        </td>
      </tr>

      <!-- 반품접수완료된 목록 -->
      <tr class="complete_list hide">
        <td colspan="2">
          <span class="btn3" style="margin-bottom:0.5rem;">접수 목록</span>
          <table class="return-item-table done">
            <colgroup class="hide-680">
              <col style="width:30px;">
              <col style="width:auto;">
              <col style="width:80px;">
              <col style="width:80px;">
              <col style="width:24%;">
              <col style="width:26%;">
              <col style="width:80px;">
            </colgroup>
            <thead>
              <tr>
                <th>번호</th>
                <th>상품정보</th>
                <th>유형</th>
                <th>수량</th>
                <th>사진</th>
                <th>메모</th>
                <th></th>
              </tr>
            </thead>
            <tbody id="complete_item">
              <!-- 여기에 tr 반복 추가 -->
            </tbody>            
          </table>
        </td>
      </tr>
    </table>

    <div class="ask-btn">
      <button type="button" class="btn1 big-btn" onclick="chkform_return_reception();">접수하기</button>
      <button type="button" class="btn2 big-btn" onclick="cancel_all_items();">취소</button>
    </div>

  </form>

</div>

<script>
$(document).ready(function() {

  //상품명 검색창
  $('#return_skeyword').select2({
    placeholder: '검색어를 입력해 주세요',
    allowClear: true,
    // dropdownParent: $('#return_skeyword').closest('td'),
    dropdownParent: $('#return_skeyword').parent(),
  });
  
  $('#return_skeyword').on('select2:select', function () {
      display_item();
      $(this).val('').trigger('change');
  });

});

//반품수량 숫자체크
$(document).on("input", ".return_qty", function () {
    const value = this.value;

    if (!/^[0-9]*$/.test(value)) {
        validationAlertMessage('반품수량은 숫자만 입력 가능합니다.');
        this.value = value.replace(/[^0-9]/g, '');
    }
});

//검색 결과
function display_item() {

  //검색 결과 Display
  $(".search_result").removeClass('hide');

  const result = @json($result);
  const idx = $('#return_skeyword').val();
  const item = result[idx];

  // console.log(item);

  $('#request_item').html(`

    <!-- 상품정보 -->
    <tr class="complete-product" data-key="${item.it_id}">
      <td data-label="번호" class="r-num">1</td>
      <td data-label="상품정보">
        <input type="hidden" name="items[${item.it_id}][title_no]" id="items[${item.it_id}][title_no]" value="${item.it_id}">
        <input type="hidden" name="items[${item.it_id}][title]" id="items[${item.it_id}][title]" value="${item.it_name}">
        <input type="hidden" name="items[${item.it_id}][it_receive_unit]" id="items[${item.it_id}][it_receive_unit]" value="${item.it_receive_unit}">
        <div class="ri1">
          <img src="${item.it_img1 ? `/images/item/${item.it_img1}` : '/images/common/no_image.gif'}" alt="">
          <p>
            ${item.it_name}
            <span>(${item.it_basic})</span>
            <b>${Number(item.ct_price ?? item.{{ $activeMember["field_it_price"] }}).toLocaleString()}원</b>
          </p>
        </div>
      </td>
      <td class="ri2" data-label="유형">
        <span class="${getTempClass(item.it_storage)}">
          ${item.it_storage_label}
        </span>
        ${returnLabelSpan(item.it_return_label)}
      </td>
      <td class="ri3" data-label="수량">
        <p>
          <u>박스</u><input type="text" name="ct_qty_box[${item.it_id}]" class="return_qty" id="ct_qty_box[${item.it_id}]" maxLength="4">
        </p>
        <p>
          <u>낱개</u><input type="text" name="ct_qty_pcs[${item.it_id}]" class="return_qty" id="ct_qty_pcs[${item.it_id}]" maxLength="4">
        </p>
      </td>
      <td class="ri4" data-label="사진">
        <input type="file" name="userfile[${item.it_id}]" id="userfile[${item.it_id}]" style="display:none;" onchange="compressReturnImage(this, '#userfile_txt_${item.it_id}')" accept="image/*">
        <input type="text" id="userfile_txt_${item.it_id}" placeholder="사진첨부" readonly>
        <button type="button" class="btn3" onclick="document.getElementById('userfile[${item.it_id}]').click();">파일선택</button>
        <p class="txt-red">*사진 첨부는 필수입니다.</p>
      </td>
      <td data-label="메모"><textarea name="contents[${item.it_id}]" id="contents[${item.it_id}]" placeholder="남기실 말씀"></textarea></td>
      <td data-label="신청">
        <button type="button" class="btn1" onclick="javascript:select_item('${item.it_id}', '${item.it_return}', '${item.it_receive_unit}', '${item.ct_id}', '${item.ct_qty_tot}');">반품신청</button>
      </td>
    </tr>
  `);
  
}


//보관유형 CSS 처리
function getTempClass(val) {
  return {
    1: 'room_temp',
    2: 'frozen_temp',
    3: 'low_temp',
    4: ''
  }[val] ?? '';
}


//반품구분 CSS 처리
function returnLabelSpan(label) {
  const cls = label === '반품가능' ? 'return_o' : 'return_x';
  return `<span class="${cls}">${label}</span>`;
}

//상품 수량 선택 시 알림
function checkStatus(returnYN, ctID){
  //반품불가 상품
  if(returnYN == 2) {
    $(this).prop('disabled', true).focus()
    validationAlertMessage('반품불가 상품입니다.<br>고객센터로 ( 1899-3153 ) 문의 바랍니다.');
    return false;
  }

  //미구매 상품
  if (!ctID || ctID === 'null' || ctID === 'undefined') {
    validationAlertMessage('구매이력이 없습니다.<br>고객센터로 ( 1899-3153 ) 문의 바랍니다.');
    return false;
  }  
}

//수량처리 >> 구매한 수량까지만..
function qtyOptions(max) {
  
  let select_option = '';
  
  if (max > 10) max = 10; //반품 가능수량은 최대 10개까지 제한
  select_option += `<option value="" 'selected'}>선택</option>`;
  for (let i = 1; i <= max; i++) {
    select_option += `<option value="${i}"}>${i}</option>`;
  }

  return select_option;
}


//반품신청
function select_item(key,returnYN,ItReceiveUnit,ctID,ctQtyTot) {  
  
  // 중복 접수 체크
  if ($('#complete_item tr.complete-product[data-key="' + key + '"]').length > 0) {
    validationAlertMessage('이미 접수된 상품입니다.');
    return false;
  }

  //반품불가 상품
  if(returnYN == 2) {
    validationAlertMessage('반품불가 상품입니다.<br>고객센터로 ( 1899-3153 ) 문의 바랍니다.');
    return false;
  }

  //구매이력 없음
  if (!ctID || ctID === 'null' || ctID === 'undefined') {
    validationAlertMessage('구매이력이 없습니다.<br>고객센터로 ( 1899-3153 ) 문의 바랍니다.');
    return false;
  }

  //반품수량 체크
  const ct_qty_box = $('input[name="ct_qty_box[' + key + ']"]');
  const ct_qty_pcs = $('input[name="ct_qty_pcs[' + key + ']"]');
  const boxVal = ct_qty_box.val();
  const pcsVal = ct_qty_pcs.val();
  const return_tot = Number(boxVal) * Number(ItReceiveUnit) + Number(pcsVal);

  if (
    (boxVal == null || boxVal === '' || boxVal < 1 ) &&
    (pcsVal == null || pcsVal === '' || pcsVal < 1 )
  ) {
    var message = '반품하실 수량을 입력해 주세요.';  
    validationAlertMessage(message, function() {
      ct_qty_pcs.focus();
    });
    return false;
  }

  
  if (return_tot > ctQtyTot) {
    var message = '반품신청 수량은 구매하신 수량을 초과할 수 없습니다.';
    validationAlertMessage(message);
    return false;
  }

  //첨부파일
  const uploadFile = $('input[type="file"][name="userfile[' + key + ']"]');
  if (uploadFile.val() == '') {
    var message = '사진을 첨부해 주세요';
    validationAlertMessage(message);
    return false;            
  }
  const allowedExt = ['jpg','jpeg','png','pdf'];
  const file = uploadFile.get(0).files[0];
  const ext = file.name.split('.').pop().toLowerCase();
  if ($.inArray(ext, allowedExt) === -1) {
      var message = '허용되지 않는 파일입니다.';
      validationAlertMessage(message);
      return;
  }

  //남기는 말씀
  const comment = $('textarea[name="contents[' + key + ']"]');
  if (comment.val() == '') {
    var message = '남기실 말씀을 작성해 주세요.';
    validationAlertMessage(message, function() {
      comment.focus();
    });

    return false;            
  }

  //검색 결과 초기화
  $(".search_result").addClass('hide');

  // 검색창 초기화
  $('#return_skeyword').val(null).trigger('change');

  //접수 목록 Display
  $(".complete_list").removeClass('hide');

  //접수 목록으로 이동 (이동 전 반품시청 버튼을 취소 버튼으로 변경)
  $("#request_item")
  .find("button.btn1")
  .text("신청취소")
  .attr("onclick", "delete_item('" + key + "')");

  $("#request_item").children().prependTo("#complete_item");

  // 접수목록 넘버링
  renumberCompleteItems();

}

// 신청 취소
function delete_item(key) {

  Swal.fire({
    toast: false,
    icon: 'warning',
    title: '신청 취소',
    html: '선택하신 상품의 반품 접수를<br>취소하시겠습니까?<br>',
    showCancelButton: true,
    confirmButtonText: '취소하기',
    cancelButtonText: '유지하기',
    confirmButtonColor: '#d33'
  }).then((result) => {

    if (!result.isConfirmed) return;

    // 해당 상품의 대표 tr 찾기
    const $productTr = $('#complete_item').find('tr.complete-product[data-key="' + key + '"]');

    if ($productTr.length === 0) return;

    // 같은 상품에 속한 tr 3개 제거
    $productTr.next('.upload_file').remove(); // 파일첨부
    $productTr.next('.comment').remove();     // 남기는말
    $productTr.remove();                      // 상품정보

    // 접수목록 넘버링
    renumberCompleteItems();

    // 접수 목록이 비었으면 영역 숨김
    if (getCompleteCount() < 1) {
      $('.complete_list').addClass('hide');
    }

    Swal.fire({
      toast: false,
      icon: 'success',
      title: '신청이 취소되었습니다',
      timer: 1500,
      showConfirmButton: false
    });

  });
}


// 반품 접수목록 넘버링
function renumberCompleteItems() {

  const total = $('#complete_item .complete-product').length;
  $('#complete_item .complete-product').each(function (index) {
    $(this).find('.r-num').text(total - index);
  });

}


// 반품 접수목록 리스트 갯수
function getCompleteCount() {
  return $('#complete_item tr.complete-product').length;
}


// 전체 신청 취소
function cancel_all_items() {

  const cnt = getCompleteCount();

  if (cnt < 1) {
    validationAlertMessage('취소할 반품 접수 상품이 없습니다.');
    return;
  }

  Swal.fire({
    toast: false,
    icon: 'warning',
    title: '전체 취소',
    html: '접수한 반품 상품을 모두 취소할까요?',
    showCancelButton: true,
    confirmButtonText: '전체 취소',
    cancelButtonText: '유지하기',
    confirmButtonColor: '#d33'
  }).then((result) => {

    if (!result.isConfirmed) return;

    // 접수 목록 전체 제거
    $('#complete_item').empty();

    // 접수 목록 영역 숨김
    $('.complete_list').addClass('hide');

    Swal.fire({
      toast: false,
      icon: 'success',
      title: '모든 반품 접수가 취소되었습니다.',
      timer: 1500,
      showConfirmButton: false
    });

  });
}


//반품접수
function chkform_return_reception() {
  var frm = $('#return_reception_frm')[0];

  const cnt = getCompleteCount();
  if (cnt < 1) {
    var message = '반품하실 상품을 검색하신 후 <br>반품 신청 버튼을 클릭해 주세요.';
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


  // 최종 확인
  Swal.fire({
    icon: 'question',
    title: '반품 접수 확인',
    html: `총 <b>${cnt}개</b> 상품을 반품 접수하시겠습니까?`,
    showCancelButton: true,
    confirmButtonText: '접수하기',
    cancelButtonText: '취소',
    confirmButtonColor: '#3085d6'
  }).then((result) => {

    if (!result.isConfirmed) return;

    // 중복 제출 방지
    $('.ask-btn button').prop('disabled', true);
    
    //반품처리
    frm.submit();

  });

}


function validationAlertMessage(message, callback) {
  Swal.fire({
    toast: false,
    icon: 'warning',
    title: '알림',
    html: message,
    width: 400,
    confirmButtonText: '확인'
  }).then(() => {
    if (callback) callback();
    return false;
  });
}

</script>

@endsection