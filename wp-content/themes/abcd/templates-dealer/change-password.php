<?php /* Template Name: Dealer-Change-Password */ ?>
<?php

global $wpdb;

$token = isset($_GET['token']) ? sanitize_text_field($_GET['token']) : '';
$email = isset($_GET['email']) ? sanitize_email($_GET['email']) : '';

// Validate token
$reset_request = $wpdb->get_row($wpdb->prepare(
    "SELECT * FROM wp_dealer_password_reset_tokens 
    WHERE token = %s 
    AND expiry > %d 
    AND email = %s",
    $token, 
    time(),
    $email
));

if (!$reset_request) {
    wp_die('Invalid, expired, or already used reset token.');
}

get_header();
?>
<main class="bg-[#EEF0F6]">
    <section class="pt-12 pb-20">
        <div class="container">
            <div class="w-full max-w-[667px] mx-auto rounded-2xl border border-solid border-neutral-200 bg-white">
                <div class="p-8 flex flex-col items-center gap-8">
                    <div class="flex flex-col items-center justify-center gap-1">
                        <h2 class="text-heading-h4 text-gray-9">
                            <?php pll_e('Create New Password') ?>
                        </h2>
                    </div>
                    <form id="reset-password-form" class="w-full">
                        <input type="hidden" id="reset-token" value="<?= $token ?>">
                        <input type="hidden" id="reset-email" value="<?= esc_attr($email) ?>">
                        <div class="flex flex-col gap-4">
                            <label class="input-label">
                                <p class="input-title"><?php pll_e('New Password') ?> <span class="text-primary">*</span></p>
                                <input type="password" id="new-password" class="input-field" placeholder="<?php pll_e('Enter your new password') ?>" required>
                            </label>
                            <label class="input-label">
                                <p class="input-title"><?php pll_e('Confirm Password') ?> <span class="text-primary">*</span></p>
                                <input type="password" id="confirm-password" class="input-field" placeholder="<?php pll_e('Confirm your new password') ?>" required>
                                <p id="error-message" class="text-red-500 mt-2"></p>
                            </label>
                        </div>
                        <div class="w-full flex flex-col items-center gap-3">
                            <button type="submit" class="w-full button bg-primary text-body-md-semibold text-white">
                                <?php pll_e('Reset Password') ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('reset-password-form');
    const newPasswordInput = document.getElementById('new-password');
    const confirmPasswordInput = document.getElementById('confirm-password');
    const errorMessage = document.getElementById('error-message');

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        errorMessage.textContent = ''; // Clear previous error messages

        if (newPasswordInput.value !== confirmPasswordInput.value) {
            errorMessage.textContent = '<?php pll_e('Passwords do not match.') ?>';
            return;
        }

        const formData = new FormData();
        formData.append('action', 'dealer_reset_password');
        formData.append('token', document.getElementById('reset-token').value);
        formData.append('email', document.getElementById('reset-email').value);
        formData.append('new_password', newPasswordInput.value);
        formData.append('security', '<?php echo wp_create_nonce("dealer_reset_password_nonce"); ?>');

        fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.data.message);
                window.location.href = '<?php echo home_url('/sign-in-dealer'); ?>'; // Redirect to login page
            } else {
                errorMessage.textContent = data.data.message;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            errorMessage.textContent = '<?php pll_e('An error occurred. Please try again.') ?>';
        });
    });
});
</script>

<?php get_footer(); ?>