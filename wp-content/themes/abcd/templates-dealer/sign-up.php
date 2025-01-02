<?php /* Template Name: Sign-Up-Dealer */ ?>
<?php 
$authenticated_dealer = validate_dealer_token();
if (isset($_COOKIE['dealer_token']) && $authenticated_dealer) {
    wp_redirect(home_url());
    exit;
}

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
    $table = $wpdb->prefix . 'account_dealers';
    $emailExists = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table WHERE email = %s", $email));

    if ($emailExists > 0) {
        $errors['email'] = pll__('Email already exists. Please use a different email.');
    }

    if (empty($errors)) {
        // Băm mật khẩu
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Thêm dữ liệu vào bảng wp_account_dealers
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
            'password' => $hashedPassword
        );
        $format = array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s');

        if ($wpdb->insert($table, $data, $format)) {
            wp_send_json_success(array('status' => 'success'));
        } else {
            $errors['general'] = pll__('An error occurred while registering. Please try again.');
        }
    }

    wp_send_json_error($errors);
}

$url = get_template_directory_uri();
get_header();
?>
<div class="relative min-h-screen overflow-hidden bg-gray-50 py-16 sm:py-12 signup text-center px-4">
	<div class="mx-auto items-center mb-16">
		<a href="index.html"><img class="mx-auto" src="<?= $url ?>/assets/dealer/img/logo.png" class="w-64" alt="" /></a>
	</div>
	<div class="mx-auto relative p-8 rounded-2xl bg-white w-1360-full overflow-hidden border-cm">
		<h1 class="text-center text-32 font-semibold mt-0 mb-8">Create an account</h1>
			<form id="signUpForm" class="w-full" method="post">	
				<div class="md:visible md:flex sm:block">
					<div class="flex-1 lg:w-32 md:w-32 sm:w-full sm:mb-5 md:mb-0 xs:w-full">
						<h4 class="text-left blue-sure text-xl mt-0 mb-6">Contact information</h4>
						<div class="grid gap-4 grid-cols-2">
							<div class="mb-4 text-left">
								<label class="block mb-1 text-base font-medium text-gray-900 dark:text-white">First name</label>
								<input type="text" name="firstName" class="box-border bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Enter your first name" autocomplete="off" />
								<p class="text-red-500 text-sm text-gray-500 mt-0 mb-4 error-firstName" id="err-mes"></p>
							</div> 
							<div class="mb-4 text-left">
								<label class="block mb-1 text-base font-medium text-gray-900 dark:text-white">Last name</label>
								<input type="text" name="lastName" class="box-border bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Enter your last name" autocomplete="off" />
								<p class="text-red-500 text-sm text-gray-500 mt-0 mb-4 error-lastName" id="err-mes"></p>
							</div> 
						</div>
						<div class="grid gap-4 grid-cols-1">
							<div class="mb-4 text-left">
								<label class="block mb-1 text-base font-medium text-gray-900 dark:text-white">Phone number <span class="text-red-500">*</span></label>
								<input type="text" name="phone" class="box-border bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Enter your phone number" autocomplete="off" />
								<p class="text-red-500 text-sm text-gray-500 mt-0 mb-4 error-phone" id="err-mes"></p>
							</div> 
						</div>
						<div class="grid gap-4 grid-cols-1">
							<div class="mb-4 text-left">
								<label for="email" class="block mb-1 text-base font-medium text-gray-900 dark:text-white">Email <span class="text-red-500">*</span></label>
								<input type="text" name="email" id="email" class="box-border bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Enter your email" autocomplete="off" />
								<p class="text-red-500 text-sm text-gray-500 mt-0 mb-4 error-email" id="err-mes"></p>
							</div> 
						</div>
						<div class="grid gap-4 grid-cols-1">
							<div class="mb-0 text-left relative" x-data="{ show: true }">
								<label for="password" class="block mb-1 text-base font-medium text-gray-900 dark:text-white">Password <span class="text-red-500">*</span></label>
								<div class="relative">
									<input :type="show ? 'password' : 'text'" name="password"  id="password" class="box-border bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Enter your password" type="password" />
									<div class="absolute inset-y-10 top-5 right-0 pr-3 flex items-center text-sm leading-5">
										<svg fill="none" @click="show = !show" :class="{'hidden': !show, 'block':show }" width="24px" height="24px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path fill-rule="evenodd" clip-rule="evenodd" d="M12 8.25C9.92893 8.25 8.25 9.92893 8.25 12C8.25 14.0711 9.92893 15.75 12 15.75C14.0711 15.75 15.75 14.0711 15.75 12C15.75 9.92893 14.0711 8.25 12 8.25ZM9.75 12C9.75 10.7574 10.7574 9.75 12 9.75C13.2426 9.75 14.25 10.7574 14.25 12C14.25 13.2426 13.2426 14.25 12 14.25C10.7574 14.25 9.75 13.2426 9.75 12Z" fill="#6B7280"></path> <path fill-rule="evenodd" clip-rule="evenodd" d="M12 3.25C7.48587 3.25 4.44529 5.9542 2.68057 8.24686L2.64874 8.2882C2.24964 8.80653 1.88206 9.28392 1.63269 9.8484C1.36564 10.4529 1.25 11.1117 1.25 12C1.25 12.8883 1.36564 13.5471 1.63269 14.1516C1.88206 14.7161 2.24964 15.1935 2.64875 15.7118L2.68057 15.7531C4.44529 18.0458 7.48587 20.75 12 20.75C16.5141 20.75 19.5547 18.0458 21.3194 15.7531L21.3512 15.7118C21.7504 15.1935 22.1179 14.7161 22.3673 14.1516C22.6344 13.5471 22.75 12.8883 22.75 12C22.75 11.1117 22.6344 10.4529 22.3673 9.8484C22.1179 9.28391 21.7504 8.80652 21.3512 8.28818L21.3194 8.24686C19.5547 5.9542 16.5141 3.25 12 3.25ZM3.86922 9.1618C5.49864 7.04492 8.15036 4.75 12 4.75C15.8496 4.75 18.5014 7.04492 20.1308 9.1618C20.5694 9.73159 20.8263 10.0721 20.9952 10.4545C21.1532 10.812 21.25 11.2489 21.25 12C21.25 12.7511 21.1532 13.188 20.9952 13.5455C20.8263 13.9279 20.5694 14.2684 20.1308 14.8382C18.5014 16.9551 15.8496 19.25 12 19.25C8.15036 19.25 5.49864 16.9551 3.86922 14.8382C3.43064 14.2684 3.17374 13.9279 3.00476 13.5455C2.84684 13.188 2.75 12.7511 2.75 12C2.75 11.2489 2.84684 10.812 3.00476 10.4545C3.17374 10.0721 3.43063 9.73159 3.86922 9.1618Z" fill="#6B7280"></path> </g></svg>
										<svg width="24px" height="24px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="h-6 text-gray-700 hidden" fill="none" @click="show = !show" :class="{'block': !show, 'hidden':show }" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M4 4L20 20" stroke="#9CA3AF" stroke-width="2" stroke-linecap="round"></path> <path fill-rule="evenodd" clip-rule="evenodd" d="M6.22308 5.63732C4.19212 6.89322 2.60069 8.79137 1.73175 11.0474C1.49567 11.6604 1.49567 12.3396 1.73175 12.9526C3.31889 17.0733 7.31641 20 12 20C14.422 20 16.6606 19.2173 18.4773 17.8915L17.042 16.4562C15.6033 17.4309 13.8678 18 12 18C8.17084 18 4.89784 15.6083 3.5981 12.2337C3.54022 12.0835 3.54022 11.9165 3.5981 11.7663C4.36731 9.76914 5.82766 8.11625 7.6854 7.09964L6.22308 5.63732ZM9.47955 8.89379C8.5768 9.6272 7.99997 10.7462 7.99997 12C7.99997 14.2091 9.79083 16 12 16C13.2537 16 14.3728 15.4232 15.1062 14.5204L13.6766 13.0908C13.3197 13.6382 12.7021 14 12 14C10.8954 14 9.99997 13.1046 9.99997 12C9.99997 11.2979 10.3618 10.6802 10.9091 10.3234L9.47955 8.89379ZM15.9627 12.5485L11.4515 8.03729C11.6308 8.0127 11.8139 8 12 8C14.2091 8 16 9.79086 16 12C16 12.1861 15.9873 12.3692 15.9627 12.5485ZM18.5678 15.1536C19.3538 14.3151 19.9812 13.3259 20.4018 12.2337C20.4597 12.0835 20.4597 11.9165 20.4018 11.7663C19.1021 8.39172 15.8291 6 12 6C11.2082 6 10.4402 6.10226 9.70851 6.29433L8.11855 4.70437C9.32541 4.24913 10.6335 4 12 4C16.6835 4 20.681 6.92668 22.2682 11.0474C22.5043 11.6604 22.5043 12.3396 22.2682 12.9526C21.7464 14.3074 20.964 15.5331 19.9824 16.5682L18.5678 15.1536Z" fill="#9CA3AF"></path> </g></svg>
									</div>
									<p class="text-red-500 text-sm text-gray-500 mt-0 mb-4 error-password" id="err-mes"></p>
								</div>
							</div>
						</div>
						<div class="grid gap-4 grid-cols-1">
							<div class="text-left relative" x-data="{ show: true }">
								<label for="re-password" class="block mb-1 text-base font-medium text-gray-900 dark:text-white">Re-enter password <span class="text-red-500">*</span></label>
								<div class="relative">
									<input :type="show ? 'password' : 'text'" name="confirmPassword" id="re-password" class="box-border bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Enter your password" type="password" />
									<div class="absolute inset-y-12 top-5 right-0 pr-3 flex items-center text-sm leading-5">
										<svg fill="none" @click="show = !show" :class="{'hidden': !show, 'block':show }" width="24px" height="24px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path fill-rule="evenodd" clip-rule="evenodd" d="M12 8.25C9.92893 8.25 8.25 9.92893 8.25 12C8.25 14.0711 9.92893 15.75 12 15.75C14.0711 15.75 15.75 14.0711 15.75 12C15.75 9.92893 14.0711 8.25 12 8.25ZM9.75 12C9.75 10.7574 10.7574 9.75 12 9.75C13.2426 9.75 14.25 10.7574 14.25 12C14.25 13.2426 13.2426 14.25 12 14.25C10.7574 14.25 9.75 13.2426 9.75 12Z" fill="#6B7280"></path> <path fill-rule="evenodd" clip-rule="evenodd" d="M12 3.25C7.48587 3.25 4.44529 5.9542 2.68057 8.24686L2.64874 8.2882C2.24964 8.80653 1.88206 9.28392 1.63269 9.8484C1.36564 10.4529 1.25 11.1117 1.25 12C1.25 12.8883 1.36564 13.5471 1.63269 14.1516C1.88206 14.7161 2.24964 15.1935 2.64875 15.7118L2.68057 15.7531C4.44529 18.0458 7.48587 20.75 12 20.75C16.5141 20.75 19.5547 18.0458 21.3194 15.7531L21.3512 15.7118C21.7504 15.1935 22.1179 14.7161 22.3673 14.1516C22.6344 13.5471 22.75 12.8883 22.75 12C22.75 11.1117 22.6344 10.4529 22.3673 9.8484C22.1179 9.28391 21.7504 8.80652 21.3512 8.28818L21.3194 8.24686C19.5547 5.9542 16.5141 3.25 12 3.25ZM3.86922 9.1618C5.49864 7.04492 8.15036 4.75 12 4.75C15.8496 4.75 18.5014 7.04492 20.1308 9.1618C20.5694 9.73159 20.8263 10.0721 20.9952 10.4545C21.1532 10.812 21.25 11.2489 21.25 12C21.25 12.7511 21.1532 13.188 20.9952 13.5455C20.8263 13.9279 20.5694 14.2684 20.1308 14.8382C18.5014 16.9551 15.8496 19.25 12 19.25C8.15036 19.25 5.49864 16.9551 3.86922 14.8382C3.43064 14.2684 3.17374 13.9279 3.00476 13.5455C2.84684 13.188 2.75 12.7511 2.75 12C2.75 11.2489 2.84684 10.812 3.00476 10.4545C3.17374 10.0721 3.43063 9.73159 3.86922 9.1618Z" fill="#6B7280"></path> </g></svg>
										<svg width="24px" height="24px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="h-6 text-gray-700 hidden" fill="none" @click="show = !show" :class="{'block': !show, 'hidden':show }" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M4 4L20 20" stroke="#9CA3AF" stroke-width="2" stroke-linecap="round"></path> <path fill-rule="evenodd" clip-rule="evenodd" d="M6.22308 5.63732C4.19212 6.89322 2.60069 8.79137 1.73175 11.0474C1.49567 11.6604 1.49567 12.3396 1.73175 12.9526C3.31889 17.0733 7.31641 20 12 20C14.422 20 16.6606 19.2173 18.4773 17.8915L17.042 16.4562C15.6033 17.4309 13.8678 18 12 18C8.17084 18 4.89784 15.6083 3.5981 12.2337C3.54022 12.0835 3.54022 11.9165 3.5981 11.7663C4.36731 9.76914 5.82766 8.11625 7.6854 7.09964L6.22308 5.63732ZM9.47955 8.89379C8.5768 9.6272 7.99997 10.7462 7.99997 12C7.99997 14.2091 9.79083 16 12 16C13.2537 16 14.3728 15.4232 15.1062 14.5204L13.6766 13.0908C13.3197 13.6382 12.7021 14 12 14C10.8954 14 9.99997 13.1046 9.99997 12C9.99997 11.2979 10.3618 10.6802 10.9091 10.3234L9.47955 8.89379ZM15.9627 12.5485L11.4515 8.03729C11.6308 8.0127 11.8139 8 12 8C14.2091 8 16 9.79086 16 12C16 12.1861 15.9873 12.3692 15.9627 12.5485ZM18.5678 15.1536C19.3538 14.3151 19.9812 13.3259 20.4018 12.2337C20.4597 12.0835 20.4597 11.9165 20.4018 11.7663C19.1021 8.39172 15.8291 6 12 6C11.2082 6 10.4402 6.10226 9.70851 6.29433L8.11855 4.70437C9.32541 4.24913 10.6335 4 12 4C16.6835 4 20.681 6.92668 22.2682 11.0474C22.5043 11.6604 22.5043 12.3396 22.2682 12.9526C21.7464 14.3074 20.964 15.5331 19.9824 16.5682L18.5678 15.1536Z" fill="#9CA3AF"></path> </g></svg>
									</div>
									<p class="text-red-500 text-sm text-gray-500 mt-0 mb-4 error-confirmPassword" id="err-mes"></p>
								</div>
							</div>
						</div>
					</div>
					<div class="flex-none lg:w-32 md:w-32 text-center hidden md:block"><div class="line-center m-auto"></div></div>
					<div class="flex-1 lg:w-32 md:w-32 sm:w-full">
						<h4 class="text-left blue-sure text-xl mt-0 mb-6">Business information</h4>
						<div class="mb-4 text-left">
							<label class="block mb-1 text-base font-medium text-gray-900 dark:text-white">Business name <span class="text-red-500">*</span></label>
							<input type="text" name="businessName" class="box-border bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Enter your business name" autocomplete="off" />
							<p class="text-red-500 text-sm text-gray-500 mt-0 mb-4 error-businessName" id="err-mes"></p>
						</div> 
						<div class="mb-4 text-left">
							<label for="email_bussiness" class="block mb-1 text-base font-medium text-gray-900 dark:text-white">Business email <span class="text-red-500">*</span></label>
							<input type="text" name="businessEmail" id="email_bussiness" class="box-border bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Enter your email" autocomplete="off" />
							<p class="text-red-500 text-sm text-gray-500 mt-0 mb-4 error-businessEmail" id="err-mes"></p>
						</div>
						<div class="mb-4 text-left">
							<label class="block mb-1 text-base font-medium text-gray-900 dark:text-white">Business network <span class="text-red-500">*</span></label>
							<input type="text" name="businessNetwork" class="box-border bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Distribution channels, branches, etc." autocomplete="off" />
							<p class="text-red-500 text-sm text-gray-500 mt-0 mb-4 error-businessNetwork" id="err-mes"></p>
						</div> 
						<div class="mb-4 text-left">
							<label class="block mb-1 text-base font-medium text-gray-900 dark:text-white">Business website</label>
							<input type="text" name="businessWebsite" class="box-border bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Enter your business website" autocomplete="off" />
						</div> 
						<div class="mb-0 text-left">
							<label class="block mb-1 text-base font-medium text-gray-900 dark:text-white">How you plan to sell our products <span class="text-red-500">*</span></label>
							<textarea name="plan" class="box-border bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 resize-none" placeholder="Enter your email"  name="" id="" placeholder="How you plan to sell our products" rows="3"></textarea>
							<p class="text-red-500 text-sm text-gray-500 mt-0 mb-4 error-plan" id="err-mes"></p>
						</div>
					</div>
				</div>
				<p class="text-left checkout-color-text my-6 p-0 text-base">By creating an account, you agree to SureMeal's <a href="#" class="blue-sure">Conditions of Use</a> and <a href="#" class="blue-sure"> Privacy Notice</a>.</p>
				<button type="submit" data-modal-target="popup-modal" data-modal-toggle="popup-modal" class="text-white bg-red-sure border-none hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm w-full sm:w-auto px-10 py-3 text-center mb-4">Sign up</button>
				<p class="text-red-500 text-sm text-gray-500 mt-0 mb-4 text-center error-general" id="err-mes"></p>
				<div id="modal-overlay" class="hidden fixed inset-0 bg-[#F2F3F5] z-40"></div>
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
			</form>
		<p class="text-center mx-0 my-0">Already have an account? <a href="<?= home_url() ?>/sign-in-dealer" class="blue-sure no-underline">Sign in</a></p>
		<hr class="my-8 w-full border-solid border-gray-300 border">
		<div class="w-full flex flex-col gap-4 items-center">
            <p class="text-body-lg-medium text-gray-8"><?php pll_e('Or connect with:') ?></p>
            <div class="w-400 flex flex-col gap-3">
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
<?php get_footer(); ?>
<script>
document.getElementById('signUpForm').addEventListener('submit', function(event) {
    event.preventDefault();

    const form = event.target;
    const formData = new FormData(form);

    // Clear all error messages before sending
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
            // Show both overlay and modal
            const overlay = document.getElementById('modal-overlay');
            const modal = document.getElementById('popup-modal');
            
            if (overlay && modal) {
                // Show overlay
                overlay.classList.remove('hidden');
                overlay.classList.add('block');
                
                // Show modal
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }
        } else {
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
        console.error('Error:', error);
        showErrorMessage('An error occurred. Please try again.');
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