function addToCart(thiss) {
    // Lấy thông tin sản phẩm từ các thuộc tính
    var id = thiss.attr("data-id"); // ID sản phẩm
    var img = thiss.attr("data-img"); // Hình ảnh sản phẩm
    var price = parseInt(thiss.attr("data-price")); // Giá sản phẩm
    var link = thiss.attr("data-link"); // Đường dẫn sản phẩm
    var title = thiss.attr("data-title"); // Tên sản phẩm
    var qty = 1; // Số lượng mặc định khi thêm mới
    var weight = thiss.attr("data-weight"); // Trọng lượng sản phẩm
    var instock = parseInt(thiss.attr("data-instock")); // Số lượng tồn kho
    var pack = parseInt(thiss.attr("data-pack")); // Gói sản phẩm

    // Định nghĩa sản phẩm mới
    var product = {
        id,
        title,
        instock,
        img,
        weight,
        price,
        link,
        pack,
        qty
    };

    // Kiểm tra tồn kho
    if (instock <= 0) {
        alert('Product is out of stock');
        return;
    }

    // Lấy giỏ hàng từ localStorage, nếu chưa có thì khởi tạo mảng rỗng
    var cart = JSON.parse(localStorage.getItem("cart")) || [];

    // Kiểm tra xem sản phẩm đã có trong giỏ hàng chưa
    var found = false; // Biến đánh dấu sản phẩm đã tồn tại

    for (var i = 0; i < cart.length; i++) {
        if (cart[i].id === product.id) {
            // Kiểm tra nếu số lượng trong giỏ hàng cộng thêm vượt quá tồn kho
            if (cart[i].qty + product.qty > instock) {
                alert('Quantity exceeds available stock');
                return;
            }

            // Nếu sản phẩm đã tồn tại, tăng số lượng
            found = true;
            cart[i].weight = product.weight;
            cart[i].pack = product.pack;
            cart[i].instock = product.instock;
            cart[i].qty += product.qty;
            cart[i].select = false;
            console.log("Đã tăng số lượng sản phẩm:", cart[i]);
            break; // Thoát khỏi vòng lặp
        }
    }

    if (!found) {
        // Nếu sản phẩm chưa tồn tại, kiểm tra tồn kho trước khi thêm
        if (product.qty > instock) {
            alert('Quantity exceeds available stock');
            return;
        }
        cart.push(product);
        console.log("Đã thêm sản phẩm mới vào giỏ hàng:", product);
    }

    // Lưu giỏ hàng đã cập nhật vào localStorage
    localStorage.setItem("cart", JSON.stringify(cart));

    // Đếm số sản phẩm khác nhau trong giỏ hàng (không tính `qty`)
    var totalProducts = cart.length;

    // Tính tổng số tiền
    var totalPrice = 0;
    for (var i = 0; i < cart.length; i++) {
        totalPrice += cart[i].price * cart[i].qty;
    }

    // Hiển thị thông tin trên giao diện
    console.log("Số sản phẩm khác nhau trong giỏ hàng:", totalProducts);
    console.log("Tổng số tiền:", totalPrice);

    // Cập nhật số sản phẩm và tổng tiền trên giao diện
    $(".cart-count").text(totalProducts);
    $(".cart-total-price").text("$ " + totalPrice.toLocaleString());

    console.log("Giỏ hàng hiện tại:", cart);
}




function calculateCartInfo() {
    // Lấy giỏ hàng từ localStorage
    var cart = JSON.parse(localStorage.getItem("cart")) || [];

    // Khởi tạo các biến tổng
    var totalItems = cart.length; // Số sản phẩm khác nhau
    var totalQuantity = 0; // Tổng số lượng sản phẩm
    var totalPrice = 0; // Tổng tiền

    // Lặp qua từng sản phẩm trong giỏ hàng
    for (var i = 0; i < cart.length; i++) {
        totalQuantity += cart[i].qty; // Cộng số lượng sản phẩm
        totalPrice += cart[i].price * cart[i].qty; // Cộng tổng tiền
    }

    // Hiển thị thông tin
    // console.log("Số sản phẩm khác nhau:", totalItems);
    // console.log("Tổng số lượng sản phẩm:", totalQuantity);
    // console.log("Tổng tiền:", totalPrice);

    // Cập nhật giao diện (nếu cần)
    $(".cart-count").text(totalItems); // Số lượng sản phẩm
    $(".cart-total-price").text("$" + totalPrice.toLocaleString()); // Tổng tiền (định dạng số)
}

// Gọi hàm để tính toán và hiển thị
calculateCartInfo();

//Get info product

function showCart() {
    let dataSave = [];

    if (localStorage.getItem('cart')) { // Lấy thông tin giỏ hàng từ loacl
        let cartLocal = localStorage.getItem('cart') ? localStorage.getItem('cart') : "";
        var cart = JSON.parse(localStorage.getItem("cart")) || [];

        // Khởi tạo các biến tổng
        var totalItems = cart.length; // Số sản phẩm khác nhau
        dataSave = cartLocal ? JSON.parse(cartLocal) : [];
        // dataSave = $.grep(dataSave, function (e) {
        //     return e.id != dataID;
        // });
        $(".cart-notfound").addClass("d-none");
        dataSave.sort(function (a, b) { // Gom nhóm id pro theo thứ tự tăng dần
            return parseInt(a["id"]) - parseInt(b["id"]);
        });
        var html = "";
        var htmltatol = "";
        var htmldetal = "<div class=\"w-full flex gap-6 items-center justify-between px-6 py-4 rounded-t-xl bg-white\">\n" +
            "                            <label class=\"custom-checkbox\">\n" +
            "                                <div class=\"checkbox-container\">\n" +
            "                                    <input name=\"product_chose\" name=\"product_chose\" class='check-all' type=\"checkbox\">\n" +
            "                                    <span class=\"checkmark\"></span>\n" +
            "                                </div>\n" +
            "                                <p class=\"text-body-md-medium text-gray-9 cart-count-in\">Select all (" + totalItems + ")</p>\n" +
            "                            </label>\n" +
            "\n" +
            "                            <button class=\"button bg-trans bg-trans-all\">\n" +
            "                                <figure class=\"w-6 h-6\"><img src=\"https://suremeal.qixtech.com/wp-content/themes/abcd/assets/image/icon/trash.svg\" alt=\"icon\">\n" +
            "                                </figure>\n" +
            "                            </button>\n" +
            "                        </div>";
        let money = 0;
        let qtytotal = 0;
        var indent = 1;
        dataSave.map(function (value, index) {
            let link = value["link"];
            let link_img = value["img"];
            let pri = value["price"];
            let qty = value["qty"];
            let title = value["title"];
            qtytotal += qty;
            let id = value["id"];
            let select = value["select"];
            let pack = value["pack"];
            let moneyqly = pri * qty;
            money += pri * qty;
            let price = parseInt(pri);
            let format = moneyqly.toLocaleString('en-US');
            let maxqty = value["instock"];



            // Phần còn lại của logic tạo HTML
            htmldetal +=
                ' <div\n' +
                '     class="w-full item-cart flex flex-wrap lg:flex-nowrap gap-4 lg:gap-6 items-center p-6 rounded-xl bg-white">\n' +
                '     <label class="custom-checkbox">\n' +
                '         <div class="checkbox-container">';
            if (select == false || select == undefined) {
                htmldetal +=
                    '             <input type="checkbox" name="product_chose" class="cart-checkbox form-check-input" data-product-id="' + id + '" value="' + id + '">';
            } else {
                htmldetal +=
                    '             <input type="checkbox" name="product_chose" class="cart-checkbox form-check-input" checked data-product-id="' + id + '" value="' + id + '">';
            }

            htmldetal +=
                '             <span class="checkmark"></span>\n' +
                '         </div>\n' +
                '     </label>\n' +
                '     <div class="flex md:w-2/3 max-w-[454px] items-center gap-5">\n' +
                '         <figure\n' +
                '             class="w-[60px] h-[60px] md:w-[100px] md:h-[100px] overflow-hidden rounded-xl border border-solid border-neutral-200">\n' +
                '             <a href="' + link + '">\n' +
                '                 <img src="' + link_img + '" alt="item">\n' +
                '             </a>\n' +
                '         </figure>\n' +
                '         <div class="flex-1 flex flex-col gap-2">\n' +
                '             <h2 class="text-body-md-medium text-gray-8 truncate-2row"><a href="' + link + '">' + title + '\n' +
                '             </a></h2>\n' +
                '             <select disabled class="input-neutral-200 text-body-sm-regular text-gray-7 select-pack" data-id="' + id + '">\n' +
                '                 <option value="">Type: ' + pack + ' Pack</option>\n' +
                '             </select>\n' +
                '         </div>\n' +
                '     </div>\n' +
                '     <div class="md:min-w-[143px] flex flex-col items-end justify-end gap-1">\n' +
                '         <p class="text-body-md-medium text-primary">$' + pri + ' </p>\n' +
                '     </div>\n' +
                '     <div\n' +
                '         class="counter flex items-center justify-center w-[110px] h-10 border border-solid border-[#eee] rounded-lg text-body-sm-medium text-[#121138]">\n' +
                '         <button class="decrement px-3 py-1 btn-minus" data-id="' + id + '" data-instock="'+ maxqty +'" data-value="' + qty + '" data-indent="' + indent + '">−</button>\n' +
                '         <input type="text" data-id="' + id + '"  name="quant[' + indent + ']" value="' + qty + '" readonly\n' +
                '             class="quantity w-8 text-center focus:outline-none" />\n' +
                '         <button class="increment px-3 py-1 btn-plus" data-instock="'+ maxqty +'" data-id="' + id + '" data-value="' + qty + '" data-indent="' + indent + '">+</button>\n' +
                '     </div>\n' +
                '     <button data-id="' + id + '" class="button bg-trans btn-remove-cart">\n' +
                '         <figure class="w-6 h-6"><img src="https://suremeal.qixtech.com/wp-content/themes/abcd/assets/image/icon/trash.svg" alt="icon">\n' +
                '         </figure>\n' +
                '     </button>\n' +
                ' </div>';
            // Lấy dữ liệu từ PHP qua AJAX
            // $.ajax({
            //     url: "https://suremeal.qixtech.com/wp-admin/admin-ajax.php",
            //     type: 'POST',
            //     data: {
            //         action: "QueryPack",
            //         id: id
            //     },
            //     dataType: 'json',
            //     success: function (response) {
            //         if (response.success) {
            //             // Gắn dữ liệu trả về vào biến htmlpack
            //             var htmlpack = response.data;
            //
            //             // Thay thế <select> với class 'select-pack' tương ứng
            //             $(".select-pack[data-id='" + id + "']").html(htmlpack);
            //         } else {
            //             console.error("Failed to fetch pack options for product ID: " + id);
            //         }
            //     },
            //
            // });
        });


        $(".menu__cart .number").html(qtytotal);

        $(".show-cart").html(html);
        $(".cart-detail").html(htmldetal);
        $(".list-total").html(htmltatol);
        $(".user-info").removeClass("d-none");
        $(".table-label").removeClass("d-none");
        // countTotal()
    } else {
        $(".cart-notfound").removeClass("d-none");
        $(".user-info").addClass("d-none");
        $(".table-label").addClass("d-none");
        $(".cart-right").addClass("d-none");
    }
}

function showCartSelect() {
    let dataSave = [];

    if (localStorage.getItem('cart')) { // Lấy thông tin giỏ hàng từ localStorage
        let cartLocal = localStorage.getItem('cart') ? localStorage.getItem('cart') : "";
        var cart = JSON.parse(localStorage.getItem("cart")) || [];

        // Lọc chỉ các sản phẩm có select là true
        dataSave = cart.filter(function (item) {
            return item.select === true; // Chỉ lấy sản phẩm có select: true
        });

        var totalItems = dataSave.length; // Số sản phẩm có chọn
        $(".cart-notfound").addClass("d-none");

        // Gom nhóm id pro theo thứ tự tăng dần
        dataSave.sort(function (a, b) {
            return parseInt(a["id"]) - parseInt(b["id"]);
        });

        var html = "";
        var htmltatol = "";
        var htmldetal = "<h2 class=\"text-body-xl-medium text-gray-9\">Product list (" + totalItems + ")</h2>";

        let money = 0;
        let qtytotal = 0;
        var indent = 1;

        // Duyệt qua từng sản phẩm đã được chọn
        dataSave.map(function (value, index) {
            let link = value["link"];
            let link_img = value["img"];
            let pri = value["price"];
            let qty = value["qty"];
            let title = value["title"];
            let pack = value["pack"];
            qtytotal += qty;
            let id = value["id"];
            let select = value["select"];
            let moneyqly = pri * qty;
            money += pri * qty;
            let price = parseInt(pri);
            let format = moneyqly.toLocaleString('en-US');
            let maxqty = value["maxqty"];

            // Xây dựng HTML cho giỏ hàng
            htmldetal = htmldetal + '<div class="flex flex-col gap-4 lg:gap-6 p-6 rounded-xl bg-white">\n' +
                '                            <div class="w-full flex flex-wrap gap-4 lg:gap-6 lg:flex-nowrap justify-between items-center">\n' +
                '                                <div class="flex md:w-2/3 max-w-[454px] items-center gap-5">\n' +
                '                                    <figure\n' +
                '                                        class="w-[60px] h-[60px] md:w-[100px] md:h-[100px] overflow-hidden rounded-xl border border-solid border-neutral-200">\n' +
                '                                        <img src="' + link_img + '" alt="item">\n' +
                '                                    </figure>\n' +
                '                                    <div class="flex-1 flex flex-col gap-2">\n' +
                '                                        <h2 class="text-body-md-medium text-gray-8 truncate-2row">' + title + '\n' +
                '                                          </h2>\n' +
                '                                        <div class="neutral-200 text-body-sm-regular text-gray-7">\n' +
                '                                            Type: ' + pack + ' Pack\n' +
                '                                        </div>\n' +
                '                                    </div>\n' +
                '                                </div>\n' +
                '                                <div\n' +
                '                                    class="counter flex items-center justify-center w-[110px] h-10 text-body-sm-medium text-[#121138]">\n' +
                '                                <p class="text-body-md-medium text-gray-7">Quantity : ' + qty + '</p>\n' +
                '                                </div>\n' +
                '                                <p class="text-body-md-medium text-primary">$' + price + '</p>\n' +
                '                            </div>\n' +
                '                        </div>';
        });

        // Cập nhật số lượng sản phẩm và giỏ hàng
        $(".menu__cart .number").html(qtytotal);
        $(".show-cart").html(html);
        $(".card-select").html(htmldetal);
        $(".list-total").html(htmltatol);
        $(".user-info").removeClass("d-none");
        $(".table-label").removeClass("d-none");
    } else {
        $(".cart-notfound").removeClass("d-none");
        $(".user-info").addClass("d-none");
        $(".table-label").addClass("d-none");
        $(".cart-right").addClass("d-none");
    }
}

function calculateTotal() {
    let total = 0;
    const cart = JSON.parse(localStorage.getItem('cart')) || [];

    $('.cart-checkbox:checked').each(function () {
        const productId = $(this).data('product-id').toString();
        const cartItem = cart.find(item => item.id === productId);

        if (cartItem) {
            total += cartItem.price * cartItem.qty;
        }
    });

    $('.total-number').text('$' + total.toFixed(2));
    $('.total-number').attr("data-total", total);
}

function calculateTotalTrue() {
    let total = 0;
    const cart = JSON.parse(localStorage.getItem('cart')) || [];

    // Duyệt qua toàn bộ giỏ hàng và tính tổng cho các sản phẩm có `selected` là true
    cart.forEach(cartItem => {
        if (cartItem.select == true) {
            total += cartItem.price * cartItem.qty;
        }
    });

    $('.total-true').text('$' + total.toFixed(2));
    $('.total-sub').text('$' + total.toFixed(2));
    $('.total-sub').attr("data-true", total);
    $('.total-true').attr("data-total", total);
}

calculateTotalTrue();
$(document).ready(function () {
    // Lấy giỏ hàng từ localStorage hoặc khởi tạo giỏ hàng rỗng
    function getCart() {
        return JSON.parse(localStorage.getItem('cart')) || [];
    }

    // Cập nhật giỏ hàng vào localStorage
    function setCart(cart) {
        localStorage.setItem('cart', JSON.stringify(cart));
    }

    // Cập nhật số lượng trong localStorage
    function updateQuantityInCart(productId, newQuantity) {
        const cart = JSON.parse(localStorage.getItem('cart')) || [];
        const itemIndex = cart.findIndex(item => item.id === productId);
        if (itemIndex !== -1) {
            cart[itemIndex].qty = newQuantity;
            setCart(cart);
        }
    }

    // Xử lý khi nhấn nút tăng số lượng
    $('.btn-plus').on('click', function () {
        const productId = $(this).data('id').toString();
        const quantityInput = $(`input[data-id="${productId}"]`);
        let currentQuantity = parseInt(quantityInput.val());
        const newQuantity = currentQuantity + 1;

        // Lấy thông tin sản phẩm từ localStorage
        const cart = JSON.parse(localStorage.getItem("cart")) || [];
        const product = cart.find(item => item.id === productId);

        // Kiểm tra tồn kho
        if (product && newQuantity > product.instock) {
            alert('Quantity exceeds available stock');
            return;
        }

        // Cập nhật số lượng hiển thị
        quantityInput.val(newQuantity);

        // Cập nhật số lượng trong localStorage
        updateQuantityInCart(productId, newQuantity);

        // Tính lại tổng giá trị
        calculateTotal();
    });

    // Xử lý khi nhấn nút giảm số lượng
    $('.btn-minus').on('click', function () {
        const productId = $(this).data('id').toString();
        const quantityInput = $(`input[data-id="${productId}"]`);
        let currentQuantity = parseInt(quantityInput.val());

        // Không giảm xuống dưới 1
        if (currentQuantity > 1) {
            const newQuantity = currentQuantity - 1;

            // Cập nhật số lượng hiển thị
            quantityInput.val(newQuantity);

            // Cập nhật số lượng trong localStorage
            updateQuantityInCart(productId, newQuantity);

            // Tính lại tổng giá trị
            calculateTotal();
        }
    });


    // Cập nhật tổng giá trị khi tải trang
    calculateTotal();
});
$(document).ready(function () {
    // Sự kiện thay đổi trạng thái của checkbox "Select All"
    $('input[name="select_all"]').on('change', function () {
        const isChecked = $(this).is(':checked');
        // Đặt trạng thái của tất cả các checkbox khác giống với "Select All"
        $('input[name="product_chose"]').prop('checked', isChecked);
        calculateTotal(); // Tính lại tổng giá trị
    });

    // Sự kiện thay đổi trạng thái của từng checkbox sản phẩm
    $('input[name="product_chose"]').on('change', function () {
        // Nếu tất cả checkbox sản phẩm được chọn thì "Select All" cũng được chọn
        const allChecked = $('input[name="product_chose"]').length === $('input[name="product_chose"]:checked').length;
        $('input[name="select_all"]').prop('checked', allChecked);
        calculateTotal(); // Tính lại tổng giá trị
    });

    calculateTotal();
});

$(document).ready(function () {
    // Khi checkbox con được chọn
    $('.cart-detail .form-check-input').not('.check-all').on('change', function () {
        const $parentDiv = $(this).closest('.cart-detail'); // Tìm container cha

        // Nếu có checkbox con không được chọn, bỏ chọn "All"
        const allChecked = $parentDiv.find('input[name="product_chose"]').not('.check-all').length ===
            $parentDiv.find('input[name="product_chose"]:checked').not('.check-all').length;

        $parentDiv.find('.check-all').prop('checked', allChecked);

        // Lấy ID sản phẩm và trạng thái checkbox
        const productId = $(this).data('product-id');
        const isChecked = $(this).is(':checked');

        // Cập nhật trạng thái trong localStorage
        updateCartSelection(isChecked, productId);
    });

    // Hàm cập nhật trạng thái `select` trong localStorage
    function updateCartSelection(isChecked, productId) {
        // Lấy dữ liệu từ localStorage
        const cart = JSON.parse(localStorage.getItem('cart')) || [];

        if (productId === 'all') {
            // Nếu là "All", cập nhật trạng thái tất cả item
            const updatedCart = cart.map(item => {
                item.select = isChecked;
                return item;
            });
            localStorage.setItem('cart', JSON.stringify(updatedCart));
        } else {
            // Nếu là checkbox riêng lẻ, cập nhật trạng thái của sản phẩm tương ứng
            const updatedCart = cart.map(item => {
                if (item.id === productId) {
                    item.select = isChecked; // Cập nhật trạng thái checkbox
                }
                return item;
            });
            localStorage.setItem('cart', JSON.stringify(updatedCart));
        }

        // Debug dữ liệu
        // console.log('Updated cart:', JSON.parse(localStorage.getItem('cart')));
    }

    // Khi checkbox "All" được chọn
    $('.cart-detail .check-all').on('change', function () {
        const $parentDiv = $(this).closest('.cart-detail');
        const isChecked = $(this).is(':checked');

        // Chọn hoặc bỏ chọn tất cả checkbox con
        $parentDiv.find('input[name="product_chose"]').not('.check-all').prop('checked', isChecked);

        // Cập nhật trạng thái trong localStorage
        updateCartSelection(isChecked, 'all');
        calculateTotal(); // Gọi hàm khi checkbox bị bỏ chọn
    });
});

// let dollar = parseFloat($('#vnd').val());

// Purchase



// Remover cart
$(document).on('click', '.btn-remove-cart', function () {
    // Lấy ID của sản phẩm từ thuộc tính data-id
    const productId = $(this).data('id').toString();

    // Lấy giỏ hàng từ localStorage
    let cart = JSON.parse(localStorage.getItem('cart')) || [];

    // Lọc giỏ hàng, loại bỏ sản phẩm có ID khớp với productId
    cart = cart.filter(item => item.id !== productId);

    // Lưu lại giỏ hàng sau khi xóa
    localStorage.setItem('cart', JSON.stringify(cart));

    // Xóa đoạn HTML chứa sản phẩm khỏi giao diện
    $(this).closest('.item-cart').remove();

    // Cập nhật tổng giá trị (nếu có hàm tính toán)
    calculateTotal();

    // Cập nhật số lượng item còn lại
    const itemCount = cart.length;


    // Hiển thị số lượng item còn lại lên giao diện (tùy chọn)
    $('.cart-count').text(itemCount);
    $('.cart-count-in').text(`Select all (${itemCount}) `);
});


$(document).ready(function () {
    // Xử lý khi click vào button có class app-voucher
    $('.app-voucher').on('click', function () {
        // Lấy giá trị từ data-voucher
        const voucherCode = $(this).data('voucher');

        // Gán giá trị vào input có name="voucher_code"
        $('input[name="voucher_code"]').val(voucherCode);

        // Bỏ thuộc tính disabled của button cuối cùng
        $('.button.bg-primary').prop('disabled', false);

        // Reset trạng thái icon của tất cả các items
        $('.app-voucher .icon-plus').removeClass('hidden');
        $('.app-voucher .icon-check').addClass('hidden');

        // Thay đổi trạng thái icon của item hiện tại
        $(this).find('.icon-plus').addClass('hidden');
        $(this).find('.icon-check').removeClass('hidden');
    });

    // Xử lý khi người dùng nhập thủ công vào input voucher_code
    $('input[name="voucher_code"]').on('input', function () {
        if ($(this).val().trim() !== '') {
            // Bỏ thuộc tính disabled khi có dữ liệu
            $('.button.bg-primary').prop('disabled', false);
        } else {
            // Thêm lại thuộc tính disabled khi input rỗng
            $('.button.bg-primary').prop('disabled', true);
        }
    });
});

function myFunction(id) {
    // Get the text field
    var copyText = document.getElementById(id);

    // Select the text field
    copyText.select();
    copyText.setSelectionRange(0, 99999); // For mobile devices

    // Copy the text inside the text field
    navigator.clipboard.writeText(copyText.value);

    // Alert the copied text
    alert("Copied the text: " + copyText.value);
}


function roundUp(number) {
    return Math.ceil(number);
}