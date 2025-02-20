<?php /* Template Name: Dealer-Password-Reset */ ?>
<?php 

global $wpdb;
$table_name = $wpdb->prefix . 'account_dealers';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize_email($_POST['email']);

    if (!is_email($email)) {
        $error_message = pll__('Invalid email address');
    } else {
        global $wpdb;
        $user = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE email = %s",
            $email
        ));

        if (!$user) {
            $error_message = pll__('No user found with this email address');
        } else {
            // Kiểm tra nếu đã có token tồn tại
            $existing_token = $wpdb->get_row($wpdb->prepare(
                "SELECT * FROM wp_dealer_password_reset_tokens WHERE email = %s AND id = %d",
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
                        'wp_dealer_password_reset_tokens',
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
                        'wp_dealer_password_reset_tokens',
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
                    ], home_url('/dealer-change-password'));
        
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
<main class="bg-[#EEF0F6]">
    <section class="pt-12 pb-20">
        <div class="container">
            <div class="flex items-center justify-center gap-4 mb-16">
                <div class="flex items-center gap-4">
                    <a href="<?= home_url() ?>">
                        <figure class="max-w-[128px]">
                            <img class="" src="<?= $url ?>/assets/image/logo.png" class="" alt="logo" />
                        </figure>
                    </a>
                    <hr class="divider vertical min-h-16" style="background: #030203 ;">
                    <a href="#">
                        <p class="text-[26px] font-semibold leading-[123%] text-secondary">
                            Dealers <br>
                            Platform
                        </p>
                    </a>
                </div>
            </div>
            <div class="w-full max-w-[667px] mx-auto rounded-2xl border border-solid border-neutral-200 bg-white">
                <div class="p-8 flex flex-col items-center gap-8">
                    <div class="flex flex-col items-center justify-center gap-1">
                        <h2 class="text-heading-h4 text-gray-9">
                            <?php pll_e('Reset password') ?>
                        </h2>
                        <p class="text-body-md-regular text-neutral-600"><?php pll_e('We will send you an email to reset your password.') ?></p>
                    </div>

                <div class="flex flex-col items-center gap-6">
                    <form method="POST" class="w-full">
                        <div class="flex flex-wrap gap-3">
                            <label class="input-label">
                                <p class="input-title"><?php pll_e('Email') ?> <span class="text-primary">*</span></p>
                                <input type="email" name="email" class="input-field" placeholder="<?php pll_e('Enter your email') ?>" required>
                            </label>
                            <?php if (isset($error_message)): ?>
                                <div class="w-full text-[#FF0000] text-body-sm-regular">
                                    <?php echo $error_message; ?>
                                </div>
                            <?php endif; ?>

                            <?php if (isset($success_message)): ?>
                                <div class="w-full text-[#16a34a] text-body-sm-regular">
                                    <?php echo $success_message; ?>
                                </div>
                            <?php endif; ?>

                            <div class="w-full flex flex-col md:flex-row items-center gap-3">
                                <button type="submit" class="w-full md:w-1/2 button bg-primary text-body-md-semibold text-white">
                                    <?php pll_e('Send email') ?>
                                </button>
                                <a href="<?= home_url('/sign-in-dealer') ?>" class="w-full md:w-1/2 button bg-neutral-200 text-body-md-semibold text-gray-8">
                                    <?php pll_e('Cancel') ?>
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
</main>
<?php get_footer() ?>