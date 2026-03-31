<footer>
	<div class="f-box1">
		<img src="{{ asset('images/common/foot1.png') }}">
	</div>
	<ul class="f-box2">
		<li><a href="/customer_service/company">회사소개</a></li>
		<li class="hide-820"><a href="/customer_service/agreement">이용약관</a></li>
		<li class="hide-820"><a href="/customer_service/private">개인정보보호방침</a></li>
		<li><a href="/customer_service/location">찾아오시는 길</a></li>
		<li><a href="/customer_service/alliance?gubun=alliance">제휴문의</a></li>
		<li><a href="/common_board/list">공지사항</a></li>
	</ul>
	<div class="f-box3">
		<img src="{{ asset('images/common/logo2.png') }}">
		<div class="mf-toggle show-820">
			<h6>고객센터 <b>1899-3153</b></h6>
			<p>삼마몰 정보 확인<img src="{{ asset('images/icon/square-down.svg') }}"></p>
		</div>
		<dl>
			<dt>상호 : {{ $activeSiteInfo['company_name'] }}</dt>
			<dd>대표자 : {{ $activeSiteInfo['ceo_name'] }}</dd>
			<dt>사업자등록번호</dt>
			<dd>{{ $activeSiteInfo['company_number'] }}</dd>
			<dt>통신판매업신고번호</dt>
			<dd>{{ $activeSiteInfo['company_order_number'] }}</dd>
			<dt>TEL</dt>
			<dd>
				<a href="tel:{{ preg_replace('/[^0-9+]/', '', $activeSiteInfo['admin_tel']) }}">
        {{ $activeSiteInfo['admin_tel'] }}
				</a>
			</dd>
			<dt>FAX</dt>
			<dd>{{ $activeSiteInfo['fax'] }}</dd>
			<dt>개인정보담당자</dt>
			<dd>{{ $activeSiteInfo['site_privacy'] }}</dd>
			<dt>주소</dt>
			<dd>{{ $activeSiteInfo['address'] }}</dd>
			<dt>이메일</dt>
			<dd>
				<a href="mailto:{{ $activeSiteInfo['mail_from'] }}">
					{{ $activeSiteInfo['mail_from'] }}
				</a>
			</dd>
		</dl>
		<address>&copy; 2026 삼마몰. All Rights Reserved.</address>
	</div>
</footer>

	<!-- 좌측 배너 -->
	<div class="lf-banner">
		<ul class="lf-slide">
			@foreach ($activeLeftBanner as $key => $row)
				@if ($row['rank_order'] == '1')
				<li><a href="{{ $row['banner_url'] }}" target="{{ $row['url_target'] }}"><img src="{{ asset('images/common_data/banner/'.$row['banner_img1']) }}"></a></li>
				@endif
			@endforeach
		</ul>
		<ul class="lf-slide">
			@foreach ($activeLeftBanner as $key => $row)
				@if ($row['rank_order'] == '2')
				<li><a href="{{ $row['banner_url'] }}" target="{{ $row['url_target'] }}"><img src="{{ asset('images/common_data/banner/'.$row['banner_img1']) }}"></a></li>
				@endif
			@endforeach
		</ul>
		<p><label for="close"><input type="checkbox" name="" id="close" value="Y">오늘 보지 않기</label></p>
	</div>

	<!-- 문의하기 버튼 (우측 하단) -->
	<button type="button" id="contactBtn"><img src="{{ asset('images/common/sm_icon.svg') }}"></button>
	<div class="contact_bg"></div>
	<div class="contact_wrap">
		<form name="form_contact" id="form_contact" method="post" action="{{ route('contact_us') }}">
			<input type="text" name="check_bot" id="check_bot" style="display:none">
			@csrf
			<div class="flex-between">
				<h3 class="flex"><img src="{{ asset('images/common/sm_icon.svg') }}">문의하기</h3>
				<button type="button" id="closeBtn"><img src="{{ asset('images/icon/close.svg') }}"></button>
			</div>
			
			<ul class="contact_list">
				<li>
					@if ($activeMember)
						<span>{{ $activeMember['mb_company'] }}</span>
						<input type="hidden" name="contact_company" id="contact_company" value="{{ $activeMember['mb_company'] }}">
					@else
						<input type="text" name="contact_company" id="contact_company" value="" placeholder="업체명을 입력해 주세요." maxlength="20">
					@endif
				</li>
				<li>
					<input type="text" id="contact_phone" name="contact_phone" value="{{ $activeMember['mb_hp'] ?? '' }}" placeholder="연락처를 입력해 주세요." maxlength="20">
				</li>
				<li>
					<textarea name="contact_contents" id="contact_contents" placeholder="문의내용을 입력해 주세요."></textarea>
				</li>
				@if (!$activeMember)
				<li>
					@php
						$contact_make_security_code = mt_rand(0000, 9999); // 보안코드
					@endphp
					<input type="hidden" name="contact_make_security_code" id="contact_make_security_code" value="{{ $contact_make_security_code }}">
					<input type="text" name="contact_security_code" id="contact_security_code" value="" placeholder="보안코드 : {{ $contact_make_security_code }} " maxlength="5"> <span>*보안코드 : {{ $contact_make_security_code }} </span>
				</li>
				@endif
			</ul>
			<button type="button" id="sendBtn" onclick="javascript:chkform_contact_us();">문의하기</button>
		</form>
	</div>

	@if(session()->has('ss_mb_code'))
	<script>
    const isActiveMember = @json($activeMember['mb_id']);
	</script>
	@else
	<script>
	const isActiveMember = '';
	</script>
	@endif

	<script>		
		function chkform_contact_us() {

			if ($("#contact_company").val() == "") {
				validationAlertMessage("업체명을 입력해 주세요.", function() {
					$("#contact_company").focus().select();
				});
				return false;
			}			

			if ($("#contact_phone").val() == "") {
				validationAlertMessage("연락처를 입력해 주세요.", function() {
					$("#contact_phone").focus().select();
				});
				return false;
			}			

			if ($("#contact_contents").val() == "") {
				validationAlertMessage("문의내용을 입력해 주세요.", function() {
					$("#contact_contents").focus().select();
				});
				return false;
			}						

			if(!isActiveMember) {
				if ($("#contact_security_code").val() == "") {
					validationAlertMessage("보안코드를 입력해 주세요.", function() {
						$("#contact_security_code").focus().select();
					});
					return false;
				}						

				if($("#contact_make_security_code").val() != $("#contact_security_code").val()) {
					validationAlertMessage("보안코드가 일치하지 않습니다.", function() {
						$("#contact_security_code").focus().select();
					});
					return false;
				}
			}

			Swal.fire({
				toast : false,
				title: '문의하기',
				text: '문의내용을 등록하시겠습니까?',
				icon: 'question',
				showCancelButton: true,
				confirmButtonText: '확인',
				cancelButtonText: '취소'
			}).then((result) => {
				if (result.isConfirmed) {
					$("#form_contact").submit();
				}
			});      			


			function validationAlertMessage(message, callback = null) {
				Swal.fire({
					toast: false,
					icon: 'warning',
					title: '알림',
					html: message,
					confirmButtonText: '확인'
				}).then(() => {
					if (callback) callback();
				});
			}			

		}
	</script>
	
	<!-- 우측 장바구니 -->
@if($activeMember)
	<div class="m-cart-bg"></div>
  <aside id="cartSidebar">

		<ul id="cartQuickBtn">
			<li class="cart_btn"><a href="javascript:;"><img src="{{ asset('images/icon/cart.svg') }}"><u id="reload_quick_cart_cnt" class="cart_cnt">{{ $activeMyPageTop['cart_cnt'] ?? 0}}</u></a></li>
			<li><a href="/customer_service/user_guide"><img src="{{ asset('images/icon/question.svg') }}"></a></li>
			<li class="viva"><a href="https://vivacook.kr/" target="_blank"><img src="{{ asset('images/icon/viva_icon.png') }}"></a></li>
			<li><a href="https://mygrang.kr/" target="_blank"><img src="{{ asset('images/icon/my_icon.svg') }}"></a></li>
			<li><a href="/common_board/list"><img src="{{ asset('images/icon/megaphone.svg') }}"></a></li>
			<li><a href="javascript:void(0);" class="all_delete_btn"><img src="{{ asset('images/icon/trash.svg') }}"></a></li>
		</ul>

		<div id="mCartQuickBtn">
			<div class="m-quick-txt">
				<img class="mqt1" src="{{ asset('images/icon/bell.gif') }}">
				<p class="mqt2">
					<span class="txt-red">배송요일 하루전 오후 2시이전 주문시 원활한 납품을 받으실 수 있습니다.</span>
					<span class="show-680 txt-blue"> / </span>
					<span class="txt-blue">기업 <span id="mb_virtual_account2"></span></span>
				</p>
			</div>
			<div class="m-quick-btn">
				<button type="button" class="btn2" onclick="location.href='/mypage/my_page'"><img src="{{ asset('images/icon/user.svg') }}"></button>				
				<button type="button" class="btn1" onclick="location.href='/mypage/cart'"><img src="{{ asset('images/icon/cart-w.svg') }}"></button>		
				<button type="button" class="btn3">장바구니<i id="reload_quick_cart_cnt" class="cart_cnt">{{ $activeMyPageTop['cart_cnt'] ?? 0}}</i><span class="total_amount"></span>원 </button>
			</div>
		</div>

		<span id="mCartCloseBtn">
			<img src="{{ asset('images/icon/circle-x2.svg') }}">
		</span>

		
		<script>
		$(document).ready(function(){
			// 장바구니 담기 시도시 수량 카운팅
			basket_count();
		}) 
		</script>

		<form name="common_frm_cart" id="common_frm_cart" method="post" action="{{ route('cartupdate') }}">
			@csrf
			<input type="hidden" name="url" value="/mall/shop/orderform">
			<input type="hidden" name="common_records" id="common_records" value="">
			<input type="hidden" name="act" value="common_buy">

			<div id="cartQuickWrap">
				<div class="quick-top">
					<p class="qp1 flex-center">
						<img src="{{ asset('images/icon/bell.gif') }}">
						<span>납품: <i id="delivery_day"></i></span>
					</p>
					<p class="qp3 show-1024">배송요일 하루전 오후 2시이전 주문시 원활한 납품을 받으실 수 있습니다.</p>
					<p class="qp2">기업 <span id="mb_virtual_account"></span></p>
				</div>
				
				<div class="sm_cart_box">
					<div class="sm_wrap_item" id="sm_wrap_item1">
						<p class="sm_wrap_title">상온제품 장바구니</p>
						<div id="addCartList" class="ac-list1">

						</div>
					</div>

					<div class="sm_wrap_item" id="sm_wrap_item2">
						<p class="sm_wrap_title">저온제품 장바구니</p>
						<div id="addCartList" class="ac-list2">          

						</div>
					</div>

				</div>

				<!-- 합계 -->
				<div class="price-group">
					<ul class="pg1"> 
						<li><span>상온합계</span><p><b class="cost dep_1_amt">0</b><small>원</small></p></li>
						<li><span>저온합계</span><p><b class="cost dep_2_amt">0</b><small>원</small></p></li>
						<li><span>배송비합계</span><p><b class="cost delivery_cost">0</b><small>원</small></p></li>
					</ul>

					<div class="pg2">
						<div>
							<span>총 합계</span>
							<p><b class="total_amount">0</b><small>원</small><u id="mTotalViewBtn">상세보기</u></p>
						</div>
						<div>
							<button type="button" class="btn1 show-1024" onclick="location.href='/mypage/cart'"><img src="{{ asset('images/icon/cart-w.svg') }}"></button>
							<button type="button" onclick="common_chkform_cart('common_buy');">
								<p><span class="total_amount">0</span>원</p>구매하기
							</button>
						</div>
					</div>
				</div>

			</div>
		</form>

			<input type="hidden" id="total_amount" value="">
			<input type="hidden" id="point_balance" value="">
			<input type="hidden" id="foot_cart_count" value="">
			<input type="hidden" id="item_sold_out" value="">
			<input type="hidden" id="diff_amount" value="">

			<input type="hidden" id="alert_message" value="">

			<input type="hidden" id="virtual_account" value="">

			<input type="hidden" id="level_ca_id2" value="">
			<input type="hidden" id="mb_credit_amount" value="">
			<input type="hidden" id="mb_manager_staff_code" value="">
			<input type="hidden" id="mb_business_hp" value="">
			<input type="hidden" id="mb_hp" value="">
			<input type="hidden" id="mb_name" value="">
			<input type="hidden" id="mb_sendcost" value="">
			<input type="hidden" id="mb_order_amount" value="0">

			<input type="hidden" id="wait_order_cnt" value="0">
			<input type="hidden" id="wait_order_date" value="">
			<input type="hidden" id="wait_order_yoil" value="">
			<input type="hidden" id="wait_order_amount" value="">
			<input type="hidden" id="wait_it_name" value="">

			<input type="hidden" id="amount_min_use" value="">
			<input type="hidden" id="min_order_amount" value="">
			<input type="hidden" id="d_order_day" value="">
			<input type="hidden" id="d_order_date" value="">


		<script>
		$(document).ready(function() {

				// 수량차감
				$(document).off('click', '.sit_cart_qty_minus').on('click', '.sit_cart_qty_minus', function(){
					var it_id  = $(this).closest('.cart_item').data('it_id');
					var ct_qty = $(this).closest('.cart_item').find('.ct_qty').val();

					$.post("/mall/proc_query_cart",{
						mode:"cart_update",
						action : 'minus',
						it_id  : it_id,
						ct_qty : ct_qty,
						path : window.location.pathname
					},function(result){

						basket_count()

						var data = JSON.parse(result);

						cart_res(data);

						var path = window.location.pathname;

						if (path == '/mypage/cart') {

							setting_table(data.data);

						}

					});
				});


				// 수량추가
				$(document).off('click', '.sit_cart_qty_plus').on('click', '.sit_cart_qty_plus', function(){
						var it_id  = $(this).closest('.cart_item').data('it_id');
						var ct_qty = $(this).closest('.cart_item').find('.ct_qty').val();

						$.post("/mall/proc_query_cart",{
								mode:"cart_update",
								action :'plus',
								it_id  : it_id,
								ct_qty : ct_qty,
								path : window.location.pathname
						},function(result){

								basket_count()

								var data = JSON.parse(result);

								cart_res(data);

								var path = window.location.pathname;

								if (path == '/mypage/cart') {

									setting_table(data.data);

								}
						});
				});

				// 상품삭제
				$(document).off('click', '.delete_btn').on('click', '.delete_btn', function(){
						var it_id  = $(this).closest('.cart_item').data('it_id');
						var ct_qty = $(this).closest('.cart_item').find('.ct_qty').val();

						$.post("/mall/proc_query_cart",{
								mode:"cart_delete",
								it_id  : it_id,
								path : window.location.pathname
						},function(result){

								basket_count()

								var data = JSON.parse(result);

								cart_res(data);

								var path = window.location.pathname;

								if (path == '/mypage/cart') {

									setting_table(data.data);

								}
						});
				});


				// 모두삭제
				$(document).off('click', '.all_delete_btn').on('click', '.all_delete_btn', function(){
					Swal.fire({
						toast : false,
						title: '장바구니 비우기',
						text: '장바구니를 모두 삭제하시겠습니까?',
						icon: 'warning',
						showCancelButton: true,
						confirmButtonText: '삭제',
						cancelButtonText: '취소'
					}).then((result) => {
						if (result.isConfirmed) {
							$.post("/mall/proc_query_cart",{
								mode :"all_cart_delete",
								path : window.location.pathname
							},function(result){
								basket_count()

								var data = JSON.parse(result);

								cart_res(data);

								var path = window.location.pathname;

								// 장바구니 페이지
								if (path == '/mypage/cart') {
									setting_table(data.data);
								}
							});
						}
					});
				});
				

				// 담기기능
				$(document).off('click', '.add-to-cart').on('click', '.add-to-cart', function(){
					var ul = $(this).closest('ul');
					var it_id = ul.data('item');
					var it_name = ul.find('.prd-name').text();
					var qty = ul.find('.ct_qty').val();
					var min_qty = ul.find('.min_ct_qty').val();
					var it_price = ul.find('.it_price').val() * 1;

					$.post('/mall/proc_query_cart', {
						mode: 'cart_insert',
						it_id: it_id,
						ct_qty: qty,
						path : window.location.pathname
					}, function(res) {
						basket_count();
						cart_res(res); 

						it_price = it_price * min_qty;
						
						ul.find('.ct_qty').val(min_qty);
						ul.find('.field_it_price_').text(it_price.toLocaleString() + '원');

						var isDiscount = ul.find('.price-dis').length > 0;
						if (isDiscount) {
							let org_it_price = ul.find('.org_it_price').val() * 1;
							let org_price = org_it_price * min_qty;
							ul.find('.price-dis').find('del').text(org_price.toLocaleString() + '원');
						}
					}, 'json');
				});


				// 수량증가
				$(document).off('click', '.sit_qty_plus').on('click', '.sit_qty_plus', function(){
					var ul = $(this).closest('ul');
					var it_id = ul.data('item');
					var it_name = ul.find('.prd-name').text();
					var qty = ul.find('.ct_qty').val() * 1;
					var min_qty = ul.find('.min_ct_qty').val() * 1;
					var it_price = ul.find('.it_price').val() * 1;
					var isDiscount = ul.find('.price-dis').length > 0;

					// 묶음판매 수량
					if (min_qty) {
						qty += min_qty;
					} else {
						qty += 1;
					}

					it_price = it_price * qty;

					if (isDiscount) {
						let org_it_price = ul.find('.org_it_price').val() * 1;
						let org_price = org_it_price * qty;
						ul.find('.price-dis').find('del').text(org_price.toLocaleString() + '원');
					}

					ul.find('.ct_qty').val(qty);
					ul.find('.field_it_price_').text(it_price.toLocaleString() + '원');
				});



				// 수량차감
				$(document).off('click', '.sit_qty_minus').on('click', '.sit_qty_minus', function(){
					var ul = $(this).closest('ul');
					var it_id = ul.data('item');
					var it_name = ul.find('.prd-name').text();
					var qty = ul.find('.ct_qty').val() * 1;
					var min_qty = ul.find('.min_ct_qty').val() * 1;
					var it_price = ul.find('.it_price').val() * 1;
					var isDiscount = ul.find('.price-dis').length > 0;

					if (min_qty == qty) {
						Swal.fire({
							toast : false,
							icon : 'info',
							html: `최소 주문 수량은 <span style="color:red;">${min_qty}개</span>입니다. <br>해당 수량 미만은 주문할 수 없습니다.`
						});
						return false;
					}

					if (qty < 2) return false;
					
					// 묶음판매 수량
					if (min_qty) {
						qty -= min_qty;
					} else {
						qty -= 1;
					}

					it_price = it_price * qty;

					if (isDiscount) {
						let org_it_price = ul.find('.org_it_price').val() * 1;
						let org_price = org_it_price * qty;
						ul.find('.price-dis').find('del').text(org_price.toLocaleString() + '원');
					}

					ul.find('.ct_qty').val(qty);
					ul.find('.field_it_price_').text(it_price.toLocaleString() + '원');
				});
		})

		// 박스구매 
		function buy_box_qty(e) {
			var ul = $(e).closest('ul');
			var it_id = ul.data('item');
			var it_name = ul.find('.prd-name').clone().find('p').remove().end().text().trim();
			var it_gubun = ul.find('.it_gubun').val();
			var qty = ul.find('.buy_box_qty').val();
			var message = '';


			if (it_gubun == 'pcs') {
				var pcs = ul.find('.it_box_sale_tot').val();
				message = `<span style="color:#e02f30">${it_name}</span><br>${pcs}입*1박스로 구매 하시겠습니까?`;
			}
			if (it_gubun == 'pack') {
				var pcs = ul.find('.it_box_sale_pcs').val();
				var pack = ul.find('.it_box_sale_pack').val();
				var total = ul.find('.it_box_sale_tot').val();
				message = `<span style="color:#e02f30">${it_name}</span><br>${pcs}입*${pack}팩*${total}개*1박스로 구매 하시겠습니까?`;
			}


			Swal.fire({
				toast : false,
				title: '구매 확인',
				html: message,
				icon: 'question',
				showCancelButton: true,
				confirmButtonText: '구매',
				cancelButtonText: '취소'
			}).then((result) => {
				if (result.isConfirmed) {
					$.post('/mall/proc_query_cart', {
						mode: 'cart_insert',
						it_id: it_id,
						ct_qty: qty,
						path : window.location.pathname
					}, function(res) {
						basket_count();
						cart_res(res); 
					}, 'json');
				}
			});
		}


		function basket_count() {
			setTimeout(function(){
				var cart_count    = $('#foot_cart_count').val() || 0;
				var alert_message = $('#alert_message').val();

				if ( cart_count > 0 && $(window).width() >= 1024 ){
					$('#cartSidebar').addClass('open');
				};
				if ( cart_count < 1 && $(window).width() >= 1024 ){
					$('#cartSidebar').removeClass('open');
				};


				if (alert_message) {
					alert(alert_message);
				};

				$('.cart_cnt').text(cart_count);
				$('.top_cart_cnt').text(cart_count);
			}, 200)
		}
		</script>


		<script type="text/javascript">
		function common_chkform_cart(act) {
				var frm = $('#common_frm_cart')[0];
				var cnt = $('#common_records').val();

				if (act == "common_buy") {
					var manager_staff_hp = '';
					var manager_staff_info_str = '';

					var mb_order_amount = $('#mb_order_amount').val() * 1;
					var mb_credit_amount = $('#mb_credit_amount').val() * 1;
					var total_amount    = $('#total_amount').val() * 1;
					var diff_amount     = $('#diff_amount').val() * 1;
					var level_ca_id2    = $('#level_ca_id2').val();
					var mb_credit_type  = $('#mb_credit_type').val();
					var item_sold_out   = $('#item_sold_out').val() * 1;

					var mb_manager_staff_code = $('#mb_manager_staff_code').val();
					var mb_business_hp      = $('#mb_business_hp').val();
					var mb_hp               = $('#mb_hp').val();
					var mb_name             = $('#mb_name').val();
					var mb_virtual_account  = $('#virtual_account').val();
					var mb_sendcost         = $('#mb_sendcost').val();

					var wait_order_cnt  = $('#wait_order_cnt').val() * 1;
					var wait_order_date = $('#wait_order_date').val();
					var wait_order_yoil = $('#wait_order_yoil').val();
					var wait_order_amount = $('#wait_order_amount').val() * 1;
					var wait_it_name    = $('#wait_it_name').val();

					var amount_min_use    = $('#amount_min_use').val();
					var min_order_amount  = $('#min_order_amount').val();

					var confirm_content = '';

					if (level_ca_id2 == '20' && mb_credit_type == '1' && mb_credit_amount > 0 && total_amount > mb_credit_amount) {
						if (mb_manager_staff_code) {
							manager_staff_hp = mb_business_hp ? mb_business_hp : mb_hp;
							manager_staff_info_str = `담당자 : ${mb_name} ${manager_staff_hp}`;
						}

						confirm_content = `
						<span style="line-height:2; color:#e02f30; font-weight:bold;">여신 한도금액 ${mb_credit_amount.toLocaleString()}원이 초과되었습니다.</span><br>
						<span style="color:#e02f30; font-weight:bold;">초과된 금액은 ${diff_amount.toLocaleString()}원 입니다.</span><br>
						<span style="color:#0d6efd; font-weight:normal;">초과된 금액을 입금하시거나 <br>초과된 주문건을 취소 후 구매 부탁드립니다.</span><br>${manager_staff_info_str}`;
					} else if (wait_order_cnt > 0) {
						confirm_content = `
						<span style="line-height:2; color:#e02f30; font-weight:bold;">미입금 주문건이 ${wait_order_cnt}건 있습니다.</span><br>
						<span style="color:#000; font-weight:bold;">주문날짜 : ${wait_order_date} ${wait_order_yoil}</span><br>
						<span style="color:#000; font-weight:bold;">입금금액 : ${wait_order_amount.toLocaleString()}원</span><br>
						<span style="color:#0d6efd; font-weight:normal;">입금계좌 : ${mb_virtual_account}</span><br>
						<span style="color:#000; font-weight:normal;">가상계좌로 입금 후 추가구매 부탁드립니다.<br>반영까지 약 1~2분 소요됩니다.<br>미입금 주문건은 마이페이지에서 확인 가능합니다.</span>`;
					} else {
						if (item_sold_out > 0) {
							confirm_content = `
							장바구니에 담긴 상품을 구매하시겠습니까?
							<br>
							<span style="line-height:2; color:#e02f30; font-weight:bold;">총 ${item_sold_out}개의 품절상품은 자동 삭제 됩니다.</span>
							`;

						} else if (amount_min_use == '1' && total_amount < min_order_amount && mb_sendcost == '1') {
							confirm_content = `
							<span style="line-height:2; color:#e02f30; font-weight:bold;">최소 주문금액이 ${min_order_amount.toLocaleString()}원 이상시 배송이 진행됩니다.</span>
							<br>장바구니에 담긴 상품을 구매하시겠습니까?`;
						} else {
							if (total_amount < mb_order_amount) {
								confirm_content = `
								<span style="line-height:2; color:#e02f30; font-weight:bold;">주문금액이 ${mb_order_amount.toLocaleString()}원 미만시 배송비가 부과됩니다.</span>
								<br>장바구니에 담긴 상품을 구매하시겠습니까?`;
							} else if (total_amount > mb_order_amount) {
								confirm_content = '장바구니에 담긴 상품을 구매하시겠습니까?';
							}
						}
					}

					
					// SweetAlert2 적용
					if (level_ca_id2 == '20' && mb_credit_type == '1' && mb_credit_amount > 0 && total_amount > mb_credit_amount) {
						Swal.fire({
							toast : false,
							title: '<i class="fas fa-solid fa-plus"></i> 상품 구매하기',
							html: confirm_content,
							icon: 'warning',
							showCancelButton: true,
							confirmButtonText: '닫기',
							cancelButtonText: '취소',
							focusConfirm: false
						});
					} else if (level_ca_id2 == '10' && wait_order_cnt > 0) {
						Swal.fire({
							toast : false,
							title: '<i class="fas fa-solid fa-plus"></i> 상품 구매하기',
							html: confirm_content,
							icon: 'warning',
							showCancelButton: true,
							confirmButtonText: '닫기',
							cancelButtonText: '취소',
							focusConfirm: false
						});
					} else {
						if (total_amount == 0) {
							Swal.fire({
								toast : false,
								title: '<i class="fas fa-solid fa-plus"></i> 상품 구매하기',
								text: '장바구니에 담긴 상품이 없습니다.',
								icon: 'info',
								confirmButtonText: '닫기'
							});
						} else if (wait_order_cnt > 0) {
							Swal.fire({
								toast : false,
								title: '<i class="fas fa-solid fa-plus"></i> 상품 구매하기',
								html: confirm_content,
								icon: 'warning',
								confirmButtonText: '닫기',
								focusConfirm: false
							});
						} else {
							Swal.fire({
								toast : false,
								title: '<i class="fas fa-solid fa-plus"></i> 상품 구매하기',
								html: confirm_content,
								icon: 'question',
								showCancelButton: true,
								confirmButtonText: '확인',
								cancelButtonText: '취소'
							}).then((result) => {
								if (result.isConfirmed) {
									frm.act.value = act;
									frm.submit();
								}
							});
						}
					}
				}

		}
		</script>
		
	</aside>
	<input type="hidden" id="generate_hash" value="{{ $ss_hash }}" />
@else
	<ul id="cartQuickBtn" class="nonmember">
		<li><a href="/customer_service/user_guide"><img src="{{ asset('images/icon/question.svg') }}"></a></li>
		<li class="viva"><a href="https://vivacook.kr/" target="_blank"><img src="{{ asset('images/icon/viva_icon.png') }}"></a></li>
		<li><a href="https://mygrang.kr/" target="_blank"><img src="{{ asset('images/icon/my_icon.svg') }}"></a></li>
		<li><a href="/common_board/list"><img src="{{ asset('images/icon/megaphone.svg') }}"></a></li>
	</ul>
@endif

<!-- 장비구매 팝업 -->
<script>
$('.Qua-call').on('click', function () {
	Swal.fire({
	toast : false,
	title: '장비 구매',
	html: '<b>고객센터에 문의하시겠습니까?',
	icon: 'question',
	showCancelButton: true,
	confirmButtonText: '확인',
	cancelButtonText: '취소'
	}).then((result) => {
		if (result.isConfirmed) {
			var ul = $(this).closest('ul');
			var it_id = ul.data('item');
			var it_name = ul.find('.prd-name').text().trim();

			$.post("{{ route('equip_qa') }}", {
				it_id : it_id,
				it_name : it_name
			}, function(res) {
				if (res.status == 'success') {
					Swal.fire({
						toast: false,
						icon: 'info',
						title: '알림',
						html: res.message,
						confirmButtonText: '확인'
					});
				} else {
					Swal.fire({
						toast: false,
						icon: 'warning',
						title: '알림',
						html: res.message,
						confirmButtonText: '확인'
					});
				}
			}, 'json');
		}
	});
});


$('.qa_btn').on('click', function () {
	Swal.fire({
	toast : false,
	title: '장비 구매',
	html: '<b>고객센터에 문의하시겠습니까?',
	icon: 'question',
	showCancelButton: true,
	confirmButtonText: '확인',
	cancelButtonText: '취소'
	}).then((result) => {
		if (result.isConfirmed) {
			var ul = $(this).closest('.prd-view-wrap');
			var it_id = ul.data('item');
			var it_name = ul.find('#sit_title').text().trim();

			$.post("{{ route('equip_qa') }}", {
				it_id : it_id,
				it_name : it_name
			}, function(res) {
				if (res.status == 'success') {
					Swal.fire({
						toast: false,
						icon: 'info',
						title: '알림',
						html: res.message,
						confirmButtonText: '확인'
					});
				} else {
					Swal.fire({
						toast: false,
						icon: 'warning',
						title: '알림',
						html: res.message,
						confirmButtonText: '확인'
					});
				}
			}, 'json');
		}
	});
});



// 메인상품 장바구니 팝업
$('.si-cart-btn').click(function(){
	var it_id = $(this).closest('ul').find('.main_show_item').val();

	$.post("/mall/item/show", {
		it_id  : it_id,
	},function(res){
		
		if (res.status == 'success') {
			var result = res.data;
			var scm1_icon = '';
			var scm2_return = '';
			var scm2_piece = '';

			if (result.it_storage == 'frozen_temp') scm1_icon = `/images/icon/snow_frozen.svg`;
			if (result.it_storage == 'low_temp') scm1_icon = `/images/icon/snow_low.svg`;

			if (result.it_return_use == '1') {
				scm2_return = (result.it_return_label == '반품불가') 
					? `<span class="return_x">반품불가</span>`
					: `<span class="return_o">반품가능</span>`;
			}

			if (result.it_price_piece_use == '1') {
				var it_price_piece = parseInt(result.it_price_piece);
				scm2_piece = `<u>개당 ${it_price_piece.toLocaleString()}원</u>`;
			}

			$('.scm1').find('.main_it_img').attr('src', result.img_url);
			$('.scm1').find('span').attr('class', result.it_storage);
			$('.scm1').find('span').html(`<img src="${scm1_icon}">` + result.it_storage_label);
			$('.scm2').html(result.it_name + result.it_basic + ` <p>${scm2_return}${scm2_piece}</p>`);
			$('.scm3').find('.cart_ct_qty').val(result.min_ct_qty);
			$('.scm3').find('.min_ct_qty').val(result.min_ct_qty);

			var scm4 = `<input type="hidden" class="main_show_item" value="${it_id}" />`;

			if (result.cust_price > 0) {
				scm4 += `
				<input type="hidden" class="it_price" value="${result.price}" />
				<input type="hidden" class="org_it_price" value="${result.row_list_cust_it_price}" />

				<del>${parseInt(result.row_list_cust_it_price).toLocaleString()}원</del>
				<span class="discount"><b>${result.it_cust_rate}%</b>${parseInt(result.row_list_field_it_price).toLocaleString()}원</span>
				`;
			} else {
				scm4 += `
				<input type="hidden" class="it_price" value="${result.price}" />
				<span class="price">${parseInt(result.row_list_field_it_price).toLocaleString()}원</span>
				`;
			}

			$('.scm4').html(scm4);
			$('.si-cart-modal').find('.total_price').text(parseInt(result.row_list_field_it_price).toLocaleString() + '원');

			$('.si-cart-modal').show();
			$('.si-bg').show();

		} else {
			var message = res.message;

			Swal.fire({
				toast: false,
				icon: 'warning',
				title: '알림',
				html: message,
				confirmButtonText: '확인'
			});
		}

	}, 'json');
});


$(document).off('click', '.scm3 .sit_qty_plus').on('click', '.scm3 .sit_qty_plus', function(){
	var scm3 = $(this).closest('.scm3');
	var scm4 = $(this).closest('.scm3').next('.scm4');

	var it_id = scm4.find('.main_show_item').val();
	var qty = scm3.find('.cart_ct_qty').val() * 1;
	var min_qty = scm3.find('.min_ct_qty').val() * 1;
	var it_price = scm4.find('.it_price').val() * 1;
	var isDiscount = scm4.find('.price-dis').length > 0;

	// 묶음판매 수량
	if (min_qty) {
		qty += min_qty;
	} else {
		qty += 1;
	}

	it_price = it_price * qty;

	if (isDiscount) {
		let org_it_price = scm4.find('.org_it_price').val() * 1;
		let org_price = org_it_price * qty;
		scm4.find('.price-dis').text(org_price.toLocaleString() + '원');
	}

	scm3.find('.cart_ct_qty').val(qty);
	scm4.closest('div').find('.total_price').text(it_price.toLocaleString() + '원');
});



// 수량차감
$(document).off('click', '.scm3 .sit_qty_minus').on('click', '.scm3 .sit_qty_minus', function(){
	var scm3 = $(this).closest('.scm3');
	var scm4 = $(this).closest('.scm3').next('.scm4');

	var it_id = scm4.find('.main_show_item').val();
	var qty = scm3.find('.cart_ct_qty').val() * 1;
	var min_qty = scm3.find('.min_ct_qty').val() * 1;
	var it_price = scm4.find('.it_price').val() * 1;
	var isDiscount = scm4.find('.price-dis').length > 0;

	if (min_qty == qty) {
		Swal.fire({
			toast : false,
			icon : 'info',
			html: `최소 주문 수량은 <span style="color:red;">${min_qty}개</span>입니다. <br>해당 수량 미만은 주문할 수 없습니다.`
		});
		return false;
	}

	if (qty < 2) return false;
	
	// 묶음판매 수량
	if (min_qty) {
		qty -= min_qty;
	} else {
		qty -= 1;
	}

	it_price = it_price * qty;

	if (isDiscount) {
		let org_it_price = scm4.find('.org_it_price').val() * 1;
		let org_price = org_it_price * qty;
		scm4.find('.price-dis').text(org_price.toLocaleString() + '원');
	}

	scm3.find('.cart_ct_qty').val(qty);
	scm4.closest('div').find('.total_price').text(it_price.toLocaleString() + '원');
});

$(document).off('click', '.si-cart-modal .scm-cart').on('click', '.si-cart-modal .scm-cart', function(){
	var scm3 = $(this).closest('.si-cart-modal').find('.scm3');
	var scm4 = $(this).closest('.si-cart-modal').find('.scm3').next('.scm4');

	var it_id = scm4.find('.main_show_item').val();
	var qty = scm3.find('.cart_ct_qty').val() * 1;
	var min_qty = scm3.find('.min_ct_qty').val() * 1;
	var it_price = scm4.find('.it_price').val() * 1;
	var isDiscount = scm4.find('.price-dis').length > 0;

	$.post('/mall/proc_query_cart', {
		mode: 'cart_insert',
		it_id: it_id,
		ct_qty: qty,
		path : window.location.pathname
	}, function(res) {
		basket_count();
		cart_res(res); 
		
		$('.scm-close').trigger('click');

	
		// it_price = it_price * min_qty;
		
		// scm3.find('.cart_ct_qty').val(min_qty);
		// scm4.closest('div').find('.total_price').text(it_price.toLocaleString() + '원');

		// var isDiscount = ul.find('.price-dis').length > 0;
		// if (isDiscount) {
		// 	let org_it_price = scm4.find('.org_it_price').val() * 1;
		// 	let org_price = org_it_price * min_qty;
		// 	scm4.find('.price-dis').text(org_price.toLocaleString() + '원');
		// }
	}, 'json');

});

$('.scm-close').click(function(){
	$('.scm1').find('img').attr('src', '');

	$('.si-cart-modal').hide();
	$('.si-bg').hide();
});
</script>

</body>
</html>