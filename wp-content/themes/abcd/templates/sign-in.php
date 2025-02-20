<?php /* Template Name: Sign-In */ ?>
<?php
$authenticated_user = validate_user_token();
if (isset($_COOKIE['user_token']) && $authenticated_user) {
    wp_redirect(home_url());
    exit;
}

$client_id = '451202130918-qkndmpg1jorcqhugj630pnr0u7sueblb.apps.googleusercontent.com';
$redirect_uri = home_url('/sign-in');
$google_login_url = 'https://accounts.google.com/o/oauth2/v2/auth?response_type=code&client_id=' . $client_id . '&redirect_uri=' . urlencode($redirect_uri) . '&scope=openid%20profile%20email&access_type=online&include_granted_scopes=true';
$login_error_message = get_field('login_error_message', 'option');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize_email($_POST['email']);
    $password = $_POST['password'];

    // Kiểm tra và xác thực như trước
    if (empty($errors)) {
        global $wpdb;
        $table = 'wp_account_users';
        $user = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE email = %s", $email));

        if ($user && password_verify($password, $user->password)) {
            $new_token = generate_unique_token();
            // Update token
            $update_result = $wpdb->update(
                $table,
                ['token' => $new_token],
                ['id' => $user->id],
                ['%s'],
                ['%d']
            );

            if ($update_result !== false) {
                $expire = time() + (30 * 24 * 60 * 60); // 30 ngay
                // setcookie('user_token', $new_token, [
                //     'expires' => $expire,
                //     'path' => '/',
                //     'domain' => '',
                //     'secure' => false,
                //     'httponly' => true
                // ]);
                setcookie('user_token', $new_token, $expire, '/', '.' . $_SERVER['HTTP_HOST']);
                setcookie('dealer_token', '', time() - 3600, '/', '.' . $_SERVER['HTTP_HOST']);

                wp_send_json_success(['redirect' => home_url()]);
            } else {
                $errors['general'] = pll__('Authentication failed. Please try again.');
                wp_send_json_error($errors);
            }
        } else {
            $errors['general'] = $login_error_message;
        }
    }

    wp_send_json_error($errors);
}

$url = get_template_directory_uri();
get_header();
?>
<style>
    #appleid-signin {
        display: none;
    }
</style>
<main class="bg-[#EEF0F6]">
    <section class="pt-12 pb-20">
        <div class="container">
            <div class="w-full max-w-[667px] mx-auto rounded-2xl border border-solid border-neutral-200 bg-white">
                <div class="p-8 flex flex-col items-center gap-8">
                    <h2 class="text-heading-h4 text-gray-9">
                        <?php pll_e('Login') ?>
                    </h2>
                    <div class="flex flex-col items-center gap-6">
                        <form id="signInForm" class="w-full">
                            <div class="flex flex-wrap gap-4">
                                <label class="input-label">
                                    <p class="input-title"><?php pll_e('Email') ?> <span class="text-primary">*</span></p>
                                    <input type="text" class="input-field" placeholder="<?php pll_e('Enter your email') ?>" name="email">
                                    <p class="text-[#FF0000] text-body-sm-regular error-email" id="err-mes"></p>
                                </label>
                                <label class="input-label">
                                    <p class="input-title"><?php pll_e('Password') ?> <span class="text-primary">*</span></p>
                                    <div class="relative password-field">
                                        <input type="password" class="input-field" placeholder="<?php pll_e('Enter your password') ?>" name="password">
                                        <figure class="show-pw w-6 h-6 cursor-pointer absolute top-1/2 right-3" style="transform: translateY(-50%);">
                                            <img src="<?= $url ?>/assets/image/icon/Eye.svg" alt="icon">
                                        </figure>
                                        <figure class="hide-pw w-6 h-6 cursor-pointer absolute top-1/2 right-3 hidden" style="transform: translateY(-50%);">
                                            <img src="<?= $url ?>/assets/image/icon/Eye-close.svg" alt="icon">
                                        </figure>
                                    </div>
                                    <p class="text-[#FF0000] text-body-sm-regular error-password" id="err-mes"></p>
                                </label>
                                <div class="w-full flex justify-end">
                                    <a href="<?= home_url() ?>/password-reset" class="text-body-md-regular text-secondary"><?php pll_e('Forgot password?') ?></a>
                                </div>
                                <div class="w-full">
                                    <label class="custom-checkbox">
                                        <div class="checkbox-container">
                                            <input type="checkbox">
                                            <span class="checkmark"></span>
                                        </div>
                                        <p class="input-text"><?php pll_e('Keep me Login') ?></p>
                                    </label>
                                    <p class="mt-1 text-body-sm-regular text-neutral-500"><?php pll_e('Check this box only when on a private device.') ?></p>
                                </div>
                                <button type="submit" class="mx-auto min-w-[162px] button bg-primary text-body-md-semibold text-white"><?php pll_e('Login') ?></button>
                            </div>
                            <p class="text-[#FF0000] text-body-sm-regular error-general text-center mt-2" id="err-mes"></p>
                        </form>
                        <p class="text-body-md-regular text-gray-9"><?php pll_e('New to SureMeal?') ?> <a href="<?= home_url() ?>/sign-up"
                                class="font-medium text-secondary"><?php pll_e('Create account') ?></a></p>
                        <hr class="divider">
                        <div class="w-full flex flex-col gap-4 items-center">
                            <p class="text-body-lg-medium text-gray-8"><?php pll_e('Or connect with:') ?></p>
                            <div class="w-full flex flex-col gap-3">
                                <a href="<?= $google_login_url; ?>" class="other-sign">
                                    <figure><img src="<?= $url ?>/assets/image/icon/sign-gg.svg" alt="sign"></figure>
                                    <p class="text-body-md-medium text-[#344054]"><?php pll_e('Continue with Google') ?></p>
                                </a>
                                <a href="#" class="other-sign" id="fb-login">
                                    <figure><img src="<?= $url ?>/assets/image/icon/sign-fb.svg" alt="sign"></figure>
                                    <p class="text-body-md-medium text-[#344054]"><?php pll_e('Continue with Facebook') ?></p>
                                </a>
                                <a href="#" class="other-sign sign-apple">
                                    <figure><img src="<?= $url ?>/assets/image/icon/sign-ap.svg" alt="sign"></figure>
                                    <p class="text-body-md-medium text-[#344054]"><?php pll_e('Continue with Apple') ?></p>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
<div id="appleid-signin"> </div>
<?php get_footer() ?>
<script type="text/javascript" src="https://appleid.cdn-apple.com/appleauth/static/jsapi/appleid/1/en_US/appleid.auth.js"></script>

<script>
// Initialize Facebook SDK
window.fbAsyncInit = function() {
    FB.init({
        appId      : '1027062409021492', // Facebook App ID
        cookie     : true,
        xfbml      : true,
        version    : 'v18.0'
    });
};

// Load Facebook SDK
(function(d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) return;
    js = d.createElement(s); js.id = id;
    js.src = "https://connect.facebook.net/en_US/sdk.js";
    fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));

// Function to handle Facebook login process
function initFacebookLogin() {
    // Get the Facebook login button
    const facebookLoginButton = document.querySelector('#fb-login');

    if (facebookLoginButton) {
        facebookLoginButton.addEventListener('click', function(event) {
            event.preventDefault();

            // Trigger Facebook login
            FB.login(function(response) {
                if (response.authResponse) {
                    // Successfully logged in
                    const accessToken = response.authResponse.accessToken;
                    getUserInfo(accessToken);
                } else {
                    // User cancelled login or did not fully authorize
                    console.log('User cancelled login or did not fully authorize.');
                    showErrorMessage('Login cancelled. Please try again.');
                }
            }, {
                scope: 'public_profile,email'
            });
        });
    }
}

// Function to get user info from Facebook
function getUserInfo(accessToken) {
    FB.api('/me', {
        fields: 'id,name,email,picture.type(large)',
        access_token: accessToken
    }, function(response) {
        if (response && !response.error) {
            // Validate required fields
            // if (!response.email) {
            //     showErrorMessage('Email permission is required. Please try again.');
            //     return;
            // }

            // Prepare data to send to server
            const userData = {
                action: 'facebook_login',
                facebook_id: response.id,
                first_name: response.name,
                email: response.email,
                avatar: response.picture.data.url,
                access_token: accessToken,
                nonce: '<?= wp_create_nonce('facebook_login_nonce') ?>'
            };

            // Send user data to server via fetch
            sendFacebookLoginRequest(userData);
        } else {
            console.error('Error fetching Facebook user info:', response.error);
            showErrorMessage('Failed to retrieve user information.');
        }
    });
}

// Function to send login request to server
function sendFacebookLoginRequest(userData) {
    // Show loading state
    toggleLoginButtonState(true);

    fetch('<?= admin_url('admin-ajax.php') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams(userData)
    })
    .then(response => response.json())
    .then(data => {
        // Hide loading state
        toggleLoginButtonState(false);

        if (data.success) {
            // Redirect or show success message
            window.location.href = '<?= home_url() ?>';
        } else {
            // Handle login failure
            console.error('Login failed:', data.data);
            showErrorMessage(data.data?.message || 'Login failed. Please try again.');
        }
    })
    .catch(error => {
        // Hide loading state
        toggleLoginButtonState(false);

        console.error('Network Error:', error);
        showErrorMessage('Network error. Please check your connection.');
    });
}

// Function to toggle login button state
function toggleLoginButtonState(isLoading) {
    const facebookLoginButton = document.querySelector('a.other-sign[href*="facebook"]');
    if (facebookLoginButton) {
        if (isLoading) {
            facebookLoginButton.classList.add('disabled');
            facebookLoginButton.innerHTML = `
                <div class="spinner-border" role="status">
                    <span class="sr-only"><?php pll_e('Loading...') ?></span>
                </div>
            `;
        } else {
            facebookLoginButton.classList.remove('disabled');
            facebookLoginButton.innerHTML = `
                <figure><img src="<?= $url ?>/assets/image/icon/sign-fb.svg" alt="sign"></figure>
                <p class="text-body-md-medium text-[#344054]"><?php pll_e('Continue with Facebook') ?></p>
            `;
        }
    }
}

// Function to show error messages
function showErrorMessage(message) {
    const errorContainer = document.querySelector('.error-general');
    if (errorContainer) {
        errorContainer.textContent = message;
        errorContainer.style.display = 'block';
        
        // Optional: Clear error after 5 seconds
        setTimeout(() => {
            errorContainer.textContent = '';
            errorContainer.style.display = 'none';
        }, 5000);
    }
}

// Initialize Facebook login when DOM is fully loaded
document.addEventListener('DOMContentLoaded', function() {
    initFacebookLogin();
});
</script>


<script>
    document.querySelector('.sign-apple').addEventListener('click', function(event) {
        event.preventDefault(); // Ngăn chặn hành vi mặc định (nếu cần)
        const appleSignIn = document.getElementById('appleid-signin');
        if (appleSignIn) {
            appleSignIn.click(); // Kích hoạt sự kiện "click" trên phần tử có ID "appleid-signin"
        } else {
            console.error('Element with ID "appleid-signin" not found');
        }
    });

    document.getElementById('signInForm').addEventListener('submit', function(event) {
        event.preventDefault();

        const form = event.target;
        const formData = new FormData(form);

        // Xóa tất cả các thông báo lỗi trước khi gửi
        form.querySelectorAll('#err-mes').forEach(errorElement => {
            errorElement.textContent = '';
        });

        fetch(form.action, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Chuyển hướng đến trang chủ
                    window.location.href = data.data.redirect;
                } else {
                    // Hiển thị lỗi
                    Object.keys(data.data).forEach(key => {
                        const errorElement = form.querySelector(`.error-${key}`);
                        errorElement.textContent = data.data[key];
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
    });
</script>