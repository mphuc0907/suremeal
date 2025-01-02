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

$lang = ICL_LANGUAGE_CODE;
?>
<main class="bg-[#EEF0F6]">
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
                } else if (lang == 'vie') {
                    window.location.href = '/vie/check-out';
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