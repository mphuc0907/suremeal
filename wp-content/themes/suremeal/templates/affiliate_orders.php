<?php /* Template Name: affiliate orders */ ?>
<?php
get_header('dealer');
$url = get_template_directory_uri();
$authenticated_user = validate_user_token();
if ($authenticated_user) {
    $id_user = $authenticated_user->ID;
}
global $wpdb;
$curron_aff = $wpdb->get_row($wpdb->prepare("SELECT * FROM wp_affiliate WHERE id_user = %s", $id_user));
if ($curron_aff) {
    $distribution_code = $curron_aff->distribution_code;
    $percent = $curron_aff->percent;
}

// Initialize parameters
$where_conditions = array();
$where_params = array($distribution_code);
$types = array('%s');

// Date range filter
if (!empty($_GET['from']) && !empty($_GET['to'])) {
    $from = DateTime::createFromFormat('d/m/Y', $_GET['from']);
    $to = DateTime::createFromFormat('d/m/Y', $_GET['to']);
    
    if ($from && $to) {
        $from_timestamp = $from->setTime(0, 0, 0)->getTimestamp();
        $to_timestamp = $to->setTime(23, 59, 59)->getTimestamp();
        
        $where_conditions[] = "time_order BETWEEN %d AND %d";
        $where_params[] = $from_timestamp;
        $where_params[] = $to_timestamp;
        $types[] = '%d';
        $types[] = '%d';
    }
}

// Order code filter
if (!empty($_GET['order_code'])) {
    $order_code = trim($_GET['order_code']);
    $where_conditions[] = "order_code LIKE %s";
    $where_params[] = '%' . $wpdb->esc_like($order_code) . '%';
    $types[] = '%s';
}

// Status filter
if (!empty($_GET['status']) && $_GET['status'] !== 'all') {
    $status = intval($_GET['status']);
    $where_conditions[] = "status = %d";
    $where_params[] = $status;
    $types[] = '%d';
}

// Build count query
$count_query = "SELECT COUNT(*) FROM wp_orders WHERE distribution_code = %s";
if (!empty($where_conditions)) {
    $count_query .= " AND " . implode(" AND ", $where_conditions);
}

// Get total records
$recordcount = $wpdb->get_var($wpdb->prepare($count_query, $where_params));

// Pagination variables
$pagesize = 10;
$total_pages = ceil($recordcount / $pagesize);
$paged = max(1, get_query_var('paged', 1));
$offset = ($paged - 1) * $pagesize;

// Build data query
$data_query = "SELECT * FROM wp_orders WHERE distribution_code = %s";
if (!empty($where_conditions)) {
    $data_query .= " AND " . implode(" AND ", $where_conditions);
}
$data_query .= " ORDER BY time_order DESC LIMIT %d OFFSET %d";

// Add pagination parameters to query
$data_params = array_merge($where_params, array($pagesize, $offset));

// Execute query
$myrows = $wpdb->get_results($wpdb->prepare($data_query, $data_params));
?>
<div class="col-span-6 text-center p-8">
    <form method="get" action="" class="mx-auto relative md:w-12/12 bg-white rounded-xl px-10 py-6 mb-8">
        <div class="mb-6 md:flex block items-center justify-between">
            <h4 class="text-left m-0 color-vector text-28 font-semibold line-height-40">Filters</h4>
            <div class="search relative md:flex block items-center xl:ml-auto order-2 xl:col-span-1 ml-0 md:justify-end justify-start mx-100-991">
                <div x-data="app()" x-init="initDate();" x-cloak>
                    <div class="relative" @keydown.escape="closeDatepicker()" @click.away="closeDatepicker()">
                        <div class="flex items-center w-full md:justify-end justify-start">
                            <span class="text-lg font-normal color-vector mr-3 w-48 lg:text-center text-left">Date Range: </span>
                            <div class="border rounded-lg p-2 flex justify-center h-48 md:w-249 text-28 w-full">
                                <input type="text" name="from" id="inputFrom" value="<?php echo isset($_GET['from']) ? htmlspecialchars($_GET['from']) : ''; ?>" x-init="dateFromValue = '<?php echo isset($_GET['from']) ? date('d/m/Y', strtotime(str_replace('/', '-', $_GET['from']))) : ''; ?>'" @click="endToShow = 'from'; initDate(); showDatepicker = true" x-model="dateFromValue" :class="{'font-semibold': endToShow == 'from' }" class="w-24 text-center border-none chart-font-size color-vector" readonly />
                                <span class="icon-center date-span chart-font-size color-vector pt-1" :class="dateFromValue ? 'block' : 'hidden'">-</span>
                                <input type="text" name="to" id="inputTo" value="<?php echo isset($_GET['to']) ? htmlspecialchars($_GET['to']) : ''; ?>" x-init="dateFromValue = '<?php echo isset($_GET['to']) ? date('d/m/Y', strtotime(str_replace('/', '-', $_GET['to']))) : ''; ?>'" @click="endToShow = 'to'; initDate(); showDatepicker = true" x-model="dateToValue" :class="{'font-semibold': endToShow == 'to' }" class="w-24 text-center border-none chart-font-size color-vector" readonly />
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
        <div class="grid grid-cols-11 gap-6">
            <div class="2xl:col-span-4 md:col-span-6 col-span-12">
                <div class="text-left">
                    <label for="order_code" class="block mb-0 text-base font-medium text-gray-900 dark:text-white mb-1">Order code</label>
                    <input type="text" name="order_code" id="order_code" value="<?php echo isset($_GET['order_code']) ? htmlspecialchars($_GET['order_code']) : ''; ?>" class="box-border bg-input border border-gray-300 text-gray-900 text-base rounded-lg block w-full pd-11-cus" placeholder="#123ABCXYZ" autocomplete="off">
                </div>
            </div>
            <div class="2xl:col-span-4 md:col-span-6 col-span-12">
                <div class="text-left">
                    <label for="status" class="block mb-0 text-base font-medium text-gray-900 dark:text-white mb-1">Payment status</label>
                    <div class="grid">
                        <select name="status" id="status_filter" class="bg-gray-50 border border-gray-300 text-gray-900 text-base rounded-lg block w-full p-3 appearance-none select-bg h-48">
                            <option value="all">All</option>
                            <option value="1" <?php echo (isset($_GET['status']) && $_GET['status'] == '1') ? 'selected' : ''; ?>>Processing</option>
                            <option value="2" <?php echo (isset($_GET['status']) && $_GET['status'] == '2') ? 'selected' : ''; ?>>Completed</option>

                            <option value="4" <?php echo (isset($_GET['status']) && $_GET['status'] == '4') ? 'selected' : ''; ?>>Cancelled</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="2xl:col-span-3 col-span-12 flex items-end">
                <a href="<?php echo strtok($_SERVER["REQUEST_URI"], '?'); ?>" class="btn-reset text-white w-full checkout-bg checkout-tag-color py-3 rounded-lg text-base font-semibold text-center">Reset filters</a>
                <button type="submit" class="text-white w-full bg-red-sure py-3 rounded-lg text-base font-semibold ml-6">Apply</button>
            </div>
        </div>
    </form>
    <div class="mx-auto relative overflow-hidden bg-white rounded-xl md:w-12/12">
        <div class="relative">
            <div class="relative h-full w-full rounded-xl bg-white shadow-[0px_0px_0px_1px_rgba(9,9,11,0.07),0px_2px_2px_0px_rgba(9,9,11,0.05)] dark:bg-zinc-900 dark:shadow-[0px_0px_0px_1px_rgba(255,255,255,0.1)] dark:before:pointer-events-none dark:before:absolute dark:before:-inset-px dark:before:rounded-xl dark:before:shadow-[0px_2px_8px_0px_rgba(0,_0,_0,_0.20),_0px_1px_0px_0px_rgba(255,_255,_255,_0.06)_inset] forced-colors:outline">
                <div class="grid h-full w-full justify-items-center overflow-hidden place-items-start justify-items-center">
                    <div class="w-full min-w-0">
                        <div class="flow-root">
                            <div class="[--gutter:theme(spacing.6)] sm:[--gutter:theme(spacing.8)] lg:[--gutter:theme(spacing.12)] -mx-[--gutter] overflow-x-auto whitespace-nowrap">
                                <div class="inline-block m-w-1518-full align-middle sm:px-[--gutter]">
                                    <div class="grid grid-cols-8 bd-line-bottom">
                                        <div class="col-span-1 px-10 py-5 font-medium text-left w-228-67">No.</div>
                                        <div class="col-span-1 px-10 py-5 font-medium text-left w-228-67">Order code</div>
                                        <div class="col-span-1 px-10 py-5 font-medium text-left w-228-67">Purchase time</div>
                                        <div class="col-span-1 px-10 py-5 font-medium text-left w-228-67">Payment status</div>
                                        <div class="col-span-1 px-10 py-5 font-medium text-right w-228-67">Payment</div>
                                        <div class="col-span-1 px-10 py-5 font-medium text-right w-228-67">Revenue</div>
                                        <div class="col-span-1 px-10 py-5 font-medium text-right w-228-67">Commission</div>
                                        <div class="col-span-1 px-10 py-5 font-medium"></div>
                                    </div>
                                    <?php
                                    $i =  0;
                                    foreach ($myrows as $key => $value) :
                                        $i++;
                                            $price = $value->price;
//                                            $percent_price =
                                        $result = ($price * $percent) / 100;
                                        $class = "";
                                        $name = "";
                                        $status = $value->status;
                                        if ($status == 1) {
                                            $class = 'orderdetail-color-yellow process';
                                            $name = 'Processing';
                                        } elseif ($status == 2) {
                                            $class = 'orderdetail-bg-blue-sure color-complete';
                                            $name = 'Completed';
                                        } elseif ($status == 4) {
                                            $class = 'checkout-color-red-text color-cancel';
                                            $name = 'Canceled';
                                        } else {
                                            $class = 'orderdetail-color-yellow processe';
                                            $name = 'In progress';
                                        }
                                        ?>
                                    <div class="grid grid-cols-8 bd-line-bottom">
                                        <div class="col-span-1 px-10 py-5 text-base text-left"><?= $i  ?></div>
                                        <div class="col-span-1 px-10 py-5 text-base text-left">#<?= $value->order_code ?></div>
                                        <div class="col-span-1 px-10 py-5 text-base text-left"><?= date('d/m/Y - g:i A', $value->time_order) ?></div>
                                        <div class="col-span-1 px-10 py-5 text-left"><span class="text-center <?= $class ?> process px-2 py-1 font-medium ml-auto order-2 text-sm"><?= $name ?></span></div>
                                        <div class="col-span-1 px-10 py-5 text-base text-right"><?= formatBalance($value->price_payment) ?></div>
                                        <div class="col-span-1 px-10 py-5 text-base text-right"><?= formatBalance($value->price) ?></div>
                                        <!-- <div class="col-span-1 px-10 py-5 text-base text-right"><?= formatBalance($result) ?> </div> -->
                                        <div class="col-span-1 px-10 py-5 text-base text-right">
                                            <?php 
                                                $affiliateProducts = json_decode($value->affiliate_product, true); // Giải mã JSON thành mảng
                                                $affiliate = 0;
                                                if (is_array($affiliateProducts)) { 
                                                    foreach ($affiliateProducts as $item) {  
                                                        $affiliate += $item['affiliate'];
                                                    }
                                                }
                                            ?>
                                            <?= formatBalance($affiliate) ?>
                                        </div>
                                        <div class="col-span-1 px-10 py-5 text-base text-center"><a href="<?= home_url() ?>/affiliate-order-detail?order_code=<?= $value->order_code ?>" class="flex items-center no-underline"><span class="text-base font-medium blue-sure">View detail</span><img class="ml-2 w-6" src="img/arrowright_tb.png" alt=""></a></div>
                                        
                                    </div>
                                    <?php endforeach;?>
                                    <div class="flex py-4 px-2 font-semibold">Total order(<?= $recordcount ?>)</div>
                                    <!-- Add pagination -->
                                    <?php if ($total_pages > 1) : ?>
                                    <div class="pagination flex justify-center items-center gap-4 py-4">
                                        <?php 
                                        $big = 999999999;
                                        echo paginate_links(array(
                                            'base'    => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
                                            'format'  => '?paged=%#%',
                                            'current' => $paged,
                                            'total'   => $total_pages,
                                            'prev_text' => __('<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M10.3537 12.6463C10.4001 12.6927 10.437 12.7479 10.4621 12.8086C10.4872 12.8693 10.5002 12.9343 10.5002 13C10.5002 13.0657 10.4872 13.1308 10.4621 13.1915C10.437 13.2521 10.4001 13.3073 10.3537 13.3538C10.3072 13.4002 10.252 13.4371 10.1914 13.4622C10.1307 13.4873 10.0656 13.5003 9.99991 13.5003C9.93421 13.5003 9.86915 13.4873 9.80846 13.4622C9.74776 13.4371 9.69261 13.4002 9.64615 13.3538L4.64615 8.35375C4.59967 8.30732 4.56279 8.25217 4.53763 8.19147C4.51246 8.13077 4.49951 8.06571 4.49951 8C4.49951 7.9343 4.51246 7.86923 4.53763 7.80853C4.56279 7.74783 4.59967 7.69269 4.64615 7.64625L9.64615 2.64625C9.73997 2.55243 9.86722 2.49973 9.99991 2.49973C10.1326 2.49973 10.2598 2.55243 10.3537 2.64625C10.4475 2.74007 10.5002 2.86732 10.5002 3C10.5002 3.13269 10.4475 3.25993 10.3537 3.35375L5.70678 8L10.3537 12.6463Z" fill="#0F0F0F" /></svg>'),
                                            'next_text' => __('<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none"><path d="M11.3538 8.35375L6.35378 13.3538C6.30733 13.4002 6.25218 13.4371 6.19148 13.4622C6.13079 13.4873 6.06573 13.5003 6.00003 13.5003C5.93434 13.5003 5.86928 13.4873 5.80859 13.4622C5.74789 13.4371 5.69274 13.4002 5.64628 13.3538C5.59983 13.3073 5.56298 13.2521 5.53784 13.1915C5.5127 13.1308 5.49976 13.0657 5.49976 13C5.49976 12.9343 5.5127 12.8693 5.53784 12.8086C5.56298 12.7479 5.59983 12.6927 5.64628 12.6463L10.2932 8L5.64628 3.35375C5.55246 3.25993 5.49976 3.13269 5.49976 3C5.49976 2.86732 5.55246 2.74007 5.64628 2.64625C5.7401 2.55243 5.86735 2.49973 6.00003 2.49973C6.13272 2.49973 6.25996 2.55243 6.35378 2.64625L11.3538 7.64625C11.4003 7.69269 11.4372 7.74783 11.4623 7.80853C11.4875 7.86923 11.5004 7.9343 11.5004 8C11.5004 8.06571 11.4875 8.13077 11.4623 8.19147C11.4372 8.25217 11.4003 8.30732 11.3538 8.35375Z" fill="#0F0F0F"/></svg>'),
                                        ));
                                        ?>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
<!--            <div class="flex items-center justify-center w-full max-w-full xl:col-span-3 md:col-span-4 sm:col-span-1 my-5" aria-label="Pagination">-->
<!--                <a href="#" class="text-center w-10 h-10 text-base relative inline-flex items-center rounded-xl px-2 py-3 border bg-white mr-4 no-underline justify-center border-paginate">-->
<!--                    <img src="--><?//= $url ?><!--/dist/img/caretleft.png" alt="">-->
<!--                </a>-->
<!--                <a href="#" aria-current="page" class="w-10 h-10 rounded-xl border text-center no-underline bg-blue-sure text-white inline-block px-2 py-2 mr-2 border-paginate">1</a>-->
<!--                <a href="#" class="w-10 h-10 rounded-xl border text-center no-underline bg-white inline-block px-2 py-2 mr-2 border-paginate">2</a>-->
<!--                <a href="#" class="w-10 h-10 rounded-xl border text-center no-underline bg-white inline-block px-2 py-2 mr-2 border-paginate">3</a>-->
<!--                <a href="#" class="w-10 h-10 rounded-xl border text-center no-underline bg-white inline-block px-2 py-2 mr-2 border-paginate">4</a>-->
<!--                <a href="#" class="w-10 h-10 rounded-xl border text-center no-underline bg-white inline-block px-2 py-2 mr-0 border-paginate">5</a>-->
<!--                <a href="#" class="text-center w-10 h-10 text-base relative inline-flex items-center rounded-xl px-2 py-3 border bg-white ml-4 no-underline justify-center border-paginate">-->
<!--                    <img src="--><?//= $url ?><!--/dist/img/caretright.png" alt="">-->
<!--                </a>-->
<!--            </div>-->
        </div>
    </div>
</div>
<?php get_footer('dealer') ?>
<script !src="">
    function formatDate(dateStr) {
        let dateParts = dateStr.split('/');
        if (dateParts.length === 3) {
            let month = dateParts[0];
            let day = dateParts[1];
            let year = dateParts[2];
            return `${day}/${month}/${year}`; // Chuyển sang định dạng d/m/Y
        }
        return dateStr;
    }
    $(document).ready(function() {

        var from = '<?= $_GET['from'] ?>';
        var to = '<?= $_GET['to'] ?>';

        var dayFrom = formatDate(from);
        var dayTo = formatDate(to);

        console.log(dayFrom, dayTo);

        setTimeout(function() {
           $('#inputFrom').val(from);
           $('.date-span').css('display', 'block');
           $('#inputTo').val(to);
        }, 1000);
    });
</script>