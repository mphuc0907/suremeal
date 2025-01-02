<?php
// Call google check captcha
function returnajax($rs)
{
    echo json_encode($rs);
    die();
}
function no_sql_injection($input)
{
    $arr = array("from", "select", "insert", "insert", "delete", "where", "drop", "drop table", "show tables", "*", "=", "update");
    $sql = str_replace($arr, "", $input);
    return trim(strip_tags(addslashes($sql))); #strtolower()
}

function save_viewed_product_to_cookie() {
    if (is_singular('product')) { // Kiểm tra nếu đang ở trang chi tiết sản phẩm
        $product_id = get_the_ID(); // Lấy ID của sản phẩm hiện tại

        // Lấy danh sách sản phẩm đã lưu trong cookie (nếu có)
        $viewed_products = isset($_COOKIE['viewed_products']) ? json_decode(stripslashes($_COOKIE['viewed_products']), true) : array();

        // Đảm bảo danh sách là một mảng
        if (!is_array($viewed_products)) {
            $viewed_products = array();
        }

        // Nếu ID sản phẩm chưa tồn tại trong danh sách, thêm vào
        if (!in_array($product_id, $viewed_products)) {
            $viewed_products[] = $product_id; // Thêm ID vào danh sách
        }

        // Lưu danh sách vào cookie (thời gian lưu: 30 ngày)
        setcookie('viewed_products', json_encode($viewed_products), time() + 30 * 24 * 60 * 60, "/");
    }
}
add_action('wp', 'save_viewed_product_to_cookie');
function get_viewed_products() {
    if (isset($_COOKIE['viewed_products'])) {
        $viewed_products = json_decode(stripslashes($_COOKIE['viewed_products']), true);
        return is_array($viewed_products) ? $viewed_products : array(); // Đảm bảo trả về mảng
    }
    return array(); // Trả về mảng rỗng nếu không có sản phẩm nào được lưu
}

function getLastOrderNumberFromDatabase(){
    global $wpdb;
    $order = $wpdb->get_row("SELECT * FROM wp_cart ORDER BY id DESC");
    $dem = 1;
    if(!empty($order)){
        $dem = (int)$order->id+1;
    }
    return $dem;
}
function xss($input)
{
    $input = str_replace('=', '', $input);
    $input = str_replace(';', '', $input);
    $input = str_replace(':', '', $input);
    $input = str_replace('[', '', $input);
    $input = str_replace(']', '', $input);
    $input = str_replace('?', '', $input);
    $input = str_replace('AND', '', $input);
    $input = str_replace('OR ', '', $input);
//    $input = str_replace('&', '', $input);
    $input = str_replace('\'', '', $input);
    $input = str_replace('"', '', $input);
    $input = str_replace('`', '', $input);
    $input = str_replace("'", '', $input);
    $input = str_replace('%', '', $input);
    $input = str_replace('<', '', $input);
    $input = str_replace('>', '', $input);
    $input = str_replace('*', '', $input);
    $input = str_replace('+', '', $input);
    $input = str_replace('#', '', $input);
    $input = str_replace(')', '', $input);
    $input = str_replace('(', '', $input);
    $input = str_replace('\\', '', $input);
    $input = str_replace('\/', '', $input);
//    $input = str_replace('-', '', $input);
    $input = str_replace('SHUTDOWN', '', $input);
    $input = str_replace('DROP', '', $input);
    $input = preg_replace("/[`]/", '', $input);
    $input = addslashes($input);
    $input = htmlspecialchars($input);
    $input = strip_tags($input);

    return $input;
}
function googleCaptcha($token)
{

    $secret = get_field("secret_key", "option");
    // call curl to POST request
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array('secret' => $secret, 'response' => $token)));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    $arrResponse = json_decode($response, true);
    return $arrResponse;
}
function checkImage($id)
{
    $avatar_hot = get_the_post_thumbnail_url($id, 'full');
    if ($avatar_hot == '') {
        $avatar_hot = get_field('image_no_image', 'option');
    }
    return $avatar_hot;
}
function formatBalance($balance) {
    $currency_unit = "$";

    // Nếu giá trị là 0, trả về chuỗi "Giá liên hệ"
    if ($balance == 0) {
        return $currency_unit . '0' ;
    }

    // Định dạng số với 4 chữ số thập phân, sau đó loại bỏ các số 0 không cần thiết
    $formatted = rtrim(number_format($balance, 4, '.', ','), '0');

    // Nếu ký tự cuối là dấu phẩy, loại bỏ luôn
    $formatted = rtrim($formatted, '.');

    // Thêm đơn vị tiền tệ vào sau kết quả
    return $currency_unit . '' . $formatted;
}


add_action('wp_ajax_nopriv_get_id_product', 'get_id_product');
add_action('wp_ajax_get_id_product', 'get_id_product');

function get_id_product() {
    // Nhận ID sản phẩm từ POST request
    $id_product = $_POST['id_product'];

    // Kiểm tra nếu ID sản phẩm hợp lệ
    if (!$id_product) {
        wp_send_json(array(
            'success' => false,
            'message' => 'Invalid product ID.   '
        ));
    }

    // Lấy thông tin sản phẩm
    $products_of_the_same_type = get_field('products_of_the_same_type', $id_product);
    $product = array(
        'id' => $id_product,
        'image' => checkImage($id_product),
        'title' => get_the_title($id_product),
        'pack' => $products_of_the_same_type
    );

    // Trả dữ liệu về dạng JSON
    wp_send_json(array(
        'success' => true,
        'data' => $product
    ));

}

add_action('wp_ajax_nopriv_submitReview', 'submitReview');
add_action('wp_ajax_submitReview', 'submitReview');
function submitReview(){
    global $wpdb;
    $token = $_POST['token1'];
    $action = $_POST['action1'];

    $arrResponse = googleCaptcha($token);
    if ($arrResponse["success"] == '1' && $arrResponse["action"] == $action && $arrResponse["score"] >= 0.5) {
        $email = no_sql_injection(xss($_POST['email']));
        $name_user = no_sql_injection(xss($_POST['name_user']));
        $title_review = no_sql_injection(xss($_POST['email']));
        $activeCount = no_sql_injection(xss($_POST['activeCount']));
        $id_user = no_sql_injection(xss($_POST['id_user']));
        $content_review = no_sql_injection(xss($_POST['content_review']));
        $id_product = no_sql_injection(xss($_POST['id_product']));

        if (!empty($id_user)){
            $user = $id_user;
        }else {
            $user ="";
        }

        // Tạo dữ liệu để chèn vào bảng
        $data = [
            'id_user' => $user, // Lấy ID user hiện tại
            'id_product' => $id_product,
            'name' => $name_user,
            'email' => $email,
            'title' => $title_review,
            'comment' => $content_review,
            'avatar' => '', // Avatar có thể để trống hoặc lấy từ WP profile
            'rating' => $activeCount,
            'likes' => 0, // Mặc định là 0 lượt thích
            'status' => 0
        ];

        // Chèn dữ liệu vào bảng wp_review
        $table_name = $wpdb->prefix . 'review';
        $inserted = $wpdb->insert($table_name, $data);

        if ($inserted) {
            $rs['status'] = 1;
            $rs['mess'] = 'Review submitted successfully!';
        } else {
            $rs['status'] = 0;
            $rs['mess'] = 'Error occurred while submitting review.';
        }
    }else {
        $rs['status'] = error_code;
        $rs['mess'] = messauth;

    }
    returnajax($rs);
}
function sendMailOrder ($order_code) {
    global $wpdb;
    $proorder = $wpdb->get_results("SELECT * FROM wp_orders WHERE order_code = '{$order_code}' ORDER BY id DESC");

    $datapros = $proorder[0];

    $delivery_information = json_decode(str_replace('\\', "", $datapros->dataproduct), true);
//    print_r($decodedData);die();
//    $delivery_information = json_decode($datapros->dataproduct);

    //Gửi mail
    $table = '<table style="width: 1000px;
    border: solid 1px;
    text-align: center;
    border-collapse: collapse;">
                        <thead style="    background: #e5c9c9;">
                        <th>#</th>
                        <th> Name</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Total</th>

</thead>
<tbody>';
    foreach ($delivery_information as $key => $value):

//        $title = get_the_title($value->id);
        $total = $value['price'] * $value['qty'];
        $table .= '
																				               <tr>
																				               <td style="border: solid 1px;">' . ($key + 1) . '</td>
																				               <td style="border: solid 1px;">' . $value['title'] . '</td>
																				               <td style="border: solid 1px;">' . $value['qty'] . '</td>
																				               <td style="border: solid 1px;">' . $value['price'] . '</td>
																				               <td style="border: solid 1px;">' . $total . '</td>

																				</tr>

																				                ';
    endforeach;
    $table .= '</tbody>';


    // Gửi mail đến khách hàng
    $time_pay = date('d/m/Y', $datapros->order);

    $headers[] = "Content-type:text/html;charset=utf-8" . "\r\n";
    $body = get_field('config_order', 'option');
    $body = str_replace('__fullname__', $datapros->name_user, $body);
    $body = str_replace('__address__', $datapros->address1, $body);
    $body = str_replace('__phonenumber__', $datapros->phoneNumber, $body);
    $body = str_replace('__email__', $datapros->email, $body);
    $body = str_replace('__cty__', $datapros->city, $body);
    $body = str_replace('__state__', $datapros->state, $body);
    $body = str_replace('__totalorder__', $datapros->price_payment, $body);
    $body = str_replace('__shippingfee__', $datapros->transport_fee, $body);
    $body = str_replace('__country__', $datapros->country, $body);
    $body = str_replace('__html__', $table, $body);
    $body = str_replace('__codeoders__', $datapros->order_code, $body);
    $body = str_replace('__timeorder__', date('m/d/Y', $datapros->time_order), $body);
    $body = str_replace('__methodpay__', 'stripe', $body);
    wp_mail($datapros->email, 'Suremeal - Order', $body, $headers);


    $tables = '<table style="width: 1000px;
    border: solid 1px;
    text-align: center;
    border-collapse: collapse;">
                        <thead style="background: #e5c9c9;">
                        <th>#</th>
                        <th>Product Name</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Total</th>

</thead>
<tbody>';
    foreach ($delivery_information as $key => $value):
        $title = get_the_title($value->id);
        $total = $value->price * $value->qty;
        $tables .= '<tr>
																				               <td style="border: solid 1px;">' . ($key + 1) . '</td>
																				               <td style="border: solid 1px;">' . $title . '</td>
																				               <td style="border: solid 1px;">' . $value->qty . '</td>
																				               <td style="border: solid 1px;">' . $value->price . '</td>
																				               <td style="border: solid 1px;">' . $total . '</td>

																				</tr>

																				                ';
    endforeach;
    $tables .= '</tbody>';
    // Gửi mail đến admin
    $admin_email = get_field('config_order_admin_email', 'option');
    $body = get_field('config_order', 'option');
    $body = str_replace('__fullname__', $datapros->name_user, $body);
    $body = str_replace('__address__', $datapros->address1, $body);
    $body = str_replace('__phonenumber__', $datapros->phoneNumber, $body);
    $body = str_replace('__html__', $tables, $body);
    $body = str_replace('__email__', $datapros->email, $body);
    $body = str_replace('__state__', $datapros->state, $body);
    $body = str_replace('__country__', $datapros->country, $body);
    $body = str_replace('__codeoders__', $datapros->order_code, $body);
    $body = str_replace('__timeorder__', date('m/d/Y', $datapros->time_order), $body);
    $body = str_replace('__methodpay__', 'stripe', $body);
//        $body = str_replace('__order__', '<p style="font-weight: bold; font-size: 20px;">Test</p>', $body);

    wp_mail($admin_email, 'Suremeal - Customer order information', $body, $headers);
}


add_action('wp_ajax_nopriv_submitOrder', 'submitOrder');
add_action('wp_ajax_submitOrder', 'submitOrder');

function submitOrder() {
    session_start();
    global $wpdb;
    require_once '/var/www/domains/qixtech.com/suremeal.qixtech.com/vendor/autoload.php';

    $dataProduct = $_POST['dataProduct'];
    $fullName = no_sql_injection(xss($_POST['fullName']));
    $email = no_sql_injection(xss($_POST['email']));
    $discount = (float)$_POST['discount'];
    $voucher_code = $_POST['voucher_code'];
    $phone_numer = no_sql_injection(xss($_POST['phone_numer']));
    $saveInfo = no_sql_injection(xss($_POST['saveInfo']));
    $country = no_sql_injection(xss($_POST['country']));
    $address1 = no_sql_injection(xss($_POST['address1']));
    $postaCode = no_sql_injection(xss($_POST['postaCode']));
    $address2 = no_sql_injection(xss($_POST['address2']));
    $city = no_sql_injection(xss($_POST['city']));
    $totalPrice = (float)no_sql_injection(xss($_POST['totalPrice']));
    $shippingFee = (float)no_sql_injection(xss($_POST['shippingFee']));
    $price = (float)no_sql_injection(xss($_POST['price']));
    $state = no_sql_injection(xss($_POST['state']));
    $province = no_sql_injection(xss($_POST['province']));
    $zipCode = no_sql_injection(xss($_POST['zipCode']));
    $decodedData = json_decode(str_replace('\\', "", $dataProduct), true);
    $timestamp = time();
    foreach ($decodedData as $key => $value) {
        $instock = get_field('instock', $value['id']);
        if ($value['qty'] > $instock) {
            $rs['status'] = 0;
            $rs['mess'] = $value['title'] . ' product is not in stock!';
            returnajax($rs);
        }else {
            continue;
        }
    }

    //update voucher nếu có
    $voucher = $wpdb->get_row($wpdb->prepare("SELECT * FROM wp_voucher WHERE voucher_code = %s", $voucher_code));
    if ($voucher) {
        if ((int)$voucher->number_of_vouchers > 0) {
            $wpdb->update(
                'wp_voucher',
                [
                    'number_of_vouchers' => (int)$voucher->number_of_vouchers - 1,
                    'number_of_uses' => (int)$voucher->number_of_uses + 1
                ],
                ['voucher_code' => $voucher_code]
            );
        } else {
            $rs['status'] = 0;
            $rs['mess'] = 'Voucher is no longer valid or has been used up.';
            returnajax($rs);
            return;
        }
    }

    // Generate unique order code
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $length = 9;
    do {
        $order_code = '';
        for ($i = 0; $i < $length; $i++) {
            $order_code .= $characters[rand(0, strlen($characters) - 1)];
        }
        $order_code .= rand(0, 9);
        $exists = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM wp_orders WHERE order_code = %s", $order_code));
        if ($exists) $length += 2;
    } while ($exists);

    $id_user = '';
    $authenticated_user = validate_user_token();
    if (isset($_COOKIE['user_token']) && $authenticated_user) {
        $id_user = $authenticated_user->ID;
    }

    $state_province = "";
    if (!empty($state)) {
        $state_province = $state;
    }else {
        $state_province = $province;
    }

    $data = [
        'id_user' => $id_user,
        'order_code' => $order_code,
        'address1' => $address1,
        'discount_price' => $discount,
        'address2' => $address2,
        'phoneNumber' => $phone_numer,
        'code_voucher' => $voucher_code,
        'posta_code' => $postaCode,
        'city' => $city,
        'state' => $state_province,
        'name_user' => $fullName,
        'email' => $email,
        'country' => $country,
        'ZIPCode' => $zipCode,
        'dataproduct' => $dataProduct,
        'price_payment' => $totalPrice,
        'price' => $price,
        'time_order' => $timestamp,
        'payment_method' => 'stripe',
        'transport_fee' => $shippingFee,
        'status' => '1',
        'status_transport' => '1'
    ];

    $table_name = $wpdb->prefix . 'orders';
    $inserted = $wpdb->insert($table_name, $data);
    foreach ($decodedData as $key => $value) {
        $qty = $value['qty'];
        $instock = get_field('instock', $value['id']);
        $newInstock = $instock - $qty;
        update_field('instock', $newInstock, $value['id']);

    }
    $authenticated_user = validate_user_token();
    $id = $authenticated_user->ID;

        if ($saveInfo == 1) {

            $data_user = array(
                'city' => $city,
                'address1_shipper' => $address1,
                'address2_shipper' => $address2,
                'fullname_shipper' => $fullName,
                'zipcode_shipper' => $zipCode,
                'email_shipper' => $email,
                'state' => $state_province,
                'phone_number' => $phone_numer,
                'country' => $country,
            );

            $where = array('id' => $id);

            $wpdb->update('wp_account_users', $data_user, $where);
        }

    sendMailOrder($order_code);
    // Calculate line items and distribute discount
    $totalProductPriceBeforeDiscount = 0;
    foreach ($decodedData as $product) {
        $totalProductPriceBeforeDiscount += $product['price'] * $product['qty'];
    }

    $lineItems = [];
    foreach ($decodedData as $product) {
        $productTitle = $product['title'];
        $productPrice = (float)$product['price'];
        $productQuantity = (int)$product['qty'];

        $discountPerProduct = ($discount / $totalProductPriceBeforeDiscount) * ($productPrice * $productQuantity);
        $adjustedPrice = $productPrice - ($discountPerProduct / $productQuantity);
        if ($adjustedPrice < 0) {
            $adjustedPrice = 0;
        }

        $lineItems[] = [
            'price_data' => [
                'currency' => 'usd',
                'product_data' => [
                    'name' => $productTitle,
                    'images' => [$product['img']],
                ],
                'unit_amount' => round($adjustedPrice * 100),
            ],
            'quantity' => $productQuantity,
        ];
    }

    $lineItems[] = [
        'price_data' => [
            'currency' => 'usd',
            'product_data' => [
                'name' => 'Shipping Fee',
            ],
            'unit_amount' => round($shippingFee * 100),
        ],
        'quantity' => 1,
    ];

// Stripe Setup
    $stripeSecretKey = 'sk_test_51QJVYKLGjsMtdIMl0BOmJPNCwXME37Fcvf6IlSjCKBV5fQHglp9VNvj62dCnJeL6HIlVDuNfjTt4tMRiDOlQ2XaC00wu6wLQyk';
    \Stripe\Stripe::setApiKey($stripeSecretKey);

    $YOUR_DOMAIN = home_url();

    header('Content-Type: application/json');
    $checkout_session = \Stripe\Checkout\Session::create([
        'payment_method_types' => ['card'],
        'line_items' => $lineItems,
        'mode' => 'payment',
        'success_url' => $YOUR_DOMAIN . '/payment-success?session_id={CHECKOUT_SESSION_ID}',
        'cancel_url' => $YOUR_DOMAIN . '/payment-error',
    ]);


    $_SESSION['order_code'] = $order_code;



    $rs = [];
    if ($inserted) {
        $rs['status'] = 1;
        $rs['url'] = $checkout_session->url;
        $rs['mess'] = 'Order submitted successfully!';
    } else {
        $rs['status'] = 0;
        $rs['mess'] = 'Error occurred while submitting Order.';
    }

    returnajax($rs);
}




//function calculateCartTotal($eligible_products) {
//    // Giả sử bạn có hàm này để tính tổng giá trị các sản phẩm đủ điều kiện trong giỏ hàng
//    $total = 0;
//    foreach ($eligible_products as $product_id) {
//        // Lấy giá từng sản phẩm từ cơ sở dữ liệu hoặc session
//        $price = getProductPrice($product_id);
//        $total += $price;
//    }
//    return $total;
//}
//
//function getProductPrice($product_id) {
//    // Lấy giá sản phẩm từ cơ sở dữ liệu
//
//        $args = array(
//            'post_type'      => 'product',
//            'post__in'       => $product_id,
//            'posts_per_page' => -1,
//            'orderby'        => 'post__in', // Sắp xếp theo thứ tự trong cookie
//        );
//
//        $viewed_query = new WP_Query($args);
//        $post_view = $viewed_query->posts;
//
//        $price = get_field('price', $post_view[0]->ID );
//        $sale_price = get_field('sale_price', $post_view[0]->ID);
//
//
//    return $product ? $product->price : 0;
//}





function addVoucher()
{
    global $wpdb;
    // Lấy dữ liệu từ form
    $ten_chuong_trinh = sanitize_text_field($_POST['ten_chuong_trinh']);
    $ma_voucher = sanitize_text_field($_POST['ma_voucher']);
    $time_start = sanitize_text_field($_POST['time_start']);
    $time_end = sanitize_text_field($_POST['time_end']);
    $loai_giam_gia = sanitize_text_field($_POST['loai_giam_gia']);
    $muc_giam = sanitize_text_field($_POST['muc_giam']);
    $muc_giam_2 = sanitize_text_field($_POST['muc_giam_2']);
    $muc_giam_co_gioi_han = sanitize_text_field($_POST['muc_giam_co_gioi_han']);
    $gia_tri_toi_thieu = sanitize_text_field($_POST['gia_tri_toi_thieu']);
    $so_luong = sanitize_text_field($_POST['so_luong']);
    $type = sanitize_text_field($_POST['type']);
    $producdIdChoose = sanitize_text_field($_POST['producdIdChoose']);
    // nếu loai_giam_gia = 0 thì dùng muc_giam còn loai_giam_gia = 1 thì dùng muc_giam_2
    $muc_giam_ins = 0;
    if ($loai_giam_gia == 1) {
        $muc_giam_ins = $muc_giam;
    } else {
        $muc_giam_ins = $muc_giam_2;
    }
    $data = array(
        'voucher_name' => $ten_chuong_trinh,
        'voucher_code' => $ma_voucher,
        'start_date' => $time_start,
        'end_date' => $time_end,
        'discount_type' => $loai_giam_gia,
        'discount_amount' => $muc_giam_ins,
        'max_discount' => $muc_giam_co_gioi_han,
        'min_order' => $gia_tri_toi_thieu,
        'products' => $producdIdChoose,
        'number_of_vouchers' => $so_luong,
        'type' =>  $type,
        'status' => 2,
    );
    $check = $wpdb->get_row("SELECT * FROM wp_voucher WHERE voucher_code = '$ma_voucher'");


    if ($check) {
        $rs = ['status' => 0, 'message' => 'Voucher code already exists!'];
    } elseif ($wpdb->insert('wp_voucher', $data)) {
        $rs = ['status' => 1, 'message' => 'Voucher added successfully'];
    } else {
        $rs = ['status' => 0, 'message' => 'Adding voucher failed!'];
    }
    returnajax($rs);
}
add_action('wp_ajax_add_voucher', 'addVoucher');
add_action('wp_ajax_nopriv_add_voucher', 'addVoucher');

function updateVoucher()
{
    global $wpdb;
    $id = $_POST['id'];
    $ten_chuong_trinh = $_POST['ten_chuong_trinh'];
    $muc_giam = $_POST['muc_giam'];
    $muc_giam_2 = $_POST['muc_giam_2'];
    $muc_giam_co_gioi_han = $_POST['muc_giam_co_gioi_han'];
    $loai_giam_gia = $_POST['loai_giam_gia'];
    $statusmucgiamtoida = $_POST['statusmucgiamtoida'];
    $statuskieuapdung = $_POST['statuskieuapdung'];
    $so_luong = $_POST['so_luong'];
    $gia_tri_toi_thieu = $_POST['gia_tri_toi_thieu'];
    $producdIdChoose = $_POST['producdIdChoose'];
   
    $time_start = $_POST['time_start'];
    $time_end = $_POST['time_end'];
    $type = $_POST['type'];
    $muc_giam_ins = 0;
    if ($loai_giam_gia == 1) {
        $muc_giam_ins = $muc_giam;
    } else {
        $muc_giam_ins = $muc_giam_2;
    }
    $data = array(
        'voucher_name' => $ten_chuong_trinh,
        'discount_type' => $loai_giam_gia,
        'discount_amount' => $muc_giam_ins,
        'max_discount' => $muc_giam_co_gioi_han,
        'number_of_vouchers' => $so_luong,
        'start_date' => $time_start,
        'end_date' => $time_end,
        'type' => $type,
        'products' => $producdIdChoose,
    );

    $where = array('id' => $id);

    if ($wpdb->update('wp_voucher', $data, $where)) {
        $rs['status'] = 1;
        $rs['message'] = 'Voucher updated successfully';
        returnajax($rs);
    } else {
        $rs['status'] = 0;
        $rs['message'] = 'Voucher update failed!';
        returnajax($rs);
    }

}

add_action('wp_ajax_update_voucher', 'updateVoucher');
add_action('wp_ajax_nopriv_update_voucher', 'updateVoucher');
function hideVoucher()
{
    global $wpdb;
    $id = $_POST['id'];
    if ($wpdb->delete('wp_voucher', array('id' => $id))) {
        $rs['status'] = success_code;
        $rs['message'] = 'Voucher hidden successfully';
        returnajax($rs);
    } else {
        $rs['status'] = error;
        $rs['message'] = 'Hiding voucher failed!';
        returnajax($rs);

    }
    die();
}
add_action('wp_ajax_hide_voucher', 'hideVoucher');
add_action('wp_ajax_nopriv_hide_voucher', 'hideVoucher');
function countReview($id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'review';

    // Truy vấn kiểm tra số lượng review
    $totalReviews = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $table_name WHERE id_product = %d AND status = 1",
        $id
    ));

    // Nếu không có review, trả về 0
    return $totalReviews ? (int)$totalReviews : 0;
}
function renderStarRatingByProductId($productId, $maxRating = 5) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'review';

    // Lấy tổng số sao trung bình cho sản phẩm từ database
    $query = $wpdb->prepare(
        "SELECT AVG(rating) AS avg_rating FROM $table_name WHERE id_product = %d AND status = 1",
        $productId // Trạng thái = 1 (có thể là đánh giá đã phê duyệt hoặc trạng thái hợp lệ)
    );
    $averageRating = $wpdb->get_var($query);

    // Nếu không có đánh giá hoặc trung bình là 0, hiển thị 5 sao trống (star-second2.svg)
    if (!$averageRating) {
        $averageRating = 0; // Đặt mặc định là 0
    }

    // Xây dựng HTML cho đánh giá
    $output = '';
    for ($i = 1; $i <= $maxRating; $i++) {
        // Nếu rating >= $i, hiển thị sao vàng, nếu không thì sao xám
        $starImage = ($averageRating >= $i) ? 'star-second.svg' : 'star-second2.svg';
        $output .= "<figure><img src=\"/html/assets/image/icon/{$starImage}\" alt=\"star icon\"></figure>";
    }

    return $output;
}


add_action('wp_ajax_sortByReview', 'sortByReview');
add_action('wp_ajax_nopriv_sortByReview', 'sortByReview');

function sortByReview() {
    global $wpdb;
    $dataSort = $_POST['dataSort'];
    $idProduct = $_POST['idProduct'];
    $table_name = $wpdb->prefix . 'review';

    // Định nghĩa câu lệnh SQL mặc định (sắp xếp theo ngày tạo mới nhất)
    $orderBy = 'ORDER BY created_at DESC';

    // Kiểm tra giá trị $dataSort và thay đổi câu lệnh SQL phù hợp
    switch ($dataSort) {
        case 'highest_rating':
            $orderBy = 'ORDER BY rating DESC'; // Sắp xếp theo đánh giá cao nhất
            break;
        case 'lowest_rating':
            $orderBy = 'ORDER BY rating ASC'; // Sắp xếp theo đánh giá thấp nhất
            break;
        case 'most_helpful':
            $orderBy = 'ORDER BY likes DESC'; // Sắp xếp theo số lượng likes (giả sử có trường likes)
            break;
        case 'most_recent':
        default:
            $orderBy = 'ORDER BY created_at DESC'; // Sắp xếp theo ngày tạo mới nhất
            break;
    }

    // Truy vấn dữ liệu từ bảng review
    $reviews = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM $table_name WHERE id_product = %d AND status = 1 $orderBy",
        $idProduct
    ));

    // Tạo HTML cho kết quả đánh giá
    $html = "";
    if ($reviews) {
        foreach ($reviews as $review) {
            $html .= '<div class="p-5 rounded-xl flex flex-col gap-4 border border-solid border-neutral-300">
                        <p class="text-body-lg-regular text-neutral-700">' . esc_html($review->comment) . '</p>
                        <div class="flex flex-col md:flex-row items-start lg:items-end gap-6 md:gap-10 justify-between">
                            <div class="flex gap-4">
                                <figure class="w-[68px] h-[68px] rounded-full"><img src="/html/assets/image/avatar-68.png" alt="icon"></figure>
                                <div class="flex flex-col gap-2">
                                    <p class="text-heading-h7 text-secondary">' . esc_html($review->name) . '</p>
                                    <div class="flex items-center gap-1">';

            $max_rating = 5;
            for ($i = 1; $i <= $max_rating; $i++) {
                $star_image = ($review->rating >= $i) ? 'star-yellow.svg' : 'star-gray.svg';
                $html .= '<figure><img src="/html/assets/image/icon/' . $star_image . '" alt="icon"></figure>';
            }

            $html .= '   </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="flex items-center gap-1">
                            <figure><img src="/html/assets/image/icon/like-gray.svg" alt=""></figure>
                            <p class="text-body-lg-regular text-neutral-500">' . esc_html($review->likes) . '</p>
                        </div>
                    </div>
                </div>
            </div>';
        }
    } else {
        $html = "<p>No reviews found.</p>";
    }

    // Trả về kết quả HTML
    echo json_encode(['status' => 1, 'html' => $html]);
    wp_die();
}
function getStatusPayment($status)
{
    $trang_thai = [
        1 => 'Processing',
        2 => 'Completed',
        3 => 'In progress',
        4 => 'Canceled',
    ];

    $text = '';
    for ($i = 1; $i <= count($trang_thai); $i++) {
        if ($status == $i) {
            $text .= "<option selected value='" . $i . "'>" . $trang_thai[$i] . "</option>";
        } else {
            $text .= "<option value='" . $i . "'>" . $trang_thai[$i] . "</option>";
        }
    }
    return $text;
}

function getStatusDealed($status)
{
    $trang_thai = [
        2 => 'Activated',
        1 => 'Not activated yet',
    ];

    $text = '';
    for ($i = 1; $i <= count($trang_thai); $i++) {
        if ($status == $i) {
            $text .= "<option selected value='" . $i . "'>" . $trang_thai[$i] . "</option>";
        } else {
            $text .= "<option value='" . $i . "'>" . $trang_thai[$i] . "</option>";
        }
    }
    return $text;
}

function getStatusShiping($status)
{
    $trang_thai = [
        1 => 'Order Placed',
        2 => 'Packaging',
        3 => 'On The Road',
        4 => 'Delivered',
    ];
    $text = '';
    for ($i = 1; $i <= count($trang_thai); $i++) {
        if ($status == $i) {
            $text .= "<option selected value='" . $i . "'>" . $trang_thai[$i] . "</option>";
        } else {
            $text .= "<option value='" . $i . "'>" . $trang_thai[$i] . "</option>";
        }
    }
    return $text;
}
// Thay đổi trạng thái thanh toan
add_action('wp_ajax_nopriv_changePaymentStatus', 'changePaymentStatus');
add_action('wp_ajax_changePaymentStatus', 'changePaymentStatus');

function changePaymentStatus()
{
    global $wpdb;
    if (!isset($_POST['status']) || !isset($_POST['orderId'])) {
        $rs['status'] = error_code;
        $rs['mess'] = messerror . " Lỗi Validate";
        returnajax($rs);
    }
    $status = no_sql_injection(xss($_POST['status']));
    $orderId = no_sql_injection(xss($_POST['orderId']));
    if ($status < 1 || $status > 4) {
        $rs['status'] = error_code;
        $rs['mess'] = messerror . " Lỗi Validate";
        returnajax($rs);
    }
    $getOrder = $wpdb->get_row("SELECT * FROM wp_orders WHERE id = {$orderId}");
    $order_code = $getOrder->order_code;
    if (empty($getOrder)) {
        $rs['status'] = error_code;
        $rs['mess'] = "No order found. Please check and try again.";
        returnajax($rs);
    }

    $wpdb->update(
        'wp_orders',
        array('status' => $status),
        array('id' => $orderId)
    );
    $rs['status'] = 'success_code';
    $rs['mess'] = "Status updated successfully!";
    returnajax($rs);
}
// Thay đổi trạng thái đơn hàng
add_action('wp_ajax_nopriv_changeTransportStatus', 'changeTransportStatus');
add_action('wp_ajax_changeTransportStatus', 'changeTransportStatus');

function changeTransportStatus()
{
    global $wpdb;
    if (!isset($_POST['status']) || !isset($_POST['orderId'])) {
        $rs['status'] = error_code;
        $rs['mess'] = messerror . " Lỗi Validate";
        returnajax($rs);
    }
    $status = no_sql_injection(xss($_POST['status']));
    $orderId = no_sql_injection(xss($_POST['orderId']));
    if ($status < 1 || $status > 5) {
        $rs['status'] = error_code;
        $rs['mess'] = messerror . " Lỗi Validate";
        returnajax($rs);
    }
    $getOrder = $wpdb->get_row("SELECT * FROM wp_orders WHERE id = {$orderId}");
    if (empty($getOrder)) {
        $rs['status'] = error_code;
        $rs['mess'] = "No order found. Please check and try again.";
        returnajax($rs);
    }
    $wpdb->update(
        'wp_orders',
        array('status_transport' => $status),
        array('id' => $orderId)
    );
    // Cap nhat vao thong bao
    $tieu_de = '';
    $noi_dung = '';
//    $link = site_url() . '/chi-tiet-don-hang/?token=' . $getOrder->id;
    if ($status == 2) {
        $tieu_de = 'Order confirmed';
        $noi_dung = 'Order <strong>' . $getOrder->order_code . '</strong> has been confirmed.';
    } elseif ($status == 3) {
        $tieu_de = 'Shipping';
        $noi_dung = 'Order <strong>' . $getOrder->order_code . '</strong> on its way to you.';
    } elseif ($status == 4) {
        $tieu_de = 'Order delivered successfully';
        $noi_dung = 'Order <strong>' . $getOrder->order_code . '</strong> successfully delivered to you.';
    } elseif ($status == 5) {
        $tieu_de = 'Order has been cancelled';
        $noi_dung = 'Order <strong>' . $getOrder->order_code . '</strong> has been cancelled.';
    }
//    $wpdb->insert(
//        'notification_',
//        array(
//            'user_id' => $getOrder->id_user,
//            'order_id' => $getOrder->id,
//            'tieu_de' => $tieu_de,
//            'noi_dung' => $noi_dung,
//            'status' => 1,
//            'created_at' => date('Y-m-d H:i:s'),
//        )
//    );
    $rs['status'] = 'success_code';
    $rs['mess'] = "Status updated successfully!";
    returnajax($rs);
}

// Thay đổi trạng thái thanh toan
add_action('wp_ajax_nopriv_changeUserStatus', 'changeUserStatus');
add_action('wp_ajax_changeUserStatus', 'changeUserStatus');

function changeUserStatus()
{
    global $wpdb;
    if (!isset($_POST['status']) || !isset($_POST['UserId'])) {
        $rs['status'] = error_code;
        $rs['mess'] = messerror . " Lỗi Validate";
        returnajax($rs);
    }
    $status = no_sql_injection(xss($_POST['status']));
    $orderId = no_sql_injection(xss($_POST['UserId']));
    if ($status < 1 || $status > 2) {
        $rs['status'] = error_code;
        $rs['mess'] = messerror . " Lỗi Validate";
        returnajax($rs);
    }
    $getOrder = $wpdb->get_row("SELECT * FROM wp_account_dealers WHERE id = {$orderId}");
    $order_code = $getOrder->id;
    if (empty($getOrder)) {
        $rs['status'] = error_code;
        $rs['mess'] = "No user found. Please check and try again.";
        returnajax($rs);
    }

    $wpdb->update(
        'wp_account_dealers',
        array('status' => $status),
        array('id' => $orderId)
    );
    $rs['status'] = 'success_code';
    $rs['mess'] = "Status updated successfully!";
    returnajax($rs);
}

// Thay đổi trạng thái thanh toan
add_action('wp_ajax_nopriv_changeVoucherStatus', 'changeVoucherStatus');
add_action('wp_ajax_changeVoucherStatus', 'changeVoucherStatus');

function changeVoucherStatus()
{
    global $wpdb;
    if (!isset($_POST['status']) || !isset($_POST['voucherId'])) {
        $rs['status'] = error_code;
        $rs['mess'] = messerror . " Lỗi Validate";
        returnajax($rs);
    }
    $status = no_sql_injection(xss($_POST['status']));
    $orderId = no_sql_injection(xss($_POST['voucherId']));
    if ($status < 1 || $status > 2) {
        $rs['status'] = error_code;
        $rs['mess'] = messerror . " Lỗi Validate";
        returnajax($rs);
    }
    $getOrder = $wpdb->get_row("SELECT * FROM wp_voucher WHERE id = {$orderId}");
    $order_code = $getOrder->id;
    if (empty($getOrder)) {
        $rs['status'] = error_code;
        $rs['mess'] = "No voucher found. Please check and try again.";
        returnajax($rs);
    }

    $wpdb->update(
        'wp_voucher',
        array('status' => $status),
        array('id' => $orderId)
    );
    $rs['status'] = 'success_code';
    $rs['mess'] = "Status updated successfully!";
    returnajax($rs);
}

add_action('wp_ajax_nopriv_choseProvince', 'choseProvince');
add_action('wp_ajax_choseProvince', 'choseProvince');

function choseProvince() {
    // Danh sách các tỉnh theo quốc gia
    $countries = [
        'Vietnam' => [
            'Ha Noi',
            'SaiGon',
            'An Giang',
            'Ba Ria - Vung Tau',
            'Bac Giang',
            'Bac Kan',
            'Bac Lieu',
            'Bac Ninh',
            'Ben Tre',
            'Binh Dinh',
            'Binh Duong',
            'Binh Phuoc',
            'Binh Thuan',
            'Ca Mau',
            'Cao Bang',
            'Dak Lak',
            'Dak Nong',
            'Dien Bien',
            'Dong Nai',
            'Dong Thap',
            'Gia Lai',
            'Ha Giang',
            'Ha Nam',
            'Ha Tinh',
            'Hai Duong',
            'Hau Giang',
            'Hoa Binh',
            'Hung Yen',
            'Khanh Hoa',
            'Kien Giang',
            'Kon Tum',
            'Lai Chau',
            'Lam Dong',
            'Lang Son',
            'Lao Cai',
            'Long An',
            'Nam Dinh',
            'Nghe An',
            'Ninh Binh',
            'Ninh Thuan',
            'Phu Tho',
            'Quang Binh',
            'Quang Nam',
            'Quang Ngai',
            'Quang Ninh',
            'Quang Tri',
            'Soc Trang',
            'Son La',
            'Tay Ninh',
            'Thai Binh',
            'Thai Nguyen',
            'Thanh Hoa',
            'Thua Thien - Hue',
            'Tien Giang',
            'Tra Vinh',
            'Tuyen Quang',
            'Vinh Long',
            'Vinh Phuc',
            'Yen Bai',
        ],
        'United States' => [
            'Alabama',
            'Alaska',
            'Arizona',
            'Arkansas',
            'California',
            'Colorado',
            'Connecticut',
            'Delaware',
            'Florida',
            'Georgia',
            'Hawaii',
            'Idaho',
            'Illinois',
            'Indiana',
            'Iowa',
            'Kansas',
            'Kentucky',
            'Louisiana',
            'Maine',
            'Maryland',
            'Massachusetts',
            'Michigan',
            'Minnesota',
            'Mississippi',
            'Missouri',
            'Montana',
            'Nebraska',
            'Nevada',
            'New Hampshire',
            'New Jersey',
            'New Mexico',
            'New York',
            'North Carolina',
            'North Dakota',
            'Ohio',
            'Oklahoma',
            'Oregon',
            'Pennsylvania',
            'Rhode Island',
            'South Carolina',
            'South Dakota',
            'Tennessee',
            'Texas',
            'Utah',
            'Vermont',
            'Virginia',
            'Washington',
            'West Virginia',
            'Wisconsin',
            'Wyoming',
        ],
        'Canada' => [
            'Alberta',
            'British Columbia',
            'Manitoba',
            'New Brunswick',
            'Newfoundland and Labrador',
            'Northwest Territories',
            'Nova Scotia',
            'Nunavut',
            'Ontario',
            'Prince Edward Island',
            'Quebec',
            'Saskatchewan',
            'Yukon',
        ],
    ];

    // Lấy quốc gia từ yêu cầu AJAX
    $parent_id = isset($_POST['parent_id']) ? sanitize_text_field($_POST['parent_id']) : '';

    // Kiểm tra và trả về dữ liệu
    if (!empty($parent_id) && isset($countries[$parent_id])) {
        wp_send_json_success($countries[$parent_id]);
    } else {
        wp_send_json_error(['message' => 'No provinces found for the selected country.']);
    }
}
// Hàm xử lý tìm kiếm đơn hàng theo mã
function search_order_by_code_ajax() {
    global $wpdb;

    // Kiểm tra tham số `order_code`
    $order_code = isset($_POST['order_code']) ? sanitize_text_field($_POST['order_code']) : '';

    if (empty($order_code)) {
        wp_send_json_error(['message' => 'Order code is required.']);
        return;
    }

    // Query dữ liệu từ bảng wp_orders
    $results = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM wp_orders WHERE order_code LIKE %s",
        '%' . $wpdb->esc_like($order_code) . '%'
    ));

    if (empty($results)) {
        wp_send_json_error(['message' => 'No orders found.']);
        return;
    }

    // Chuẩn bị dữ liệu trả về
    $orders = [];
    foreach ($results as $order) {
        $orders[] = [
            'order_code' => $order->order_code,
            'date' => date('M d, Y', $order->time_order),
            'status' => get_order_status($order->status), // Hàm lấy trạng thái đơn hàng
            'status_class' => get_order_status_class($order->status), // Lấy class CSS theo trạng thái
        ];
    }

    wp_send_json_success(['orders' => $orders]);
}
add_action('wp_ajax_search_order_by_code', 'search_order_by_code_ajax');
add_action('wp_ajax_nopriv_search_order_by_code', 'search_order_by_code_ajax'); // Cho người dùng chưa đăng nhập

// Hàm phụ: Lấy tên trạng thái đơn hàng
function get_order_status($status) {
    $status_names = [
        1 => 'Processing',
        2 => 'Completed',
        3 => 'On The Road',
        4 => 'Canceled',
    ];
    return isset($status_names[$status]) ? $status_names[$status] : 'Unknown';
}

// Hàm phụ: Lấy class CSS theo trạng thái
function get_order_status_class($status) {
    $status_classes = [
        1 => 'warning',
        2 => 'success',
        3 => 'info',
        4 => 'error',
    ];
    return isset($status_classes[$status]) ? $status_classes[$status] : 'neutral';
}

function check_and_render_like_status($id_review, $id_user, $id_product) {
    global $wpdb;

    // Query to check if the user has liked the review
    $is_liked = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM liked_review WHERE id_review = %d AND id_user = %d",
        $id_review,
        $id_user
    ));

    // Query to count total likes for the review
    $like_count = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM liked_review WHERE id_review = %d",
        $id_review
    ));

    // Check if the user has liked
    $like_icon = $is_liked > 0 ? 'like-second.svg' : 'like-gray.svg';

    // Render HTML
    echo '<div class="flex items-center gap-1 click-like" data-review="' . $id_review . '" data-user="' . $id_user . '" data-product="' . $id_product . '">';
    echo '    <figure><img src="/html/assets/image/icon/' . $like_icon . '" alt=""></figure>';
    echo '    <p class="text-body-lg-regular text-neutral-500">' . $like_count . '</p>';
    echo '</div>';
}
add_action('wp_ajax_LikeReview', 'LikeReview');
add_action('wp_ajax_nopriv_LikeReview', 'LikeReview');

function LikeReview(){
    global $wpdb;

    $review = no_sql_injection(xss($_POST['review']));
    $user = no_sql_injection(xss($_POST['user']));
    $product = no_sql_injection(xss($_POST['product']));

    // Check if the user already liked the review
    $is_liked = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM liked_review WHERE id_review = %d AND id_user = %d",
        $review,
        $user
    ));

    if ($is_liked > 0) {
        // User already liked the review, remove the like
        $wpdb->delete('liked_review', [
            'id_review' => $review,
            'id_user' => $user
        ]);

        $status = 0; // Unlike status
    } else {
        // Add a like to the review
        $wpdb->insert('liked_review', [
            'id_review' => $review,
            'id_user' => $user,
            'id_product' => $product,
            'created_at' => current_time('mysql'),
            'update_at' => current_time('mysql')
        ]);

        $status = 1; // Like status
    }

    // Count total likes
    $like_count = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM liked_review WHERE id_review = %d",
        $review
    ));

    // Update the rating column in wp_review
    $wpdb->update(
        'wp_review',
        ['likes' => $like_count], // Update rating with like_count
        ['id' => $review], // Where condition
        ['%d'], // Data format
        ['%d']
    );

    // Prepare response HTML
    $like_icon = $status === 1 ? 'like-second.svg' : 'like-gray.svg';
    $html = '<div class="flex items-center gap-1 click-like" data-review="' . $review . '" data-user="' . $user . '" data-product="' . $product . '">';
    $html .= '    <figure><img src="/html/assets/image/icon/' . $like_icon . '" alt=""></figure>';
    $html .= '    <p class="text-body-lg-regular text-neutral-500">' . $like_count . '</p>';
    $html .= '</div>';

    // Send JSON response
    wp_send_json(["status" => $status, "html" => $html]);
}
function get_user_id($id)
{
    global $wpdb;
    $table = $wpdb->prefix . 'account_users';

    // Check
//    $user_token = sanitize_text_field($_COOKIE['user_token']);

    // Lay thong tin
    $user = $wpdb->get_row($wpdb->prepare(
        "SELECT email, first_name, last_name, avatar, addresses, provider, ID FROM $table WHERE ID = %s",
        $id
    ));

    return $user ? $user : false;
}
function languageArr()
{
    $arr_lang = [
        [
            'title' => 'Tiếng Anh',
            'link' => '',
            'number' => '',
            'lang' => 'en',
            'lang_datetime' => 'en',
            'active' => '',
            'name_lang' => 'EN',
            'name_eng' => 'English',
            'name_img' => 'lang',
        ],
        [
            'title' => 'Tiếng Việt',
            'link' => '/vie', // Đường dẫn cho tiếng Nga
            'number' => '',
            'lang' => 'vie',
            'lang_datetime' => 'vie',
            'active' => '',
            'name_lang' => 'VI',
            'name_eng' => 'vietnamese', // Tên ngôn ngữ hiển thị bằng tiếng Nga
            'name_img' => 'lang-VI',
        ],
        [
            'title' => 'Tiếng Tây Ban Nha',
            'link' => '/es', // Đường dẫn cho tiếng Trung
            'number' => '',
            'lang' => 'es',
            'lang_datetime' => 'es',
            'active' => '',
            'name_lang' => 'ES',
            'name_eng' => 'Español', // Tên ngôn ngữ hiển thị bằng tiếng Trung
            'name_img' => 'lang-ES',
        ]
    ];
//    print_r($arr_lang);die;
    foreach ($arr_lang as $key => $arr) {
        if (ICL_LANGUAGE_CODE == $arr['lang']) {
            $arr_lang[$key]['active'] = $arr['lang'];
        }
    }
    return $arr_lang;
}
function QueryPack() {
    // Lấy ID từ AJAX request
    $id = isset($_POST['id']) ? $_POST['id'] : null;

    if (!$id) {
        // Nếu không có ID, trả về lỗi
        wp_send_json_error(['message' => 'Invalid product ID']);
        return;
    }

    // Lấy dữ liệu sản phẩm dựa vào ID
    $args = array(
        'post_type'      => 'product',
        'post__in'       => [$id], // Đảm bảo $id là mảng
        'posts_per_page' => 1,
        'orderby'        => 'post__in', // Sắp xếp theo thứ tự trong cookie
    );

    $viewed_query = new WP_Query($args);

    // Nếu không tìm thấy sản phẩm, trả về lỗi
    if (!$viewed_query->have_posts()) {
        wp_send_json_error(['message' => 'Product not found']);
        return;
    }

    // Lấy sản phẩm
    $post_view = $viewed_query->posts[0];
    $qty = get_field('quantity', $post_view->ID); // Lấy quantity của sản phẩm
    $products = get_field('products_of_the_same_type', $post_view->ID); // Lấy sản phẩm cùng loại

    // Tạo HTML options cho <select>
    $html = '<option value="">Type: ' . esc_html($qty) . ' Pack</option>';

    if (!empty($products) && is_array($products)) {
        foreach ($products as $product) {
            $qty_op = get_field('quantity', $product->ID); // Lấy quantity của từng sản phẩm cùng loại
            $html .= '<option value="' . esc_attr($product->ID) . '">Type: ' . esc_html($qty_op) . ' Pack</option>';
        }
    }

    // Trả về JSON thành công với dữ liệu HTML
    wp_send_json_success($html);
}

// Đăng ký action cho AJAX
add_action('wp_ajax_QueryPack', 'QueryPack');
add_action('wp_ajax_nopriv_QueryPack', 'QueryPack'); // Nếu muốn cho phép truy cập khi không đăng nhập


function SelectPack() {
    // Lấy ID từ AJAX request
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

    // Kiểm tra ID có hợp lệ không
    if (!$id) {
        wp_send_json_error(['message' => 'Invalid product ID']);
        return;
    }

    // Query sản phẩm dựa vào ID
    $args = array(
        'post_type'      => 'product',      // Chỉ lấy từ post type 'product'
        'post__in'       => [$id],         // Tìm sản phẩm dựa trên ID
        'posts_per_page' => 1,             // Lấy tối đa 1 sản phẩm
        'orderby'        => 'post__in',    // Sắp xếp theo thứ tự ID
    );
    $query = new WP_Query($args);

    // Kiểm tra có sản phẩm nào không
    if (!$query->have_posts()) {
        wp_send_json_error(['message' => 'Product not found']);
        return;
    }

    // Duyệt qua sản phẩm để lấy dữ liệu
    $data = [];
    while ($query->have_posts()) {
        $query->the_post();

        // Lấy thông tin sản phẩm
        $price = get_post_meta(get_the_ID(), 'price', true); // Custom field 'price'
        $pack = get_post_meta(get_the_ID(), 'pack', true);   // Custom field 'pack'
        $img_id = get_post_thumbnail_id(get_the_ID());       // ID ảnh đại diện

        // Thêm vào mảng dữ liệu
        $data = [
            'id'    => get_the_ID(),
            'title' => get_the_title(),
            'price' => $price ?: '0',                              // Giá sản phẩm
            'pack'  => $pack ?: 'N/A',                             // Số lượng trong pack
            'link'  => get_permalink(),                           // Link sản phẩm
            'img'   => wp_get_attachment_url($img_id),            // URL ảnh đại diện
        ];
    }

    // Reset lại query
    wp_reset_postdata();

    // Trả JSON dữ liệu
    wp_send_json_success($data);
}

// Đăng ký AJAX handler
add_action('wp_ajax_SelectPack', 'SelectPack');
add_action('wp_ajax_nopriv_SelectPack', 'SelectPack');

function remover_select() {
    global $wpdb;

    // Check for valid request
    if (!isset($_POST['ids']) || !is_array($_POST['ids'])) {
        wp_send_json_error(['message' => 'Invalid request.']);
    }

    $ids = array_map('intval', $_POST['ids']);
    $table_name = $wpdb->prefix . 'voucher';

    // Delete vouchers from database
    $placeholders = implode(',', array_fill(0, count($ids), '%d'));
    $query = "DELETE FROM $table_name WHERE id IN ($placeholders)";

    // Prepare and execute the query
    $sql = $wpdb->prepare($query, ...$ids);
    $result = $wpdb->query($sql);

    if ($result !== false) {
        wp_send_json_success(['message' => 'Items deleted successfully.']);
    } else {
        wp_send_json_error(['message' => 'Failed to delete items.']);
    }
}
add_action('wp_ajax_remover_select', 'remover_select');
add_action('wp_ajax_nopriv_remover_select', 'remover_select');

?>