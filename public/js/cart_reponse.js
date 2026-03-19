$(function(){
    // 로그인 사용자 
    var generate_hash = $('#generate_hash').val();


    if (generate_hash) {
        $.post({
            url : '/mall/proc_query_cart',
            data : {
                mode : 'show'
            },
            dataType : 'json'
        }).done(function(res){
            cart_res(res);
        }).fail(function(error){
            console.log(error);
        });
    }
});

function cart_res(data) {

    var res = data.data;

    var img_url = "/images/item/";

    info_data_set(res);

    if (data.status == 'success') {
        var cart_count = 0;
        var deilivery_cost = res.deilivery_cost;

        var dep_1 = ``; // 상온제품
        var dep_2 = ``; // 저온제품

        var dep_1_amt = 0;
        var dep_2_amt = 0;

        var $form = $("#common_frm_cart");

        $form.find('input[name="it_id[]"]').remove();
        $form.find('input[name="it_name[]"]').remove();
        $form.find('input[name="common_ct_chk[]"]').remove();

        $.each(res.cart_items, function(e) {
            var cartList = res.cart_items[e];


            $.each(cartList, function(i, val) {
                cart_count++;

                $form.append(
                    $('<input>', {
                        type: 'hidden',
                        name: 'it_id[]', 
                        value: val.it_id
                    })
                );
                $form.append(
                    $('<input>', {
                        type: 'hidden',
                        name: 'it_name[]', 
                        value: val.it_name
                    })
                );
                $form.append(
                    $('<input>', {
                        type: 'hidden',
                        name: 'common_ct_chk[]', 
                        value: 1
                    })
                );

                if (e == '1') {
                    var add_field_1 = '';
                    if (val.it_soldout == '1' || val.it_force_soldout == '10') {
                        add_field_1 += `
                            <span class="txt-red">품절</span>                            
                        `;
                    } else {
                        add_field_1 += `
                            <button type="button" class="sit_cart_qty_minus"><img src="	/images/icon/minus.svg"></button>
                            <input type="text" name="ct_qty" class="m-0 ct_qty" value="${val.ct_qty}" readonly>
                            <button type="button" class="sit_cart_qty_plus"><img src="/images/icon/plus.svg"></button>
                        `;

                        dep_1_amt += parseInt(val.pt_sales);
                    }

                    dep_1 += `
                    <ul class="item-div cart_item" data-it_id="${val.it_id}">
                        <li class="tit_div">
                            <div class="readmore1" title="${val.it_name}">
                                ${val.it_name}
                            </div>
                        </li>`;
                    
                        if (val.it_img1) {
                            dep_1 += `<li class="item-div-img"><img src="${img_url}${val.it_img1}"></li>`;
                        }

                    dep_1 += `
                        <li class="plate">
                            ${add_field_1}
                        </li>
                        <li class="del">
                            <a href="javascript:;" class="delete_btn"><img src="/images/icon/delete.svg"></a>
                        </li>
                    </ul>
                    `;
                }

                if (e == '2') {
                    var add_field_1 = '';
                    if (val.it_soldout == '1' || val.it_force_soldout == '10') {
                        add_field_1 += `
                            <span class="txt-red">품절</span>   
                        `;
                    } else {
                        add_field_1 += `
                            <button type="button" class="sit_cart_qty_minus"><img src="	/images/icon/minus.svg"></button>
                            <input type="text" name="ct_qty" class="m-0 ct_qty"  value="${val.ct_qty}" readonly>
                            <button type="button" class="sit_cart_qty_plus"><img src="/images/icon/plus.svg"></button>
                        `;

                        dep_2_amt += parseInt(val.pt_sales);
                    }

                    dep_2 += `
                    <ul class="item-div cart_item" data-it_id="${val.it_id}">
                        <li class="tit_div">
                            <div class="readmore1" title="${val.it_name}">
                                ${val.it_name}
                            </div>
                        </li>`; 
                        
                        if (val.it_img1) {
                            dep_2 += `<li class="item-div-img"><img src="${img_url}${val.it_img1}"></li>`;
                        }

                    dep_2 +=  `
                        <li class="plate">
                            ${add_field_1}
                        </li>
                        <li class="del">
                            <a href="javascript:;" class="delete_btn"><img src="https://xn--hz2bqq88l.com/images/icon/delete.svg"></a>
                        </li>
                    </ul>
                    `;
                }
            });
        });

        

        $('.ac-list1').html(dep_1);  // 상온제품
        $('.ac-list2').html(dep_2);  // 저온제품

        if (dep_1_amt == 0) {
            $('.empty_items_dp1').remove();
            $('.ac-list1').after(
                `
                <p class="empty_items_dp1">상품이 없습니다.</p>
                `
            );
        } else {
            $('.empty_items_dp1').remove();
        }

        if (dep_2_amt == 0) {
            $('.empty_items_dp2').remove();
            $('.ac-list2').after(
                `
                <p class="empty_items_dp2">상품이 없습니다.</p>
                `
            );
        } else {
            $('.empty_items_dp2').remove();
        }
        
        // 상품수량
        $('#foot_cart_count').val(cart_count);
        
        $('.delivery_cost').text(parseInt(deilivery_cost).toLocaleString());
        $('.dep_1_amt').text(parseInt(dep_1_amt).toLocaleString());
        $('.dep_2_amt').text(parseInt(dep_2_amt).toLocaleString());
        $('.total_amount, .reload_total_cart_price').text( (parseInt(deilivery_cost) + parseInt(dep_1_amt) + parseInt(dep_2_amt)).toLocaleString());

        var path = window.location.pathname;

        // 장바구니 페이지
        if (path == '/mypage/cart') {
            $('.reload_total_send_cost').html(parseInt(deilivery_cost).toLocaleString());
            $('.reload_cart_price_1').html(parseInt(dep_1_amt).toLocaleString());
            $('.reload_cart_price_2').html(parseInt(dep_2_amt).toLocaleString());
            $('.reload_total_cart_price').text( (parseInt(deilivery_cost) + parseInt(dep_1_amt) + parseInt(dep_2_amt)).toLocaleString());
        }
        
        basket_count();

    } else {
        Swal.fire({
            // toast : true,
            icon : 'info',
            html: data.message,
            confirmButtonText: '확인'
        });
    }
    if (window.innerWidth > 820) {
        const collapsed = window.innerWidth <= 1500 ? 13 : 15;
        $('.readmore1').readmore({
            collapsedHeight: collapsed,
            moreLink: '<a href="#" class="readmore-btn1"><img src="/images/icon/square-down.svg" alt="더보기"></a>',
            lessLink: '<a href="#" class="readmore-btn1"><img src="/images/icon/square-up.svg" alt="접기"></a>'
        });
    }
}


function info_data_set(res) {
    // 헤더
    if (res.header_into.mb_virtual_account !== '') {
        $('#mb_virtual_account').text(res.header_into.mb_virtual_account);
        $('#mb_virtual_account2').text(res.header_into.mb_virtual_account);
    } else {
        $('.qp2').hide();
    }
    
    $('#delivery_day').text(res.header_into.delivery_day);

    $('#d_order_day').val(res.header_into.ship_date);
    $('#d_order_date').val(res.header_into.d_od_delivery_date);
    $('#virtual_account').val(res.header_into.mb_virtual_account);
    $('#min_order_amount').val(res.min_order_amount);
    $('#item_sold_out').val(res.sold_out);
    $('#total_amount').val(res.total_amount);
    $('#wait_it_name').val(res.wait_it_name);
    $('#wait_order_amount').val(res.wait_order_amount);
    $('#wait_order_cnt').val(res.wait_order_cnt);
    $('#wait_order_date').val(res.wait_order_date);
    $('#wait_order_yoil').val(res.wait_order_yoil);

    $('#diff_amount').val(calcDifference(res.total_amount, $('#mb_credit_amount').val()));
}


function calcDifference(total, minimum) {
    total   = Number(total);     // 문자열 가능성 대비 숫자로 변환
    minimum = Number(minimum);

    let difference = minimum - total;

    if(difference <= 0){
        return 0; // 이미 최소 금액 이상 구매함
    } else {
        return difference; // 추가 결제 필요 금액
    }
}
