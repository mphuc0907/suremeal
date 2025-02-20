<?php
/* Template Name: OTP */
?>
<?php
if (!isset($_GET['email'])) {
    wp_redirect(home_url());
    exit;
}

$encoded_email = $_GET['email'];
$email = base64_decode($encoded_email);

global $wpdb;
$user = $wpdb->get_row($wpdb->prepare(
    "SELECT last_resend_date FROM wp_pending_users WHERE email = %s",
    $email
));
$remaining_seconds = 0;
if ($user) {
    $last_resend = strtotime($user->last_resend_date);
    $current_time = time();
    $elapsed_seconds = $current_time - $last_resend;
    $remaining_seconds = 120 - $elapsed_seconds; // 120 seconds = 2 minutes
}
// echo 'Last resend' . $last_resend . '<br>';
// echo 'Current time' . $current_time . '<br>';
// echo 'Elapsed second' . $elapsed_seconds . '<br>';
// echo 'Remaining second' . $remaining_seconds . '<br>';
// var_dump($user);


// Xử lý submit OTP
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $otp = '';
    // Ghép 6 số OTP từ form
    for($i = 1; $i <= 6; $i++) {
        $otp .= $_POST['otp_' . $i];
    }
    
    global $wpdb;
    
    // Kiểm tra OTP có hợp lệ không
    $pending_user = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM wp_pending_users 
        WHERE email = %s 
        AND otp = %s 
        AND CONVERT_TZ(otp_expired_at, '+00:00', '+07:00') > NOW()",
        $email,
        $otp
    ));
    
    if ($pending_user) {
        $expire = time() + (30 * 24 * 60 * 60);
        $new_token = generate_unique_token();
        // Chuyển data sang bảng account_users
        $wpdb->insert('wp_account_users', array(
            'first_name' => $pending_user->first_name,
            'last_name' => $pending_user->last_name,
            'email' => $pending_user->email,
            'phone' => $pending_user->phone,
            'business_name' => $pending_user->business_name,
            'business_email' => $pending_user->business_email, 
            'business_network' => $pending_user->business_network,
            'business_website' => $pending_user->business_website,
            'plan' => $pending_user->plan,
            'password' => $pending_user->password,
            'token' => $new_token,
            'type' => $pending_user->type,
            'status' => $pending_user->status,
            'created_at' => current_time('mysql')
        ));
        setcookie('user_token', $new_token, $expire, '/', str_replace(platform, '', $_SERVER['HTTP_HOST']));
        setcookie('dealer_token', '', time() - 3600, '/', str_replace(platform, '', $_SERVER['HTTP_HOST']));

        // Xóa record trong pending_users
        $wpdb->delete('wp_pending_users', array('email' => $email));
        
        wp_send_json_success(array('redirect' => home_url('/sign-in')));
    } else {
        wp_send_json_error(array('message' => 'Invalid or expired OTP'));
    }
}

$url = get_template_directory_uri();
get_header('dealer1');
?>
<script>
var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
var initialTimeLeft = <?php echo $remaining_seconds; ?>;
</script>
    <div class="relative min-h-screen overflow-hidden bg-gray-50 py-16 sm:py-12 reset-pass text-center px-4">
        <div class="mx-auto items-center mb-16">
            <a href="<?= home_url() ?>"><img src="<?= $url ?>/assets/dealer/img/logo-sale-partner.png" class="w-64" alt="" /></a>
        </div>
        <div class="mx-auto relative p-8 rounded-2xl bg-white w-500-full overflow-hidden border-cm">
            <h1 class="text-center text-32 font-semibold mt-0 mb-2">Account Verification</h1>
            <p class="text-center text-base otp-color mt-0 mb-6">You have successfully registered as a dealer, please check your email inbox to receive the OTP for account verification and log in.</p>
            <form class="" action="#" method="">
                <div class="mb-6 text-left">
                    <div class="flex">
                        <div class="countdown"></div>
                        <div class="timer ml-auto order-2 text-base text-arm" x-data="timer(new Date().setDate(new Date().getDate() + 1))" x-init="init();">
                            <span class="text-arm text-baset" x-text="time().minutes"></span><span class="text-arm"></span><span class="text-arm text-base" x-text="time().seconds"></span>
                        </div>
                        <label class="block mb-1 text-base font-medium checkout-color-text">Enter OTP here</label>
                    </div>
                    <div class="flex">
                        <input type="number" class="box-border bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white text-center text-base font-medium" placeholder="-" autocomplete="off" maxlength="4" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" value=""/>
                        <input type="number" class="box-border bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue- mx-2 text-center text-base font-medium" placeholder="-" autocomplete="off" maxlength="4" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" value=""/>
                        <input type="number" class="box-border bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 text-center text-base font-medium" placeholder="-" autocomplete="off" maxlength="4" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" value=""/>
                        <input type="number" class="box-border bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 mx-2 text-center text-base font-medium" placeholder="-" autocomplete="off" maxlength="4" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" value=""/>
                        <input type="number" class="box-border bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 mr-2 text-center text-base font-medium" placeholder="-" autocomplete="off" maxlength="4" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" value=""/>
                        <input type="number" class="box-border bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 text-center text-base font-medium" placeholder="-" autocomplete="off" maxlength="4" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" value=""/>
                    </div>
                </div>
                <p class="text-red-500 text-sm mt-2 error"></p>
                <p class="text-center otp-color-2 mt-0 p-0 mb-6 text-base font-normal">
                    Didn't receive the OTP? 
                    <a href="#" class="otp-color-3 resend-link" data-email="<?php echo $_GET['email']; ?>">Resend</a>
                </p>
                <button type="submit" class="text-white bg-red-sure border-none bg-red-sure font-medium rounded-lg text-sm w-full px-10 py-4 text-center">Submit</button>
                <div id="popup-modal" tabindex="-1" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
                    <div class="relative w-full max-w-3xl max-h-full">
                        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700 p-10">
                            <p class="text-xl mt-0 mb-6 text-center text-32 font-bold signup-color-1">Welcome to SureMeal's Dealers Platform</p>
                            <p class="text-center text-base mb-8 signup-color-1 font-normal">You have successfully registered as a sale partner. Your registration request is pending approval by SureMeal. Please check your email for the approval result and to log in to the website.</p>
                            <div class="flex items-center justify-center">
                                <a href="<?= home_url() ?>" class="text-white bg-red-sure border-none hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm w-full sm:w-auto px-20 py-3 text-center dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800 no-underline">Go to homepage</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-900/50 dark:bg-gray-900/80 fixed inset-0 z-40 hidden"></div>
            </form>
        </div>
    </div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const error = document.querySelector('.error');
    const timerDisplay = document.querySelector('.timer');
    const resendLink = document.querySelector('.resend-link');
    const originalResendText = resendLink.textContent;
    let timeLeft = initialTimeLeft; // 2 minutes in seconds
    let timerInterval;

    function updateTimer() {
        clearInterval(timerInterval);
        
        // Nếu không còn thời gian chờ, enable resend link ngay
        if (timeLeft <= 0) {
            timerDisplay.textContent = '00:00';
            resendLink.classList.remove('disabled');
            return;
        }

        // Disable resend link và bắt đầu đếm ngược
        resendLink.classList.add('disabled');
        
        timerInterval = setInterval(() => {
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            timerDisplay.textContent = `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
            
            if (timeLeft > 0) {
                timeLeft--;
            } else {
                clearInterval(timerInterval);
                resendLink.classList.remove('disabled');
            }
        }, 1000);
    }
    
    // Khởi tạo timer
    updateTimer();
    
    // OTP input handling
    const inputs = document.querySelectorAll('input[type="number"]');
    
    inputs.forEach((input, index) => {
        input.addEventListener('input', function(e) {
            if (this.value.length >= 1) {
                this.value = this.value.slice(0, 1);
                if (index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }
            }
        });
        
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Backspace' && this.value === '') {
                if (index > 0) {
                    inputs[index - 1].focus();
                }
            }
        });

        input.addEventListener('paste', (event) => {
            event.preventDefault();
            const pasteData = (event.clipboardData || window.clipboardData).getData('text');
            
            if (/^\d+$/.test(pasteData)) {
                const numbers = pasteData.split('');
                numbers.forEach((num, i) => {
                    if (index + i < inputs.length) {
                        inputs[index + i].value = num;
                    }
                });
                const lastInputIndex = Math.min(index + numbers.length, inputs.length) - 1;
                inputs[lastInputIndex].focus();
            }
        });
    });
    
    // Form submission
    document.querySelector('form').addEventListener('submit', function(e) {
        e.preventDefault();
        error.textContent = ''; // Clear previous error
        
        const formData = new FormData();
        inputs.forEach((input, index) => {
            formData.append(`otp_${index + 1}`, input.value);
        });
        
        fetch(window.location.href, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Create account successfully!');
                window.location.href = data.data.redirect;
            } else {
                error.textContent = data.data.message;
            }
        })
        .catch(err => {
            error.textContent = 'An error occurred. Please try again.';
        });
    });
    
    // Resend OTP
    resendLink.addEventListener('click', function(e) {
        e.preventDefault();
        
        if (this.classList.contains('disabled')) {
            return;
        }

        const formData = new FormData();
        formData.append('action', 'resend_otp');
        formData.append('email', this.dataset.email);

        resendLink.classList.add('disabled');
        resendLink.textContent = 'Sending...';
        error.textContent = ''; 

        fetch(ajaxurl, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Reset timer
                timeLeft = 120;
                updateTimer();
                
                // Show success message
                error.textContent = 'OTP has been resent to your email';
                error.style.color = 'green';
            } else {
                error.textContent = data.data.message;
                error.style.color = 'red';
            }
        })
        .catch(err => {
            error.textContent = 'An error occurred. Please try again.';
            error.style.color = 'red';
        })
        .finally(() => {
            resendLink.textContent = originalResendText;
        });
    });
});

// Add CSS for disabled state
const style = document.createElement('style');
style.textContent = `
    .resend-link.disabled {
        opacity: 0.5;
        pointer-events: none;
    }
    .error {
        margin-top: 0.5rem;
        font-size: 0.875rem;
    }
`;
document.head.appendChild(style);
</script>