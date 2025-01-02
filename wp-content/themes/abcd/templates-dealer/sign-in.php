<?php /* Template Name: Sign-In-Dealer */ ?>
<?php 
$authenticated_dealer = validate_dealer_token();
if (isset($_COOKIE['dealer_token']) && $authenticated_dealer) {
    wp_redirect(home_url());
    exit;
}

$client_id = '451202130918-qkndmpg1jorcqhugj630pnr0u7sueblb.apps.googleusercontent.com';
$redirect_uri = home_url('/sign-in-dealer');
$google_login_url = 'https://accounts.google.com/o/oauth2/v2/auth?response_type=code&client_id=' . $client_id . '&redirect_uri=' . urlencode($redirect_uri) . '&scope=openid%20profile%20email&access_type=online&include_granted_scopes=true';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize_email($_POST['email']);
    $password = $_POST['password'];

    // Kiểm tra và xác thực như trước
    if (empty($errors)) {
        global $wpdb;
        $table = $wpdb->prefix . 'account_dealers';
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
                setcookie('dealer_token', $new_token, [
                    'expires' => $expire,
                    'path' => '/',
                    'domain' => '',
                    'secure' => false,
                    'httponly' => true,
                    'samesite' => 'Strict'
                ]);

                wp_send_json_success(['redirect' => home_url()]);
            } else {
                $errors['general'] = pll__('Authentication failed. Please try again.');
                wp_send_json_error($errors);
            }
        } else {
            $errors['general'] = pll__('Wrong email or password');
        }
    }

    wp_send_json_error($errors);
}

$url = get_template_directory_uri();
get_header();
?>
	<div class="relative min-h-screen overflow-hidden bg-gray-50 py-16 sm:py-12 py-4 signin text-center">
		<div class="mx-auto items-center mb-16">
			<a href="index.html"><img class="mx-auto" src="<?= $url ?>/assets/dealer/img/logo.png" class="w-64" alt="" /></a>
		</div>
		<div class="px-4">
			<div class="mx-auto relative p-8 rounded-2xl bg-white w-668-full overflow-hidden border-cm">
			<h1 class="text-center text-32 font-semibold mt-0 mb-8">Sign In</h1>
			<form id="signInForm" class="w-full">
				<div class="mb-4 text-left">
					<label for="email" class="block mb-1 text-base font-medium text-gray-900 dark:text-white">Email <span class="text-red-500">*</span></label>
					<input type="text" name="email" id="email" class="box-border bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-3 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Enter your email" autocomplete="off" />
					<p class="text-red-500 text-sm text-gray-500 mt-0 mb-4 error-email" id="err-mes"></p>
				</div> 
				<div class="mb-1 text-left" x-data="{ show: true }">
					<label for="password" class="block mb-1 text-base font-medium text-gray-900 dark:text-white">Password <span class="text-red-500">*</span></label>
					<div class="relative">
						<input :type="show ? 'password' : 'text'" name="password" id="password" class="box-border bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Enter your password" type="password" />
						<div class="absolute top-0 bottom-0 right-0 pr-3 flex items-center text-sm leading-5">
							<svg @click="show = !show" :class="{'hidden': !show, 'block':show }"  xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
							<path fill-rule="evenodd" clip-rule="evenodd" d="M12 8.25C9.92893 8.25 8.25 9.92893 8.25 12C8.25 14.0711 9.92893 15.75 12 15.75C14.0711 15.75 15.75 14.0711 15.75 12C15.75 9.92893 14.0711 8.25 12 8.25ZM9.75 12C9.75 10.7574 10.7574 9.75 12 9.75C13.2426 9.75 14.25 10.7574 14.25 12C14.25 13.2426 13.2426 14.25 12 14.25C10.7574 14.25 9.75 13.2426 9.75 12Z" fill="#9CA3AF"/>
							<path fill-rule="evenodd" clip-rule="evenodd" d="M12 3.25C7.48587 3.25 4.44529 5.9542 2.68057 8.24686L2.64874 8.2882C2.24964 8.80653 1.88206 9.28392 1.63269 9.8484C1.36564 10.4529 1.25 11.1117 1.25 12C1.25 12.8883 1.36564 13.5471 1.63269 14.1516C1.88206 14.7161 2.24964 15.1935 2.64875 15.7118L2.68057 15.7531C4.44529 18.0458 7.48587 20.75 12 20.75C16.5141 20.75 19.5547 18.0458 21.3194 15.7531L21.3512 15.7118C21.7504 15.1935 22.1179 14.7161 22.3673 14.1516C22.6344 13.5471 22.75 12.8883 22.75 12C22.75 11.1117 22.6344 10.4529 22.3673 9.8484C22.1179 9.28391 21.7504 8.80652 21.3512 8.28818L21.3194 8.24686C19.5547 5.9542 16.5141 3.25 12 3.25ZM3.86922 9.1618C5.49864 7.04492 8.15036 4.75 12 4.75C15.8496 4.75 18.5014 7.04492 20.1308 9.1618C20.5694 9.73159 20.8263 10.0721 20.9952 10.4545C21.1532 10.812 21.25 11.2489 21.25 12C21.25 12.7511 21.1532 13.188 20.9952 13.5455C20.8263 13.9279 20.5694 14.2684 20.1308 14.8382C18.5014 16.9551 15.8496 19.25 12 19.25C8.15036 19.25 5.49864 16.9551 3.86922 14.8382C3.43064 14.2684 3.17374 13.9279 3.00476 13.5455C2.84684 13.188 2.75 12.7511 2.75 12C2.75 11.2489 2.84684 10.812 3.00476 10.4545C3.17374 10.0721 3.43063 9.73159 3.86922 9.1618Z" fill="#9CA3AF"/>
							</svg>
							<svg width="24px" height="24px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="h-6 text-gray-700 hidden" fill="none" @click="show = !show" :class="{'block': !show, 'hidden':show }" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M4 4L20 20" stroke="#9CA3AF" stroke-width="2" stroke-linecap="round"></path> <path fill-rule="evenodd" clip-rule="evenodd" d="M6.22308 5.63732C4.19212 6.89322 2.60069 8.79137 1.73175 11.0474C1.49567 11.6604 1.49567 12.3396 1.73175 12.9526C3.31889 17.0733 7.31641 20 12 20C14.422 20 16.6606 19.2173 18.4773 17.8915L17.042 16.4562C15.6033 17.4309 13.8678 18 12 18C8.17084 18 4.89784 15.6083 3.5981 12.2337C3.54022 12.0835 3.54022 11.9165 3.5981 11.7663C4.36731 9.76914 5.82766 8.11625 7.6854 7.09964L6.22308 5.63732ZM9.47955 8.89379C8.5768 9.6272 7.99997 10.7462 7.99997 12C7.99997 14.2091 9.79083 16 12 16C13.2537 16 14.3728 15.4232 15.1062 14.5204L13.6766 13.0908C13.3197 13.6382 12.7021 14 12 14C10.8954 14 9.99997 13.1046 9.99997 12C9.99997 11.2979 10.3618 10.6802 10.9091 10.3234L9.47955 8.89379ZM15.9627 12.5485L11.4515 8.03729C11.6308 8.0127 11.8139 8 12 8C14.2091 8 16 9.79086 16 12C16 12.1861 15.9873 12.3692 15.9627 12.5485ZM18.5678 15.1536C19.3538 14.3151 19.9812 13.3259 20.4018 12.2337C20.4597 12.0835 20.4597 11.9165 20.4018 11.7663C19.1021 8.39172 15.8291 6 12 6C11.2082 6 10.4402 6.10226 9.70851 6.29433L8.11855 4.70437C9.32541 4.24913 10.6335 4 12 4C16.6835 4 20.681 6.92668 22.2682 11.0474C22.5043 11.6604 22.5043 12.3396 22.2682 12.9526C21.7464 14.3074 20.964 15.5331 19.9824 16.5682L18.5678 15.1536Z" fill="#9CA3AF"></path> </g></svg>
						</div>
					</div>
					<p class="text-red-500 text-sm text-gray-500 mt-0 mb-4 error-password" id="err-mes"></p>
				</div> 
				<div class="mb-6 text-right">
					<a href="<?= home_url() ?>/dealer-password-reset" class="blue-sure no-underline text-base">Forgot password?</a>
				</div>
				<div class="flex items-start">
					<div class="flex items-center h-5">
					<input id="remember" type="checkbox" value="" class="w-5 h-5 border border-gray-300 rounded bg-gray-50 focus:ring-3 focus:ring-blue-300 dark:bg-gray-700 dark:border-gray-600 dark:focus:ring-blue-600 dark:ring-offset-gray-800" />
					</div>
					<label for="remember" class="pl-2 pb-1 ms-2 text-sm text-gray-900 dark:text-gray-300">Keep me sign in</label>
				</div>
				<p class="text-left text-gray-500 mt-0 p-0 mb-6 text-sm">Check this box only when on a private device.</p>
				<button type="submit" class="text-white bg-red-sure border-none focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-base w-full sm:w-auto pl-16 pr-16 py-3 text-center my-0">Sign in</button>
				<p class="text-red-500 text-sm text-gray-500 mt-2 mb-4 text-center error-general" id="err-mes"></p>
			</form>
			<p class="text-center mt-6">New to SureMeal? <a href="<?= home_url() ?>/sign-up-dealer" class="blue-sure no-underline">Create account</a></p>
			<hr class="mb-8 mt-8 w-full border-solid border-gray-300 border">
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
<?php get_footer(); ?>
<script>
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
<script>
window.fbAsyncInit = function() {
    FB.init({
        appId      : '1027062409021492',
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
            // Prepare data to send to server
            const userData = {
                action: 'facebook_login_dealer',
                facebook_id: response.id,
                first_name: response.name,
                email: response.email,
                avatar: response.picture.data.url,
                access_token: accessToken,
                nonce: '<?= wp_create_nonce('facebook_login_dealer_nonce') ?>'
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