<?php /* Template Name: Dealer-Affiliate-Customization */ ?>
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
<div>
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
                    <li class="flex items-center pt-4 pb-4 pl-6 item-f management checkout-menu-active">
                    <a class="flex items-center no-underline menu-item" href="<?= home_url() ?>/dealer-affiliate-customization"><img class="mr-3 w-6 active" src="<?= $url ?>/assets/dealer/img/material-symbols_link_ac.png" alt=""><span class="menu-item">Affiliate link<br>customization</span></a>
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
                    <?php if($type == 2): ?>
                        <li class="mt-2">
                            <a class="no-underline text-sm" href="<?= home_url() ?>/dealer-bank-informmation">Bank account information</a>
                        </li>
                    <?php endif; ?>
                </ul>
                </li>
            </ul>
        </div>
        <div class="col-span-6 text-center md:p-8 p-4 grid grid-cols-12">
            <div class="xl:col-span-9 col-span-12 text-left">
                <div class="bg-white md:px-10 px-4 py-6 rounded-xl">
                    <h4 class="text-left m-0 color-vector text-28 font-semibold mb-6">Affiliate link customization</h4>
                    <div class="mb-6 text-left">
                        <label for="link" class="block mb-1 text-base font-medium text-gray-900 dark:text-white checkout-color-unactive">Distribution link</label>
                        <div class="relative">
                            <img src="<?= $url ?>/assets/dealer/img/icon_edit.png" alt="" class="w-6 absolute right-4 top-2">
                            <input type="text" id="link" class="box-border bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-3 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="https://suremeal.com/v2/73BbWa03CxKWTWsDbEnPVA?lp=3gang" autocomplete="off">
                        </div>
                        </div>
                        <div class="mb-4 text-left">
                        <label for="link" class="block mb-1 text-base font-medium text-gray-900 dark:text-white checkout-color-unactive">Shorten link</label>
                        <div class="relative grid grid-cols-12">
                                <div class="2xl:col-span-4 col-span-6">
                                    <div class="relative">
                                        <img src="<?= $url ?>/assets/dealer/img/icon_edit.png" alt="" class="w-6 absolute right-4 top-2">
                                        <input class="box-border bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-l-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-3 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" type="text" name="" placeholder="https://suremeal.com/">
                                    </div>
                                </div>
                                <div class="2xl:col-span-5 col-span-6">
                                        <input class="box-border bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-r-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-3 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" type="text" name="" placeholder="">
                                </div>
                                <div class="2xl:col-span-3 col-span-12 md:pl-6 pl-0 md:pt-0 pt-2">
                                    <button type="submit" class="w-full text-white bg-red-sure border-none font-medium rounded-lg text-base w-full pl-8 pr-8 py-3 text-center my-0 md:mt-0 mt-2 ml-0">Generate link</button>
                                </div>
                        </div>
                        </div>
                </div>
            </div>
    </div>
</div>
<?php get_footer() ?>