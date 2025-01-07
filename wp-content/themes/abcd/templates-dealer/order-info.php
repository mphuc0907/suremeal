<?php /* Template Name: Dealer-Order-Info */ ?>
<?php
$authenticated_dealer = validate_dealer_token();
if (!isset($_COOKIE['dealer_token']) || !$authenticated_dealer) {
    wp_redirect(home_url());
    exit;
}
$searchOrder = $_GET['searchOrder'];

global $wpdb;

// Lấy thông tin người dùng
$user_email = $authenticated_dealer->email;
$first_name = $authenticated_dealer->first_name;
$last_name = $authenticated_dealer->last_name;
$avatar = $authenticated_dealer->avatar;
$addresses = json_decode($authenticated_dealer->addresses, true) ?: [];
$provider = $authenticated_dealer->provider;
$status = $authenticated_dealer->status;
$id = $authenticated_dealer->ID;
// Lấy thông tin người dùng từ session
$addresses = json_decode($_SESSION['addresses'], true);

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

$dealer_id = null;
if ($authenticated_dealer) {
    $dealer_id = $authenticated_dealer->ID;
}

$url = get_template_directory_uri();

if (!empty($searchOrder)) {
    $myrows = $wpdb->get_results(
        "SELECT * FROM wp_orders WHERE id_dealer = " . intval($id) . " AND order_code = '" . esc_sql($searchOrder) . "' ORDER BY id DESC"
    );


}else {
    $myrows = $wpdb->get_results("SELECT * FROM wp_orders WHERE id_dealer =" . $id . " ORDER BY id DESC");

}

$countmyrows = count($myrows);

// Đếm số lượng với điều kiện status_transport = 1
$order_placed = $wpdb->get_results(
    $wpdb->prepare(
        "SELECT * FROM wp_orders WHERE id_dealer = %d AND status = %d",
        $id,
        1
    )
);
$countOrder = count($order_placed);

// Đếm số lượng với điều kiện status_transport = 2
$packaging = $wpdb->get_results(
    $wpdb->prepare(
        "SELECT * FROM wp_orders WHERE id_dealer = %d AND status = %d",
        $id,
        3
    )
);
$countpackaging = count($packaging);

// Đếm số lượng với điều kiện status_transport = 3
$on_the_road = $wpdb->get_results(
    $wpdb->prepare(
        "SELECT * FROM wp_orders WHERE id_dealer = %d AND status = %d",
        $id,
        2
    )
);
$countroad = count($on_the_road);

// Đếm số lượng với điều kiện status_transport = 4
$delivered = $wpdb->get_results(
    $wpdb->prepare(
        "SELECT * FROM wp_orders WHERE id_dealer = %d AND status = %d",
        $id,
        4
    )
);
$countdelivered = count($delivered);

$url = get_template_directory_uri();
get_header();
?>
<div class="relative">
    <div class="grid 2xl:grid-cols-7 xl:grid-cols-7 md:grid-cols-8 sm:grid-cols-1 grid-cols-1 gap-0">
        <div class="flex items-start col-span-1 2xl:col-span-1 xl:col-span-1 md:col-span-2 grid bg-white md:h-screen height-full">
            <ul class="list-none pl-0 text-left mt-0 memubar" x-data="{ top: false }">
                <li class="flex items-center pt-4 pb-4 pl-6 item-f dashboard">
                <a class="flex items-center no-underline" href="<?= home_url() ?>/dealer-dashboard"><img class="mr-3 w-6 default" src="<?= $url ?>/assets/dealer/img/cuida.png" alt=""><img class="mr-3 w-6 active hidden" src="<?= $url ?>/assets/dealer/img/cuida_ac.png" alt=""><span class="menu-item">Dashboard</span></a>
                </li>
                <li class="flex items-center pt-4 pb-4 pl-6 item-f my-order checkout-menu-active">
                <a class="flex items-center no-underline menu-item" href="<?= home_url() ?>/dealer-order-info"><img class="mr-3 w-6 active" src="<?= $url ?>/assets/dealer/img/cart_ac.png " alt=""><span class="menu-item">My orders</span></a>
                </li>
                <?php if($status == 2): ?>
                    <li class="flex items-center pt-4 pb-4 pl-6 item-f management">
                    <a class="flex items-center no-underline menu-item" href="<?= home_url() ?>/dealer-affiliate-order"><img class="mr-3 w-6 default" src="<?= $url ?>/assets/dealer/img/tabler_shopping-bag-discount.png" alt=""><img class="mr-3 w-6 active hidden" src="<?= $url ?>/assets/dealer/img/tabler_shopping-bag-discount_ac.png" alt=""><span class="menu-item">Affiliate orders</span></a>
                    </li>
                    <li class="flex items-center pt-4 pb-4 pl-6 item-f management">
                    <a class="flex items-center no-underline menu-item" href="<?= home_url() ?>/dealer-affiliate-customization"><img class="mr-3 w-6 default" src="<?= $url ?>/assets/dealer/img/material-symbols_link.png" alt=""><img class="mr-3 w-6 hidden active" src="<?= $url ?>/assets/dealer/img/material-symbols_link_ac.png" alt=""><span class="menu-item">Affiliate link<br>customization</span></a>
                    </li>
                    <li class="flex items-center pt-4 pb-4 pl-6 item-f management">
                    <a class="flex items-center no-underline menu-item" href="<?= home_url() ?>/point-management"><img class="mr-3 w-6 default" src="<?= $url ?>/assets/dealer/img/reward.png" alt=""><img class="mr-3 w-6 hidden active" src="<?= $url ?>/assets/dealer/img/reward_ac.png" alt=""><span class="menu-item">Point management</span></a>
                    </li>
                <?php endif; ?>
                <li class="pt-4 pb-4 pl-6 item-f account" >
                <div class="flex items-center no-underline w-full" href="" x-on:click="top = ! top"><img class="mr-3 w-6 default" src="<?= $url ?>/assets/dealer/img/circle.png" alt=""><img class="mr-3 w-6 hidden active" src="<?= $url ?>/assets/dealer/img/circle_ac.png" alt="">
                    <span class="menu-item cursor-pointer">Account<br>information</span>
                    <div class="order-2 ml-auto mr-4">
                        <img class="w-6" :src="top ? '<?= $url ?>/assets/dealer/img/icon_down.png' : '<?= $url ?>/assets/dealer/img/icon_up.png'"  alt="">
                    </div>
                </div>
                </li>
                <li class="pl-6">
                <ul class="list-none" x-show="top">
                    <li class="mt-2">
                        <a class="no-underline text-sm" href="<?= home_url() ?>/dealer-personal-informmation">Personal information</a>
                    </li>
                    <li class="mt-2">
                        <a class="no-underline text-sm" href="<?= home_url() ?>/dealer-business-informmation">Business information</a>
                    </li>
                    <?php if($status == 2): ?>
                        <li class="mt-2">
                            <a class="no-underline text-sm" href="<?= home_url() ?>/dealer-bank-informmation">Bank account information</a>
                        </li>
                    <?php endif; ?>
                </ul>
                </li>
            </ul>
        </div>
        <div class="col-span-6 text-center md:py-8 py-4 m-h-content px-2">
            <div class="mx-auto relative w-1192-full overflow-hidden border-cm">
                <div class="flex flex-col lg:flex-row gap-6 items-center justify-between">
                    <h2 class="text-heading-h5 text-gray-8"><?php pll_e('My orders') ?></h2>
                    <form action="" id="searchOrder">
                    <input type="text" name="searchOrder" value="<?= $searchOrder ?>" class="home-search radius-8 xl:max-w-[200px] 2xl:max-w-[386px]"
                        placeholder="<?php pll_e('Search by order code') ?>">
                    </form>
                </div>

                <div class="mt-4 rounded-t-xl bg-white flex pt-2 items-center">
                    <div class="max-w-full overflow-x-auto custom-scrollbar">
                        <div class="grid grid-cols-[repeat(5,minmax(202px,1fr))]">
                            <div class="order-tab-item active" data-tab-id="all">
                                <p class="text-body-md-regular"><?php pll_e('All') ?> (<?= $countmyrows ?>)</p>
                            </div>
                            <div class="order-tab-item" data-tab-id="processing">
                                <p class="text-body-md-regular"><?php pll_e('Processing') ?> (<?= $countOrder ?>)</p>
                            </div>
                            <div class="order-tab-item" data-tab-id="in-progress">
                                <p class="text-body-md-regular"><?php pll_e('In progress') ?> (<?= $countpackaging ?>)</p>
                            </div>
                            <div class="order-tab-item" data-tab-id="completed">
                                <p class="text-body-md-regular"><?php pll_e('Completed') ?> (<?= $countroad ?>)</p>
                            </div>
                            <div class="order-tab-item" data-tab-id="canceled">
                                <p class="text-body-md-regular"><?php pll_e('Canceled') ?> (<?= $countdelivered ?>)</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-3 flex flex-col gap-6 order-tab" data-tab-id="all">
                    <?php foreach ($myrows as $key => $value) :

                        $get_newest_order = $wpdb->get_results("SELECT * FROM wp_orders WHERE order_code = '{$value->order_code}' ORDER BY id DESC");
                        $dataProduct = $get_newest_order[0]->dataproduct;
                        $decodedData = json_decode(str_replace('\\', "", $dataProduct), true);
                        $countProduct = count($decodedData);

                        $idPro = $decodedData[0]['id'];
                        $price = get_field('price', $idPro);
                        $sale_price = get_field('sale_price', $idPro);
                        $status = $value->status;
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
                        
                        // Get dealer discount if dealer is logged in
                        $dealer_discount = get_dealer_discount($dealer_id, $idPro);

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
                    ?>
                        <div class="w-full flex flex-col items-center justify-center bg-white rounded-xl">
                            <div
                                class="w-full flex flex-wrap items-center justify-between gap-2 px-8 py-6 border-b border-neutral-200 border-solid">
                                <div class="flex flex-wrap items-center gap-4">
                                    <p class="text-body-md-medium text-gray-8"><?= date('m/d/Y', $value->time_order) ?></p>
                                    <div class="w-1 h-1 rounded-full bg-[#D9D9D9]"></div>
                                    <div class="flex items-center gap-2">
                                        <p class="text-body-md-medium text-gray-8"><?php pll_e('Order code') ?>:</p>
                                        <p class="text-body-md-medium text-neutral-500"><?= $value->order_code ?></p>
                                        <input type="text" value="<?= $value->order_code ?>" id="myInput<?= $value->order_code ?>" style="display: none">
                                        <button class="button button-trans p-0"  onclick="myFunction('myInput<?= $value->order_code ?>')">
                                            <figure class="w-5 h-5"><img src="<?= $url ?>/assets/image/icon/duplicate.svg"
                                                                            alt="icon">
                                            </figure>
                                        </button>
                                    </div>
                                    <div class="w-1 h-1 rounded-full bg-[#D9D9D9]"></div>
                                    <p class="text-body-md-medium text-neutral-500" ><?php pll_e('Deliver') ?></p>
                                    <div class="w-1 h-1 rounded-full bg-[#D9D9D9]"></div>
                                    <p class="text-body-md-medium text-neutral-500"><?= $countProduct ?> <?php pll_e('products') ?></p>
                                    <div class="w-1 h-1 rounded-full bg-[#D9D9D9]"></div>
                                    <div class="flex items-center gap-2">
                                        <a href="/pdf/?order_code=<?= $value->order_code ?>" target="_blank" class="text-body-md-medium text-neutral-500">Receipt</a>
                                        <button class="button bg-trans" onclick="window.open('/pdf/?order_code=' . <?= $value->order_code ?>, '_blank')">
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

                                <div class="badge <?= $class ?>">
                                    <?= $name ?>
                                </div>
                            </div>

                            <div class="w-full py-6 px-8 flex flex-col gap-3 border-b border-solid border-neutral-200">
                                <!-- product -->
                                <div class="w-full flex flex-wrap lg:flex-nowrap justify-between items-center">
                                    <div class="flex lg:flex-0 w-full md:w-2/3 max-w-[653px] items-center gap-5">
                                        <figure
                                            class="w-[60px] h-[60px] md:w-[100px] md:h-[100px] rounded-xl border border-solid border-neutral-200">
                                            <img src="<?= $decodedData[0]['img'] ?>" alt="item">
                                        </figure>
                                        <div class="flex-1 flex flex-col gap-2">
                                            <h2 class="text-body-md-medium text-gray-8 truncate-2row"><?= $decodedData[0]['title'] ?></h2>
                                            <div class="neutral-200 text-body-sm-regular text-gray-7">
                                                <?php pll_e('Type') ?>: <?= get_field('quantity', $idPro) ?> <?php pll_e('Pack') ?>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="text-body-md-regular text-gray-8"><?php pll_e('Quantity') ?>: <?= $decodedData[0]['qty'] ?></p>
                                    <?php if ($final_price < $price): ?>
                                        <div class="flex flex-col items-end justify-end gap-1">
                                            <p class="text-body-md-medium text-gray-8"><?= formatBalance($final_price) ?> </p>
                                            <p class="line-through text-body-sm-regular text-neutral-500"><?= formatBalance($price) ?></p>
                                        </div>
                                    <?php else: ?>
                                        <div class="flex flex-col items-end justify-end gap-1">
                                            <p class="text-body-sm-regular text-neutral-500"><?= formatBalance($price) ?></p>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- total -->
                                <div class="flex items-center justify-between">
                                    <a href="/dealer-order-detail/?order_code=<?= $value->order_code ?>" class="button button-trans p-0 text-body-md-semibold text-secondary">
                                        <?php pll_e('View details') ?>
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <g id="Outline / Arrows / Alt Arrow Right">
                                                <path id="Vector (Stroke)" fill-rule="evenodd" clip-rule="evenodd" d="M8.51192 4.43057C8.82641 4.161 9.29989 4.19743 9.56946 4.51192L15.5695 11.5119C15.8102 11.7928 15.8102 12.2072 15.5695 12.4881L9.56946 19.4881C9.29989 19.8026 8.82641 19.839 8.51192 19.5695C8.19743 19.2999 8.161 18.8264 8.43057 18.5119L14.0122 12L8.43057 5.48811C8.161 5.17361 8.19743 4.70014 8.51192 4.43057Z" fill="#0E74BC" />
                                            </g>
                                        </svg>
                                    </a>

                                    <div class="flex items-center gap-2">
                                        <p class="text-body-md-regular text-neutral-500"><?php pll_e('Total') ?>:</p>
                                        <p class="text-heading-h6 font-medium text-primary"><?= formatBalance($value->price_payment) ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="mt-3 flex flex-col gap-6 order-tab" data-tab-id="processing">
                    <?php foreach ($order_placed as $key => $value) :

                        $get_newest_order = $wpdb->get_results("SELECT * FROM wp_orders WHERE order_code = '{$value->order_code}' ORDER BY id DESC");
                        $dataProduct = $get_newest_order[0]->dataproduct;
                        $decodedData = json_decode(str_replace('\\', "", $dataProduct), true);
                        $countProduct = count($decodedData);

                        $idPro = $decodedData[0]['id'];
                        $price = get_field('price', $idPro);
                        $sale_price = get_field('sale_price', $idPro);
                        $status = $value->status;
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
                        
                        // Get dealer discount if dealer is logged in
                        $dealer_discount = get_dealer_discount($dealer_id, $idPro);

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
                    ?>
                        <div class="w-full flex flex-col items-center justify-center bg-white rounded-xl">
                            <div
                                class="w-full flex flex-wrap items-center justify-between gap-2 px-8 py-6 border-b border-neutral-200 border-solid">
                                <div class="flex flex-wrap items-center gap-4">
                                    <p class="text-body-md-medium text-gray-8"><?= date('m/d/Y', $value->time_order) ?></p>
                                    <div class="w-1 h-1 rounded-full bg-[#D9D9D9]"></div>
                                    <div class="flex items-center gap-2">
                                        <p class="text-body-md-medium text-gray-8"><?php pll_e('Order code') ?>:</p>
                                        <p class="text-body-md-medium text-neutral-500"><?= $value->order_code ?></p>
                                        <input type="text" value="<?= $value->order_code ?>" id="myInput<?= $value->order_code ?>" style="display: none">
                                        <button class="button button-trans p-0"  onclick="myFunction('myInput<?= $value->order_code ?>')">
                                            <figure class="w-5 h-5"><img src="<?= $url ?>/assets/image/icon/duplicate.svg"
                                                                            alt="icon">
                                            </figure>
                                        </button>
                                    </div>
                                    <div class="w-1 h-1 rounded-full bg-[#D9D9D9]"></div>
                                    <p class="text-body-md-medium text-neutral-500"><?php pll_e('Deliver') ?></p>
                                    <div class="w-1 h-1 rounded-full bg-[#D9D9D9]"></div>
                                    <p class="text-body-md-medium text-neutral-500"><?= $countProduct ?> <?php pll_e('products') ?></p>
                                    <div class="w-1 h-1 rounded-full bg-[#D9D9D9]"></div>
                                    <div class="flex items-center gap-2">
                                        <a href="/pdf/?order_code=<?= $value->order_code ?>" target="_blank" class="text-body-md-medium text-neutral-500">Receipt</a>
                                        <button class="button bg-trans" onclick="window.open('/pdf/?order_code=' . <?= $value->order_code ?>, '_blank')">
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

                                <div class="badge <?= $class ?>">
                                    <?= $name ?>
                                </div>
                            </div>

                            <div class="w-full py-6 px-8 flex flex-col gap-3 border-b border-solid border-neutral-200">
                                <!-- product -->
                                <div class="w-full flex flex-wrap lg:flex-nowrap justify-between items-center">
                                    <div class="flex lg:flex-0 w-full md:w-2/3 max-w-[653px] items-center gap-5">
                                        <figure
                                            class="w-[60px] h-[60px] md:w-[100px] md:h-[100px] rounded-xl border border-solid border-neutral-200">
                                            <img src="<?= $decodedData[0]['img'] ?>" alt="item">
                                        </figure>
                                        <div class="flex-1 flex flex-col gap-2">
                                            <h2 class="text-body-md-medium text-gray-8 truncate-2row"><?= $decodedData[0]['title'] ?></h2>
                                            <div class="neutral-200 text-body-sm-regular text-gray-7">
                                                <?php pll_e('Type') ?>: <?= get_field('quantity', $idPro) ?> <?php pll_e('Pack') ?>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="text-body-md-regular text-gray-8"><?php pll_e('Quantity') ?>: <?= $decodedData[0]['qty'] ?></p>
                                    <?php if ($final_price < $price): ?>
                                        <div class="flex flex-col items-end justify-end gap-1">
                                            <p class="text-body-md-medium text-gray-8"><?= formatBalance($final_price) ?> </p>
                                            <p class="line-through text-body-sm-regular text-neutral-500"><?= formatBalance($price) ?></p>
                                        </div>
                                    <?php else: ?>
                                        <div class="flex flex-col items-end justify-end gap-1">
                                            <p class="text-body-sm-regular text-neutral-500"><?= formatBalance($price) ?></p>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- total -->
                                <div class="flex items-center justify-between">
                                    <a href="/dealer-order-detail/?order_code=<?= $value->order_code ?>" class="button button-trans p-0 text-body-md-semibold text-secondary">
                                        <?php pll_e('View details') ?>
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <g id="Outline / Arrows / Alt Arrow Right">
                                                <path id="Vector (Stroke)" fill-rule="evenodd" clip-rule="evenodd" d="M8.51192 4.43057C8.82641 4.161 9.29989 4.19743 9.56946 4.51192L15.5695 11.5119C15.8102 11.7928 15.8102 12.2072 15.5695 12.4881L9.56946 19.4881C9.29989 19.8026 8.82641 19.839 8.51192 19.5695C8.19743 19.2999 8.161 18.8264 8.43057 18.5119L14.0122 12L8.43057 5.48811C8.161 5.17361 8.19743 4.70014 8.51192 4.43057Z" fill="#0E74BC" />
                                            </g>
                                        </svg>
                                    </a>

                                    <div class="flex items-center gap-2">
                                        <p class="text-body-md-regular text-neutral-500"><?php pll_e('Total') ?>:</p>
                                        <p class="text-heading-h6 font-medium text-primary"><?= formatBalance($value->price_payment) ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="mt-3 flex flex-col gap-6 order-tab" data-tab-id="in-progress">
                    <?php foreach ($packaging as $key => $value) :

                        $get_newest_order = $wpdb->get_results("SELECT * FROM wp_orders WHERE order_code = '{$value->order_code}' ORDER BY id DESC");
                        $dataProduct = $get_newest_order[0]->dataproduct;
                        $decodedData = json_decode(str_replace('\\', "", $dataProduct), true);
                        $countProduct = count($decodedData);

                        $idPro = $decodedData[0]['id'];
                        $price = get_field('price', $idPro);
                        $sale_price = get_field('sale_price', $idPro);
                        $status = $value->status;
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
                        
                        // Get dealer discount if dealer is logged in
                        $dealer_discount = get_dealer_discount($dealer_id, $idPro);

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
                    ?>
                        <div class="w-full flex flex-col items-center justify-center bg-white rounded-xl">
                            <div
                                class="w-full flex flex-wrap items-center justify-between gap-2 px-8 py-6 border-b border-neutral-200 border-solid">
                                <div class="flex flex-wrap items-center gap-4">
                                    <p class="text-body-md-medium text-gray-8"><?= date('m/d/Y', $value->time_order) ?></p>
                                    <div class="w-1 h-1 rounded-full bg-[#D9D9D9]"></div>
                                    <div class="flex items-center gap-2">
                                        <p class="text-body-md-medium text-gray-8"><?php pll_e('Order code') ?>:</p>
                                        <p class="text-body-md-medium text-neutral-500"><?= $value->order_code ?></p>
                                        <input type="text" value="<?= $value->order_code ?>" id="myInput<?= $value->order_code ?>" style="display: none">
                                        <button class="button button-trans p-0"  onclick="myFunction('myInput<?= $value->order_code ?>')">
                                            <figure class="w-5 h-5"><img src="<?= $url ?>/assets/image/icon/duplicate.svg"
                                                                            alt="icon">
                                            </figure>
                                        </button>
                                    </div>
                                    <div class="w-1 h-1 rounded-full bg-[#D9D9D9]"></div>
                                    <p class="text-body-md-medium text-neutral-500"><?php pll_e('Deliver') ?></p>
                                    <div class="w-1 h-1 rounded-full bg-[#D9D9D9]"></div>
                                    <p class="text-body-md-medium text-neutral-500"><?= $countProduct ?> <?php pll_e('products') ?></p>
                                    <div class="w-1 h-1 rounded-full bg-[#D9D9D9]"></div>
                                    <div class="flex items-center gap-2">
                                        <a href="/pdf/?order_code=<?= $value->order_code ?>" target="_blank" class="text-body-md-medium text-neutral-500">Receipt</a>
                                        <button class="button bg-trans" onclick="window.open('/pdf/?order_code=' . <?= $value->order_code ?>, '_blank')">
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

                                <div class="badge <?= $class ?>">
                                    <?= $name ?>
                                </div>
                            </div>

                            <div class="w-full py-6 px-8 flex flex-col gap-3 border-b border-solid border-neutral-200">
                                <!-- product -->
                                <div class="w-full flex flex-wrap lg:flex-nowrap justify-between items-center">
                                    <div class="flex lg:flex-0 w-full md:w-2/3 max-w-[653px] items-center gap-5">
                                        <figure
                                            class="w-[60px] h-[60px] md:w-[100px] md:h-[100px] rounded-xl border border-solid border-neutral-200">
                                            <img src="<?= $decodedData[0]['img'] ?>" alt="item">
                                        </figure>
                                        <div class="flex-1 flex flex-col gap-2">
                                            <h2 class="text-body-md-medium text-gray-8 truncate-2row"><?= $decodedData[0]['title'] ?></h2>
                                            <div class="neutral-200 text-body-sm-regular text-gray-7">
                                                <?php pll_e('Type') ?>: <?= get_field('quantity', $idPro) ?> <?php pll_e('Pack') ?>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="text-body-md-regular text-gray-8"><?php pll_e('Quantity') ?>: <?= $decodedData[0]['qty'] ?></p>
                                    <?php if ($final_price < $price): ?>
                                        <div class="flex flex-col items-end justify-end gap-1">
                                            <p class="text-body-md-medium text-gray-8"><?= formatBalance($final_price) ?> </p>
                                            <p class="line-through text-body-sm-regular text-neutral-500"><?= formatBalance($price) ?></p>
                                        </div>
                                    <?php else: ?>
                                        <div class="flex flex-col items-end justify-end gap-1">
                                            <p class="text-body-sm-regular text-neutral-500"><?= formatBalance($price) ?></p>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- total -->
                                <div class="flex items-center justify-between">
                                    <a href="/dealer-order-detail/?order_code=<?= $value->order_code ?>" class="button button-trans p-0 text-body-md-semibold text-secondary">
                                        <?php pll_e('View details') ?>
                                        <figure><img src="<?= $url ?>/assets/image/icon/chev-right-24-second.svg"
                                                alt="icon">
                                        </figure>
                                    </a>

                                    <div class="flex items-center gap-2">
                                        <p class="text-body-md-regular text-neutral-500"><?php pll_e('Total') ?>:</p>
                                        <p class="text-heading-h6 font-medium text-primary"><?= formatBalance($value->price_payment) ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="mt-3 flex flex-col gap-6 order-tab" data-tab-id="completed">
                    <?php foreach ($on_the_road as $key => $value) :

                        $get_newest_order = $wpdb->get_results("SELECT * FROM wp_orders WHERE order_code = '{$value->order_code}' ORDER BY id DESC");
                        $dataProduct = $get_newest_order[0]->dataproduct;
                        $decodedData = json_decode(str_replace('\\', "", $dataProduct), true);
                        $countProduct = count($decodedData);

                        $idPro = $decodedData[0]['id'];
                        $price = get_field('price', $idPro);
                        $sale_price = get_field('sale_price', $idPro);
                        $status = $value->status;
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
                        
                        // Get dealer discount if dealer is logged in
                        $dealer_discount = get_dealer_discount($dealer_id, $idPro);

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
                    ?>
                        <div class="w-full flex flex-col items-center justify-center bg-white rounded-xl">
                            <div
                                class="w-full flex flex-wrap items-center justify-between gap-2 px-8 py-6 border-b border-neutral-200 border-solid">
                                <div class="flex flex-wrap items-center gap-4">
                                    <p class="text-body-md-medium text-gray-8"><?= date('m/d/Y', $value->time_order) ?></p>
                                    <div class="w-1 h-1 rounded-full bg-[#D9D9D9]"></div>
                                    <div class="flex items-center gap-2">
                                        <p class="text-body-md-medium text-gray-8"><?php pll_e('Order code') ?>:</p>
                                        <p class="text-body-md-medium text-neutral-500 order_code"><?= $value->order_code ?></p>
                                        <input type="text" value="<?= $value->order_code ?>" id="myInput<?= $value->order_code ?>" style="display: none">
                                        <button class="button button-trans p-0"  onclick="myFunction('myInput<?= $value->order_code ?>')">
                                            <figure class="w-5 h-5"><img src="<?= $url ?>/assets/image/icon/duplicate.svg"
                                                                            alt="icon">
                                            </figure>
                                        </button>
                                    </div>
                                    <div class="w-1 h-1 rounded-full bg-[#D9D9D9]"></div>
                                    <p class="text-body-md-medium text-neutral-500"><?php pll_e('Deliver') ?></p>
                                    <div class="w-1 h-1 rounded-full bg-[#D9D9D9]"></div>
                                    <p class="text-body-md-medium text-neutral-500"><?= $countProduct ?> <?php pll_e('products') ?></p>
                                    <div class="w-1 h-1 rounded-full bg-[#D9D9D9]"></div>
                                    <div class="flex items-center gap-2">
                                        <a href="/pdf/?order_code=<?= $value->order_code ?>" target="_blank" class="text-body-md-medium text-neutral-500">Receipt</a>
                                        <button class="button bg-trans" onclick="window.open('/pdf/?order_code=' . <?= $value->order_code ?>, '_blank')">
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

                                <div class="badge <?= $class ?>">
                                    <?= $name ?>
                                </div>
                            </div>

                            <div class="w-full py-6 px-8 flex flex-col gap-3 border-b border-solid border-neutral-200">
                                <!-- product -->
                                <div class="w-full flex flex-wrap lg:flex-nowrap justify-between items-center">
                                    <div class="flex lg:flex-0 w-full md:w-2/3 max-w-[653px] items-center gap-5">
                                        <figure
                                            class="w-[60px] h-[60px] md:w-[100px] md:h-[100px] rounded-xl border border-solid border-neutral-200">
                                            <img src="<?= $decodedData[0]['img'] ?>" alt="item">
                                        </figure>
                                        <div class="flex-1 flex flex-col gap-2">
                                            <h2 class="text-body-md-medium text-gray-8 truncate-2row"><?= $decodedData[0]['title'] ?></h2>
                                            <div class="neutral-200 text-body-sm-regular text-gray-7">
                                                <?php pll_e('Type') ?>: <?= get_field('quantity', $idPro) ?> <?php pll_e('Pack') ?>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="text-body-md-regular text-gray-8"><?php pll_e('Quantity') ?>: <?= $decodedData[0]['qty'] ?></p>
                                    <?php if ($final_price < $price): ?>
                                        <div class="flex flex-col items-end justify-end gap-1">
                                            <p class="text-body-md-medium text-gray-8"><?= formatBalance($final_price) ?> </p>
                                            <p class="line-through text-body-sm-regular text-neutral-500"><?= formatBalance($price) ?></p>
                                        </div>
                                    <?php else: ?>
                                        <div class="flex flex-col items-end justify-end gap-1">
                                            <p class="text-body-sm-regular text-neutral-500"><?= formatBalance($price) ?></p>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- total -->
                                <div class="flex items-center justify-between">
                                    <a href="/dealer-order-detail/?order_code=<?= $value->order_code ?>" class="button button-trans p-0 text-body-md-semibold text-secondary">
                                        <?php pll_e('View details') ?>
                                        <figure><img src="<?= $url ?>/assets/image/icon/chev-right-24-second.svg"
                                                alt="icon">
                                        </figure>
                                    </a>

                                    <div class="flex items-center gap-2">
                                        <p class="text-body-md-regular text-neutral-500"><?php pll_e('Total') ?>:</p>
                                        <p class="text-heading-h6 font-medium text-primary"><?= formatBalance($value->price_payment) ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="mt-3 flex flex-col gap-6 order-tab " data-tab-id="canceled">
                    <?php foreach ($delivered as $key => $value) :

                        $get_newest_order = $wpdb->get_results("SELECT * FROM wp_orders WHERE order_code = '{$value->order_code}' ORDER BY id DESC");
                        $dataProduct = $get_newest_order[0]->dataproduct;
                        $decodedData = json_decode(str_replace('\\', "", $dataProduct), true);
                        $countProduct = count($decodedData);

                        $idPro = $decodedData[0]['id'];
                        $price = get_field('price', $idPro);
                        $sale_price = get_field('sale_price', $idPro);
                        $status = $value->status;
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
                        
                        // Get dealer discount if dealer is logged in
                        $dealer_discount = get_dealer_discount($dealer_id, $idPro);

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
                    ?>
                        <div class="w-full flex flex-col items-center justify-center bg-white rounded-xl">
                            <div
                                class="w-full flex flex-wrap items-center justify-between gap-2 px-8 py-6 border-b border-neutral-200 border-solid">
                                <div class="flex flex-wrap items-center gap-4">
                                    <p class="text-body-md-medium text-gray-8"><?= date('m/d/Y', $value->time_order) ?></p>
                                    <div class="w-1 h-1 rounded-full bg-[#D9D9D9]"></div>
                                    <div class="flex items-center gap-2">
                                        <p class="text-body-md-medium text-gray-8"><?php pll_e('Order code') ?>:</p>
                                        <p class="text-body-md-medium text-neutral-500"><?= $value->order_code ?></p>
                                        <input type="text" value="<?= $value->order_code ?>" id="myInput<?= $value->order_code ?>" style="display: none">
                                        <button class="button button-trans p-0"  onclick="myFunction('myInput<?= $value->order_code ?>')">
                                            <figure class="w-5 h-5"><img src="<?= $url ?>/assets/image/icon/duplicate.svg"
                                                                            alt="icon">
                                            </figure>
                                        </button>
                                    </div>
                                    <div class="w-1 h-1 rounded-full bg-[#D9D9D9]"></div>
                                    <p class="text-body-md-medium text-neutral-500"><?php pll_e('Deliver') ?></p>
                                    <div class="w-1 h-1 rounded-full bg-[#D9D9D9]"></div>
                                    <p class="text-body-md-medium text-neutral-500"><?= $countProduct ?> <?php pll_e('products') ?></p>
                                    <div class="w-1 h-1 rounded-full bg-[#D9D9D9]"></div>
                                    <div class="flex items-center gap-2">
                                        <a href="/pdf/?order_code=<?= $value->order_code ?>" target="_blank" class="text-body-md-medium text-neutral-500">Receipt</a>
                                        <button class="button bg-trans" onclick="window.open('/pdf/?order_code=' . <?= $value->order_code ?>, '_blank')">
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

                                <div class="badge <?= $class ?>">
                                    <?= $name ?>
                                </div>
                            </div>

                            <div class="w-full py-6 px-8 flex flex-col gap-3 border-b border-solid border-neutral-200">
                                <!-- product -->
                                <div class="w-full flex flex-wrap lg:flex-nowrap justify-between items-center">
                                    <div class="flex lg:flex-0 w-full md:w-2/3 max-w-[653px] items-center gap-5">
                                        <figure
                                            class="w-[60px] h-[60px] md:w-[100px] md:h-[100px] rounded-xl border border-solid border-neutral-200">
                                            <img src="<?= $decodedData[0]['img'] ?>" alt="item">
                                        </figure>
                                        <div class="flex-1 flex flex-col gap-2">
                                            <h2 class="text-body-md-medium text-gray-8 truncate-2row"><?= $decodedData[0]['title'] ?></h2>
                                            <div class="neutral-200 text-body-sm-regular text-gray-7">
                                                <?php pll_e('Type') ?>: <?= get_field('quantity', $idPro) ?> <?php pll_e('Pack') ?>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="text-body-md-regular text-gray-8"><?php pll_e('Quantity') ?>: <?= $decodedData[0]['qty'] ?></p>
                                    <?php if ($final_price < $price): ?>
                                        <div class="flex flex-col items-end justify-end gap-1">
                                            <p class="text-body-md-medium text-gray-8"><?= formatBalance($final_price) ?> </p>
                                            <p class="line-through text-body-sm-regular text-neutral-500"><?= formatBalance($price) ?></p>
                                        </div>
                                    <?php else: ?>
                                        <div class="flex flex-col items-end justify-end gap-1">
                                            <p class="text-body-sm-regular text-neutral-500"><?= formatBalance($price) ?></p>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- total -->
                                <div class="flex items-center justify-between">
                                    <a href="/dealer-order-detail/?order_code=<?= $value->order_code ?>" class="button button-trans p-0 text-body-md-semibold text-secondary">
                                        <?php pll_e('View details') ?>
                                        <figure><img src="<?= $url ?>/assets/image/icon/chev-right-24-second.svg"
                                                alt="icon">
                                        </figure>
                                    </a>

                                    <div class="flex items-center gap-2">
                                        <p class="text-body-md-regular text-neutral-500"><?php pll_e('Total') ?>:</p>
                                        <p class="text-heading-h6 font-medium text-primary"><?= formatBalance($value->price_payment) ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div> 
    </div>
</div>
<?php get_footer() ?>
<style>
    .order-tab-item {
        display: flex;
        padding: 8px 16px 12px 16px;
        justify-content: center;
        align-items: center;
        gap: 8px;
        border-bottom: 2px solid;
        border-color: transparent;
        background: #FFF;
        cursor: pointer;
    }

    .order-tab-item.active {
        font-weight: 600;
        color: #0e74bc;
        border-color: #0e74bc;

    }
</style>
<!-- tab active -->
<script !src="">
    function myFunction(id) {
        // Get the text field
        var copyText = document.getElementById(id);

        // Select the text field
        copyText.select();
        copyText.setSelectionRange(0, 99999); // For mobile devices

        // Copy the text inside the text field
        navigator.clipboard.writeText(copyText.value);

        // Alert the copied text
        alert("<?php pll_e('Copied the text') ?>: " + copyText.value);
    }

</script>
<script>
    jQuery(document).ready(function($) {

        // Xử lý khi người dùng nhấn Enter trong ô input
        $('#searchOrder').on('submit', function(e) {
            e.preventDefault();
            if (e.key === 'Enter') {
                e.preventDefault(); // Ngăn gửi form mặc định

                let orderCode = $(this).val().trim();

                if (orderCode) {
                    // Gửi AJAX request
                    $.ajax({
                        url: '<?= admin_url("admin-ajax.php"); ?>', // Biến ajaxurl được thêm bởi WordPress
                        type: 'POST',
                        data: {
                            action: 'search_order_by_code', // Hook action được định nghĩa trong PHP
                            order_code: orderCode,
                        },
                        beforeSend: function() {
                            Swal.fire({
                                title: 'Processing',
                                html: '<?php pll_e('Please wait...') ?>',
                                didOpen: () => {
                                    Swal.showLoading()
                                }
                            });
                        },
                        success: function(response) {
                            if (response.success) {
                                // renderOrderResults(); // Gọi hàm render kết quả
                                const resultsContainer = $('.order-tab'); // Thay '.order-tab' bằng vùng chứa kết quả
                                resultsContainer.empty(); // Xóa nội dung cũ

                                // Lặp qua danh sách đơn hàng và hiển thị
                                $.each(response.orders, function(index, order) {
                                    resultsContainer.append(`
                <div class="w-full flex flex-col items-center justify-center bg-white rounded-xl">
                    <div class="w-full flex flex-wrap items-center justify-between gap-2 px-8 py-6 border-b border-neutral-200 border-solid">
                        <div class="flex flex-wrap items-center gap-4">
                            <p class="text-body-md-medium text-gray-8">${order.date}</p>
                            <div class="w-1 h-1 rounded-full bg-[#D9D9D9]"></div>
                            <div class="flex items-center gap-2">
                                <p class="text-body-md-medium text-gray-8">Order code:</p>
                                <p class="text-body-md-medium text-neutral-500">${order.order_code}</p>
                            </div>
                        </div>
                        <div class="badge ${order.status_class}">
                            ${order.status}
                        </div>
                    </div>
                </div>
            `);
                                });
                            }
                            } else {
                                $('.order-tab').html('<p>' + (response.message || '<?php pll_e('No orders found.') ?>') + '</p>');
                            }
                        },
                        error: function(error) {
                            console.error('Error:', error);
                            $('.order-tab').html('<p>Something went wrong. Please try again later.</p>');
                        },
                    });
                } else {
                    alert('<?php pll_e('Please enter an order code.') ?>');
                }
            }
        });

        // Hàm hiển thị kết quả đơn hàng
        function renderOrderResults(orders) {

    });
</script>
<script defer>
    document.addEventListener("DOMContentLoaded", () => {
        const tabItems = document.querySelectorAll(".order-tab-item");

        tabItems.forEach((item) => {
            item.addEventListener("click", () => {
                // Loại bỏ class `active` khỏi tất cả các mục
                tabItems.forEach((tab) => tab.classList.remove("active"));

                // Thêm class `active` vào mục được click
                item.classList.add("active");
            });
        });
    });
    document.addEventListener('DOMContentLoaded', () => {
        const tabItems = document.querySelectorAll('.order-tab-item');
        const tabContents = document.querySelectorAll('.order-tab');

        // Xử lý click vào tab
        tabItems.forEach(tab => {
            tab.addEventListener('click', () => {
                const tabId = tab.getAttribute('data-tab-id'); // Lấy ID của tab được click

                // Xóa class active khỏi tất cả các tab
                tabItems.forEach(item => item.classList.remove('active'));

                // Ẩn tất cả các nội dung tab
                tabContents.forEach(content => content.style.display = 'none');

                // Thêm class active cho tab được click
                tab.classList.add('active');

                // Hiển thị nội dung tab tương ứng với tab được click
                const activeContent = document.querySelector(`.order-tab[data-tab-id="${tabId}"]`);
                if (activeContent) {
                    activeContent.style.display = 'flex';
                }
            });
        });

        // Hiển thị tab đầu tiên mặc định
        if (tabContents.length > 0) {
            tabContents.forEach(content => content.style.display = 'none'); // Ẩn tất cả nội dung
            const firstContent = tabContents[0];
            if (firstContent) {
                firstContent.style.display = 'flex'; // Hiển thị nội dung tab đầu tiên
            }
        }
    });
</script>