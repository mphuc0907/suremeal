<?php /* Template Name: Password Reset */ ?>

<?php
$authenticated_user = validate_user_token();
if (isset($_COOKIE['user_token']) && $authenticated_user->type == 1) {
    wp_redirect(home_url());
    exit;
}

global $wpdb;
$table_name = 'wp_account_users';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize_email($_POST['email']);

    if (!is_email($email)) {
        $error_message = pll__('Invalid email address');
    } else {
        global $wpdb;
        $user = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE email = %s AND type = 1",
            $email
        ));

        if (!$user) {
            $error_message = pll__('No user found with this email address');
        } else {
            // Kiểm tra nếu đã có token tồn tại
            $existing_token = $wpdb->get_row($wpdb->prepare(
                "SELECT * FROM wp_password_reset_tokens WHERE email = %s AND id = %d",
                $email,
                $user->id
            ));
        
            $current_time = time();
            if ($existing_token && $existing_token->expiry > $current_time) {
                // Token vẫn còn hạn
                $success_message = pll__('A valid password reset link has already been sent to your email.');
            } else {
                // Tạo token mới hoặc cập nhật token hết hạn
                $reset_token = wp_generate_password(32, false);
                $expiry = $current_time + (24 * 60 * 60); // Token mới có hiệu lực 24 giờ
        
                if ($existing_token) {
                    // Cập nhật token cũ đã hết hạn
                    $result = $wpdb->update(
                        'wp_password_reset_tokens',
                        [
                            'token' => $reset_token,
                            'expiry' => $expiry,
                            'created_at' => current_time('mysql'),
                        ],
                        ['email' => $email, 'id' => $user->id],
                        ['%s', '%d', '%s'],
                        ['%s', '%d']
                    );
                } else {
                    // Tạo token mới
                    $result = $wpdb->insert(
                        'wp_password_reset_tokens',
                        [
                            'id' => $user->id,
                            'token' => $reset_token,
                            'expiry' => $expiry,
                            'email' => $email,
                            'created_at' => current_time('mysql'),
                        ],
                        ['%d', '%s', '%d', '%s', '%s']
                    );
                }
        
                if ($result !== false) {
                    // Gửi email với token mới
                    $reset_link = add_query_arg([
                        'token' => $reset_token,
                        'email' => $email
                    ], home_url('/change-password'));
        
                    $to = $email;
                    $subject = 'Password Reset Request';
                    $message = "Click the following link to reset your password:\n\n" . $reset_link;
                    $headers = ['Content-Type: text/plain; charset=UTF-8'];
        
                    $email_sent = wp_mail($to, $subject, $message, $headers);
        
                    if ($email_sent) {
                        $success_message = pll__('Password reset link sent to your email.');
                    } else {
                        $error_message = pll__('Failed to send reset email.');
                    }
                } else {
                    $error_message = pll__('Failed to generate a reset token. Please try again.');
                }
            }
        }        
    }
}

$url = get_template_directory_uri();
get_header();
?>
<link rel="stylesheet" type="text/css" href="<?= $url ?>/dist/build/tailwind.css">
<link rel="stylesheet" type="text/css" href="<?= $url ?>/dist/build/style.css">
<div class="relative min-h-screen overflow-hidden md:py-16 sm:py-12 reset-pass text-center px-4">
    <div class="mx-auto flex justify-center items-center mb-10">
        <a href="<?= home_url() ?>"><img src="<?= $url ?>/assets/dealer/img/logo-sale-partner.png" class="w-275" alt="" /></a>
    </div>
    <div class="mx-auto relative p-8 resetpass-radius-20 bg-white w-668-full overflow-hidden border-cm">
        <h1 class="text-center text-32 font-semibold mt-0 mb-1 line-height-44">Reset Password</h1>
        <p class="text-center text-base text-will mt-0 mb-10 font-normal">We will send you an email to reset your password.</p>
        <form class="" method="POST">
            <div class="mb-4 text-left">
                <label for="email" class="block mb-1 text-base font-medium text-gray-900 dark:text-white">Email <span class="text-red-500">*</span></label>
                <input type="email" name="email" id="email" class="box-border bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-3 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" placeholder="Enter your email" autocomplete="off" />
            </div>
            <?php if (isset($error_message)): ?>
                <div class="w-full text-red-600 text-body-sm-regular mb-4 text-left">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>

            <?php if (isset($success_message)): ?>
                <div class="w-full text-green-600 text-body-sm-regular mb-4 text-left">
                    <?php echo $success_message; ?>
                </div>
            <?php endif; ?>
            <div class="grid md:gap-3 md:grid-cols-2 sm:gap-3 sm:grid-cols-2">
                <button type="submit" class="text-white bg-red-sure border-none font-medium rounded-lg text-sm w-full px-10 py-4 text-center sm-mb-10">Send email</button>
                <button type="button" class="text-gray-900 bg-gray-en border-none font-medium rounded-lg text-sm w-full px-10 py-4 text-center">Cancel</button>
            </div>
        </form>

    </div>
</div>