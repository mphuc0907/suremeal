<?php /* Template Name: Check-Out */ ?>
<?php
get_header();

$countryregion = get_field('countryregion', 'option');

$json = json_encode($countryregion);
$id = get_the_ID();
global $wpdb;
$list_voucher = $wpdb->get_results("SELECT * FROM `wp_voucher` WHERE `status` = 2");
$count_vouchers = $wpdb->get_var("SELECT COUNT(*) FROM `wp_voucher` WHERE `status` = 2");

$total_order = get_field('total_order', 'option');

$url = get_template_directory_uri();

$authenticated_user = validate_user_token();
$id_user = $authenticated_user->ID;
$user = $wpdb->get_results("SELECT * FROM `wp_account_users` WHERE `id` = " . $id_user);

$state = $user[0]->state;
$city = $user[0]->city;
$country = $user[0]->country;
$phone_number = $user[0]->phone_number;
$fullname_shipper = $user[0]->fullname_shipper;
$email_shipper = $user[0]->email_shipper;
$zipcode_shipper = $user[0]->zipcode_shipper;
$address1_shipper = $user[0]->address1_shipper;
$address2_shipper = $user[0]->address2_shipper;

?>

<main class="bg-[#EEF0F6]">
    <section class="py-6">
        <div class="container">
            <a onclick="history.back()" class="text-body-md-medium text-secondary flex gap-3">
                <figure><img src="<?= $url ?>/assets/image/icon/arrow-left-20-second.svg" alt="icon"></figure>
                <?php pll_e('Back to cart') ?>
            </a>
        </div>
    </section>
    <style>
        .view-offer,
        .view-delivery-info {
            position: absolute;
            transition: all 0.5s ease-in-out;
        }

        .view-offer.active,
        .view-delivery-info.active {
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
                <div class="flex-1 flex flex-col gap-6 w-full lg:w-[67%] max-w-[900px]">
                    <div class="flex flex-col gap-4 card-select">
                        <h2 class="text-body-xl-medium text-gray-9"><?php pll_e('Product list') ?> (2)</h2>

                    </div>

                    <div class="flex flex-col gap-4">
                        <h2 class="text-body-xl-medium text-gray-9"><?php pll_e('Delivery information') ?></h2>
                        <div class="flex flex-col gap-4 lg:gap-6 p-6 rounded-xl bg-white">
                            <?php if (isset($_COOKIE['user_token']) || $authenticated_user) {?>
                                <?php if (!empty($fullname_shipper)) :?>
                            <div class="flex flex-col gap-2">

                                <label class="flex items-center gap-2">
                                    <input type="radio" name="payment" class="radio-blue" value="1"
                                           data-fullname="<?= $fullname_shipper ?>"
                                           data-city="<?= $city ?>"
                                           data-state="<?= $state ?>"
                                           data-country="<?= $country ?>"
                                           data-phone_number="<?= $phone_number ?>"
                                           data-email="<?= $email_shipper ?>"
                                           data-zipcode_shipper="<?= $zipcode_shipper ?>"
                                           data-address1_shipper="<?= $address1_shipper ?>"
                                           data-address2_shipper="<?= $address2_shipper ?>"
                                    >
                                    <div class="flex flex-col">
                                        <h2 class="text-body-md-semibold text-gray-8"><?= $fullname_shipper ?></h2>
                                        <p class="text-body-md-regular text-gray-3"><?= $address1_shipper ?>, <?= $city ?>, <?= $state ?>, <?= $country ?> </p>
                                    </div>

                                </label>
                            </div>
                                <?php endif;?>
    <?php }?>
                            <div class="flex flex-col gap-2">

                                <label class="flex items-center gap-2">
                                    <input type="radio" name="payment" class="radio-blue select-delivery" value="0">
                                    <p class="text-body-md-regular text-secondary"><?php pll_e('Enter other delivery information') ?></p>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div
                        class="flex flex-col gap-4 view-delivery-info absolute left-0 right-0 overflow-hidden max-h-0 opacity-0 invisible transition-[max-height,opacity] duration-500 ease-in-out">
                        <h2 class="text-body-xl-medium text-gray-9"><?php pll_e('Enter delivery information') ?></h2>
                        <div class="flex flex-col gap-4 lg:gap-6 p-6 rounded-xl bg-white">
                            <form action="" id="check-out" class="flex flex-wrap gap-5">
                                <div class="w-full flex flex-col lg:flex-row items-center gap-5">
                                    <label class="input-label w-50-g-10">
                                        <p class="input-title"><?php pll_e('Name') ?> <span class="text-primary">*</span></p>
                                        <input type="text" class="input-field" name="fullName"  placeholder="<?php pll_e('Your name') ?>">
                                    </label>
                                    <label class="input-label w-50-g-10">
                                        <p class="input-title"><?php pll_e('Email') ?> <span class="text-primary">*</span></p>
                                        <input type="text" class="input-field" name="email" placeholder="<?php pll_e('Email address') ?>">
                                    </label>

                                </div>
                                <label class="input-label w-50-g-10">
                                    <p class="input-title"><?php pll_e('Phone number') ?> <span class="text-primary">*</span></p>
                                    <input type="text" name="phone_numer" class="input-field" placeholder="<?php pll_e('Phone number') ?>">
                                </label>
                                <label class="input-label w-50-g-10">
                                    <p class="input-title"><?php pll_e('Country/Region') ?> <span class="text-primary">*</span>
                                    </p>
                                    <select class="input-field" id="country" name="country">
                                        <?php foreach ($countryregion as $key => $val) :?>
                                        <option value="<?= $val['region'] ?>"><?= $val['region'] ?></option>
                                        <?php endforeach;?>

                                    </select>
                                </label>

                                <label class="input-label w-50-g-10">
                                    <p class="input-title"><?php pll_e('Address') ?> 1 <span class="text-primary">*</span></p>
                                    <input type="text" name="address1" class="input-field" placeholder="<?php pll_e('Street address') ?>">
                                </label>
                                <label class="input-label w-50-g-10">
                                    <p class="input-title"><?php pll_e('Address') ?> 2 <span
                                            class="text-body-md-regular text-neutral-400">(<?php pll_e('optional') ?>)</span>
                                    </p>
                                    <input type="text" name="address2" class="input-field" placeholder="Apt, suite, unit, building, floor, etc.
                                    ">
                                </label>

                                <div class="w-full flex flex-col lg:flex-row items-center gap-5">
                                    <label class="input-label">
                                        <p class="input-title"><?php pll_e('City') ?> <span class="text-primary">*</span></p>
                                        <input type="text" class="input-field" name="city"  placeholder="<?php pll_e('Enter your city') ?>
                                        ">
                                    </label>
                                    <label class="input-label Vietnam" style="display: none">
                                        <p class="input-title"><?php pll_e('Province') ?> <span class="text-primary">*</span>
                                        </p>
                                        <select class="input-field" id="province" name="province">
                                            <option value=""><?php pll_e('Choose province') ?></option>

                                        </select>

                                    </label>
                                    <label class="input-label Vietnam" style="display: none">
                                        <p class="input-title"><?php pll_e('Postal code') ?> <span class="text-primary">*</span></p>
                                        <input type="text" class="input-field" name="postaCode" placeholder="<?php pll_e('Enter your Postal code') ?>
                                        ">
                                    </label>

                                    <label class="input-label noVietnam">
                                        <p class="input-title"><?php pll_e('State') ?> <span class="text-primary">*</span>
                                        </p>
                                        <select class="input-field" id="state" name="state">
                                            <option value=""><?php pll_e('Choose State') ?></option>
                                            <option value="Alabama">Alabama</option>
                                            <option value="Alaska">Alaska</option>
                                            <option value="Arizona">Arizona</option>
                                            <option value="Arkansas">Arkansas</option>
                                            <option value="California">California</option>
                                            <option value="Colorado">Colorado</option>
                                            <option value="Connecticut">Connecticut</option>
                                            <option value="Delaware">Delaware</option>
                                            <option value="Florida">Florida</option>
                                            <option value="Georgia">Georgia</option>
                                            <option value="Hawaii">Hawaii</option>
                                            <option value="Idaho">Idaho</option>
                                            <option value="Illinois">Illinois</option>
                                            <option value="Indiana">Indiana</option>
                                            <option value="Iowa">Iowa</option>
                                            <option value="Kansas">Kansas</option>
                                            <option value="Kentucky">Kentucky</option>
                                            <option value="Louisiana">Louisiana</option>
                                            <option value="Maine">Maine</option>
                                            <option value="Maryland">Maryland</option>
                                            <option value="Massachusetts">Massachusetts</option>
                                            <option value="Michigan">Michigan</option>
                                            <option value="Minnesota">Minnesota</option>
                                            <option value="Mississippi">Mississippi</option>
                                            <option value="Missouri">Missouri</option>
                                            <option value="Montana">Montana</option>
                                            <option value="Nebraska">Nebraska</option>
                                            <option value="Nevada">Nevada</option>
                                            <option value="New Hampshire">New Hampshire</option>
                                            <option value="New Jersey">New Jersey</option>
                                            <option value="New Mexico">New Mexico</option>
                                            <option value="New York">New York</option>
                                            <option value="North Carolina">North Carolina</option>
                                            <option value="North Dakota">North Dakota</option>
                                            <option value="Ohio">Ohio</option>
                                            <option value="Oklahoma">Oklahoma</option>
                                            <option value="Oregon">Oregon</option>
                                            <option value="Pennsylvania">Pennsylvania</option>
                                            <option value="Rhode Island">Rhode Island</option>
                                            <option value="South Carolina">South Carolina</option>
                                            <option value="South Dakota">South Dakota</option>
                                            <option value="Tennessee">Tennessee</option>
                                            <option value="Texas">Texas</option>
                                            <option value="Utah">Utah</option>
                                            <option value="Vermont">Vermont</option>
                                            <option value="Virginia">Virginia</option>
                                            <option value="Washington">Washington</option>
                                            <option value="West Virginia">West Virginia</option>
                                            <option value="Wisconsin">Wisconsin</option>
                                            <option value="Wyoming">Wyoming</option>
                                        </select>

                                    </label>
                                    <label class="input-label noVietnam">
                                        <p class="input-title"><?php pll_e('ZIP Code') ?> <span class="text-primary">*</span></p>
                                        <input type="text" class="input-field" name="zipCode" placeholder="<?php pll_e('Enter your ZIP Code') ?>
                                        ">
                                    </label>
                                </div>
<?php if (isset($_COOKIE['user_token']) || $authenticated_user) {?>
                                <label class="custom-checkbox">
                                    <div class="checkbox-container">
                                        <input type="checkbox" name="saveInformation" value="1">
                                        <span class="checkmark"></span>
                                    </div>
                                    <p class="text-body-md-regular text-gray-9"><?php pll_e('Save this information for next time') ?>
                                    </p>
                                </label>
    <?php }?>
                            </form>
                        </div>
                    </div>

                    <div class="flex flex-col gap-4">
                        <h2 class="text-body-xl-medium text-gray-9"><?php pll_e('Select payment method') ?></h2>
                        <div class="flex flex-col rounded-xl bg-white">
<!--                            <label class="flex items-center gap-5 px-5 py-4 border-b border-neutral-300 border-solid">-->
<!--                                <input type="radio" name="paymentCart" class="radio-blue">-->
<!--                                <div class="flex flex-wrap items-center gap-2">-->
<!--                                    <figure><img src="<?= $url ?>/assets/image/badge-visa.svg" alt=""></figure>-->
<!--                                    <div class="w-full lg:w-auto flex gap-2">-->
<!--                                        <p class="text-body-md-semibold text-gray-9">Visa</p>-->
<!--                                        <p class="text-body-md-regular text-neutral-500">Ending in 4567</p>-->
<!--                                    </div>-->
<!--                                </div>-->
<!--                            </label>-->
<!--                            <label class="flex items-center gap-5 px-5 py-4 border-b border-neutral-300 border-solid">-->
<!--                                <input type="radio" name="paymentCart" class="radio-blue">-->
<!--                                <div class="flex flex-wrap items-center gap-2">-->
<!--                                    <figure><img src="<?= $url ?>/assets/image/badge-master.svg" alt=""></figure>-->
<!--                                    <div class="w-full lg:w-auto flex gap-2">-->
<!--                                        <p class="text-body-md-semibold text-gray-9">Mastercard</p>-->
<!--                                        <p class="text-body-md-regular text-neutral-500">Ending in 1234</p>-->
<!--                                    </div>-->
<!--                                </div>-->
<!--                            </label>-->
                            <label class="flex items-center gap-5 px-5 py-4">
                                <input type="radio" name="paymentCart" class="radio-blue" checked>
                                <div class="w-full flex flex-wrap items-center gap-2 justify-between">
                                    <!-- <figure><img src="<?= $url ?>/assets/image/badge-master.svg" alt=""></figure> -->
                                    <p class="text-body-md-medium text-gray-9"><?php pll_e('Add a credit or debit') ?></p>
                                    <div class="flex w-full lg:w-auto flex-wrap items-center gap-2">
                                        <div class="logo-icon">
                                            <figure><img src="<?= $url ?>/assets/image/footer-visa.svg" alt="visa">
                                            </figure>
                                        </div>
                                        <div class="logo-icon">
                                            <figure><img src="<?= $url ?>/assets/image/footer-mc.svg" alt="mc"></figure>
                                        </div>
                                        <div class="logo-icon">
                                            <figure><img src="<?= $url ?>/assets/image/footer-amex.svg" alt="amex">
                                            </figure>
                                        </div>
                                        <div class="logo-icon">
                                            <figure><img src="<?= $url ?>/assets/image/footer-ap.svg" alt="ap"></figure>
                                        </div>
                                        <div class="logo-icon">
                                            <figure><img src="<?= $url ?>/assets/image/footer-pp.svg" alt="pp"></figure>
                                        </div>
                                        <div class="logo-icon">
                                            <figure><img src="<?= $url ?>/assets/image/footer-jcb.svg" alt="jcb">
                                            </figure>
                                        </div>
                                        <div class="logo-icon">
                                            <figure><img src="<?= $url ?>/assets/image/footer-dcv.svg" alt="dcv">
                                            </figure>
                                        </div>
                                        <div class="logo-icon">
                                            <figure><img src="<?= $url ?>/assets/image/footer-dc.svg" alt="dc"></figure>
                                        </div>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col gap-4 w-full lg:w-[33%] lg:max-w-[437px] pt-12">
                    <div
                            class="select-offer cursor-pointer flex items-center gap-6 justify-between px-6 py-4 rounded-t-xl bg-white">
                        <div class="flex items-center gap-3">
                            <figure class="w-6 h-6"><img src="<?= $url ?>/assets/image/icon/ticket-sale-primary.svg"
                                                         alt="icon">
                            </figure>
                            <p class="text-body-md-medium text-gray-9"><?php pll_e('Select or enter an offer') ?></p>
                        </div>
                        <button class="rotate-icon button bg-trans transition-transform rotate-0">
                            <figure class=""><img src="<?= $url ?>/assets/image/icon/chev-up-24.svg" alt="icon">
                            </figure>
                        </button>
                    </div>

                    <div
                            class="view-offer absolute left-0 right-0 flex flex-col items-center gap-6 px-6 py-4 rounded-xl bg-white overflow-hidden max-h-0 opacity-0 invisible transition-[max-height,opacity] duration-500 ease-in-out">
                        <label class="input-label">
                            <p class="input-title"><?php pll_e('Discount code') ?></p>
                            <input type="text" class="input-field" id="codeVoucher" name="voucher_code" placeholder="<?php pll_e('Enter your discount code here') ?>">
                        </label>
                        <button class="button bg-primary text-body-md-semibold w-full code_apply" disabled><?php pll_e('Apply code') ?></button>

                        <div class="flex flex-col gap-3">
                            <p><?php pll_e('Promotion') ?> (<?= $count_vouchers ?>)</p>
                            <div class="flex flex-col gap-3 max-h-[400px] overflow-auto custom-scrollbar">
                                <!-- single promo -->
                                <?php foreach ($list_voucher as $key => $value) :?>
                                    <div class="py-3 flex items-center gap-5 rounded-xl bg-white">
                                        <figure class="w-14 h-14"><img src="<?= $url ?>/assets/image/icon/couple.svg"
                                                                       alt="couple">
                                        </figure>
                                        <div class="flex-1 flex flex-col gap-2">
                                            <h2 class="text-body-md-medium text-gray-9 truncate-2row"><?= $value->voucher_name ?></h2>
                                            <p class="text-body-xs-regular text-neutral-500 truncate-2row"><?= $value->order_description ?>
                                            </p>
                                        </div>
                                        <button class="w-8 h-8 flex items-center justify-center rounded-full bg-[#FEE] app-voucher" data-voucher="<?= $value->voucher_code ?>">
                                            <figure class="w-6 h-6"><img
                                                        src="<?= $url ?>/assets/image/icon/plus-24-primary.svg" alt="icon">
                                            </figure>
                                        </button>
                                    </div>
                                <?php endforeach;?>

                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col gap-5 px-6 py-4 rounded-xl bg-white">
                        <p><?php pll_e('Order information') ?></p>
                        <div class="flex flex-col gap-3">
                            <div class="flex items-center justify-between">
                                <p class="text-body-sm-regular text-gray-7"><?php pll_e('Sub total') ?></p>
                                <h2 class="text-body-md-medium text-gray-9 total-sub">$0.00</h2>
                            </div>
                            <hr class="divider">
                            <div class="flex items-center justify-between">
                                <p class="text-body-sm-regular text-gray-7"><?php pll_e('Discount') ?></p>
                                <h2 class="text-body-md-medium text-gray-9 discount-fee">$0.00</h2>
                            </div>
                            <div class="flex items-center justify-between">
                                <p class="text-body-sm-regular text-gray-7"><?php pll_e('Shipping fee') ?></p>
                                <!-- <p class="text-body-sm-regular text-secondary">Free of charge</p> -->
                                <h2 class="text-body-md-medium text-gray-9 shipping-fee" >--</h2>
                            </div>
<!--                            <div class="flex items-center justify-between">-->
<!--                                <p class="text-body-sm-regular text-gray-7">Taxes</p>-->
<!--                                <h2 class="text-body-md-medium text-gray-9">$0.00 </h2>-->
<!--                            </div>-->
                            <hr class="divider">
                            <div class="flex items-center justify-between">
                                <p class="text-body-sm-regular text-gray-7"><?php pll_e('Total payment') ?></p>
                                <h2 class="text-body-xl-medium text-primary total-true" >$0.00</h2>
                            </div>
                        </div>
                        <p class="text-body-sm-regular">
                            <?php pll_e(`By proceeding with your purchase, you agree to SureMeal's Terms of Service`) ?> By proceeding with your purchase, you agree to SureMeal's <a href="<?= get_field('link_terms_of_service', $id) ?>"
                                class="text-secondary"><?php pll_e('Terms of Service') ?></a> <?php pll_e('and') ?> <a href="<?= get_field('link_personal_data_processing_policy', $id) ?>"
                                class="text-secondary"><?php pll_e('Personal Data Processing Policy.') ?></a>
                        </p>
                        <button onclick="ConfirmOrder()" class="mt-3 w-full button bg-primary text-body-md-semibold text-white" id="submit-check-out"><?php pll_e('Check out') ?></button>
                         <button onclick="ConfirmOrderInfomation()" class="mt-3 w-full button bg-primary text-body-md-semibold text-white" id="submit-check-out-info" style="display: none"><?php pll_e('Check out') ?></button>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
<?php get_footer() ?>
<!-- script offer js -->


<script>



    var cart = JSON.parse(localStorage.getItem("cart")); // Chuyển đổi JSON thành object hoặc array

    if (!cart || cart.length === 0) { // Kiểm tra nếu không có giỏ hàng hoặc giỏ hàng rỗng
        alert("<?php pll_e('Please select a product before proceeding to checkout!') ?>");
        window.location.href = "http://suremeal.qixtech.com/product/";
    }

    // Bắt sự kiện thay đổi của danh mục
    $('#country').change(function () {
        var selectedCountry = $(this).val();

        $.ajax({
            url: '<?= admin_url("admin-ajax.php"); ?>',
            type: 'POST',
            data: {
                action: 'choseProvince',
                parent_id: selectedCountry
            },
            success: function (response) {
                if (selectedCountry == 'United States') {
                    $('#state').empty().append('<option value="">Choose State</option>');

                    if (response.success) {
                        $.each(response.data, function (index, provinceName) {
                            $('#state').append('<option value="' + provinceName + '">' + provinceName + '</option>');
                        });
                    } else {
                        alert(response.data.message || '<?php pll_e('No provinces found.') ?>');
                    }
                }else {
                    $('#province').empty().append('<option value=""><?php pll_e('Choose province') ?></option>');

                    if (response.success) {
                        $.each(response.data, function (index, provinceName) {
                            $('#province').append('<option value="' + provinceName + '">' + provinceName + '</option>');
                        });
                    } else {
                        alert(response.data.message || '<?php pll_e('No provinces found.') ?>');
                    }
                }

            },
            error: function () {
                alert('<?php pll_e('Unable to fetch provinces. Please try again later.') ?>');
            }
        });
    });

</script>
<script defer>
    // $(document).ready(function () {
        var total = $('.total-true').data('total');


    //Áp mã giảm giá
    $(document).ready(function () {
        $('.code_apply').on('click', function (event) {
            const carts = JSON.parse(localStorage.getItem('cart')) || [];
            const voucherCode = $('input[name="voucher_code"]').val();

            // Check if any products are selected
            const selectedItems = carts.filter(item => item.select === true);
            if (selectedItems.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    text: '<?php pll_e('Voucher cannot be applied because there are no products selected!') ?>'
                });
                return;
            }

            // Calculate subtotal for selected items
            const subtotal = selectedItems.reduce((total, item) => total + (item.price * item.qty), 0);

            // Get IDs of selected products
            const selectedProductIds = selectedItems.map(item => item.id);

            $.ajax({
                url: "<?= admin_url('admin-ajax.php'); ?>",
                type: 'POST',
                data: {
                    voucherCode: voucherCode,
                    dataProduct: selectedProductIds,
                    subtotal: subtotal,
                    action: 'submitVoucher'
                },
                dataType: 'json',
                beforeSend: function () {
                    Swal.fire({
                        title: 'Processing',
                        html: '<?php pll_e('Please wait...') ?>',
                        didOpen: () => {
                            Swal.showLoading()
                        }
                    });
                },
                success: function (response) {
                    if (response.status === 1 || response.status === 3) {
                        const voucher = response.voucher;
                        let discountAmount = 0;

                        // Tính tổng tiền ban đầu của các sản phẩm được chọn
                        const subTotal = selectedItems.reduce((total, item) => total + (item.price * item.qty), 0);

                        // Nếu mã giảm giá áp dụng cho các sản phẩm cụ thể (type = 0)
                        if (voucher.type == 0) {
                            // Lọc các sản phẩm được áp dụng mã giảm giá
                            const applicableProducts = selectedItems.filter(item => 
                                voucher.product_id.includes(item.id)
                            );

                            // Tính tổng tiền của các sản phẩm được áp dụng
                            const applicableTotal = applicableProducts.reduce((total, item) => total + (item.price * item.qty), 0);

                            // Tính giảm giá
                            if (voucher.discount_type == 1) {
                                // Giảm giá cố định
                                discountAmount = Math.min(applicableTotal, parseFloat(voucher.discount_amount));
                            } else if (voucher.discount_type == 2) {
                                // Giảm giá theo phần trăm
                                discountAmount = applicableTotal * (parseFloat(voucher.discount_amount) / 100);
                                
                                // Kiểm tra nếu max_discount là 0 hoặc null thì không giới hạn
                                if (parseFloat(voucher.max_discount) === 0 || voucher.max_discount === null) {
                                    discountAmount = applicableTotal * (parseFloat(voucher.discount_amount) / 100);
                                } else {
                                    // Giới hạn giảm tối đa
                                    discountAmount = Math.min(discountAmount, parseFloat(voucher.max_discount));
                                }
                            }
                        } 
                        // Nếu mã giảm giá áp dụng cho toàn bộ đơn hàng (type = 1)
                        else if (voucher.type == 1) {
                            if (voucher.discount_type == 1) {
                                // Giảm giá cố định
                                discountAmount = Math.min(subTotal, parseFloat(voucher.discount_amount));
                            } else if (voucher.discount_type == 2) {
                                // Giảm giá theo phần trăm
                                discountAmount = subTotal * (parseFloat(voucher.discount_amount) / 100);
                                
                                // Kiểm tra nếu max_discount là 0 hoặc null thì không giới hạn
                                if (parseFloat(voucher.max_discount) === 0 || voucher.max_discount === null) {
                                    discountAmount = subTotal * (parseFloat(voucher.discount_amount) / 100);
                                } else {
                                    // Giới hạn giảm tối đa
                                    discountAmount = Math.min(discountAmount, parseFloat(voucher.max_discount));
                                }
                            }
                        }

                        // Tính tổng thanh toán mới
                        const totalAfterDiscount = subTotal - discountAmount;

                        Swal.fire({
                            icon: 'success',
                            text: response.message,
                        }).then(() => {
                            $('.discount-fee').text(`$${discountAmount.toFixed(2)}`);
                            $('.discount-fee').attr('data-discount', discountAmount);
                            var shippingFee = parseFloat($('.shipping-fee').data('shipping')) || 0;
                            updateTotal(shippingFee);
                        });
                    } else {
                        Swal.fire({
                            icon: 'warning',
                            text: response.message,
                        });
                    }
                },
                error: function (xhr) {
                    Swal.fire({
                        icon: 'error',
                        text: '<?php pll_e('An error occurred. Please try again.') ?>'
                    });
                }
            });
        });
    });
    // Dữ liệu quốc gia dạng JSON
        var jsoncountry = JSON.parse('<?= $json ?>');
    console.log(jsoncountry);
        // Hàm tính phí vận chuyển
    function calculateShipping(selectedCountry, selectedState = '') {
        // Tìm thông tin quốc gia từ jsoncountry
        var countryData = jsoncountry.find(item => item.region === selectedCountry);
        const totalElement = $('.total-true');
        const shippingFeeElement = $('.shipping-fee');

        if (countryData) {
            let shippingFee = 0;
            var feeShip = '<?=$total_order ?>';
            console.log(feeShip);
            // Nếu là United States
            if (selectedCountry === 'United States') {
                const totalValue = parseFloat(totalElement.data('total'));
                const cityState = countryData.city_state;
                if (totalValue > parseFloat(feeShip)) {
                    shippingFee = 0;
                    shippingFeeElement.text('<?php pll_e('Free of charge') ?>');
                } else {
                    shippingFee = parseFloat(countryData.shipping) || 0;
                    shippingFeeElement.text(`$${shippingFee}`);
                }
                updateTotal(shippingFee);
                if (cityState) {
                         if (selectedState) {
                                            // Xử lý logic khi có danh sách tỉnh/thành phố
                        // $('.input-field[name="state"]').off('change').on('change', function () {
                                const selectedProvince = selectedState;
                                const provinceData = cityState.find(city => city.city === selectedProvince);
                                        
                            if (provinceData) {
                                let cart = localStorage.getItem('cart');

                                if (cart) {
                                    cart = JSON.parse(cart); // Chuyển chuỗi JSON thành object
                                    let totalWeight = 0;

                                    // Lọc chỉ các item có select là true
                                    const selectedItems = cart.filter(item => item.select === true);

                                    // Lặp qua các item đã được chọn
                                    selectedItems.forEach(item => {
                                        const weight = parseFloat(item.weight) || 0;
                                        const qty = parseInt(item.qty) || 0;

                                        totalWeight += weight * qty;
                                    });

                                    // Tính phí ship dựa trên provinceData
                                    shippingFee = totalWeight * parseFloat(provinceData.shipping_state || 0);
                                    shippingFeeElement.text(`$${shippingFee.toFixed(2)}`);
                                    shippingFeeElement.attr('data-shipping', shippingFee);

                                    // Cập nhật tổng giá trị
                                    updateTotal(shippingFee);
                                } else {
                                
                                    if (totalValue > parseFloat(feeShip)) {
                                    shippingFee = 0;
                                    shippingFeeElement.text('<?php pll_e('Free of charge') ?>');
                                    shippingFeeElement.attr('data-shipping', 0);
                                        updateTotal(shippingFee);
                                } else {
                                    shippingFee = parseFloat(countryData.shipping) || 0;
                                    shippingFeeElement.text(`$${shippingFee}`);
                                        updateTotal(shippingFee);
                                }

                                }
                            } else {
                                console.log('Không có dữ liệu provinceData. Không tính phí ship.');
                                shippingFeeElement.text('Free of charge');
                                
                                if (totalValue > parseFloat(feeShip)) {
                                    shippingFee = 0;
                                    shippingFeeElement.text('<?php pll_e('Free of charge') ?>');
                                    shippingFeeElement.attr('data-shipping', 0);
                                    updateTotal(shippingFee);
                                } else {
                                    shippingFee = parseFloat(countryData.shipping) || 0;
                                    shippingFeeElement.text(`$${shippingFee}`);
                                    shippingFeeElement.attr('data-shipping', 10);
                                    updateTotal(shippingFee);
                                }

                            }
                        // });
                    }else {
                                    // Xử lý logic khi có danh sách tỉnh/thành phố
                        $('.input-field[name="state"]').off('change').on('change', function () {
                                const selectedProvince = $(this).val();
                                const provinceData = cityState.find(city => city.city === selectedProvince);
                                        
                            if (provinceData) {
                                let cart = localStorage.getItem('cart');

                                if (cart) {
                                    cart = JSON.parse(cart); // Chuyển chuỗi JSON thành object
                                    let totalWeight = 0;

                                    // Lọc chỉ các item có select là true
                                    const selectedItems = cart.filter(item => item.select === true);

                                    // Lặp qua các item đã được chọn
                                    selectedItems.forEach(item => {
                                        const weight = parseFloat(item.weight) || 0;
                                        const qty = parseInt(item.qty) || 0;

                                        totalWeight += weight * qty;
                                    });

                                    // Tính phí ship dựa trên provinceData
                                    shippingFee = totalWeight * parseFloat(provinceData.shipping_state || 0);
                                    shippingFeeElement.text(`$${shippingFee.toFixed(2)}`);
                                    shippingFeeElement.attr('data-shipping', shippingFee);

                                    // Cập nhật tổng giá trị
                                    updateTotal(shippingFee);
                                } else {
                                
                                    if (totalValue > parseFloat(feeShip)) {
                                    shippingFee = 0;
                                    shippingFeeElement.text('<?php pll_e('Free of charge') ?>');
                                    shippingFeeElement.attr('data-shipping', 0);
                                } else {
                                    shippingFee = parseFloat(countryData.shipping) || 0;
                                    shippingFeeElement.text(`$${shippingFee}`);
                                }
                                updateTotal(shippingFee);
                                }
                            } else {
                                console.log('Không có dữ liệu provinceData. Không tính phí ship.');
                                shippingFeeElement.text('Free of charge');
                                
                                if (totalValue > parseFloat(feeShip)) {
                                    shippingFee = 0;
                                    shippingFeeElement.text('<?php pll_e('Free of charge') ?>');
                                    shippingFeeElement.attr('data-shipping', 0);
                                    updateTotal(shippingFee);
                                } else {
                                    shippingFee = parseFloat(countryData.shipping) || 0;
                                    shippingFeeElement.text(`$${shippingFee}`);
                                    shippingFeeElement.attr('data-shipping', 10);
                                    updateTotal(shippingFee);
                                }

                            }
                        });
                    }
                        
                }
                else {
                    // Nếu không có cityState, dùng phí ship quốc gia
                    if (totalValue > parseFloat(feeShip)) {
                        shippingFee = 0;
                        shippingFeeElement.text('Free of charge');
                    } else {
                        shippingFee = parseFloat(countryData.shipping) || 0;
                        shippingFeeElement.text(`$${shippingFee}`);
                    }
                    shippingFeeElement.attr('data-shipping', shippingFee);
                }
                updateTotal(shippingFee);
                $('.Vietnam').css('display', 'none');
                $('.noVietnam').css('display', 'block');
            } else {
                // Quốc gia khác United States
                let cart = localStorage.getItem('cart');
                const cityState = countryData.city_state;
                const minimum_volume = countryData.minimum_volume;
                const shipping_weight = countryData.shipping_weight;
                console.log(shipping_weight);
                if (cityState) {
                    if (selectedState) {
                        const selectedProvince = selectedState;
                        const provinceData = cityState.find(city => city.city === selectedProvince);

                        let cart = localStorage.getItem('cart');

                        if (cart) {
                            cart = JSON.parse(cart); // Chuyển chuỗi JSON thành object
                            let totalWeight = 0;
                            let totalQty = 0;

                            // Lọc chỉ các item có select là true
                            const selectedItems = cart.filter(item => item.select === true);

                            // Lặp qua các item đã được chọn
                            selectedItems.forEach(item => {
                                const weight = parseFloat(item.weight) || 0;
                                const qty = parseInt(item.qty) || 0;

                                totalWeight += weight * qty;

                                totalQty += qty;
                            });
                            if (totalWeight <= minimum_volume){
                                totalWeight = minimum_volume;
                            }else {
                                totalWeight = roundUp(totalWeight);
                            }
                            console.log('total Weight ' +  totalWeight);
                            console.log('minimum volume ' + minimum_volume);
                            if (provinceData) {
                                // Nếu chọn tỉnh/thành phố, chỉ tính phí ship của tỉnh
                                shippingFee = totalWeight * parseFloat(provinceData.shipping_state || 0);
                            } else {
                                // Nếu không chọn tỉnh/thành phố, tính phí ship quốc gia
                                shippingFee = totalWeight * parseFloat(countryData.shipping || 0);
                            }

                            // Hiển thị phí vận chuyển
                            shippingFeeElement.text(`$${shippingFee.toFixed(2)}`);
                            shippingFeeElement.attr('data-shipping', shippingFee);

                            // Cập nhật tổng giá trị
                            updateTotal(shippingFee);

                            console.log(`Province: ${selectedProvince}, Shipping Fee: $${shippingFee}`);
                            console.log('Selected Items:', selectedItems);
                            console.log('Total Weight:', totalWeight);
                            console.log('Total Quantity:', totalQty);
                        } else {
                            console.error('Cart không tồn tại trong localStorage');
                            shippingFeeElement.text('$0.00');
                            shippingFeeElement.attr('data-shipping', 0);
                        }
                           }else {
                        $('.input-field[name="province"]').off('change').on('change', function () {
                        const selectedProvince = $(this).val();
                        const provinceData = cityState.find(city => city.city === selectedProvince);

                        let cart = localStorage.getItem('cart');

                        if (cart) {
                            cart = JSON.parse(cart); // Chuyển chuỗi JSON thành object
                            let totalWeight = 0;
                            let totalQty = 0;

                            // Lọc chỉ các item có select là true
                            const selectedItems = cart.filter(item => item.select === true);

                            // Lặp qua các item đã được chọn
                            selectedItems.forEach(item => {
                                const weight = parseFloat(item.weight) || 0;
                                const qty = parseInt(item.qty) || 0;

                                totalWeight += weight * qty;
                                totalQty += qty;
                            });
                            if (totalWeight <= minimum_volume){
                                totalWeight = minimum_volume;
                            }else {
                                totalWeight = roundUp(totalWeight);
                            }
                            console.log('total Weight ' + totalWeight);
                            console.log('minimum volume ' + minimum_volume);
                            if (provinceData) {
                                // Nếu chọn tỉnh/thành phố, chỉ tính phí ship của tỉnh
                                shippingFee = totalWeight * parseFloat(provinceData.shipping_state || 0);
                            } else {
                                // Nếu không chọn tỉnh/thành phố, tính phí ship quốc gia
                                shippingFee = totalWeight * parseFloat(countryData.shipping || 0);
                            }

                            // Hiển thị phí vận chuyển
                            shippingFeeElement.text(`$${shippingFee.toFixed(2)}`);
                            shippingFeeElement.attr('data-shipping', shippingFee);

                            // Cập nhật tổng giá trị
                            updateTotal(shippingFee);

                            console.log(`Province: ${selectedProvince}, Shipping Fee: $${shippingFee}`);
                            console.log('Selected Items:', selectedItems);
                            console.log('Total Weight:', totalWeight);
                            console.log('Total Quantity:', totalQty);
                        } else {
                            console.error('Cart không tồn tại trong localStorage');
                            shippingFeeElement.text('$0.00');
                            shippingFeeElement.attr('data-shipping', 0);
                        }
                    });
                           }
                  
                }
                else {
                    $('.input-field[name="province"]').off('change').on('change', function () {
                        const selectedProvince = $(this).val();
                        const provinceData = cityState.find(city => city.city === selectedProvince);


                        let cart = localStorage.getItem('cart');

                        if (cart) {
                            cart = JSON.parse(cart); // Chuyển chuỗi JSON thành object
                            let totalWeight = 0;
                            let totalQty = 0;

                            // Lọc chỉ các item có select là true
                            const selectedItems = cart.filter(item => item.select === true);

                            // Lặp qua các item đã được chọn
                            selectedItems.forEach(item => {
                                const weight = parseFloat(item.weight) || 0;
                                const qty = parseInt(item.qty) || 0;

                                totalWeight += weight * qty;
                                totalQty += qty;
                            });
                            if (totalWeight <= minimum_volume){
                                totalWeight = minimum_volume;
                            }else {
                                totalWeight = roundUp(totalWeight);
                            }
                            if (provinceData) {
                                // Nếu chọn tỉnh/thành phố, chỉ tính phí ship của tỉnh
                                shippingFee = totalWeight * parseFloat(provinceData.shipping_state || 0);
                            } else {
                                // Nếu không chọn tỉnh/thành phố, tính phí ship quốc gia
                                shippingFee = totalWeight * parseFloat(countryData.shipping || 0);
                            }

                            // Hiển thị phí vận chuyển
                            shippingFeeElement.text(`$${shippingFee.toFixed(2)}`);
                            shippingFeeElement.attr('data-shipping', shippingFee);

                            // Cập nhật tổng giá trị
                            updateTotal(shippingFee);

                            console.log(`Province: ${selectedProvince}, Shipping Fee: $${shippingFee}`);
                            console.log('Selected Items:', selectedItems);
                            console.log('Total Weight:', totalWeight);
                            console.log('Total Quantity:', totalQty);
                        } else {
                            console.error('Cart không tồn tại trong localStorage');
                            shippingFeeElement.text('$0.00');
                            shippingFeeElement.attr('data-shipping', 0);
                        }
                    });
                }
                if (cart) {
                    cart = JSON.parse(cart); // Chuyển chuỗi JSON thành object

                    if (Array.isArray(cart)) {
                        let totalWeight = 0;
                        let totalQty = 0;

                        // Lọc chỉ các item có select là true
                        const selectedItems = cart.filter(item => item.select === true);

                        // Lặp qua các item đã được chọn
                        selectedItems.forEach(item => {
                            const weight = parseFloat(item.weight) || 0;
                            const qty = parseInt(item.qty) || 0;

                            totalWeight += weight * qty;
                            totalQty += qty;
                            console.log(qty,weight);
                        });
                        if (totalWeight <= minimum_volume){
                            totalWeight = minimum_volume;
                        }else {
                            totalWeight = roundUp(totalWeight);
                        }
                        // Tính phí vận chuyển cơ bản
                        shippingFee = totalWeight * parseFloat(countryData.shipping || 0);

                        shippingFeeElement.text(`$${shippingFee.toFixed(2)}`);
                        shippingFeeElement.attr('data-shipping', shippingFee);
                        updateTotal(shippingFee);
                    } else {
                        console.error('Cart không phải là danh sách các item!');
                        shippingFeeElement.text('$0.00');
                        shippingFeeElement.attr('data-shipping', 0);
                    }
                }
                else {
                    console.error('Cart không tồn tại trong localStorage');
                    shippingFeeElement.text('$0.00');
                    shippingFeeElement.attr('data-shipping', 0);
                }

                $('.noVietnam').css('display', 'none');
                $('.Vietnam').css('display', 'block');
            }

            // Cập nhật tổng tiền
            updateTotal(shippingFee);
        } else {
            // Nếu không tìm thấy quốc gia
            $('.Vietnam, .noVietnam').css('display', 'none');
            alert('<?php pll_e('Country not supported for shipping!') ?>');
        }
    }

    // Hàm cập nhật tổng tiền
    function updateTotal(shippingFee) {
        const totalElement = $('.total-true');
        const discountElement = $('.discount-fee');

        // Lấy tổng gốc
        const baseTotal = parseFloat(totalElement.data('total')) || 0;

        // Lấy giá trị giảm giá (nếu có)
        const discountAmount = parseFloat(discountElement.data('discount')) || 0;

        // Tính tổng tiền mới sau khi trừ giảm giá và thêm phí vận chuyển
        const newTotal = baseTotal - discountAmount + shippingFee;

        // Cập nhật giá trị và hiển thị
        totalElement.attr('data-total', newTotal);
        total = newTotal; // Cập nhật giá trị global nếu cần
        totalElement.text(`$${newTotal.toFixed(2)}`);
    }


    // Bắt sự kiện thay đổi giá trị ô select "country"
        $('.input-field[name="country"]').on('change', function () {
            const selectedCountry = $(this).val();
            calculateShipping(selectedCountry);
        });

        // Khởi tạo phí vận chuyển khi tải trang
        // const initialCountry = $('.input-field[name="country"]').val();
        // calculateShipping(initialCountry);
    // });
  $('input[name="payment"]').on('change', function () {
    const selectedValue = $(this).val();
    const country = $(this).data('country');
    const state = $(this).data('state');
   
    console.log('Selected payment value:', selectedValue);
    if (selectedValue == 1) {
        $('#submit-check-out').css('display', 'none');
         $('#submit-check-out-info').css('display', 'block');
         calculateShipping(country,state)
    }else {
         $('#submit-check-out').css('display', 'block');
         $('#submit-check-out-info').css('display', 'none');
           const initialCountry = $('.input-field[name="country"]').val();
        calculateShipping(initialCountry);
    }
});

     function ConfirmOrderInfomation() {
        const radio = $('input[name="payment"][value="1"]'); // Chọn radio có value="1"
  const dataAttributes = radio.data(); // Lấy toàn bộ `data-` thuộc tính
if (radio.length > 0) {
  var fullName = dataAttributes.fullname;
        var email = dataAttributes.email;
        var phone_numer = dataAttributes.phone_number;
        var country = dataAttributes.country;
        var province = dataAttributes.state;
        var address1 = dataAttributes.address1_shipper;
        var address2 = dataAttributes.address2_shipper;
        var voucher_code = $('input[name="voucher_code"]').val();
        var city = dataAttributes.city;
        var state = dataAttributes.state;
        var postaCode = dataAttributes.zipcodeShipper;
        var zipCode = dataAttributes.zipcodeShipper;
        var regexEmail = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
        var totalPrice = total;
        var price = $('.total-sub').data('true');
        var discount = $('.discount-fee').data('discount');
        var shippingFee = $('.shipping-fee').data('shipping');
        // const saveInfo = $('input[name="saveInformation"]').is(':checked') ? $('input[name="saveInformation"]').val() : 0;

}
        console.log(dataAttributes);
        // if (saveInfo == 1) {
             var user_po = {
            fullName,
            country,
            province,
            email,
            address1,
            phone_numer,
            city,
            state,
            zipCode
        }
        localStorage.setItem("userBuy", JSON.stringify(user_po));
        // }
       

        var dataProduct = JSON.parse(localStorage.getItem("cart"));
        if (!Array.isArray(dataProduct)) {
            Swal.fire({
                icon: 'error',
                text: 'Cart data is invalid or empty.',
            });
            return false;
        }

        var selectedItems = [];
        dataProduct.forEach(function(item) {
            if (item.select === true) {
                selectedItems.push(item);
            }
        });

// In ra selectedItems dưới dạng mảng
        console.log(selectedItems);

        var form = $('#check-out')[0];
        if (!form) {
            Swal.fire({
                icon: 'error',
                text: 'Form not found.'
            });
            return false;
        }
        var jsonData = JSON.stringify(selectedItems);

        var formData = new FormData(form);
        formData.append('fullName', fullName);
        formData.append('email', email);
        formData.append('phone_numer', phone_numer);
        formData.append('country', country);
        formData.append('price', price);
        formData.append('address1', address1);
        formData.append('voucher_code', voucher_code);
        formData.append('address2', address2);
        formData.append('dataProduct', jsonData); // Ensure it is passed as a string
        formData.append('action', 'submitOrder');
        formData.append('city', city);
        // formData.append('saveInfo', saveInfo);
        formData.append('discount', discount);
        formData.append('totalPrice', totalPrice);
        formData.append('state', state);
        formData.append('province', province);
        formData.append('postaCode', postaCode);
        formData.append('zipCode', zipCode);
        formData.append('shippingFee', shippingFee);

        $.ajax({
            url: "<?= admin_url('admin-ajax.php'); ?>",
            type: 'POST',
            processData: false,
            contentType: false,
            data: formData,
            dataType: 'json',
            beforeSend: function () {
                Swal.fire({
                    title: 'Processing',
                    html: '<?php pll_e('Please wait...') ?>',
                    didOpen: () => {
                        Swal.showLoading()
                    }
                });
            },
            success: function (response) {
                if (response.status === 1) {
                    Swal.fire({
                        icon: 'success',
                        text: response.mess,
                    }).then(() => {
                        window.location.href = response.url;

                    });
                    window.location.href = response.url;
                    localStorage.removeItem("cart");

                } else {
                    Swal.fire({
                        icon: 'warning',
                        text: response.mess,
                    });
                }
            },
            error: function (xhr) {
                Swal.fire({
                    icon: 'error',
                    text: '<?php pll_e('An error occurred. Please try again.') ?>'
                });
            }
        });
    }

    function ConfirmOrder() {

        var fullName = $('input[name="fullName"]').val();
        var email = $('input[name="email"]').val();
        var phone_numer = $('input[name="phone_numer"]').val();
        var country = $('select[name="country"]').val();
        var province = $('select[name="province"]').val();
        var address1 = $('input[name="address1"]').val();
        var address2 = $('input[name="address2"]').val();
        var voucher_code = $('input[name="voucher_code"]').val();
        var city = $('input[name="city"]').val();
        var state = $('select[name="state"]').val();
        var postaCode = $('input[name="postaCode"]').val();
        var zipCode = $('input[name="zipCode"]').val();
        var regexEmail = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
        var totalPrice = total;
        var price = $('.total-sub').data('true');
        var discount = $('.discount-fee').data('discount');
        var shippingFee = $('.shipping-fee').data('shipping');
        const saveInfo = $('input[name="saveInformation"]').is(':checked') ? $('input[name="saveInformation"]').val() : 0;

        if (fullName == '') {
            Swal.fire({
                icon: 'error',
                text: '<?php pll_e('Please enter your full name') ?>'
            });
            return false;
        }
        if (phone_numer == '') {
            Swal.fire({
                icon: 'error',
                text: '<?php pll_e('Please enter your phone numer') ?>'
            });
            return false;
        }
        if (country == '') {
            Swal.fire({
                icon: 'error',
                text: '<?php pll_e('Please enter your country') ?>'
            });
            return false;
        }
        if (email == '') {
            Swal.fire({
                icon: 'error',
                text: '<?php pll_e('Please enter your email') ?>'
            });
            return false;
        }
        if (!regexEmail.test(email)) {
            Swal.fire({
                icon: 'error',
                text: '<?php pll_e('Invalid email address') ?>'
            });
            return false;
        }
        if (address1 == '') {
            Swal.fire({
                icon: 'error',
                text: '<?php pll_e('Please enter your address') ?>'
            });
            return false;
        }
        if (city == '') {
            Swal.fire({
                icon: 'error',
                text: '<?php pll_e('Please enter your city') ?>'
            });
            return false;
        }
        if (country != 'United States') {
            if (postaCode == '') {
                Swal.fire({
                    icon: 'error',
                    text: '<?php pll_e('Please enter your Postal code') ?>'
                });
                return false;
            }
            if (province == '') {
                Swal.fire({
                    icon: 'error',
                    text: '<?php pll_e('Please enter your province') ?>'
                });
                return false;
            }
        }
        else {
            if (state == '') {
                Swal.fire({
                    icon: 'error',
                    text: '<?php pll_e('Please enter your state or province') ?>'
                });
                return false;
            }
            if (zipCode == '') {
                Swal.fire({
                    icon: 'error',
                    text: '<?php pll_e('Please enter your postal code') ?>'
                });
                return false;
            }
        }
        if (saveInfo == 1) {
             var user_po = {
            fullName,
            country,
            province,
            email,
            address1,
            phone_numer,
            city,
            state,
            zipCode
        }
        localStorage.setItem("userBuy", JSON.stringify(user_po));
        }
       

        var dataProduct = JSON.parse(localStorage.getItem("cart"));
        if (!Array.isArray(dataProduct)) {
            Swal.fire({
                icon: 'error',
                text: 'Cart data is invalid or empty.',
            });
            return false;
        }

        var selectedItems = [];
        dataProduct.forEach(function(item) {
            if (item.select === true) {
                selectedItems.push(item);
            }
        });

// In ra selectedItems dưới dạng mảng
        console.log(selectedItems);

        var form = $('#check-out')[0];
        if (!form) {
            Swal.fire({
                icon: 'error',
                text: 'Form not found.'
            });
            return false;
        }
        var jsonData = JSON.stringify(selectedItems);

        var formData = new FormData(form);
        formData.append('fullName', fullName);
        formData.append('email', email);
        formData.append('phone_numer', phone_numer);
        formData.append('country', country);
        formData.append('price', price);
        formData.append('address1', address1);
        formData.append('voucher_code', voucher_code);
        formData.append('address2', address2);
        formData.append('dataProduct', jsonData); // Ensure it is passed as a string
        formData.append('action', 'submitOrder');
        formData.append('city', city);
        formData.append('saveInfo', saveInfo);
        formData.append('discount', discount);
        formData.append('totalPrice', totalPrice);
        formData.append('state', state);
        formData.append('province', province);
        formData.append('postaCode', postaCode);
        formData.append('zipCode', zipCode);
        formData.append('shippingFee', shippingFee);

        $.ajax({
            url: "<?= admin_url('admin-ajax.php'); ?>",
            type: 'POST',
            processData: false,
            contentType: false,
            data: formData,
            dataType: 'json',
            beforeSend: function () {
                Swal.fire({
                    title: 'Processing',
                    html: '<?php pll_e('Please wait...') ?>',
                    didOpen: () => {
                        Swal.showLoading()
                    }
                });
            },
            success: function (response) {
                if (response.status === 1) {
                    Swal.fire({
                        icon: 'success',
                        text: response.mess,
                    }).then(() => {
                        window.location.href = response.url;

                    });
                    window.location.href = response.url;
                    localStorage.removeItem("cart");

                } else {
                    Swal.fire({
                        icon: 'warning',
                        text: response.mess,
                    });
                }
            },
            error: function (xhr) {
                Swal.fire({
                    icon: 'error',
                    text: '<?php pll_e('An error occurred. Please try again.') ?>'
                });
            }
        });
    }


    showCartSelect();
    const radios = document.querySelectorAll('input[name="payment"]');
    const viewDeliver = document.querySelector(".view-delivery-info");


    radios.forEach((radio) => {
        radio.addEventListener("change", () => {
            if (radio.classList.contains("select-delivery") && radio.checked) {
                // Hiển thị view-offer
                viewDeliver.classList.remove("absolute", "invisible", "opacity-0", "max-h-0");
                viewDeliver.classList.add("active", "opacity-100", "max-h-[1000px]");
            } else {
                // Thu gọn max-height về 0 trước
                viewDeliver.classList.add("max-h-0", "opacity-0");
                viewDeliver.classList.remove("max-h-[500px]", "opacity-100");

                // Sau 500ms (đúng với thời gian transition của max-height), thêm absolute
                setTimeout(() => {
                    if (viewDeliver.classList.contains("max-h-0")) {
                        viewDeliver.classList.add("absolute", "invisible");
                        viewDeliver.classList.remove("active");
                    }
                }, 500); // 500ms trùng với transition duration
            }
        });
    });
</script>
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