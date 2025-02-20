<?php /* Template Name: Order-detail */ ?>
<?php
$url = get_template_directory_uri();
get_header();

global $wpdb;
$code = $_GET['order_code'];

$get_newest_order = $wpdb->get_results("SELECT * FROM wp_orders WHERE order_code = '{$code}' ORDER BY id DESC");
$dataProduct = $get_newest_order[0]->dataproduct;
$decodedData = json_decode(str_replace('\\', "", $dataProduct), true);
$countProduct = count($decodedData);
$time_order_uat = $get_newest_order[0]->time_order;
date_default_timezone_set('America/New_York');
$formattedDateEastern = date('m/d/Y', $time_order_uat);

$totalRecords = count($decodedData);
$url = get_template_directory_uri();
// Tên người dùng
$name_info = $get_newest_order[0]->name_user;
$transport_fee = $get_newest_order[0]->transport_fee;
$discount_price = $get_newest_order[0]->discount_price;
$email = $get_newest_order[0]->email;
$country = $get_newest_order[0]->country;
$address1 = $get_newest_order[0]->address1;
$city = $get_newest_order[0]->city;
$ZIPCode = $get_newest_order[0]->ZIPCode;
$ship = $get_newest_order[0]->transport_fee;
$phoneNumber = $get_newest_order[0]->phoneNumber;
$price_payment = $get_newest_order[0]->price_payment;
$status = $get_newest_order[0]->status;
$price = $get_newest_order[0]->price;
$status_transport = $get_newest_order[0]->status_transport;


$class = "";
$name = "";
if ($status == 1) {
    $class = 'warning';
    $name = 'Processing';
} elseif ($status == 2) {
    $class = 'success';
    $name = 'Completed';
} elseif ($status == 4) {
    $class = 'error';
    $name = 'Canceled';
} else {
    $class = 'warning';
    $name = 'In progress';
}
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
                            <span itemprop="name">Home</span>
                        </a>
                        <meta itemprop="position" content="1" />
                    </li>
                    <span>/</span>
                    <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                        <a href="<?= home_url() ?>/person-info/" class="text-secondary hover:text-primary" itemprop="item">
                            <span itemprop="name">Personal</span>
                        </a>
                        <meta itemprop="position" content="1" />
                    </li>
                    <span>/</span>
                    <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                        <a href="<?= home_url() ?>/order-info/" class="text-secondary hover:text-primary" itemprop="item">
                            <span itemprop="name">Order information</span>
                        </a>
                        <meta itemprop="position" content="1" />
                    </li>
                    <span>/</span>
                    <!-- <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                            <a href="#" class="text-secondary hover:text-primary" itemprop="item">
                                <span itemprop="name">Blog</span>
                            </a>
                            <meta itemprop="position" content="1" />
                        </li>
                        <span>/</span> -->
                    <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem"
                        aria-current="page">
                        <span itemprop="name">Order details</span>
                        <meta itemprop="position" content="4" />
                    </li>
                </ol>
            </nav>
        </div>
    </section>

    <section class="pb-20">
        <div class="container">
            <div class="flex flex-col lg:flex-row gap-6">
                <div class="w-full lg:w-[67%] lg:max-w-[900px]">
                    <div class="w-full flex flex-col gap-6">
                        <div class="w-full flex flex-col items-center justify-center bg-white rounded-xl">
                            <div
                                class="w-full flex flex-wrap items-center justify-between gap-4 px-8 py-6 border-b border-neutral-200 border-solid">
                                <div class="flex flex-wrap items-center gap-4">
                                    <p class="text-body-md-medium text-gray-8"><?= $formattedDateEastern ?></p>

                                    <div class="flex items-center gap-4">
                                        <div class="w-1 h-1 rounded-full bg-[#D9D9D9]"></div>
                                        <div class="flex items-center gap-2">
                                            <p class="text-body-md-medium text-gray-8">Order code:</p>
                                            <p class="text-body-md-medium text-neutral-500 order_code"><?= $code ?></p>
                                            <input type="text" value="<?= $code ?>" id="myInput<?= $code ?>" style="display: none">
                                            <img class="w-5 h-auto ml-2" style="cursor: pointer;" onclick="myFunction('myInput_completed<?= $code ?>')" src="<?= $url ?>/dist/img/copy.svg" alt="">
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-4">
                                        <div class="w-1 h-1 rounded-full bg-[#D9D9D9]"></div>
                                        <p class="text-body-md-medium text-neutral-500">Deliver</p>
                                    </div>

                                    <div class="flex items-center gap-4">
                                        <div class="w-1 h-1 rounded-full bg-[#D9D9D9]"></div>
                                        <p class="text-body-md-medium text-neutral-500"><?= $countProduct ?> products</p>
                                    </div>
                                    <div class="flex items-center gap-4">
                                        <div class="w-1 h-1 rounded-full bg-[#D9D9D9]"></div>
                                        <div class="flex items-center gap-2">
                                            <a href="/pdf/?order_code=<?= $code ?>" target="_blank" class="text-body-md-medium text-neutral-500">Receipt</a>
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

                                <div class="badge <?= $class ?>">
                                    <?= $name ?>
                                </div>
                            </div>

                            <div
                                class="min-h-[120px] overflow-x-auto 3xl:overflow-x-hidden custom-scrollbar w-full py-6 flex flex-col gap-3 border-b border-solid border-neutral-200">
                                <!-- progress bar -->
                                <div class="progress-order">
                                    <div class="progress-order-bar">
                                        <!-- thêm active khi đã xong tiến trình -->
                                        <div class="progress-order-item <?php if ($status_transport >= 1): ?> active<?php endif; ?>">
                                            <div class="dot"></div>
                                            <div class="content">
                                                <figure class="w-8 h-8"><img
                                                        src="<?= $url ?>/assets/image/order/note-book-primary.svg"
                                                        alt="icon">
                                                </figure>
                                                <p>Order Placed</p>
                                            </div>
                                        </div>
                                        <!-- thêm active khi đã xong tiến trình -->
                                        <div class="progress-order-divider first <?php if ($status_transport >= 2): ?> active <?php endif; ?>"></div>
                                        <div class="progress-order-item <?php if ($status_transport > 2): ?> active<?php elseif ($status_transport == 2):  ?>processing  <?php endif; ?>">
                                            <div class="dot"></div>
                                            <div class="content">
                                                <figure class="w-8 h-8"><img
                                                        src="<?= $url ?>/assets/image/order/pakage.svg" alt="icon">
                                                </figure>
                                                <p>Packaging</p>
                                            </div>
                                        </div>
                                        <!-- thêm active khi đã xong tiến trình -->
                                        <div class="progress-order-divider second <?php if ($status_transport >= 3): ?> active <?php endif; ?>"></div>
                                        <div class="progress-order-item  <?php if ($status_transport > 3): ?> active<?php elseif ($status_transport == 3):  ?>processing  <?php endif; ?>">
                                            <div class="dot"></div>
                                            <div class="content">
                                                <figure class="w-8 h-8"><img src="<?= $url ?>/assets/image/order/truck.svg"
                                                        alt="icon">
                                                </figure>
                                                <p>On The Road</p>
                                            </div>
                                        </div>
                                        <!-- thêm active khi đã xong tiến trình -->
                                        <div class="progress-order-divider third <?php if ($status_transport >= 4): ?> active <?php endif; ?>"></div>
                                        <div class="progress-order-item <?php if ($status_transport >= 4): ?> active<?php endif; ?>">
                                            <div class="dot"></div>
                                            <div class="content">
                                                <figure class="w-8 h-8"><img
                                                        src="<?= $url ?>/assets/image/order/deliver.svg" alt="icon">
                                                </figure>
                                                <p>Delivered</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="w-full flex flex-col lg:flex-row gap-6 py-4 px-8">
                                <div class="w-full flex flex-col gap-4">
                                    <div class="flex items-center gap-2">
                                        <figure class="w-6 h-6"><img src="<?= $url ?>/assets/image/icon/user.svg"
                                                alt="icon">
                                        </figure>
                                        <p class="text-body-md-medium text-gray-8">Recipient information</p>
                                    </div>
                                    <div class="min-h-[76px] flex flex-col gap-2">
                                        <h2 class="text-body-md-medium text-gray-8"><?= $name_info ?></h2>
                                        <p class="text-body-sm-regular text-neutral-500">
                                            Address: <?= $address1 . "," . $country ?>
                                            <br>
                                            Phone: <?= $phoneNumber ?>
                                        </p>
                                    </div>
                                </div>
                                <div class=" w-full h-[1px] lg:w-[1px] lg:h-[68px] bg-neutral-300"></div>
                                <div class="w-full flex flex-col gap-4">
                                    <div class="flex items-center gap-2">
                                        <figure class="w-6 h-6"><img src="<?= $url ?>/assets/image/icon/card.svg"
                                                alt="icon">
                                        </figure>
                                        <p class="text-body-md-medium text-gray-8">Payment method</p>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <figure class="w-[46px] h-[32px]"><img
                                                src="<?= $url ?>/assets/image/badge-visa.svg" alt="visa"></figure>
                                        <h2 class="text-body-sm-medium text-gray-9"> ***4242 | Expires 12/34</h2>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col gap-4">
                            <h2 class="text-body-xl-medium text-gray-9">Product list (<?= $totalRecords ?>)</h2>
                            <div class="flex flex-col gap-4 lg:gap-6 p-6 rounded-xl bg-white">

                                <?php foreach ($decodedData as $key => $va) :

                                    $idPro = $va['id'];
                                    $price = get_field('price', $idPro);
                                    $sale_price = get_field('sale_price', $idPro);
                                    $price_sub += $price * $va['qty'];
                                ?>
                                    <div
                                        class="w-full flex flex-wrap md:flex-row gap-6 lg:gap-0 justify-between items-end md:items-center">
                                        <div class="flex lg:flex-0 w-full md:w-2/3 max-w-[454px] items-center gap-5">
                                            <figure
                                                class="w-[60px] h-[60px] md:w-[100px] md:h-[100px] rounded-xl border border-solid border-neutral-200">
                                                <img src="<?= $va['img'] ?>" alt="item">
                                            </figure>
                                            <div class="flex-1 flex flex-col gap-2">
                                                <h2 class="text-body-md-medium text-gray-8 truncate-2row"><?= $va['title'] ?></h2>
                                                <div class="neutral-200 text-body-sm-regular text-gray-7">
                                                    Type: <?= $va['pack'] ?> Pack
                                                </div>
                                            </div>
                                        </div>
                                        <p class="text-body-md-regular text-gray-8">Quantity: <?= $va['qty'] ?></p>

                                        <p class="text-body-md-medium text-primary"><?= formatBalance($va['price']); ?></p>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="w-full lg:w-[32%] lg:max-w-[436px]">
                    <div class="flex flex-col gap-5 px-6 py-4 rounded-xl bg-white">
                        <p class="text-body-md-medium text-gray-9">Payment information</p>
                        <div class="flex flex-col gap-3">
                            <div class="flex items-center justify-between">
                                <p class="text-body-sm-regular text-gray-7">Sub total</p>
                                <h2 class="text-body-md-medium text-gray-9"><?php echo formatBalance($price_sub) ?> </h2>
                            </div>
                            <hr class="divider">
                            <div class="flex items-center justify-between">
                                <p class="text-body-sm-regular text-gray-7">Discount</p>
                                <h2 class="text-body-md-medium text-gray-9"><?php echo formatBalance($discount_price) ?> </h2>
                            </div>
                            <div class="flex items-center justify-between">
                                <p class="text-body-sm-regular text-gray-7">Shipping fee</p>
                                <h2 class="text-body-md-medium text-gray-9"><?php echo formatBalance($transport_fee) ?></h2>
                            </div>
                            <!--                            <div class="flex items-center justify-between">-->
                            <!--                                <p class="text-body-sm-regular text-gray-7">Taxes</p>-->
                            <!--                                <<p class="text-body-sm-regular text-secondary">Free of charge</p> -->
                            <!--                                <h2 class="text-body-md-medium text-gray-9">$190.00 </h2>-->
                            <!--                            </div>-->
                            <hr class="divider">
                            <div class="flex items-center justify-between">
                                <p class="text-body-sm-regular text-gray-7">Total payment</p>
                                <h2 class="text-body-xl-medium text-primary"><?php echo formatBalance($price_payment) ?></h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
<?php get_footer() ?>
<script !src="">
    function myFunction(inputId) {
        var copyText = document.getElementById(inputId);

        if (!copyText) {
            console.error('Không tìm thấy phần tử input với ID:', inputId);
            return;
        }

        // Copy bằng Clipboard API
        navigator.clipboard.writeText(copyText.value).then(() => {
            alert("<?php pll_e('Copied the text') ?>: " + copyText.value);
        }).catch(err => {
            console.error('Lỗi khi copy:', err);
        });
    }

</script>