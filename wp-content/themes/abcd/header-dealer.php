<?php
$url = get_template_directory_uri();
$home_url = home_url();
function is_active($link) {
    global $actual_link;
    $normalized_actual_link = rtrim(parse_url($actual_link, PHP_URL_PATH), '/');
    $normalized_link = rtrim(parse_url($link, PHP_URL_PATH), '/');
    return $normalized_actual_link === $normalized_link;
}

$id_resller = isset($_GET['distribution_code']) ? $_GET['distribution_code'] : '';
if ($id_resller) {
    setcookie("distribution_code", $id_resller, time() + (86400 * 3), "/"); // cookie lưu 3 ngày
}
?>
<!DOCTYPE html>
<html lang="">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard - SureMeal</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="<?= $url ?>/dist/build/tailwind.css">
    <link rel="stylesheet" type="text/css" href="<?= $url ?>/dist/build/style.css">
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.js" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.js" defer></script>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
    <!-- CSS -->
    <link rel="stylesheet" href="https://unpkg.com/flickity@2/dist/flickity.min.css">
    <!-- JavaScript -->
    <script src="https://unpkg.com/flickity@2/dist/flickity.pkgd.min.js"></script>
    <style>
        @import url(https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/5.3.45/css/materialdesignicons.min.css);
        @import url(https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.css);
    </style>
    <style>
        #chartjs-tooltip {
            background: transparent url("img/bgvector.png") no-repeat top center;
            opacity: 0;
            font-size: 12px;
            z-index: 100;
            width: 147px;
            height: 150px;
            border: none !important;
            box-shadow: 0 0 0 -10px black;
        }
        #chartjs-tooltip table td{
            border: none !important;
        }
    </style>
</head>
<body>
<!-- <div class="bg-white bd-line-bottom">
    <div class="relative isolate flex min-h-svh w-full bg-white max-lg:flex-col lg:bg-zinc-100 dark:bg-zinc-900 dark:lg:bg-zinc-950">
        <div class="items-center justify-center bd-line-right w-262 fixed inset-y-0 left-0 bg-white hidden lg:block">
            <a class="pb-4 pt-4 px-4 w-full block text-center" aria-label="Home" href="/"><img class="" src="<?= $url ?>/dist/img/logo2.svg" alt=""></a>
            <div class="flex items-start grid bg-white md:height-full h-auto">
                <ul class="list-none pl-0 text-left mt-0 memubar" x-data="{ top: false }">
                    <li class="flex items-center py-5 px-6 item-f dashboard <?= is_active($home_url) ? 'checkout-menu-active' : ''; ?>">
                        <a class="flex items-center no-underline" href="<?= $home_url ?>"><img class="mr-3 w-6 default" src="<?= $url ?>/dist/img/cuida.svg" alt=""><img class="mr-3 w-6 active hidden" src="<?= $url ?>/dist/img/cuida_ac.svg" alt=""><span class="menu-item text-base font-medium">Dashboard</span></a>
                    </li>
                    <li class="flex items-center py-5 px-6 item-f my-order <?= is_active($home_url . '/my-order') ? 'checkout-menu-active' : ''; ?>">
                        <a class="flex items-center no-underline menu-item" href="<?= $home_url ?>/my-order"><img class="mr-3 w-6 default hidden" src="<?= $url ?>/dist/img/cart.svg" alt=""><img class="mr-3 w-6 active" src="<?= $url ?>/dist/img/cart_ac.svg " alt=""><span class="menu-item text-base font-medium">My orders</span></a>
                    </li>
                    <li class="flex items-center py-5 px-6 item-f management <?= is_active($home_url . '/affiliate-orders') ? 'checkout-menu-active' : ''; ?>">
                        <a class="flex items-center no-underline menu-item" href="<?= $home_url ?>/affiliate-orders"><img class="mr-3 w-6 default" src="<?= $url ?>/dist/img/tabler_shopping-bag-discount.svg" alt=""><img class="mr-3 w-6 active hidden" src="<?= $url ?>/dist/img/tabler_shopping-bag-discount_ac.svg" alt=""><span class="menu-item text-base font-medium">Affiliate orders</span></a>
                    </li>
                    <li class="flex items-center py-5 px-6 item-f management <?= is_active($home_url . '/affiliate-customization') ? 'checkout-menu-active' : ''; ?>">
                        <a class="flex items-center no-underline menu-item" href="<?= $home_url ?>/affiliate-customization"><img class="mr-3 w-6 default" src="<?= $url ?>/dist/img/material-symbols_link.svg" alt=""><img class="mr-3 w-6 hidden active" src="<?= $url ?>/dist/img/material-symbols_link_ac.svg" alt=""><span class="menu-item text-base font-medium">Affiliate link<br>customization</span></a>
                    </li>
                    <li class="flex items-center py-5 px-6 item-f management <?= is_active($home_url . '/my-offer') ? 'checkout-menu-active' : ''; ?>">
                        <a class="flex items-center no-underline menu-item" href="<?= $home_url ?>/my-offer"><img class="mr-3 w-6 default" src="<?= $url ?>/dist/img/lineicons_offer.svg" alt=""><img class="mr-3 w-6 hidden active" src="<?= $url ?>/dist/img/lineicons_offer.svg" alt=""><span class="menu-item text-base font-medium">My offer</span></a>
                    </li>
                    <li class="flex items-center py-5 px-6 item-f management  <?= is_active($home_url . '/point-management') ? 'checkout-menu-active' : ''; ?>">
                        <a class="flex items-center no-underline menu-item" href="<?= $home_url ?>/point-management"><img class="mr-3 w-6 default" src="<?= $url ?>/dist/img/reward.svg" alt=""><img class="mr-3 w-6 hidden active" src="<?= $url ?>/dist/img/reward_ac.svg" alt=""><span class="menu-item text-base font-medium">Point management</span></a>
                    </li>
                    <li class="py-5 pl-6 pr-4 item-f account" >
                        <div class="flex items-center no-underline w-full" href="" x-on:click="top = ! top"><img class="mr-3 w-6 default" src="<?= $url ?>/dist/img/circle.svg" alt=""><img class="mr-3 w-6 active hidden" src="<?= $url ?>/dist/img/circle_ac.svg" alt="">
                            <span class="menu-item cursor-pointer text-base font-medium">Account<br>information</span>
                            <div class="order-2 ml-auto">
                                <img class="w-6" :src="top ? '<?= $url ?>/dist/img/icon_up.svg' : '<?= $url ?>/dist/img/icon_down.svg'"  alt="">
                            </div>
                        </div>
                    </li>
                    <li class="pl-14">
                        <ul class="list-none" x-show="top">
                            <li class="mt-2">
                                <a class="no-underline text-sm line-height-24" href="<?= $home_url ?>/personal-info">Personal information</a>
                            </li>
                            <li class="mt-2">
                                <a class="no-underline text-sm line-height-24" href="<?= $home_url ?>/business-information">Business information</a>
                            </li>
                            <li class="mt-2">
                                <a class="no-underline text-sm line-height-24" href="<?= $home_url ?>/bank-account-information">Bank account information</a>
                            </li>
                        </ul>
                    </li>

                </ul>
            </div>
        </div>
        <div class="flex flex-1 flex-col pb-2 lg:min-w-0 pl-none lg:pl-262 bg-content">
            <div class="text-left bg-white lg:hidden" x-data="{ menu: false }">
                <a class="p-4 w-full block" aria-label="Home" href="/">
                    <img class="" src="<?= $url ?>/dist/img/logo2.png" alt="">
                </a>
                <div class="absolute right-2 top-5 open-menu" x-on:click="menu = ! menu">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M20.75 7C20.75 7.41421 20.4142 7.75 20 7.75L4 7.75C3.58579 7.75 3.25 7.41421 3.25 7C3.25 6.58579 3.58579 6.25 4 6.25L20 6.25C20.4142 6.25 20.75 6.58579 20.75 7Z" fill="#000"/>
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M20.75 12C20.75 12.4142 20.4142 12.75 20 12.75L4 12.75C3.58579 12.75 3.25 12.4142 3.25 12C3.25 11.5858 3.58579 11.25 4 11.25L20 11.25C20.4142 11.25 20.75 11.5858 20.75 12Z" fill="#000"/>
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M20.75 17C20.75 17.4142 20.4142 17.75 20 17.75L4 17.75C3.58579 17.75 3.25 17.4142 3.25 17C3.25 16.5858 3.58579 16.25 4 16.25L20 16.25C20.4142 16.25 20.75 16.5858 20.75 17Z" fill="#000"/>
                    </svg>
                </div>
                <div class="menu bg-white fixed right-0 top-0 w-2/3 transform duration-300 ease-in-out min-h-screen transition-transform z-50 shadow-1 py-4 p-0" x-show="menu">
                    <div class="close-menu text-right pb-2 block w-full pr-2" x-on:click="menu = ! menu">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                            <path d="M19 19L5 5" stroke="#121219" stroke-width="2" stroke-linecap="round"/>
                            <path d="M19 5L5 19" stroke="#121219" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                    </div>
                    <ul class="list-none pl-0 text-left mt-0 memubar menu-min-height" x-data="{ top: false }">
                        <li class="flex items-center py-5 px-6 item-f dashboard <?= is_active($home_url) ? 'checkout-menu-active' : ''; ?>">
                            <a class="flex items-center no-underline" href="<?= $home_url ?>"><img class="mr-3 w-6 default" src="<?= $url ?>/dist/img/cuida.svg" alt=""><img class="mr-3 w-6 active hidden" src="<?= $url ?>/dist/img/cuida_ac.svg" alt=""><span class="menu-item text-base font-medium">Dashboard</span></a>
                        </li>
                        <li class="flex items-center py-5 px-6 item-f my-order <?= is_active($home_url . '/my-order') ? 'checkout-menu-active' : ''; ?>">
                            <a class="flex items-center no-underline menu-item" href="<?= $home_url ?>/my-order"><img class="mr-3 w-6 default hidden" src="<?= $url ?>/dist/img/cart.svg" alt=""><img class="mr-3 w-6 active" src="<?= $url ?>/dist/img/cart_ac.svg " alt=""><span class="menu-item text-base font-medium">My orders</span></a>
                        </li>
                        <li class="flex items-center py-5 px-6 item-f management <?= is_active($home_url . '/affiliate-orders') ? 'checkout-menu-active' : ''; ?>">
                            <a class="flex items-center no-underline menu-item" href="<?= $home_url ?>/affiliate-orders"><img class="mr-3 w-6 default" src="<?= $url ?>/dist/img/tabler_shopping-bag-discount.svg" alt=""><img class="mr-3 w-6 active hidden" src="<?= $url ?>/dist/img/tabler_shopping-bag-discount_ac.svg" alt=""><span class="menu-item text-base font-medium">Affiliate orders</span></a>
                        </li>
                        <li class="flex items-center py-5 px-6 item-f management <?= is_active($home_url . '/affiliate-customization') ? 'checkout-menu-active' : ''; ?>">
                            <a class="flex items-center no-underline menu-item" href="<?= $home_url ?>/affiliate-customization"><img class="mr-3 w-6 default" src="<?= $url ?>/dist/img/material-symbols_link.svg" alt=""><img class="mr-3 w-6 hidden active" src="<?= $url ?>/dist/img/material-symbols_link_ac.svg" alt=""><span class="menu-item text-base font-medium">Affiliate link<br>customization</span></a>
                        </li>
                        <li class="flex items-center py-5 px-6 item-f management <?= is_active($home_url . '/my-offer') ? 'checkout-menu-active' : ''; ?>">
                            <a class="flex items-center no-underline menu-item" href="<?= $home_url ?>/my-offer"><img class="mr-3 w-6 default" src="<?= $url ?>/dist/img/lineicons_offer.svg" alt=""><img class="mr-3 w-6 hidden active" src="<?= $url ?>/dist/img/lineicons_offer.svg" alt=""><span class="menu-item text-base font-medium">My offer</span></a>
                        </li>
                        <li class="flex items-center py-5 px-6 item-f management  <?= is_active($home_url . '/point-management') ? 'checkout-menu-active' : ''; ?>">
                            <a class="flex items-center no-underline menu-item" href="<?= $home_url ?>/point-management"><img class="mr-3 w-6 default" src="<?= $url ?>/dist/img/reward.svg" alt=""><img class="mr-3 w-6 hidden active" src="<?= $url ?>/dist/img/reward_ac.svg" alt=""><span class="menu-item text-base font-medium">Point management</span></a>
                        </li>
                        <li class="py-5 pl-6 pr-4 item-f account" >
                            <div class="flex items-center no-underline w-full" href="" x-on:click="top = ! top"><img class="mr-3 w-6 default" src="<?= $url ?>/dist/img/circle.svg" alt=""><img class="mr-3 w-6 active hidden" src="<?= $url ?>/dist/img/circle_ac.svg" alt="">
                                <span class="menu-item cursor-pointer text-base font-medium">Account<br>information</span>
                                <div class="order-2 ml-auto">
                                    <img class="w-6" :src="top ? '<?= $url ?>/dist/img/icon_up.svg' : '<?= $url ?>/dist/img/icon_down.svg'"  alt="">
                                </div>
                            </div>
                        </li>
                        <li class="pl-14">
                            <ul class="list-none" x-show="top">
                                <li class="mt-2">
                                    <a class="no-underline text-sm line-height-24" href="<?= $home_url ?>/personal-info">Personal information</a>
                                </li>
                                <li class="mt-2">
                                    <a class="no-underline text-sm line-height-24" href="<?= $home_url ?>/business-information">Business information</a>
                                </li>
                                <li class="mt-2">
                                    <a class="no-underline text-sm line-height-24" href="<?= $home_url ?>/bank-account-information">Bank account information</a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="flex items-center justify-between sm:px-8 px-4 py-2 sm:py-6 bd-line-bottom bg-white">
                <div class="pl-0 relative flex items-center 2xl:col-span-9 xl:col-span-9 md:col-span-8 sm:col-span-6 col-span-6">
                    <div class="text-28 font-semibold text-18-sm">Hi, <span class="blue-sure">Duong Nguyen</span></div>
                </div>
                <div class="flex justify-end items-center order-2 gap-2 2xl:col-span-3 xl:col-span-3 md:col-span-4 sm:col-span-6 col-span-6">
                    <img class="mr-3" src="<?= $url ?>/dist/img/avatar.png" alt=""><span class="chart-font-size color-pr text-14-sm">Nguyen Duong</span>
                </div>
            </div> -->