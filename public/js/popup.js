$(document).ready(function() {
    var hash = $('#generate_hash').val();

    $(document).on('click', '.main_popup', function() {
        if ($('#main_popup').is(':checked')) {
            var key = hash + ":" +'main_popup';

            if (!hasBannerCookie('main_popup')) {
                setCookie(key, 'Y');
            }
        }
    });


    // 메인팝업 하루 비노출 여부
    if (getCookie(hash+':main_popup') !== 'Y' && !hasBannerCookie('main_popup')) {
        $('.main-popup').show();
    } else {
        $('.main-popup').hide();
    }


    $(document).on('click', '#close', function() {
        var checked = $(this).is(':checked');

        if (checked) {
            $('.lf-banner').hide();

            var key = hash + ":" +'left_banner';

            if (!hasBannerCookie('left_banner')) {
                setCookie(key, 'Y');
            }
        }
    });


    if (getCookie(hash+':left_banner') !== 'Y' && !hasBannerCookie('left_banner')) {
        $('.lf-banner').show();
    } else {
        $('.lf-banner').hide();
    }
});


function hasBannerCookie(bannerType) {
    if (!bannerType) return false;

    var cookies = document.cookie.split(';');
    var suffix = ':' + bannerType;

    for (var i = 0; i < cookies.length; i++) {
        var cookie = cookies[i].trim();
        var key = cookie.split('=')[0];

        if (key.endsWith(suffix)) {
            return true;
        }
    }
    return false;
}


