<?php /* Template Name: affiliate orders */ ?>
<?php
get_header('dealer');
$url = get_template_directory_uri();
?>
<script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.js" defer></script>
<div class="relative">
    <div class="grid 2xl:grid-cols-7 xl:grid-cols-7 md:grid-cols-8 sm:grid-cols-1 grid-cols-1 gap-0">
        <div class="flex items-start col-span-1 2xl:col-span-1 xl:col-span-1 md:col-span-2 grid bg-white md:h-screen h-auto">
            <ul class="list-none pl-0 text-left mt-0 memubar" x-data="{ top: false }">
                <li class="flex items-center pt-4 pb-4 pl-6 item-f dashboard">
                    <a class="flex items-center no-underline" href=""><img class="mr-3 w-6 default" src="img/cuida.png" alt=""><img class="mr-3 w-6 hidden active" src="img/cuida_ac.png" alt=""><span class="menu-item">Dashboard</span></a>
                </li>
                <li class="flex items-center pt-4 pb-4 pl-6 item-f my-order">
                    <a class="flex items-center no-underline menu-item" href="my-order.html"><img class="mr-3 w-6 default" src="img/cart.png" alt=""><img class="mr-3 w-6 active hidden" src="img/cart_ac.png " alt=""><span class="menu-item">My orders</span></a>
                </li>
                <li class="flex items-center pt-4 pb-4 pl-6 item-f management checkout-menu-active">
                    <a class="flex items-center no-underline menu-item" href="affiliate-order.html"><img class="mr-3 w-6 default hidden" src="img/tabler_shopping-bag-discount.png" alt=""><img class="mr-3 w-6 active" src="img/tabler_shopping-bag-discount_ac.png" alt=""><span class="menu-item">Affiliate orders</span></a>
                </li>
                <li class="flex items-center pt-4 pb-4 pl-6 item-f management">
                    <a class="flex items-center no-underline menu-item" href="affiliate-customization.html"><img class="mr-3 w-6 default" src="img/material-symbols_link.png" alt=""><img class="mr-3 w-6 hidden active" src="img/material-symbols_link_ac.png" alt=""><span class="menu-item">Affiliate link<br>customization</span></a>
                </li>
                <li class="flex items-center pt-4 pb-4 pl-6 item-f management">
                    <a class="flex items-center no-underline menu-item" href="my-offer.html"><img class="mr-3 w-6 default" src="img/lineicons_offer.png" alt=""><img class="mr-3 w-6 hidden active" src="img/lineicons_offer.png" alt=""><span class="menu-item">My offer</span></a>
                </li>
                <li class="flex items-center pt-4 pb-4 pl-6 item-f management">
                    <a class="flex items-center no-underline menu-item" href="point-management.html"><img class="mr-3 w-6 default" src="img/reward.png" alt=""><img class="mr-3 w-6 hidden active" src="img/reward_ac.png" alt=""><span class="menu-item">Point management</span></a>
                </li>
                <li class="pt-4 pb-4 pl-6 item-f account" >
                    <div class="flex items-center no-underline w-full" href="" x-on:click="top = ! top"><img class="mr-3 w-6 default" src="img/circle.png" alt=""><img class="mr-3 w-6 active hidden" src="img/circle_ac.png" alt="">
                        <span class="menu-item cursor-pointer">Account<br>information</span>
                        <div class="order-2 ml-auto mr-4">
                            <img class="w-6" :src="top ? 'img/icon_down.png' : 'img/icon_up.png'"  alt="">
                        </div>
                    </div>
                </li>
                <li class="pl-6">
                    <ul class="list-none" x-show="top">
                        <li class="mt-2">
                            <a class="no-underline text-sm" href="personal-info.html">Personal information</a>
                        </li>
                        <li class="mt-2">
                            <a class="no-underline text-sm" href="business-information.html">Business information</a>
                        </li>
                        <li class="mt-2">
                            <a class="no-underline text-sm" href="bank-account-information.html">Bank account information</a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
        <div class="col-span-6 text-center py-6 px-4">
            <div class="mx-auto relative md:w-11/12 bg-white md:p-8 p-4 rounded-xl mb-8">
                <div class="mb-4 flex items-center grid grid-cols-2">
                    <h4 class="text-left m-0 color-vector text-28 font-semibold">Filters</h4>
                    <div class="search relative flex items-center xl:ml-auto md:order-2 order-0 xl:col-span-1 col-span-12 ml-0">
                        <div x-data="app()" x-init="initDate();" x-cloak>
                            <div class="relative" @keydown.escape="closeDatepicker()" @click.away="closeDatepicker()">
                                <div class="flex items-center">
                                    <span class="text-lg font-normal color-vector mr-3">Date Range: </span>
                                    <div class="border rounded-lg p-2 w-48 flex justify-center">
                                        <input type="text" name="from" @click="endToShow = 'from'; initDate(); showDatepicker = true" x-model="dateFromValue" :class="{'font-semibold': endToShow == 'from' }" class="w-20 text-center" readonly />
                                        <span class="icon-center" :class="dateFromValue ? 'block' : 'hidden'">-</span>
                                        <input type="text" name="to" @click="endToShow = 'to'; initDate(); showDatepicker = true" x-model="dateToValue" :class="{'font-semibold': endToShow == 'to' }" class="w-20 text-center" readonly />
                                    </div>
                                </div>
                                <div
                                    class="bg-white mt-12 rounded-lg shadow p-4 absolute top-0 left-0"
                                    style="width: 17rem; z-index: 1;"
                                    x-show.transition="showDatepicker"
                                >

                                    <div class="flex justify-between items-center mb-2">
                                        <div>
                                            <span x-text="MONTH_NAMES[month]" class="text-lg font-bold text-gray-800"></span>
                                            <span x-text="year" class="ml-1 text-lg text-gray-600 font-normal"></span>
                                        </div>
                                        <div>
                                            <button
                                                type="button"
                                                class="transition ease-in-out duration-100 inline-flex cursor-pointer hover:bg-gray-200 p-1 rounded-full"
                                                @click="if (month == 0) {year--; month=11;} else {month--;} getNoOfDays()">
                                                <svg class="h-6 w-6 text-gray-500 inline-flex"  fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                                </svg>
                                            </button>
                                            <button
                                                type="button"
                                                class="transition ease-in-out duration-100 inline-flex cursor-pointer hover:bg-gray-200 p-1 rounded-full"
                                                @click="if (month == 11) {year++; month=0;} else {month++;}; getNoOfDays()">
                                                <svg class="h-6 w-6 text-gray-500 inline-flex"  fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="flex flex-wrap mb-3 -mx-1">
                                        <template x-for="(day, index) in DAYS" :key="index">
                                            <div style="width: 14.26%" class="px-1">
                                                <div
                                                    x-text="day"
                                                    class="text-gray-800 font-medium text-center text-xs"
                                                ></div>
                                            </div>
                                        </template>
                                    </div>

                                    <div class="flex flex-wrap -mx-1">
                                        <template x-for="blankday in blankdays">
                                            <div
                                                style="width: 14.28%"
                                                class="text-center border p-1 border-transparent text-sm"
                                            ></div>
                                        </template>
                                        <template x-for="(date, dateIndex) in no_of_days" :key="dateIndex">
                                            <div style="width: 14.28%">
                                                <div
                                                    @click="getDateValue(date)"
                                                    x-text="date"
                                                    class="p-1 cursor-pointer text-center text-sm leading-none hover:bg-blue-200 leading-loose transition ease-in-out duration-100"
                                                    :class="{'font-bold': isToday(date) == true, 'bg-blue-800 text-white rounded-l-full': isDateFrom(date) == true, 'bg-blue-800 text-white rounded-r-full': isDateTo(date) == true, 'bg-blue-200': isInRange(date) == true, 'cursor-not-allowed opacity-25': isDisabled(date) }"
                                                    :disabled="isDisabled(date) ? true : false"
                                                ></div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-12 gap-6">
                    <div class="2xl:col-span-4 md:col-span-6 col-span-12">
                        <div class="text-left">
                            <label for="order_code" class="block mb-0 text-base font-medium text-gray-900 dark:text-white mb-1">Order code</label>
                            <input type="text" id="order_code" class="box-border bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-3 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="#123ABCXYZ" autocomplete="off">
                        </div>
                    </div>
                    <div class="2xl:col-span-4 md:col-span-6 col-span-12">
                        <div class="text-left">
                            <label for="order_code" class="block mb-0 text-base font-medium text-gray-900 dark:text-white mb-1">Payment status</label>
                            <div class="grid">
                                <select id="countries" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-3 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 appearance-none select-bg" >
                                    <option value="all" selected>All</option>
                                    <option value="pending">Pending</option>
                                    <option value="CA">Complete</option>
                                    <option value="cancel">Cancel</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="2xl:col-span-4 col-span-12 flex items-end pb-1 ">
                        <button class="text-white w-full checkout-bg checkout-tag-color py-3 px-8 rounded-lg text-base font-semibold">Reset filters</button>
                        <button class="text-white w-full bg-red-sure py-3 px-8 rounded-lg text-base font-semibold ml-6">Apply</button>
                    </div>
                </div>
            </div>
            <div class="mx-auto relative overflow-hidden bg-white rounded-xl md:w-11/12">
                <div class="relative">
                    <div class="relative h-full w-full rounded-xl bg-white shadow-[0px_0px_0px_1px_rgba(9,9,11,0.07),0px_2px_2px_0px_rgba(9,9,11,0.05)] dark:bg-zinc-900 dark:shadow-[0px_0px_0px_1px_rgba(255,255,255,0.1)] dark:before:pointer-events-none dark:before:absolute dark:before:-inset-px dark:before:rounded-xl dark:before:shadow-[0px_2px_8px_0px_rgba(0,_0,_0,_0.20),_0px_1px_0px_0px_rgba(255,_255,_255,_0.06)_inset] forced-colors:outline">
                        <div class="grid h-full w-full justify-items-center overflow-hidden place-items-start justify-items-center">
                            <div class="w-full min-w-0">
                                <div class="flow-root">
                                    <div class="mt-6 [--gutter:theme(spacing.6)] sm:[--gutter:theme(spacing.8)] lg:[--gutter:theme(spacing.12)] -mx-[--gutter] overflow-x-auto whitespace-nowrap">
                                        <div class="inline-block m-w-1518-full align-middle sm:px-[--gutter]">
                                            <div class="grid grid-cols-7 bd-line-bottom">
                                                <div class="col-span-1 px-10 py-5 font-medium text-left">Order code</div>
                                                <div class="col-span-1 px-10 py-5 font-medium text-left">Purchase time</div>
                                                <div class="col-span-1 px-10 py-5 font-medium text-left">Payment status</div>
                                                <div class="col-span-1 px-10 py-5 font-medium text-right">Payment</div>
                                                <div class="col-span-1 px-10 py-5 font-medium text-right">Revenue</div>
                                                <div class="col-span-1 px-10 py-5 font-medium text-right">Commission</div>
                                                <div class="col-span-1 px-10 py-5 font-medium"></div>
                                            </div>
                                            <div class="grid grid-cols-7 bd-line-bottom">
                                                <div class="col-span-1 px-10 py-5 text-base text-left">#123ABCXYZ</div>
                                                <div class="col-span-1 px-10 py-5 text-base text-left">11/05/2023 - 7:05 PM</div>
                                                <div class="col-span-1 px-10 py-5 text-left"><span class="text-center color-cancel bg-cancel process px-2 py-1 font-medium ml-auto order-2 text-sm">Canceled</span></div>
                                                <div class="col-span-1 px-10 py-5 text-base text-right">$5460.00</div>
                                                <div class="col-span-1 px-10 py-5 text-base text-right">$5460.00</div>
                                                <div class="col-span-1 px-10 py-5 text-base text-right">$5460.00 </div>
                                                <div class="col-span-1 px-10 py-5 text-base text-center"><a href="affiliate-order-detail.html" class="flex items-center no-underline"><span class="text-base font-semibold blue-sure">View detail</span><img class="ml-2 w-6" src="img/arrowright_tb.png" alt=""></a></div>
                                            </div>
                                            <div class="grid grid-cols-7 bd-line-bottom">
                                                <div class="col-span-1 px-10 py-5 text-base text-left">#123ABCXYZ</div>
                                                <div class="col-span-1 px-10 py-5 text-base text-left">11/05/2023 - 7:05 PM</div>
                                                <div class="col-span-1 px-10 py-5 text-left"><span class="text-center color-cancel bg-cancel process px-2 py-1 font-medium ml-auto order-2 text-sm">Canceled</span></div>
                                                <div class="col-span-1 px-10 py-5 text-base text-right">$5460.00</div>
                                                <div class="col-span-1 px-10 py-5 text-base text-right">$5460.00</div>
                                                <div class="col-span-1 px-10 py-5 text-base text-right">$5460.00 </div>
                                                <div class="col-span-1 px-10 py-5 text-base text-center"><a href="affiliate-order-detail.html" class="flex items-center no-underline"><span class="text-base font-semibold blue-sure">View detail</span><img class="ml-2 w-6" src="img/arrowright_tb.png" alt=""></a></div>
                                            </div>
                                            <div class="grid grid-cols-7 bd-line-bottom">
                                                <div class="col-span-1 px-10 py-5 text-base text-left">#123ABCXYZ</div>
                                                <div class="col-span-1 px-10 py-5 text-base text-left">11/05/2023 - 7:05 PM</div>
                                                <div class="col-span-1 px-10 py-5 text-left"><span class="text-center color-cancel bg-cancel process px-2 py-1 font-medium ml-auto order-2 text-sm">Canceled</span></div>
                                                <div class="col-span-1 px-10 py-5 text-base text-right">$5460.00</div>
                                                <div class="col-span-1 px-10 py-5 text-base text-right">$5460.00</div>
                                                <div class="col-span-1 px-10 py-5 text-base text-right">$5460.00 </div>
                                                <div class="col-span-1 px-10 py-5 text-base text-center"><a href="affiliate-order-detail.html" class="flex items-center no-underline"><span class="text-base font-semibold blue-sure">View detail</span><img class="ml-2 w-6" src="img/arrowright_tb.png" alt=""></a></div>
                                            </div>
                                            <div class="grid grid-cols-7 bd-line-bottom">
                                                <div class="col-span-1 px-10 py-5 text-base text-left">#123ABCXYZ</div>
                                                <div class="col-span-1 px-10 py-5 text-base text-left">11/05/2023 - 7:05 PM</div>
                                                <div class="col-span-1 px-10 py-5 text-left"><span class="text-center color-cancel bg-cancel process px-2 py-1 font-medium ml-auto order-2 text-sm">Canceled</span></div>
                                                <div class="col-span-1 px-10 py-5 text-base text-right">$5460.00</div>
                                                <div class="col-span-1 px-10 py-5 text-base text-right">$5460.00</div>
                                                <div class="col-span-1 px-10 py-5 text-base text-right">$5460.00 </div>
                                                <div class="col-span-1 px-10 py-5 text-base text-center"><a href="affiliate-order-detail.html" class="flex items-center no-underline"><span class="text-base font-semibold blue-sure">View detail</span><img class="ml-2 w-6" src="img/arrowright_tb.png" alt=""></a></div>
                                            </div>
                                            <div class="grid grid-cols-7 bd-line-bottom">
                                                <div class="col-span-1 px-10 py-5 text-base text-left">#123ABCXYZ</div>
                                                <div class="col-span-1 px-10 py-5 text-base text-left">11/05/2023 - 7:05 PM</div>
                                                <div class="col-span-1 px-10 py-5 text-left"><span class="text-center color-cancel bg-cancel process px-2 py-1 font-medium ml-auto order-2 text-sm">Canceled</span></div>
                                                <div class="col-span-1 px-10 py-5 text-base text-right">$5460.00</div>
                                                <div class="col-span-1 px-10 py-5 text-base text-right">$5460.00</div>
                                                <div class="col-span-1 px-10 py-5 text-base text-right">$5460.00 </div>
                                                <div class="col-span-1 px-10 py-5 text-base text-center"><a href="affiliate-order-detail.html" class="flex items-center no-underline"><span class="text-base font-semibold blue-sure">View detail</span><img class="ml-2 w-6" src="img/arrowright_tb.png" alt=""></a></div>
                                            </div>
                                            <div class="grid grid-cols-7 bd-line-bottom">
                                                <div class="col-span-1 px-10 py-5 text-base text-left">#123ABCXYZ</div>
                                                <div class="col-span-1 px-10 py-5 text-base text-left">11/05/2023 - 7:05 PM</div>
                                                <div class="col-span-1 px-10 py-5 text-left"><span class="text-center color-cancel bg-cancel process px-2 py-1 font-medium ml-auto order-2 text-sm">Canceled</span></div>
                                                <div class="col-span-1 px-10 py-5 text-base text-right">$5460.00</div>
                                                <div class="col-span-1 px-10 py-5 text-base text-right">$5460.00</div>
                                                <div class="col-span-1 px-10 py-5 text-base text-right">$5460.00 </div>
                                                <div class="col-span-1 px-10 py-5 text-base text-center"><a href="affiliate-order-detail.html" class="flex items-center no-underline"><span class="text-base font-semibold blue-sure">View detail</span><img class="ml-2 w-6" src="img/arrowright_tb.png" alt=""></a></div>
                                            </div>
                                            <div class="grid grid-cols-7 bd-line-bottom">
                                                <div class="col-span-1 px-10 py-5 text-base text-left">#123ABCXYZ</div>
                                                <div class="col-span-1 px-10 py-5 text-base text-left">11/05/2023 - 7:05 PM</div>
                                                <div class="col-span-1 px-10 py-5 text-left"><span class="text-center color-cancel bg-cancel process px-2 py-1 font-medium ml-auto order-2 text-sm">Canceled</span></div>
                                                <div class="col-span-1 px-10 py-5 text-base text-right">$5460.00</div>
                                                <div class="col-span-1 px-10 py-5 text-base text-right">$5460.00</div>
                                                <div class="col-span-1 px-10 py-5 text-base text-right">$5460.00 </div>
                                                <div class="col-span-1 px-10 py-5 text-base text-center"><a href="affiliate-order-detail.html" class="flex items-center no-underline"><span class="text-base font-semibold blue-sure">View detail</span><img class="ml-2 w-6" src="img/arrowright_tb.png" alt=""></a></div>
                                            </div>
                                            <div class="grid grid-cols-7 bd-line-bottom">
                                                <div class="col-span-1 px-10 py-5 text-base text-left">#123ABCXYZ</div>
                                                <div class="col-span-1 px-10 py-5 text-base text-left">11/05/2023 - 7:05 PM</div>
                                                <div class="col-span-1 px-10 py-5 text-left"><span class="text-center color-cancel bg-cancel process px-2 py-1 font-medium ml-auto order-2 text-sm">Canceled</span></div>
                                                <div class="col-span-1 px-10 py-5 text-base text-right">$5460.00</div>
                                                <div class="col-span-1 px-10 py-5 text-base text-right">$5460.00</div>
                                                <div class="col-span-1 px-10 py-5 text-base text-right">$5460.00 </div>
                                                <div class="col-span-1 px-10 py-5 text-base text-center"><a href="affiliate-order-detail.html" class="flex items-center no-underline"><span class="text-base font-semibold blue-sure">View detail</span><img class="ml-2 w-6" src="img/arrowright_tb.png" alt=""></a></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center justify-center w-full max-w-full xl:col-span-3 md:col-span-4 sm:col-span-1 my-5" aria-label="Pagination">
                        <a href="#" class="text-center w-12 text-base relative inline-flex items-center rounded-xl px-2 py-3 border bg-white mr-4 no-underline justify-center">
                            <img src="img/caretleft.png" alt="">
                        </a>
                        <a href="#" aria-current="page" class="w-12 rounded-xl border text-center no-underline bg-blue-sure text-white inline-block px-2 py-2 mr-2">1</a>
                        <a href="#" class="w-12 rounded-xl border text-center no-underline bg-white inline-block px-2 py-2 mr-2">2</a>
                        <a href="#" class="w-12 rounded-xl border text-center no-underline bg-white inline-block px-2 py-2 mr-2">3</a>
                        <a href="#" class="w-12 rounded-xl border text-center no-underline bg-white inline-block px-2 py-2 mr-2">4</a>
                        <a href="#" class="w-12 rounded-xl border text-center no-underline bg-white inline-block px-2 py-2 mr-0">5</a>
                        <a href="#" class="text-center w-12 text-base relative inline-flex items-center rounded-xl px-2 py-3 border bg-white ml-4 no-underline justify-center">
                            <img src="img/caretright.png" alt="">
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php get_footer('dealer') ?>
<script src="<?= $url ?>/dist/js/affiliate.js"></script>