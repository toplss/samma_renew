@extends('layouts.header')

@section('content')

<div class="sub-container">
	<div class="sub-title-wrap">
    <h4>공지사항</h4>  
  </div>

	<div class="board-wrap">
    <form name="frm" id="frm" onsubmit="return submitForm();" action="{{ route('notice_save') }}" method="post" enctype="multipart/form-data">
      @csrf
      <input type="hidden" name="bd_num" value="{{ request('bd_num') }}" />
      <table class="board-write ask-table">
        <tr>
          <th>글종류</th>
          <td>
            <label><input type="checkbox" name="bd_notice" id="bd_notice" value="1" {{ (isset($item->bd_notice) && $item->bd_notice == '1') ? 'selected' : ''  }}>공지사항</label>
          </td>
        </tr>
        <tr>
          <th>작성자</th>
          <td><input name="bd_name" type="text" value="{{ $activeMember['mb_name'] }}"></td>
        </tr>
        <tr>
          <th>제목</th>
          <td>
            <div class="flex" style="gap:5px;">
              <input type="text" name="bd_subject" id="bd_subject" value="{{ isset($item->bd_subject) ? $item->bd_subject : ''  }}" style="width:80%;">
              <p><input type="color" name="bd_ext1" id="" value="{{ isset($item->bd_ext1) ? $item->bd_ext1 : ''  }}"><span>(제목 색상)</span></p>
            </div>
          </td>
        </tr>
        <tr style="display:none;">
          <th>링크 url</th>
          <td><input type="text" name="bd_url" id="bd_url" style="width:100%;"></td>
        </tr>
        <tr>
          <th>내용</th>
          <td>
            <div class="bw1">
              <textarea id="bwContent" name="bwcontent">{{ isset($item->bd_content) ? $item->bd_content : ''  }}</textarea>
              <script src="/smarteditor2/js/service/HuskyEZCreator.js" charset="utf-8"></script>
              <script>
              var oEditors = [];
              nhn.husky.EZCreator.createInIFrame({
                  oAppRef: oEditors,
                  elPlaceHolder: "bwContent",
                  sSkinURI: "/smarteditor2/SmartEditor2Skin.html",
                  fCreator: "createSEditor2"
              });
              </script>
            </div>
          </td>
        </tr>
        <tr>
          <th>첨부파일</th>
          <td class="bw2">
            <button type="button" onclick="add_files()" class="btn1 add_file">첨부파일 추가</button>
            <span>첨부파일은 최대 10개까지 추가 가능합니다.</span>
            <ul id="file_list">
              @if(isset($item->files) && $item->files->count() > 0)
              @foreach ($item->files as $index => $file)
              <li>
                <input type="button" class="btn2 file-btn" value="첨부파일 #{{ ++$index}}">
                <input type="file" name="files[]" class="file-input" hidden>
                <input type="text" class="file-name" value="{{ $file->file_orgin_name }}" readonly style="width:80%;">
                <button type="button" onclick="location.href='/common_board/file_del?f_k={{ $file->idx }}';">x</button>
              </li>
              @endforeach
              @else 
              <li>
                <input type="button" class="btn2 file-btn" value="첨부파일 #1">
                <input type="file" name="files[]" class="file-input" hidden>
                <input type="text" class="file-name" readonly style="width:80%;">
              </li>
              @endif
            </ul>
          </td>
        </tr>
      </table>

      <div class="flex-center" style="gap:0.5rem;">
        <button type="submit" class="btn3 big-btn" >확인</button>
        <button type="button" class="btn1 big-btn" onclick="location.href = '/common_board/list';">취소</button>
      </div>
    </form>

	</div>
		
</div>

<script>
const fileLimit = 10;

$(document).ready(function() {

  // 첨부파일 추가
  $('.add_file').on('click', function() {

    let count = $('#file_list li').length;

    if (count >= fileLimit) {
      alert('첨부파일은 최대 ' + fileLimit + '개까지 가능합니다.');
      return;
    }

    let num = count + 1;

    let html = `
      <li>
        <input type="button" class="btn2 file-btn" value="첨부파일 #${num}">
        <input type="file" name="files[]" class="file-input" hidden>
        <input type="text" class="file-name" readonly style="width:80%;">
      </li>
    `;

    $('#file_list').append(html);

  });

  // 파일 선택 버튼 클릭
  $(document).on('click', '.file-btn', function() {

    $(this).closest('li').find('.file-input').click();

  });

  // 파일명 표시
  $(document).on('change', '.file-input', function() {

    let fileName = this.files[0] ? this.files[0].name : '';

    $(this).closest('li').find('.file-name').val(fileName);

  });

});

function submitForm() {
  let subject = $('#bd_subject').val().trim();
  if (subject == '') {
    
    validationAlertMessage('제목을 입력해 주세요.');

    return false;
  }

  let content = oEditors.getById["bwContent"].getIR();
  if (content.trim() == '') {
    
    validationAlertMessage('내용을 입력해 주세요.');

    return false;
  }

  oEditors.getById["bwContent"].exec("UPDATE_CONTENTS_FIELD", []);

  document.forms[0].submit();
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


