<?php
include __DIR__ . "/../includes/padding.php";
$url = get_template_directory_uri();
global $wpdb;

$idCustomer = xss(no_sql_injection($_GET['id']));
$queryCustomers = $wpdb->get_results("SELECT * FROM wp_account_users WHERE id = '{$idCustomer}'");

$addresses = $queryCustomers[0]->addresses;
$first_name = $queryCustomers[0]->first_name;
$last_name = $queryCustomers[0]->last_name;
$email = $queryCustomers[0]->email;
$phone_number = $queryCustomers[0]->phone_number;
$avatar = $queryCustomers[0]->avatar;
$provider = $queryCustomers[0]->provider;

$totalOrders = $wpdb->get_var("SELECT COUNT(*) FROM wp_orders WHERE id_user = '{$idCustomer}'");

$myrows = $wpdb->get_results("SELECT * FROM wp_orders WHERE id_user = '{$idCustomer}' ORDER BY time_order DESC");

?>

<style>
    input {
        width: 100%;
    }

    .d-none {
        display: none;
    }
    .order-item-product img{
        width: 100%;
    }
    .order-item-product{
        padding: 40px 45px;
        /*background: #ecf1ff;*/
        border-radius: 10px;
        margin-bottom: 20px;
    }
    .order-item-product .order-status{
        margin: 20px auto;
    }
    .order-item-product .order-status .st-main{
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 50px;
    }
    .order-item-product .order-status .st-main .st-left{
        display: flex;
        align-items: center;
        gap: 40px;
        flex-basis: 70%;
    }
    .order-item-product .order-status .st-main .st-left .status{
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .order-item-product .order-status .st-main .st-left .status span{
        display: block;
        font-size: 14px;
        line-height: 20px;
        color: #292b2e;
        margin: 0;
    }
    .order-item-product .order-status .st-main .st-left .status strong{
        display: block;
        font-size: 14px;
        line-height: 20px;
        color: #e91c24;
        font-family: K2D-Bold, sans-serif;
        margin: 0;
    }
    .order-item-product .order-status .st-main .st-right{
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: flex-end;
        text-align: right;
        gap: 50px;
    }
    .order-item-product .order-status .st-main .st-right span{
        flex: 1;
        display: block;
        font-size: 16px;
        line-height: 24px;
        color: #292b2e;
        margin: 0;
    }
    .order-item-product .order-status .st-main .st-right strong{
        display: block;
        font-size: 20px;
        line-height: 26px;
        color: #e91c24;
        font-family: K2D-Bold, sans-serif;
    }
    .order-item-product .order-detail{
        border-bottom: 1px solid #d9d9d9;
        padding-bottom: 25px;
    }
    .order-item-product .order-detail .list-product{

    }
    .order-item-product .order-detail .list-product .morth-item{
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 50px;
        margin-bottom: 20px;
    }
    .order-item-product .order-detail .list-product .morth-item .morth-img{
        display: flex;
        align-items: center;
        gap: 50px;
        flex-basis: 70%;
    }
    .order-item-product .order-detail .list-product .morth-item .morth-img figure{
        position: relative;
        width: 125px;
        height: 125px;
        border-radius: 10px;
    }
    .order-item-product .order-detail .list-product .morth-item .morth-img figure img {
        height: 100%;
        object-fit: contain;
    }
    .order-item-product .order-detail .list-product .morth-item .morth-img .info{

    }
    .order-item-product .order-detail .list-product .morth-item .morth-img .info h4{
        font-size: 20px;
        line-height: 26px;
        font-family: var(--f-bold);
        color: #292b2e;
        margin-bottom: 10px;
    }
    .order-item-product .order-detail .list-product .morth-item .morth-img .info .type{
        display: flex;
        align-items: center;
        gap: 15px;
    }
    .order-item-product .order-detail .list-product .morth-item .morth-img .info .type span{
        display: block;
        font-size: 14px;
        line-height: 24px;
        color: rgba(22, 7, 8, 0.5019607843);
    }

    .order-item-product .order-detail .list-product .morth-item .morth-price strong{
        font-size: 20px;
        line-height: 26px;
        font-family: K2D-ExtraBold, sans-serif;
        color: #292b2e;
        margin: 0;
    }
    .order-item-product .order-detail .list-product .morth-item .morth-price{
        flex: 1;
        text-align: right;
    }
    .order-item-product .order-detail .list-info{
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-top: 20px;
        margin-top: 30px;
        border-top: 1px solid #d9d9d9;
    }
    .order-item-product .order-detail .list-info .info__left ul li{
        display: flex;
        align-items: center;
        flex-basis: 50%;
        gap: 15px;
        margin-bottom: 10px;
    }
    .order-item-product .order-detail .list-info .info__left ul{
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        margin: 0;
        padding: 0;
        list-style: none;
    }
    .order-item-product .order-detail .list-info .info__left{
        flex-basis: 70%;
    }
    .order-item-product .order-detail .list-info .info__right ul{
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        margin: 0;
        padding: 0;
        list-style: none;
    }
    .order-item-product .order-detail .list-info .info__right ul li{
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 10px;
    }
    .order-item-product .order-detail .list-info .info__right{
        flex: 1;
        padding-left: 120px;
    }
    .order-item-product strong{
        display: block;
        font-size: 16px;
        line-height: 26px;

        color: #292b2e;
        margin: 0;
    }
    .order-item-product span{
        display: block;
        font-size: 16px;
        line-height: 26px;
        color: #292b2e;
        margin: 0;
    }
    .st-left button{
        padding: 9px 30px;
        border: none;
        font-size: 14px;
        line-height: 20px;
        color: #e91c24;
        border: 1px solid #e91c24;
        background: rgba(0, 0, 0, 0);
        border-radius: 10px;
    }
    
    .avatar {
        border-radius: 50%;
        width: 3rem;
        height: 3rem;
    }
</style>
<style>
    .metabox-holder {
        width: 100%;
    }
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
</style>
<div class="divgif">
    <img class="iconloadgif" src="<?php echo get_template_directory_uri(); ?>/ajax/images/loading2.gif" alt="">
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<div class="wrap">
    <h1>
        Info Customer
    </h1>
    <div id="poststuff">
        <div class="metabox-holder columns-2" id="post-body">
            <!---left-->
            <div id="post-body-content" class="pos1">
                <div class="postbox">
                    <div class="inside">
                        <div class="order-table">
                            <div class="order-item-product">
                                <div class="order-detail">

                                    <div class="list-product">
                                            <div class="morth-item">
                                                <div class="morth-img">
<!--                                                    <figure>-->
<!--                                                        <img class="avatar" src="--><?//= $avatar ? $avatar : $url . '/assets/image/dashboard/avatar-80.svg' ?><!--" alt="">-->
<!--                                                    </figure>-->
                                                    <div class="info">
                                                        <h4><?= $first_name ?> <?= $last_name ?></h4>

                                                        <div class="type">
                                                            <span>Type: <?= $provider ? $provider : 'nomal' ?></span>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                    </div>

                                    <div class="list-info">
                                        <div class="info__left">
                                            <ul>

                                                <li>
                                                    <span>First name:</span>
                                                    <strong><?= $first_name ?></strong>
                                                </li>
                                                <li>
                                                    <span>Last name:</span>
                                                    <strong><?= $last_name ?></strong>
                                                </li>

                                                <li>
                                                    <span>Email:</span>
                                                    <strong><?= $email ?></strong>
                                                </li>
                                                <li>
                                                    <span>Phone number:</span>
                                                    <strong><?= $phone_number ?></strong>
                                                </li>
                                                <li>
                                                    <span>Total Order:</span>
                                                    <strong><?= $totalOrders ?></strong>
                                                </li>
                                                    <?php
                                                    $stored_addresses = json_decode($addresses, true) ?: [];

                                                    foreach ($stored_addresses as $index => $address) :
                                                    ?>
                                                        <li>
                                                            <span>Address(<?= $index + 1 ?>):</span>
                                                            <strong><?= htmlspecialchars($address) ?></strong>
                                                        </li>
                                                    <?php endforeach ?>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="post-body-content" class="pos1">
                <h1>
                    List Order
                </h1>
                <table class="wp-list-table widefat fixed striped posts">
            <thead>
                <tr class="headline">
                    <th style="width:30px;text-align:center;">No.</th>
                    <th>Order Code</th>
                    <th>Discount Code</th>
                    <th>Order Date</th>
                    <th>Payment Method</th>
                    <th>Total Amount</th>
                </tr>
                </thead>

                <?php
                $i = 0;
                foreach ($myrows as $order) {
                    $delivery_information = json_decode($order->delivery_information);
                    $i++;
                    $rowlink = 'admin.php?page=orders_manager&sub=edit&id=' . $order->id;
                    $rowlinkvoucher = 'admin.php?page=discount_price&sub=voucher_info&code=' . $order->code_voucher;
                    ?>
                    <tr>
                        <td><?= $i ?></td>
                        <td><a href="<?php echo $rowlink; ?>" target="_blank"><?= $order->order_code ?></a></td>
                        <td><a href="<?php echo $rowlinkvoucher ?>"><?= $order->code_voucher ?></a></td>
                        <td><?= date('H:i d/m/Y', $order->time_order) ?></td>
                        <td>    Stripe
                        <td><?= formatBalance($order->price_payment) ?> </td>
                    
                        <td><a href="<?php echo $rowlink; ?>" target="_blank">View detail</a></td>
                    </tr>
                <?php } ?>
            </table>
            </div>

        </div>
    </div>
</div>

<?php
//add_admin_js('image.upload.js');

add_admin_js('jquery.min.js');
add_admin_js('jquery.validate.min.js');
?>