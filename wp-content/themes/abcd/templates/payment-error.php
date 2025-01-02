<?php /* Template Name: Payment-Error */ ?>
<?php
session_start();
global $wpdb;

get_header();
$code = $_SESSION['order_code'];

$get_newest_order = $wpdb->get_results("SELECT * FROM wp_orders WHERE order_code = '{$code}' ORDER BY id DESC");
$dataProduct = $get_newest_order[0]->dataproduct;
$decodedData = json_decode(str_replace('\\', "", $dataProduct), true);
$jsonid = str_replace('\\', "", $dataProduct);
$data = json_decode($jsonid, true);
// Tạo mảng mới chỉ chứa các 'id'
$ids = [];
foreach ($data as $item) {
    $ids[] = (int) $item['id']; // Chuyển id thành kiểu số nguyên (int)
}

// Chuyển mảng ids thành JSON
$jsonIds = json_encode($ids);

// In ra mảng JSON



$time_order_uat = $get_newest_order[0]->time_order;
date_default_timezone_set('America/New_York');
$formattedDateEastern = date('m/d/Y', $time_order_uat);

$totalRecords = count($decodedData);

//Tên người dùng
$name_info = $get_newest_order[0]->name_user;
$phone = $get_newest_order[0]->phoneNumber;
$address1 = $get_newest_order[0]->address1;
$city = $get_newest_order[0]->city;
$country = $get_newest_order[0]->country;
$state = $get_newest_order[0]->state;
$ZIPCode = $get_newest_order[0]->ZIPCode;
$price_payment = $get_newest_order[0]->price_payment;
$status = $get_newest_order[0]->status;

    $wpdb->update(
        'wp_orders',
        array('status' => 4),
        array('order_code' => $code)
    );

$class = "";
$name = "";
if ($status == 1) {
    $class = 'warning';
    $name = pll__('Processing');
} elseif ($status == 2) {
    $class = 'success';
    $name = pll__('Completed');
} elseif ($status == 4) {
    $class = 'error';
    $name = pll__('Canceled');
} else {
    $class = 'warning';
    $name = pll__('In progress');
}
// Kiểm tra kết quả
$url = get_template_directory_uri();
?>
<main class="bg-[#EEF0F6]">
    <section class="py-6">
        <div class="container">
            <a href="<?= home_url() ?>/cart" class="text-body-md-medium text-secondary flex gap-3">
                <figure><img src="<?php echo get_template_directory_uri(); ?>/assets/image/icon/arrow-left-20-second.svg" alt="icon"></figure>
                <?php pll_e('Back to cart') ?>
            </a>
        </div>
    </section>

    <section class="pb-20">
        <div class="container">
            <div class="w-full flex flex-col gap-6">
                <div class="w-full flex flex-col items-center justify-center gap-10 p-6 lg:p-10 bg-white rounded-xl">
<!--                    <figure class="max-w-[220px]"><img src="--><?php //echo get_template_directory_uri(); ?><!--/assets/image/payment-success-illus.svg" alt="icon">-->
<!--                    </figure>-->
                    <div class="w-full max-w-[884px] flex flex-col gap-4 items-center">
                        <h2 class="text-heading-h4 text-[#191c1f] text-center">Your order has failed to be paid, please check again
                        !</h2>
                        <p class="text-body-md-regular text-gray-7 text-justify lg:text-center">Your order has been sent to SureMeal's order processing system. During the processing, we will contact you if we need more information from you. In addition, SureMeal will also send an order confirmation via Email.
                        </p>
                    </div>
                    <a href="<?= home_url() ?>/product" class="button bg-primary text-body-md-semibold text-white">View other products</a>
                </div>

                <div class="w-full flex flex-col items-center justify-center bg-white rounded-xl">
                    <div
                        class="w-full flex flex-wrap items-center justify-between gap-4 px-8 py-6 border-b border-neutral-200 border-solid">
                        <div class="flex flex-wrap items-center gap-4">
                            <p class="text-body-md-medium text-gray-8"><?= $formattedDateEastern ?></p>

                            <div class="flex items-center gap-4">
                                <div class="w-1 h-1 rounded-full bg-[#D9D9D9]"></div>
                                <div class="flex items-center gap-2">
                                    <p class="text-body-md-medium text-gray-8"><?php pll_e('Order code') ?>:</p>
                                    <p class="text-body-md-medium text-neutral-500">#<?= $code ?></p>
                                    <input type="text" value="<?= $code ?>" id="myInput<?= $code ?>" style="display: none">
                                    <button class="button button-trans p-0" onclick="myFunction('myInput<?= $code ?>')">
                                        <figure class="w-5 h-5"><img src="<?= $url ?>/assets/image/icon/duplicate.svg"
                                                                     alt="icon">
                                        </figure>
                                    </button>
                                </div>
                            </div>
                            <div class="flex items-center gap-4">
                                <div class="w-1 h-1 rounded-full bg-[#D9D9D9]"></div>
                                <p class="text-body-md-medium text-neutral-500"><?php pll_e('Deliver') ?></p>
                            </div>

                            <div class="flex items-center gap-4">
                                <div class="w-1 h-1 rounded-full bg-[#D9D9D9]"></div>
                                <p class="text-body-md-medium text-neutral-500"><?= $totalRecords ?> <?php pll_e('products') ?></p>
                            </div>
                            <div class="flex items-center gap-4">
                                <div class="w-1 h-1 rounded-full bg-[#D9D9D9]"></div>
                                <div class="flex items-center gap-2">
                                    <a href="/pdf/?order_code=<?= $code ?>" target="_blank" class="text-body-md-medium text-neutral-500"><?php pll_e('Receipt') ?></a>
                                    <button class="button bg-trans" onclick="window.open('/pdf/?order_code=' . <?= $code ?>, '_blank')">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="20"
                                             viewBox="0 0 16 20" fill="none">
                                            <path
                                                d="M9.75 1.24258V4.96007C9.75 5.46411 9.75 5.71614 9.84537 5.90866C9.92926 6.078 10.0631 6.21568 10.2278 6.30197C10.4149 6.40006 10.66 6.40006 11.15 6.40006H14.7642M11.5 10.9H4.5M11.5 14.5H4.5M6.25 7.3H4.5M9.75 1H5.2C3.72986 1 2.99479 1 2.43327 1.29428C1.93935 1.55314 1.53778 1.96619 1.28611 2.47423C1 3.05179 1 3.80786 1 5.32V14.68C1 16.1921 1 16.9482 1.28611 17.5258C1.53778 18.0338 1.93935 18.4469 2.43327 18.7057C2.99479 19 3.72986 19 5.2 19H10.8C12.2701 19 13.0052 19 13.5667 18.7057C14.0607 18.4469 14.4622 18.0338 14.7139 17.5258C15 16.9482 15 16.1921 15 14.68V6.4L9.75 1Z"
                                                stroke="#475467" stroke-width="1.5" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="badge error">
                            Canceled
                        </div>
                    </div>


                    <div class="w-full py-6 px-8 flex flex-col gap-3 border-b border-solid border-neutral-200">
                        <!-- product -->
                        <?php
                        foreach ($decodedData as $product) :
                            $productTitle = $product['title'];
                            $productPrice = (int)$product['price'];
                            $productQuantity = (int)$product['qty'];
                            $totalProductPrice = $productPrice * $productQuantity;
                            $id_product = $product['id'];
                            $qty = get_field('quantity', $id_product);
                            // Cộng dồn tổng tiền
                            $totalPrice += $totalProductPrice;
                            ?>
                            <div
                                class="w-full flex flex-wrap md:flex-row gap-6 lg:gap-0 justify-between items-end md:items-center">
                                <div class="flex lg:flex-0 w-full md:w-2/3 max-w-[454px] items-center gap-5">
                                    <figure
                                        class="w-[60px] h-[60px] md:w-[100px] md:h-[100px] rounded-xl border border-solid border-neutral-200">
                                        <img src="<?= $product['img'] ?>" alt="item">
                                    </figure>
                                    <div class="flex-1 flex flex-col gap-2">
                                        <h2 class="text-body-md-medium text-gray-8 truncate-2row"><?= $productTitle ?></h2>
                                        <div class="neutral-200 text-body-sm-regular text-gray-7">
                                            <?php pll_e('Type') ?>: <?= $qty ?> <?php pll_e('Pack') ?>
                                        </div>
                                    </div>
                                </div>
                                <p class="text-body-md-regular text-gray-8"><?php pll_e('Quantity') ?>: <?= $productQuantity ?></p>
                                <div class="flex flex-col items-end justify-end gap-1">
                                    <p class="text-body-md-medium text-gray-8">$<?= number_format($totalProductPrice, 2, '.', ','); ?> </p>
                                    <p class="line-through text-body-sm-regular text-neutral-500">$<?= number_format($totalProductPrice, 2, '.', ','); ?> </p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <!-- total -->
                        <div class="flex items-center justify-between">
                            <a href="<?= home_url() ?>/order-detail/?order_code=<?= $code ?>" class="button button-trans p-0 text-body-md-semibold text-secondary">
                                <?php pll_e('View details') ?>
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <g id="Outline / Arrows / Alt Arrow Right">
                                        <path id="Vector (Stroke)" fill-rule="evenodd" clip-rule="evenodd" d="M8.51192 4.43057C8.82641 4.161 9.29989 4.19743 9.56946 4.51192L15.5695 11.5119C15.8102 11.7928 15.8102 12.2072 15.5695 12.4881L9.56946 19.4881C9.29989 19.8026 8.82641 19.839 8.51192 19.5695C8.19743 19.2999 8.161 18.8264 8.43057 18.5119L14.0122 12L8.43057 5.48811C8.161 5.17361 8.19743 4.70014 8.51192 4.43057Z" fill="#0E74BC" />
                                    </g>
                                </svg>

                            </a>

                            <div class="flex items-center gap-2">
                                <p class="text-body-md-regular text-neutral-500"><?php pll_e('Total') ?>:</p>
                                <p class="text-heading-h6 font-medium text-primary">$<?= number_format($price_payment, 2, '.', ','); ?> </p>
                            </div>
                        </div>
                    </div>

                    <div class="w-full flex flex-col lg:flex-row gap-6 py-4 px-8">
                        <div class="w-full flex flex-col gap-4">
                            <div class="flex items-center gap-2">
                                <figure class="w-6 h-6"><img src="<?= $url ?>/assets/image/icon/user.svg" alt="icon">
                                </figure>
                                <p class="text-body-md-medium text-gray-8"><?php pll_e('Recipient information') ?></p>
                            </div>
                            <div class="min-h-[76px] flex flex-col gap-2">
                                <h2 class="text-body-md-medium text-gray-8"><?= $name_info ?></h2>
                                <p class="text-body-sm-regular text-neutral-500">
                                    <?php pll_e('Address') ?>: <?= $address1 ?>, <?= $city ?>,<?= $state ?>,<?= $country ?>
                                    <br>
                                    <?php pll_e('Phone') ?>: <?= $phone ?>
                                </p>
                            </div>
                        </div>
                        <div class=" w-full h-[1px] lg:w-[1px] lg:h-[68px] bg-neutral-300"></div>
                        <div class="w-full flex flex-col gap-4">
<!--                            <div class="flex items-center gap-2">-->
<!--                                <figure class="w-6 h-6"><img src="--><?//= $url ?><!--/assets/image/icon/card.svg" alt="icon">-->
<!--                                </figure>-->
<!--                                <p class="text-body-md-medium text-gray-8">--><?php //pll_e('Payment method') ?><!--</p>-->
<!--                            </div>-->
<!--                            <div class="flex items-center gap-3">-->
<!--                                <figure class="w-[46px] h-[32px]"><img src="--><?//= $url ?><!--/assets/image/badge-visa.svg"-->
<!--                                                                       alt="visa"></figure>-->
<!--                                <h2 class="text-body-sm-medium text-gray-9"> ***4242 | Expires 12/34</h2>-->
<!--                            </div>-->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
<?php get_footer() ?>
<!-- script offer js -->
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