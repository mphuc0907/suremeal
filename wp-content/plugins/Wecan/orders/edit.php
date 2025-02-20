<?php
include __DIR__ . "/../includes/padding.php";
global $wpdb;

$idOrder = xss(no_sql_injection($_GET['id']));
$queryOrders = $wpdb->get_results("SELECT * FROM wp_orders WHERE id = '{$idOrder}'");

$dataProduct = $queryOrders[0]->dataproduct;
$decodedData = json_decode(str_replace('\\', "", $dataProduct), true);

$adress = $queryOrders->delivery_information;



$time_order_uat = $queryOrders[0]->time_order;
date_default_timezone_set("America/Chicago");
// date_default_timezone_set('America/New_York');
$formattedDateEastern = date('Y-m-d', $time_order_uat);

$totalRecords = count($decodedData);

//Tên người dùng
$name_info = $queryOrders[0]->name_user;
$email = $queryOrders[0]->email;
$order_code = $queryOrders[0]->order_code;
$phoneNumber = $queryOrders[0]->phoneNumber;
$address1 = $queryOrders[0]->address1;
$ZIPCode = $queryOrders[0]->ZIPCode;
$state = $queryOrders[0]->state;
$price_payment = $queryOrders[0]->price_payment;
$time_order = $queryOrders[0]->time_order;
$discount_price = $queryOrders[0]->discount_price;
$status = $queryOrders[0]->status;
$status_transport = $queryOrders[0]->status_transport;
$country = $queryOrders[0]->country;
$payment_method = $queryOrders[0]->price_payment;
$transport_fee = $queryOrders[0]->transport_fee;
?>

<style>
    input {
        width: 100%;
    }

    .d-none {
        display: none;
    }

    .order-item-product img {
        width: 100%;
    }

    .order-item-product {
        padding: 40px 45px;
        /*background: #ecf1ff;*/
        border-radius: 10px;
        margin-bottom: 20px;
    }

    .order-item-product .order-status {
        margin: 20px auto;
    }

    .order-item-product .order-status .st-main {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 50px;
    }

    .order-item-product .order-status .st-main .st-left {
        display: flex;
        align-items: center;
        gap: 40px;
        flex-basis: 70%;
    }

    .order-item-product .order-status .st-main .st-left .status {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .order-item-product .order-status .st-main .st-left .status span {
        display: block;
        font-size: 14px;
        line-height: 20px;
        color: #292b2e;
        margin: 0;
    }

    .order-item-product .order-status .st-main .st-left .status strong {
        display: block;
        font-size: 14px;
        line-height: 20px;
        color: #e91c24;
        font-family: K2D-Bold, sans-serif;
        margin: 0;
    }

    .order-item-product .order-status .st-main .st-right {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: flex-end;
        text-align: right;
        gap: 50px;
    }

    .order-item-product .order-status .st-main .st-right span {
        flex: 1;
        display: block;
        font-size: 16px;
        line-height: 24px;
        color: #292b2e;
        margin: 0;
    }

    .order-item-product .order-status .st-main .st-right strong {
        display: block;
        font-size: 20px;
        line-height: 26px;
        color: #e91c24;
        font-family: K2D-Bold, sans-serif;
    }

    .order-item-product .order-detail {
        border-bottom: 1px solid #d9d9d9;
        padding-bottom: 25px;
    }

    .order-item-product .order-detail .list-product {}

    .order-item-product .order-detail .list-product .morth-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 50px;
        margin-bottom: 20px;
    }

    .order-item-product .order-detail .list-product .morth-item .morth-img {
        display: flex;
        align-items: center;
        gap: 50px;
        flex-basis: 70%;
    }

    .order-item-product .order-detail .list-product .morth-item .morth-img figure {
        position: relative;
        width: 150px;
        height: 125px;
        border-radius: 10px;
    }

    .order-item-product .order-detail .list-product .morth-item .morth-img figure img {
        height: 160px;
        object-fit: contain;
    }

    .order-item-product .order-detail .list-product .morth-item .morth-img .info {}

    .order-item-product .order-detail .list-product .morth-item .morth-img .info h4 {
        font-size: 20px;
        line-height: 26px;
        font-family: var(--f-bold);
        color: #292b2e;
        margin-bottom: 10px;
    }

    .order-item-product .order-detail .list-product .morth-item .morth-img .info .type {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .order-item-product .order-detail .list-product .morth-item .morth-img .info .type span {
        display: block;
        font-size: 14px;
        line-height: 24px;
        color: rgba(22, 7, 8, 0.5019607843);
    }

    .order-item-product .order-detail .list-product .morth-item .morth-price strong {
        font-size: 20px;
        line-height: 26px;
        font-family: K2D-ExtraBold, sans-serif;
        color: #292b2e;
        margin: 0;
    }

    .order-item-product .order-detail .list-product .morth-item .morth-price {
        flex: 1;
        text-align: right;
    }

    .order-item-product .order-detail .list-info {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 40px;
        padding-top: 20px;
        margin-top: 30px;
        border-top: 1px solid #d9d9d9;
    }

    .order-item-product .order-detail .list-info .info__left ul li {
        display: flex;
        align-items: center;
        flex-basis: calc(50% - 20px/2);
        gap: 15px;
        margin-bottom: 10px;
    }

    .order-item-product .order-detail .list-info .info__left ul {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
        margin: 0;
        padding: 0;
        list-style: none;
    }

    .order-item-product .order-detail .list-info .info__left {
        flex-basis: 70%;
    }

    .order-item-product .order-detail .list-info .info__right ul {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        margin: 0;
        padding: 0;
        list-style: none;
    }

    .order-item-product .order-detail .list-info .info__right ul li {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 10px;
    }

    .order-item-product .order-detail .list-info .info__right {
        flex: 1;
    }

    .order-item-product strong {
        display: block;
        font-size: 16px;
        line-height: 26px;

        color: #292b2e;
        margin: 0;
    }

    .order-item-product span {
        display: block;
        font-size: 16px;
        line-height: 26px;
        color: #292b2e;
        margin: 0;
    }

    .st-left button {
        padding: 9px 30px;
        border: none;
        font-size: 14px;
        line-height: 20px;
        color: #e91c24;
        border: 1px solid #e91c24;
        background: rgba(0, 0, 0, 0);
        border-radius: 10px;
    }
</style>
<style>
    .flr {
        display: flex;
        float: right;
    }

    .d-none {
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
</style>
<div class="divgif">
    <img class="iconloadgif" src="<?php echo get_template_directory_uri(); ?>/ajax/images/loading2.gif" alt="">
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<div class="wrap">
    <h1>
        Order management
    </h1>
    <form id="adddaily" method="post" action="<?php echo $module_path . '&sub=edit&edit_action=1&id=' . $id; ?>"
        name="post">
        <div id="poststuff">
            <input type="hidden" value="<?php echo $id; ?>" name="id" />
            <div class="metabox-holder columns-2" id="post-body" style="width: 100%;">
                <!---left-->
                <div id="post-body-content" class="pos1">
                    <div class="postbox">
                        <div class="inside">
                            <div class="order-table">
                                <div class="order-item-product">
                                    <div class="order-detail">

                                        <div class="list-product">

                                            <?php foreach ($decodedData as $key => $orderdetail): ?>
                                                <?php

                                                $total = $orderdetail['price'] * $orderdetail['qty'];

                                                ?>
                                                <div class="morth-item">
                                                    <div class="morth-img">
                                                        <figure>
                                                            <?php if ($orderdetail['img']) : ?>
                                                                <img src="<?= $orderdetail['img'] ?>" alt="">
                                                            <?php else: ?>
                                                                <img src="<?= $orderdetail['img'] ?>" alt="">
                                                            <?php endif ?>
                                                        </figure>
                                                        <div class="info">
                                                            <h4><?= $orderdetail['title'] ?></h4>

                                                            <div class="type">
                                                                <span>Quantity: <?= $orderdetail['qty'] ?></span>
                                                            </div>

                                                        </div>
                                                    </div>
                                                    <div class="morth-price">
                                                        <strong><?= formatBalance($total) ?></strong>
                                                    </div>
                                                </div>

                                            <?php endforeach; ?>
                                            <h1><b>Total: <?= formatBalance($payment_method) ?> </b></h1>
                                        </div>

                                        <div class="list-info">
                                            <div class="info__left">
                                                <ul>

                                                    <li>
                                                        <span>Full name:</span>
                                                        <strong><?= $name_info ?></strong>
                                                    </li>
                                                    <li>
                                                        <span>Order code:</span>
                                                        <strong><?= $order_code ?></strong>
                                                    </li>

                                                    <li>
                                                        <span>Phone number:</span>
                                                        <strong><?= $phoneNumber ?></strong>
                                                    </li>

                                                    <li>
                                                        <span>Payment method:</span>
                                                        <strong>Stripe</strong>
                                                    </li>

                                                    <li>
                                                        <span>Address:</span>
                                                        <strong><?= $address1 ?></strong>
                                                    </li>
                                                    <li>
                                                        <span>Order time:</span>
                                                        <strong><?= date('H:i M,d Y', $time_order) ?></strong>
                                                    </li>
                                                    <li>
                                                        <span>Email:</span>
                                                        <strong><?= $email ?></strong>
                                                    </li>
                                                    <li>
                                                        <span>Order status:</span>
                                                        <strong> <select class="statusPayment" data-order-id="<?php echo $idOrder; ?>">
                                                                <!-- <?php echo getStatusPayment($status); ?> -->
                                                                <option value="1" <?= ($status == 1) ? 'selected' : '' ?>>Processing</option>
                                                              
                                                                <option value="2" <?= ($status == 2) ? 'selected' : '' ?>>Completed</option>
                                                                <option value="4" <?= ($status == 4) ? 'selected' : '' ?>>Canceled</option>
                                                            </select></strong>
                                                    </li>
                                                    <li>
                                                        <span>Shipping status:</span>
                                                        <strong> <select class="statusTransport" data-order-id="<?php echo $idOrder; ?>">
                                                                <?php echo getStatusShiping($status_transport); ?>
                                                            </select></strong>
                                                    </li>
                                                    <li>
                                                        <span>Country/City:</span>
                                                        <strong><?= $country ?>/<?= $state ?></strong>
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class="info__right">
                                                <ul>

                                                    <li>
                                                        <span>Discount:</span>
                                                        <strong><?= formatBalance($discount_price) ?></strong>
                                                    </li>
                                                    <li>
                                                        <span>Shipping fee:</span>
                                                        <strong><?= formatBalance($transport_fee) ?></strong>
                                                    </li>
                                                    <li>
                                                        <span>Total amount:</span>
                                                        <strong style="font-size: 25px; color: red"><?= formatBalance($payment_method) ?></strong>
                                                    </li>

                                                </ul>
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
    </form>
</div>

<?php
//add_admin_js('image.upload.js');

add_admin_js('jquery.min.js');
add_admin_js('jquery.validate.min.js');
?>

<script>
    $(document).ready(function() {
        $(document).ready(function() {
            // Thay doi trang thai don hang
            $('.statusTransport').on('change', function() {
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
            $('.statusPayment').on('change', function() {
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
                        $('.divgif').css('display', 'block');
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
    });
</script>