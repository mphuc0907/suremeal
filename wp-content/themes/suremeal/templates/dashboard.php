<?php /* Template Name: Dashboard */ ?>
<?php
// Khởi tạo các biến cần thiết
global $wpdb;
$url = get_template_directory_uri();
$revenue = 0;
$result = 0;
$total_withdrawal = 0;

// Kiểm tra xác thực người dùng
$authenticated_user = validate_user_token();
$id_user = intval($authenticated_user->ID);

// Lấy thông tin affiliate
$curron_aff = $wpdb->get_row($wpdb->prepare(
    "SELECT distribution_code, percent, total_click FROM wp_affiliate WHERE id_user = %d",
    $id_user
));

$distribution_code = sanitize_text_field($curron_aff->distribution_code);
$percent = floatval($curron_aff->percent);
$total_click = intval($curron_aff->total_click);

// Xử lý lọc theo ngày từ form (định dạng d/m/Y)
$from_date = isset($_GET['from']) ? sanitize_text_field($_GET['from']) : '';
$to_date   = isset($_GET['to']) ? sanitize_text_field($_GET['to']) : '';

if (!empty($from_date) && !empty($to_date)) {
    // Chuyển đổi ngày theo định dạng d/m/Y
    $from_dt = DateTime::createFromFormat('d/m/Y', $from_date);
    $to_dt   = DateTime::createFromFormat('d/m/Y', $to_date);
    if ($from_dt && $to_dt) {
        $from_timestamp = strtotime($from_dt->format('Y-m-d 00:00:00'));
        $to_timestamp   = strtotime($to_dt->format('Y-m-d 23:59:59'));
    } else {
        $from_timestamp = null;
        $to_timestamp   = null;
    }
} else {
    $from_timestamp = null;
    $to_timestamp   = null;
}

// Xây dựng truy vấn SQL động tùy thuộc vào giá trị ngày
$sql = "SELECT * FROM wp_orders WHERE distribution_code = %s AND id_user != %d AND status != 4";
$params = [$distribution_code, $id_user];

if ($from_timestamp !== null && $to_timestamp !== null) {
    $sql .= " AND time_order BETWEEN %d AND %d";
    array_push($params, $from_timestamp, $to_timestamp);
}

$myrows = $wpdb->get_results($wpdb->prepare($sql, ...$params));



// Tính doanh thu (tổng giá trị đơn hàng) và hoa hồng (theo %)
foreach ($myrows as $value) {
    $affiliateProducts = json_decode($value->affiliate_product, true); // Giải mã JSON thành mảng
    $affiliate = 0;
    if (is_array($affiliateProducts)) {
        foreach ($affiliateProducts as $item) {
            $affiliate += $item['affiliate'];
        }
    }
    $result += $affiliate;
    $revenue += floatval($value->price);
}
//print_r($result);die();

// Tính tổng số tiền đã rút
$withdrawals = $wpdb->get_results($wpdb->prepare(
    "SELECT withdrawal_amount FROM log_withdrawal WHERE id_user = %d",
    $id_user
));
foreach ($withdrawals as $row) {
    $total_withdrawal += floatval($row->withdrawal_amount);
}
$result = $result - $total_withdrawal;

// Thống kê theo tháng (30 ngày gần nhất)
$month_start = strtotime('-30 days 00:00:00');
$month_end   = strtotime('today 23:59:59');

$total_price = $wpdb->get_var($wpdb->prepare(
    "SELECT COALESCE(SUM(price), 0) FROM wp_orders 
     WHERE distribution_code = %s 
       AND time_order BETWEEN %d AND %d 
       AND id_user != %d AND status != 4",
    $distribution_code,
    $month_start,
    $month_end,
    $id_user
));

// Xử lý lọc theo ngày từ form (định dạng d/m/Y)
$from_date = isset($_GET['from']) ? sanitize_text_field($_GET['from']) : '';
$to_date   = isset($_GET['to']) ? sanitize_text_field($_GET['to']) : '';

if (!empty($from_date) && !empty($to_date)) {
    // Chuyển đổi ngày theo định dạng d/m/Y
    $from_dt = DateTime::createFromFormat('d/m/Y', $from_date);
    $to_dt   = DateTime::createFromFormat('d/m/Y', $to_date);
    if ($from_dt && $to_dt) {
        $from_timestamp_order = strtotime($from_dt->format('Y-m-d 00:00:00'));
        $to_timestamp_order   = strtotime($to_dt->format('Y-m-d 23:59:59'));
    } else {
        $from_timestamp_order = strtotime('-30 days');
        $to_timestamp_order   = time();
    }
} else {
    $from_timestamp_order = strtotime('-30 days');
    $to_timestamp_order   = time();
}

$total_orders = $wpdb->get_var($wpdb->prepare(
    "SELECT COUNT(*) FROM wp_orders 
     WHERE distribution_code = %s 
       AND time_order BETWEEN %d AND %d 
       AND id_user != %d AND status != 4" ,
    $distribution_code,
    $from_timestamp_order,  // dùng giá trị từ form
    $to_timestamp_order,    // dùng giá trị từ form
    $id_user
));


// Thống kê theo tuần
$week_start = strtotime('monday this week 00:00:00');
$week_end   = strtotime('sunday this week 23:59:59');

$total_orders_week = $wpdb->get_var($wpdb->prepare(
    "SELECT COUNT(*) FROM wp_orders 
     WHERE distribution_code = %s 
       AND time_order BETWEEN %d AND %d 
       AND id_user != %d AND status != 4",
    $distribution_code,
    $week_start,
    $week_end,
    $id_user
));

$total_price_week = $wpdb->get_var($wpdb->prepare(
    "SELECT COALESCE(SUM(price), 0) FROM wp_orders 
     WHERE distribution_code = %s 
       AND time_order BETWEEN %d AND %d 
       AND id_user != %d AND status != 4",
    $distribution_code,
    $week_start,
    $week_end,
    $id_user
));

// Xử lý dữ liệu cho biểu đồ (mặc định 30 ngày, bao gồm cả hôm nay)
$chart_from_dt = new DateTime();
$chart_from_dt->modify('-29 days');
$chart_to_dt = new DateTime(); // hôm nay
$chart_from_timestamp = $chart_from_dt->setTime(0, 0, 0)->getTimestamp();
$chart_to_timestamp   = $chart_to_dt->setTime(23, 59, 59)->getTimestamp();

$interval = new DatePeriod(
    $chart_from_dt,
    new DateInterval('P1D'),
    (clone $chart_to_dt)->modify('+1 day')
);
if ($from_date && $to_date) {
// Chuyển đổi ngày theo định dạng d/m/Y
    $from_dt_chart = DateTime::createFromFormat('d/m/Y', $from_date);
    $to_dt_chart = DateTime::createFromFormat('d/m/Y', $to_date);
    if ($from_dt && $to_dt) {
        $chart_from_timestamp = $from_dt_chart->setTime(0, 0, 0)->getTimestamp();
        $chart_to_timestamp   = $to_dt_chart->setTime(23, 59, 59)->getTimestamp();
    } else {
        $chart_from_timestamp = strtotime('-30 days');
        $chart_to_timestamp   = time();
    }
} else {
    $chart_from_timestamp = strtotime('-30 days');
    $chart_to_timestamp   = time();
}
// Lấy dữ liệu đơn hàng cho biểu đồ (loại trừ đơn của affiliate)
$dataOrder = $wpdb->get_results($wpdb->prepare(
    "SELECT time_order, price FROM wp_orders 
     WHERE distribution_code = %s 
       AND time_order BETWEEN %d AND %d 
       AND id_user != %d AND status != 4",
    $distribution_code,
    $chart_from_timestamp,
    $chart_to_timestamp,
    $id_user
));

// Khởi tạo mảng dữ liệu cho biểu đồ (mỗi ngày)
$days = [];
foreach ($interval as $date) {
    $formatted_date = $date->format("jS");
    $days[$formatted_date] = ["revenue" => 0, "order" => 0];
}
foreach ($dataOrder as $order) {
    $date_key = date("jS", $order->time_order);
    if (isset($days[$date_key])) {
        $days[$date_key]["revenue"] += floatval($order->price);
        $days[$date_key]["order"]++;
    }
}
$revenueData = [
    "total" => array_sum(array_column($days, "revenue")),
    "data"  => [
        "labels"  => array_keys($days),
        "revenue" => array_column($days, "revenue"),
        "order"   => array_column($days, "order")
    ]
];
$dataport = json_encode($revenueData, JSON_PRETTY_PRINT);
?>


<!-- Form lọc theo ngày -->

<?php
get_header('dealer');
?>
<style>
    @media screen and (max-width: 1300px) {
        .image-customer img {
            width: 4.5rem;
        }
    }
</style>
<div class="col-span-6 text-center p-8 m-h-content">
    <div class="mx-auto relative w-full bg-white px-10 py-6 rounded-xl mb-8">
        <div class="md:mb-10 mb-4 lg:flex block items-center">
            <div class="text-left">
                <h4 class="text-left m-0 color-vector text-28 font-semibold">Overview</h4>
                <p class="mt-1 md:mb-0 mb-4 text-18">Monitor key metrics to boost efficiency!</p>
            </div>
            <div class="lg:mt-0 mt-4 search relative lg:flex block items-center xl:ml-auto order-2 xl:col-span-1 ml-0 md:justify-end justify-start mx-100-991 ">
                <div x-data="app()" x-init="initDate();" x-cloak>
                    <div class="relative" @keydown.escape="closeDatepicker()" @click.away="closeDatepicker()">
                        <div class="flex items-center w-full md:justify-end justify-start">
                            <span class="text-lg font-normal color-vector mr-3 w-48 lg:text-center text-left">Date Range: </span>

                            <form id="dateFilterForm" method="GET" class="date-filter flex">
                                <div class="border rounded-lg p-2 flex justify-center h-48 md:w-249 text-28 w-full">
                                    <input type="text" name="from" id="inputFrom" value="<?php echo isset($_GET['from']) ? htmlspecialchars($_GET['from']) : ''; ?>" x-init="dateFromValue = '<?php echo isset($_GET['from']) ? date('d/m/Y', strtotime(str_replace('/', '-', $_GET['from']))) : ''; ?>'" @click="endToShow = 'from'; initDate(); showDatepicker = true" x-model="dateFromValue" :class="{'font-semibold': endToShow == 'from' }" class="w-24 text-center border-none chart-font-size color-vector" readonly />
                                    <span class="icon-center date-span chart-font-size color-vector pt-1" :class="dateFromValue ? 'block' : 'hidden'">-</span>
                                    <input type="text" name="to" id="inputTo" value="<?php echo isset($_GET['to']) ? htmlspecialchars($_GET['to']) : ''; ?>" x-init="dateFromValue = '<?php echo isset($_GET['to']) ? date('d/m/Y', strtotime(str_replace('/', '-', $_GET['to']))) : ''; ?>'" @click="endToShow = 'to'; initDate(); showDatepicker = true" x-model="dateToValue" :class="{'font-semibold': endToShow == 'to' }" class="w-24 text-center border-none chart-font-size color-vector" readonly />
                                </div>
                                <button type="submit" style="padding: 0 40px" class="text-white w-full bg-red-sure py-3 rounded-lg text-base font-semibold ml-6">Apply</button>
                            </form>

                        </div>
                        <div class="bg-white mt-12 rounded-lg shadow p-4 absolute top-0 left-0"
                            style="width: 17rem; z-index: 1;"
                            x-show.transition="showDatepicker">

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
                                        <svg class="h-6 w-6 text-gray-500 inline-flex" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                        </svg>
                                    </button>
                                    <button
                                        type="button"
                                        class="transition ease-in-out duration-100 inline-flex cursor-pointer hover:bg-gray-200 p-1 rounded-full"
                                        @click="if (month == 11) {year++; month=0;} else {month++;}; getNoOfDays()">
                                        <svg class="h-6 w-6 text-gray-500 inline-flex" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <div class="flex flex-wrap mb-3 -mx-1">
                                <template x-for="(day, index) in DAYS" :key="index">
                                    <div style="width: 14.26%" class="px-1">
                                        <div
                                            x-text="day"
                                            class="text-gray-800 font-medium text-center text-xs"></div>
                                    </div>
                                </template>
                            </div>

                            <div class="flex flex-wrap -mx-1">
                                <template x-for="blankday in blankdays">
                                    <div
                                        style="width: 14.28%"
                                        class="text-center border p-1 border-transparent text-sm"></div>
                                </template>
                                <template x-for="(date, dateIndex) in no_of_days" :key="dateIndex">
                                    <div style="width: 14.28%">
                                        <div
                                            @click="getDateValue(date)"
                                            x-text="date"
                                            class="p-1 cursor-pointer text-center text-sm leading-none hover:bg-blue-200 leading-loose transition ease-in-out duration-100"
                                            :class="{'font-bold': isToday(date) == true, 'bg-blue-800 text-white rounded-l-full': isDateFrom(date) == true, 'bg-blue-800 text-white rounded-r-full': isDateTo(date) == true, 'bg-blue-200': isInRange(date) == true, 'cursor-not-allowed opacity-25': isDisabled(date) }"
                                            :disabled="isDisabled(date) ? true : false"></div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="grid lg:grid-cols-12 gap-8 static">
            <div class="flex items-start col-span-3">
                <div class="image mr-4 image-customer"><img class="w-24 h-auto" src="<?= $url ?>/dist/img/statis-1.png" alt=""></div>
                <div class="text-left">
                    <p class="chart-font-size font-normal mt-0 mb-2">Revenue</p>
                    <p class="text-32 font-semibold chart-color-green my-1"><?= formatBalance($revenue)  ?> </p>
                </div>
            </div>
            <div class="flex items-start col-span-3">
                <div class="image mr-4 image-customer"><img class="w-24 h-auto" src="<?= $url ?>/dist/img/statis-2.png" alt=""></div>
                <div class="text-left">
                    <p class="chart-font-size font-normal mt-0 mb-2">Current commission</p>
                    <p class="text-32 font-semibold chart-color-green my-1" style="color: #DB5EA8"><?= formatBalance($result) ?></p>
                </div>
            </div>
            <div class="flex items-start col-span-3">
                <div class="image mr-4 image-customer"><img class="w-24 h-auto" src="<?= $url ?>/dist/img/terp.png" alt=""></div>
                <div class="text-left">
                    <p class="chart-font-size font-normal mt-0 mb-2">Withdrawed</p>
                    <p class="text-32 font-semibold chart-color-green my-1" style="color: #EF9815"><?= formatBalance($total_withdrawal) ?></p>
                </div>
            </div>
            <div class="flex items-start col-span-3">
                <div class="image mr-4 image-customer"><img class="w-24 h-auto" src="<?= $url ?>/dist/img/statis-3.png" alt=""></div>
                <div class="text-left">
                    <p class="chart-font-size font-normal mt-0 mb-2">Orders</p>
                    <p class="text-32 font-semibold chart-color-blue my-1"><?= $total_orders ?> Orders</p>
                </div>
            </div>
        </div>
    </div>

    <div class="mx-auto relative grid grid-cols-12 gap-8">
        <div class="xl:col-span-4 col-span-12">
            <div class="text-left bg-white rounded-xl py-6 px-10 mb-5">
                <p class="text-18 font-normal mt-0 mb-1">Total clicks</p>
                <p class="chart-color-yellow text-xl font-semibold my-0"><?= $total_click ?></p>
            </div>
            <div class="text-left bg-white rounded-xl py-6 px-10 mb-5">
                <p class="text-18 font-normal mt-0 mb-1">Monthly Revenue</p>
                <p class="chart-color-blue-2 text-xl font-semibold mt-0 mb-6"><?= formatBalance($total_price) ?></p>
                <p class="text-18 font-normal mt-0 mb-1">Monthly Order</p>
                <p class="chart-color-blue-3 text-xl font-semibold my-0"><?= $total_orders ?> orders</p>
            </div>
            <div class="text-left bg-white rounded-xl py-6 px-10">
                <p class="text-18 font-normal mt-0 mb-1">Weekly Revenue</p>
                <p class="chart-color-blue-2 text-xl font-semibold mt-0 mb-6"><?= formatBalance($total_price_week) ?></p>
                <p class="text-18 font-normal mt-0 mb-1">Weekly Order</p>
                <p class="chart-color-blue-3 text-xl font-semibold my-0"><?= $total_orders_week ?> orders</p>
            </div>
        </div>
        <div class="xl:col-span-8 col-span-12">
            <div class="min-w-screen bg-white flex items-start justify-center px-10 py-6 rounded-xl">

                <div class="text-gray-500 rounded w-full" x-data="{chartData:chartData()}" x-init="chartData.fetch()">
                    <h5 class="chart-font-size font-normal p-0 m-0 pb-6 text-left">Today's Revenue</h5>
                    <div class="grid grid-cols-12 mb-10">
                        <div class="md:col-span-6 col-span-12 flex items-center text-left">
                            <ul class="list-none flex pl-0 m-0">
                                <li class="flex items-center mr-10"><img class="w-4 mr-2" src="<?= $url ?>/dist/img/e1.png" alt=""><span class="chart-font-size">Revenue</span></li>
                                <li class="flex items-center"><img class="w-4 mr-2" src="<?= $url ?>/dist/img/e2.png" alt=""><span>Order</span></li>
                            </ul>

                        </div>
                    </div>
                    <div>
                        <canvas id="chart"></canvas>
                        <div id="chartjs-tooltip" class="absolute opacity-0 pointer-events-none py-4 px-6 text-base">
                            <table></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
get_footer('dealer');
?>
<script>
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
<script>
    Number.prototype.comma_formatter = function() {
        return this.toString().replace(/\B(?<!\.\d*)(?=(\d{3})+(?!\d))/g, ",");
    }

    var customTooltips = function(tooltip) {
        var tooltipEl = document.getElementById("chartjs-tooltip");

        if (!tooltipEl) {
            tooltipEl = document.createElement("div");
            tooltipEl.id = "chartjs-tooltip";
            tooltipEl.innerHTML = "";
            this._chart.canvas.parentNode.appendChild(tooltipEl);
        }

        if (tooltip.opacity === 0) {
            tooltipEl.style.opacity = 0;
            return;
        }

        if (tooltip.dataPoints && tooltip.dataPoints.length) {
            var dataPoint = tooltip.dataPoints[0];

            tooltipEl.classList.remove("above", "below", "no-transform");
            if (tooltip.yAlign) {
                tooltipEl.classList.add(tooltip.yAlign);
            } else {
                tooltipEl.classList.add("no-transform");
            }

            var datasetIndex = dataPoint.datasetIndex;
            var value = dataPoint.yLabel;

            var innerHtml = "<thead></thead><tbody>";

            if (datasetIndex === 0) {
                innerHtml += `<tr><td class="text-left text-base px-4">Revenue<br><b class="chart-font-size">${value.comma_formatter()}</b></td></tr>`;
            } else if (datasetIndex === 1) {
                innerHtml += `<tr><td class="text-left text-base px-4">Order<br><b class="chart-font-size">${value.comma_formatter()}</b></td></tr>`;
            }

            innerHtml += "</tbody>";
            var tableRoot = tooltipEl.querySelector("table");
            tableRoot.innerHTML = innerHtml;

            var positionY = this._chart.canvas.offsetTop;
            var positionX = this._chart.canvas.offsetLeft;

            tooltipEl.style.opacity = 1;
            tooltipEl.style.left = positionX + dataPoint.x + 40 + "px";
            tooltipEl.style.top = positionY + tooltip.caretY - 100 + "px";
            tooltipEl.style.fontFamily = tooltip._bodyFontFamily;
            tooltipEl.style.fontSize = tooltip.bodyFontSize + "px";
            tooltipEl.style.fontStyle = tooltip._bodyFontStyle;
            tooltipEl.style.padding = tooltip.yPadding + "px " + tooltip.xPadding + "px";
        }
    };

    function graphClickEvent(event, array) {
        if (array[0]) {
            var chartData = array[0]["_chart"].config.data;
            var idx = array[0]["_index"];

            var label = chartData.labels[idx];
            var value = chartData.datasets[0].data[idx];
        }
    }

    function randomNumbers(min, max) {
        return Math.floor(Math.random() * max) + min;
    }

    function randomScalingFactor() {
        return randomNumbers(1, 100);
    }

    function createLinearGradient(ctx, color1, color2) {
        const gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, color1);
        gradient.addColorStop(1, color2);
        return gradient;
    }

    let chartData = function(){
        var dates = {
            "d": <?= $dataport ?>

        };
        return {
            date: 'd',
            options: [
                {
                    label: '15m',
                    value: '15m',
                },
                {
                    label: '1h',
                    value: '1h',
                },
                {
                    label: 'd',
                    value: 'd',
                },
                {
                    label: 'm',
                    value: 'm',
                },
                {
                    label: 'y',
                    value: 'y',
                },
            ],
            showDropdown: false,
            selectedOption: 0,
            selectOption: function(index){
                this.selectedOption = index;
                this.date = this.options[index].value;
                this.renderChart();
            },
            data: null,
            fetch: function(){
                fetch(dates)
                    .then(res => {
                        this.data = dates;
                        this.renderChart();
                    })
            },
            renderChart: function(){
                let c = false;

                Chart.helpers.each(Chart.instances, function(instance) {
                    if (instance.chart.canvas.id == 'chart') {
                        c = instance;
                    }
                });

                if(c) {
                    c.destroy();
                }

                let ctx = document.getElementById('chart').getContext('2d');
                let chart = new Chart(ctx, {
                    type: "line",
                    data: {
                        labels: this.data[this.date].data.labels,
                        datasets: [
                            {
                                label: "Revenue",
                                backgroundColor: createLinearGradient(ctx, 'rgba(20, 201, 201, 0.10)', 'rgba(255, 255, 255, 0.00)'),
                                borderColor: "#14C9C9",
                                pointStyle: 'line',
                                data: this.data[this.date].data.revenue,
                                yAxisID: "left"
                            },
                            {
                                label: "order",
                                backgroundColor: createLinearGradient(ctx, 'rgba(255,255,255,1)', 'rgba(255, 255, 255, 0.00)'),
                                borderColor: "#18A0FB",
                                pointStyle: 'line',
                                pointBackgroundColor: "#fff",
                                data: this.data[this.date].data.order,
                                yAxisID: "right"
                            },
                        ],
                    },
                    options: {
                        legend: {
                            display: false,
                        },
                        tooltips: {
                            enabled: false,
                            custom: customTooltips,
                        },
                        scales: {
                            xAxes: [{
                                gridLines: { display: false },
                                ticks: {
                                    fontSize: 16,
                                    fontFamily: 'Poppins',
                                    callback: function(value,index,array) {
                                        return value > 1000 ? ((value < 1000000) ? value/1000 + 'K' : value/1000000 + 'M') : value;
                                    }
                                }
                            }],
                            yAxes: [
                                {
                                    "scaleLabel": {
                                        "display": true,
                                    },
                                    "id": "left",
                                    "stacked": false,
                                    "ticks": {
                                        "beginAtZero": true,
                                        suggestedMin: 0,
                                        suggestedMax: 1600,
                                        callback: function(value) {
                                            return value + ' USD';
                                        }
                                    }
                                },
                                {
                                    "scaleLabel": {
                                        "display": true,
                                    },
                                    "id": "right",
                                    "position": "right",
                                    "stacked": false,
                                    "ticks": {
                                        "beginAtZero": true,
                                        suggestedMin: 5,
                                        suggestedMax: 40
                                    }
                                }
                            ]
                        },
                    }
                });
            }
        }
    }
</script>