<?php /* Template Name: Personal information */ ?>
<?php
$authenticated_user = validate_user_token();

global $wpdb;
$table_name = 'wp_account_users';

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
//$table_name = $wpdb->prefix . 'account_users';

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
    $user_phone = $_POST['phone_number'];
//    print_r($user_phone);die();
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
?>
<?php
get_header('dealer');
?>
<script type="text/javascript" src="<?= $url ?>/dist/js/personnal-info.js"></script>

<div id="info-person" class="mx-auto relative rounded-2xl bg-white w-600-full overflow-hidden border-cm m-8 sm:px-0 px-5">
    <div class="py-4 md:px-8 px-4 bd-line-bottom">
        <h1 class="text-left mt-0 mb-0 color-vector text-28 text-mb-20 font-semibold leading-10">Personal information</h1>
    </div>
    <div class="py-4 md:px-8 px-4">
        <div class="flex items-center justify-center">
            <div class="w-455 m-auto mb-6 text-center">
<!--                <div class="pt-8 mb-8 text-center"><img class="w-20 h-auto" src="--><?//= $avatar ? $avatar : $url . '/dist/img/avatar_default.png' ?><!--" alt=""></div>-->
                <div class="flex items-center bd-line-bottom pb-4 pt-4">
                    <span class="text-base font-normal color-vector">Full name</span>
                    <span class="text-base font-medium color-vector ml-auto order-2"><?= $first_name ?> <?= $last_name ?></span>
                </div>
                <div class="flex items-center bd-line-bottom py-4">
                    <span class="text-base font-normal color-vector">Email</span>
                    <span class="text-base font-medium color-vector ml-auto order-2"><?= $user_email ?></p></span>
                </div>
                <div class="flex items-center bd-line-bottom py-4">
                    <span class="text-base font-normal color-vector">Phone number</span>
                    <span class="text-base font-medium color-vector ml-auto order-2"><?= $user_phone ?></p></span>
                </div>
                <div class="flex flex-col items-center">
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
                                <span class="text-base font-normal color-vector">
                                    Address (<?= $index + 1 ?>)
                                </span>
                                <span class="text-base font-medium color-vector ml-auto order-2 text-right">
                                    <?= htmlspecialchars($address) ?>
                                </span>
                            </div>
                        <?php endforeach ?>
                    <?php endif ?>
                    <!-- <span class="text-base font-normal color-vector">Address</span>
                    <span class="text-base font-medium color-vector ml-auto order-2 text-right">8815 Fairbanks North Houston Rd<br>Houston, TX 77064</span> -->
                </div>
                <div class="flex items-center bd-line-bottom py-4 mb-8">
                    <span class="text-base font-normal color-vector">Password</span>
                    <span class="text-base font-medium color-vector ml-auto order-2">********</span>
                </div>
                <button id="change-info" type="submit" class="text-white bg-red-sure border-none font-semibold rounded-lg text-base w-full sm:w-auto md:px-14 px-4 py-4 text-center my-0">Change information</button>
            </div>
        </div>
    </div>
</div>
<div id="edit-person" class="mx-auto relative rounded-2xl bg-white w-600-full overflow-hidden border-cm m-8 sm:px-0 px-5 w-1014 hidden">
    <div class="py-4 md:px-8 px-4 bd-line-bottom">
        <h1 class="text-left mt-0 mb-0 color-vector text-xl text-mb-20 font-normal leading-10 line-height-32">Personal information</h1>
    </div>
    <form class="py-4 md:px-8 px-4 form-edit" method="POST" enctype="multipart/form-data" >
        <input type="hidden" name="update_profile" value="1">
        <div class="flex items-center justify-center">
            <div class="w-455 m-auto mb-6">
<!--                <div class="pt-8 mb-8 text-center">-->
<!--                    <div class="avatar-edit relative w-20 h-20 m-auto">-->
<!--                        <input type="file" name="avatar" id="profile-image" accept="image/*" class="hidden">-->
<!--                        <label for="profile-image" class="cursor-pointer relative block w-full h-full rounded-full overflow-hidden">-->
<!--                           Avatar -->
<!--                            <figure class="w-full h-full">-->
<!--                                <img src="--><?//= $avatar ? $avatar : $url . '/assets/image/dashboard/avatar-80.svg' ?><!--" alt="avatar"-->
<!--                                     class="w-full h-full object-cover" />-->
<!--                            </figure>-->
<!--                            Overlay với icon camera -->
<!--                            <div class="absolute inset-0 bg-[#0000008a] flex justify-center items-center opacity-0 hover:opacity-100 transition-opacity">-->
<!--                                <figure>-->
<!--                                    <img src="--><?//= $url ?><!--/assets/image/icon/camera.svg" alt="icon"-->
<!--                                         class="w-6 h-6" />-->
<!--                                </figure>-->
<!--                            </div>-->
<!--                        </label>-->
<!--                    </div>-->
<!--                </div>-->
                <div class="pb-5 text-left">
                    <span class="block mb-1 text-base font-medium text-gray-900 dark:text-white">First name</span>
                    <input type="text" name="first_name" class="box-border bg-input border border-gray-300 text-gray-900 text-base rounded-lg block w-full pd-11-cus" value="<?= $first_name ?>" placeholder="Enter your first name">
                    <?php if (isset($errors['first_name'])): ?>
                        <p class="validation-error text-[#FF0000] text-sm mt-1"><?= $errors['first_name'] ?></p>
                    <?php endif; ?>
                </div>
                <div class="pb-5 text-left">
                    <span class="block mb-1 text-base font-medium text-gray-900 dark:text-white">Last name</span>
                    <input type="text" name="last_name" class="box-border bg-input border border-gray-300 text-gray-900 text-base rounded-lg block w-full pd-11-cus" value="<?= $last_name ?>" placeholder="Enter your last name">
                    <?php if (isset($errors['last_name'])): ?>
                        <p class="validation-error text-[#FF0000] text-sm mt-1"><?= $errors['last_name'] ?></p>
                    <?php endif; ?>
                </div>
                <div class="pb-5 text-left">
                    <span class="block mb-1 text-base font-medium cl-gray">Email</span>
                    <input type="text" name="email" class="box-border bg-input border border-gray-300 text-gray-900 text-base rounded-lg block w-full pd-11-cus cl-gray"  value="<?= $user_email ?>"
                        <?= !empty($user_email) ? 'disabled' : '' ?>
                           placeholder="Enter your email">
                    <?php if (isset($errors['email'])): ?>
                        <p class="validation-error text-[#FF0000] text-sm mt-1"><?= $errors['email'] ?></p>
                    <?php endif; ?>
                </div>
                <div class="pb-5 text-left">
                    <span class="block mb-1 text-base font-medium cl-gray">Phone</span>
                    <input type="number" name="phone_number" class="box-border bg-input border border-gray-300 text-gray-900 text-base rounded-lg block w-full pd-11-cus cl-gray"  value="<?= $user_phone ?>"
                        <?= !empty($user_phone) ?>
                           placeholder="Enter your phone number">
                    <?php if (isset($errors['phone'])): ?>
                        <p class="validation-error text-[#FF0000] text-sm mt-1"><?= $errors['phone'] ?></p>
                    <?php endif; ?>
                </div>
                <div class="pb-5 text-left">
                    <span class="block mb-1 text-base font-medium text-gray-900 dark:text-white">Address</span>
                    <div id="address-container">
                        <?php foreach ($addresses as $index => $address): ?>
                        <div class="address-input relative mt-3" id="current-edit">
                            <div id="remove-address-edit" class="absolute right-0">
                                <figure>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" class="w-24 absolute right-3 top-3">
                                        <g id="Outline / Essentional, UI / Trash Bin Minimalistic">
                                            <g id="Vector">
                                                <path d="M12 2.75C11.0215 2.75 10.1871 3.37503 9.87787 4.24993C9.73983 4.64047 9.31134 4.84517 8.9208 4.70713C8.53026 4.56909 8.32557 4.1406 8.46361 3.75007C8.97804 2.29459 10.3661 1.25 12 1.25C13.634 1.25 15.022 2.29459 15.5365 3.75007C15.6745 4.1406 15.4698 4.56909 15.0793 4.70713C14.6887 4.84517 14.2602 4.64047 14.1222 4.24993C13.813 3.37503 12.9785 2.75 12 2.75Z" fill="#1F2237"/>
                                                <path d="M2.75 6C2.75 5.58579 3.08579 5.25 3.5 5.25H20.5001C20.9143 5.25 21.2501 5.58579 21.2501 6C21.2501 6.41421 20.9143 6.75 20.5001 6.75H3.5C3.08579 6.75 2.75 6.41421 2.75 6Z" fill="#1F2237"/>
                                                <path d="M5.91508 8.45011C5.88753 8.03681 5.53015 7.72411 5.11686 7.75166C4.70356 7.77921 4.39085 8.13659 4.41841 8.54989L4.88186 15.5016C4.96735 16.7844 5.03641 17.8205 5.19838 18.6336C5.36678 19.4789 5.6532 20.185 6.2448 20.7384C6.83639 21.2919 7.55994 21.5307 8.41459 21.6425C9.23663 21.75 10.2751 21.75 11.5607 21.75H12.4395C13.7251 21.75 14.7635 21.75 15.5856 21.6425C16.4402 21.5307 17.1638 21.2919 17.7554 20.7384C18.347 20.185 18.6334 19.4789 18.8018 18.6336C18.9637 17.8205 19.0328 16.7844 19.1183 15.5016L19.5818 8.54989C19.6093 8.13659 19.2966 7.77921 18.8833 7.75166C18.47 7.72411 18.1126 8.03681 18.0851 8.45011L17.6251 15.3492C17.5353 16.6971 17.4712 17.6349 17.3307 18.3405C17.1943 19.025 17.004 19.3873 16.7306 19.6431C16.4572 19.8988 16.083 20.0647 15.391 20.1552C14.6776 20.2485 13.7376 20.25 12.3868 20.25H11.6134C10.2626 20.25 9.32255 20.2485 8.60915 20.1552C7.91715 20.0647 7.54299 19.8988 7.26957 19.6431C6.99616 19.3873 6.80583 19.025 6.66948 18.3405C6.52891 17.6349 6.46488 16.6971 6.37503 15.3492L5.91508 8.45011Z" fill="#1F2237"/>
                                                <path d="M9.42546 10.2537C9.83762 10.2125 10.2051 10.5132 10.2464 10.9254L10.7464 15.9254C10.7876 16.3375 10.4869 16.7051 10.0747 16.7463C9.66256 16.7875 9.29502 16.4868 9.25381 16.0746L8.75381 11.0746C8.71259 10.6625 9.0133 10.2949 9.42546 10.2537Z" fill="#1F2237"/>
                                                <path d="M15.2464 11.0746C15.2876 10.6625 14.9869 10.2949 14.5747 10.2537C14.1626 10.2125 13.795 10.5132 13.7538 10.9254L13.2538 15.9254C13.2126 16.3375 13.5133 16.7051 13.9255 16.7463C14.3376 16.7875 14.7051 16.4868 14.7464 16.0746L15.2464 11.0746Z" fill="#1F2237"/>
                                            </g>
                                        </g>
                                    </svg>
                                </figure>
                            </div>
                            <input type="text" name="addresses[]" class="box-border bg-input border border-gray-300 text-gray-900 text-base rounded-lg block w-full pd-11-cus pr-40" value="<?= htmlspecialchars($address) ?>" placeholder="Enter your address" />
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <div id="add-address" class="button bg-trans text-body-md-semibold text-secondary flex items-center mt-3">
                        <figure class="w-6 h-6 mr-2">
                            <img src="http://suremealdev.wecan-group.info/wp-content/themes/abcd/assets/image/icon/add-circle.svg" alt="icon">
                        </figure>
                        <span id="" class="cl-b text-base font-semibold line-height-24">Add new address </span>
                    </div>
                </div>
                <?php if ($password_is_null): ?>
                    <div class="pb-5 text-left">
                        <span class="block mb-1 text-base font-medium">New password</span>
                        <input type="password" name="new_password" class="box-border bg-input border border-gray-300 text-gray-900 text-base rounded-lg block w-full pd-11-cus cl-gray" placeholder="Enter new password" autocomplete="off" >
                        <?php if (isset($errors['new_password'])): ?>
                            <p class="validation-error text-[#FF0000] text-sm mt-1"><?= $errors['new_password'] ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="pb-5 text-left">
                        <span class="block mb-1 text-base font-medium">Re-enter new password</span>
                        <input type="password" name="confirm_password" class="box-border bg-input border border-gray-300 text-gray-900 text-base rounded-lg block w-full pd-11-cus cl-gray" placeholder="Enter your email" autocomplete="off" >
                        <?php if (isset($errors['confirm_password'])): ?>
                            <p class="validation-error text-[#FF0000] text-sm mt-1"><?= $errors['confirm_password'] ?></p>
                        <?php endif; ?>
                    </div>

                <?php else: ?>

                <div class="pb-5 text-left">
                    <span class="block mb-1 text-base font-medium">Current password</span>
                    <input type="password" name="current_password" class="box-border bg-input border border-gray-300 text-gray-900 text-base rounded-lg block w-full pd-11-cus cl-gray" placeholder="Enter current password" autocomplete="off" >
                    <?php if (isset($errors['current_password'])): ?>
                        <p class="validation-error text-[#FF0000] text-sm mt-1"><?= $errors['current_password'] ?></p>
                    <?php endif; ?>
                </div>
                <div class="pb-5 text-left">
                    <span class="block mb-1 text-base font-medium">New password</span>
                    <input type="password" name="new_password" class="box-border bg-input border border-gray-300 text-gray-900 text-base rounded-lg block w-full pd-11-cus cl-gray" placeholder="Enter new password" autocomplete="off"  >
                    <?php if (isset($errors['new_password'])): ?>
                        <p class="validation-error text-[#FF0000] text-sm mt-1"><?= $errors['new_password'] ?></p>
                    <?php endif; ?>
                </div>
                <div class="pb-8 text-left">
                    <span class="block mb-1 text-base font-medium">Re-enter new password</span>
                    <input type="password" name="confirm_password" class="box-border bg-input border border-gray-300 text-gray-900 text-base rounded-lg block w-full pd-11-cus cl-gray" placeholder="Confirm new password" autocomplete="off"  >
                </div>
                <?php endif; ?>
                <div class="text-center">
                    <button id="edit-update " type="submit" class="text-white bg-red-sure border-none font-semibold rounded-lg text-base w-full sm:w-auto md:px-14 px-4 py-4 text-center my-0">Update information</button>
                </div>
                <?php if (isset($errors['password_fields'])): ?>
                    <div class="validation-error text-[#FF0000] text-sm mt-1">
                        <?= $errors['password_fields'] ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </form>
    <div>
    </div>

</div>
<?php
get_footer('dealer');
?>
<script defer>

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