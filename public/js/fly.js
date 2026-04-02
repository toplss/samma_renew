var itemcount = 0;
var isFlying = false; // ✅ 애니메이션 lock

function flyToElement(flyer, flyingTo, e) {

	// 연타 방지
	if (isFlying) return;
	isFlying = true;

	// 마우스 클릭 좌표 (시작위치)
	var startX = e.clientX;
	var startY = e.clientY;

	var flyerClone = $(flyer).clone();

	// 이미지 스타일 설정 (클릭 지점에서 생성)
	$(flyerClone).css({
		position: 'fixed',
		top: startY,
		left: startX,
		opacity: 0.8,
		width: 20,
		height: 20,
		zIndex: 9999,
		pointerEvents: 'none',
		transform: 'translate(-50%, -50%)'
	});

	$('body').append(flyerClone);

	// 도착 위치
	var cartRect = $(flyingTo)[0].getBoundingClientRect();

	var gotoX = cartRect.left - cartRect.width / 2;
	var gotoY = cartRect.top + cartRect.height * 1.5;

	// 모바일 도착 위치
	if ($(window).width() < 1024) {
		gotoX = window.innerWidth / 3 * 2;
		gotoY = window.innerHeight - 40;
	}

	// 애니메이션
	$(flyerClone).animate({
		left: gotoX,
		top: gotoY,
		width: 30,
		height: 30,
		opacity: 0.8
	}, 200, function () {

		// 장바구니 수량 증가
		itemcount++;
		$(".item-count").text(itemcount);

		// 이미지 제거
		$(flyerClone).fadeOut(120, function () {
			$(this).remove();
			isFlying = false;
		});
	});

	// 카트 아이콘 일시 변경
	$(".cart_btn img").attr('src', '/images/icon/cart-plus.svg').css('transform','scale(1.2)');
	setTimeout(function () {
		$(".cart_btn img").attr('src', '/images/icon/cart.svg').css('transform','none');
	}, 500);
}

// 실행
$(document).ready(function () {
	// 상품리스트
	$('.prd-box .add-to-cart').on('click', function (e) {
		const $box = $(this).closest('.prd-box');
    const isSoldOut = $box.find('.prd-img').hasClass('sold-out');

		// 품절일땐 액션x
		if (isSoldOut) return;

		flyToElement($box.find('.prd-img img').eq(0),$('.cart_cnt'),e);
	});

	// 추천상품 슬라이드
	$('.recom-item-list .add-to-cart').on('click', function (e) {
		const $box = $(this).closest('.recom-item-list');
    const isSoldOut = $box.find('.recom-img').hasClass('sold-out');

		// 품절일땐 액션x
		if (isSoldOut) return;

		flyToElement($box.find('.recom-img img').eq(0),$('.cart_cnt'),e);
	});

	// 메인 장바구니 모달
	$(document).on('click', '.si-cart-modal .scm-cart', function (e) {
		const $box = $(this).closest('.si-cart-modal').find('ul');
		flyToElement($box.find('.scm1 .main_it_img').eq(0), $('.cart_cnt'), e);
	});
});