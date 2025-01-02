<?php /* Template Name: Sign-Up */ ?>
<?php
$authenticated_user = validate_user_token();
if (isset($_COOKIE['user_token']) && $authenticated_user) {
    wp_redirect(home_url());
    exit;
}

$client_id = '451202130918-qkndmpg1jorcqhugj630pnr0u7sueblb.apps.googleusercontent.com';
$redirect_uri = home_url('/sign-in');
$google_login_url = 'https://accounts.google.com/o/oauth2/v2/auth?response_type=code&client_id=' . $client_id . '&redirect_uri=' . urlencode($redirect_uri) . '&scope=openid%20profile%20email&access_type=online&include_granted_scopes=true';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = sanitize_text_field($_POST['firstName']);
    $lastName = sanitize_text_field($_POST['lastName']);
    $email = sanitize_email($_POST['email']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];

    $errors = array();

    if (empty($firstName)) {
        $errors['firstName'] = pll__('Enter your first name');
    }
    if (empty($lastName)) {
        $errors['lastName'] = pll__('Enter your last name');
    }
    if (empty($email)) {
        $errors['email'] = pll__('Enter your email');
    } elseif (!is_email($email)) {
        $errors['email'] = pll__('Invalid email');
    }
    if (empty($password)) {
        $errors['password'] = pll__('Enter your password');
    } elseif (strlen($password) < 6) {
        $errors['password'] = pll__('Password must have at least 6 characters');
    }
    if (empty($confirmPassword)) {
        $errors['confirmPassword'] = pll__('Please re-enter your password');
    } elseif ($password !== $confirmPassword) {
        $errors['confirmPassword'] = pll__('Passwords do not match');
    }

    // Kiểm tra email đã tồn tại
    global $wpdb;
    $table = $wpdb->prefix . 'account_users';
    $emailExists = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table WHERE email = %s", $email));

    if ($emailExists > 0) {
        $errors['email'] = pll__('Email already exists. Please use a different email.');
    }

    if (empty($errors)) {
        // Băm mật khẩu
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Thêm dữ liệu vào bảng wp_account_users
        $data = array(
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'password' => $hashedPassword
        );
        $format = array('%s', '%s', '%s', '%s');

        if ($wpdb->insert($table, $data, $format)) {
            // chuyển hướng sang đăng nhập
            wp_send_json_success(array('redirect' => home_url('/sign-in')));
        } else {
            $errors['general'] = pll__('An error occurred while registering. Please try again.');
        }
    }

    wp_send_json_error($errors);
}

$url = get_template_directory_uri();
get_header();
?>
<main class="bg-[#EEF0F6]">
    <section class="pt-12 pb-20">
        <div class="container">
            <div class="w-full max-w-[667px] mx-auto rounded-2xl border border-solid border-neutral-200 bg-white">
                <div class="p-8 flex flex-col items-center gap-8">
                    <h2 class="text-heading-h4 text-gray-9">
                        <?php pll_e('Create account') ?>
                    </h2>
                    <div class="flex flex-col items-center gap-6">
                        <form id="signUpForm" class="w-full">
                            <div class="flex flex-wrap gap-4">
                                <div class="flex flex-col md:flex-row gap-4 w-full">
                                    <label class="input-label">
                                        <p class="input-title"><?php pll_e('First name') ?> <span class="text-primary">*</span></p>
                                        <input type="text" class="input-field" placeholder="<?php pll_e('Enter your first name') ?>" name="firstName">
                                        <p class="text-body-sm-regular text-[#FF0000] error-firstName" id="err-mes"></p>
                                    </label>
                                    <label class="input-label">
                                        <p class="input-title"><?php pll_e('Last name') ?> <span class="text-primary">*</span></p>
                                        <input type="text" class="input-field" placeholder="<?php pll_e('Enter your last name') ?>" name="lastName">
                                        <p class="text-body-sm-regular text-[#FF0000] error-lastName" id="err-mes"></p>
                                    </label>
                                </div>
                                <label class="input-label">
                                    <p class="input-title"><?php pll_e('Email') ?> <span class="text-primary">*</span></p>
                                    <input type="text" class="input-field" placeholder="<?php pll_e('Enter your email') ?>" name="email">
                                    <p class="text-body-sm-regular text-[#FF0000] error-email" id="err-mes"></p>
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
                                    <p class="text-body-sm-regular text-[#FF0000] error-password" id="err-mes"></p>
                                </label>
                                <label class="input-label">
                                    <p class="input-title"><?php pll_e('Re-enter password') ?> <span class="text-primary">*</span></p>
                                    <div class="relative password-field">
                                        <input type="password" class="input-field" placeholder="Enter your password again" name="confirmPassword">
                                        <figure class="show-pw w-6 h-6 cursor-pointer absolute top-1/2 right-3" style="transform: translateY(-50%);">
                                            <img src="<?= $url ?>/assets/image/icon/Eye.svg" alt="icon">
                                        </figure>
                                        <figure class="hide-pw w-6 h-6 cursor-pointer absolute top-1/2 right-3 hidden" style="transform: translateY(-50%);">
                                            <img src="<?= $url ?>/assets/image/icon/Eye-close.svg" alt="icon">
                                        </figure>
                                    </div>
                                    <p class="text-body-sm-regular text-[#FF0000] error-confirmPassword" id="err-mes"></p>
                                </label>
                                <p class="text-body-md-regular w-full"><?php pll_e(`By creating an account, you agree to SureMeal's`) ?> <a href="#" class="underline text-secondary"><?php pll_e('Conditions of Use') ?></a> <?php pll_e('and') ?> <a href="#" class="underline text-secondary"><?php pll_e('Privacy Notice.') ?></a></p>
                                <button type="submit" class="mx-auto min-w-[162px] button bg-primary text-body-md-semibold text-white"><?php pll_e('Sign up') ?></button>
                                <p class="text-body-sm-regular text-[#FF0000] error-general" id="err-mes"></p>
                            </div>
                        </form>
                        <p class="text-body-md-regular text-gray-9"><?php pll_e('Already have an account?') ?> <a href="<?= home_url() ?>/sign-in"
                                class="font-medium text-secondary"><?php pll_e('Sign in') ?></a></p>
                        <hr class="divider">
                        <div class="w-full flex flex-col gap-4 items-center">
                            <p class="text-body-lg-medium text-gray-8"><?php pll_e('Or connect with:') ?></p>
                            <div class="w-full flex flex-col gap-3">
                                <a href="<?php echo $google_login_url; ?>" class="other-sign">
                                    <figure><img src="<?= $url ?>/assets/image/icon/sign-gg.svg" alt="sign"></figure>
                                    <p class="text-body-md-medium text-[#344054]"><?php pll_e('Continue with Google') ?></p>
                                </a>
                                <a href="#" class="other-sign" id="fb-login">
                                    <figure><img src="<?= $url ?>/assets/image/icon/sign-fb.svg" alt="sign"></figure>
                                    <p class="text-body-md-medium text-[#344054]"><?php pll_e('Continue with Facebook') ?></p>
                                </a>
                                <a href="#" class="other-sign">
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
<?php get_footer() ?>
<script>
    document.getElementById('signUpForm').addEventListener('submit', function(event) {
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
                    // Chuyển hướng đến trang đăng nhập
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

    window.fbAsyncInit = function() {
        FB.init({
            appId: '1027062409021492', // Replace with your actual Facebook App ID
            cookie: true,
            xfbml: true,
            version: 'v18.0'
        });
    };

    // Load Facebook SDK
    (function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s);
        js.id = id;
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
                        showErrorMessage('<?php pll_e('Login cancelled. Please try again.') ?>');
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
                // Prepare data to send to server
                const userData = {
                    action: 'facebook_login',
                    facebook_id: response.id,
                    first_name: response.name,
                    // Only include email if it exists
                    ...(response.email && {
                        email: response.email
                    }),
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