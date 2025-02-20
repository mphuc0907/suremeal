<?php /* Template Name: Person-Info */ ?>
<?php

// Kiểm tra đăng nhập
$authenticated_user = validate_user_token();
if (!isset($_COOKIE['user_token']) || !$authenticated_user) {
    wp_redirect(home_url());
    exit;
}

// Kiểm tra email tồn tại
function check_email_exists($email) {
    global $wpdb; 
    $table_name = $wpdb->prefix . 'account_users';
    
    $result = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $table_name WHERE email = %s",
        $email
    ));
    
    return $result > 0;
 }

global $wpdb;
$table_name = $wpdb->prefix . 'account_users';

// Lấy thông tin người dùng
$user_email = $authenticated_user->email;
$user_phone = $authenticated_user->phone_number;
$first_name = $authenticated_user->first_name;
$last_name = $authenticated_user->last_name;
$avatar = $authenticated_user->avatar;
$addresses = json_decode($authenticated_user->addresses, true) ?: [];
$provider = $authenticated_user->provider;

$password_is_null = $authenticated_user->password == '';

// Xử lý cập nhật thông tin khi submit form
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    // Khởi tạo mảng lỗi
    $errors = [];

    // Validate names - at least one must be filled
    $first_name = sanitize_text_field($_POST['first_name']);
    $last_name = sanitize_text_field($_POST['last_name']);
    $user_phone = sanitize_text_field($_POST['phone_number']);

    if (empty($first_name) && empty($last_name)) {
        $errors['name'] = 'At least one name field must be filled';
    }
    if (empty($user_phone)) {
        $errors['phone_number'] = 'At least one phone field must be filled';
    }

    // Xử lý email (nếu chưa có)
    $user_email = $authenticated_user->email;
    $new_email = sanitize_email($_POST['email']);
    if (empty($user_email)) {
        if (empty($new_email)) {
            $errors['email'] = 'Email is required';
        } elseif (!is_email($new_email)) {
            $errors['email'] = 'Invalid email format';
        } elseif (check_email_exists($new_email)) {
            $errors['email'] = 'This email is already registered';
        } else {
            $user_email = $new_email;
        }
    }

    // Xử lý địa chỉ
    $addresses = isset($_POST['addresses']) ? array_filter(array_map('sanitize_text_field', $_POST['addresses'])) : [];

    // Xử lý avatar
    require_once(ABSPATH . 'wp-admin/includes/media.php');
    require_once(ABSPATH . 'wp-admin/includes/file.php');
    require_once(ABSPATH . 'wp-admin/includes/image.php');

    // Xử lý avatar (giữ nguyên như cũ)
    $avatar = $authenticated_user->avatar;
    if (!empty($_FILES['avatar']['name'])) {
        // Kiểm tra loại file
        $allowed_types = array('image/jpeg', 'image/png', 'image/gif');
        if (!in_array($_FILES['avatar']['type'], $allowed_types)) {
            $errors['avatar'] = 'Invalid file type. Only JPG, PNG and GIF are allowed.';
        }

        // Kiểm tra kích thước file
        $max_file_size = 5 * 1024 * 1024; // 5MB
        if ($_FILES['avatar']['size'] > $max_file_size) {
            $errors['avatar'] = 'File is too large. Maximum size is 5MB.';
        }

        // Sử dụng WordPress upload handler
        $upload = wp_handle_upload($_FILES['avatar'], array('test_form' => false));

        if (!isset($upload['error'])) {
            $avatar = $upload['url'];

            // Lưu ID của attachment để có thể xóa ảnh cũ sau này nếu cần
            $attachment_id = wp_insert_attachment(array(
                'guid' => $upload['url'],
                'post_mime_type' => $upload['type'],
                'post_title' => basename($upload['file']),
                'post_content' => '',
                'post_status' => 'inherit'
            ), $upload['file']);
        } else {
            $errors['avatar'] = $upload['error'];
        }
    }

    // Xử lý đổi/tạo mật khẩu
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Biến để kiểm tra xem có thực hiện validate mật khẩu không
    $password_change_attempted = !empty($current_password) || !empty($new_password) || !empty($confirm_password);

    // Nếu có thực hiện thay đổi mật khẩu
    if ($password_change_attempted) {
        // Nếu tài khoản chưa có mật khẩu (null)
        if ($password_is_null) {
            // Kiểm tra điền đủ new password và confirm password
            if (empty($new_password) || empty($confirm_password)) {
                $errors['password_fields'] = 'Please fill in all password fields';
            } else {
                // Validate mật khẩu mới
                if (strlen($new_password) < 6) {
                    $errors['new_password'] = 'Password must be at least 6 characters';
                }

                // Validate xác nhận mật khẩu
                if ($new_password !== $confirm_password) {
                    $errors['confirm_password'] = 'Passwords do not match';
                }
            }
        } 
        // Nếu tài khoản đã có mật khẩu 
        else {
            // Kiểm tra điền đủ cả 3 trường mật khẩu
            if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
                $errors['password_fields'] = 'Please fill in all password fields';
            } else {
                // Kiểm tra mật khẩu hiện tại
                if (!password_verify($current_password, $authenticated_user->password)) {
                    $errors['current_password'] = 'Current password is incorrect';
                }

                // Validate mật khẩu mới
                if (strlen($new_password) < 6) {
                    $errors['new_password'] = 'Password must be at least 6 characters';
                }

                // Validate xác nhận mật khẩu
                if ($new_password !== $confirm_password) {
                    $errors['confirm_password'] = 'Passwords do not match';
                }
            }
        }
    }

    // Nếu không có lỗi, thực hiện cập nhật
    if (empty($errors)) {
        $update_data = [
            'first_name' => $first_name,
            'last_name' => $last_name,
            'phone_number' => $user_phone,
            'addresses' => json_encode($addresses)
//            'avatar' => $avatar
        ];

        // Cập nhật email nếu chưa có
        if (empty($authenticated_user->email)) {
            $update_data['email'] = $user_email;
        }

        // Xử lý mật khẩu (chỉ khi có thay đổi)
        if ($password_change_attempted && empty($errors)) {
            $update_data['password'] = password_hash($new_password, PASSWORD_DEFAULT);
        }

        // Thực hiện cập nhật
        $result = $wpdb->update(
            $table_name,
            $update_data,
            ['token' => $_COOKIE['user_token']]
        );

        if ($result !== false) {
            // Redirect with success message
            wp_redirect(add_query_arg('update', 'success', get_permalink()));
            exit;
        }
    }
}

$url = get_template_directory_uri();
get_header();
?>
<main class="bg-[#EEF0F6]">
    <section class="py-6">
        <div class="container">
            <nav class="breadcrumb text-body-md-medium text-gray-8" aria-label="breadcrumb">
                <!-- Sử dụng schema.org để định nghĩa breadcrumb list -->
                <ol class="flex flex-wrap gap-3 items-center" itemscope
                    itemtype="https://schema.org/BreadcrumbList">
                    <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                        <a href="<?= home_url() ?>" class="text-secondary hover:text-primary" itemprop="item">
                            <span itemprop="name"><?php pll_e('Home') ?></span>
                        </a>
                        <meta itemprop="position" content="1" />
                    </li>
                    <span>/</span>
                    <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem"
                        aria-current="page">
                        <span itemprop="name"><?php pll_e('Personal') ?></span>
                        <meta itemprop="position" content="4" />
                    </li>
                </ol>
            </nav>
        </div>
    </section>

    <section class="pb-20">
        <div class="container">
            <div class="flex flex-col lg:flex-row gap-6">
                <div class="w-full lg:max-w-[322px] flex flex-col gap-5">
                    <!-- profile card -->
                    <div  class="h-[200px] bg-secondary rounded-xl flex flex-col items-center justify-center gap-4">
                        <figure class="w-20 h-20 rounded-full overflow-hidden">
                            <img src="<?= $url . '/assets/image/dashboard/avatar-80.svg' ?>" alt="avatar">
                        </figure>
                        <div class="flex flex-col items-center justify-center">
                            <h2 class="text-body-md-semibold text-white"><?= $first_name ?> <?= $last_name ?></h2>
                            <p class="text-body-sm-regular text-white"><?= $user_email ?>
                            </p>
                        </div>
                    </div>

                    <!-- nav-active -->
                    <div class="dashboard-container">
                        <a href="<?= home_url() ?>/person-info" class="dashboard-item active">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M8.25 9C8.25 6.92893 9.92893 5.25 12 5.25C14.0711 5.25 15.75 6.92893 15.75 9C15.75 11.0711 14.0711 12.75 12 12.75C9.92893 12.75 8.25 11.0711 8.25 9ZM12 6.75C10.7574 6.75 9.75 7.75736 9.75 9C9.75 10.2426 10.7574 11.25 12 11.25C13.2426 11.25 14.25 10.2426 14.25 9C14.25 7.75736 13.2426 6.75 12 6.75Z"
                                    fill="#0E74BC" />
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M1.25 12C1.25 6.06294 6.06294 1.25 12 1.25C17.9371 1.25 22.75 6.06294 22.75 12C22.75 17.9371 17.9371 22.75 12 22.75C6.06294 22.75 1.25 17.9371 1.25 12ZM12 2.75C6.89137 2.75 2.75 6.89137 2.75 12C2.75 14.5456 3.77827 16.851 5.4421 18.5235C5.6225 17.5504 5.97694 16.6329 6.68837 15.8951C7.75252 14.7915 9.45416 14.25 12 14.25C14.5457 14.25 16.2474 14.7915 17.3115 15.8951C18.023 16.6329 18.3774 17.5505 18.5578 18.5236C20.2217 16.8511 21.25 14.5456 21.25 12C21.25 6.89137 17.1086 2.75 12 2.75ZM17.1937 19.6554C17.0918 18.4435 16.8286 17.5553 16.2318 16.9363C15.5823 16.2628 14.3789 15.75 12 15.75C9.62099 15.75 8.41761 16.2628 7.76815 16.9363C7.17127 17.5553 6.90811 18.4434 6.80622 19.6553C8.28684 20.6618 10.0747 21.25 12 21.25C13.9252 21.25 15.7131 20.6618 17.1937 19.6554Z"
                                    fill="#0E74BC" />
                            </svg>
                            <p class="text-body-md-medium"><?php pll_e('Personal information') ?></p>
                        </a>
                        <a href="<?= home_url() ?>/order-info" class="dashboard-item">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none">
                                <path
                                    d="M8.75039 13C8.75039 12.5858 8.4146 12.25 8.00039 12.25C7.58618 12.25 7.25039 12.5858 7.25039 13V17C7.25039 17.4142 7.58618 17.75 8.00039 17.75C8.4146 17.75 8.75039 17.4142 8.75039 17V13Z"
                                    fill="#373A51" />
                                <path
                                    d="M16.0004 12.25C16.4146 12.25 16.7504 12.5858 16.7504 13V17C16.7504 17.4142 16.4146 17.75 16.0004 17.75C15.5862 17.75 15.2504 17.4142 15.2504 17V13C15.2504 12.5858 15.5862 12.25 16.0004 12.25Z"
                                    fill="#373A51" />
                                <path
                                    d="M12.7504 13C12.7504 12.5858 12.4146 12.25 12.0004 12.25C11.5862 12.25 11.2504 12.5858 11.2504 13V17C11.2504 17.4142 11.5862 17.75 12.0004 17.75C12.4146 17.75 12.7504 17.4142 12.7504 17V13Z"
                                    fill="#373A51" />
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M17.2742 3.47298C16.7986 3.28712 16.2659 3.25574 15.5823 3.25077C15.3016 2.65912 14.6988 2.25 14.0004 2.25H10.0004C9.30199 2.25 8.69914 2.65912 8.41843 3.25077C7.73489 3.25574 7.20215 3.28712 6.7266 3.47298C6.15841 3.69505 5.6642 4.07255 5.30048 4.5623C4.93355 5.05639 4.76131 5.68968 4.52423 6.56133L3.89636 8.86426C3.50885 9.06269 3.16977 9.32992 2.88691 9.6922C2.26491 10.4888 2.15476 11.4377 2.26541 12.5261C2.37278 13.5822 2.70527 14.9121 3.12149 16.5769L3.14789 16.6825C3.41107 17.7353 3.62475 18.5901 3.87889 19.2572C4.14386 19.9527 4.48026 20.5227 5.03488 20.9558C5.5895 21.3888 6.22414 21.5769 6.96315 21.6653C7.67197 21.75 8.55305 21.75 9.63823 21.75H14.3625C15.4476 21.75 16.3287 21.75 17.0375 21.6653C17.7766 21.5769 18.4112 21.3888 18.9658 20.9558C19.5204 20.5227 19.8568 19.9527 20.1218 19.2572C20.3759 18.5901 20.5896 17.7353 20.8528 16.6825L20.8792 16.577C21.2954 14.9122 21.6279 13.5822 21.7353 12.5261C21.8459 11.4377 21.7358 10.4888 21.1138 9.6922C20.8309 9.32995 20.4919 9.06274 20.1044 8.86431L19.4765 6.56133C19.2395 5.68968 19.0672 5.05639 18.7003 4.5623C18.3366 4.07255 17.8424 3.69505 17.2742 3.47298ZM7.27263 4.87007C7.49243 4.78416 7.75801 4.75888 8.41984 4.75219C8.70115 5.34225 9.30315 5.75 10.0004 5.75H14.0004C14.6976 5.75 15.2996 5.34225 15.5809 4.75219C16.2428 4.75888 16.5084 4.78416 16.7282 4.87007C17.0341 4.98964 17.3002 5.19291 17.4961 5.45663C17.6722 5.69377 17.7755 6.02508 18.0664 7.09194L18.42 8.3887C17.3822 8.24996 16.0424 8.24998 14.3777 8.25H9.62295C7.95837 8.24998 6.61858 8.24996 5.58079 8.38868L5.93437 7.09195C6.22527 6.02508 6.32861 5.69376 6.50472 5.45662C6.70057 5.19291 6.96668 4.98964 7.27263 4.87007ZM10.0004 3.75C9.86232 3.75 9.75039 3.86193 9.75039 4C9.75039 4.13807 9.86232 4.25 10.0004 4.25H14.0004C14.1385 4.25 14.2504 4.13807 14.2504 4C14.2504 3.86193 14.1385 3.75 14.0004 3.75H10.0004ZM4.06921 10.6153C4.34805 10.2582 4.78902 10.0183 5.70019 9.88649C6.63083 9.75187 7.89266 9.75 9.68501 9.75H14.3157C16.108 9.75 17.3699 9.75187 18.3005 9.88649C19.2117 10.0183 19.6526 10.2582 19.9315 10.6153C20.2103 10.9725 20.3361 11.4584 20.243 12.3744C20.1479 13.3099 19.8436 14.5345 19.4089 16.2733C19.1317 17.3824 18.9386 18.1496 18.7201 18.7231C18.5088 19.2778 18.3019 19.5711 18.0427 19.7735C17.7835 19.9758 17.4488 20.1054 16.8594 20.1759C16.2501 20.2488 15.4589 20.25 14.3157 20.25H9.68501C8.54182 20.25 7.75064 20.2488 7.14125 20.1759C6.55188 20.1054 6.21716 19.9758 5.958 19.7735C5.69884 19.5711 5.49193 19.2778 5.28061 18.7231C5.06212 18.1496 4.86902 17.3824 4.59176 16.2733C4.15705 14.5345 3.85282 13.3099 3.75772 12.3744C3.6646 11.4584 3.79038 10.9725 4.06921 10.6153Z"
                                    fill="#373A51" />
                            </svg>
                            <p class="text-body-md-medium"><?php pll_e('Order infomation') ?></p>
                        </a>
                        <form class="dashboard-item" id="logoutForm" action="<?php echo esc_url(admin_url('admin-ajax.php')); ?>" method="POST">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none">
                                <path
                                    d="M18.5303 12.5303C18.8232 12.2374 18.8232 11.7626 18.5303 11.4697L16.5303 9.46967C16.2374 9.17678 15.7626 9.17678 15.4697 9.46967C15.1768 9.76256 15.1768 10.2374 15.4697 10.5303L16.1893 11.25L9 11.25C8.58578 11.25 8.25 11.5858 8.25 12C8.25 12.4142 8.58578 12.75 9 12.75L16.1893 12.75L15.4697 13.4697C15.1768 13.7626 15.1768 14.2374 15.4697 14.5303C15.7626 14.8232 16.2374 14.8232 16.5303 14.5303L18.5303 12.5303Z"
                                    fill="#373A51" />
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M10.0547 1.25H8.94491C7.57732 1.24998 6.475 1.24996 5.60803 1.36652C4.70792 1.48754 3.95005 1.74643 3.34813 2.34835C2.74622 2.95027 2.48732 3.70814 2.36631 4.60825C2.24974 5.47522 2.24976 6.57754 2.24978 7.94513V16.0549C2.24976 17.4225 2.24974 18.5248 2.36631 19.3918C2.48732 20.2919 2.74622 21.0497 3.34813 21.6517C3.95005 22.2536 4.70792 22.5125 5.60803 22.6335C6.475 22.75 7.57732 22.75 8.94491 22.75H10.0547C11.4222 22.75 12.5246 22.75 13.3915 22.6335C14.2916 22.5125 15.0495 22.2536 15.6514 21.6517C16.0504 21.2527 16.2993 20.7844 16.457 20.2498C17.4083 20.2486 18.2009 20.2381 18.8431 20.1518C19.6071 20.0491 20.2694 19.8268 20.7981 19.2981C21.3268 18.7694 21.5491 18.1071 21.6518 17.3431C21.75 16.6123 21.75 15.6865 21.75 14.5537V9.44631C21.75 8.31349 21.75 7.38774 21.6518 6.65689C21.5491 5.89294 21.3268 5.2306 20.7981 4.7019C20.2694 4.17321 19.6071 3.95093 18.8431 3.84822C18.2009 3.76188 17.4083 3.75142 16.457 3.75017C16.2993 3.21562 16.0504 2.74729 15.6514 2.34835C15.0495 1.74643 14.2916 1.48754 13.3915 1.36652C12.5246 1.24996 11.4222 1.24998 10.0547 1.25ZM16.748 17.0042C16.7444 17.6487 16.7338 18.2293 16.6972 18.7491C17.5316 18.7459 18.152 18.7312 18.6432 18.6652C19.2409 18.5848 19.5339 18.441 19.7374 18.2374C19.941 18.0339 20.0848 17.7409 20.1652 17.1432C20.2484 16.5241 20.25 15.6997 20.25 14.5V9.5C20.25 8.30029 20.2484 7.47595 20.1652 6.85676C20.0848 6.25914 19.941 5.9661 19.7374 5.76256C19.5339 5.55902 19.2409 5.41519 18.6432 5.33484C18.152 5.2688 17.5316 5.25415 16.6972 5.25091C16.7338 5.77073 16.7444 6.35129 16.748 6.99583C16.7503 7.41003 16.4164 7.74768 16.0022 7.74999C15.588 7.7523 15.2504 7.41838 15.2481 7.00418C15.242 5.91068 15.2136 5.1356 15.1055 4.54735C15.0014 3.98054 14.8342 3.65246 14.5908 3.40901C14.314 3.13225 13.9254 2.9518 13.1917 2.85315C12.4363 2.75159 11.4352 2.75 9.99978 2.75H8.99978C7.56437 2.75 6.56325 2.75159 5.8079 2.85315C5.07413 2.9518 4.68555 3.13225 4.40879 3.40901C4.13203 3.68577 3.95158 4.07435 3.85293 4.80812C3.75138 5.56347 3.74978 6.56459 3.74978 8V16C3.74978 17.4354 3.75138 18.4365 3.85293 19.1919C3.95158 19.9257 4.13203 20.3142 4.40879 20.591C4.68555 20.8678 5.07413 21.0482 5.8079 21.1469C6.56325 21.2484 7.56437 21.25 8.99978 21.25H9.99978C11.4352 21.25 12.4363 21.2484 13.1917 21.1469C13.9254 21.0482 14.314 20.8678 14.5908 20.591C14.8342 20.3475 15.0014 20.0195 15.1055 19.4527C15.2136 18.8644 15.242 18.0893 15.2481 16.9958C15.2504 16.5816 15.588 16.2477 16.0022 16.25C16.4164 16.2523 16.7503 16.59 16.748 17.0042Z"
                                    fill="#373A51" />
                            </svg>
                            <input type="hidden" name="action" value="logout">
                            <button type="submit" class="text-body-md-medium logout"><?php pll_e('Log out') ?></button>
                        </form>
                    </div>
                </div>
                <div class="flex-1">
                    <div class="rounded-xl bg-white pb-10 flex flex-col gap-12">
                        <div class="px-8 py-4 border-b border-solid border-neutral-200">
                            <h2 class="text-body-xl-regular text-gray-8"><?php pll_e('Personal information') ?></h2>
                        </div>

                        <div class="info-wrap flex items-center justify-center">
                            <!-- thông tin - xem -->
                            <div
                                class="view-info w-full max-w-[455px] px-4 lg:px-0 flex flex-col items-center justify-center gap-8">
<!--                                <figure class="w-20 h-20 rounded-full overflow-hidden"><img-->
<!--                                        src="--><?//= $url . '/assets/image/dashboard/avatar-80.svg' ?><!--" alt="avatar"></figure>-->
                                <div class="w-full">
                                    <div
                                        class="name-info w-full flex py-4 border-b border-solid border-neutral-200">
                                        <p class="w-1/5 whitespace-nowrap text-body-md-regular text-gray-8">
                                            <?php pll_e('Full name') ?>
                                        </p>
                                        <p class="w-4/5 pl-8 text-end text-body-md-medium text-gray-8"
                                            id="view-name">
                                            <?= $first_name ?> <?= $last_name ?>
                                        </p>
                                    </div>
                                    <div
                                        class="email-info w-full flex py-4 border-b border-solid border-neutral-200">
                                        <p class="w-1/5 whitespace-nowrap text-body-md-regular text-gray-8">
                                            <?php pll_e('Email') ?>
                                        </p>
                                        <p class="w-4/5 pl-8 text-end text-body-md-medium text-gray-8"
                                            id="view-email">
                                            <?= $user_email ?></p>
                                    </div>
                                    <div
                                            class="email-info w-full flex py-4 border-b border-solid border-neutral-200">
                                        <p class="w-1/5 whitespace-nowrap text-body-md-regular text-gray-8">
                                            <?php pll_e('Phone number') ?>
                                        </p>
                                        <p class="w-4/5 pl-8 text-end text-body-md-medium text-gray-8"
                                           id="view-email">
                                            <?= $user_phone ?></p>
                                    </div>
                                    <div class="address-wrap w-full flex flex-col">
                                        <?php
                                        // Kiểm tra và decode addresses
                                        $stored_addresses = is_string($authenticated_user->addresses)
                                            ? json_decode($authenticated_user->addresses, true)
                                            : (is_array($authenticated_user->addresses)
                                                ? $authenticated_user->addresses
                                                : []);

                                        $stored_addresses = $stored_addresses ?: [];

                                        // Kiểm tra và render địa chỉ
                                        if (!empty($stored_addresses)) :
                                            foreach ($stored_addresses as $index => $address) :
                                        ?>
                                                <div class="address-info w-full flex py-4 border-b border-solid border-neutral-200">
                                                    <p class="w-1/5 whitespace-nowrap text-body-md-regular text-gray-8">
                                                        <?php pll_e('Address') ?> (<?= $index + 1 ?>)
                                                    </p>
                                                    <p class="w-4/5 pl-8 text-end text-body-md-medium text-gray-8">
                                                        <?= htmlspecialchars($address) ?>
                                                    </p>
                                                </div>
                                            <?php endforeach ?>
                                        <?php endif ?>
                                    </div>
                                    <div
                                        class="password-info w-full flex py-4 border-b border-solid border-neutral-200">
                                        <p class="w-1/5 whitespace-nowrap text-body-md-regular text-gray-8">
                                            <?php pll_e('Password') ?>
                                        </p>
                                        <input type="password" id="view-password"
                                            class="input-trans w-4/5 pl-8 text-end text-body-md-medium text-gray-8"
                                            value="123456" disabled>
                                    </div>
                                </div>
                                <button id="change-info"
                                    class="change-info w-full max-w-[281px] button bg-primary text-body-md-semibold text-white"><?php pll_e('Change information') ?></button>
                            </div>
                            <!-- thông tin - edit -->
                            <form method="POST" enctype="multipart/form-data" class="form-edit">
                                <input type="hidden" name="update_profile" value="1">
                                <div class="edit-info hidden w-full max-w-[455px] px-4 lg:px-0 flex flex-col items-center justify-center gap-8">
<!--                                    <div class="avatar-edit relative w-20 h-20">-->
<!--                                        <input type="file" name="avatar" id="profile-image" accept="image/*" class="hidden" />-->
<!--                                        <label for="profile-image"-->
<!--                                            class="cursor-pointer relative block w-full h-full rounded-full overflow-hidden">-->
<!--                                             Avatar -->
<!--                                            <figure class="w-full h-full">-->
<!--                                                <img src="--><?//= $avatar ? $avatar : $url . '/assets/image/dashboard/avatar-80.svg' ?><!--" alt="avatar"-->
<!--                                                    class="w-full h-full object-cover" />-->
<!--                                            </figure>-->
<!--                                            Overlay với icon camera -->
<!--                                            <div-->
<!--                                                class="absolute inset-0 bg-[#0000008a] flex justify-center items-center opacity-0 hover:opacity-100 transition-opacity">-->
<!--                                                <figure>-->
<!--                                                    <img src="--><?//= $url ?><!--/assets/image/icon/camera.svg" alt="icon"-->
<!--                                                        class="w-6 h-6" />-->
<!--                                                </figure>-->
<!--                                            </div>-->
<!--                                        </label>-->
<!--                                    </div>-->

                                    <div class="w-full flex flex-col gap-5">
                                        <div class="flex flex-col md:flex-row gap-4 w-full">
                                            <label class="fullname-edit input-label">
                                                <p class="input-title"><?php pll_e('First name') ?></p>
                                                <input type="text" name="first_name" class="input-field" value="<?= $first_name ?>" placeholder="<?php pll_e('Enter your first name') ?>">
                                                <?php if (isset($errors['first_name'])): ?>
                                                    <p class="validation-error text-[#FF0000] text-sm mt-1"><?= $errors['first_name'] ?></p>
                                                <?php endif; ?>
                                            </label>
                                            <label class="fullname-edit input-label">
                                                <p class="input-title"><?php pll_e('Last name') ?></p>
                                                <input type="text" name="last_name" class="input-field" value="<?= $last_name ?>" placeholder="<?php pll_e('Enter your last name') ?>">
                                                <?php if (isset($errors['last_name'])): ?>
                                                    <p class="validation-error text-[#FF0000] text-sm mt-1"><?= $errors['last_name'] ?></p>
                                                <?php endif; ?>
                                            </label>
                                        </div>

                                        <label class="email-edit input-label">
                                            <p class="input-title text-neutral-500"><?php pll_e('Email') ?></p>
                                            <input type="email" name="email" class="input-field" 
                                                value="<?= $user_email ?>" 
                                                <?= !empty($user_email) ? 'disabled' : '' ?> 
                                                placeholder="<?php pll_e('Enter your email') ?>">
                                            <?php if (isset($errors['email'])): ?>
                                                <p class="validation-error text-[#FF0000] text-sm mt-1"><?= $errors['email'] ?></p>
                                            <?php endif; ?>
                                        </label>

                                        <label class="email-edit input-label">
                                            <p class="input-title text-neutral-500"><?php pll_e('Phone') ?></p>
                                            <input type="number" name="phone_number" class="input-field"
                                                   value="<?= $user_phone ?>"
                                                <?= !empty($user_phone) ?>
                                                   placeholder="<?php pll_e('Enter your phone number') ?>">
                                            <?php if (isset($errors['phone'])): ?>
                                                <p class="validation-error text-[#FF0000] text-sm mt-1"><?= $errors['phone'] ?></p>
                                            <?php endif; ?>
                                        </label>

                                        <div class="address-edit w-full flex flex-col gap-3">
                                            <div id="address-container" class="w-full flex flex-col gap-3">
                                                <label class="address-edit input-label">
                                                    <p class="input-title text-neutral-500"><?php pll_e('Address') ?></p>
                                                    <?php foreach ($addresses as $index => $address): ?>
                                                        <div class="address-edit input-label">
                                                            <div class="address-input relative">
                                                                <input type="text" name="addresses[]" class="input-field" value="<?= htmlspecialchars($address) ?>" placeholder="<?php pll_e('Enter your address') ?>">
                                                                <button class="absolute top-1/2 -translate-y-1/2 right-4 remove-address">
                                                                    <figure>
                                                                        <img src="<?= $url ?>/assets/image/icon/trash.svg" alt="icon">
                                                                    </figure>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </label>
                                            </div>
                                            <!-- Add new address button remains the same -->
                                            <div id="add-address" class="button bg-trans text-body-md-semibold text-secondary flex items-center gap-2">
                                                <figure class="w-6 h-6">
                                                    <img src="<?= $url ?>/assets/image/icon/add-circle.svg" alt="icon">
                                                </figure>
                                                <?php pll_e('Add new address') ?>
                                            </div>
                                        </div>
                                        <?php if ($password_is_null): ?>
                                            <!-- Create new password section for social login without password -->
                                            <label class="newpassword-edit input-label">
                                                <p class="input-title"><?php pll_e('New password') ?></p>
                                                <div class="relative password-field">
                                                    <input type="password" name="new_password" class="input-field" placeholder="<?php pll_e('Enter new password') ?>">
                                                    <!-- Password visibility toggle remains the same -->
                                                </div>
                                                <?php if (isset($errors['new_password'])): ?>
                                                    <p class="validation-error text-[#FF0000] text-sm mt-1"><?= $errors['new_password'] ?></p>
                                                <?php endif; ?>
                                            </label>

                                            <label class="repassword-edit input-label">
                                                <p class="input-title">Re-enter new password</p>
                                                <div class="relative password-field">
                                                    <input type="password" name="confirm_password" class="input-field" placeholder="<?php pll_e('Confirm new password') ?>">
                                                    <!-- Password visibility toggle remains the same -->
                                                </div>
                                                <?php if (isset($errors['confirm_password'])): ?>
                                                    <p class="validation-error text-[#FF0000] text-sm mt-1"><?= $errors['confirm_password'] ?></p>
                                                <?php endif; ?>
                                            </label>
                                        <?php else: ?>
                                            <!-- Existing password change section -->
                                            <label class="currentpassword-edit input-label">
                                                <p class="input-title">Current password</p>
                                                <div class="relative password-field">
                                                    <input type="password" name="current_password" class="input-field" placeholder="<?php pll_e('Enter current password') ?>">
                                                    <!-- Password visibility toggle remains the same -->
                                                </div>
                                                <?php if (isset($errors['current_password'])): ?>
                                                    <p class="validation-error text-[#FF0000] text-sm mt-1"><?= $errors['current_password'] ?></p>
                                                <?php endif; ?>
                                            </label>

                                            <label class="newpassword-edit input-label">
                                                <p class="input-title">New password</p>
                                                <div class="relative password-field">
                                                    <input type="password" name="new_password" class="input-field" placeholder="<?php pll_e('Enter new password') ?>">
                                                    <!-- Password visibility toggle remains the same -->
                                                </div>
                                                <?php if (isset($errors['new_password'])): ?>
                                                    <p class="validation-error text-[#FF0000] text-sm mt-1"><?= $errors['new_password'] ?></p>
                                                <?php endif; ?>
                                            </label>

                                            <label class="repassword-edit input-label">
                                                <p class="input-title"><?php pll_e('Re-enter new password') ?></p>
                                                <div class="relative password-field">
                                                    <input type="password" name="confirm_password" class="input-field" placeholder="<?php pll_e('Confirm new password') ?>">
                                                    <!-- Password visibility toggle remains the same -->
                                                </div>
                                                <?php if (isset($errors['confirm_password'])): ?>
                                                    <p class="validation-error text-[#FF0000] text-sm mt-1"><?= $errors['confirm_password'] ?></p>
                                                <?php endif; ?>
                                            </label>
                                        <?php endif; ?>
                                    </div>
                                    <button id="edit-update" class="edit-update w-full max-w-[281px] button bg-primary text-body-md-semibold text-white">
                                        <?php pll_e('Update information') ?>
                                    </button>
                                    <?php if (isset($errors['password_fields'])): ?>
                                        <div class="validation-error text-[#FF0000] text-sm mt-1">
                                            <?= $errors['password_fields'] ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
<?php get_footer() ?>
<script defer>
    document.addEventListener('DOMContentLoaded', function() {
        const changeInfoButton = document.getElementById('change-info');
        const editInfoSection = document.querySelector('.edit-info');
        const viewInfoSection = document.querySelector('.view-info');
        const updateButton = document.getElementById('edit-update');
        const addAddressButton = document.getElementById('add-address');
        const addressContainer = document.getElementById('address-container');
        const addressWrap = document.querySelector('.address-wrap'); // Chọn thẻ address-wrap nơi các địa chỉ hiển thị

        // Khi nhấn nút "Change information"
        changeInfoButton.addEventListener('click', function() {
            // Chuyển từ chế độ xem sang chế độ chỉnh sửa
            viewInfoSection.classList.add('hidden');
            editInfoSection.classList.remove('hidden');
        });

        // Khi nhấn nút "Add new address"
        addAddressButton.addEventListener('click', function() {
            const newAddressDiv = document.createElement('div');
            newAddressDiv.classList.add('address-edit', 'w-full', 'flex', 'flex-col', 'gap-3');
            newAddressDiv.innerHTML = `
            <label class="address-edit input-label">
                <div class="address-input relative">
                    <input type="text" name="addresses[]" class="input-field" placeholder="Enter your address">
                    <button type="button" class="absolute top-1/2 -translate-y-1/2 right-4 remove-address">
                        <figure>
                            <img src="<?= $url ?>/assets/image/icon/trash.svg" alt="icon">
                        </figure>
                    </button>
                </div>
            </label>
        `;
            addressContainer.appendChild(newAddressDiv);

            // Thêm sự kiện xóa cho các địa chỉ mới thêm vào
            newAddressDiv.querySelector('.remove-address').addEventListener('click', function() {
                addressContainer.removeChild(newAddressDiv);
            });
        });

        // Xử lý sự kiện xóa cho tất cả các địa chỉ hiện có
        addressContainer.addEventListener('click', function(event) {
            const removeButton = event.target.closest('.remove-address');

            if (removeButton) {
                event.preventDefault();

                // Tìm phần tử cha chứa địa chỉ để xóa
                const addressDiv = removeButton.closest('.address-edit');

                // Xóa địa chỉ khỏi DOM
                addressDiv.remove();
            }
        });
    });
    document.addEventListener('DOMContentLoaded', function() {
        const profileImageInput = document.getElementById('profile-image');
        const profileImagePreview = document.querySelector('.avatar-edit figure img');

        profileImageInput.addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    profileImagePreview.src = e.target.result;
                }

                reader.readAsDataURL(file);
            }
        });
    });
    document.addEventListener('DOMContentLoaded', function() {
        const form_edit = document.querySelector('.form-edit');
        const updateButton = document.getElementById('edit-update');
        const currentPasswordInput = document.querySelector('input[name="current_password"]');
        const newPasswordInput = document.querySelector('input[name="new_password"]');
        const confirmPasswordInput = document.querySelector('input[name="confirm_password"]');
        
        // Check if password exists (based on whether current password field exists)
        const hasExistingPassword = !!currentPasswordInput;

        updateButton.addEventListener('click', function(event) {
            event.preventDefault(); 

            // Clear existing error messages
            const existingErrors = document.querySelectorAll('.validation-error');
            existingErrors.forEach(error => error.remove());

            let hasError = false;

            // Validate names
            const firstNameInput = document.querySelector('input[name="first_name"]');
            const lastNameInput = document.querySelector('input[name="last_name"]');
            
            if (!firstNameInput.value.trim() && !lastNameInput.value.trim()) {
                createErrorMessage(firstNameInput, '<?php pll_e('At least one name field must be filled') ?>');
                hasError = true;
            }

            // Password validation logic
            const passwordFieldsFilled = hasExistingPassword ? 
                [currentPasswordInput, newPasswordInput, confirmPasswordInput].some(input => input.value.trim() !== '') :
                [newPasswordInput, confirmPasswordInput].some(input => input.value.trim() !== '');

            if (passwordFieldsFilled) {
                if (hasExistingPassword) {
                    // For accounts with existing password, check all three fields
                    if (!currentPasswordInput.value.trim() || !newPasswordInput.value.trim() || !confirmPasswordInput.value.trim()) {
                        const errorElement = document.createElement('p');
                        errorElement.classList.add('validation-error', 'text-[#FF0000]', 'text-sm', 'mt-1');
                        errorElement.textContent = '<?php pll_e('Please fill in all password fields') ?>';
                        confirmPasswordInput.parentNode.insertBefore(errorElement, confirmPasswordInput.nextSibling);
                        hasError = true;
                    }
                } else {
                    // For accounts without password, only check new password and confirm password
                    if (!newPasswordInput.value.trim() || !confirmPasswordInput.value.trim()) {
                        const errorElement = document.createElement('p');
                        errorElement.classList.add('validation-error', 'text-[#FF0000]', 'text-sm', 'mt-1');
                        errorElement.textContent = '<?php pll_e('Please fill in all password fields') ?>';
                        confirmPasswordInput.parentNode.insertBefore(errorElement, confirmPasswordInput.nextSibling);
                        hasError = true;
                    }
                }

                // Validate new password length
                if (newPasswordInput.value.trim().length < 6) {
                    createErrorMessage(newPasswordInput, '<?php pll_e('Password must be at least 6 characters') ?>');
                    hasError = true;
                }

                // Validate password confirmation
                if (newPasswordInput.value !== confirmPasswordInput.value) {
                    createErrorMessage(confirmPasswordInput, '<?php pll_e('Passwords do not match') ?>');
                    hasError = true;
                }

                // Verify current password if it exists
                if (!hasError && hasExistingPassword) {
                    fetch('<?= admin_url("admin-ajax.php") ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `action=verify_current_password&current_password=${encodeURIComponent(currentPasswordInput.value)}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (!data.success) {
                            const errorElement = document.createElement('p');
                            errorElement.classList.add('validation-error', 'text-[#FF0000]', 'text-sm', 'mt-1');
                            errorElement.textContent = '<?php pll_e('Current password is incorrect') ?>';
                            currentPasswordInput.parentNode.insertBefore(errorElement, currentPasswordInput.nextSibling);
                            hasError = true;
                        }
                        
                        if (!hasError) {
                            form_edit.submit();
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
                } else if (!hasError) {
                    form_edit.submit();
                }
            } else {
                // If no password fields are filled, submit form
                form_edit.submit();
            }
        });

        function createErrorMessage(inputElement, message) {
            const errorElement = document.createElement('p');
            errorElement.classList.add('validation-error', 'text-[#FF0000]', 'text-sm', 'mt-1');
            errorElement.textContent = message;
            inputElement.parentNode.insertBefore(errorElement, inputElement.nextSibling);
        }
    });
    
    document.addEventListener("DOMContentLoaded", function() {
        const logoutForm = document.getElementById("logoutForm");

        // Thêm sự kiện click cho toàn bộ form
        logoutForm.addEventListener("click", function(event) {
            // Ngăn chặn sự kiện click lan sang các phần tử khác
            event.preventDefault();

            // Hiển thị SweetAlert
            Swal.fire({
                title: 'Are you sure?',
                text: "You will be logged out!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, log me out!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Người dùng xác nhận, gửi form
                    logoutForm.submit();
                }
            });
        });
    });
</script>