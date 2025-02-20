<?php /* Template Name: Dealer-Personal-Info */ ?>
<?php 
$authenticated_dealer = validate_dealer_token();
if (!(isset($_COOKIE['dealer_token']) && $authenticated_dealer)) {
    wp_redirect(home_url());
    exit;
}

// Kiểm tra email tồn tại
function check_email_exists($email) {
    global $wpdb; 
    $table_name = $wpdb->prefix . 'account_dealers';
    
    $result = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $table_name WHERE email = %s",
        $email
    ));
    
    return $result > 0;
 }

global $wpdb;
$table_name = $wpdb->prefix . 'account_dealers';

// Lấy thông tin người dùng
$user_email = $authenticated_dealer->email;
$phone = $authenticated_dealer->phone;
$first_name = $authenticated_dealer->first_name;
$last_name = $authenticated_dealer->last_name;
$avatar = $authenticated_dealer->avatar;
$addresses = json_decode($authenticated_dealer->addresses, true) ?: [];
$provider = $authenticated_dealer->provider;

$password_is_null = $authenticated_dealer->password == '';

// Xử lý cập nhật thông tin khi submit form
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    // Khởi tạo mảng lỗi
    $errors = [];

    // Validate names - at least one must be filled
    $first_name = sanitize_text_field($_POST['first_name']);
    $last_name = sanitize_text_field($_POST['last_name']);
    $phone = sanitize_text_field($_POST['phone']);
    
    if (empty($first_name) && empty($last_name)) {
        $errors['name'] = 'At least one name field must be filled';
    }

    // Xử lý email (nếu chưa có)
    $user_email = $authenticated_dealer->email;
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
    $avatar = $authenticated_dealer->avatar;
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
                if (!password_verify($current_password, $authenticated_dealer->password)) {
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
            'phone' => $phone,
            'addresses' => json_encode($addresses)
//            'avatar' => $avatar
        ];

        // Cập nhật email nếu chưa có
        if (empty($authenticated_dealer->email)) {
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
            ['token' => $_COOKIE['dealer_token']]
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
    <div class="relative">
        <div class="grid 2xl:grid-cols-7 xl:grid-cols-7 md:grid-cols-8 sm:grid-cols-1 grid-cols-1 gap-0">
            <div class="flex items-start col-span-1 2xl:col-span-1 xl:col-span-1 md:col-span-2 grid bg-white md:h-screen height-full">
                <ul class="list-none pl-0 text-left mt-0 memubar" x-data="{ top: true }">
<!--                    <li class="flex items-center pt-4 pb-4 pl-6 item-f dashboard">-->
<!--                    <a class="flex items-center no-underline" href="--><?//= home_url() ?><!--/dealer-dashboard"><img class="mr-3 w-6 default" src="--><?//= $url ?><!--/assets/dealer/img/cuida.png" alt=""><img class="mr-3 w-6 active hidden" src="--><?//= $url ?><!--/assets/dealer/img/cuida_ac.png" alt=""><span class="menu-item">Dashboard</span></a>-->
<!--                    </li>-->
                    <li class="flex items-center pt-4 pb-4 pl-6 item-f my-order">
                    <a class="flex items-center no-underline menu-item" href="<?= home_url() ?>/dealer-order-info"><img class="mr-3 w-6 default" src="<?= $url ?>/assets/dealer/img/cart.png" alt=""><img class="mr-3 w-6 active hidden" src="<?= $url ?>/assets/dealer/img/cart_ac.png " alt=""><span class="menu-item">My orders</span></a>
                    </li>
                    <li class="pt-4 pb-4 pl-6 item-f account checkout-menu-active" >
                    <div class="flex items-center no-underline w-full" href="" x-on:click="top = ! top"><img class="mr-3 w-6 default hidden" src="<?= $url ?>/assets/dealer/img/circle.png" alt=""><img class="mr-3 w-6 active" src="<?= $url ?>/assets/dealer/img/circle_ac.png" alt="">
                        <span class="menu-item cursor-pointer">Account<br>information</span>
                        <div class="order-2 ml-auto mr-4">
                            <img class="w-6" :src="top ? '<?= $url ?>/assets/dealer/img/icon_down.png' : '<?= $url ?>/assets/dealer/img/icon_up.png'"  alt="">
                        </div>
                    </div>
                    </li>
                    <li class="pl-6">
                    <ul class="list-none" x-show="top">
                        <li class="mt-2">
                            <a class="no-underline text-sm blue-sure" href="<?= home_url() ?>/dealer-personal-informmation">Personal information</a>
                        </li>
                        <li class="mt-2">
                            <a class="no-underline text-sm" href="<?= home_url() ?>/dealer-business-informmation">Business information</a>
                        </li>
                    </ul>
                    </li>
                </ul>
            </div>
            <div class="col-span-6 text-center md:p-8 p-4 m-h-content">
                <div class="mx-auto relative rounded-2xl bg-white w-600-full overflow-hidden border-cm">
                    <div class="py-4 md:px-8 px-4 bd-line-bottom">
                        <h1 class="text-left mt-0 mb-0 color-vector text-28 text-mb-20 font-semibold">Personal information</h1>
                    </div>
                    <div class="py-4 md:px-8 px-4">
                        <div class="flex items-center justify-center">
                            <div class="view-info w-full max-w-[455px] px-4 lg:px-0 flex flex-col items-center justify-center gap-8">
<!--                                <figure class="w-20 h-20 rounded-full overflow-hidden"><img-->
<!--                                        src="--><?//= $avatar ? $avatar : $url . '/assets/image/dashboard/avatar-80.svg' ?><!--" alt="avatar"></figure>-->
                                <div class="w-full">
                                    <div
                                        class="name-info w-full flex py-4 border-b border-solid border-neutral-200">
                                        <p class="w-1/5 text-justify whitespace-nowrap text-body-md-regular text-gray-8">
                                            Full name
                                        </p>
                                        <p class="w-4/5 pl-8 text-end text-body-md-medium text-gray-8"
                                            id="view-name">
                                            <?= $first_name ?> <?= $last_name ?>
                                        </p>
                                    </div>
                                    <div
                                        class="email-info w-full flex py-4 border-b border-solid border-neutral-200">
                                        <p class="w-1/5 text-justify whitespace-nowrap text-body-md-regular text-gray-8">
                                            Email
                                        </p>
                                        <p class="w-4/5 pl-8 text-end text-body-md-medium text-gray-8"
                                            id="view-email">
                                            <?= $user_email ?></p>
                                    </div>
                                    <div
                                        class="email-info w-full flex py-4 border-b border-solid border-neutral-200">
                                        <p class="w-1/5 text-justify whitespace-nowrap text-body-md-regular text-gray-8">
                                            Phone number
                                        </p>
                                        <p class="w-4/5 pl-8 text-end text-body-md-medium text-gray-8"
                                            id="view-email">
                                            <?= $phone ?></p>
                                    </div>
                                    <div class="address-wrap w-full flex flex-col">
                                        <?php
                                        // Kiểm tra và decode addresses
                                        $stored_addresses = is_string($authenticated_dealer->addresses)
                                            ? json_decode($authenticated_dealer->addresses, true)
                                            : (is_array($authenticated_dealer->addresses)
                                                ? $authenticated_dealer->addresses
                                                : []);

                                        $stored_addresses = $stored_addresses ?: [];

                                        // Kiểm tra và render địa chỉ
                                        if (!empty($stored_addresses)) :
                                            foreach ($stored_addresses as $index => $address) :
                                        ?>
                                                <div class="address-info w-full flex py-4 border-b border-solid border-neutral-200">
                                                    <p class="w-1/5 text-justify whitespace-nowrap text-body-md-regular text-gray-8">
                                                        Address (<?= $index + 1 ?>)
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
                                        <p class="w-1/5 text-justify whitespace-nowrap text-body-md-regular text-gray-8">
                                            Password
                                        </p>
                                        <input type="password" id="view-password"
                                            class="input-trans w-4/5 pl-8 text-end text-body-md-medium text-gray-8"
                                            value="123456" disabled>
                                    </div>
                                </div>
                                <button id="change-info"
                                    class="change-info w-full max-w-[281px] button bg-primary text-body-md-semibold text-white">Change
                                    information</button>
                            </div>
                            <!-- thông tin - edit -->
                            <form method="POST" enctype="multipart/form-data" class="form-edit">
                                <input type="hidden" name="update_profile" value="1">
                                <div class="edit-info hidden w-full max-w-[455px] px-4 lg:px-0 flex flex-col items-center justify-center gap-8">
<!--                                    <div class="avatar-edit relative w-20 h-20">-->
<!--                                        <input type="file" name="avatar" id="profile-image" accept="image/*" class="hidden" />-->
<!--                                        <label for="profile-image"-->
<!--                                            class="cursor-pointer relative block w-full h-full rounded-full overflow-hidden">-->
<!--                                            Avatar -->
<!--                                            <figure class="w-full h-full">-->
<!--                                                <img src="--><?//= $avatar ? $avatar : $url . '/assets/image/dashboard/avatar-80.svg' ?><!--" alt="avatar"-->
<!--                                                    class="w-full h-full object-cover" />-->
<!--                                            </figure>-->
<!--                                          Overlay với icon camera -->
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
                                                <p class="input-title text-justify">First name</p>
                                                <input type="text" name="first_name" class="input-field" value="<?= $first_name ?>" placeholder="Enter your first name">
                                                <?php if (isset($errors['first_name'])): ?>
                                                    <p class="validation-error text-justify text-[#FF0000] text-sm mt-1"><?= $errors['first_name'] ?></p>
                                                <?php endif; ?>
                                            </label>
                                            <label class="fullname-edit input-label">
                                                <p class="input-title text-justify">Last name</p>
                                                <input type="text" name="last_name" class="input-field" value="<?= $last_name ?>" placeholder="Enter your last name">
                                                <?php if (isset($errors['last_name'])): ?>
                                                    <p class="validation-error text-justify text-[#FF0000] text-sm mt-1"><?= $errors['last_name'] ?></p>
                                                <?php endif; ?>
                                            </label>
                                        </div>

                                        <label class="email-edit input-label">
                                            <p class="input-title text-justify text-neutral-500">Email</p>
                                            <input type="email" name="email" class="input-field" 
                                                value="<?= $user_email ?>" 
                                                <?= !empty($user_email) ? 'disabled' : '' ?> 
                                                placeholder="Enter your email">
                                            <?php if (isset($errors['email'])): ?>
                                                <p class="validation-error text-justify text-[#FF0000] text-sm mt-1"><?= $errors['email'] ?></p>
                                            <?php endif; ?>
                                        </label>

                                        <label class="phone-edit input-label">
                                            <p class="input-title text-justify text-neutral-500">Phone number</p>
                                            <input type="text" name="phone" class="input-field" 
                                                value="<?= $phone ?>" 
                                                placeholder="Enter your phone number">
                                            <?php if (isset($errors['phone'])): ?>
                                                <p class="validation-error text-justify text-[#FF0000] text-sm mt-1"><?= $errors['phone'] ?></p>
                                            <?php endif; ?>
                                        </label>

                                        <div class="address-edit w-full flex flex-col gap-3">
                                            <div id="address-container" class="w-full flex flex-col gap-3">
                                                <label class="address-edit input-label">
                                                    <p class="input-title text-justify text-neutral-500">Address</p>
                                                    <?php foreach ($addresses as $index => $address): ?>
                                                        <div class="address-edit input-label">
                                                            <div class="address-input relative">
                                                                <input type="text" name="addresses[]" class="input-field" value="<?= htmlspecialchars($address) ?>" placeholder="Enter your address">
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
                                                Add new address
                                            </div>
                                        </div>
                                        <?php if ($password_is_null): ?>
                                            <!-- Create new password section for social login without password -->
                                            <label class="newpassword-edit input-label">
                                                <p class="input-title text-justify">New password</p>
                                                <div class="relative password-field">
                                                    <input type="password" name="new_password" class="input-field" placeholder="Enter new password">
                                                    <!-- Password visibility toggle remains the same -->
                                                </div>
                                                <?php if (isset($errors['new_password'])): ?>
                                                    <p class="validation-error text-justify text-[#FF0000] text-sm mt-1"><?= $errors['new_password'] ?></p>
                                                <?php endif; ?>
                                            </label>

                                            <label class="repassword-edit input-label">
                                                <p class="input-title text-justify">Re-enter new password</p>
                                                <div class="relative password-field">
                                                    <input type="password" name="confirm_password" class="input-field" placeholder="Confirm new password">
                                                    <!-- Password visibility toggle remains the same -->
                                                </div>
                                                <?php if (isset($errors['confirm_password'])): ?>
                                                    <p class="validation-error text-justify text-[#FF0000] text-sm mt-1"><?= $errors['confirm_password'] ?></p>
                                                <?php endif; ?>
                                            </label>
                                        <?php else: ?>
                                            <!-- Existing password change section -->
                                            <label class="currentpassword-edit input-label">
                                                <p class="input-title text-justify">Current password</p>
                                                <div class="relative password-field">
                                                    <input type="password" name="current_password" class="input-field" placeholder="Enter current password">
                                                    <!-- Password visibility toggle remains the same -->
                                                </div>
                                                <?php if (isset($errors['current_password'])): ?>
                                                    <p class="validation-error text-justify text-[#FF0000] text-sm mt-1"><?= $errors['current_password'] ?></p>
                                                <?php endif; ?>
                                            </label>

                                            <label class="newpassword-edit input-label">
                                                <p class="input-title text-justify">New password</p>
                                                <div class="relative password-field">
                                                    <input type="password" name="new_password" class="input-field" placeholder="Enter new password">
                                                    <!-- Password visibility toggle remains the same -->
                                                </div>
                                                <?php if (isset($errors['new_password'])): ?>
                                                    <p class="validation-error text-justify text-[#FF0000] text-sm mt-1"><?= $errors['new_password'] ?></p>
                                                <?php endif; ?>
                                            </label>

                                            <label class="repassword-edit input-label">
                                                <p class="input-title text-justify">Re-enter new password</p>
                                                <div class="relative password-field">
                                                    <input type="password" name="confirm_password" class="input-field" placeholder="Confirm new password">
                                                    <!-- Password visibility toggle remains the same -->
                                                </div>
                                                <?php if (isset($errors['confirm_password'])): ?>
                                                    <p class="validation-error text-justify text-[#FF0000] text-sm mt-1"><?= $errors['confirm_password'] ?></p>
                                                <?php endif; ?>
                                            </label>
                                        <?php endif; ?>
                                    </div>
                                    <button id="edit-update" class="edit-update w-full max-w-[281px] button bg-primary text-body-md-semibold text-white">
                                        Update information
                                    </button>
                                    <?php if (isset($errors['password_fields'])): ?>
                                        <div class="validation-error text-justify text-[#FF0000] text-sm mt-1">
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
    </div>
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
                createErrorMessage(firstNameInput, 'At least one name field must be filled');
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
                        errorElement.textContent = 'Please fill in all password fields';
                        confirmPasswordInput.parentNode.insertBefore(errorElement, confirmPasswordInput.nextSibling);
                        hasError = true;
                    }
                } else {
                    // For accounts without password, only check new password and confirm password
                    if (!newPasswordInput.value.trim() || !confirmPasswordInput.value.trim()) {
                        const errorElement = document.createElement('p');
                        errorElement.classList.add('validation-error', 'text-[#FF0000]', 'text-sm', 'mt-1');
                        errorElement.textContent = 'Please fill in all password fields';
                        confirmPasswordInput.parentNode.insertBefore(errorElement, confirmPasswordInput.nextSibling);
                        hasError = true;
                    }
                }

                // Validate new password length
                if (newPasswordInput.value.trim().length < 6) {
                    createErrorMessage(newPasswordInput, 'Password must be at least 6 characters');
                    hasError = true;
                }

                // Validate password confirmation
                if (newPasswordInput.value !== confirmPasswordInput.value) {
                    createErrorMessage(confirmPasswordInput, 'Passwords do not match');
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
                            errorElement.textContent = 'Current password is incorrect';
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
            errorElement.classList.add('validation-error', 'text-justify', 'text-[#FF0000]', 'text-sm', 'mt-1');
            errorElement.textContent = message;
            inputElement.parentNode.insertBefore(errorElement, inputElement.nextSibling);
        }
    });
</script>