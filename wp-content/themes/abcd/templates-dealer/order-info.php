<?php /* Template Name: Dealer-Order-Info */ ?>
<?php
$authenticated_dealer = validate_dealer_token();
if (!isset($_COOKIE['dealer_token']) || !$authenticated_dealer) {
    wp_redirect(home_url());
    exit;
}
$status = $authenticated_dealer->status;

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
                <div class="mb-4 md:flex block items-center">
                    <h4 class="text-left m-0 md:mb-0 mb-4 color-vector text-28 font-semibold">My orders</h4>
                    <form action="" class="ml-auto order-2">
                        <div class="search relative flex items-center  w-386">
                            <img src="<?= $url ?>/assets/dealer/img/search.png" class="absolute top-3 right-5">
                    <input type="text" class="border rounded-xl py-3 w-full px-5 py-3 text-base" placeholder="Search by product name">
                    </div>
                </form>
                </div>
                <div class="">
                    <div x-data="{ selectedTab: 'all' }" class="w-full">
                        <div @keydown.right.prevent="$focus.wrap().next()" @keydown.left.prevent="$focus.wrap().previous()" class="flex gap-0 overflow-x-auto grid grid-cols-5 mb-3 bg-white pt-2 rounded-t" role="tablist" aria-label="tab options">
                            <button @click="selectedTab = 'all'" :aria-selected="selectedTab === 'all'" :tabindex="selectedTab === 'all' ? '0' : '-1'" :class="selectedTab === 'all' ? 'pt-2 pb-3 bg-white blue-sure myorder-border-bottom text-base font-semibold' : 'pt-2 pb-3 bg-white text-base font-normal color-icon-eye'" type="button" role="tab" aria-controls="tabpanelGroups" >All (12)</button>
                            <button @click="selectedTab = 'Processing'" :aria-selected="selectedTab === 'Processing'" :tabindex="selectedTab === 'Processing' ? '0' : '-1'" :class="selectedTab === 'Processing' ? 'pt-2 pb-3 bg-white blue-sure myorder-border-bottom text-base font-semibold' : 'pt-2 pb-3 bg-white text-base font-normal color-icon-eye'" class="h-min px-4 py-2 text-sm" type="button" role="tab" aria-controls="tabpanelLikes" >Processing (1)</button>
                            <button @click="selectedTab = 'progress'" :aria-selected="selectedTab === 'progress'" :tabindex="selectedTab === 'progress' ? '0' : '-1'" :class="selectedTab === 'progress' ? 'pt-2 pb-3 bg-white blue-sure myorder-border-bottom text-base font-semibold' : 'pt-2 pb-3 bg-white text-base font-normal color-icon-eye'" class="h-min px-4 py-2 text-sm" type="button" role="tab" aria-controls="tabpanelComments" >In progress (1)</button>
                            <button @click="selectedTab = 'completed'" :aria-selected="selectedTab === 'completed'" :tabindex="selectedTab === 'completed' ? '0' : '-1'" :class="selectedTab === 'completed' ? 'pt-2 pb-3 bg-white blue-sure myorder-border-bottom text-base font-semibold' : 'pt-2 pb-3 bg-white text-base font-normal color-icon-eye'" class="h-min px-4 py-2 text-sm" type="button" role="tab" aria-controls="tabpanelSaved" >Completed (5)</button>
                            <button @click="selectedTab = 'canceled'" :aria-selected="selectedTab === 'canceled'" :tabindex="selectedTab === 'canceled' ? '0' : '-1'" :class="selectedTab === 'canceled' ? 'pt-2 pb-3 bg-white blue-sure myorder-border-bottom text-base font-semibold' : 'pt-2 pb-3 bg-white text-base font-normal color-icon-eye'" class="h-min px-4 py-2 text-sm" type="button" role="tab" aria-controls="tabpanelSaved" >Canceled (1)</button>
                        </div>
                        <div class="text-neutral-600 dark:text-neutral-300">
                            <div x-show="selectedTab === 'all'" id="tabpanelGroups" role="tabpanel" aria-label="all">
                                <div class="py-4 mb-6 bg-white rounded-lg">
                                    <div class="2xl:flex items-center md:px-8 px-2 bd-line-bottom pb-3 inline-block w-full">
                                        <div class="text-base font-medium color-vector flex items-center 2xl:float-none float-left mb-1">
                                                <span class="text-base">03/22/2024</span>
                                                <svg class="mx-4" xmlns="http://www.w3.org/2000/svg" width="4" height="4" viewBox="0 0 4 4" fill="none">
                                                <circle cx="2" cy="2" r="2" fill="#D9D9D9"></circle>
                                                </svg>
                                            </div>
                                        <div class="flex items-center 2xl:float-none float-left mb-1">
                                            <span class="text-base font-medium checkout-color-text mr-2 text-base">Order code:</span>
                                            <span class="checkout-color-unactive font-medium text-base">#123ABCXYZ</span>
                                            <img class="w-5 h-auto ml-2" src="<?= $url ?>/assets/dealer/img/copy.png" alt="">
                                            </div>
                                        <div class="text-base checkout-color-unactive font-medium flex items-center col-span-4 2xl:float-none float-left mb-1">
                                            <svg class="mx-4" xmlns="http://www.w3.org/2000/svg" width="4" height="4" viewBox="0 0 4 4" fill="none">
                                                    <circle cx="2" cy="2" r="2" fill="#D9D9D9"></circle>
                                                </svg>
                                            <span class="checkout-color-unactive font-medium text-base">Deliver</span>
                                            <svg class="mx-4" xmlns="http://www.w3.org/2000/svg" width="4" height="4" viewBox="0 0 4 4" fill="none">
                                                    <circle cx="2" cy="2" r="2" fill="#D9D9D9"></circle>
                                                </svg>
                                                <span class="checkout-color-unactive font-medium">2 products</span>
                                                <svg class="mx-4" xmlns="http://www.w3.org/2000/svg" width="4" height="4" viewBox="0 0 4 4" fill="none">
                                                    <circle cx="2" cy="2" r="2" fill="#D9D9D9"></circle>
                                                </svg>
                                            </div>
                                        <div class="grid grid-cols-2 col-span-6 2xl:float-none float-left mb-1">
                                            <div class="flex items-center text-base checkout-color-unactive font-medium text-base">Receipt <img class="w-3 ml-2" src="<?= $url ?>/assets/dealer/img/receipt.png" alt=""></div>
                                        </div>
                                        <div class="col-span-2 text-center orderdetail-color-yellow process px-2 py-1 font-medium ml-auto order-2 text-sm 2xl:float-none float-left mb-1">Processing</div>
                                    </div>
                                    <div class="pt-6 pb-3 px-8 grid grid-cols-12 gap-4 text-left" x-data="{ currentVal: 1, minVal: 0, maxVal: 10, decimalPoints: 0, incrementAmount: 1, price: 96 }">
                                        <div class="flex items-center border rounded-xl p-2 xl:col-span-2 md:col-span-2 col-span-4">
                                            <img class="w-full flex items-center" src="<?= $url ?>/assets/dealer/img/img8.png" alt="">
                                        </div>
                                        <div class="xl:col-span-5 md:col-span-5 col-span-8">
                                            <h5 class="font-medium leading-8 text-base checkout-color-text my-0">
                                                SureMeal™ Vital - Immune Builder Enhanced Collagen, Biotin &amp; HA
                                            </h5>
                                            <div class="tag mt-2">
                                                <span class="gap-2 rounded-lg checkout-bg py-2 px-3 w-auto text-sm checkout-tag-color">
                                                    Type: 3 Pack
                                                </span>
                                            </div>
                                        </div>
                                        <div class="flex items-center md:justify-center justify-start 2xl:col-span-3 xl:col-span-3 md:col-span-3 col-span-6">
                                                <div class="flex items-center justify-start color-vector">
                                                    Quantity:&nbsp;<span>01</span>
                                                </div>
                                        </div>
                                        <div class="flex items-center justify-end 2xl:col-span-2 xl:col-span-2 md:col-span-2 col-span-6">
                                            <div class="text-right"><span class="color-vector font-medium text-lg">$96.00</span><br><span class="font-normal text-base line-through color-icon-eye">$120.00</span></div>
                                        </div>
                                    </div>
                                    <div class="flex px-8 flex items-center">
                                        <div><a href="my-order-details.html" class="flex items-center no-underline"><span class="text-base font-semibold blue-sure">View details</span><img class="ml-2 w-6" src="<?= $url ?>/assets/dealer/img/arrow_right_blue.png" alt=""></a></div>
                                        <div class="order-2 flex items-center ml-auto">
                                            <span class="text-base checkout-color-unactive">Total:&nbsp;</span>
                                            <span class="font-medium text-2xl checkout-color-red-text">$96.00</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="py-4 mb-6 bg-white rounded-lg">
                                <div class="2xl:flex items-center md:px-8 px-2 bd-line-bottom pb-3 inline-block w-full">
                                    <div class="text-base font-medium color-vector flex items-center 2xl:float-none float-left mb-1">
                                            <span class="text-base">03/22/2024</span>
                                            <svg class="mx-4" xmlns="http://www.w3.org/2000/svg" width="4" height="4" viewBox="0 0 4 4" fill="none">
                                            <circle cx="2" cy="2" r="2" fill="#D9D9D9"></circle>
                                            </svg>
                                        </div>
                                    <div class="flex items-center 2xl:float-none float-left mb-1">
                                        <span class="text-base font-medium checkout-color-text mr-2 text-base">Order code:</span>
                                        <span class="checkout-color-unactive font-medium text-base">#123ABCXYZ</span>
                                        <img class="w-5 h-auto ml-2" src="<?= $url ?>/assets/dealer/img/copy.png" alt="">
                                        </div>
                                    <div class="text-base checkout-color-unactive font-medium flex items-center col-span-4 2xl:float-none float-left mb-1">
                                        <svg class="mx-4" xmlns="http://www.w3.org/2000/svg" width="4" height="4" viewBox="0 0 4 4" fill="none">
                                                <circle cx="2" cy="2" r="2" fill="#D9D9D9"></circle>
                                            </svg>
                                        <span class="checkout-color-unactive font-medium text-base">Deliver</span>
                                        <svg class="mx-4" xmlns="http://www.w3.org/2000/svg" width="4" height="4" viewBox="0 0 4 4" fill="none">
                                                <circle cx="2" cy="2" r="2" fill="#D9D9D9"></circle>
                                            </svg>
                                            <span class="checkout-color-unactive font-medium">2 products</span>
                                            <svg class="mx-4" xmlns="http://www.w3.org/2000/svg" width="4" height="4" viewBox="0 0 4 4" fill="none">
                                                <circle cx="2" cy="2" r="2" fill="#D9D9D9"></circle>
                                            </svg>
                                        </div>
                                    <div class="grid grid-cols-2 col-span-6 2xl:float-none float-left mb-1">
                                        <div class="flex items-center text-base checkout-color-unactive font-medium text-base">Receipt <img class="w-3 ml-2" src="<?= $url ?>/assets/dealer/img/receipt.png" alt=""></div>
                                    </div>
                                    <div class="col-span-2 text-center color-complete bg-complete process process px-2 py-1 font-medium ml-auto order-2 text-sm 2xl:float-none float-left mb-1">Completed</div>
                                </div>

                                <div class="pt-6 pb-3 px-8 grid grid-cols-12 gap-4 text-left" x-data="{ currentVal: 1, minVal: 0, maxVal: 10, decimalPoints: 0, incrementAmount: 1, price: 96 }">
                                    <div class="flex items-center border rounded-xl p-2 xl:col-span-2 md:col-span-2 col-span-4">
                                        <img class="w-full flex items-center" src="<?= $url ?>/assets/dealer/img/img5.png" alt="">
                                    </div>
                                    <div class="xl:col-span-5 md:col-span-5 col-span-8">
                                        <h5 class="font-medium leading-8 text-base checkout-color-text my-0">
                                            SureMeal™ Vital - Immune Builder Enhanced Collagen, Biotin &amp; HA
                                        </h5>
                                        <div class="tag mt-2">
                                            <span class="gap-2 rounded-lg checkout-bg py-2 px-3 w-auto text-sm checkout-tag-color">
                                                Type: 3 Pack
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex items-center md:justify-center justify-start 2xl:col-span-3 xl:col-span-3 md:col-span-3 col-span-6">
                                            <div class="flex items-center justify-start color-vector">
                                                Quantity:&nbsp;<span>01</span>
                                            </div>
                                    </div>
                                    <div class="flex items-center justify-end 2xl:col-span-2 xl:col-span-2 md:col-span-2 col-span-6">
                                        <div class="text-right"><span class="color-vector font-medium text-lg">$96.00</span><br><span class="font-normal text-base line-through color-icon-eye">$120.00</span></div>
                                    </div>
                                </div>
                                <div class="pt-4 pb-3 px-8 grid grid-cols-12 gap-4 text-left" x-data="{ currentVal: 1, minVal: 0, maxVal: 10, decimalPoints: 0, incrementAmount: 1, price: 96 }">
                                    <div class="flex items-center border rounded-xl p-2 xl:col-span-2 md:col-span-2 col-span-4">
                                        <img class="w-full flex items-center" src="<?= $url ?>/assets/dealer/img/img1.png" alt="">
                                    </div>
                                    <div class="xl:col-span-5 md:col-span-5 col-span-8">
                                        <h5 class="font-medium leading-8 text-base checkout-color-text my-0">
                                            SureMeal™ Vital - Immune Builder Enhanced Collagen, Biotin &amp; HA
                                        </h5>
                                        <div class="tag mt-2">
                                            <span class="gap-2 rounded-lg checkout-bg py-2 px-3 w-auto text-sm checkout-tag-color">
                                                Type: 3 Pack
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex items-center md:justify-center justify-start 2xl:col-span-3 xl:col-span-3 md:col-span-3 col-span-6">
                                            <div class="flex items-center justify-start color-vector">
                                                Quantity:&nbsp;<span>01</span>
                                            </div>
                                    </div>
                                    <div class="flex items-center justify-end 2xl:col-span-2 xl:col-span-2 md:col-span-2 col-span-6">
                                        <div class="text-right"><span class="color-vector font-medium text-lg">$96.00</span><br><span class="font-normal text-base line-through color-icon-eye">$120.00</span></div>
                                    </div>
                                </div>
                                <div class="pt-4 pb-3 px-8 grid grid-cols-12 gap-4 text-left" x-data="{ currentVal: 1, minVal: 0, maxVal: 10, decimalPoints: 0, incrementAmount: 1, price: 96 }">
                                    <div class="flex items-center border rounded-xl p-2 xl:col-span-2 md:col-span-2 col-span-4">
                                        <img class="w-full flex items-center" src="<?= $url ?>/assets/dealer/img/img3.png" alt="">
                                    </div>
                                    <div class="xl:col-span-5 md:col-span-5 col-span-8">
                                        <h5 class="font-medium leading-8 text-base checkout-color-text my-0">
                                            SureMeal™ Vital - Immune Builder Enhanced Collagen, Biotin &amp; HA
                                        </h5>
                                        <div class="tag mt-2">
                                            <span class="gap-2 rounded-lg checkout-bg py-2 px-3 w-auto text-sm checkout-tag-color">
                                                Type: 3 Pack
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex items-center md:justify-center justify-start 2xl:col-span-3 xl:col-span-3 md:col-span-3 col-span-6">
                                            <div class="flex items-center justify-start color-vector">
                                                Quantity:&nbsp;<span>01</span>
                                            </div>
                                    </div>
                                    <div class="flex items-center justify-end 2xl:col-span-2 xl:col-span-2 md:col-span-2 col-span-6">
                                        <div class="text-right"><span class="color-vector font-medium text-lg">$96.00</span><br><span class="font-normal text-base line-through color-icon-eye">$120.00</span></div>
                                    </div>
                                </div>
                                <div class="flex px-8 flex items-center">
                                    <div><a href="my-order-details.html"  class="flex items-center no-underline"><span class="text-base font-semibold blue-sure">View details</span><img class="ml-2 w-6" src="<?= $url ?>/assets/dealer/img/arrow_right_blue.png" alt=""></a></div>
                                    <div class="order-2 flex items-center ml-auto">
                                        <span class="text-base checkout-color-unactive">Total:&nbsp;</span>
                                        <span class="font-medium text-2xl checkout-color-red-text">$96.00</span>
                                    </div>
                                </div>
                                </div>
                                <div class="py-4 mb-0 bg-white rounded-lg">
                                    <div class="2xl:flex items-center md:px-8 px-2 bd-line-bottom pb-3 inline-block w-full">
                                        <div class="text-base font-medium color-vector flex items-center 2xl:float-none float-left mb-1">
                                                <span class="text-base">03/22/2024</span>
                                                <svg class="mx-4" xmlns="http://www.w3.org/2000/svg" width="4" height="4" viewBox="0 0 4 4" fill="none">
                                                <circle cx="2" cy="2" r="2" fill="#D9D9D9"></circle>
                                                </svg>
                                            </div>
                                        <div class="flex items-center 2xl:float-none float-left mb-1">
                                            <span class="text-base font-medium checkout-color-text mr-2 text-base">Order code:</span>
                                            <span class="checkout-color-unactive font-medium text-base">#123ABCXYZ</span>
                                            <img class="w-5 h-auto ml-2" src="<?= $url ?>/assets/dealer/img/copy.png" alt="">
                                            </div>
                                        <div class="text-base checkout-color-unactive font-medium flex items-center col-span-4 2xl:float-none float-left mb-1">
                                            <svg class="mx-4" xmlns="http://www.w3.org/2000/svg" width="4" height="4" viewBox="0 0 4 4" fill="none">
                                                    <circle cx="2" cy="2" r="2" fill="#D9D9D9"></circle>
                                                </svg>
                                            <span class="checkout-color-unactive font-medium text-base">Deliver</span>
                                            <svg class="mx-4" xmlns="http://www.w3.org/2000/svg" width="4" height="4" viewBox="0 0 4 4" fill="none">
                                                    <circle cx="2" cy="2" r="2" fill="#D9D9D9"></circle>
                                                </svg>
                                                <span class="checkout-color-unactive font-medium">2 products</span>
                                                <svg class="mx-4" xmlns="http://www.w3.org/2000/svg" width="4" height="4" viewBox="0 0 4 4" fill="none">
                                                    <circle cx="2" cy="2" r="2" fill="#D9D9D9"></circle>
                                                </svg>
                                            </div>
                                        <div class="grid grid-cols-2 col-span-6 2xl:float-none float-left mb-1">
                                            <div class="flex items-center text-base checkout-color-unactive font-medium text-base">Receipt <img class="w-3 ml-2" src="<?= $url ?>/assets/dealer/img/receipt.png" alt=""></div>
                                        </div>
                                        <div class="col-span-2 text-center color-cancel bg-cancel process process px-2 py-1 font-medium ml-auto order-2 text-sm 2xl:float-none float-left mb-1">Cancel</div>
                                    </div>
                                    <div class="pt-4 pb-3 px-8 grid grid-cols-12 gap-4 text-left" x-data="{ currentVal: 1, minVal: 0, maxVal: 10, decimalPoints: 0, incrementAmount: 1, price: 96 }">
                                        <div class="flex items-center border rounded-xl p-2 xl:col-span-2 md:col-span-2 col-span-4">
                                            <img class="w-full flex items-center" src="<?= $url ?>/assets/dealer/img/img4.png" alt="">
                                        </div>
                                        <div class="xl:col-span-5 md:col-span-5 col-span-8">
                                            <h5 class="font-medium leading-8 text-base checkout-color-text my-0">
                                                SureMeal™ Vital - Immune Builder Enhanced Collagen, Biotin &amp; HA
                                            </h5>
                                            <div class="tag mt-2">
                                                <span class="gap-2 rounded-lg checkout-bg py-2 px-3 w-auto text-sm checkout-tag-color">
                                                    Type: 3 Pack
                                                </span>
                                            </div>
                                        </div>
                                        <div class="flex items-center md:justify-center justify-start 2xl:col-span-3 xl:col-span-3 md:col-span-3 col-span-6">
                                                <div class="flex items-center justify-start color-vector">
                                                    Quantity:&nbsp;<span>01</span>
                                                </div>
                                        </div>
                                        <div class="flex items-center justify-end 2xl:col-span-2 xl:col-span-2 md:col-span-2 col-span-6">
                                            <div class="text-right"><span class="color-vector font-medium text-lg">$96.00</span><br><span class="font-normal text-base line-through color-icon-eye">$120.00</span></div>
                                        </div>
                                    </div>
                                    <div class="flex px-8 flex items-center">
                                        <div><a href="my-order-details.html"  class="flex items-center no-underline"><span class="text-base font-semibold blue-sure">View details</span><img class="ml-2 w-6" src="<?= $url ?>/assets/dealer/img/arrow_right_blue.png" alt=""></a></div>
                                        <div class="order-2 flex items-center ml-auto">
                                            <span class="text-base checkout-color-unactive">Total:&nbsp;</span>
                                            <span class="font-medium text-2xl checkout-color-red-text">$96.00</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div x-show="selectedTab === 'Processing'" id="tabpanelLikes" role="tabpanel" aria-label="Processing">
                                <div class="py-4 mb-0 bg-white rounded-lg">
                                            <div class="2xl:flex items-center md:px-8 px-2 bd-line-bottom pb-3 inline-block w-full">
                                            <div class="text-base font-medium color-vector flex items-center 2xl:float-none float-left mb-1">
                                                    <span class="text-base">03/22/2024</span>
                                                    <svg class="mx-4" xmlns="http://www.w3.org/2000/svg" width="4" height="4" viewBox="0 0 4 4" fill="none">
                                                    <circle cx="2" cy="2" r="2" fill="#D9D9D9"></circle>
                                                    </svg>
                                                </div>
                                            <div class="flex items-center 2xl:float-none float-left mb-1">
                                                <span class="text-base font-medium checkout-color-text mr-2 text-base">Order code:</span>
                                                <span class="checkout-color-unactive font-medium text-base">#123ABCXYZ</span>
                                                <img class="w-5 h-auto ml-2" src="<?= $url ?>/assets/dealer/img/copy.png" alt="">
                                                </div>
                                            <div class="text-base checkout-color-unactive font-medium flex items-center col-span-4 2xl:float-none float-left mb-1">
                                                <svg class="mx-4" xmlns="http://www.w3.org/2000/svg" width="4" height="4" viewBox="0 0 4 4" fill="none">
                                                        <circle cx="2" cy="2" r="2" fill="#D9D9D9"></circle>
                                                    </svg>
                                                <span class="checkout-color-unactive font-medium text-base">Deliver</span>
                                                <svg class="mx-4" xmlns="http://www.w3.org/2000/svg" width="4" height="4" viewBox="0 0 4 4" fill="none">
                                                        <circle cx="2" cy="2" r="2" fill="#D9D9D9"></circle>
                                                    </svg>
                                                    <span class="checkout-color-unactive font-medium">2 products</span>
                                                    <svg class="mx-4" xmlns="http://www.w3.org/2000/svg" width="4" height="4" viewBox="0 0 4 4" fill="none">
                                                        <circle cx="2" cy="2" r="2" fill="#D9D9D9"></circle>
                                                    </svg>
                                                </div>
                                            <div class="grid grid-cols-2 col-span-6 2xl:float-none float-left mb-1">
                                                <div class="flex items-center text-base checkout-color-unactive font-medium text-base">Receipt <img class="w-3 ml-2" src="<?= $url ?>/assets/dealer/img/receipt.png" alt=""></div>
                                            </div>
                                            <div class="col-span-2 text-center orderdetail-color-yellow process px-2 py-1 font-medium ml-auto order-2 text-sm 2xl:float-none float-left mb-1">Processing</div>
                                        </div>
                                    <div class="pt-6 pb-3 px-8 grid grid-cols-12 gap-4 text-left" x-data="{ currentVal: 1, minVal: 0, maxVal: 10, decimalPoints: 0, incrementAmount: 1, price: 96 }">
                                        <div class="flex items-center border rounded-xl p-2 xl:col-span-2 md:col-span-2 col-span-4">
                                            <img class="w-full flex items-center" src="<?= $url ?>/assets/dealer/img/img8.png" alt="">
                                        </div>
                                        <div class="xl:col-span-5 md:col-span-5 col-span-8">
                                            <h5 class="font-medium leading-8 text-base checkout-color-text my-0">
                                                SureMeal™ Vital - Immune Builder Enhanced Collagen, Biotin &amp; HA
                                            </h5>
                                            <div class="tag mt-2">
                                                <span class="gap-2 rounded-lg checkout-bg py-2 px-3 w-auto text-sm checkout-tag-color">
                                                    Type: 3 Pack
                                                </span>
                                            </div>
                                        </div>
                                        <div class="flex items-center md:justify-center justify-start 2xl:col-span-3 xl:col-span-3 md:col-span-3 col-span-6">
                                                <div class="flex items-center justify-start color-vector">
                                                    Quantity:&nbsp;<span>01</span>
                                                </div>
                                        </div>
                                        <div class="flex items-center justify-end 2xl:col-span-2 xl:col-span-2 md:col-span-2 col-span-6">
                                            <div class="text-right"><span class="color-vector font-medium text-lg">$96.00</span><br><span class="font-normal text-base line-through color-icon-eye">$120.00</span></div>
                                        </div>
                                    </div>
                                    <div class="flex px-8 flex items-center">
                                        <div><a href="my-order-details.html" class="flex items-center no-underline"><span class="text-base font-semibold blue-sure">View details</span><img class="ml-2 w-6" src="<?= $url ?>/assets/dealer/img/arrow_right_blue.png" alt=""></a></div>
                                        <div class="order-2 flex items-center ml-auto">
                                            <span class="text-base checkout-color-unactive">Total:&nbsp;</span>
                                            <span class="font-medium text-2xl checkout-color-red-text">$96.00</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div x-show="selectedTab === 'progress'" id="tabpanelComments" role="tabpanel" aria-label="progress">
                                
                            </div>
                            <div x-show="selectedTab === 'completed'" id="tabpanelSaved" role="tabpanel" aria-label="completed">
                                <div class="2xl:flex items-center md:px-8 px-2 bd-line-bottom pb-3 inline-block w-full">
                                <div class="text-base font-medium color-vector flex items-center 2xl:float-none float-left mb-1">
                                        <span class="text-base">03/22/2024</span>
                                        <svg class="mx-4" xmlns="http://www.w3.org/2000/svg" width="4" height="4" viewBox="0 0 4 4" fill="none">
                                        <circle cx="2" cy="2" r="2" fill="#D9D9D9"></circle>
                                        </svg>
                                    </div>
                                <div class="flex items-center 2xl:float-none float-left mb-1">
                                    <span class="text-base font-medium checkout-color-text mr-2 text-base">Order code:</span>
                                    <span class="checkout-color-unactive font-medium text-base">#123ABCXYZ</span>
                                    <img class="w-5 h-auto ml-2" src="<?= $url ?>/assets/dealer/img/copy.png" alt="">
                                    </div>
                                <div class="text-base checkout-color-unactive font-medium flex items-center col-span-4 2xl:float-none float-left mb-1">
                                    <svg class="mx-4" xmlns="http://www.w3.org/2000/svg" width="4" height="4" viewBox="0 0 4 4" fill="none">
                                            <circle cx="2" cy="2" r="2" fill="#D9D9D9"></circle>
                                        </svg>
                                    <span class="checkout-color-unactive font-medium text-base">Deliver</span>
                                    <svg class="mx-4" xmlns="http://www.w3.org/2000/svg" width="4" height="4" viewBox="0 0 4 4" fill="none">
                                            <circle cx="2" cy="2" r="2" fill="#D9D9D9"></circle>
                                        </svg>
                                        <span class="checkout-color-unactive font-medium">2 products</span>
                                        <svg class="mx-4" xmlns="http://www.w3.org/2000/svg" width="4" height="4" viewBox="0 0 4 4" fill="none">
                                            <circle cx="2" cy="2" r="2" fill="#D9D9D9"></circle>
                                        </svg>
                                    </div>
                                <div class="grid grid-cols-2 col-span-6 2xl:float-none float-left mb-1">
                                    <div class="flex items-center text-base checkout-color-unactive font-medium text-base">Receipt <img class="w-3 ml-2" src="<?= $url ?>/assets/dealer/img/receipt.png" alt=""></div>
                                </div>
                                <div class="col-span-2 text-center color-complete bg-complete process process px-2 py-1 font-medium ml-auto order-2 text-sm 2xl:float-none float-left mb-1">Completed</div>
                            </div>
                                <div class="py-4 mb-6 bg-white rounded-lg">
                                    <div class="pt-6 pb-3 px-8 grid grid-cols-12 gap-4 text-left" x-data="{ currentVal: 1, minVal: 0, maxVal: 10, decimalPoints: 0, incrementAmount: 1, price: 96 }">
                                    <div class="flex items-center border rounded-xl p-2 xl:col-span-2 md:col-span-2 col-span-4">
                                        <img class="w-full flex items-center" src="<?= $url ?>/assets/dealer/img/img5.png" alt="">
                                    </div>
                                    <div class="xl:col-span-5 md:col-span-5 col-span-8">
                                        <h5 class="font-medium leading-8 text-base checkout-color-text my-0">
                                            SureMeal™ Vital - Immune Builder Enhanced Collagen, Biotin &amp; HA
                                        </h5>
                                        <div class="tag mt-2">
                                            <span class="gap-2 rounded-lg checkout-bg py-2 px-3 w-auto text-sm checkout-tag-color">
                                                Type: 3 Pack
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex items-center md:justify-center justify-start 2xl:col-span-3 xl:col-span-3 md:col-span-3 col-span-6">
                                            <div class="flex items-center justify-start color-vector">
                                                Quantity:&nbsp;<span>01</span>
                                            </div>
                                    </div>
                                    <div class="flex items-center justify-end 2xl:col-span-2 xl:col-span-2 md:col-span-2 col-span-6">
                                        <div class="text-right"><span class="color-vector font-medium text-lg">$96.00</span><br><span class="font-normal text-base line-through color-icon-eye">$120.00</span></div>
                                    </div>
                                </div>
                                <div class="pt-4 pb-3 px-8 grid grid-cols-12 gap-4 text-left" x-data="{ currentVal: 1, minVal: 0, maxVal: 10, decimalPoints: 0, incrementAmount: 1, price: 96 }">
                                    <div class="flex items-center border rounded-xl p-2 xl:col-span-2 md:col-span-2 col-span-4">
                                        <img class="w-full flex items-center" src="<?= $url ?>/assets/dealer/img/img1.png" alt="">
                                    </div>
                                    <div class="xl:col-span-5 md:col-span-5 col-span-8">
                                        <h5 class="font-medium leading-8 text-base checkout-color-text my-0">
                                            SureMeal™ Vital - Immune Builder Enhanced Collagen, Biotin &amp; HA
                                        </h5>
                                        <div class="tag mt-2">
                                            <span class="gap-2 rounded-lg checkout-bg py-2 px-3 w-auto text-sm checkout-tag-color">
                                                Type: 3 Pack
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex items-center md:justify-center justify-start 2xl:col-span-3 xl:col-span-3 md:col-span-3 col-span-6">
                                            <div class="flex items-center justify-start color-vector">
                                                Quantity:&nbsp;<span>01</span>
                                            </div>
                                    </div>
                                    <div class="flex items-center justify-end 2xl:col-span-2 xl:col-span-2 md:col-span-2 col-span-6">
                                        <div class="text-right"><span class="color-vector font-medium text-lg">$96.00</span><br><span class="font-normal text-base line-through color-icon-eye">$120.00</span></div>
                                    </div>
                                </div>
                                <div class="pt-4 pb-3 px-8 grid grid-cols-12 gap-4 text-left" x-data="{ currentVal: 1, minVal: 0, maxVal: 10, decimalPoints: 0, incrementAmount: 1, price: 96 }">
                                    <div class="flex items-center border rounded-xl p-2 xl:col-span-2 md:col-span-2 col-span-4">
                                        <img class="w-full flex items-center" src="<?= $url ?>/assets/dealer/img/img3.png" alt="">
                                    </div>
                                    <div class="xl:col-span-5 md:col-span-5 col-span-8">
                                        <h5 class="font-medium leading-8 text-base checkout-color-text my-0">
                                            SureMeal™ Vital - Immune Builder Enhanced Collagen, Biotin &amp; HA
                                        </h5>
                                        <div class="tag mt-2">
                                            <span class="gap-2 rounded-lg checkout-bg py-2 px-3 w-auto text-sm checkout-tag-color">
                                                Type: 3 Pack
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex items-center md:justify-center justify-start 2xl:col-span-3 xl:col-span-3 md:col-span-3 col-span-6">
                                            <div class="flex items-center justify-start color-vector">
                                                Quantity:&nbsp;<span>01</span>
                                            </div>
                                    </div>
                                    <div class="flex items-center justify-end 2xl:col-span-2 xl:col-span-2 md:col-span-2 col-span-6">
                                        <div class="text-right"><span class="color-vector font-medium text-lg">$96.00</span><br><span class="font-normal text-base line-through color-icon-eye">$120.00</span></div>
                                    </div>
                                </div>
                                <div class="flex px-8 flex items-center">
                                    <div><a href="my-order-details.html"  class="flex items-center no-underline"><span class="text-base font-semibold blue-sure">View details</span><img class="ml-2 w-6" src="<?= $url ?>/assets/dealer/img/arrow_right_blue.png" alt=""></a></div>
                                    <div class="order-2 flex items-center ml-auto">
                                        <span class="text-base checkout-color-unactive">Total:&nbsp;</span>
                                        <span class="font-medium text-2xl checkout-color-red-text">$96.00</span>
                                    </div>
                                </div>
                                </div>
                            </div>
                            <div x-show="selectedTab === 'canceled'" id="tabpanelSaved" role="tabpanel" aria-label="canceled">
                                <div class="py-4 mb-0 bg-white rounded-lg">
                                        <div class="2xl:flex items-center md:px-8 px-2 bd-line-bottom pb-3 inline-block w-full">
                                        <div class="text-base font-medium color-vector flex items-center 2xl:float-none float-left mb-1">
                                                <span class="text-base">03/22/2024</span>
                                                <svg class="mx-4" xmlns="http://www.w3.org/2000/svg" width="4" height="4" viewBox="0 0 4 4" fill="none">
                                                <circle cx="2" cy="2" r="2" fill="#D9D9D9"></circle>
                                                </svg>
                                            </div>
                                        <div class="flex items-center 2xl:float-none float-left mb-1">
                                            <span class="text-base font-medium checkout-color-text mr-2 text-base">Order code:</span>
                                            <span class="checkout-color-unactive font-medium text-base">#123ABCXYZ</span>
                                            <img class="w-5 h-auto ml-2" src="<?= $url ?>/assets/dealer/img/copy.png" alt="">
                                            </div>
                                        <div class="text-base checkout-color-unactive font-medium flex items-center col-span-4 2xl:float-none float-left mb-1">
                                            <svg class="mx-4" xmlns="http://www.w3.org/2000/svg" width="4" height="4" viewBox="0 0 4 4" fill="none">
                                                    <circle cx="2" cy="2" r="2" fill="#D9D9D9"></circle>
                                                </svg>
                                            <span class="checkout-color-unactive font-medium text-base">Deliver</span>
                                            <svg class="mx-4" xmlns="http://www.w3.org/2000/svg" width="4" height="4" viewBox="0 0 4 4" fill="none">
                                                    <circle cx="2" cy="2" r="2" fill="#D9D9D9"></circle>
                                                </svg>
                                                <span class="checkout-color-unactive font-medium">2 products</span>
                                                <svg class="mx-4" xmlns="http://www.w3.org/2000/svg" width="4" height="4" viewBox="0 0 4 4" fill="none">
                                                    <circle cx="2" cy="2" r="2" fill="#D9D9D9"></circle>
                                                </svg>
                                            </div>
                                        <div class="grid grid-cols-2 col-span-6 2xl:float-none float-left mb-1">
                                            <div class="flex items-center text-base checkout-color-unactive font-medium text-base">Receipt <img class="w-3 ml-2" src="<?= $url ?>/assets/dealer/img/receipt.png" alt=""></div>
                                        </div>
                                        <div class="col-span-2 text-center color-cancel bg-cancel process process px-2 py-1 font-medium ml-auto order-2 text-sm 2xl:float-none float-left mb-1">Cancel</div>
                                    </div>
                                    <div class="pt-4 pb-3 px-8 grid grid-cols-12 gap-4 text-left" x-data="{ currentVal: 1, minVal: 0, maxVal: 10, decimalPoints: 0, incrementAmount: 1, price: 96 }">
                                        <div class="flex items-center border rounded-xl p-2 xl:col-span-2 md:col-span-2 col-span-4">
                                            <img class="w-full flex items-center" src="<?= $url ?>/assets/dealer/img/img4.png" alt="">
                                        </div>
                                        <div class="xl:col-span-5 md:col-span-5 col-span-8">
                                            <h5 class="font-medium leading-8 text-base checkout-color-text my-0">
                                                SureMeal™ Vital - Immune Builder Enhanced Collagen, Biotin &amp; HA
                                            </h5>
                                            <div class="tag mt-2">
                                                <span class="gap-2 rounded-lg checkout-bg py-2 px-3 w-auto text-sm checkout-tag-color">
                                                    Type: 3 Pack
                                                </span>
                                            </div>
                                        </div>
                                        <div class="flex items-center md:justify-center justify-start 2xl:col-span-3 xl:col-span-3 md:col-span-3 col-span-6">
                                                <div class="flex items-center justify-start color-vector">
                                                    Quantity:&nbsp;<span>01</span>
                                                </div>
                                        </div>
                                        <div class="flex items-center justify-end 2xl:col-span-2 xl:col-span-2 md:col-span-2 col-span-6">
                                            <div class="text-right"><span class="color-vector font-medium text-lg">$96.00</span><br><span class="font-normal text-base line-through color-icon-eye">$120.00</span></div>
                                        </div>
                                    </div>
                                    <div class="flex px-8 flex items-center">
                                        <div><a href="my-order-details.html" class="flex items-center no-underline"><span class="text-base font-semibold blue-sure">View details</span><img class="ml-2 w-6" src="<?= $url ?>/assets/dealer/img/arrow_right_blue.png" alt=""></a></div>
                                        <div class="order-2 flex items-center ml-auto">
                                            <span class="text-base checkout-color-unactive">Total:&nbsp;</span>
                                            <span class="font-medium text-2xl checkout-color-red-text">$96.00</span>
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