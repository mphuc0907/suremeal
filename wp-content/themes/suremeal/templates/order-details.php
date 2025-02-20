<?php /* Template Name: Order detail */ ?>
<?php
$url = get_template_directory_uri();

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
<?php
get_header('dealer');
?>
<style>
    .badge {
        border-radius: 8px;
    }

    .warning {
        color: #ffa300;
        background: #fff8d6;
    }

    .success {
        color: #00a151;
        background: #dbffe4;
    }

    .error {
        color: #e02d2d;
        background: #ffe7e0;
    }
</style>
<div class="2xl:w-12/12 xl:w-12/12 md:w-full m-auto grid grid-cols-12 gap-8 xl:py-20 xl:px-32 p-5">
    <div class="2xl:col-span-8 xl:col-span-8 md:col-span-12 col-span-12 text-left">
        <div class="py-4 mb-0 bg-white rounded-xl">
            <div
                    class="items-center md:px-8 px-2 bd-line-bottom pb-2 mb-8 block w-full overflow-hidden">
                <div class="text-base font-medium color-vector flex items-center float-left mb-1">
                    <span class="text-base"><?= $formattedDateEastern ?></span>
                    <svg class="mx-2" xmlns="http://www.w3.org/2000/svg" width="4" height="4"
                         viewBox="0 0 4 4" fill="none">
                        <circle cx="2" cy="2" r="2" fill="#D9D9D9"></circle>
                    </svg>
                </div>
                <div class="flex items-center float-left mb-1">
									<span class="text-base font-medium checkout-color-text mr-2 text-base">Order
										code:</span>
                    <span class="checkout-color-unactive font-medium text-base"><?= $code ?></span>
                    <img class="w-5 h-auto ml-2" onclick="myFunction('myInput_completed<?= $code ?>')" src="<?= $url ?>/assets/dealer/img/copy.png" alt="">
                </div>
                <div
                        class="text-base checkout-color-unactive font-medium flex items-center col-span-4 float-left mb-1">
                    <svg class="mx-2" xmlns="http://www.w3.org/2000/svg" width="4" height="4"
                         viewBox="0 0 4 4" fill="none">
                        <circle cx="2" cy="2" r="2" fill="#D9D9D9"></circle>
                    </svg>
                    <span class="checkout-color-unactive font-medium text-base">Deliver</span>
                    <svg class="mx-2" xmlns="http://www.w3.org/2000/svg" width="4" height="4"
                         viewBox="0 0 4 4" fill="none">
                        <circle cx="2" cy="2" r="2" fill="#D9D9D9"></circle>
                    </svg>
                    <span class="checkout-color-unactive font-medium"><?= $countProduct ?> products</span>
                    <svg class="mx-2" xmlns="http://www.w3.org/2000/svg" width="4" height="4"
                         viewBox="0 0 4 4" fill="none">
                        <circle cx="2" cy="2" r="2" fill="#D9D9D9"></circle>
                    </svg>
                </div>
                <div class="grid grid-cols-2 col-span-6 float-left mb-1">
                    <div class="flex items-center text-base checkout-color-unactive font-medium text-base">
                        Receipt <img class="w-3 ml-2" src="<?= $url ?>/assets/dealer/img/receipt.png" alt="">
                    </div>
                </div>
                <div class="col-span-2 text-center px-2 py-1 font-medium ml-auto order-2 text-sm float-right mb-1 badge <?= $class ?>">
                    <?= $name ?>
                </div>
            </div>
            <div class="w-full md:px-24 px-14 pb-4 h-124">
                <div class="relative flex items-center justify-between w-full">
                    <div
                            class="absolute left-0 top-2 h-2 w-full -translate-y-2/4 orderdetail-bg-blue-sure">
                    </div>
                    <!-- step 1: w-1/3, step2: w-2/3, step3: w-full -->
                    <?php if ($status_transport >= 1): ?>
                        <div
                                class="absolute left-0 top-2 h-2 
                                <?php if ($status_transport == 2): ?>
                                    w-1/3
                                <?php elseif($status_transport == 3): ?>
                                    w-2/3
                                <?php elseif($status_transport == 4): ?>
                                    w-3/3
                                <?php endif ?>
                                -translate-y-2/4 bg-blue-sure transition-all duration-500">
                        </div>
                    <?php endif ?>
                    <div
                            class="relative z-10 grid text-white transition-all duration-300 place-items-center <?php if ($status_transport >= 1): ?>active<?php endif; ?>">
                        <div class="absolute w-max text-center top-0">
                            <svg xmlns="http://www.w3.org/2000/svg" width="25" height="24"
                                 viewBox="0 0 25 24" fill="none">
                                <circle cx="12.5" cy="12" r="12" fill="#0E74BC"></circle>
                            </svg>
                            <svg class="tooltip-checked" xmlns="http://www.w3.org/2000/svg" width="15"
                                 height="15" viewBox="0 0 13 12" fill="none">
                                <path d="M10.625 3.375L5.375 8.625L2.75 6" stroke="white"
                                      stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                </path>
                            </svg>
                            <div class="relative text-center mt-5">
                                <div class="">
                                    <img class="w-8 h-8" src="<?= $url ?>/assets/dealer/img/notebook.png" alt="">
                                </div>
                                <div>
													<span class="text-base font-medium">
														Order <br class="md:hidden block">Placed
													</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div
                            class="relative z-10 grid text-white transition-all duration-300 place-items-center" <?php if ($status_transport >= 2): ?> active<?php endif; ?>>
                        <div class="absolute w-max text-center top-0">
                            <?php if ($status_transport < 2): ?>
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 25 24" fill="none">
                                    <circle cx="12.5" cy="12" r="11" fill="white" stroke="#0E74BC"
                                            stroke-width="2" />
                                </svg>
                            <?php endif ?>
                            <?php if ($status_transport >= 2): ?>
                                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="24"
                                    viewBox="0 0 25 24" fill="none">
                                    <circle cx="12.5" cy="12" r="12" fill="#0E74BC"></circle>
                                </svg>
                                <svg class="tooltip-checked" xmlns="http://www.w3.org/2000/svg" width="15"
                                    height="15" viewBox="0 0 13 12" fill="none">
                                    <path d="M10.625 3.375L5.375 8.625L2.75 6" stroke="white"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    </path>
                                </svg>
                            <?php endif; ?>
                            <div class="relative text-center mt-5">
                                <div class="">
                                    <img class="w-8 h-8" src="<?= $url ?>/assets/dealer/img/package.png" alt="">
                                </div>
                                <div>
													<span class="text-base font-medium">
														Packaging
													</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div
                            class="relative z-10 grid text-white transition-all duration-300 place-items-center <?php if ($status_transport >= 3): ?> active<?php endif; ?>">
                        <div class="absolute w-max text-center top-0">
                            <?php if ($status_transport < 3): ?>
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 25 24" fill="none">
                                    <circle cx="12.5" cy="12" r="11" fill="white" stroke="#0E74BC"
                                            stroke-width="2" />
                                </svg>
                            <?php endif ?>
                            <?php if ($status_transport >= 3): ?>
                                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="24"
                                    viewBox="0 0 25 24" fill="none">
                                    <circle cx="12.5" cy="12" r="12" fill="#0E74BC"></circle>
                                </svg>
                                <svg class="tooltip-checked" xmlns="http://www.w3.org/2000/svg" width="15"
                                    height="15" viewBox="0 0 13 12" fill="none">
                                    <path d="M10.625 3.375L5.375 8.625L2.75 6" stroke="white"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    </path>
                                </svg>
                            <?php endif; ?>
                            <div class="relative text-center mt-5 opacity-50">
                                <div class="">
                                    <img class="w-8 h-8" src="<?= $url ?>/assets/dealer/img/truck.png" alt="">
                                </div>
                                <div>
													<span>
														On The <br class="md:hidden block"> Road
													</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div
                            class="relative z-10 grid text-white transition-all duration-300 place-items-center <?php if ($status_transport >= 4): ?> active<?php endif; ?>">
                        <div class="absolute w-max text-center top-0">
                            <?php if ($status_transport < 4): ?>
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 25 24" fill="none">
                                    <circle cx="12.5" cy="12" r="11" fill="white" stroke="#0E74BC"
                                            stroke-width="2" />
                                </svg>
                            <?php endif ?>
                            <?php if ($status_transport >= 4): ?>
                                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="24"
                                    viewBox="0 0 25 24" fill="none">
                                    <circle cx="12.5" cy="12" r="12" fill="#0E74BC"></circle>
                                </svg>
                                <svg class="tooltip-checked" xmlns="http://www.w3.org/2000/svg" width="15"
                                    height="15" viewBox="0 0 13 12" fill="none">
                                    <path d="M10.625 3.375L5.375 8.625L2.75 6" stroke="white"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    </path>
                                </svg>
                            <?php endif; ?>
                            <div class="relative text-center mt-5 opacity-50">
                                <div class="">
                                    <img class="w-8 h-8" src="<?= $url ?>/assets/dealer/img/handshake.png" alt="">
                                </div>
                                <div>
													<span>
														Delivered
													</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex grid grid-cols-11 md:px-8 mt-8 px-2 pt-4 border-top-vector">
                <div class="col-span-12 md:col-span-5">
                    <div class="flex items-center"><img class="w-6 mr-2" src="<?= $url ?>/assets/dealer/img/user_rounded.png"
                                                        alt="">
                        <h4 class="text-base color-vector font-medium my-0">Recipient information</h4>
                    </div>
                    <p class="text-base font-medium color-vector my-4 mx-0"><?= $name_info ?></p>
                    <p class="checkout-color-unactive font-normal text-sm py-0 my-0">Address: <?= $address1 . "," . $country ?></p>
                    <p class="checkout-color-unactive font-normal text-sm">Phone: <?= $phoneNumber ?></p>
                </div>
                <div class="col-span-1 md:block hidden">
                    <div class="line-center m-auto"></div>
                </div>
                <div class="col-span-12 md:col-span-5">
                    <div class="flex items-center"><img class="w-6 mr-2" src="<?= $url ?>/assets/dealer/img/card_method.png"
                                                        alt="">
                        <h4 class="text-base color-vector font-medium my-0">Payment method</h4>
                    </div>
                    <div
                            class="checkout-color-unactive font-normal text-sm py-4 my-0 flex items-center">
                        <img class="w-11 mr-3" src="<?= $url ?>/assets/dealer/img/payment_method_icon.png" alt=""> ***0933 |
                        Expires 3/29
                    </div>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-1 gap-4 mt-6 mb-4 mx-0">
            <h4 class="font-medium text-xl leading-8 checkout-color-text py-0 my-0 text-left">Product
                list (<?= $totalRecords ?>)</h4>
        </div>
        <div class="bg-white rounded-xl px-5">
            <?php foreach ($decodedData as $key => $va) :
                $idPro = $va['id'];
                $price_sub += $va['price'] * $va['qty'];
            ?>
                <div class="py-6 grid grid-cols-7 gap-5 bd-line-bottom">
                    <div
                            class="2xl:col-span-1 xl:col-span-1 lg:col-span-1 sm:col-span-1 col-span-7 flex items-center border rounded-xl p-0 w-h-100 p-1">
                        <img class="w-full" src="<?= $va['img'] ?>" alt="">
                    </div>
                    <div
                            class="2xl:col-span-5 xl:col-span-5 lg:col-span-5 sm:col-span-5 col-span-5 grid grid-cols-11 flex">
                        <div class="2xl:col-span-8 xl:col-span-6 lg:col-span-11 col-span-11 mb-0 mb-pr-2">
                            <h5
                                    class="font-medium leading-6 text-base checkout-color-text my-0 block title-pr">
                                <?= $va['title'] ?>
                            </h5>
                            <div class="tag mt-2 flex">
                                                <span
                                                        class="gap-2 rounded-lg checkout-bg py-2 px-3 w-auto text-sm checkout-tag-color">
                                                    Type: <?= $va['pack'] ?> Pack
                                                </span>
                            </div>
                        </div>
                        <div
                                class="2xl:col-span-3 xl:col-span-5 lg:col-span-11 col-span-11 flex items-center xl:justify-end lg:justify-start md:justify-start">
                            <div class="flex flex-col gap-1 justify-center items-center">
                                <div class="flex items-center justify-start color-vector">
                                    Quantity: <span> <?= $va['qty'] ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div
                            class="2xl:col-span-1 xl:col-span-1 lg:col-span-1 sm:col-span-1 col-span-2 flex items-center justify-end">
                        <div class="checkout-color-red-text font-medium">$<?= $va['price'] ?><span
                                    class="checkout-color-red-text font-medium"
                                    x-text="(currentVal*price).toFixed(2)"></span></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="2xl:col-span-4 xl:col-span-4 md:col-span-12 col-span-12">
        <div class="info px-6 py-4 bg-white rounded-xl">
            <h5 class="text-base checkout-color-text font-medium mb-5 text-left mt-0 p-0 ">
                Payment information
            </h5>
            <div class="price">
                <div class="flex items-center grid grid-cols-2 bd-line-bottom pb-3 mb-3">
                    <span class="text-left checkout-tag-color text-sm">Sub total</span>
                    <div class="flex justify-end">
                        <span class="text-base checkout-color-text font-medium">$<?= $price_sub ?></span>
                    </div>
                </div>
                <div class="flex items-center grid grid-cols-2 pb-3">
                    <span class="text-left checkout-tag-color text-sm">Discount</span>
                    <div class="flex justify-end">
                        <span class="text-base checkout-color-text font-medium">$<?= $discount_price ?></span>
                    </div>
                </div>
                <div class="flex items-center grid grid-cols-2 pb-3">
                    <span class="text-left checkout-tag-color text-sm">Shipping fee</span>
                    <div class="flex justify-end">
                        <span class="text-base checkout-color-text font-medium">$<?= $transport_fee ?></span>
                    </div>
                </div>
                <div class="flex items-center grid grid-cols-2 pb-0 mb-0">
                    <span class="text-left checkout-tag-color text-sm">Total payment</span>
                    <div class="flex justify-end">
										<span
                                                class="text-xl checkout-color-text font-medium checkout-color-red-text">$<?= $price_payment ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script !src="">
    function myFunction(inputId) {
        // Lấy input element
        var copyText = document.getElementById(inputId);

        copyText.style.display = "block";

        // Select text
        copyText.select();

        try {
            // Thực hiện copy
            document.execCommand('copy');

            // Thông báo
            alert("<?php pll_e('Copied the text') ?>: " + copyText.value);
        } catch (err) {
            console.error('Copy error: ', err);
        } finally {
            // Ẩn input lại
            copyText.style.display = "none";
        }
    }
</script>
<?php
get_footer('dealer');
?>
