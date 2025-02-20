<?php /* Template Name: Sign-Up */ ?>
<?php
$authenticated_user = validate_user_token();
if (isset($_COOKIE['user_token']) && $authenticated_user->type == 1) {
    wp_redirect(home_url());
    exit;
}

$client_id = '451202130918-qkndmpg1jorcqhugj630pnr0u7sueblb.apps.googleusercontent.com';
$redirect_uri = home_url('/sign-in');
$google_login_url = 'https://accounts.google.com/o/oauth2/v2/auth?response_type=code&client_id=' . $client_id . '&redirect_uri=' . urlencode($redirect_uri) . '&scope=openid%20profile%20email&access_type=online&include_granted_scopes=true&state=dealer';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = sanitize_text_field($_POST['firstName']);
    $lastName = sanitize_text_field($_POST['lastName']);
    $email = sanitize_email($_POST['email']);
    $phone = sanitize_text_field($_POST['phone']);
    $businessName = sanitize_text_field($_POST['businessName']);
    $businessEmail = sanitize_email($_POST['businessEmail']);
    $businessNetwork = sanitize_text_field($_POST['businessNetwork']);
    $businessWebsite = sanitize_text_field($_POST['businessWebsite']);
    $plan = sanitize_text_field($_POST['plan']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];

    $errors = array();

    if (empty($firstName)) {
        $errors['firstName'] = pll__('Enter your first name');
    }
    if (empty($lastName)) {
        $errors['lastName'] = pll__('Enter your last name');
    }
    if (empty($phone)) {
        $errors['phone'] = pll__('Enter your phone');
    }
    if (empty($businessName)) {
        $errors['businessName'] = pll__('Enter your business name');
    }
    if (empty($businessNetwork)) {
        $errors['businessNetwork'] = pll__('Enter your business network');
    }
    if (empty($plan)) {
        $errors['plan'] = pll__('Enter your plan');
    }
    if (empty($email)) {
        $errors['email'] = pll__('Enter your email');
    } elseif (!is_email($email)) {
        $errors['email'] = pll__('Invalid email');
    }
    if (empty($businessEmail)) {
        $errors['businessEmail'] = pll__('Enter your business email');
    } elseif (!is_email($businessEmail)) {
        $errors['businessEmail'] = pll__('Invalid email');
    }
    if (empty($password)) {
        $errors['password'] = pll__('Enter your password');
    } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[#@$!%*?&])[A-Za-z\d#@$!%*?&]{8,}$/', $password)) {
        $errors['password'] = pll__('Minimum 8 characters including letters, numbers, uppercase, lowercase, special characters');
    }
    if (empty($confirmPassword)) {
        $errors['confirmPassword'] = pll__('Please re-enter your password');
    } elseif ($password !== $confirmPassword) {
        $errors['confirmPassword'] = pll__('Passwords do not match');
    }

    // Kiểm tra email đã tồn tại
    global $wpdb;
    $table = 'wp_account_users';

    // Kiểm tra email đã tồn tại trong wp_account_users 
    $emailExistsInAccount = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM wp_account_users WHERE email = %s", $email));

    // Kiểm tra email đã tồn tại trong wp_pending_users
    $pendingUser = $wpdb->get_row($wpdb->prepare(
        "SELECT *, TIMESTAMPDIFF(DAY, created_at, NOW()) as days_diff 
        FROM wp_pending_users 
        WHERE email = %s",
        $email
    ));

    if (!empty($email)) {
        if ($emailExistsInAccount > 0) {
            $errors['email'] = pll__('Email already exists. Please use a different email.');
        } else if ($pendingUser) {
            if ($pendingUser->days_diff >= 1) {
                // Nếu đã quá 24h, xóa bản ghi cũ
                $wpdb->delete('wp_pending_users', array('email' => $email));
            } else if ($pendingUser->resend_count >= 3) {
                // Nếu đã gửi quá 3 lần và chưa đủ 24h
                $time_until_reset = strtotime($pendingUser->created_at . ' +1 day') - time();
                $hours = floor($time_until_reset / 3600);
                $minutes = floor(($time_until_reset % 3600) / 60);

                $errors['email'] = sprintf(
                    pll__('This email is already registered and pending verification. Please wait %d hours and %d minutes before trying again.'),
                    $hours,
                    $minutes
                );
            } else {
                // Nếu chưa gửi quá 3 lần, cập nhật thông tin mới
                $current_time = current_time('mysql');
                $new_otp = sprintf("%06d", mt_rand(0, 999999));
                $otp_expired_at = date('Y-m-d H:i:s', strtotime($current_time . ' +2 minutes'));

                $wpdb->update(
                    'wp_pending_users',
                    array(
                        'first_name' => $firstName,
                        'last_name' => $lastName,
                        'phone' => $phone,
                        'business_name' => $businessName,
                        'business_email' => $businessEmail,
                        'business_network' => $businessNetwork,
                        'business_website' => $businessWebsite,
                        'plan' => $plan,
                        'password' => $hashedPassword,
                        'otp' => $new_otp,
                        'otp_expired_at' => $otp_expired_at,
                        'resend_count' => $pendingUser->resend_count + 1,
                        'last_resend_date' => $current_time
                    ),
                    array('email' => $email)
                );

                // Gửi mail OTP mới
                sendmailOTP($new_otp, $email);

                // Chuyển hướng đến trang OTP
                $encoded_email = base64_encode($email);
                wp_send_json_success(array('redirect' => home_url('/otp?email=' . $encoded_email)));
            }
        }
    }

    date_default_timezone_set('Asia/Ho_Chi_Minh');
    
    if (empty($errors)) {
        // Băm mật khẩu
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Tạo OTP 6 số ngẫu nhiên
        $otp = sprintf("%06d", mt_rand(0, 999999));

        // Tính thời gian hết hạn OTP (2 phút)
        $current_time = current_time('mysql'); // Lấy thời gian hiện tại theo WordPress timezone
        $otp_expired_at = date('Y-m-d H:i:s', strtotime($current_time . ' +2 minutes'));

        // Thêm dữ liệu vào bảng wp_pending_users
        $table = 'wp_pending_users';
        $data = array(
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'phone' => $phone,
            'business_name' => $businessName,
            'business_email' => $businessEmail,
            'business_network' => $businessNetwork,
            'business_website' => $businessWebsite,
            'plan' => $plan,
            'password' => $hashedPassword,
            'type' => 1,
            'status' => 1,
            'otp' => $otp,
            'otp_expired_at' => $otp_expired_at,
            'last_resend_date' => $current_time,
            'created_at' => $current_time
        );

        $format = array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%d', '%s', '%s', '%s', '%s');

        if ($wpdb->insert($table, $data, $format)) {
            // Gửi mail OTP
            sendmailOTP($otp, $email);

            // Chuyển hướng đến trang OTP với email được mã hóa
            $encoded_email = base64_encode($email);
            wp_send_json_success(array('redirect' => home_url('/otp?email=' . $encoded_email)));
        } else {
            $errors['general'] = pll__('An error occurred while registering. Please try again.');
        }
    }

    wp_send_json_error($errors);
}

$url = get_template_directory_uri();
get_header();
?>
<style>
    .disabled {
        opacity: 0.5;
        pointer-events: none;
    }
</style>
<section class="py-10 xl:py-12 bg-[#EEF0F6]">
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
                        Sale Partner <br>
                        Platform
                    </p>
                </a>
            </div>
        </div>
        <div class="mt-10 xl:mt-12 w-full mx-auto rounded-2xl border border-solid border-neutral-200 bg-white">
            <div class="p-8 flex flex-col gap-8">
                <div class="flex gap-6 items-center">
                    <a href="<?= home_url() ?>/sign-in" class="sign-tab">Login</a>
                    <a class="sign-tab active">Sign up</a>
                </div>
                <div class="flex flex-col items-center gap-6">
                    <form id="signUpForm" class="w-full" method="post">
                        <div class="flex flex-wrap gap-4">
                            <div class="w-full flex flex-col lg:flex-row gap-6 lg:gap-10 xl:gap-12">
                                <div class="flex flex-wrap gap-4">
                                    <p class="text-body-lg-semibold text-secondary mb-1"><?php pll_e('Contact information') ?></p>
                                    <div class="flex flex-col md:flex-row gap-4 w-full">
                                        <label class="input-label">
                                            <p class="input-title"><?php pll_e('First name') ?> <span class="text-primary">*</span></p>
                                            <input type="text" name="firstName" class="input-field" placeholder="<?php pll_e('Enter your first name') ?>" name="firstName">
                                            <p class="text-body-sm-regular text-[#FF0000] error-firstName" id="err-mes"></p>
                                        </label>
                                        <label class="input-label">
                                            <p class="input-title"><?php pll_e('Last name') ?> <span class="text-primary">*</span></p>
                                            <input type="text" name="lastName" class="input-field" placeholder="<?php pll_e('Enter your last name') ?>" name="lastName">
                                            <p class="text-body-sm-regular text-[#FF0000] error-lastName" id="err-mes"></p>
                                        </label>
                                    </div>
                                    <label class="input-label">
                                        <p class="input-title"><?php pll_e('Email') ?> <span class="text-primary">*</span></p>
                                        <input type="text" name="email" class="input-field" placeholder="<?php pll_e('Enter your email') ?>" name="email">
                                        <p class="text-body-sm-regular text-[#FF0000] error-email" id="err-mes"></p>
                                    </label>
                                    <label class="input-label">
                                        <p class="input-title"><?php pll_e('Phone number') ?> <span class="text-primary">*</span></p>
                                        <input type="text" name="phone" class="input-field" placeholder="<?php pll_e('Enter your Phone number') ?>" name="phone-number">
                                        <p class="text-body-sm-regular text-[#FF0000] error-phone" id="err-mes"></p>
                                    </label>
                                    <label class="input-label">
                                        <p class="input-title"><?php pll_e('Password') ?> <span class="text-primary">*</span></p>
                                        <div class="relative password-field">
                                            <input type="password" name="password" class="input-field" placeholder="<?php pll_e('Enter your password') ?>" name="password">
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
                                            <input type="password" name="confirmPassword" class="input-field" placeholder="Enter your password again" name="confirmPassword">
                                            <figure class="show-pw w-6 h-6 cursor-pointer absolute top-1/2 right-3" style="transform: translateY(-50%);">
                                                <img src="<?= $url ?>/assets/image/icon/Eye.svg" alt="icon">
                                            </figure>
                                            <figure class="hide-pw w-6 h-6 cursor-pointer absolute top-1/2 right-3 hidden" style="transform: translateY(-50%);">
                                                <img src="<?= $url ?>/assets/image/icon/Eye-close.svg" alt="icon">
                                            </figure>
                                        </div>
                                        <p class="text-body-sm-regular text-[#FF0000] error-confirmPassword" id="err-mes"></p>
                                    </label>
                                </div>

                                <hr class="divider vertical mobile">

                                <div class="flex flex-wrap gap-4">
                                    <p class="text-body-lg-semibold text-secondary mb-1"><?php pll_e('Business information') ?></p>

                                    <label class="input-label">
                                        <p class="input-title"><?php pll_e('Business name') ?> <span class="text-primary">*</span></p>
                                        <input type="text" name="businessName" class="input-field" placeholder="<?php pll_e('Enter your Business name') ?>" name="business-name">
                                        <p class="text-body-sm-regular text-[#FF0000] error-businessName" id="err-mes"></p>
                                    </label>
                                    <label class="input-label">
                                        <p class="input-title"><?php pll_e('Business email') ?> <span class="text-primary">*</span></p>
                                        <input type="text" name="businessEmail" class="input-field" placeholder="<?php pll_e('Enter your email') ?>" name="business-email">
                                        <p class="text-body-sm-regular text-[#FF0000] error-businessEmail" id="err-mes"></p>
                                    </label>
                                    <label class="input-label">
                                        <p class="input-title"><?php pll_e('Business network') ?> <span class="text-primary">*</span></p>
                                        <input type="text" name="businessNetwork" class="input-field" placeholder="<?php pll_e('Distribution channels, branches, etc.') ?>" name="business-network">
                                        <p class="text-body-sm-regular text-[#FF0000] error-businessNetwork" id="err-mes"></p>
                                    </label>
                                    <label class="input-label">
                                        <p class="input-title"><?php pll_e('Business website') ?></p>
                                        <input type="text" name="businessWebsite" class="input-field" placeholder="<?php pll_e('Enter your business website') ?>" name="business-website">
                                    </label>
                                    <label class="input-label">
                                        <p class="input-title"><?php pll_e('How you plan to sell our products') ?> <span class="text-primary">*</span></p>
                                        <textarea type="text" name="plan" class="input-field" name="content_review" rows="3" placeholder="<?php pll_e('How you plan to sell our products') ?>" name="info"></textarea>
                                        <p class="text-body-sm-regular text-[#FF0000] error-plan" id="err-mes"></p>
                                    </label>
                                </div>
                            </div>

                            <p class="text-body-md-regular w-full"><?php pll_e("By creating an account, you agree to SureMeal's") ?> <a href="#" class="underline text-secondary"><?php pll_e('Conditions of Use') ?></a> <?php pll_e('and') ?> <a href="#" class="underline text-secondary"><?php pll_e('Privacy Notice.') ?></a></p>
                            <button type="submit" class="mx-auto min-w-[162px] button bg-primary text-body-md-semibold text-white"><?php pll_e('Sign up') ?></button>
                            <p class="text-body-sm-regular text-[#FF0000] error-general" id="err-mes"></p>
                        </div>

                    </form>


                    <p class="text-body-md-regular text-gray-9"><?php pll_e('Already have an account?') ?> <a href="<?= home_url() ?>/sign-in"
                            class="font-medium text-secondary"><?php pll_e('Login') ?></a></p>
                    <hr class="divider">
                    <div class="w-full flex flex-col gap-4 items-center">
                        <p class="text-body-lg-medium text-gray-8"><?php pll_e('Or connect with:') ?></p>
                        <div class="w-full flex flex-col gap-3 max-w-[440px]">
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
<script>
    document.getElementById('signUpForm').addEventListener('submit', function(event) {
        event.preventDefault();

        const submitButton = document.querySelector('button[type="submit"]');
        const form = event.target;
        const formData = new FormData(form);

        submitButton.classList.add('disabled');
        submitButton.innerHTML = 'Sending...';

        // Reset error messages
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
                    // Chuyển hướng trực tiếp đến trang OTP
                    window.location.href = data.data.redirect;
                } else {
                    submitButton.innerHTML = 'Sign up';
                    submitButton.classList.remove('disabled');
                    // Display errors
                    Object.keys(data.data).forEach(key => {
                        const errorElement = form.querySelector(`.error-${key}`);
                        if (errorElement) {
                            errorElement.textContent = data.data[key];
                        }
                    });
                }
            })
            .catch(error => {
                submitButton.innerHTML = 'Sign up';
                submitButton.classList.remove('disabled');
                console.error('Error:', error);
                const generalError = form.querySelector('.error-general');
                if (generalError) {
                    generalError.textContent = 'An error occurred. Please try again.';
                }
            });
    });
    document.addEventListener('DOMContentLoaded', function() {
        // Lấy tất cả các trường password
        const passwordFields = document.querySelectorAll('.password-field');

        passwordFields.forEach(field => {
            const input = field.querySelector('input');
            const showButton = field.querySelector('.show-pw');
            const hideButton = field.querySelector('.hide-pw');

            // Xử lý sự kiện khi click vào nút hiện mật khẩu
            showButton.addEventListener('click', function() {
                input.type = 'text';
                showButton.classList.add('hidden');
                hideButton.classList.remove('hidden');
            });

            // Xử lý sự kiện khi click vào nút ẩn mật khẩu
            hideButton.addEventListener('click', function() {
                input.type = 'password';
                hideButton.classList.add('hidden');
                showButton.classList.remove('hidden');
            });
        });
    });
</script>
<script>
    window.fbAsyncInit = function() {
        FB.init({
            appId: '1027062409021492',
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

        fetch('<?= admin_url('admin-ajax.php') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams(userData)
            })
            .then(response => response.json())
            .then(data => {

                if (data.success) {
                    // Xoa storage
                    localStorage.removeItem('cart');
                    // Redirect or show success message
                    window.location.href = '<?= home_url() ?>';
                } else {
                    // Handle login failure
                    console.error('Login failed:', data.data);
                    showErrorMessage(data.data?.message || 'Login failed. Please try again.');
                }
            })
            .catch(error => {
                console.error('Network Error:', error);
                showErrorMessage('Network error. Please check your connection.');
            });
    }
    document.addEventListener('DOMContentLoaded', function() {
        initFacebookLogin();
    });
</script>