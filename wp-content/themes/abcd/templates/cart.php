<?php /* Template Name: Cart */ ?>
<?php
$url =  get_template_directory_uri();
?>

<?php
get_header();
global $wpdb;
$current_time = current_time('Y-m-d H:i:s'); // Current date and time
$list_voucher = $wpdb->get_results("
    SELECT * FROM `wp_voucher` 
    WHERE `status` = 1 
    AND `end_date` > '$current_time'
");
$authenticated_dealer = validate_dealer_token();
$dealer_id = null;
if ($authenticated_dealer) {
    $dealer_id = $authenticated_dealer->ID;
}
// Function to get dealer discount for a specific product
function get_dealer_discount($dealer_id, $product_id)
{
    global $wpdb;
    $discount = $wpdb->get_row(
        $wpdb->prepare(
            "SELECT * FROM wp_discount_dealer 
            WHERE id_dealer = %d AND product = %d",
            $dealer_id,
            $product_id
        )
    );
    return $discount;
}

// Function to calculate final price with dealer discount
function calculate_dealer_price($original_price, $discount)
{
    if (!$discount) return false;

    if ($discount->discount_type == 0) {
        // Fixed amount discount
        return max(0, $original_price - $discount->discount_amount);
    } else {
        // Percentage discount
        $discount_amount = $original_price * ($discount->discount_amount / 100);
        return max(0, $original_price - $discount_amount);
    }
}
$products_same_type = [];
// Lấy tất cả products_of_the_same_type cho các sản phẩm có thể có trong cart
$all_products = get_posts(array(
    'post_type' => 'product',
    'posts_per_page' => -1,
    'fields' => 'ids'
));

foreach($all_products as $product_id) {
    $same_type = get_field('products_of_the_same_type', $product_id);
    if ($same_type) {
        // Create array to store products with additional fields
        $products_with_fields = [];
        
        foreach ($same_type as $product) {
            // Get the additional fields
            $quantity = get_field('quantity', $product->ID);
            $price = get_field('price', $product->ID);
            $sale_price = get_field('sale_price', $product->ID);
            $instock = get_field('instock', $product->ID);
            $list_promotion = get_field('list_promotion', $product->ID);
            $permalink = get_permalink($product->ID);

            // Get dealer discount if dealer is logged in
            $dealer_discount = $dealer_id ? get_dealer_discount($dealer_id, $product->ID) : null;

            // Calculate final price based on dealer discount
            $final_price = $price;
            if ($dealer_discount) {
                // Calculate dealer discount price from original price
                $dealer_price = calculate_dealer_price($price, $dealer_discount);
                // If there's a sale price, compare it with dealer price
                if ($sale_price) {
                    $final_price = min($dealer_price, $sale_price);
                } else {
                    $final_price = $dealer_price;
                }
            } else {
                // If no dealer discount, use sale price if available
                $final_price = $sale_price ? $sale_price : $price;
            }
            
            // Add the fields to the product object
            $product_data = (array) $product;  // Convert to array to add new properties
            $product_data['quantity'] = $quantity;
            $product_data['price'] = $final_price;
            $product_data['instock'] = $instock;
            $product_data['list_promotion'] = $list_promotion;
            $product_data['permalink'] = $permalink;
            
            $products_with_fields[] = $product_data;
        }
        
        $products_same_type[$product_id] = $products_with_fields;
    }
}
$products_same_type_json = json_encode($products_same_type);
// var_dump($products_same_type_json);
$lang = ICL_LANGUAGE_CODE;
?>
    <main class="bg-[#EEF0F6]">
        <section class="py-6">
            <div class="container">
                <nav class="breadcrumb text-body-md-medium text-gray-8" aria-label="breadcrumb">
                    <!-- Sử dụng schema.org để định nghĩa breadcrumb list -->
                    <ol class="flex flex-wrap gap-3 items-center" itemscope
                        itemtype="https://schema.org/BreadcrumbList">

                        <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                            <a href="<?= home_url() ?>" class="text-secondary hover:text-primary" itemprop="item">
                                <span itemprop="name"><?php pll_e('Home') ?></span>
                            </a>
                            <meta itemprop="position" content="1" />
                        </li>
                        <span>/</span>
                        <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem"
                            aria-current="page">
                            <span itemprop="name"><?php pll_e('Cart') ?></span>
                            <meta itemprop="position" content="4" />
                        </li>

                    </ol>
                </nav>
            </div>
        </section>
        <style>
            .view-offer {
                position: absolute;
                transition: all 0.5s ease-in-out;
            }

            .view-offer.active {
                position: static;

            }

            .rotate-icon {
                transition: transform 0.5s ease-in-out;
            }

            .rotate-icon.active {
                transform: rotate(180deg);
                transition: all 0.5s ease-in-out;
            }

            select option:disabled {
                color: #9CA3AF;
                background-color: #F3F4F6;
                cursor: not-allowed;
            }
        </style>
        <section class="pb-20">
            <div class="container">
                <div class="w-full flex flex-col lg:flex-row gap-6">
                    <div class="flex-1 flex flex-col gap-3 max-w-[900px] cart-detail">
                        <div class="w-full flex gap-6 items-center justify-between px-6 py-4 rounded-t-xl bg-white">
                            <label class="custom-checkbox">
                                <div class="checkbox-container">
                                    <input type="checkbox">
                                    <span class="checkmark"></span>
                                </div>
                                <p class="text-body-md-medium text-gray-9 cart-count-in"><?php pll_e('Select all') ?> (0)</p>
                            </label>

                            <button class="button bg-trans bg-trans-all">
                                <figure class="w-6 h-6"><img src="<?php echo get_template_directory_uri(); ?>/assets/image/icon/trash.svg" alt="icon">
                                </figure>
                            </button>
                        </div>
                        <!-- item select -->

                    </div>

                    <div class="flex flex-col gap-4 w-full lg:max-w-[437px]">



                        <div class="flex flex-col gap-5 px-6 py-4 rounded-xl bg-white">
                            <p><?php pll_e('Order information') ?></p>
                            <div class="flex flex-col gap-3">
                                <div class="flex items-center justify-between">
                                    <p class="text-body-sm-regular text-gray-7"><?php pll_e('Sub total') ?></p>
                                    <h2 class="text-body-md-medium text-gray-9 total-number">$0.00 </h2>
                                </div>
                                <hr class="divider">
                                <div class="flex items-center justify-between">
                                    <p class="text-body-sm-regular text-gray-7"><?php pll_e('Discount') ?></p>
                                    <h2 class="text-body-md-medium text-gray-9">$0.00</h2>
                                </div>
                                <div class="flex items-center justify-between">
                                    <p class="text-body-sm-regular text-gray-7"><?php pll_e('Shipping fee') ?></p>
                                    <h2 class="text-body-md-medium text-gray-9">--</h2>
                                </div>
                                <hr class="divider">
                                <div class="flex items-center justify-between">
                                    <p class="text-body-sm-regular text-gray-7"><?php pll_e('Total payment') ?></p>
                                    <h2 class="text-body-xl-medium text-primary total-number"> </h2>
                                </div>
                            </div>
                            <a href="javascript:;"
                                class="mt-3 w-full button bg-primary text-body-md-semibold text-white btn-cart-purchase"><?php pll_e('Check out') ?></a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <?php get_footer() ?>
    <!-- script offer js -->
    <script>
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
                // console.log(dataSave);
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
                    let promo = value["promo"];
                    let moneyqly = pri * qty;
                    money += pri * qty;
                    let price = parseInt(pri);
                    let format = moneyqly.toLocaleString('en-US');
                    let maxqty = value["instock"];

                    let productsSameType = <?php echo $products_same_type_json; ?>;

                    // Khi render sản phẩm 
                    // console.log(productsSameType[id]); 
                    // productsSameType[id].map(function (value, index) {
                    //     console.log('Product ID:', value['ID']);
                    //     console.log('Quantity:', value['quantity']);
                    //     console.log('Quantity:', value['stock']);
                    //     console.log('Promotions:', value['list_promotion'][0]['promotion']);
                    //     console.log('Product Link:', value['permalink']);
                    // })
                    let optionsHtml = '';
                    let productsSameTypeList = productsSameType[id] || [];

                    if (productsSameTypeList.length > 0) {
                        productsSameTypeList.map(function(product, index) {
                            // Check if product is already in cart
                            const isInCart = cart.some(item => item.id == product['ID']);
                            // Check if product has sufficient stock
                            const hasStock = product['instock'] !== "" && parseInt(product['instock']) >= qty;
                            
                            // Add disabled attribute and a class for styling if needed
                            const disabledAttr = (isInCart || !hasStock) ? 'disabled' : '';
                            const disabledClass = (isInCart || !hasStock) ? 'text-gray-400' : '';
                            
                            // Add a title/tooltip to show why option is disabled
                            let titleAttr = '';
                            if (isInCart) {
                                titleAttr = 'Already in cart';
                            } else if (!hasStock) {
                                titleAttr = 'Insufficient stock';
                            }
                            
                            optionsHtml += `<option 
                                value="${product['ID']}" 
                                ${disabledAttr}
                                class="${disabledClass}"
                                title="${titleAttr}"
                            >${product['list_promotion'][0]['promotion']}</option>`;
                        });
                    }

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
                        '             </a></h2>\n';
                    if(productsSameTypeList.length > 0){
                        htmldetal +=
                        '             <select class="input-neutral-200 max-w-[250px] text-body-sm-regular text-gray-7 select-pack" data-id="' + id + '">\n' +
                        '                 <option value="'+ id +'">' + promo + '</option>\n' +
                                            optionsHtml + 
                        '             </select>\n';
                        }
                    htmldetal +=
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
        // Add this after your showCart() function
        $(document).on('change', '.select-pack', function() {
            const selectedProductId = $(this).val();
            const currentProductId = $(this).data('id');
            const currentQty = parseInt($(this).closest('.item-cart').find('.quantity').val());
            
            // Get products same type data
            const productsSameType = <?php echo $products_same_type_json; ?>;
            
            let cart = JSON.parse(localStorage.getItem('cart')) || [];
            
            // Check if product already exists
            const productExistsInCart = cart.some(item => item.id == selectedProductId);
            if (productExistsInCart) {
                alert('This product is already in your cart');
                $(this).val(currentProductId);
                return;
            }
            
            // Find selected product data
            const selectedProduct = productsSameType[currentProductId].find(p => p.ID == selectedProductId);
            
            // Check stock
            if (selectedProduct.instock == "" || parseInt(selectedProduct.instock) < currentQty) {
                alert('Not enough stock available');
                $(this).val(currentProductId);
                return;
            }
            
            // Update cart item
            const cartItemIndex = cart.findIndex(item => item.id == currentProductId);
            if (cartItemIndex !== -1) {
                cart[cartItemIndex] = {
                    ...cart[cartItemIndex],
                    id: String(selectedProduct.ID),
                    title: selectedProduct.post_title,
                    price: parseInt(selectedProduct.price),
                    promo: selectedProduct.list_promotion[0].promotion,
                    link: selectedProduct.permalink,
                    instock: parseInt(selectedProduct.instock),
                    qty: cart[cartItemIndex].qty,
                    select: cart[cartItemIndex].select
                };
                
                // Update storage and refresh display
                localStorage.setItem('cart', JSON.stringify(cart));
                showCart();
                calculateCartInfo();
                calculateTotal();
            }
        });
    </script>
    <script>
        
        $('.btn-cart-purchase').click(function() {
            var orderSelectedProduct = $('.cart-checkbox:checked').map(function() {
                return $(this).attr('data-product-id');
            }).get();
            var check = false;

            $('.input-number').map(function() {
                if ($(this).val().length == 0 || $(this).val() == 0) {
                    check = true;
                }
            });

            if (orderSelectedProduct.length == 0) {
                Swal.fire({
                    icon: 'warning',
                    text: 'Please select the product you want to buy. If you want to buy all, click select all!',
                });
            } else if (check) {
                Swal.fire({
                    icon: 'warning',
                    text: 'Product quantity cannot be empty',
                });
            } else {
                // Cập nhật trạng thái "select" vào localStorage
                var cart = JSON.parse(localStorage.getItem('cart')) || [];

                // Lặp qua từng sản phẩm trong giỏ hàng
                cart = cart.map(item => {
                    if (orderSelectedProduct.includes(item.id.toString())) {
                        item.select = true; // Đánh dấu sản phẩm được chọn
                    } else {
                        item.select = false; // Đánh dấu sản phẩm không được chọn
                    }
                    return item;
                });

                // Lưu lại giỏ hàng vào localStorage
                localStorage.setItem('cart', JSON.stringify(cart));
                var lang = '<?= $lang ?>';
                // Điều hướng sang trang thanh toán
                if (lang == 'en') {
                    window.location.href = '/check-out';
                } else if (lang == 'vn') {
                    window.location.href = '/vn/check-out';
                } else if (lang == 'es') {
                    window.location.href = '/es/check-out';
                }

            }
        });
        showCart();
        // Cập nhật `localStorage` khi có thay đổi
        $(document).on('change', 'input[name="product_chose"]', function() {
            const cart = JSON.parse(localStorage.getItem('cart')) || [];
            const productId = $(this).data('product-id');
            const isSelected = $(this).is(':checked');

            // Cập nhật trạng thái `select` trong cart
            const updatedCart = cart.map(item => {
                if (item.id === productId) {
                    item.select = isSelected;
                }
                return item;
            });

            // Lưu lại vào localStorage
            localStorage.setItem('cart', JSON.stringify(updatedCart));
            console.log('Updated cart:', updatedCart);



        });
        $('input.check-all').on('change', function() {
            if (!$(this).is(':checked')) {
                console.log("Checkbox đã bị bỏ chọn");

            }
        });



        $('.bg-trans-all').on('click', function() {
            // Kiểm tra xem checkbox đã được chọn hay chưa
            if (!$('.check-all').is(':checked')) {
                alert('<?php pll_e('You have not selected the checkbox yet!') ?>');
            } else {

                localStorage.removeItem("cart");
                $('.item-cart').remove();
                $('.cart-count-in').text('<?php pll_e('Select all') ?> (0)');
                $('.cart-count').text('0');
                $('.check-all').prop('checked', false);
                $(".cart-total-price").text("$0.00");
                calculateTotal();
                alert('<?php pll_e('There are no products left in the cart, please select a product to purchase') ?>\n');
            }
        });

        //$('.select-pack').on('change', function () {
        //    let id_pro = $(this).val(); // Lấy ID sản phẩm từ select
        //    let $this = $(this); // Lưu select hiện tại để xử lý sau
        //
        //    $.ajax({
        //        url: "<?php //echo admin_url('admin-ajax.php'); 
                        ?>//",
        //        type: 'POST',
        //        data: {
        //            action: "SelectPack",
        //            id: id_pro
        //        },
        //        dataType: 'json',
        //        success: function (response) {
        //            if (response.success) {
        //                // Lấy thông tin sản phẩm mới
        //                let newProduct = response.data;
        //
        //                // Lấy giỏ hàng từ localStorage
        //                let cart = JSON.parse(localStorage.getItem('cart')) || [];
        //                let parentContainer = $this.closest('.item-cart'); // Tìm container của sản phẩm hiện tại
        //
        //                // Cập nhật thông tin sản phẩm trong cart
        //                cart = cart.map(item => {
        //                    if (item.id === newProduct.id) {
        //                        item.price = parseFloat(newProduct.price); // Cập nhật giá mới
        //                        item.qty = parseInt(item.qty); // Giữ nguyên số lượng cũ
        //                        item.title = newProduct.title; // Cập nhật tên sản phẩm mới
        //                        item.pack = newProduct.pack; // Cập nhật thông tin pack
        //                    }
        //                    return item;
        //                });
        //
        //                // Lưu lại vào localStorage
        //                localStorage.setItem('cart', JSON.stringify(cart));
        //
        //                // Cập nhật UI: giá tiền và thông tin sản phẩm
        //                parentContainer.find('.text-primary').text('$' + newProduct.price);
        //                parentContainer.find('.quantity').val(newProduct.qty);
        //                parentContainer.find('.select-pack option:selected').text('Type: ' + newProduct.pack + ' Pack');
        //                // Tải lại trang sau khi cập nhật thành công
        //                location.reload();
        //            } else {
        //                console.error("Failed to fetch pack options for product ID: " + id_pro);
        //            }
        //        },
        //        error: function () {
        //            Swal.fire({
        //                icon: 'error',
        //                text: 'An error occurred. Please try again.'
        //            });
        //        }
        //    });
        //});
    </script>
    <script defer src="<?= $url ?>/assets/js/quantity-plus-minus.js"></script>

    <script defer>
        const selectOffer = document.querySelector(".select-offer");
        const viewOffer = document.querySelector(".view-offer");
        const rotateIcon = document.querySelector(".rotate-icon");

        selectOffer.addEventListener("click", () => {
            if (viewOffer.classList.contains("invisible")) {
                // Hiển thị view-offer
                viewOffer.classList.remove("absolute", "invisible", "opacity-0", "max-h-0");
                viewOffer.classList.add("active", "opacity-100", "max-h-[1000px]");

                // Xoay icon
                rotateIcon.classList.add("active");
            } else {
                // Thu gọn max-height về 0 trước
                viewOffer.classList.add("max-h-0", "opacity-0");
                viewOffer.classList.remove("max-h-[500px]", "opacity-100");

                // Sau 500ms (đúng với thời gian transition của max-height), thêm absolute
                setTimeout(() => {
                    if (viewOffer.classList.contains("max-h-0")) {
                        viewOffer.classList.add("absolute", "invisible");
                        viewOffer.classList.remove("active");
                    }
                }, 500); // 500ms trùng với transition duration
                // Xoay lại icon
                rotateIcon.classList.remove("active");
            }
        });
    </script>