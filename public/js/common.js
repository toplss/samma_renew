$(document).ready(function(){

  // header
  // allmenu
  $('.allmenu_icon').click(function(){
    $(this).toggleClass('active');
    $(".allmenu_bar").toggle();
  });
  $('.m_hamburger').click(function(){
    $(this).toggleClass('active');
  });
  // 서브메뉴가 없을때
  $('ul.sub_go').each(function() {
    if ($(this).find('li').length === 0) {
      $(this).css('width','0');
    }
  });
  $(".main_go").hover(function() {
    $(this).find(".sub_go").show();
  });
  $(".main_go").mouseleave(function() {
    $(this).find(".sub_go").hide();
  });


  // 헤더 높이
  const header = document.querySelector('header');

  const observer = new ResizeObserver(() => {
    document.documentElement.style.setProperty(
      '--header-height',
      header.getBoundingClientRect().height + 'px'
    );
  });

  observer.observe(header);



  // 메인 팝업
  $('.m-pop-btn .close-btn').click(function(){
    $('.main-popup').hide();
  });

  // 메뉴 슬라이딩 패널
  const panelHeight = $('.nav-slide-panel').prop('scrollHeight');
  document.documentElement.style.setProperty(
    '--panel-height',panelHeight + 'px'
  );
  $(window).on('resize', function () {
    if ($('.sub-container').hasClass('down')) {
      const h = $('.nav-slide-panel').prop('scrollHeight');
      document.documentElement.style.setProperty('--panel-height', h + 'px');
    }
  });

  $(window).on("scroll", function() { //스크롤이 발생한 경우
		let scrollTop = $(this).scrollTop();
    if (scrollTop > 50) {
      $(".nav-slide-panel").removeClass("on");
      $(".sub-container").removeClass("down");
      $('.panel-close-btn').hide();
    }
	});
  $('html').click(function(e){ //배너 밖 영역을 클릭한 경우
    if(!$(e.target).hasClass('.nav-slide-panel')){
      $(".nav-slide-panel").removeClass("on");
      $(".sub-container").removeClass("down");
      $('.panel-close-btn').hide();
    }
	});
  $('.panel-close-btn').click(function(){ //닫기 버튼을 클릭할 경우
    $(".nav-slide-panel").removeClass("on");
    $(".sub-container").removeClass("down");
    $(this).hide();
  })
  $(".nav-slide-panel").on("mouseleave", function(e) { //마우스가 영역을 벗어날 경우

    // 마우스가 이동한 대상이 닫기 버튼이면 무시
    if ($(e.relatedTarget).closest('.panel-close-btn').length) {
      return;
    }

    $(this).removeClass("on");
    $(".sub-container").removeClass("down");
    $('.panel-close-btn').hide();
  });

  // 새로고침시 항상 스크롤 맨위
  if (window.innerWidth >= 1024) {
    if ('scrollRestoration' in history) {
      history.scrollRestoration = 'manual';
    }

    window.addEventListener('load', () => {
      window.scrollTo({ top: 0, left: 0, behavior: 'auto' });
    });
  };

  // 메뉴 x축 스크롤
  if (!('ontouchstart' in window)) {
    const $menu = $('.menu');

    let isDown = false;
    let startX;
    let scrollLeft;

    $menu.on('mousedown', function (e) {
      isDown = true;
      startX = e.pageX;
      scrollLeft = this.scrollLeft;
      $menu.css('user-select', 'none');
    });

    $(document).on('mouseup', function () {
      isDown = false;
      $menu.css('user-select', '');
    });

    $menu.on('mousemove', function (e) {
      if (!isDown) return;
      e.preventDefault();
      this.scrollLeft = scrollLeft + (startX - e.pageX);
    });
  }


  // 모바일헤더
  $('.m_hamburger').click(function(){
    $('.m-menu').toggleClass('open');

    if ($('.m-menu').hasClass('open')) {
      $('body').css('overflow-y','hidden');
    } else {
      $('body').css('overflow-y','scroll');
    }
  });

  $('#mSearchBtn').click(function(){
    $('.m-search-modal').show();
  });
  $('.ms-close').click(function(){
    $('.m-search-modal').hide();
  })

  // 모바일 메뉴 위치 저장
  if ($(window).width() <= 1024) {
    var $menu = $(".menu");
    var $active = $menu.find(".active");

    if ($active.length > 0) {
      var menuWidth = $menu.outerWidth();
      var activeCenter = $active.position().left + ($active.outerWidth() / 2);

      $menu.animate({
        scrollLeft: activeCenter - (menuWidth / 2)
      }, 200);
    };
  }

  
  // footer
  $(".lf-slide").slick({
    dots: false,
    arrows: false,
    autoplay: true,
    infinite: true,
    slidesToShow: 1,
    speed: 300,
    autoplaySpeed: 3500,
    pauseOnHover : false,
    pauseOnFocus: false,
    fade: true
  });

  // 우측 장바구니
  $('.cart_btn').click(function(){
    $('aside').toggleClass('open');
  });

  // 장바구니 모바일
  // 높이계산 (bottom값)
  function setCartQuickHeight() {
    const h = $('#cartQuickWrap').outerHeight() + 3;
    document.documentElement.style.setProperty(
      '--cartQuickHeight',`${h}px`
    );
  }
  setCartQuickHeight();
  window.addEventListener('load', setCartQuickHeight);
  requestAnimationFrame(setCartQuickHeight);

  // 장바구니 토글
  function toggleCartBg() {
    if ($(window).width() <= 1024 && $('#cartSidebar').hasClass('open')) {
      $('.m-cart-bg').show();
      $('#mCartQuickBtn').hide();
      $('#mCartCloseBtn').show();
    } else {
      $('.m-cart-bg').hide();
      $('#mCartCloseBtn').hide();
    };
  };
  toggleCartBg();
  $(window).on('resize', toggleCartBg);

  $('.m-quick-btn .btn3').click(function(){
    $('#cartSidebar').addClass('open');
    $('#mCartCloseBtn').show();
    $('.m-cart-bg').show();
    $('#mCartQuickBtn').hide();
    $('.pg1').hide();
  });
  $('.m-cart-bg, #mCartCloseBtn').click(function(){
    $('#cartSidebar').removeClass('open');
    $('#mCartCloseBtn').hide();
    $('.m-cart-bg, #mCartCloseBtn').hide();
    $('.pg1').hide();
    setTimeout(function(){
      $('#mCartQuickBtn').show();
    },100);
  });

  $('#mTotalViewBtn').click(function(){
    $('.pg1').slideToggle('fast');
  });

  // 푸터 toggle
  $('.mf-toggle p').click(function(){
    if ($('.f-box3 dl').is(':visible')) {
      $('.f-box3 dl').slideUp();
      $('.mf-toggle p img').css('transform', 'none');
    } else {
      $('.f-box3 dl').css('display', 'grid').hide().slideDown();
      $('.mf-toggle p img').css('transform', 'rotate(180deg)');
    }
  });

  // 문의하기 버튼

  $('#contactBtn').click(function(){
    $('.contact_wrap').toggle();
  });

  $('#closeBtn').click(function(){
    $('.contact_wrap').hide();
  });

  // 카테고리 버튼고정
  var generate_hash = $('#generate_hash').val();

  var url_params = new URLSearchParams(window.location.search);
  var ca_id = url_params.get('ca_id') || '';
  var type = url_params.get('type') || '';

  if(ca_id.length >= 2 && ca_id.length <= 4) {
    ca_id = ca_id.substring(0, 2);
  }

  if (ca_id) {
    var targetLi = $('#cateList li[data-ca-id="' + ca_id + '"]');
    
    if (targetLi.length > 0) {
      var text = targetLi.find('a').text().trim();

      $('#cateBtn').contents().filter(function() {
          return this.nodeType === 3; // 텍스트 노드만 선택
      }).first().replaceWith(text);
    }
  }

  if (type == 'vivacook' || type == 'mygrang' || type == 'sale') {
    let title = '';

    switch (type) {
      case 'vivacook' : title = '비바쿡'; break;
      case 'mygrang' : title = '마이그랑'; break;
      case 'sale' : title = '이달의행사'; break;
    }
    $('#cateBtn').contents().filter(function() {
        return this.nodeType === 3; // 텍스트 노드만 선택
    }).first().replaceWith(title);
  }


  // 상품 뷰 형태
  $(document).on('click', '#gridType', function() {
      var view_type_key = generate_hash +':shop:item_view';
      
      deleteCookie(view_type_key);
      setCookie(view_type_key, 'grid');
  });

  $(document).on('click', '#rowType', function() {
      var view_type_key = generate_hash +':shop:item_view';
      
      deleteCookie(view_type_key);
      setCookie(view_type_key, 'row');
  });
  
  if (getCookie(generate_hash + ':shop:item_view') === 'row') {
      $('#rowType').addClass('on');
      $('#gridType').removeClass('on');
      $('.prd-list').addClass('type-row');
  } else {
      $('#rowType').removeClass('on');
      $('#gridType').addClass('on');
      $('.prd-list').removeClass('type-row');
  };

  function togglePrdRowHead() {
    if ($('#rowType').hasClass('on')) {
      $('.prd-row-head').show();
    } else if ($('#gridType').hasClass('on')) {
      $('.prd-row-head').hide();
    }
  };

  togglePrdRowHead();

  $('#gridType, #rowType').on('click', function () {
    togglePrdRowHead();
  });

  $('#gridType').click(function(){
    $('.prd-row-head').hide();
  });
  $('#rowType').click(function(){
    $('.prd-row-head').show();
  })

});



function logoutBtn() {
  $('#log_out').submit();
}

