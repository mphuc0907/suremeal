<?php
require_once __DIR__ . '/../includes/function.php';

//$myrows = $wpdb->get_results( "SELECT * FROM ".$wpdb->prefix."useragency" );
//$module_path = 'admin.php?page=daily';
$module_pathadd = 'admin.php?page=affiliate_orders_manager';
$module_short_url = str_replace('admin.php?page=', '', $module_pathadd);
$mess = '';
$mdlconf = array('title' => 'Affiliate Orders');
include __DIR__ . "/../includes/padding.php";

//pr(123);
global $wpdb;
//action_list_del("wp_orders");
$pagesize = 20;
$s = '';
$my_str = "WHERE 1=1 AND distribution_code != ''";

date_default_timezone_set("America/Chicago");
if (isset($_REQUEST['search'])) {
    $keyword = fixqQ($_REQUEST['order_code']);
    $s .= '&search=1';
    $status = (int)$_REQUEST['status'];
    $de_status = (int)$_REQUEST['de_status'];

    // Date and time range filtering
    // $time_start = !empty($_REQUEST['time_start']) ? strtotime($_REQUEST['time_start']) : null;
    // $time_end = !empty($_REQUEST['time_end']) ? strtotime($_REQUEST['time_end']) : null;

    // Existing keyword search
    if ($keyword != null && !in_array(strtolower($keyword))) {
        $my_str .= ' AND (order_code like "%' . $keyword . '%")';
    }

    // Status filters
    if($status != 0) {
        $my_str .= ' AND status = ' . $status;
    }

    if($de_status != 0) {
        $my_str .= ' AND status_transport = ' . $de_status;
    }

    if (!empty($_REQUEST['time_start'])) {
        $time_start = strtotime($_REQUEST['time_start'] . ' 00:00:00 America/Chicago');
    } else {
        $time_start = null;
    }

    if (!empty($_REQUEST['time_end'])) {
        $time_end = strtotime($_REQUEST['time_end'] . ' 23:59:59 America/Chicago');
    } else {
        $time_end = null;
    }

    // Date and time range filter
    if ($time_start && $time_end) {
        $my_str .= " AND time_order BETWEEN $time_start AND $time_end";
    } elseif ($time_start) {
        $my_str .= " AND time_order >= $time_start";
    } elseif ($time_end) {
        $my_str .= " AND time_order <= $time_end";
    }
}

$recordcount = count_total_db("wp_orders", $my_str);
$paged = isset($_GET['paged']) ? (int)$_GET['paged'] : 1;

$beginpaging = beginpaging($pagesize, $recordcount, $paged);
add_admin_css('main.css');
add_admin_js('jquery-2.2.4.min.js');
$city = file_get_contents(plugin_dir_path(__FILE__) . 'vn_city.json');
$json = json_decode($city, true);

// Loại tài khoản

?>
<style>
    .flr{
        display: flex;
        float: right;
    }
    .d-none{
        display: none;
    }
    .divgif {
        position: fixed;
        width: 100%;
        height: 100%;
        z-index: 1100;
        display: none;
        background: #dedede;
        opacity: 0.5;
        top: 0;
        left: 0;
    }
    .iconloadgif {
        top: 0;
        right: 0;
        left: 0;
        bottom: 0;
        position: absolute;
        margin: auto;
        width: 150px;
        height: 150px;
    }
    .date-time {
        display: flex;
        flex-wrap: wrap;
    }

    .time-space {
        padding-left: 10px;
        padding-right: 10px;
        font-size: 20px;
    }
</style>
<div class="divgif">
    <img class="iconloadgif" src="<?php echo get_template_directory_uri(); ?>/ajax/images/loading2.gif" alt="">
</div>
<div class="wrap">
    <h1 style="margin-bottom:15px;">List <?php echo $mdlconf['title']; ?>
    </h1>

    <ul class="subsubsub">
        <li class="all"><a class="current" href="<?php echo $module_path; ?>">All <span
                        class="count">(<?php echo $recordcount; ?>)</span></a></li>
    </ul>
    <form class="search-box flr" method="POST" action="<?php echo $module_path; ?>">
        <div class="date-time">
            <div class="time-start">
                <span>From: </span>
                <input type="date" id="time_start" name="time_start">
            </div>
            <div class="time-space">-</div>
            <div class="time-end">
                <span>To: </span>
                <input type="date" id="time_end" name="time_end">
            </div>
        </div>
        <div>
            <span style="line-height: 24px; margin-right: 10px">Order status:  </span>
            <select name="status">
                <option value="0"<?php if($status == 0) { echo 'selected'; } ?>>All Statuses</option>
                <option value="1"<?php if($status == 1) { echo 'selected'; } ?>>Processing</option>
                <option value="3"<?php if($status == 3) { echo 'selected'; } ?>>In process</option>
                <option value="2"<?php if($status == 2) { echo 'selected'; } ?>>Completed</option>
                <option value="4"<?php if($status == 4) { echo 'selected'; } ?>>Cancelled</option>
            </select>
        </div>

        <div>
            <span style="line-height: 24px; margin-right: 10px">Delivery status:  </span>
            <select name="de_status">
                <option value="0"<?php if($de_status == 0) { echo 'selected'; } ?>>All Statuses</option>
                <option value="1"<?php if($de_status == 1) { echo 'selected'; } ?>>Order placed</option>
                <option value="2"<?php if($de_status == 2) { echo 'selected'; } ?>>Packaging</option>
                <option value="3"<?php if($de_status == 3) { echo 'selected'; } ?>>On the road</option>
                <option value="4"<?php if($de_status == 4) { echo 'selected'; } ?>>Delivered</option>
            </select>
        </div>

        <input class="sear_2" value="<?php if (isset($keyword)) echo $keyword; ?>" type="text" name="order_code"
               placeholder="Search by code">

        <input type="submit" name="search" value="Search" class="button"/>
    </form>
    <?php
    $myrows = $wpdb->get_results("SELECT * FROM wp_orders ". $my_str ." AND distribution_code != '' ORDER BY id DESC LIMIT  " . $beginpaging[0] . ",$pagesize");
    ?>
    <!--    --><?php // if ( $mess != '' ) { ?>
    <!--        <div class="notice notice-warning is-dismissible" id="message">-->
    <!--            <p>--><?php //echo $mess; ?><!--</p>-->
    <!--        </div>-->
    <!--    --><?php // } ?>

    <form class="" method="POST" action="<?php echo $module_path; ?>">

        <table class="wp-list-table widefat fixed striped posts">
            <thead>
            <tr class="headline">
                <th style="width:30px;text-align:center;">No.</th>
                <th>Order Code</th>
                <th>Buyer</th>
                <th>Phone Number</th>
                <th>Discount Code</th>
                <th>Order Date</th>
                <th>Payment Method</th>
                <th>Order Status</th>
                <th>Delivery Status</th>
                <th>Total Amount</th>
            </tr>
            </thead>
            <tfoot>
            <tr class="headline">
                <th style="width:30px;text-align:center;">No.</th>
                <th>Order Code</th>
                <th>Buyer</th>
                <th>Phone Number</th>
                <th>Discount Code</th>
                <th>Order Date</th>
                <th>Payment Method</th>
                <th>Order Status</th>
                <th>Delivery Status</th>
                <th>Total Amount</th>
            </tr>
            </tfoot>


            <?php
            $i = 0;
            foreach ($myrows as $order) {
                $delivery_information = json_decode($order->delivery_information);
                $i++;
                $rowlink = 'admin.php?page=orders_manager&sub=edit&id=' . $order->id;
                $rowlinkUser = 'admin.php?page=daily&sub=edit&id=' . $order->id_user;
                $rowlinkvoucher = 'admin.php?page=orders_manager&sub=voucher_info&code=' . $order->code_voucher;
                ?>
                <tr>
                    <td><?= $i ?></td>
                    <td><a href="<?php echo $rowlink; ?>" target="_blank"><?= $order->order_code ?></a></td>
                    <td><?= $order->name_user ?></td>
                    <td><?= $order->phoneNumber ?></td>
                    <td><a href="<?php echo $rowlinkvoucher ?>"><?= $order->code_voucher ?></a></td>
                    <td><?= date('H:i m/d/Y', $order->time_order) ?></td>
                    <td>    Stripe
                    <td>
                        <select class="statusPayment" data-order-id="<?php echo $order->id; ?>">
                            <!-- <?php echo getStatusPayment($order->status); ?> -->
                            <option value="1" <?= ($status == 1) ? 'selected' : '' ?>>Processing</option>
                            <option value="3" <?= ($status == 3) ? 'selected' : '' ?>>In processing</option>
                            <option value="2" <?= ($status == 2) ? 'selected' : '' ?>>Completed</option>
                            <option value="4" <?= ($status == 4) ? 'selected' : '' ?>>Canceled</option>
                        </select>
                    </td>
                    <td>
                        <select class="statusTransport" data-order-id="<?php echo $order->id; ?>">
                            <?php echo getStatusShiping($order->status_transport); ?>
                        </select>
                    </td>
                    <td><?= formatBalance($order->price_payment) ?> </td>


                    <td><a href="<?php echo $rowlink; ?>" target="_blank">View detail</a></td>
                </tr>
            <?php } ?>
        </table>

    </form>

    <?php echo paddingpage($module_short_url, $beginpaging[1], $beginpaging[2], $beginpaging[3], $paged, $pagesize, $recordcount, $s); ?>

</div>

<div class="box-alert"></div>
<?php
add_admin_js('common.js');
?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.14.5/sweetalert2.min.css" integrity="sha512-Xxs33QtURTKyRJi+DQ7EKwWzxpDlLSqjC7VYwbdWW9zdhrewgsHoim8DclqjqMlsMeiqgAi51+zuamxdEP2v1Q==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.14.5/sweetalert2.min.js" integrity="sha512-JCDnPKShC1tVU4pNu5mhCEt6KWmHf0XPojB0OILRMkr89Eq9BHeBP+54oUlsmj8R5oWqmJstG1QoY6HkkKeUAg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
    flatpickr("#time_start", {
        dateFormat: "m/d/Y" // MM/DD/YYYY
    });
    flatpickr("#time_end", {
        dateFormat: "m/d/Y" // MM/DD/YYYY
    });
    $(document).ready(function () {
        // Thay doi trang thai don hang
        $('.statusTransport').on('change', function () {
            let newStatus = $(this).val();
            let orderId = $(this).attr('data-order-id');

            $.ajax({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                type: 'POST',
                cache: false,
                dataType: "text",
                data: {
                    status: newStatus,
                    orderId: orderId,
                    action: 'changeTransportStatus',
                },
                beforeSend: function() {
                    $('.divgif').css('display', 'block');
                },
                success: function(rs) {
                    $('.divgif').css('display', 'none');
                    rs = JSON.parse(rs);
                    if (rs.status == <?php echo 'success_code' ?>) {
                        Swal.fire({
                            icon: 'success',
                            text: rs.mess,
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            text: rs.mess,
                        });
                    }
                }
            });
        });
        // Thay doi trang thai thanh toan
        $('.statusPayment').on('change', function () {
            let newPayment = $(this).val();
            let orderId = $(this).attr('data-order-id');

            $.ajax({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                type: 'POST',
                cache: false,
                dataType: "text",
                data: {
                    status: newPayment,
                    orderId: orderId,
                    action: 'changePaymentStatus',
                },
                beforeSend: function() {
                    Swal.fire({
                        title: 'Processing',
                        html: 'Please wait...',
                        didOpen: () => {
                            Swal.showLoading()
                        }
                    });
                },
                success: function(rs) {
                    $('.divgif').css('display', 'none');
                    rs = JSON.parse(rs);
                    Swal.fire({
                        icon: 'success',
                        text: rs.mess,
                    });
                    if (rs.status == <?php echo 'success_code' ?>) {

                    } else {
                        Swal.fire({
                            icon: 'error',
                            text: rs.mess,
                        });
                    }
                }
            });
        });
    });
</script>