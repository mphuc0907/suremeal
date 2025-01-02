jQuery(function($) {
    function resize_img(freme, int) {
        var width_iframe = $(freme).width();
        $(window).resize(function(){
            var height_iframe = width_iframe/int;
            $(freme).css({
                'height': height_iframe,
            });
        }).resize();
    }
    resize_img('.img-post-hot', 0.94);

    // End load qty
    loadQtyCart();
    function loadQtyCart() {
        var imgnotcart = $(".carticon").attr("data-cart");
        var imgcart = $(".carticon").attr("data-cart2");
        if (localStorage.getItem('cart')) { // Lấy thông tin giỏ hàng từ loacl
            let cartLocal = localStorage.getItem('cart') ? localStorage.getItem('cart') : "";
            let dataSave = cartLocal ? JSON.parse(cartLocal) : [];
            $(".qty__cart").text(dataSave.length);
            $(".carticon").attr("src",imgcart);
            $(".qty__cart").removeClass("d-none");
        }else{
            $(".carticon").attr("src",imgnotcart);
            $(".qty__cart").addClass("d-none");
        }
        var cookielogin = getCookie();
        if(cookielogin){
            let urlCart = $('#urlCart').val();
            $(".carturlcheck").attr("href",urlCart);
        }
    }
});

// Lấy cookie đăng nhập
function getCookie() {
    // Lấy tất cả các cookie được lưu trữ trong trang web
    var allCookies = document.cookie;

    // Tách các cookie thành từng cặp tên và giá trị
    var cookieArray = allCookies.split(";");

    // Lặp qua từng cookie để tìm cookie có tên là "myCookie"
    for (var i = 0; i < cookieArray.length; i++) {
        var cookiePair = cookieArray[i].split("="); // Tách cặp tên và giá trị
        var cookieName = cookiePair[0].trim(); // Lấy tên cookie
        var cookieValue = cookiePair[1]; // Lấy giá trị cookie
        if (cookieName === "ssidd") {
            // Đã tìm thấy cookie có tên là "myCookie"
            return cookieValue;
            // break; // Thoát khỏi vòng lặp
        }
    }
}

function formatNumber(price) {
    return price.toLocaleString("vi").replace(",",".")+" đ";
}
function formatNumberNotText(price) {
    return price.toLocaleString("vi").replace(",",".");
}

// Đăng xuất
function logout() {
    let urlLogin = $('#urlLogin').val();
    document.cookie = 'ssidd=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;';
    window.location.href = urlLogin;
}


$(document).ready(function () {
    // load quantity cart
    function loadQtyCart() {
        var imgnotcart = $(".carticon").attr("data-cart");
        var imgcart = $(".carticon").attr("data-cart2");
        if (localStorage.getItem('cart')) { // Lấy thông tin giỏ hàng từ loacl
            let cartLocal = localStorage.getItem('cart') ? localStorage.getItem('cart') : "";
            let dataSave = cartLocal ? JSON.parse(cartLocal) : [];
            $(".qty__cart").text(dataSave.length);
            $(".carticon").attr("src",imgcart);
            $(".qty__cart").removeClass("d-none");
        }else{
            $(".carticon").attr("src",imgnotcart);
            $(".qty__cart").addClass("d-none");
        }
        var cookielogin = getCookie();
        if(cookielogin){
            let urlCart = $('#urlCart').val();
            $(".carturlcheck").attr("href",urlCart);
        }
    }
    // Load cart
    let templateUrl = $('#templateUrl').val();
    showCart();
    function showCart() {
        let dataSave = [];
        if (localStorage.getItem('cart')) { // Lấy thông tin giỏ hàng từ loacl
            let cartLocal = localStorage.getItem('cart') ? localStorage.getItem('cart') : "";
            dataSave = cartLocal ? JSON.parse(cartLocal) : [];
            // dataSave = $.grep(dataSave, function (e) {
            //     return e.id != dataID;
            // });

            $(".cart-notfound").addClass("d-none");
            dataSave.sort(function (a, b) { // Gom nhóm id pro theo thứ tự tăng dần
                return parseInt(a["id"]) - parseInt(b["id"]);
            });
            var html = "";
            dataSave.map(function (value, index) {
                let link = value["link"];
                let link_img = value["link_img"];
                let price = parseFloat(value["price"]);
                let orginal_price = parseFloat(value["orginal_price"]);
                let qty = value["qty"];
                let title = value["title"];
                let keyidpl = value["keyidpl"];

                html = html + '<li class="list-item-'+ keyidpl +'">\n' +
                    '             <a href="'+ link +'" class="item">\n' +
                    '                 <div class="img">\n' +
                    '                     <figure><img src="'+ link_img +'" alt="img"></figure>\n' +
                    '                 </div>\n' +
                    '                 <div class="text">\n' +
                    '                     <div class="left">\n' +
                    '                         <h3>'+ title +'</h3>\n' +
                    '                         <div class="info">\n' +
                    '                             <div class="price">\n' +
                    '                                 <span>'+ price.toLocaleString() +' đ</span>\n' +
                    '                                 <span>'+ orginal_price.toLocaleString() +' đ</span>\n' +
                    '                             </div>\n' +
                    '                             <div class="sl"><span>Số lượng:</span><strong>'+ qty +'</strong></div>\n' +
                    '                         </div>\n' +
                    '                     </div>\n' +
                    '                     <div class="right"><button class="popup-delete-item" data-id="'+ keyidpl +'"><img src="'+ templateUrl +'/dist/images/bin.svg" alt="bin"></button></div>\n' +
                    '                 </div>\n' +
                    '             </a>\n' +
                    '         </li>';
            });
            $(".popup-cart").html(html);
            // countTotal()
        } else {
            $(".popup-cart").html('<h6>Bạn chưa có sản phẩm nào trong giỏ hàng!</h6>');
        }
    }

    // Xóa sản phẩm trong giỏ hàng
    $("body").on("click", ".popup-delete-item", function (e) {
        e.preventDefault();
        var keyidpl = $(this).attr("data-id");
        $(".list-item-" + keyidpl).remove();
        if (localStorage.getItem('cart')) { // Lấy thông tin giỏ hàng từ loacl
            let cartLocal = localStorage.getItem('cart') ? localStorage.getItem('cart') : "";
            let dataSave = cartLocal ? JSON.parse(cartLocal) : [];
            if (dataSave.length == 1) {
                localStorage.removeItem("cart");
                $(".popup-cart").html('<h6>Bạn chưa có sản phẩm nào trong giỏ hàng!</h6>');
                loadQtyCart();

            } else {
                for (var i = 0; i < dataSave.length; i++) {
                    if (dataSave[i].keyidpl == keyidpl) {
                        dataSave.splice(i, 1);
                        break;
                    }
                }
                localStorage.setItem('cart', JSON.stringify(dataSave));
                loadQtyCart();
            }
        }
    });
    $('body').on('click', '.btn-notification', function () {
        var cookielogin = getCookie();
        var notifiCount = $('.notifi-count').text();
        if(cookielogin && notifiCount) {
            let urlAjax = $("#urlAjax").val();
            let site_key = $("#site_key").val();

            grecaptcha.ready(function() {
                grecaptcha.execute(site_key, {action: 'read_notification'}).then(function(token) {
                    $.ajax({
                        url: urlAjax,
                        type: 'POST',
                        cache: false,
                        dataType: "text",
                        data: {
                            action: 'readNotification',
                            action1: "read_notification",
                            token1: token
                        },
                        beforeSend: function() {
                        },
                        success: function(rs) {
                            $('.notifi-count').text('');
                            $('.notifi-count').hide();
                        }
                    });
                    return false;
                });
            });
        }
    });
})
