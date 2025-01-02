<?php /* Template Name: Dealer-Dashboard */ ?>
<?php

$url = get_template_directory_uri();
get_header();
?>
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
<div class="relative">
    <div class="grid 2xl:grid-cols-7 xl:grid-cols-7 md:grid-cols-8 sm:grid-cols-1 grid-cols-1 gap-0">
    <div class="flex items-start col-span-1 2xl:col-span-1 xl:col-span-1 md:col-span-2 grid bg-white md:h-screen height-full">
            <ul class="list-none pl-0 text-left mt-0 memubar" x-data="{ top: true }">
                <li class="flex items-center pt-4 pb-4 pl-6 item-f dashboard checkout-menu-active">
                <a class="flex items-center no-underline" href="<?= home_url() ?>/dealer-dashboard"><img class="mr-3 w-6 active" src="<?= $url ?>/assets/dealer/img/cuida_ac.png" alt=""><span class="menu-item">Dashboard</span></a>
                </li>
                <li class="flex items-center pt-4 pb-4 pl-6 item-f my-order">
                <a class="flex items-center no-underline menu-item" href="<?= home_url() ?>/dealer-order-info"><img class="mr-3 w-6 default" src="<?= $url ?>/assets/dealer/img/cart.png" alt=""><img class="mr-3 w-6 active hidden" src="<?= $url ?>/assets/dealer/img/cart_ac.png " alt=""><span class="menu-item">My orders</span></a>
                </li>
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
                </ul>
                </li>
            </ul>
        </div>
        <div class="col-span-6 text-center py-6 px-4 m-h-content">
            <div class="mx-auto relative md:w-11/12 w-full bg-white md:y-6 md:px-10 p-4 rounded-xl mb-8">
                <div class="md:mb-10 mb-4 flex items-center grid grid-cols-2">
                    <div class="text-left">
                        <h4 class="text-left m-0 color-vector text-28 font-semibold">Overview</h4>
                        <p class="mt-1 md:mb-0 mb-4">Monitor key metrics to boost efficiency!</p>
                    </div>
                    <div class="search relative flex items-center xl:ml-auto md:order-2 order-0 xl:col-span-1 col-span-12 ml-0">
                        <div x-data="app()" x-init="initDate();" x-cloak>
                            <div class="relative" @keydown.escape="closeDatepicker()" @click.away="closeDatepicker()">
                                <div class="flex items-center">
                                    <span class="text-lg font-normal color-vector mr-3">Date Range: </span>
                                    <div class="border rounded-lg p-2 w-48 flex justify-center">
                                        <input type="text" name="from" @click="endToShow = 'from'; initDate(); showDatepicker = true" x-model="dateFromValue" :class="{'font-semibold': endToShow == 'from' }" class="w-20 text-center" readonly />
                                        <span class="icon-center" :class="dateFromValue ? 'block' : 'hidden'">-</span>
                                        <input type="text" name="to" @click="endToShow = 'to'; initDate(); showDatepicker = true" x-model="dateToValue" :class="{'font-semibold': endToShow == 'to' }" class="w-20 text-center" readonly/>
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
                <div class="grid lg:grid-cols-12 gap-8 static">
                    <div class="flex items-start col-span-4">
                        <div class="image mr-4"><img class="w-24 h-auto" src="<?= $url ?>/assets/dealer/img/statis-1.png" alt=""></div>
                        <div class="text-left">
                            <p class="chart-font-size font-normal mt-0 mb-2">Revenue</p>
                            <p class="text-32 font-semibold chart-color-green my-1">$5,000.23 </p>
                        </div>
                    </div>
                    <div class="flex items-start col-span-4">
                        <div class="image mr-4"><img class="w-24 h-auto" src="<?= $url ?>/assets/dealer/img/statis-2.png" alt=""></div>
                        <div class="text-left">
                            <p class="chart-font-size font-normal mt-0 mb-2">Commission</p>
                            <p class="text-32 font-semibold chart-color-green my-1">$523.84</p>
                        </div>
                    </div>
                    <div class="flex items-start col-span-4">
                        <div class="image mr-4"><img class="w-24 h-auto" src="<?= $url ?>/assets/dealer/img/statis-3.png" alt=""></div>
                        <div class="text-left">
                            <p class="chart-font-size font-normal mt-0 mb-2">Orders</p>
                            <p class="text-32 font-semibold chart-color-blue my-1">23 Orders</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mx-auto relative md:w-11/12 grid grid-cols-12 gap-8">
                <div class="xl:col-span-4 col-span-12">
                    <div class="text-left bg-white rounded-xl py-6 px-10 mb-5">
                        <p class="text-18 font-normal mt-0 mb-1">Total clicks</p>
                        <p class="chart-color-yellow text-xl font-semibold my-0">123.000.000</p>
                    </div>
                    <div class="text-left bg-white rounded-xl py-6 px-10 mb-5">
                        <p class="text-18 font-normal mt-0 mb-1">Monthly Revenue</p>
                        <p class="chart-color-blue-2 text-xl font-semibold mt-0 mb-6">$7,000</p>
                        <p class="text-18 font-normal mt-0 mb-1">Monthly Order</p>
                        <p class="chart-color-blue-3 text-xl font-semibold my-0">900 orders</p>
                    </div>
                    <div class="text-left bg-white rounded-xl py-6 px-10">
                        <p class="text-18 font-normal mt-0 mb-1">Weekly Revenue</p>
                        <p class="chart-color-blue-2 text-xl font-semibold mt-0 mb-6">$7,000</p>
                        <p class="text-18 font-normal mt-0 mb-1">Weekly Order</p>
                        <p class="chart-color-blue-3 text-xl font-semibold my-0">900 orders</p>
                    </div>
                </div>
                <div class="xl:col-span-8 col-span-12">
                    <div class="min-w-screen bg-white flex items-start justify-center px-5 py-5">

                        <div class="text-gray-500 rounded py-5 px-5 w-full" x-data="{chartData:chartData()}" x-init="chartData.fetch()">
                            <h5 class="chart-font-size font-normal p-0 m-0 pb-6 text-left">Today's Revenue</h5>
                            <div class="grid grid-cols-12 mb-10">
                                <div class="md:col-span-6 col-span-12 flex items-center text-left">
                                    <ul class="list-none flex pl-0 m-0">
                                        <li class="flex items-center mr-10"><img class="w-4 mr-2" src="<?= $url ?>/assets/dealer/img/e1.png" alt=""><span class="chart-font-size">Revenue</span></li>
                                        <li class="flex items-center"><img class="w-4 mr-2" src="<?= $url ?>/assets/dealer/img/e2.png" alt=""><span>Order</span></li>
                                    </ul>

                                </div>
                                <div class="relative md:col-span-6 col-span-12 flex md:justify-end justify-start" @click.away="chartData.showDropdown=false">
                                    <ul class="list-reset text-xs flex list-none pl-0">
                                        <template x-for="(item,index) in chartData.options">
                                            <li class="px-4 transition-colors duration-100 cursor-pointer text-base color-option-chart" :class="{'font-medium active':index==chartData.selectedOption}" @click="chartData.selectOption(index);chartData.showDropdown=false">
                                                <span x-text="item.label"></span>
                                            </li>
                                        </template>
                                    </ul>
                                </div>
                            </div>
                            <div>
                                <canvas id="chart"></canvas>  
                                    <div id="chartjs-tooltip" class="absolute opacity-0 pointer-events-none py-4 px-6 text-base"><table></table></div>  
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> 
    </div>
</div>
<script src="<?= $url ?>/assets/dealer/js/affiliate.js"></script>
<script src="<?= $url ?>/assets/dealer/js/doashboard.js"></script>
<?php get_footer() ?>