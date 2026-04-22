<?php

use App\Http\Controllers\IntroController;
use App\Http\Controllers\JoinController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\ShopController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ShopCartApi;
use App\Http\Controllers\BuyChargeItemsController;
use App\Http\Controllers\CommonController;
use App\Http\Controllers\CustomerServiceController;
use App\Http\Controllers\MyPageController;
use App\Http\Controllers\MyCartController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\UserGuideController;
use App\Http\Controllers\MainProductItemController;
use App\Http\Controllers\Api\MallShopApi;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/********** 개발용 테스트 임시페이지 **********/
Route::get('/dev_test', [JoinController::class, 'dev_func'])->name('dev_test');


/********** 인트로 **********/
// Route::get('/intro', function () {
//     return view('intro');
// });


/********** 공격자 ip 차단 **********/
Route::middleware(['block.ip'])->group(function () {
    Route::get('/intro', [IntroController::class, 'intro'])->name('intro');

    // 임시
    Route::get('/intro2', [IntroController::class, 'intro2']);


    // Route::get('/mall/browse_products', [MainController::class, 'index'])->name('browse_products');
    Route::get('/', [MainController::class, 'index'])->name('/');



    Route::get('/503', function() {
        return view('errors.503');
    });


    Route::middleware(['refresh.limit'])->group(function () {


        /********** 로그인 **********/
        Route::post('/login', [LoginController::class, 'login'])->name('login');


        /********** 로그아웃 **********/
        Route::get('/logout', [LoginController::class, 'logout']);

        
        Route::post('/logout', [LoginController::class, 'logout'])->name('logout');



        /********** 가입약관 동의 **********/
        Route::get('/join_step_agree', function() { return view('mypage.join_step_agree'); });

        
        /********** 로그인 **********/
        // Route::get('/login', function() { return view('mypage.login'); });
        Route::get('/login', [IntroController::class, 'intro'])->name('login_intro');


        /********** 회원가입 **********/
        Route::get('/join', [JoinController::class, 'MemberJoin']);

        Route::post('/check_id', [JoinController::class, 'CheckId'])->name('check_id');

        Route::post('/check_company_no', [JoinController::class, 'CheckCompanyNo'])->name('check_company_no');

        Route::post('/check_coupon_no', [JoinController::class, 'CheckCouponNo'])->name('check_coupon_no');

        Route::post('/check_recommend_id', [JoinController::class, 'CheckRecommendId'])->name('check_recommend_id');

        Route::get('/franchise_sub_category', [JoinController::class, 'FranchiseSubCategory'])->name('franchise_sub_category');

        Route::post('/join_save', [JoinController::class, 'MemberJoinSave'])->name('join_save');
        



        /********** 공지사항 **********/
        Route::get('/common_board/list', [CustomerServiceController::class, 'MyPageBoardList'])->name('notice');



        Route::get('/common_board/view', [CustomerServiceController::class, 'MyPageBoardView']);



        Route::get('/common_board/write', [CustomerServiceController::class, 'MyPageBoardWirte']);



        Route::post('/common_board/write', [CustomerServiceController::class, 'MyPageBoardSave'])->name('notice_save');



        Route::post('/common_board/delete', [CustomerServiceController::class, 'MyPageBoardDelete'])->name('notice_delete');



        Route::get('/common_board/file_del', [CustomerServiceController::class, 'MyPageBoardFileDelete']);



        Route::get('/common_board/file_download', [CustomerServiceController::class, 'MyPageBoardFileDownload']);




        /********** 둘러보기 **********/
        // Route::get('/mall/browse_products', [MainController::class, 'index'])->name('browse_products');



        /********** 상품목록 **********/
        Route::get('/mall/shop/list', [ShopController::class, 'ShopList'])->name('shop.list');



        /********** 상품상세 **********/
        Route::get('/mall/shop/view', [ShopController::class, 'ShopDetail'])->name('view');



        /********** 상품검색 목록 **********/
        Route::get('/mall/shop/list_all', [ShopController::class, 'ShopListAll'])->name('list_all');



        /********** 비바쿡 목록 **********/
        Route::get('/mall/shop/list_gubun', [ShopController::class, 'ShopCookList']);



        /********** 제휴문의 **********/
        Route::get('/customer_service/alliance', function() { 
            return view('customer_service.alliance'); 
        });


        /********** 제휴문의 저장 **********/
        Route::post('/customer_service/alliance/save', [CustomerServiceController::class, 'MyPageAllianceSave'])->name('alliance_save');

        /********** 고객센터 **********/
        Route::get('/customer_service/user_guide', [CustomerServiceController::class, 'MyPageUserGuide']);



        /********** 회사소개 **********/
        Route::get('/customer_service/company', [UserGuideController::class, 'CompanyGuide']);


            
        /********** 이용약관 **********/
        Route::get('/customer_service/agreement', [UserGuideController::class, 'Agreement']);



        /********** 개인정보보호방침 **********/
        Route::get('/customer_service/private', [UserGuideController::class, 'PrivatPolicy']);



        /********** 키친시스템 **********/
        Route::get('/public/sub/kitchen_system_01', function () {
            return view('public.sub.kitchen_system_01');
        });

        Route::get('/public/sub/kitchen_system_02', function () {
            return view('public.sub.kitchen_system_02');
        });

        Route::get('/public/sub/kitchen_system_03', function () {
            return view('public.sub.kitchen_system_03');
        });

        Route::get('/public/sub/kitchen_system_04', function () {
            return view('public.sub.kitchen_system_04');
        });

        Route::get('/public/sub/kitchen_system_05', function () {
            return view('public.sub.kitchen_system_05');
        });



        /********** 찾아오시는길 **********/
        Route::get('/customer_service/location', function() { 
            return view('customer_service.location'); 
        });

        /********** 이벤트 **********/
        Route::get('/customer_service/event', function () {
            return view('customer_service.event');
        });
        Route::get('/customer_service/event_view', function () {
            return view('customer_service.event_view');
        });


        /********** 퀵 메뉴 하단 문의하기 **********/
        Route::Post('contact_us', [CustomerServiceController::class, 'ContactUsSave'])->name('contact_us');    


        /********** 로그인 세션 그룹 **********/
        Route::middleware(['auth.session'])->group(function () {
                
            /********** 메인 **********/
            // Route::get('/', [MainController::class, 'index'])->name('/');

            /********** 장비구매 **********/
            Route::get('/mall/shop/list_gubun_equipment', [ShopController::class, 'ShopItemEquipList']);



            /********** A/S부속품 **********/
            Route::get('/mall/shop/list_gubun_accessory', [ShopController::class, 'ShopItemEquipList']);


            /********** 레시피 주문 **********/
            Route::get('/mall/shop/list_gubun_recipe', [ShopController::class, 'ShopRecipeList'])->name('list_gubun_recipe');



            /********** 레시피 주문 **********/
            Route::get('/mall/shop/list_gubun_recipe_view', [ShopController::class, 'ShopRecipeView'])->name('list_gubun_recipe_view');



            /********** 레시피 주문 **********/
            Route::get('/mall/shop/list_gubun_recipe_pop', [ShopController::class, 'ShopRecipePop'])->name('list_gubun_recipe_pop');



            /********** 체인점 주문 **********/
            Route::get('/mall/shop/list_gubun_chain', [ShopController::class, 'ShopChainList'])->name('list_gubun_chain');


            /********** 간편 주문 **********/
            Route::get('/mall/shop/list_gubun_simple', [ShopController::class, 'ShopItemSimple'])->name('list_gubun_simple');


            /********** 내지역 주문 **********/
            Route::get('/mall/shop/list_gubun_area', function () {
                return view('mall.shop.list_gubun_area');
            });

            



            /********** 충전금 구매 **********/
            Route::get('/mall/shop/list_charge', [BuyChargeItemsController::class, 'ListCharege'])->name('list_charge');


            Route::post('/mall/shop/list_charge', [BuyChargeItemsController::class, 'BuyChareItem'])->name('buy_charge');


            /********** 카트 구매 **********/
            Route::post('/mall/shop/cartupdate', [ShopController::class, 'CartUpdate'])->name('cartupdate');



            /********** 주문내역 **********/
            Route::get('/mypage/orderinquiry', [MyPageController::class, 'orderinquiry'])->name('orderinquiry');
            

            /********** 주문정보 View **********/
            Route::get('/mypage/orderinquiryview', [MyPageController::class, 'OrderInquiryView'])->name('orderinquiryview');
            

            /********** 주문정보 상세보기 팝업 **********/
            Route::get('/mypage/orderinquiry_pop', [MyPageController::class, 'OrderInquiryPop'])->name('orderinquiry_pop');

        
            /********** 보유장비 **********/
            Route::get('/mypage/my_equipment_list', [MyPageController::class, 'MyEquipmentList'])->name('my_equipment_list');

        
            /********** 장바구니 **********/
            Route::get('/mypage/cart', [MyCartController::class, 'MyCartList']);
            
        
            /********** 충전금내역 (선불업체 전용) **********/
            Route::get('/mypage/my_point', [MyPageController::class, 'MyPoint'])->name('my_point');

        
            /********** 적립금내역 (선불업체 전용) **********/
            Route::get('/mypage/my_point_reserve', [MyPageController::class, 'MyPointReserve'])->name('my_point_reserve');


            /********** 적립금 상세내역 팝업 (선불업체 전용) **********/
            Route::get('/mypage/my_point_reserve_detail_pop', [MyPageController::class, 'MyPointReserveDetailPop'])->name('my_point_reserve_detail_pop');


            /********** 입금내역 **********/
            Route::get('/mypage/deposit_history', [MyPageController::class, 'DepositHistory'])->name('deposit_history');

        
            /********** 회원정보수정 **********/
            Route::get('/mypage/register_access', function () {
                return view('mypage.register_access');
            });

            Route::Post('/mypage/check_pass', [MyPageController::class, 'CheckPass'])->name('check_pass');

            Route::get('/mypage/register_edit', [MyPageController::class, 'RegisterEdit'])->name('register_edit');

            Route::get('/mypage/download_account_file', [MyPageController::class, 'DownloadAccountFile'])->name('download_account_file');

            Route::Post('/mypage/register_edit_save', [MyPageController::class, 'RegisterEditSave'])->name('register_edit_save');

            Route::get('/mypage/my_page', function () {
                return view('mypage.my_page');
            });


            /********** 상담문의 **********/
            Route::get('/customer_service/my_qa_list', [CustomerServiceController::class, 'MyPageQaList']);

            

            Route::get('/customer_service/my_qa_view', [CustomerServiceController::class, 'MyPageQaView']);


            /********** 상담문의 삭제 **********/
            Route::get('/customer_service/my_qa_delete', [CustomerServiceController::class, 'MyPageQaDel']);



            Route::get('/customer_service/my_qa_modify', [CustomerServiceController::class, 'MyPageModifyView']);
            

            /********** 상담문의 작성 **********/
            Route::get('/customer_service/my_qa_write', [CustomerServiceController::class, 'MyPageQaWrite']);


            
            Route::post('/customer_service/my_qa_write', [CustomerServiceController::class, 'MyPageQaSave'])->name('my_qa_save');



            Route::get('/customer_service/my_contact_write', [CustomerServiceController::class, 'MyPageContactWrite']);



            Route::post('/customer_service/my_contact_write', [CustomerServiceController::class, 'MyPageContactSave'])->name('my_contact_save');



            /********** 반품접수 **********/
            Route::get('/customer_service/return_reception', [CustomerServiceController::class, 'MyPageReturnReceptionList'])->name('return_list');

            Route::post('/customer_service/return_reception', [CustomerServiceController::class, 'MyPageReturnReceptionSave'])->name('return_save');


            
            /********** A/S접수 **********/
            Route::get('/customer_service/as_reception', [CustomerServiceController::class, 'MyPageAsReception']);


            /********** A/S접수 **********/
            Route::post('/customer_service/as_reception', [CustomerServiceController::class, 'MyPageAsReceptionSave'])->name('as_save');


            /********** 클레임접수 **********/
            Route::get('/customer_service/claim', function () {
                return view('customer_service.claim');
            });


            /********** 클레임접수 **********/
            Route::post('/customer_service/claim', [CustomerServiceController::class, 'MyPageClaimSave'])->name('claim_save');


            /********** 장비문의 **********/
            Route::post('/customer_service/equip-qa', [CustomerServiceController::class, 'MyPageEquipQASave'])->name('equip_qa');


            /********** 파일 다운로드 (공통) **********/
            Route::get('donwload', [CommonController::class, 'fileDonwload'])->name('download');



            /********** 결재관련 route **********/
            Route::prefix('payment')->group(function () {

                
                Route::get('/test', [PaymentController::class, 'test']);   // 요청 페이지


                // 결재전 품절체크
                Route::post('checkSoldout', [MallShopApi::class, 'checkSoldout']);

                
                // 결재전 입력정보 저장
                Route::post('orderdata', [MallShopApi::class, 'orderdata']);


                Route::get('request', [PaymentController::class, 'request'])->middleware('no-back-history')->name('payment.request');   // 결제 요청


                // 결제완료후 view 페이지
                Route::get('result', [PaymentController::class, 'orderformview'])->name('payment.view');


                // 결제 완료 후 사용자 리턴 (PC)
                Route::post('result', [PaymentController::class, 'result'])->name('payment.result');


                // PC 팝업 내부 URL
                Route::get('popup', [PaymentController::class, 'popup'])->name('payment.popup');


                // PC 팝업 닫기 URL
                Route::get('close', [PaymentController::class, 'close'])->name('payment.close');


                // 사인키 갱신
                Route::get('refresh', [PaymentController::class, 'refreshSignature'])->name('payment.refresh');


                # 모바일 결제요청
                Route::get('noti', [PaymentController::class, 'noti'])->name('payment.noti');


                # 모바일 완료
                Route::get('noti_result', [PaymentController::class, 'noti_result'])->name('payment.noti_result');


                # 모바일 승인처리
                Route::post('approve', [PaymentController::class, 'approve'])->name('payment.approve');

            });

        });


        
    });


    # 장바구니 기능
    Route::post('/mall/proc_query_cart', [ShopCartApi::class, 'execute'])->middleware('auth.session');

    # 장바구니 구매전 품절체크
    Route::post('check_soldout_cnt', [ShopCartApi::class, 'check_soldout_cnt'])->name('check_soldout_cnt');


    # 메인상품 팝업용
    Route::post('/mall/item/show', [MainProductItemController::class, 'show']);


    # 주문취소 기능
    Route::post('/mall/order/order_cancel', [MyCartController::class, 'MyOrderCancel'])->middleware('auth.session');


    Route::post('duplicate_login_check', [LoginController::class, 'duplicateLoginCheck'])->name('duplicate_login_check');
});

