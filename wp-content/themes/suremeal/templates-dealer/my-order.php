<?php
/* Template Name: My order */
?>
<?php

$searchOrder = $_GET['searchOrder'];
global $wpdb;
$authenticated_user = validate_user_token();
$id = $authenticated_user->ID;

if (!empty($searchOrder)) {
    $myrows = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT * FROM wp_orders WHERE id_user = %d AND order_code LIKE %s ORDER BY id DESC",
            intval($id),
            '%' . $wpdb->esc_like($searchOrder) . '%'
        )
    );
} else {
    $myrows = $wpdb->get_results("SELECT * FROM wp_orders WHERE id_user =" . $id . " ORDER BY id DESC");
}

$countmyrows = count($myrows);

// Đếm số lượng với điều kiện status_transport = 1
$order_placed = $wpdb->get_results(
    $wpdb->prepare(
        "SELECT * FROM wp_orders WHERE id_user = %d AND status = %d",
        $id,
        1
    )
);
$countOrder = count($order_placed);

// Đếm số lượng với điều kiện status_transport = 2
$packaging = $wpdb->get_results(
    $wpdb->prepare(
        "SELECT * FROM wp_orders WHERE id_user = %d AND status = %d",
        $id,
        3
    )
);
$countpackaging = count($packaging);

// Đếm số lượng với điều kiện status_transport = 3
$on_the_road = $wpdb->get_results(
    $wpdb->prepare(
        "SELECT * FROM wp_orders WHERE id_user = %d AND status = %d",
        $id,
        2
    )
);
$countroad = count($on_the_road);

// Đếm số lượng với điều kiện status_transport = 4
$delivered = $wpdb->get_results(
    $wpdb->prepare(
        "SELECT * FROM wp_orders WHERE id_user = %d AND status = %d",
        $id,
        4
    )
);
$countdelivered = count($delivered);

get_header('dealer');
$url = get_template_directory_uri();
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

    .button {
        display: flex;
        align-items: center;
        justify-content: center;
        width: fit-content;
        height: fit-content;
        gap: 8px;
        padding: 16px 32px;
        border-radius: 8px;
        border: 1px solid;
        border-color: transparent;
        cursor: pointer;
    }

    @media (max-width: 768px) {
        .button {
            padding: 12px 24px;
        }
    }

    .button.bg-trans {
        padding: 0;
        background-color: transparent;
        border-color: transparent;
    }

    /* max-width cho các kích thước màn hình xl và 2xl */
    @media (min-width: 1280px) {
        .custom-width {
            max-width: 200px;
        }
    }

    @media (min-width: 1536px) {
        .custom-width {
            max-width: 386px;
        }
    }

    /* Vị trí và dịch chuyển */
    .custom-button {
        position: absolute;
        right: 24px;
        /* 6 * 4px = 24px */
        top: 50%;
        transform: translateY(-50%);
    }



    /* search header style */
    input.home-search {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        padding: 10px 36px 10px 16px;
        font-size: 14px;
        border: 1px solid #e5e7eb;
        border-radius: 999px;
        background-color: #fff;
        background-image: url(../../assets/image/icon/mag-glass.svg);
        background-repeat: no-repeat;
        background-position: right 14px center;
        color: #373a51;
    }

    input.home-search.no-bg {
        background-image: none;
        position: relative;
    }

    input.home-search::placeholder {
        color: #6b7280;
    }

    input.home-search.radius-8 {
        border-radius: 8px;
    }

    input.home-search:hover {
        border-color: #0e74bc;
        caret-color: #0e74bc;
    }

    input.home-search:focus {
        border-color: #0e74bc;
        caret-color: #0e74bc;
    }
</style>
<div class="text-center md:p-8 py-4 m-h-content px-2">
    <div class="mx-auto relative w-1050-full overflow-hidden">
        <div class="mb-4 block md:flex items-center justify-between">
            <h4 class="text-left m-0 md:mb-0 mb-4 color-vector text-28 font-semibold w-full">My orders</h4>
            <form id="searchOrder" class="relative w-full ml-auto order-2">
                <input type="text" name="searchOrder" value="<?= $searchOrder ?>" class="home-search no-bg radius-8 w-386"
                    placeholder="<?php pll_e('Search by order code') ?>">
                <button class="button bg-trans absolute custom-button" type="submit">
                    <figure class="" style="width: 16px; height: 16px; margin: 0;"><img src="<?= $url ?>/assets/image/icon/mag-glass.svg" alt=""></figure>
                </button>
            </form>

        </div>
        <div class="">
            <div x-data="{ selectedTab: 'all' }" class="w-full">
                <div @keydown.right.prevent="$focus.wrap().next()" @keydown.left.prevent="$focus.wrap().previous()" class="flex gap-0 overflow-x-auto grid grid-cols-4 mb-3 bg-white pt-2 rounded-t-xl" role="tablist" aria-label="tab options">
                    <button @click="selectedTab = 'all'" :aria-selected="selectedTab === 'all'" :tabindex="selectedTab === 'all' ? '0' : '-1'" :class="selectedTab === 'all' ? 'pt-2 pb-3 bg-white blue-sure myorder-border-bottom text-base font-semibold' : 'pt-2 pb-3 bg-white text-base font-normal color-icon-eye'" type="button" role="tab" aria-controls="tabpanelGroups" class="px-4">All (<?= $countmyrows ?>)</button>
                    <button @click="selectedTab = 'Processing'" :aria-selected="selectedTab === 'Processing'" :tabindex="selectedTab === 'Processing' ? '0' : '-1'" :class="selectedTab === 'Processing' ? 'pt-2 pb-3 bg-white blue-sure myorder-border-bottom text-base font-semibold' : 'pt-2 pb-3 bg-white text-base font-normal color-icon-eye'" class="h-min px-4 py-2 text-sm" type="button" role="tab" aria-controls="tabpanelLikes" class="px-4">Processing (<?= $countOrder ?>)</button>
                    <!-- <button @click="selectedTab = 'progress'" :aria-selected="selectedTab === 'progress'" :tabindex="selectedTab === 'progress' ? '0' : '-1'" :class="selectedTab === 'progress' ? 'pt-2 pb-3 bg-white blue-sure myorder-border-bottom text-base font-semibold' : 'pt-2 pb-3 bg-white text-base font-normal color-icon-eye'" class="h-min px-4 py-2 text-sm" type="button" role="tab" aria-controls="tabpanelComments" class="px-4">In progress (<?= $countpackaging ?>)</button> -->
                    <button @click="selectedTab = 'completed'" :aria-selected="selectedTab === 'completed'" :tabindex="selectedTab === 'completed' ? '0' : '-1'" :class="selectedTab === 'completed' ? 'pt-2 pb-3 bg-white blue-sure myorder-border-bottom text-base font-semibold' : 'pt-2 pb-3 bg-white text-base font-normal color-icon-eye'" class="h-min px-4 py-2 text-sm" type="button" role="tab" aria-controls="tabpanelSaved" class="px-4">Completed (<?= $countroad ?>)</button>
                    <button @click="selectedTab = 'canceled'" :aria-selected="selectedTab === 'canceled'" :tabindex="selectedTab === 'canceled' ? '0' : '-1'" :class="selectedTab === 'canceled' ? 'pt-2 pb-3 bg-white blue-sure myorder-border-bottom text-base font-semibold' : 'pt-2 pb-3 bg-white text-base font-normal color-icon-eye'" class="h-min px-4 py-2 text-sm" type="button" role="tab" aria-controls="tabpanelSaved" class="px-4">Canceled (<?= $countdelivered ?>)</button>
                </div>
                <div class="text-neutral-600 dark:text-neutral-300">
                    <div x-show="selectedTab === 'all'" id="tabpanelGroups" role="tabpanel" aria-label="all">
                        <?php foreach ($myrows as $key => $value) :
                            $get_newest_order = $wpdb->get_results("SELECT * FROM wp_orders WHERE order_code = '{$value->order_code}' ORDER BY id DESC");
                            $dataProduct = $get_newest_order[0]->dataproduct;
                            $decodedData = json_decode(str_replace('\\', "", $dataProduct), true);
                            $countProduct = count($decodedData);

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
                        ?>
                            <div class="pt-4 pb-6 mb-6 bg-white rounded-xl">
                                <div class="items-center md:px-8 px-2 bd-line-bottom pb-4 block w-full overflow-hidden">
                                    <div class="text-base font-medium color-vector flex items-center float-left">
                                        <span class="text-base"><?= date('m/d/Y', $value->time_order) ?></span>
                                        <svg class="mx-4" xmlns="http://www.w3.org/2000/svg" width="4" height="4" viewBox="0 0 4 4" fill="none">
                                            <circle cx="2" cy="2" r="2" fill="#D9D9D9"></circle>
                                        </svg>
                                    </div>
                                    <div class="flex items-center float-left">
                                        <span class="text-base font-medium checkout-color-text mr-2 text-base">Order code:</span>
                                        <span class="checkout-color-unactive font-medium text-base"><?= $value->order_code ?></span>
                                        <input type="text" value="<?= $value->order_code ?>" id="myInput_all<?= $value->order_code ?>" style="display: none">
                                        <img class="w-5 h-auto ml-2" style="cursor: pointer;" onclick="myFunction('myInput_all<?= $value->order_code ?>')" src="<?= $url ?>/dist/img/copy.svg" alt="">
                                    </div>
                                    <div class="text-base checkout-color-unactive font-medium flex items-center col-span-4 float-left">
                                        <svg class="mx-4" xmlns="http://www.w3.org/2000/svg" width="4" height="4" viewBox="0 0 4 4" fill="none">
                                            <circle cx="2" cy="2" r="2" fill="#D9D9D9"></circle>
                                        </svg>
                                        <span class="checkout-color-unactive font-medium text-base">Deliver</span>
                                        <svg class="mx-4" xmlns="http://www.w3.org/2000/svg" width="4" height="4" viewBox="0 0 4 4" fill="none">
                                            <circle cx="2" cy="2" r="2" fill="#D9D9D9"></circle>
                                        </svg>
                                        <span class="checkout-color-unactive font-medium"><?= $countProduct ?> products</span>
                                        <svg class="mx-4" xmlns="http://www.w3.org/2000/svg" width="4" height="4" viewBox="0 0 4 4" fill="none">
                                            <circle cx="2" cy="2" r="2" fill="#D9D9D9"></circle>
                                        </svg>
                                    </div>
                                    <div class="grid grid-cols-2 col-span-6 float-left">
                                        <a href="/pdf/?order_code=<?= $value->order_code ?>" target="_blank" class="flex items-center text-base checkout-color-unactive font-medium text-base">Receipt <img class="w-3 ml-2" src="<?= $url ?>/dist/img/receipt.svg" alt=""></a>
                                    </div>
                                    <div class="badge <?= $class ?> col-span-2 text-center px-2 py-1 font-medium ml-auto order-2 text-sm float-right">
                                        <?= $name ?>
                                    </div>
                                </div>
                                <?php foreach ($decodedData as $key => $item):
                                    $idPro = $item['id'];
                                ?>
                                    <div class="pt-6 pb-3 px-8 grid grid-cols-8 gap-5" x-data="{ currentVal: 1, minVal: 0, maxVal: 10, decimalPoints: 0, incrementAmount: 1, price: 96 }">
                                        <div class="2xl:col-span-1 xl:col-span-1 lg:col-span-1 sm:col-span-1 col-span-8 flex items-center border rounded-xl h-fit p-1">
                                            <img class="w-full" src="<?= $item['img'] ?>" alt="">
                                        </div>
                                        <div class="2xl:col-span-6 xl:col-span-6 lg:col-span-6 sm:col-span-5 col-span-6 grid grid-cols-11 flex">
                                            <div class="2xl:col-span-8 xl:col-span-6 lg:col-span-11 col-span-11 mb-0 mb-pr-2">
                                                <h5 class="font-medium leading-6 text-base checkout-color-text my-0 block title-pr text-left">
                                                    <?= $item['title'] ?>
                                                </h5>
                                                <?php if ($item['pack']): ?>
                                                    <div class="tag mt-2 flex">
                                                        <span class="gap-2 rounded-lg checkout-bg py-2 px-3 w-auto text-sm checkout-tag-color">
                                                            Type: <?= $item['pack'] ?> Pack
                                                        </span>
                                                    </div>
                                                <?php endif ?>
                                            </div>
                                            <div class="2xl:col-span-3 xl:col-span-5 lg:col-span-11 col-span-11 flex items-center xl:justify-end lg:justify-end md:justify-start">
                                                <div class="flex flex-col gap-1 justify-center items-center">
                                                    <div class="flex items-center justify-start color-vector">
                                                        Quantity:<span><?= $item['qty'] ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="2xl:col-span-1 xl:col-span-1 lg:col-span-1 sm:col-span-1 col-span-2 flex items-center justify-end">
                                            <div class="text-right"><span class="color-vector font-medium text-lg">$<?= $item['price'] ?></span></div>
                                        </div>
                                    </div>
                                <?php endforeach ?>
                                <div class="flex px-8 flex items-center">
                                    <div>
                                        <a href="<?= home_url() ?>/order-detail/?order_code=<?= $value->order_code ?>" class="flex items-center no-underline">
                                            <span class="text-base font-semibold blue-sure">View details</span>
                                            <img class="ml-2 w-6" src="<?= $url ?>/dist/img/arrow_right_blue.png" alt="">
                                        </a>
                                    </div>
                                    <div class="order-2 flex items-center ml-auto">
                                        <span class="text-base checkout-color-unactive">Total:&nbsp;</span>
                                        <span class="font-medium text-2xl checkout-color-red-text">$<?= $value->price_payment ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div x-show="selectedTab === 'Processing'" id="tabpanelLikes" role="tabpanel" aria-label="Processing">
                        <?php foreach ($order_placed as $key => $value) :
                            $get_newest_order = $wpdb->get_results("SELECT * FROM wp_orders WHERE order_code = '{$value->order_code}' ORDER BY id DESC");
                            $dataProduct = $get_newest_order[0]->dataproduct;
                            $decodedData = json_decode(str_replace('\\', "", $dataProduct), true);
                            $countProduct = count($decodedData);

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
                        ?>
                            <div class="pt-4 pb-6 mb-6 bg-white rounded-xl">
                                <div class="items-center md:px-8 px-2 bd-line-bottom pb-4 block w-full overflow-hidden">
                                    <div class="text-base font-medium color-vector flex items-center float-left">
                                        <span class="text-base"><?= date('m/d/Y', $value->time_order) ?></span>
                                        <svg class="mx-4" xmlns="http://www.w3.org/2000/svg" width="4" height="4" viewBox="0 0 4 4" fill="none">
                                            <circle cx="2" cy="2" r="2" fill="#D9D9D9"></circle>
                                        </svg>
                                    </div>
                                    <div class="flex items-center float-left">
                                        <span class="text-base font-medium checkout-color-text mr-2 text-base">Order code:</span>
                                        <span class="checkout-color-unactive font-medium text-base"><?= $value->order_code ?></span>
                                        <input type="text" value="<?= $value->order_code ?>" id="myInput_processing<?= $value->order_code ?>" style="display: none">
                                        <img class="w-5 h-auto ml-2" style="cursor: pointer;" onclick="myFunction('myInput_processing<?= $value->order_code ?>')" src="<?= $url ?>/dist/img/copy.svg" alt="">
                                    </div>
                                    <div class="text-base checkout-color-unactive font-medium flex items-center col-span-4 float-left">
                                        <svg class="mx-4" xmlns="http://www.w3.org/2000/svg" width="4" height="4" viewBox="0 0 4 4" fill="none">
                                            <circle cx="2" cy="2" r="2" fill="#D9D9D9"></circle>
                                        </svg>
                                        <span class="checkout-color-unactive font-medium text-base">Deliver</span>
                                        <svg class="mx-4" xmlns="http://www.w3.org/2000/svg" width="4" height="4" viewBox="0 0 4 4" fill="none">
                                            <circle cx="2" cy="2" r="2" fill="#D9D9D9"></circle>
                                        </svg>
                                        <span class="checkout-color-unactive font-medium"><?= $countProduct ?> products</span>
                                        <svg class="mx-4" xmlns="http://www.w3.org/2000/svg" width="4" height="4" viewBox="0 0 4 4" fill="none">
                                            <circle cx="2" cy="2" r="2" fill="#D9D9D9"></circle>
                                        </svg>
                                    </div>
                                    <div class="grid grid-cols-2 col-span-6 float-left">
                                        <a href="/pdf/?order_code=<?= $value->order_code ?>" target="_blank" class="flex items-center text-base checkout-color-unactive font-medium text-base">Receipt <img class="w-3 ml-2" src="<?= $url ?>/dist/img/receipt.svg" alt=""></a>
                                    </div>
                                    <div class="badge <?= $class ?> col-span-2 text-center px-2 py-1 font-medium ml-auto order-2 text-sm float-right">
                                        <?= $name ?>
                                    </div>
                                </div>
                                <?php foreach ($decodedData as $key => $item):
                                    $idPro = $item['id'];
                                ?>
                                    <div class="pt-6 pb-3 px-8 grid grid-cols-8 gap-5" x-data="{ currentVal: 1, minVal: 0, maxVal: 10, decimalPoints: 0, incrementAmount: 1, price: 96 }">
                                        <div class="2xl:col-span-1 xl:col-span-1 lg:col-span-1 sm:col-span-1 col-span-8 flex items-center border rounded-xl h-fit p-1">
                                            <img class="w-full" src="<?= $item['img'] ?>" alt="">
                                        </div>
                                        <div class="2xl:col-span-6 xl:col-span-6 lg:col-span-6 sm:col-span-5 col-span-6 grid grid-cols-11 flex">
                                            <div class="2xl:col-span-8 xl:col-span-6 lg:col-span-11 col-span-11 mb-0 mb-pr-2">
                                                <h5 class="font-medium leading-6 text-base checkout-color-text my-0 block title-pr text-left">
                                                    <?= $item['title'] ?>
                                                </h5>
                                                <?php if ($item['pack']): ?>
                                                    <div class="tag mt-2 flex">
                                                        <span class="gap-2 rounded-lg checkout-bg py-2 px-3 w-auto text-sm checkout-tag-color">
                                                            Type: <?= $item['pack'] ?> Pack
                                                        </span>
                                                    </div>
                                                <?php endif ?>
                                            </div>
                                            <div class="2xl:col-span-3 xl:col-span-5 lg:col-span-11 col-span-11 flex items-center xl:justify-end lg:justify-end md:justify-start">
                                                <div class="flex flex-col gap-1 justify-center items-center">
                                                    <div class="flex items-center justify-start color-vector">
                                                        Quantity:<span><?= $item['qty'] ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="2xl:col-span-1 xl:col-span-1 lg:col-span-1 sm:col-span-1 col-span-2 flex items-center justify-end">
                                            <div class="text-right"><span class="color-vector font-medium text-lg">$<?= $item['price'] ?></span></div>
                                        </div>
                                    </div>
                                <?php endforeach ?>
                                <div class="flex px-8 flex items-center">
                                    <div>
                                        <a href="<?= home_url() ?>/order-detail/?order_code=<?= $value->order_code ?>" class="flex items-center no-underline">
                                            <span class="text-base font-semibold blue-sure">View details</span>
                                            <img class="ml-2 w-6" src="<?= $url ?>/dist/img/arrow_right_blue.png" alt="">
                                        </a>
                                    </div>
                                    <div class="order-2 flex items-center ml-auto">
                                        <span class="text-base checkout-color-unactive">Total:&nbsp;</span>
                                        <span class="font-medium text-2xl checkout-color-red-text">$<?= $value->price_payment ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <!-- <div x-show="selectedTab === 'progress'" id="tabpanelComments" role="tabpanel" aria-label="progress">
                        <?php foreach ($packaging as $key => $value) :
                            $get_newest_order = $wpdb->get_results("SELECT * FROM wp_orders WHERE order_code = '{$value->order_code}' ORDER BY id DESC");
                            $dataProduct = $get_newest_order[0]->dataproduct;
                            $decodedData = json_decode(str_replace('\\', "", $dataProduct), true);
                            $countProduct = count($decodedData);

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
                        ?>
                            <div class="pt-4 pb-6 mb-6 bg-white rounded-xl">
                                <div class="items-center md:px-8 px-2 bd-line-bottom pb-4 block w-full overflow-hidden">
                                    <div class="text-base font-medium color-vector flex items-center float-left">
                                        <span class="text-base"><?= date('m/d/Y', $value->time_order) ?></span>
                                        <svg class="mx-4" xmlns="http://www.w3.org/2000/svg" width="4" height="4" viewBox="0 0 4 4" fill="none">
                                            <circle cx="2" cy="2" r="2" fill="#D9D9D9"></circle>
                                        </svg>
                                    </div>
                                    <div class="flex items-center float-left">
                                        <span class="text-base font-medium checkout-color-text mr-2 text-base">Order code:</span>
                                        <span class="checkout-color-unactive font-medium text-base"><?= $value->order_code ?></span>
                                        <input type="text" value="<?= $value->order_code ?>" id="myInput_in_progress<?= $value->order_code ?>" style="display: none">
                                        <img class="w-5 h-auto ml-2" style="cursor: pointer;" onclick="myFunction('myInput_in_progress<?= $value->order_code ?>')" src="<?= $url ?>/dist/img/copy.svg" alt="">
                                    </div>
                                    <div class="text-base checkout-color-unactive font-medium flex items-center col-span-4 float-left">
                                        <svg class="mx-4" xmlns="http://www.w3.org/2000/svg" width="4" height="4" viewBox="0 0 4 4" fill="none">
                                            <circle cx="2" cy="2" r="2" fill="#D9D9D9"></circle>
                                        </svg>
                                        <span class="checkout-color-unactive font-medium text-base">Deliver</span>
                                        <svg class="mx-4" xmlns="http://www.w3.org/2000/svg" width="4" height="4" viewBox="0 0 4 4" fill="none">
                                            <circle cx="2" cy="2" r="2" fill="#D9D9D9"></circle>
                                        </svg>
                                        <span class="checkout-color-unactive font-medium"><?= $countProduct ?> products</span>
                                        <svg class="mx-4" xmlns="http://www.w3.org/2000/svg" width="4" height="4" viewBox="0 0 4 4" fill="none">
                                            <circle cx="2" cy="2" r="2" fill="#D9D9D9"></circle>
                                        </svg>
                                    </div>
                                    <div class="grid grid-cols-2 col-span-6 float-left">
                                        <a href="/pdf/?order_code=<?= $value->order_code ?>" target="_blank" class="flex items-center text-base checkout-color-unactive font-medium text-base">Receipt <img class="w-3 ml-2" src="<?= $url ?>/dist/img/receipt.svg" alt=""></a>
                                    </div>
                                    <div class="badge <?= $class ?> col-span-2 text-center px-2 py-1 font-medium ml-auto order-2 text-sm float-right">
                                        <?= $name ?>
                                    </div>
                                </div>
                                <?php foreach ($decodedData as $key => $item):
                                    $idPro = $item['id'];
                                ?>
                                    <div class="pt-6 pb-3 px-8 grid grid-cols-8 gap-5" x-data="{ currentVal: 1, minVal: 0, maxVal: 10, decimalPoints: 0, incrementAmount: 1, price: 96 }">
                                        <div class="2xl:col-span-1 xl:col-span-1 lg:col-span-1 sm:col-span-1 col-span-8 flex items-center border rounded-xl p-0 w-h-100 p-1">
                                            <img class="w-full" src="<?= $item['img'] ?>" alt="">
                                        </div>
                                        <div class="2xl:col-span-6 xl:col-span-6 lg:col-span-6 sm:col-span-5 col-span-6 grid grid-cols-11 flex">
                                            <div class="2xl:col-span-8 xl:col-span-6 lg:col-span-11 col-span-11 mb-0 mb-pr-2">
                                                <h5 class="font-medium leading-6 text-base checkout-color-text my-0 block title-pr text-left">
                                                    <?= $item['title'] ?>
                                                </h5>
                                                <?php if ($item['pack']): ?>
                                                    <div class="tag mt-2 flex">
                                                        <span class="gap-2 rounded-lg checkout-bg py-2 px-3 w-auto text-sm checkout-tag-color">
                                                            Type: <?= $item['pack'] ?> Pack
                                                        </span>
                                                    </div>
                                                <?php endif ?>
                                            </div>
                                            <div class="2xl:col-span-3 xl:col-span-5 lg:col-span-11 col-span-11 flex items-center xl:justify-end lg:justify-end md:justify-start">
                                                <div class="flex flex-col gap-1 justify-center items-center">
                                                    <div class="flex items-center justify-start color-vector">
                                                        Quantity:<span><?= $item['qty'] ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="2xl:col-span-1 xl:col-span-1 lg:col-span-1 sm:col-span-1 col-span-2 flex items-center justify-end">
                                            <div class="text-right"><span class="color-vector font-medium text-lg">$<?= $item['price'] ?></span></div>
                                        </div>
                                    </div>
                                <?php endforeach ?>
                                <div class="flex px-8 flex items-center">
                                    <div>
                                        <a href="<?= home_url() ?>/order-detail/?order_code=<?= $value->order_code ?>" class="flex items-center no-underline">
                                            <span class="text-base font-semibold blue-sure">View details</span>
                                            <img class="ml-2 w-6" src="<?= $url ?>/dist/img/arrow_right_blue.png" alt="">
                                        </a>
                                    </div>
                                    <div class="order-2 flex items-center ml-auto">
                                        <span class="text-base checkout-color-unactive">Total:&nbsp;</span>
                                        <span class="font-medium text-2xl checkout-color-red-text">$<?= $value->price_payment ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div> -->
                    <div x-show="selectedTab === 'completed'" id="tabpanelSaved" role="tabpanel" aria-label="completed">
                        <?php foreach ($on_the_road as $key => $value) :
                            $get_newest_order = $wpdb->get_results("SELECT * FROM wp_orders WHERE order_code = '{$value->order_code}' ORDER BY id DESC");
                            $dataProduct = $get_newest_order[0]->dataproduct;
                            $decodedData = json_decode(str_replace('\\', "", $dataProduct), true);
                            $countProduct = count($decodedData);

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
                        ?>
                            <div class="pt-4 pb-6 mb-6 bg-white rounded-xl">
                                <div class="items-center md:px-8 px-2 bd-line-bottom pb-4 block w-full overflow-hidden">
                                    <div class="text-base font-medium color-vector flex items-center float-left">
                                        <span class="text-base"><?= date('m/d/Y', $value->time_order) ?></span>
                                        <svg class="mx-4" xmlns="http://www.w3.org/2000/svg" width="4" height="4" viewBox="0 0 4 4" fill="none">
                                            <circle cx="2" cy="2" r="2" fill="#D9D9D9"></circle>
                                        </svg>
                                    </div>
                                    <div class="flex items-center float-left">
                                        <span class="text-base font-medium checkout-color-text mr-2 text-base">Order code:</span>
                                        <span class="checkout-color-unactive font-medium text-base"><?= $value->order_code ?></span>
                                        <input type="text" value="<?= $value->order_code ?>" id="myInput_completed<?= $value->order_code ?>" style="display: none">
                                        <img class="w-5 h-auto ml-2" style="cursor: pointer;" onclick="myFunction('myInput_completed<?= $value->order_code ?>')" src="<?= $url ?>/dist/img/copy.svg" alt="">
                                    </div>
                                    <div class="text-base checkout-color-unactive font-medium flex items-center col-span-4 float-left">
                                        <svg class="mx-4" xmlns="http://www.w3.org/2000/svg" width="4" height="4" viewBox="0 0 4 4" fill="none">
                                            <circle cx="2" cy="2" r="2" fill="#D9D9D9"></circle>
                                        </svg>
                                        <span class="checkout-color-unactive font-medium text-base">Deliver</span>
                                        <svg class="mx-4" xmlns="http://www.w3.org/2000/svg" width="4" height="4" viewBox="0 0 4 4" fill="none">
                                            <circle cx="2" cy="2" r="2" fill="#D9D9D9"></circle>
                                        </svg>
                                        <span class="checkout-color-unactive font-medium"><?= $countProduct ?> products</span>
                                        <svg class="mx-4" xmlns="http://www.w3.org/2000/svg" width="4" height="4" viewBox="0 0 4 4" fill="none">
                                            <circle cx="2" cy="2" r="2" fill="#D9D9D9"></circle>
                                        </svg>
                                    </div>
                                    <div class="grid grid-cols-2 col-span-6 float-left">
                                        <a href="/pdf/?order_code=<?= $value->order_code ?>" target="_blank" class="flex items-center text-base checkout-color-unactive font-medium text-base">Receipt <img class="w-3 ml-2" src="<?= $url ?>/dist/img/receipt.svg" alt=""></a>
                                    </div>
                                    <div class="badge <?= $class ?> col-span-2 text-center px-2 py-1 font-medium ml-auto order-2 text-sm float-right">
                                        <?= $name ?>
                                    </div>
                                </div>
                                <?php foreach ($decodedData as $key => $item):
                                    $idPro = $item['id'];
                                ?>
                                    <div class="pt-6 pb-3 px-8 grid grid-cols-8 gap-5" x-data="{ currentVal: 1, minVal: 0, maxVal: 10, decimalPoints: 0, incrementAmount: 1, price: 96 }">
                                        <div class="2xl:col-span-1 xl:col-span-1 lg:col-span-1 sm:col-span-1 col-span-8 flex items-center border rounded-xl h-fit p-1">
                                            <img class="w-full" src="<?= $item['img'] ?>" alt="">
                                        </div>
                                        <div class="2xl:col-span-6 xl:col-span-6 lg:col-span-6 sm:col-span-5 col-span-6 grid grid-cols-11 flex">
                                            <div class="2xl:col-span-8 xl:col-span-6 lg:col-span-11 col-span-11 mb-0 mb-pr-2">
                                                <h5 class="font-medium leading-6 text-base checkout-color-text my-0 block title-pr text-left">
                                                    <?= $item['title'] ?>
                                                </h5>
                                                <?php if ($item['pack']): ?>
                                                    <div class="tag mt-2 flex">
                                                        <span class="gap-2 rounded-lg checkout-bg py-2 px-3 w-auto text-sm checkout-tag-color">
                                                            Type: <?= $item['pack'] ?> Pack
                                                        </span>
                                                    </div>
                                                <?php endif ?>
                                            </div>
                                            <div class="2xl:col-span-3 xl:col-span-5 lg:col-span-11 col-span-11 flex items-center xl:justify-end lg:justify-end md:justify-start">
                                                <div class="flex flex-col gap-1 justify-center items-center">
                                                    <div class="flex items-center justify-start color-vector">
                                                        Quantity:<span><?= $item['qty'] ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="2xl:col-span-1 xl:col-span-1 lg:col-span-1 sm:col-span-1 col-span-2 flex items-center justify-end">
                                            <div class="text-right"><span class="color-vector font-medium text-lg">$<?= $item['price'] ?></span></div>
                                        </div>
                                    </div>
                                <?php endforeach ?>
                                <div class="flex px-8 flex items-center">
                                    <div>
                                        <a href="<?= home_url() ?>/order-detail/?order_code=<?= $value->order_code ?>" class="flex items-center no-underline">
                                            <span class="text-base font-semibold blue-sure">View details</span>
                                            <img class="ml-2 w-6" src="<?= $url ?>/dist/img/arrow_right_blue.png" alt="">
                                        </a>
                                    </div>
                                    <div class="order-2 flex items-center ml-auto">
                                        <span class="text-base checkout-color-unactive">Total:&nbsp;</span>
                                        <span class="font-medium text-2xl checkout-color-red-text">$<?= $value->price_payment ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div x-show="selectedTab === 'canceled'" id="tabpanelSaved" role="tabpanel" aria-label="canceled">
                        <?php foreach ($delivered as $key => $value) :
                            $get_newest_order = $wpdb->get_results("SELECT * FROM wp_orders WHERE order_code = '{$value->order_code}' ORDER BY id DESC");
                            $dataProduct = $get_newest_order[0]->dataproduct;
                            $decodedData = json_decode(str_replace('\\', "", $dataProduct), true);
                            $countProduct = count($decodedData);

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
                        ?>
                            <div class="pt-4 pb-6 mb-6 bg-white rounded-xl">
                                <div class="items-center md:px-8 px-2 bd-line-bottom pb-4 block w-full overflow-hidden">
                                    <div class="text-base font-medium color-vector flex items-center float-left">
                                        <span class="text-base"><?= date('m/d/Y', $value->time_order) ?></span>
                                        <svg class="mx-4" xmlns="http://www.w3.org/2000/svg" width="4" height="4" viewBox="0 0 4 4" fill="none">
                                            <circle cx="2" cy="2" r="2" fill="#D9D9D9"></circle>
                                        </svg>
                                    </div>
                                    <div class="flex items-center float-left">
                                        <span class="text-base font-medium checkout-color-text mr-2 text-base">Order code:</span>
                                        <span class="checkout-color-unactive font-medium text-base"><?= $value->order_code ?></span>
                                        <input type="text" value="<?= $value->order_code ?>" id="myInput_canceled<?= $value->order_code ?>" style="display: none">
                                        <img class="w-5 h-auto ml-2" style="cursor: pointer;" onclick="myFunction('myInput_canceled<?= $value->order_code ?>')" src="<?= $url ?>/dist/img/copy.svg" alt="">
                                    </div>
                                    <div class="text-base checkout-color-unactive font-medium flex items-center col-span-4 float-left">
                                        <svg class="mx-4" xmlns="http://www.w3.org/2000/svg" width="4" height="4" viewBox="0 0 4 4" fill="none">
                                            <circle cx="2" cy="2" r="2" fill="#D9D9D9"></circle>
                                        </svg>
                                        <span class="checkout-color-unactive font-medium text-base">Deliver</span>
                                        <svg class="mx-4" xmlns="http://www.w3.org/2000/svg" width="4" height="4" viewBox="0 0 4 4" fill="none">
                                            <circle cx="2" cy="2" r="2" fill="#D9D9D9"></circle>
                                        </svg>
                                        <span class="checkout-color-unactive font-medium"><?= $countProduct ?> products</span>
                                        <svg class="mx-4" xmlns="http://www.w3.org/2000/svg" width="4" height="4" viewBox="0 0 4 4" fill="none">
                                            <circle cx="2" cy="2" r="2" fill="#D9D9D9"></circle>
                                        </svg>
                                    </div>
                                    <div class="grid grid-cols-2 col-span-6 float-left">
                                        <a href="/pdf/?order_code=<?= $value->order_code ?>" target="_blank" class="flex items-center text-base checkout-color-unactive font-medium text-base">Receipt <img class="w-3 ml-2" src="<?= $url ?>/dist/img/receipt.svg" alt=""></a>
                                    </div>
                                    <div class="badge <?= $class ?> col-span-2 text-center px-2 py-1 font-medium ml-auto order-2 text-sm float-right">
                                        <?= $name ?>
                                    </div>
                                </div>
                                <?php foreach ($decodedData as $key => $item):
                                    $idPro = $item['id'];
                                ?>
                                    <div class="pt-6 pb-3 px-8 grid grid-cols-8 gap-5" x-data="{ currentVal: 1, minVal: 0, maxVal: 10, decimalPoints: 0, incrementAmount: 1, price: 96 }">
                                        <div class="2xl:col-span-1 xl:col-span-1 lg:col-span-1 sm:col-span-1 col-span-8 flex items-center border rounded-xl h-fit p-1">
                                            <img class="w-full" src="<?= $item['img'] ?>" alt="">
                                        </div>
                                        <div class="2xl:col-span-6 xl:col-span-6 lg:col-span-6 sm:col-span-5 col-span-6 grid grid-cols-11 flex">
                                            <div class="2xl:col-span-8 xl:col-span-6 lg:col-span-11 col-span-11 mb-0 mb-pr-2">
                                                <h5 class="font-medium leading-6 text-base checkout-color-text my-0 block title-pr text-left">
                                                    <?= $item['title'] ?>
                                                </h5>
                                                <?php if ($item['pack']): ?>
                                                    <div class="tag mt-2 flex">
                                                        <span class="gap-2 rounded-lg checkout-bg py-2 px-3 w-auto text-sm checkout-tag-color">
                                                            Type: <?= $item['pack'] ?> Pack
                                                        </span>
                                                    </div>
                                                <?php endif ?>
                                            </div>
                                            <div class="2xl:col-span-3 xl:col-span-5 lg:col-span-11 col-span-11 flex items-center xl:justify-end lg:justify-end md:justify-start">
                                                <div class="flex flex-col gap-1 justify-center items-center">
                                                    <div class="flex items-center justify-start color-vector">
                                                        Quantity:<span><?= $item['qty'] ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="2xl:col-span-1 xl:col-span-1 lg:col-span-1 sm:col-span-1 col-span-2 flex items-center justify-end">
                                            <div class="text-right"><span class="color-vector font-medium text-lg">$<?= $item['price'] ?></span></div>
                                        </div>
                                    </div>
                                <?php endforeach ?>
                                <div class="flex px-8 flex items-center">
                                    <div>
                                        <a href="<?= home_url() ?>/order-detail/?order_code=<?= $value->order_code ?>" class="flex items-center no-underline">
                                            <span class="text-base font-semibold blue-sure">View details</span>
                                            <img class="ml-2 w-6" src="<?= $url ?>/dist/img/arrow_right_blue.png" alt="">
                                        </a>
                                    </div>
                                    <div class="order-2 flex items-center ml-auto">
                                        <span class="text-base checkout-color-unactive">Total:&nbsp;</span>
                                        <span class="font-medium text-2xl checkout-color-red-text">$<?= $value->price_payment ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="popup-modal1" tabindex="-1" class="pp-checkout hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative w-full max-w-3xl max-h-full">
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700 py-10 px-6">
            <p class="text-xl mt-0 mb-6 text-center text-32 font-bold signup-color-1 line-height-140">Welcome to SureMeal's Dealers Platform</p>
            <p class="text-center text-base mb-8 signup-color-1 font-normal">Enjoy exclusive pricing available only for dealers! Join us today. To become one of our dealers, simply sign up now and start benefiting from these fantastic deals.</p>
            <div class="sm:flex d-none items-center justify-center gird grid-cols-2 gap-8">
                <a href="sign-up.html" class="btn-pp-checkout text-white border-red font-semibold rounded-lg text-sm w-full sm:w-auto px-20 py-3 text-center no-underline checkout-color-red-text">Sign up now</a>
                <a href="" class="btn-pp-checkout text-white bg-red-sure border-none hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-semibold rounded-lg text-sm w-full sm:w-auto px-20 py-3 text-center dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800 no-underline">Maybe later</a>
            </div>
        </div>
    </div>
</div>

<!-- modal add gift -->
<!-- <div id="ModalAddGif" class="modal-wrapper" style="display: flex;"> -->
<div id="ModalAddGif" class="modal-wrapper" style="display: none;">
    <div class="modal-container w-640">
        <div class="modal-head">
            <div class="w-full flex justify-center gap-6">
                <h2 class="text-heading-h5 text-[#373A51]">
                    Enter Shipping Address
                </h2>
                <div class="close-modal">
                    <figure class="w-6 h-6"><img src="<?= $url ?>/assets/image/icon/close-24.svg" alt="close">
                    </figure>
                </div>
            </div>
        </div>
        <div class="modal-content">
            <form action="" id="review-product" class="flex flex-col gap-3">
                <label class="input-label">
                    <p class="input-title">Name
                        <!-- <span class="text-primary">*</span> -->
                    </p>
                    <input type="text" class="input-field" name="userName" placeholder="Your name
                            ">
                </label>
                <label class="input-label">
                    <p class="input-title">Email
                        <!-- <span class="text-primary">*</span> -->
                    </p>
                    <input type="text" class="input-field" name="email" placeholder="Your email
                            ">
                </label>


                <div class="flex flex-col lg:flex-row gap-5">
                    <label class="input-label">
                        <p class="input-title">Phone Number
                            <!-- <span class="text-primary">*</span> -->
                        </p>
                        <input type="text" class="input-field" name="phone" placeholder="Phone number
                            ">
                    </label>
                    <label class="input-label">
                        <p class="input-title">Phone Number
                            <!-- <span class="text-primary">*</span> -->
                        </p>
                        <select class="input-field" name="country">
                            <option value="1">United States</option>
                            <option value="2">United States 1</option>
                            <option value="3">United States 2</option>
                        </select>
                    </label>
                </div>

                <label class="input-label">
                    <p class="input-title">Address
                        <!-- <span class="text-primary">*</span> -->
                    </p>
                    <input type="text" class="input-field" name="address" placeholder="Street address
                        ">
                </label>

                <div class="flex flex-col lg:flex-row gap-3">
                    <label class="input-label">
                        <p class="input-title">City
                            <!-- <span class="text-primary">*</span> -->
                        </p>
                        <input type="text" class="input-field" name="city" placeholder="City Name
                        ">
                    </label>
                    <label class="input-label">
                        <p class="input-title">State
                            <!-- <span class="text-primary">*</span> -->
                        </p>
                        <select class="input-field" name="country" placeholder="Choose state">
                            <option value=" 1">Us</option>
                            <option value="2">Us 1</option>
                            <option value="3">Us 2</option>
                        </select>
                    </label>
                    <label class="input-label">
                        <p class="input-title">Zip code
                            <!-- <span class="text-primary">*</span> -->
                        </p>
                        <input type="number" class="input-field" name="zipcode" placeholder="Zip code
                            ">
                    </label>
                </div>

                <button type="submit" class="mt-3 button bg-primary text-body-md-semibold text-white"
                    style="width: 100%">Submit information</button>
            </form>
        </div>
    </div>
</div>

<!-- modal gif success -->
<!-- <div id="ModalAddGifSuccess" class="modal-wrapper" style="display: flex;"> -->
<div id="ModalAddGifSuccess" class="modal-wrapper" style="display: none;">
    <div class="modal-container" style="max-width: 450px;">
        <!-- <div class="modal-head">
            <div class="w-full flex justify-center gap-6">
                <h2 class="text-heading-h5 text-[#373A51]">
                    Enter Shipping Address
                </h2>
                <div class="close-modal">
                    <figure class="w-6 h-6"><img src="<?= $url ?>/assets/image/icon/close-24.svg" alt="close">
                    </figure>
                </div>
            </div>
        </div> -->
        <div class="modal-content">
            <div class="flex flex-col">
                <figure class="min-w-16 h-16">
                    <svg xmlns="http://www.w3.org/2000/svg" width="64" height="65" viewBox="0 0 64 65" fill="none">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M32.0026 59.1666C46.7301 59.1666 58.6693 47.2274 58.6693 32.5C58.6693 17.7724 46.7301 5.83331 32.0026 5.83331C17.275 5.83331 5.33594 17.7724 5.33594 32.5C5.33594 47.2274 17.275 59.1666 32.0026 59.1666ZM44.2479 25.728C44.9261 24.8561 44.769 23.5995 43.8973 22.9214C43.0253 22.2432 41.7687 22.4003 41.0906 23.2722L30.4053 37.0104C30.1677 37.316 29.7207 37.3557 29.433 37.0968L22.6739 31.0136C21.8529 30.2746 20.5883 30.341 19.8494 31.1621C19.1104 31.9832 19.177 33.2477 19.998 33.9866L26.7573 40.0698C28.7709 41.8821 31.8994 41.6048 33.5626 39.4661L44.2479 25.728Z" fill="#04D14A" />
                    </svg>
                </figure>

                <div class="flex flex-col items-center justify-center gap-2 mt-4">
                    <p class="text-body-lg-semibold text-[#000] text-center">Your gift has been successfully redeemed!</p>
                    <p class="text-body-sm-regular text-[#000] text-center">We sincerely appreciate your support. Your reward is on its way and will be delivered to you shortly.</p>
                </div>

                <button type="submit" class="mt-6 button bg-primary text-body-md-semibold text-white"
                    style="width: 100%">Back to Dashboard</button>
            </div>
        </div>
    </div>
</div>
<?php
get_footer('dealer');
?>
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

<script defer src="<?= $url ?>/assets/js/modal.js"></script>