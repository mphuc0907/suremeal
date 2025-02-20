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
$status = $queryCustomers[0]->status;
$avatar = $queryCustomers[0]->avatar;
$provider = $queryCustomers[0]->provider;

$args = array(
    'post_type' => 'san_pham',
    'posts_per_page' => 10,
    'orderby' => 'post_date',
);

$query_post = new WP_Query($args);
$posts = $query_post->posts;
$arg_product = array(
    'post_type' => array('product', 'product-app', 'smart_screen'),
    'posts_per_page' => -1,
);
$query_product = new WP_Query($arg_product);
////$products = $query_product->posts;
//$response = wp_remote_get('http://suremealdev.wecan-group.info/wp-json/wp/v2/suremeal_api');
//
//if (is_wp_error($response)) {
//    echo 'Error: ' . $response->get_error_message();
//} else {
//    $products = json_decode(wp_remote_retrieve_body($response), true);
//
//    // Hiển thị dữ liệu
////    if (!empty($posts)) {
////        foreach ($posts as $post) {
////            echo '<h2>' . esc_html($post['title']['rendered']) . '</h2>';
////            echo '<p>' . esc_html($post['content']['rendered']) . '</p>';
////        }
////    }
//}

// Query dữ liệu sản phẩm từ domain chính
$sql = "SELECT * FROM wp_posts WHERE post_type = 'product' AND post_status = 'publish'";
$products = $wpdb->get_results($sql);

$options = '';
foreach ($products as $product) {
    // Lấy giá trị custom field từ bảng wp_postmeta
    $price = $wpdb->get_var($wpdb->prepare(
        "SELECT meta_value FROM wp_postmeta WHERE post_id = %d AND meta_key = 'price'",
        $product->ID
    ));
    $sale_price = $wpdb->get_var($wpdb->prepare(
        "SELECT meta_value FROM wp_postmeta WHERE post_id = %d AND meta_key = 'sale_price'",
        $product->ID
    ));

    if ($sale_price) {
        $options .= '<option data-price="' . esc_attr($sale_price) . '" value="' . esc_attr($product->ID) . '">' . esc_html($product->post_title) . '</option>';
    }else {
        $options .= '<option data-price="' . esc_attr($price) . '" value="' . esc_attr($product->ID) . '">' . esc_html($product->post_title) . '</option>';
    }
}

$totalOrders = $wpdb->get_var("SELECT COUNT(*) FROM wp_orders WHERE id_dealer = '{$idCustomer}'");

$myrows = $wpdb->get_results("SELECT * FROM wp_orders WHERE id_dealer = '{$idCustomer}' ORDER BY time_order DESC");

$discount = $wpdb->get_results("SELECT * FROM wp_affiliate WHERE id_user = '{$idCustomer}' ORDER BY id ASC");

$discountList = json_decode($discount[0]->product_infomation);

?>
<input type="hidden" id="urlAjax" value="<?= admin_url() ?>admin-ajax.php">
<!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css"> -->
<style>
    input, select {
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
        width: 125px;
        height: 125px;
        border-radius: 10px;
    }

    .order-item-product .order-detail .list-product .morth-item .morth-img figure img {
        height: 100%;
        object-fit: contain;
    }

    .order-item-product .order-detail .list-product .morth-item .morth-img .info {

    }

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
        padding-top: 20px;
        margin-top: 30px;
        border-top: 1px solid #d9d9d9;
    }

    .order-item-product .order-detail .list-info .info__left ul li {
        display: flex;
        align-items: center;
        flex-basis: 50%;
        gap: 15px;
        margin-bottom: 10px;
    }

    .order-item-product .order-detail .list-info .info__left ul {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
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
        padding-left: 120px;
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

    .avatar {
        border-radius: 50%;
        width: 3rem;
        height: 3rem;
    }

    .form-group {
        border: 1px solid rgb(157, 157, 157);
        border-radius: 20px;
        padding: 30px 40px;
        margin-bottom: 16px;
    }

    .form-input {
        display: flex;
        justify-content: space-between;
        width: 70%;
        margin: auto;
    }

    .item-1 {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    #add-form-group {
        display: block;
        margin: auto;
        margin-top: 10px;
        border: none;
        cursor: pointer;
    }

    .remove-form-group {
        display: block;
        margin: auto;
        margin-top: 16px;
        border: none;
        cursor: pointer;
    }

    #form-add-discount {
        position: relative;
        padding-bottom: 3rem;
    }

    #submit-form {
        position: absolute;
        bottom: 0;
        left: 48%;
        width: fit-content;
        right: 48%;
        padding: 6px;
        border-radius: 10px;
        cursor: pointer;
    }

    #submit-form:hover {
        background: #000;
        color: white;
    }
</style>
<style>
    .error-message {
        color: red;
        display: block;
        margin-top: 5px;
        font-size: 14px;
    }

    .metabox-holder {
        width: 100%;
    }

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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script> -->
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
<!--                                                <figure>-->
<!--                                                    <img class="avatar"-->
<!--                                                         src="--><?//= $avatar ? $avatar : $url . '/assets/image/dashboard/avatar-80.svg' ?><!--"-->
<!--                                                         alt="">-->
<!--                                                </figure>-->
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
                                                    <span>Status:</span>
                                                    <strong><?= $status == 1 ? 'Activated' : 'Unactivated' ?></strong>
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
            <?php
            $affiliate = $wpdb->get_row($wpdb->prepare("SELECT * FROM wp_affiliate WHERE id_user = %d", $idCustomer));

            if ($affiliate) :
            ?>
            <div class="pos1">
                <h1>Percent affiliate</h1>
                <form id="affiliate-percent-form">
                    <input type="hidden" name="user_id" value="<?php echo esc_attr($idCustomer); ?>">

                        <label for="exampleInputEmail1">Percent</label>
                        <input class="form-control"
                               type="number"
                               name="percent"
                               id="percent"
                               value="<?php echo esc_attr($affiliate->percent); ?>"
                               min="0"
                               max="100"
                               required
                               style="width: 120px;"
                        >

                        <label for="exampleInputEmail1">Discount</label>
                        <input class="form-control"
                               type="number"
                               name="discount"
                               id="discount"
                               value="<?php echo esc_attr($affiliate->discount); ?>"
                               min="0"
                               max="100"
                               required
                               style="width: 120px;"
                        >


                    <button type="submit" class="btn btn-success" id="submit-form-percent">Submit</button>
                    <span id="percent-error" class="error-message"></span>
                </form>
            </div>
            <?php endif; ?>
            <div class="pos1">
                <h1>
                    Product affiliate
                </h1>
                <div class="postbox">
                    <div class="inside">
                        <form action="" id="form-add-discount">
                            <button type="button" id="submit-form">Submit</button>
                            <?php

                            foreach ($discountList as $item):

//                                $post = get_post($item->product);
                                ?>
                                <div class="form-group">
                                    <div class="form-input top-up">
                                        <div class="item-1">
                                            <label for="">Choose product:</label>
<!--                                            <select name="product[]" id="">-->
<!--                                                <option value="">Choose product</option>-->
<!--                                                --><?php
//                                                foreach ($products as $product) :
//                                                    $selected = ($product->ID == $item->product) ? 'selected' : '';
//                                                    ?>
<!--                                                    <option data-price="--><?//= $product->sale_price ? $product->sale_price : $product->price ?><!--" value="--><?//= esc_attr($product->ID) ?><!--" --><?//= $selected ?><!--<?//= $product->post_title ?></option>-->
<!--                                                --><?php //endforeach; ?>
<!--                                            </select>-->
                                            <select name="product[]" id="select" class="form-control selectpiker" >
                                                <option value="">Choose product</option>
                                                <?php

                                                foreach ($products as $key => $pro) :

                                                    $id = $item->ID; // Thay bằng ID bạn muốn truy vấn
                                                    $price = $wpdb->get_var($wpdb->prepare(
                                                        "SELECT meta_value FROM wp_postmeta WHERE post_id = %d AND meta_key = 'price'",
                                                        $pro->ID
                                                    ));
                                                    $sale_price = $wpdb->get_var($wpdb->prepare(
                                                        "SELECT meta_value FROM wp_postmeta WHERE post_id = %d AND meta_key = 'sale_price'",
                                                        $pro->ID
                                                    ));
                                                    $sql = $wpdb->prepare(
                                                        "SELECT * FROM wp_posts WHERE post_type = 'product' AND post_status = 'publish' AND ID = %d",
                                                        $id
                                                    );

                                                    $product_crr = $wpdb->get_row($sql); // Lấy một dòng duy nhất

                                                    $selected = ($pro->ID == $item->id) ? 'selected' : '';
                                                    ?>
                                                    <option data-price="<?= $sale_price ? $sale_price : $price ?>" value="<?= esc_attr($pro->ID ) ?>" <?= $selected ?>><?= $pro->post_title ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="item-1">
                                            <label for="">Price:</label>
                                            <input type="text" class="price-input" value="<?= $item->price ?>" disabled>
                                        </div>

                                    </div>
                                    <button class="remove-form-group">
                                        <svg fill="#000000" height="20px" width="20px" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                             viewBox="0 0 310.285 310.285" xml:space="preserve">
                                <path d="M155.143,0.001C69.597,0.001,0,69.597,0,155.143c0,85.545,69.597,155.142,155.143,155.142s155.143-69.597,155.143-155.142
                                    C310.285,69.597,240.689,0.001,155.143,0.001z M244.143,171.498c0,4.411-3.589,8-8,8h-163c-4.411,0-8-3.589-8-8v-32
                                    c0-4.411,3.589-8,8-8h163c4.411,0,8,3.589,8,8V171.498z"/>
                                </svg>
                                    </button>
                                </div>
                            <?php endforeach; ?>
                        </form>
                        <button id="add-form-group">
                            <svg width="24px" height="24px" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg" fill="#000000"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"><title>70 Basic icons by Xicons.co</title><path d="M24,3A21,21,0,1,0,45,24,21,21,0,0,0,24,3Z" fill="#000000"></path><path d="M32,26H16a2,2,0,0,1,0-4H32A2,2,0,0,1,32,26Z" fill="#ffffff"></path><path d="M24,34a2,2,0,0,1-2-2V16a2,2,0,0,1,4,0V32A2,2,0,0,1,24,34Z" fill="#ffffff"></path></g></svg>
                        </button>
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
                            <td> Stripe
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
add_admin_js('jquery.min.js');
add_admin_js('jquery.validate.min.js');
?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script>
    const form = document.querySelector(".form");
    const dropdowns = document.querySelectorAll(".dropdown");

    // Check if Dropdowns are Exist
    // Loop Dropdowns and Create Custom Dropdown for each Select Element
    if (dropdowns.length > 0) {
        dropdowns.forEach((dropdown) => {
            createCustomDropdown(dropdown);
        });
    }

    // Check if Form Element Exist on Page
    if (form !== null) {
        form.addEventListener("submit", (e) => {
            e.preventDefault();
        });
    }

    // Create Custom Dropdown
    function createCustomDropdown(dropdown) {
        // Get All Select Options
        // And Convert them from NodeList to Array
        const options = dropdown.querySelectorAll("option");
        const optionsArr = Array.prototype.slice.call(options);

        // Create Custom Dropdown Element and Add Class Dropdown
        const customDropdown = document.createElement("div");
        customDropdown.classList.add("dropdown");
        dropdown.insertAdjacentElement("afterend", customDropdown);

        // Create Element for Selected Option
        const selected = document.createElement("div");
        selected.classList.add("dropdown-select");
        selected.textContent = optionsArr[0].textContent;
        customDropdown.appendChild(selected);

        // Create Element for Dropdown Menu
        // Add Class and Append it to Custom Dropdown
        const menu = document.createElement("div");
        menu.classList.add("dropdown-menu");
        customDropdown.appendChild(menu);
        selected.addEventListener("click", toggleDropdown.bind(menu));

        // Create Search Input Element
        const search = document.createElement("input");
        search.placeholder = "Search...";
        search.type = "text";
        search.classList.add("dropdown-menu-search");
        menu.appendChild(search);

        // Create Wrapper Element for Menu Items
        // Add Class and Append to Menu Element
        const menuInnerWrapper = document.createElement("div");
        menuInnerWrapper.classList.add("dropdown-menu-inner");
        menu.appendChild(menuInnerWrapper);

        // Loop All Options and Create Custom Option for Each Option
        // And Append it to Inner Wrapper Element
        optionsArr.forEach((option) => {
            const item = document.createElement("div");
            item.classList.add("dropdown-menu-item");
            item.dataset.value = option.value;
            item.textContent = option.textContent;
            menuInnerWrapper.appendChild(item);

            item.addEventListener(
                "click",
                setSelected.bind(item, selected, dropdown, menu)
            );
        });

        // Add Selected Class to First Custom Select Option
        menuInnerWrapper.querySelector("div").classList.add("selected");

        // Add Input Event to Search Input Element to Filter Items
        // Add Click Event to Element to Close Custom Dropdown if Clicked Outside
        // Hide the Original Dropdown(Select)
        search.addEventListener("input", filterItems.bind(search, optionsArr, menu));
        document.addEventListener(
            "click",
            closeIfClickedOutside.bind(customDropdown, menu)
        );
        dropdown.style.display = "none";
    }

    // Toggle for Display and Hide Dropdown
    function toggleDropdown() {
        if (this.offsetParent !== null) {
            this.style.display = "none";
        } else {
            this.style.display = "block";
            this.querySelector("input").focus();
        }
    }

    // Set Selected Option
    function setSelected(selected, dropdown, menu) {
        // Get Value and Label from Clicked Custom Option
        const value = this.dataset.value;
        const label = this.textContent;

        // Change the Text on Selected Element
        // Change the Value on Select Field
        selected.textContent = label;
        dropdown.value = value;

        // Close the Menu
        // Reset Search Input Value
        // Remove Selected Class from Previously Selected Option
        // And Show All Div if they Were Filtered
        // Add Selected Class to Clicked Option
        menu.style.display = "none";
        menu.querySelector("input").value = "";
        menu.querySelectorAll("div").forEach((div) => {
            if (div.classList.contains("is-select")) {
                div.classList.remove("is-select");
            }
            if (div.offsetParent === null) {
                div.style.display = "block";
            }
        });
        this.classList.add("is-select");
    }

    // Filter the Items
    function filterItems(itemsArr, menu) {
        // Get All Custom Select Options
        // Get Value of Search Input
        // Get Filtered Items
        // Get the Indexes of Filtered Items
        const customOptions = menu.querySelectorAll(".dropdown-menu-inner div");
        const value = this.value.toLowerCase();
        const filteredItems = itemsArr.filter((item) =>
            item.value.toLowerCase().includes(value)
        );
        const indexesArr = filteredItems.map((item) => itemsArr.indexOf(item));

        // Check if Option is not Inside Indexes Array
        // And Hide it and if it is Inside Indexes Array and it is Hidden Show it
        itemsArr.forEach((option) => {
            if (!indexesArr.includes(itemsArr.indexOf(option))) {
                customOptions[itemsArr.indexOf(option)].style.display = "none";
            } else {
                if (customOptions[itemsArr.indexOf(option)].offsetParent === null) {
                    customOptions[itemsArr.indexOf(option)].style.display = "block";
                }
            }
        });
    }

    // Close Dropdown if Clicked Outside Dropdown Element
    function closeIfClickedOutside(menu, e) {
        if (
            e.target.closest(".dropdown") === null &&
            e.target !== this &&
            menu.offsetParent !== null
        ) {
            menu.style.display = "none";
        }
    }

</script>

<script>
    $(document).ready(function () {
        function updatePrice(selectElement) {
            let formInputDiv = $(selectElement).closest('.form-input');
            let priceInput = formInputDiv.find('.price-input');
            let selectedOption = $(selectElement).find('option:selected');

            if (selectedOption.val()) {
                let price = selectedOption.data('price');
                priceInput.val(new Intl.NumberFormat('en-US', {
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0
                }).format(price));
            } else {
                priceInput.val('');
            }
        }

        function updateProductSelections() {
            let selectedProducts = [];
            $('select[name="product[]"]').each(function () {
                let value = $(this).val();
                if (value) selectedProducts.push(value);
            });

            $('select[name="product[]"]').each(function () {
                let currentValue = $(this).val();
                $(this).find('option').each(function () {
                    if ($(this).val()) {
                        $(this).prop('disabled', selectedProducts.includes($(this).val()) && $(this).val() !== currentValue);
                    }
                });
            });
        }

        $(document).on('change', 'select[name="product[]"]', function () {
            updatePrice(this);
            updateProductSelections();
        });

        $(document).on('click', '#add-form-group', function (e) {
            e.preventDefault();
            let form = $('#form-add-discount');
            let newFormGroup = $('<div class="form-group"></div>');

            newFormGroup.html(`
            <div class="form-input top-up">
                <div class="item-1">
                    <label for="">Choose product:</label>
                    <select name="product[]" class="form-control selectpiker">
                        <option value="">Choose product</option>
                        <?= $options ?>
                    </select>
                </div>
                <div class="item-1">
                    <label for="">Price:</label>
                    <input type="text" class="price-input" value="" disabled>
                </div>
            </div>
            <button class="remove-form-group">Remove</button>
        `);

            form.append(newFormGroup);
            updateProductSelections();
        });

        $(document).on('click', '.remove-form-group', function (e) {
            e.preventDefault();
            $(this).closest('.form-group').remove();
            updateProductSelections();
        });

        let urlAjax = $('#urlAjax').val();
        let id_user = '<?= $idCustomer ?>';

        $('#submit-form').on('click', function (event) {
            event.preventDefault();
            let selectedProducts = [];
            $('select[name="product[]"]').each(function () {
                let selectedValue = $(this).val();
                if (selectedValue) {
                    let price = $(this).find(':selected').data('price');
                    selectedProducts.push({ id: selectedValue, price: price });
                }
            });

            $.ajax({
                url: urlAjax,
                type: 'POST',
                dataType: 'json',
                data: {
                    selectedProducts,
                    action: 'save_product_sale_partner',
                    id_user,
                },
                beforeSend: function () {
                    Swal.fire({
                        title: 'Processing',
                        html: 'Please wait...',
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                },
                success: function (rs) {
                    Swal.fire({
                        icon: rs.status == 1 ? 'success' : 'error',
                        text: rs.message
                    });
                }
            });
        });

        function deleteDiscount(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $('.divgif').show();
                    $.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        data: {
                            action: 'delete_discount',
                            discount_id: id,
                            security: ajax_object.nonce
                        },
                        success: function (response) {
                            $('.divgif').hide();
                            if (response.success) {
                                Swal.fire('Deleted!', 'Discount has been deleted.', 'success').then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire('Error!', 'Something went wrong.', 'error');
                            }
                        },
                        error: function () {
                            $('.divgif').hide();
                            Swal.fire('Error!', 'Something went wrong.', 'error');
                        }
                    });
                }
            });
        }

        $("#select").select2({
            tags: true,
            theme: "bootstrap"
        });

        updateProductSelections();
    });

</script>

<script>
jQuery(document).ready(function($) {
    $('#affiliate-percent-form').on('submit', function(e) {
        e.preventDefault();
        
        const percent = parseInt($('#percent').val());
        const discount = parseInt($('#discount').val());
        const errorElement = $('#percent-error');
        
        // Clear previous error
        errorElement.text('');
        
        // Validate input
        if (isNaN(percent) || percent <= 0 || percent >= 100) {
            errorElement.text('Please enter a number between 0 and 100');
            return false;
        }
        if (isNaN(discount) || discount <= 0 || discount >= 100) {
            errorElement.text('Please enter a number between 0 and 100');
            return false;
        }
        // Show loading
        $('.divgif').show();
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'update_affiliate_percent',
                user_id: $('input[name="user_id"]').val(),
                percent: percent,
                discount: discount,
                nonce: '<?php echo wp_create_nonce("update_affiliate_percent"); ?>'
            },
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'Affiliate percent updated successfully'
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.data || 'An error occurred'
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while updating'
                });
            },
            complete: function() {
                $('.divgif').hide();
            }
        });
    });
    
    // Add real-time validation
    $('#percent').on('input', function() {
        const value = $(this).val();
        const errorElement = $('#percent-error');
        
        if (value <= 0 || value >= 100) {
            errorElement.text('Please enter a number between 0 and 100');
        } else {
            errorElement.text('');
        }
    });
});
</script>