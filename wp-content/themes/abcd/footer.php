<?php
$logo_footer = get_field('logo_footer', 'option');
$list_certification = get_field('list_certification', 'option');
$contact_info = get_field('contact_info', 'option');
$service = get_field('service', 'option');
$list_payment_method = get_field('list_payment_method', 'option');
$list_social_media = get_field('list_social_media', 'option');
$copyright = get_field('copyright', 'option');
$lang = ICL_LANGUAGE_CODE;
$url = get_template_directory_uri();
?>

<footer class="bg-[#161A28]">
    <style>
        .wpcf7-not-valid-tip {
            display: none;
        }

        .wpcf7-response-output {
            display: none;
        }
    </style>
    <div class="footer pt-16 pb-8 container 3xl:max-w-[1616px]">
        <div class="flex flex-col items-center justify-center xl:items-start xl:justify-start xl:flex-row gap-10 2xl:gap-12 3xl:gap-[100px]">
            <!-- left col logo -->
            <div data-aos="fade-right" data-aos-duration="1500" class="flex flex-col gap-4">
                <a href="" class="max-w-[180px] 2xl:max-w-[212px]">
                    <figure class="justify-start"><img src="<?= $logo_footer ?>" alt="logo"></figure>
                </a>
                <!-- chứng chỉ -->
                <?php if ($list_certification): ?>
                    <div class="flex gap-2 items-center">
                        <?php foreach ($list_certification as $item): ?>
                            <figure class="w-[60px] 2xl:w-[95px] rounded-full">
                                <img src="<?= $item['image'] ?>" alt="icon">
                            </figure>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            <!-- right info -->
            <div class="xl:flex-1 flex flex-wrap xl:flex-nowrap gap-6 3xl:gap-10 3xl:justify-between">
                <!-- first col -->
                <?php if ($contact_info): ?>
                    <div data-aos="fade-up" data-aos-duration="1500" class="w-full md:w-[48%] xl:w-[35%] 3xl:max-w-[318px] flex flex-col gap-6">
                        <h2 class="text-body-md-bold text-white"><?php pll_e('Customer Support') ?></h2>
                        <div class="flex flex-col gap-5">
                            <?php foreach ($contact_info as $item): ?>
                                <div class="flex gap-3 items-center footer-hover-link">
                                    <div class="icon">
                                        <figure class="w-5 h-5"><img src="<?= $item['icon'] ?>" alt="icon">
                                        </figure>
                                    </div>
                                    <div class="flex-1 text text-body-md-regular text-white">
                                        <p class="text-body-md-regular text-white"><?= $item['name'] ?></p>
                                        <a href="<?= $item['url'] ?>" class="font-semibold"><?= $item['info'] ?></a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif ?>
                <!-- second col -->
                <?php if ($service): ?>
                    <div data-aos="fade-down" data-aos-duration="1500" class="w-full md:w-[48%] xl:w-[16%] 3xl:max-w-[235px] flex flex-col gap-6">
                        <h2 class="text-body-md-bold text-white"><?php pll_e('Services') ?></h2>
                        <div class="">
                            <ul class="grid grid-cols-2 xl:grid-cols-1 gap-2">
                                <?php foreach ($service as $item): ?>
                                    <li class="text-body-md-regular">
                                        <a href="<?= $item['url'] ?>"
                                            class="text-white hover:text-primary"><?= $item['name'] ?></a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                <?php endif ?>
                <!-- third row -->
                <div data-aos="fade-up" data-aos-duration="1500" class="w-full lg:w-[100%] xl:w-[44%] 3xl:max-w-[362px] flex flex-col gap-4">
                    <h2 class="text-body-md-bold text-white"><?php pll_e('Join our Mailing List') ?></h2>
                    <div class="form-register flex flex-col gap-6 2xl:gap-10">
                        <?php if (pll_current_language() == 'en'): ?>
                            <?php echo do_shortcode('[contact-form-7 id="e56ceb0" title="Form đăng ký"]'); ?>
                        <?php elseif (pll_current_language() == 'vn'): ?>
                            <?php echo do_shortcode('[contact-form-7 id="61187f0" title="Form đăng ký(VN)"]'); ?>
                        <?php endif ?>
                        <div class="notice text-[#FF0000]">
                            <span class="no"></span>
                        </div>
                        <?php if ($list_payment_method): ?>
                            <div class="flex flex-wrap gap-3 justify-center xl:justify-start items-center">
                                <?php foreach ($list_payment_method as $item): ?>
                                    <figure class="shadow-icon-footer"><img src="<?= $item['image'] ?>" alt="icon"></figure>
                                <?php endforeach; ?>
                            </div>
                        <?php endif ?>

                        <?php if ($list_social_media): ?>
                            <div class="flex flex-col gap-3 justify-center items-center xl:items-start">
                                <h2 class="text-body-md-bold text-white"><?php pll_e('Join our Mailing List') ?></h2>
                                <div class="flex items-center gap-2.5">
                                    <?php foreach ($list_social_media as $item): ?>
                                        <div class="social-icon">
                                            <figure>
                                                <a href="<?= $item['url'] ?>"><?= $item['icon'] ?></a>
                                            </figure>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif ?>
                    </div>
                </div>
            </div>
        </div>

        <hr class="divider-footer">

        <div class="flex items-center justify-center mt-6">
            <div class="copy-right flex flex-col lg:flex-row gap-4">
                <p class="text-body-md-regular text-white">
                    Copyright @2024 SureMeal
                </p>
                <div class="hidden lg:block w-[1px] h-[21px] bg-white"></div>
                <p class="text-body-md-regular text-white text-center">
                    Designed by <a href="https://qixtech.com/" class="ml-1 hover:text-primary">
                        Qixtech - Web Design & Digital Marketing
                    </a>
                </p>
            </div>
            <!--            <p class="text-body-md-regular text-white">-->
            <!--                --><? //= $copyright 
                                    ?>
            <!--            </p>-->
        </div>
    </div>
</footer>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('.form-register form.wpcf7-form');
        const notice = document.querySelector('.notice .no');
        const emailInputs = form.querySelectorAll('.input-footer'); // Change to class selector
        const submitButton = form.querySelector('button[type="submit"]');
        const currentLang = '<?php echo pll_current_language(); ?>';
        const messages = {
            'en': {
                emailError: 'Please enter a valid email address',
                submitSuccess: 'Registered to receive information successfully!',
                loading: 'Sending...',
                button: 'Register',
                notice: 'An error occurred. Please try again.'
            },
            'vi': {
                emailError: 'Vui lòng nhập đúng định dạng email',
                submitSuccess: 'Đăng ký nhận thông tin thành công!',
                loading: 'Đang gửi...',
                button: 'Đăng ký',
                notice: 'Có lỗi xảy ra. Vui lòng thử lại.'
            }
        };

        if (form) {
            // Use Contact Form 7's events instead
            form.addEventListener('wpcf7beforesubmit', function(e) {
                let isValid = true;
                emailInputs.forEach(input => {
                    if (!validateEmail(input.value)) {
                        notice.innerHTML = messages[currentLang].emailError;
                        isValid = false;
                        e.preventDefault();
                    }
                });
                if (isValid) {
                    submitButton.innerHTML = messages[currentLang].loading;
                }
            });

            form.addEventListener('wpcf7mailsent', function() {
                notice.innerHTML = messages[currentLang].submitSuccess;
                form.reset();
                submitButton.innerHTML = messages[currentLang].button;
            });

            form.addEventListener('wpcf7mailfailed', function() {
                notice.innerHTML = messages[currentLang].notice;
                submitButton.innerHTML = messages[currentLang].button;
            });
        }

        function validateEmail(email) {
            const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return re.test(String(email).toLowerCase());
        }
    });
</script>
<?php wp_footer() ?>
<!-- fancy-box -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.js"
    integrity="sha512-uURl+ZXMBrF4AwGaWmEetzrd+J5/8NRkWAvJx5sbPSSuOb0bZLqf+tOzniObO00BjHa/dD7gub9oCGMLPQHtQA=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script type="text/javascript"
    src="https://appleid.cdn-apple.com/appleauth/static/jsapi/appleid/1/en_US/appleid.auth.js"></script>

<!-- aos js -->
<script src="<?= $url ?>/assets/js/aos.js"></script>
<script src="<?= $url ?>/assets/js/wow.min.js"></script>
<script>
    AOS.init();
    new WOW().init();
</script>
<!-- other js -->
<script defer src="<?= $url ?>/assets/js/header.js"></script>
<script defer src="<?= $url ?>/assets/js/tab-item.js"></script>
<script defer src="<?= $url ?>/assets/js/quantity-plus-minus.js"></script>
<script defer src="<?= $url ?>/assets/js/show-password.js"></script>
<script defer src="<?= $url ?>/assets/js/ranger-select.js"></script>
<script src="<?= $url ?>/assets/lib/jquery/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js"></script>
<script src="https://www.google.com/recaptcha/api.js?render=<?= get_field("site_key", "option") ?>"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="<?= $url ?>/assets/js/dev-js.js"></script>
<!-- script combo -->
<script defer src="<?= $url ?>/assets/js/combo-select.js"></script>
<script>
    function buyNow(thiss) {
        // Lấy thông tin sản phẩm từ các thuộc tính
        var id = thiss.attr("data-id"); // ID sản phẩm
        var img = thiss.attr("data-img"); // Hình ảnh sản phẩm
        var price = parseFloat(thiss.attr("data-price")); // Giá sản phẩm
        var link = thiss.attr("data-link"); // Đường dẫn sản phẩm
        var title = thiss.attr("data-title"); // Tên sản phẩm
        var qty = parseInt(thiss.attr("data-quantity")); // Số lượng mặc định khi thêm mới
        var weight = thiss.attr("data-weight"); // Trọng lượng sản phẩm
        var instock = parseInt(thiss.attr("data-instock"));
        var promo = thiss.attr("data-promo"); // Promotion
        var pack = parseInt(thiss.attr("data-pack"));
        console.log(qty);
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
            qty,
            promo,
            select: true // Đặt mặc định là chọn sản phẩm này
        };
        // Kiểm tra tồn kho
        if (instock <= 0) {
            alert('Product is out of stock');
            return;
        }
        console.log("Sản phẩm cần thêm:", product);

        // Lấy giỏ hàng từ localStorage, nếu chưa có thì khởi tạo mảng rỗng
        var cart = JSON.parse(localStorage.getItem("cart")) || [];

        // Chuyển tất cả sản phẩm trong giỏ hàng thành không được chọn (select = false)
        cart.forEach(item => item.select = false);

        // Kiểm tra xem sản phẩm đã có trong giỏ hàng chưa
        var found = false; // Biến đánh dấu sản phẩm đã tồn tại

        for (var i = 0; i < cart.length; i++) {
            if (cart[i].id === product.id) {
                // Nếu sản phẩm đã tồn tại, cập nhật thông tin
                found = true;
                cart[i].weight = product.weight;
                cart[i].pack = product.pack;
                cart[i].instock = product.instock;
                cart[i].qty = qty; // Đặt lại số lượng thành 1
                cart[i].select = true; // Chỉ chọn sản phẩm này
                console.log("Đã cập nhật sản phẩm trong giỏ hàng:", cart[i]);
                break; // Thoát khỏi vòng lặp
            }
        }

        if (!found) {
            // Nếu sản phẩm chưa tồn tại, thêm sản phẩm vào giỏ hàng
            cart.push(product);
            console.log("Đã thêm sản phẩm mới vào giỏ hàng:", product);
        }

        // Lưu giỏ hàng đã cập nhật vào localStorage
        localStorage.setItem("cart", JSON.stringify(cart));

        // Tính tổng số sản phẩm khác nhau trong giỏ hàng (không tính `qty`)
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

        // Chuyển hướng sang trang checkout
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
</script>
</body>

</html>