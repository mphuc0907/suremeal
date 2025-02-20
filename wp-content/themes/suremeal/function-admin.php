<?php
add_action('wp_ajax_add_points', 'add_points');
add_action('wp_ajax_nopriv_add_points', 'add_points');
function add_points() {
    global $wpdb;
    $name = $_POST['name'];
    $selectedValue = $_POST['selectedValue'];
    $status = $_POST['status'];
    $created_at_time = $_POST['created_at'];
    $created_at = strtotime($created_at_time);
    $id_value = $_POST['id_value'];
    $points = $_POST['points'];
    $expiration_date_time = $_POST['expiration_date'];
    $expiration_date = strtotime($expiration_date_time);
    $note = $_POST['note'];
    $purchases = $_POST['purchases'];
    if ($selectedValue == "product") {
        $type = 0;
    }elseif ($selectedValue == "voucher") {
        $type = 1;
    }elseif ($selectedValue == "gift") {
        $type = 2;
    }
    $url_image = '';
    //upload file
    if (!function_exists('wp_handle_upload')) {
        require_once(ABSPATH . 'wp-admin/includes/file.php');
    }
    if (isset($_FILES['fileInput']) && !empty($_FILES['fileInput']['name'])) {
        $uploaded_file = $_FILES['fileInput'];

        // Cấu hình upload
        $upload_overrides = array('test_form' => false);

        // Xử lý upload file
        $movefile = wp_handle_upload($uploaded_file, $upload_overrides);
        $url_image = $movefile['url'];
        if ($movefile && !isset($movefile['error'])) {
            // Nếu upload thành công
//            wp_send_json_success(array(
//                'message' => 'File uploaded successfully!',
//                'url' => $movefile['url']  // Đường dẫn file đã upload
//            ));
        } else {
            // Nếu upload thất bại
            wp_send_json_error(array(
                'message' => $movefile['error']
            ));
        }
    } else {
        wp_send_json_error(array(
            'message' => 'No file uploaded.'
        ));
    }
    $data = array(
        'type' => $type,
        'name' => $name,
        'note' => $note,
        'point' => $points,
        'image' => $url_image,
        'id_value' => $id_value,
        'created_at' => $created_at,
        'expiration_date' => $expiration_date,
        'purchases' => $purchases,
        'status' => $status,
    );
    if ($wpdb->insert('product_points', $data)) {
        $rs['status'] = 1;
        $rs['message'] = 'Product updated successfully';

    } else {
        $rs['status'] = 0;
        $rs['message'] = 'Product update failed!';
    }
    returnajax($rs);
}
add_action('wp_ajax_edit_points', 'edit_points');
add_action('wp_ajax_nopriv_edit_points', 'edit_points');
function edit_points() {
    global $wpdb;
    $name = $_POST['name'];
    $id = $_POST['id'];
    $selectedValue = $_POST['selectedValue'];
    $status = $_POST['status'];
    $created_at_time = $_POST['created_at'];
    $created_at = strtotime($created_at_time);
    $id_value = $_POST['id_value'];
    $points = $_POST['points'];
    $expiration_date_time = $_POST['expiration_date'];
    $expiration_date = strtotime($expiration_date_time);
    $note = $_POST['note'];
    $purchases = $_POST['purchases'];
    if ($selectedValue == "product") {
        $type = 0;
    }elseif ($selectedValue == "voucher") {
        $type = 1;
    }elseif ($selectedValue == "gift") {
        $type = 2;
    }
    $url_image = '';
    //upload file
    if (!function_exists('wp_handle_upload')) {
        require_once(ABSPATH . 'wp-admin/includes/file.php');
    }
    if (isset($_FILES['fileInput']) && !empty($_FILES['fileInput']['name'])) {
        $uploaded_file = $_FILES['fileInput'];

        // Cấu hình upload
        $upload_overrides = array('test_form' => false);

        // Xử lý upload file
        $movefile = wp_handle_upload($uploaded_file, $upload_overrides);
        $url_image = $movefile['url'];
        if ($movefile && !isset($movefile['error'])) {
            // Nếu upload thành công
//            wp_send_json_success(array(
//                'message' => 'File uploaded successfully!',
//                'url' => $movefile['url']  // Đường dẫn file đã upload
//            ));
        } else {
            // Nếu upload thất bại
            wp_send_json_error(array(
                'message' => $movefile['error']
            ));
        }
    } else {
        wp_send_json_error(array(
            'message' => 'No file uploaded.'
        ));
    }
    $data = array(
        'type' => $type,
        'name' => $name,
        'note' => $note,
        'point' => $points,
        'image' => $url_image,
        'id_value' => $id_value,
        'created_at' => $created_at,
        'expiration_date' => $expiration_date,
        'purchases' => $purchases,
        'status' => $status,
    );
    $where = array('id' => $id);
    if ($wpdb->update('product_points', $data, $where)) {
        $rs['status'] = 1;
        $rs['message'] = 'Product updated successfully';

    } else {
        $rs['status'] = 0;
        $rs['message'] = 'Product update failed!';
    }
    returnajax($rs);
}
function AddTransaction() {
    $current_user = wp_get_current_user();

    // Lấy dữ liệu từ form
    $id_user = $_POST['id_user'];
    $withdrawal_amount = $_POST['withdrawal_amount'];
    $textValue = $_POST['action_user'];
    if ($withdrawal_amount <= 0 ){
        $rs['status'] = 0;
        $rs['mess'] = 'The user does not have enough money to withdraw!';
        returnajax($rs);
    }
    global $wpdb;
    $time = current_time('mysql'); // Lấy thời gian hiện tại theo WordPress

    // Xử lý ảnh upload
    $upload_dir = wp_upload_dir(); // Lấy đường dẫn thư mục upload của WP
    $custom_upload_dir = $upload_dir['basedir'] . '/upload_image/'; // Thư mục custom

    // Tạo thư mục nếu chưa có
    if (!file_exists($custom_upload_dir)) {
        wp_mkdir_p($custom_upload_dir);
    }

    $transaction_img_url = ''; // Mặc định là rỗng nếu không có ảnh

    if (!empty($_FILES['transaction_img']['name'])) {
        $file = $_FILES['transaction_img'];

        // Lấy phần mở rộng của file
        $file_ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif']; // Chỉ cho phép một số định dạng

        if (in_array(strtolower($file_ext), $allowed_ext)) {
            $new_file_name = time() . '-' . sanitize_file_name($file['name']); // Tạo tên file duy nhất
            $file_path = $custom_upload_dir . $new_file_name;

            $file_url = $upload_dir['baseurl'] . '/upload_image/' . $new_file_name; // Đường dẫn URL

            if (move_uploaded_file($file['tmp_name'], $file_path)) {
                $transaction_img_url = $file_url; // Lưu đường dẫn vào DB nếu upload thành công
            } else {
                $rs['status'] = 0;
                $rs['mess'] = 'Image upload failed. Please try again!';
                returnajax($rs);
            }
        } else {
            $rs['status'] = 0;
            $rs['mess'] = 'Invalid image format! Only JPG, JPEG, PNG, GIF are allowed.';
            returnajax($rs);
        }
    }

    // Chuẩn bị dữ liệu để lưu vào DB
    $data = [
        'id_user' => $id_user,
        'withdrawal_amount' => $withdrawal_amount,
        'name_admin' => $current_user->user_login,
        'transaction_img' => $transaction_img_url, // Lưu URL thay vì tên file
        'action_user' => $textValue,
        'created_at' => $time,
        'status' => 0,
        'updated_at' => $time,
    ];

    // Chèn dữ liệu vào bảng wp_review
    $table_name = 'log_withdrawal';
    $inserted = $wpdb->insert($table_name, $data);

    if ($inserted) {
        $rs['status'] = 1;
        $rs['mess'] = 'Log submitted for approval!';
    } else {
        $rs['status'] = 0;
        $rs['mess'] = 'Log upload failed. Please try again!';
    }

    returnajax($rs);
}

// Đăng ký Ajax handler cho cả người dùng đã đăng nhập và chưa đăng nhập
add_action('wp_ajax_AddTransaction', 'AddTransaction');
add_action('wp_ajax_nopriv_AddTransaction', 'AddTransaction');

add_action('wp_ajax_getCommission', 'getCommissionCallback');
add_action('wp_ajax_nopriv_getCommission', 'getCommissionCallback'); // Cho phép người dùng chưa đăng nhập gọi AJAX nếu cần

function getCommissionCallback() {
    if (!isset($_POST['id_user'])) {
        wp_send_json_error(['message' => 'Thiếu id_user']);
    }

    global $wpdb;
    $id_user = intval($_POST['id_user']);

    $curron_aff = $wpdb->get_row($wpdb->prepare("SELECT * FROM wp_affiliate WHERE id_user = %s", $id_user));
    if ($curron_aff) {
        $distribution_code = $curron_aff->distribution_code;
        $percent = $curron_aff->percent;
    }else {
        exit();
    }

    $myrows = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT * FROM wp_orders WHERE distribution_code = %s AND status != 4",
            $distribution_code
        )
    );
    $revenue = 0;
    $result = 0;
    $log = $wpdb->get_results("SELECT * FROM log_withdrawal WHERE id_user = $id_user");
    $total_withdrawal = 0;


    foreach ($myrows as $key => $value) {
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
    foreach ($log as $row) {
        $total_withdrawal += $row->withdrawal_amount;
    }

    $total_withdrawal = $result - $total_withdrawal;
    // Trả về JSON
    wp_send_json_success([
        'withdrawal_amount' => $total_withdrawal ? $total_withdrawal : 0
    ]);
}
function format_date($date) {
    $decoded_date = urldecode($date);
    $date_parts = explode('/', $decoded_date);
//        print_r($date_parts);die();
    if (count($date_parts) === 3) {

            return "{$date_parts[2]}-{$date_parts[1]}-{$date_parts[0]}"; // YYYY-MM-DD
    }
    return ''; // Trả về chuỗi rỗng nếu không hợp lệ
}


add_action('wp_ajax_deleteUser', 'deleteUser');
add_action('wp_ajax_nopriv_deleteUser', 'deleteUser'); // Cho phép người dùng chưa đăng nhập gọi AJAX nếu cần

function deleteUser() {
    if (!isset($_POST['id_user'])) {
        wp_send_json_error(['message' => 'Thiếu id_user']);
    }

    global $wpdb;
    $id_user = intval($_POST['id_user']);

    $user = $wpdb->get_row($wpdb->prepare("SELECT * FROM wp_account_users WHERE id = %s", $id_user));

    print_r($user);die();
}
?>