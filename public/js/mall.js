$(document).ready(function(){
  // main
  $(".intro-slide").slick({
    dots: false,
    arrows: false,
    autoplay: true,
    infinite: true,
    slidesToShow: 2,
    speed: 500,
    autoplaySpeed: 3500,
    pauseOnHover : false,
    pauseOnFocus: false,
    responsive: [
      {
        breakpoint: 680,
        settings: {
          slidesToShow: 1,
        }
      },      
    ]
  });

  $(".main-full-slide").slick({
    dots: true,
    arrows: false,
    autoplay: true,
    infinite: true,
    slidesToShow: 1,
    speed: 500,
    autoplaySpeed: 3500,
    pauseOnHover : false,
    pauseOnFocus: false,
  });
  $(".m-main-full-slide").slick({
    dots: true,
    arrows: false,
    autoplay: true,
    infinite: true,
    slidesToShow: 1,
    speed: 500,
    autoplaySpeed: 3500,
    pauseOnHover : false,
    pauseOnFocus: false,
  });

  $('.sale-item-slide').on('init', function(event, slick) {
    if (slick.slideCount <= slick.options.slidesToShow) {
      $(this).find('.slick-dots').hide();
    }
  });
  $(".sale-item-slide").slick({
    dots: true,
    arrows: false,
    autoplay: true,
    infinite: false,
    slidesToShow: 3,
    speed: 500,
    autoplaySpeed: 3500,
    pauseOnHover : true,
    pauseOnFocus: true,
  });


  $(".review-slide").slick({
    dots: false,
    arrows: false,
    autoplay: true,
    infinite: true,
    slidesToShow: 2,
    speed: 500,
    autoplaySpeed: 3500,
    pauseOnHover : false,
    pauseOnFocus: false,
    responsive: [
      {
        breakpoint: 1280,
        settings: {
          slidesToShow: 3,
        }
      },
      {
        breakpoint: 680,
        settings: {
          slidesToShow: 2,
        }
      },      
    ]
  });

  $(".recipe-slide").slick({
    dots: false,
    arrows: false,
    autoplay: true,
    infinite: true,
    slidesToShow: 6,
    speed: 500,
    autoplaySpeed: 3500,
    pauseOnHover : false,
    pauseOnFocus: false,
    responsive: [
      {
        breakpoint: 1280,
        settings: {
          slidesToShow: 5,
        }
      },
      {
        breakpoint: 820,
        settings: {
          slidesToShow: 3,
        }
      },      
    ]
  });

$(".sale-item-tab li").click(function () {

  const $con = $(this).closest(".main-container");
  const tabId = $(this).data("tab");

  if ($(this).hasClass("active")) return;

  // 탭 active
  $(this).addClass("active").siblings().removeClass("active");
  // 현재 컨텐츠
  $con.find(".sale-item-slide, .no-item").removeClass("active");
  // 다음 컨텐츠
  const $next = $con.find("#" + tabId);
  $next.addClass("active");

  if ($next.hasClass("slick-initialized")) {
    $next.slick("setPosition");
  }

});

  if ($(window).width() <= 1024) {
    $('.m-pop-slide').slick({
      dots: false,
      arrows: false,
      autoplay: true,
      infinite: true,
      slidesToShow: 1,
      speed: 300,
      autoplaySpeed: 3000,
      pauseOnHover : false,
      pauseOnFocus: false,
      fade: true
    });
  }
    

  // sub
  $(".sub-list-slide").slick({
    dots: false,
    arrows: false,
    autoplay: false,
    infinite: false,
    slidesToShow: 3,
    speed: 300,
    autoplaySpeed: 3000,
    pauseOnHover : false,
    pauseOnFocus: false,
    responsive: [
      {
        breakpoint: 1280,
        settings: {
          dots: true,
          slidesToShow: 2,
        }
      },
      {
        breakpoint: 680,
        settings: {
          dots: true,
          slidesToShow: 1,
        }
      },      
    ]
  });
  $(".recom-item-slide1").slick({
    dots: false,
    arrows: false,
    autoplay: true,
    infinite: false,
    slidesToShow: 6,
    speed: 300,
    autoplaySpeed: 3000,
    pauseOnHover : true,
    pauseOnFocus: true,
    responsive: [
      {
        breakpoint: 1280,
        settings: {
          slidesToShow: 5,
        }
      },
      {
        breakpoint: 680,
        settings: {
          slidesToShow: 3,
        }
      },      
    ]
  });
  $(".recom-item-slide2").slick({
    dots: false,
    arrows: false,
    autoplay: true,
    infinite: false,
    slidesToShow: 6,
    speed: 300,
    autoplaySpeed: 3000,
    pauseOnHover : true,
    pauseOnFocus: true,
    responsive: [
      {
        breakpoint: 1280,
        settings: {
          slidesToShow: 5,
        }
      },
      {
        breakpoint: 680,
        settings: {
          slidesToShow: 3,
        }
      },      
    ]
  });





  // 서브 상단 타이틀바
  $('#cateBtn').click(function(){
    $('#cateList').toggle();
  });
  $('#mcateBtn1').click(function(){
    $('.cate-details2').toggle();
  });
    $('#mcateBtn2').click(function(){
    $('.cate-r-ul2').toggle();
  });

  // cate 외부 클릭시 닫기
  $(document).on('click', function (e) {
    if (!$(e.target).closest('.category').length) {
      $('#cateList').hide();
    }
  });
  $(document).on('click', function (e) {
    if (!$(e.target).closest('.m-cate1').length) {
      $('.cate-details2').hide();
    }
  });
  $(document).on('click', function (e) {
    if (!$(e.target).closest('.m-cate2').length) {
      $('.cate-r-ul2').hide();
    }
  });


  $('#gridType').click(function(){
    $(this).addClass('on');
    $('#rowType').removeClass('on');
    $('.prd-list').removeClass('type-row');
  });

  $('#rowType').click(function(){
    $(this).addClass('on');
    $('#gridType').removeClass('on');
    $('.prd-list').addClass('type-row');
  });

  $('.cate-details li').each(function () {
    $(this).toggleClass('open', $(this).find('ul').length > 0);
  });

  // // 장바구니
  // if ($(window).width() <= 680) {
  //   $(".cart-table-ea #buy_box_btn").text("박스");
  // } else {
  //   $("#buy_box_btn").text("박스구매");
  // };


  // 상품 상세
  $('.prd-view-more button').click(function(){
    $('.prd-view-more p').toggleClass('open');
    $(this).toggleClass('open');

    const isOpen = $('.prd-view-more p').hasClass('open');
    $(this).find('span').text(isOpen ? '상세정보 접기' : '상세정보 펼쳐보기');
  });

  $('.low_temp img').attr('src', '/images/icon/snow_low.svg');
  $('.frozen_temp img').attr('src', '/images/icon/snow_frozen.svg');

  // 품절일때 (상품리스트)
  $('.prd-box').each(function () {
    if ($(this).find('.prd-img').hasClass('sold-out')) {
      $(this).find('.prd-check .cart_it_id')
        .prop('disabled', true)
        .prop('checked', false); // 혹시 체크되어 있으면 해제
      $(this).find('#buy_box_btn')
        .prop('disabled', true).css('opacity','0.4');
    }
  });


  // as 접수
  function setSelectedRow() {
    $('.claim-item-table tr').removeClass('on');
    $('.claim-item-table .it_id:checked').each(function () {
      $(this).closest('tr').addClass('on');
    });
  }
  setSelectedRow();

  $('.claim-item-table .it_id').on('change', function () {
    setSelectedRow();
  });


  // 주문내역 ul
  $('.odr-list2').each(function () {
    if ($.trim($(this).html()) === '') {
      $(this).hide();
    }
  });

  // 문의접수
  $('.terms-toggle').on('click', function () {
    const $btn = $(this);
    const $note = $('.terms-note');
    const isOpen = $note.is(':visible');

    $note.slideToggle(200);
    $btn.text(isOpen ? '보기' : '접기');
  });

  // si 모달
  $('.si-cart-btn').click(function(){
    $('.si-cart-modal').show();
    $('.si-bg').show();
  });
  $('.scm-close').click(function(){
    $('.si-cart-modal').hide();
    $('.si-bg').hide();
  });




  // 찾아오시는길  
  /* 지점 선택 */
  $('.brnach-list a').on('click', function (e) {
    e.preventDefault();

    const tabId = $(this).data('tab');
    const text = $(this).text() || '';

    // 버튼 텍스트 변경
    $('.branch-btn').contents().first()[0].textContent = text + ' ';

    // 리스트 닫기
    $('.brnach-list').hide();

    // 지도 탭 변경
    $('.map-wrap').removeClass('show');
    $('#' + tabId).addClass('show');

    // 카카오 지도 리사이즈 트리거 (필수)
    setTimeout(function () {
      window.dispatchEvent(new Event('resize'));
    }, 200);
  });

  // 키친시스템
  const kc_type = new URLSearchParams(location.search).get('type');
  const kc_text = $('.kitchen-list a[href*="type=' + kc_type + '"]').data('name');

  if (kc_text) {
    $('.kitchen-btn').contents().first()[0].textContent = kc_text + ' ';
  };



});

