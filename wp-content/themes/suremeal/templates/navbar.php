<?php
$actual_link = (empty($_SERVER['HTTPS']) ? 'http' : 'https') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$home_url = rtrim(home_url(), '/');

// Function to normalize and compare URLs
function is_active($link) {
    global $actual_link;
    $normalized_actual_link = rtrim(parse_url($actual_link, PHP_URL_PATH), '/');
    $normalized_link = rtrim(parse_url($link, PHP_URL_PATH), '/');
    return $normalized_actual_link === $normalized_link;
}
?>
<ul class="list-none pl-0 text-left mt-0 memubar menu-min-height" x-data="{ top: false }">
    <li class="flex items-center py-5 px-6 item-f dashboard <?= is_active($home_url) ? 'checkout-menu-active' : ''; ?>">
        <a class="flex items-center no-underline" href="<?= $home_url ?>"><img class="mr-3 w-6 default" src="img/cuida.svg" alt=""><img class="mr-3 w-6 active hidden" src="img/cuida_ac.svg" alt=""><span class="menu-item text-base font-medium">Dashboard</span></a>
    </li>
    <li class="flex items-center py-5 px-6 item-f my-order <?= is_active($home_url . '/my-order') ? 'checkout-menu-active' : ''; ?>">
        <a class="flex items-center no-underline menu-item" href="<?= $home_url ?>/my-order"><img class="mr-3 w-6 default hidden" src="img/cart.svg" alt=""><img class="mr-3 w-6 active" src="img/cart_ac.svg " alt=""><span class="menu-item text-base font-medium">My orders</span></a>
    </li>
    <li class="flex items-center py-5 px-6 item-f management <?= is_active($home_url . '/affiliate-orders') ? 'checkout-menu-active' : ''; ?>">
        <a class="flex items-center no-underline menu-item" href="<?= $home_url ?>/affiliate-orders"><img class="mr-3 w-6 default" src="img/tabler_shopping-bag-discount.svg" alt=""><img class="mr-3 w-6 active hidden" src="img/tabler_shopping-bag-discount_ac.svg" alt=""><span class="menu-item text-base font-medium">Affiliate orders</span></a>
    </li>
    <li class="flex items-center py-5 px-6 item-f management <?= is_active($home_url . '/affiliate-customization') ? 'checkout-menu-active' : ''; ?>">
        <a class="flex items-center no-underline menu-item" href="<?= $home_url ?>/affiliate-customization"><img class="mr-3 w-6 default" src="img/material-symbols_link.svg" alt=""><img class="mr-3 w-6 hidden active" src="img/material-symbols_link_ac.svg" alt=""><span class="menu-item text-base font-medium">Affiliate link<br>customization</span></a>
    </li>
    <li class="flex items-center py-5 px-6 item-f management <?= is_active($home_url . '/my-offer') ? 'checkout-menu-active' : ''; ?>">
        <a class="flex items-center no-underline menu-item" href="<?= $home_url ?>/my-offer"><img class="mr-3 w-6 default" src="img/lineicons_offer.svg" alt=""><img class="mr-3 w-6 hidden active" src="img/lineicons_offer.svg" alt=""><span class="menu-item text-base font-medium">My offer</span></a>
    </li>
    <li class="flex items-center py-5 px-6 item-f management <?= is_active($home_url . '/point-management') ? 'checkout-menu-active' : ''; ?>">
        <a class="flex items-center no-underline menu-item" href="<?= $home_url ?>/point-management"><img class="mr-3 w-6 default" src="img/reward.svg" alt=""><img class="mr-3 w-6 hidden active" src="img/reward_ac.svg" alt=""><span class="menu-item text-base font-medium">Point management</span></a>
    </li>
    <li class="py-5 pl-6 pr-4 item-f account" >
        <div class="flex items-center no-underline w-full" href="" x-on:click="top = ! top"><img class="mr-3 w-6 default" src="img/circle.svg" alt=""><img class="mr-3 w-6 active hidden" src="img/circle_ac.svg" alt="">
            <span class="menu-item cursor-pointer text-base font-medium">Account<br>information</span>
            <div class="order-2 ml-auto">
                <img class="w-6" :src="top ? 'img/icon_up.svg' : 'img/icon_down.svg'"  alt="">
            </div>
        </div>
    </li>
    <li class="pl-14">
        <ul class="list-none pl-0" x-show="top">
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