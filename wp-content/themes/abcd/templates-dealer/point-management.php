<?php /* Template Name: Point-Management */ ?>
<?php
$authenticated_user = validate_user_token();
if (!(isset($_COOKIE['user_token']) && $authenticated_user->type == 2)) {
    wp_redirect(home_url());
    exit;
}
$type = $authenticated_user->type;

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
                <li class="flex items-center pt-4 pb-4 pl-6 item-f my-order">
                <a class="flex items-center no-underline menu-item" href="<?= home_url() ?>/dealer-order-info"><img class="mr-3 w-6 default" src="<?= $url ?>/assets/dealer/img/cart.png" alt=""><img class="mr-3 w-6 active hidden" src="<?= $url ?>/assets/dealer/img/cart_ac.png " alt=""><span class="menu-item">My orders</span></a>
                </li>
                <?php if($type == 2): ?>
                    <li class="flex items-center pt-4 pb-4 pl-6 item-f management">
                    <a class="flex items-center no-underline menu-item" href="<?= home_url() ?>/dealer-affiliate-order"><img class="mr-3 w-6 default" src="<?= $url ?>/assets/dealer/img/tabler_shopping-bag-discount.png" alt=""><img class="mr-3 w-6 active hidden" src="<?= $url ?>/assets/dealer/img/tabler_shopping-bag-discount_ac.png" alt=""><span class="menu-item">Affiliate orders</span></a>
                    </li>
                    <li class="flex items-center pt-4 pb-4 pl-6 item-f management">
                    <a class="flex items-center no-underline menu-item" href="<?= home_url() ?>/dealer-affiliate-customization"><img class="mr-3 w-6 default" src="<?= $url ?>/assets/dealer/img/material-symbols_link.png" alt=""><img class="mr-3 w-6 hidden active" src="<?= $url ?>/assets/dealer/img/material-symbols_link_ac.png" alt=""><span class="menu-item">Affiliate link<br>customization</span></a>
                    </li>
                    <li class="flex items-center pt-4 pb-4 pl-6 item-f management checkout-menu-active">
                    <a class="flex items-center no-underline menu-item" href="<?= home_url() ?>/point-management"><img class="mr-3 w-6 active" src="<?= $url ?>/assets/dealer/img/reward_ac.png" alt=""><span class="menu-item">Point management</span></a>
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
                    <?php if($type == 2): ?>
                        <li class="mt-2">
                            <a class="no-underline text-sm" href="<?= home_url() ?>/dealer-bank-informmation">Bank account information</a>
                        </li>
                    <?php endif; ?>
                </ul>
                </li>
            </ul>
        </div>
        <div class="col-span-6 text-center md:p-8 p-2 m-h-content">
            <div class="mx-auto relative w-1192-full overflow-hidden border-cm">
                <div class="mb-6 md:mt-0 mt-6">
                    <h4 class="text-left m-0 color-vector text-2xl font-medium">Current Points Balance</h4>
                </div>
                <div class="bg-white py-5 px-0 rounded-xl">
                <div class="flex items-center grid grid-cols-11 md:mb-8 mb-4 px-5 gap-4">
                    <div class="col-span-12 2xl:col-span-1 md:col-span-2 text-left">
                        <span class="text-32 blue-sure font-semibold">5,000</span>
                    </div>
                    <div class="col-span-12 2xl:col-span-8 md:col-span-6 text-left">
                        <p class="font-semibold text-base m-0 mb-1">Points available</p>
                        <p class="font-normal text-base m-0">400 points will expire on 12/27/2024</p>
                    </div>
                    <div class="col-span-12 2xl:col-span-2 md:col-span-3 sm:text-right text-left">
                        <button type="button" class="text-white bg-red-sure border-none px-6 py-3 rounded-lg">Redeem offer</button>
                    </div>
                </div>
                <div x-data="{ selectedTab: 'all' }" class="w-full">
                    <div @keydown.right.prevent="$focus.wrap().next()" @keydown.left.prevent="$focus.wrap().previous()" class="flex gap-0 overflow-x-auto grid grid-cols-4 bg-white pt-2 bd-line-bottom-2" role="tablist" aria-label="tab options">
                        <button @click="selectedTab = 'all'" :aria-selected="selectedTab === 'all'" :tabindex="selectedTab === 'all' ? '0' : '-1'" :class="selectedTab === 'all' ? 'pt-2 pb-3 bg-white blue-sure balance-border-bottom text-base font-semibold checkout-color-red-text' : 'pt-2 pb-3 bg-white text-base font-normal color-icon-eye'" type="button" role="tab" aria-controls="tabpanelGroups" aria-selected="true" tabindex="0" class="pt-2 pb-3 bg-white text-18">All history</button>
                        <button @click="selectedTab = 'received'" :aria-selected="selectedTab === 'received'" :tabindex="selectedTab === 'received' ? '0' : '-1'" :class="selectedTab === 'received' ? 'pt-2 pb-3 bg-white blue-sure balance-border-bottom text-base font-semibold checkout-color-red-text' : 'pt-2 pb-3 bg-white text-18 font-normal color-icon-eye'" class="h-min px-4 py-2 text-sm pt-2 pb-3 bg-white text-base font-normal color-icon-eye" type="button" role="tab" aria-controls="tabpanelLikes" tabindex="-1">Received</button>
                        <button @click="selectedTab = 'progress'" :aria-selected="selectedTab === 'used'" :tabindex="selectedTab === 'used' ? '0' : '-1'" :class="selectedTab === 'used' ? 'pt-2 pb-3 bg-white blue-sure balance-border-bottom text-base font-semibold checkout-color-red-text' : 'pt-2 pb-3 bg-white text-base font-normal color-icon-eye'" class="h-min px-4 py-2 text-18 pt-2 pb-3 bg-white text-base font-normal color-icon-eye" type="button" role="tab" aria-controls="tabpanelComments" tabindex="-1">Used</button>
                    </div>
                    <div class="text-neutral-600 dark:text-neutral-300">
                        <div x-show="selectedTab === 'all'" id="tabpanelGroups" role="tabpanel" aria-label="all">
                            <div class="">
                            <div class="pt-6 pb-4 pl-5 pr-8 grid grid-cols-8 gap-4 text-left bd-line-bottom" x-data="{ currentVal: 1, minVal: 0, maxVal: 10, decimalPoints: 0, incrementAmount: 1, price: 96 }">
                                <div class="col-span-12 lg:col-span-1 md:col-span-2 sm:col-span-2 flex items-center">
                                    <img class="w-full" src="<?= $url ?>/assets/dealer/img/image.png" alt="">
                                </div>
                                <div class="col-span-12 lg:col-span-6 md:col-span-4 sm:col-span-4">
                                    <h5 class="font-medium leading-8 text-base checkout-color-text mt-0 mb-1">
                                        Bonus points
                                    </h5>
                                    <p class="text-base font-normal mt-0 mb-1">You got points when completing orders</p>
                                    <p class="text-base font-normal my-0">00:41 03-11-2024</p>
                                </div>
                                <div class="col-span-12 lg:col-span-1 md:col-span-2 sm:col-span-2 flex items-center sm:justify-end justify-start">
                                    <div class="text-right"><span class="font-semibold text-lg blue-sure">+ 5,000</span></div>
                                </div>
                            </div>
                            <div class="pt-6 pb-4 pl-5 pr-8 grid grid-cols-8 gap-4 text-left bd-line-bottom" x-data="{ currentVal: 1, minVal: 0, maxVal: 10, decimalPoints: 0, incrementAmount: 1, price: 96 }">
                                <div class="col-span-12 lg:col-span-1 md:col-span-2 sm:col-span-2 flex items-center">
                                    <img class="w-full" src="<?= $url ?>/assets/dealer/img/image-1.png" alt="">
                                </div>
                                <div class="col-span-12 lg:col-span-6 md:col-span-4 sm:col-span-4">
                                    <h5 class="font-medium leading-8 text-base checkout-color-text mt-0 mb-1">
                                        Used points
                                    </h5>
                                    <p class="text-base font-normal mt-0 mb-1">You used points to redeem offer</p>
                                    <p class="text-base font-normal my-0">00:41 03-11-2024</p>
                                </div>
                                <div class="col-span-12 lg:col-span-1 md:col-span-2 sm:col-span-2 flex items-center sm:justify-end justify-start">
                                    <div class="text-right"><span class="font-semibold text-lg">- 5,000</span></div>
                                </div>
                            </div>
                            <div class="pt-6 pb-4 pl-5 pr-8 grid grid-cols-8 gap-4 text-left bd-line-bottom" x-data="{ currentVal: 1, minVal: 0, maxVal: 10, decimalPoints: 0, incrementAmount: 1, price: 96 }">
                                <div class="col-span-12 lg:col-span-1 md:col-span-2 sm:col-span-2 flex items-center">
                                    <img class="w-full" src="<?= $url ?>/assets/dealer/img/image-2.png" alt="">
                                </div>
                                <div class="col-span-12 lg:col-span-6 md:col-span-4 sm:col-span-4">
                                    <h5 class="font-medium leading-8 text-base checkout-color-text mt-0 mb-1">
                                        Points expiration
                                    </h5>
                                    <p class="text-base font-normal mt-0 mb-1">Points were deducted due to expiration</p>
                                    <p class="text-base font-normal my-0">00:41 03-11-2024</p>
                                </div>
                                <div class="col-span-12 lg:col-span-1 md:col-span-2 sm:col-span-2 flex items-center sm:justify-end justify-start">
                                    <div class="text-right"><span class="font-semibold text-lg">- 5,000</span></div>
                                </div>
                            </div>
                            <div class="pt-6 pb-4 pl-5 pr-8 grid grid-cols-8 gap-4 text-left bd-line-bottom" x-data="{ currentVal: 1, minVal: 0, maxVal: 10, decimalPoints: 0, incrementAmount: 1, price: 96 }">
                                <div class="col-span-12 lg:col-span-1 md:col-span-2 sm:col-span-2 flex items-center">
                                    <img class="w-full" src="<?= $url ?>/assets/dealer/img/image-3.png" alt="">
                                </div>
                                <div class="col-span-12 lg:col-span-6 md:col-span-4 sm:col-span-4">
                                    <h5 class="font-medium leading-8 text-base checkout-color-text mt-0 mb-1">
                                        Bonus points
                                    </h5>
                                    <p class="text-base font-normal mt-0 mb-1">Received points. You got points when completing orders</p>
                                    <p class="text-base font-normal my-0">00:41 03-11-2024</p>
                                </div>
                                <div class="col-span-12 lg:col-span-1 md:col-span-2 sm:col-span-2 flex items-center sm:justify-end justify-start">
                                    <div class="text-right"><span class="font-semibold text-lg blue-sure">+ 5,000</span></div>
                                </div>
                            </div>
                            <div class="pt-6 pb-4 pl-5 pr-8 grid grid-cols-8 gap-4 text-left bd-line-bottom" x-data="{ currentVal: 1, minVal: 0, maxVal: 10, decimalPoints: 0, incrementAmount: 1, price: 96 }">
                                <div class="col-span-12 lg:col-span-1 md:col-span-2 sm:col-span-2 flex items-center">
                                    <img class="w-full" src="<?= $url ?>/assets/dealer/img/image-3.png" alt="">
                                </div>
                                <div class="col-span-12 lg:col-span-6 md:col-span-4 sm:col-span-4">
                                    <h5 class="font-medium leading-8 text-base checkout-color-text mt-0 mb-1">
                                        Bonus points
                                    </h5>
                                    <p class="text-base font-normal mt-0 mb-1">Received points. You got points when completing orders</p>
                                    <p class="text-base font-normal my-0">00:41 03-11-2024</p>
                                </div>
                                <div class="col-span-12 lg:col-span-1 md:col-span-2 sm:col-span-2 flex items-center sm:justify-end justify-start">
                                    <div class="text-right"><span class="font-semibold text-lg blue-sure">+ 5,000</span></div>
                                </div>
                            </div>
                            </div>
                        </div>
                        <div x-show="selectedTab === 'Processing'" id="tabpanelLikes" role="tabpanel" aria-label="Processing" style="display: none;">
                            <div class="mb-6 bg-white">
                            <div class="pt-6 pb-4 pl-5 pr-8 grid grid-cols-8 gap-4 text-left bd-line-bottom" x-data="{ currentVal: 1, minVal: 0, maxVal: 10, decimalPoints: 0, incrementAmount: 1, price: 96 }">
                                <div class="col-span-12 lg:col-span-1 md:col-span-2 sm:col-span-2 flex items-centerr">
                                    <img class="w-full" src="<?= $url ?>/assets/dealer/img/image.png" alt="">
                                </div>
                                <div class="col-span-12 lg:col-span-6 md:col-span-4 sm:col-span-4">
                                    <h5 class="font-medium leading-8 text-base checkout-color-text mt-0 mb-1">
                                        Bonus points
                                    </h5>
                                    <p class="text-base font-normal mt-0 mb-1">You got points when completing orders</p>
                                    <p class="text-base font-normal my-0">00:41 03-11-2024</p>
                                </div>
                                <div class="col-span-12 lg:col-span-1 md:col-span-2 sm:col-span-2 flex items-center sm:justify-end justify-start">
                                    <div class="text-right"><span class="font-semibold text-lg blue-sure">+ 5,000</span></div>
                                </div>
                            </div>
                            <div class="pt-6 pb-4 pl-5 pr-8 grid grid-cols-8 gap-4 text-left bd-line-bottom" x-data="{ currentVal: 1, minVal: 0, maxVal: 10, decimalPoints: 0, incrementAmount: 1, price: 96 }">
                                <div class="col-span-12 lg:col-span-1 md:col-span-2 sm:col-span-2 flex items-center">
                                    <img class="w-full" src="<?= $url ?>/assets/dealer/img/image-3.png" alt="">
                                </div>
                                <div class="col-span-12 lg:col-span-6 md:col-span-4 sm:col-span-4">
                                    <h5 class="font-medium leading-8 text-base checkout-color-text mt-0 mb-1">
                                        Bonus points
                                    </h5>
                                    <p class="text-base font-normal mt-0 mb-1">Received points. You got points when completing orders</p>
                                    <p class="text-base font-normal my-0">00:41 03-11-2024</p>
                                </div>
                                <div class="col-span-12 lg:col-span-1 md:col-span-2 sm:col-span-2 flex items-center sm:justify-end justify-start">
                                    <div class="text-right"><span class="font-semibold text-lg blue-sure">+ 5,000</span></div>
                                </div>
                            </div>
                            <div class="pt-6 pb-4 pl-5 pr-8 grid grid-cols-8 gap-4 text-left bd-line-bottom" x-data="{ currentVal: 1, minVal: 0, maxVal: 10, decimalPoints: 0, incrementAmount: 1, price: 96 }">
                                <div class="col-span-12 lg:col-span-1 md:col-span-2 sm:col-span-2 flex items-center">
                                    <img class="w-full" src="<?= $url ?>/assets/dealer/img/image-3.png" alt="">
                                </div>
                                <div class="col-span-12 lg:col-span-6 md:col-span-4 sm:col-span-4">
                                    <h5 class="font-medium leading-8 text-base checkout-color-text mt-0 mb-1">
                                        Bonus points
                                    </h5>
                                    <p class="text-base font-normal mt-0 mb-1">Received points. You got points when completing orders</p>
                                    <p class="text-base font-normal my-0">00:41 03-11-2024</p>
                                </div>
                                <div class="col-span-12 lg:col-span-1 md:col-span-2 sm:col-span-2 flex items-center sm:justify-end justify-start">
                                    <div class="text-right"><span class="font-semibold text-lg blue-sure">+ 5,000</span></div>
                                </div>
                            </div>
                            </div>
                        </div>
                        <div x-show="selectedTab === 'progress'" id="tabpanelComments" role="tabpanel" aria-label="progress" style="display: none;">
                            <div class="mb-6 bg-white">
                            <div class="pt-6 pb-4 pl-5 pr-8 grid grid-cols-8 gap-4 text-left bd-line-bottom" x-data="{ currentVal: 1, minVal: 0, maxVal: 10, decimalPoints: 0, incrementAmount: 1, price: 96 }">
                                <div class="col-span-12 lg:col-span-1 md:col-span-2 sm:col-span-2 flex items-center">
                                    <img class="w-full" src="<?= $url ?>/assets/dealer/img/image-1.png" alt="">
                                </div>
                                <div class="col-span-12 lg:col-span-6 md:col-span-4 sm:col-span-4">
                                    <h5 class="font-medium leading-8 text-base checkout-color-text mt-0 mb-1">
                                        Used points
                                    </h5>
                                    <p class="text-base font-normal mt-0 mb-1">You used points to redeem offer</p>
                                    <p class="text-base font-normal my-0">00:41 03-11-2024</p>
                                </div>
                                <div class="col-span-12 lg:col-span-1 md:col-span-2 sm:col-span-2 flex items-center sm:justify-end justify-start">
                                    <div class="text-right"><span class="font-semibold text-lg">- 5,000</span></div>
                                </div>
                            </div>
                            <div class="pt-6 pb-4 pl-5 pr-8 grid grid-cols-8 gap-4 text-left bd-line-bottom" x-data="{ currentVal: 1, minVal: 0, maxVal: 10, decimalPoints: 0, incrementAmount: 1, price: 96 }">
                                <div class="col-span-12 lg:col-span-1 md:col-span-2 sm:col-span-2 flex items-center">
                                    <img class="w-full" src="<?= $url ?>/assets/dealer/img/image-2.png" alt="">
                                </div>
                                <div class="col-span-12 lg:col-span-6 md:col-span-4 sm:col-span-4">
                                    <h5 class="font-medium leading-8 text-base checkout-color-text mt-0 mb-1">
                                        Points expiration
                                    </h5>
                                    <p class="text-base font-normal mt-0 mb-1">Points were deducted due to expiration</p>
                                    <p class="text-base font-normal my-0">00:41 03-11-2024</p>
                                </div>
                                <div class="col-span-12 lg:col-span-1 md:col-span-2 sm:col-span-2 flex items-center sm:justify-end justify-start">
                                    <div class="text-right"><span class="font-semibold text-lg">- 5,000</span></div>
                                </div>
                            </div>
                            </div>
                        </div>
                    </div>
                </div>
                </div> 
            </div>
        </div>
    </div>
</div>
<?php get_footer() ?>