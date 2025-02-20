<?php /* Template Name: Dealer-Business-Info */ ?>
<?php 
$authenticated_dealer = validate_dealer_token();
if (!(isset($_COOKIE['dealer_token']) && $authenticated_dealer)) {
    wp_redirect(home_url());
    exit;
}

global $wpdb;
$table_name = $wpdb->prefix . 'account_dealers';

// Lấy thông tin người dùng
$business_name = $authenticated_dealer->business_name;
$business_email = $authenticated_dealer->business_email;
$business_network = $authenticated_dealer->business_network;
$business_website = $authenticated_dealer->business_website;
$plan = $authenticated_dealer->plan;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $errors = [];

    $business_name = sanitize_text_field($_POST['business_name']);
    $business_email = sanitize_text_field($_POST['business_email']);
    $business_network = sanitize_text_field($_POST['business_network']);
    $business_website = sanitize_text_field($_POST['business_website']);
    $plan = sanitize_text_field($_POST['plan']);

    if (empty($business_name)) {
        $errors['business_name'] = 'Enter your business name';
    }

    if (empty($business_email)) {
        $errors['business_email'] = 'Enter your business email';
    }

    if (empty($business_network)) {
        $errors['business_network'] = 'Enter your business network';
    }

    if (empty($plan)) {
        $errors['plan'] = 'Enter your plan';
    }

    if (empty($errors)) {
        $update_data = [
            'business_name' => $business_name,
            'business_email' => $business_email,
            'business_network' => $business_network,
            'business_website' => $business_website,
            'plan' => $plan
        ];

        // Thực hiện cập nhật
        $result = $wpdb->update(
            $table_name,
            $update_data,
            ['token' => $_COOKIE['dealer_token']]
        );

        if ($result !== false) {
            // Redirect with success message
            wp_redirect(add_query_arg('update', 'success', get_permalink()));
            exit;
        }
    }
}

$url = get_template_directory_uri();
get_header();
?>
<div class="relative">
    <div class="grid 2xl:grid-cols-7 xl:grid-cols-7 md:grid-cols-8 sm:grid-cols-1 grid-cols-1 gap-0">
        <div class="flex items-start col-span-1 2xl:col-span-1 xl:col-span-1 md:col-span-2 grid bg-white md:h-screen height-full">
            <ul class="list-none pl-0 text-left mt-0 memubar" x-data="{ top: true }">
                <li class="flex items-center pt-4 pb-4 pl-6 item-f my-order">
                <a class="flex items-center no-underline menu-item" href="<?= home_url() ?>/dealer-order-info"><img class="mr-3 w-6 default" src="<?= $url ?>/assets/dealer/img/cart.png" alt=""><img class="mr-3 w-6 active hidden" src="<?= $url ?>/assets/dealer/img/cart_ac.png " alt=""><span class="menu-item">My orders</span></a>
                </li>
                <li class="pt-4 pb-4 pl-6 item-f account checkout-menu-active" >
                <div class="flex items-center no-underline w-full" href="" x-on:click="top = ! top"><img class="mr-3 w-6 default hidden" src="<?= $url ?>/assets/dealer/img/circle.png" alt=""><img class="mr-3 w-6 active" src="<?= $url ?>/assets/dealer/img/circle_ac.png" alt="">
                    <span class="menu-item cursor-pointer">Account<br>information</span>
                    <div class="order-2 ml-auto mr-4">
                        <img class="w-6" :src="top ? '<?= $url ?>/assets/dealer/img/icon_down.png' : '<?= $url ?>/assets/dealer/img/icon_up.png'"  alt="">
                    </div>
                </div>
                </li>
                <li class="pl-6">
                <ul class="list-none" x-show="top">
                    <li class="mt-2">
                        <a class="no-underline text-sm" href="<?= home_url() ?>/dealer-personal-informmation">Personal information</a>
                    </li>
                    <li class="mt-2">
                        <a class="no-underline text-sm blue-sure" href="<?= home_url() ?>/dealer-business-informmation">Business information</a>
                    </li>
                </ul>
                </li>
            </ul>
        </div>
        <div class="col-span-6 text-center md:p-8 p-4">
            <div class="mx-auto relative w-1192-full overflow-hidden border-cm">
                <div class="mb-6">
                    <h4 class="text-left m-0 color-vector text-2xl font-medium">Business information</h4>
                </div>
                <div class="bg-white info-border-radius md:px-10 px-4 md:py-14 py-4">
                    <form method="POST" class="form-edit">
                        <input type="hidden" name="update_profile" value="1">
                        <div class="grid md:gap-10 gap-4 md:grid-cols-2 grid-cols-1 mb-6">
                            <div class="text-left">
                                <label class="block mb-1 text-base font-medium text-gray-900 dark:text-dark">Business name</label>
                                <input type="text" name="business_name" value="<?= $business_name ?>" class="box-border bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-dark dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Account Number" autocomplete="off">
                                <?php if (isset($errors['business_name'])): ?>
                                    <p class="validation-error text-justify text-[#FF0000] text-sm mt-1"><?= $errors['business_name'] ?></p>
                                <?php endif; ?>
                            </div>
                            <div class="text-left">
                                <label class="block mb-1 text-base font-medium text-gray-900 dark:text-white">Business email</label>
                                <input type="email" name="business_email" value="<?= $business_email ?>" class="box-border bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-dark dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Enter information here" autocomplete="off">
                                <?php if (isset($errors['business_email'])): ?>
                                    <p class="validation-error text-justify text-[#FF0000] text-sm mt-1"><?= $errors['business_email'] ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="grid md:gap-10 gap-4 md:grid-cols-2 grid-cols-1 mb-6">
                            <div class="text-left">
                                <label class="block mb-1 text-base font-medium text-gray-900 dark:text-dark">Business network</label>
                                <input type="text" name="business_network" value="<?= $business_network ?>" class="box-border bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-dark dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Enter information here" autocomplete="off">
                                <?php if (isset($errors['business_network'])): ?>
                                    <p class="validation-error text-justify text-[#FF0000] text-sm mt-1"><?= $errors['business_network'] ?></p>
                                <?php endif; ?>
                            </div>
                            <div class="text-left">
                                <label class="block mb-1 text-base font-medium text-gray-900 dark:text-dark">Business website</label>
                                <input type="text" name="business_website" value="<?= $business_website ?>" class="box-border bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-dark dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Enter information here" autocomplete="off">
                                <?php if (isset($errors['business_website'])): ?>
                                    <p class="validation-error text-justify text-[#FF0000] text-sm mt-1"><?= $errors['business_website'] ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="grid md:gap-10 gap-4 md:grid-cols-2 grid-cols-1 mb-10">
                            <div class="text-left">
                                <label class="block mb-1 text-base font-medium text-gray-900 dark:text-dark">How you plan to sell our products</label>
                                <textarea name="plan" class="box-border bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-dark dark:focus:ring-blue-500 dark:focus:border-blue-500 resize-none" placeholder="How you plan to sell our products" id="plan-to-sell" rows="3"><?= $plan ?></textarea>
                                <?php if (isset($errors['plan'])): ?>
                                    <p class="validation-error text-justify text-[#FF0000] text-sm mt-1"><?= $errors['plan'] ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <button id="edit-update" class="text-white bg-red-sure border-none font-medium rounded-lg text-base w-full sm:w-auto pl-16 pr-16 py-3 text-center my-0">Save information</button>
                        </div>
                    </form>
                </div>
            </div>
        </div> 
    </div>
</div>
<?php get_footer() ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form_edit = document.querySelector('.form-edit');
        const updateButton = document.getElementById('edit-update');

        updateButton.addEventListener('click', function(event) {
            event.preventDefault(); 

            // Clear existing error messages
            const existingErrors = document.querySelectorAll('.validation-error');
            existingErrors.forEach(error => error.remove());

            let hasError = false;

            // Validate
            const businessNameInput = document.querySelector('input[name="business_name"]');
            const businessEmailInput = document.querySelector('input[name="business_email"]');
            const businessNetworkInput = document.querySelector('input[name="business_network"]');
            const planInput = document.querySelector('#plan-to-sell');
            
            if (!businessNameInput.value.trim()) {
                createErrorMessage(businessNameInput, 'Enter your business name');
                hasError = true;
            }
            
            if (!businessEmailInput.value.trim()) {
                createErrorMessage(businessEmailInput, 'Enter your business email');
                hasError = true;
            }
            
            if (!businessNetworkInput.value.trim()) {
                createErrorMessage(businessNetworkInput, 'Enter your business network');
                hasError = true;
            }
            
            if (!planInput.value.trim()) {
                createErrorMessage(planInput, 'Enter your plan');
                hasError = true;
            }

            if(hasError == false){
                form_edit.submit();
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: 'Updated successfully!'
                })
            }
            
        });

        function createErrorMessage(inputElement, message) {
            const errorElement = document.createElement('p');
            errorElement.classList.add('validation-error', 'text-justify', 'text-[#FF0000]', 'text-sm', 'mt-1');
            errorElement.textContent = message;
            inputElement.parentNode.insertBefore(errorElement, inputElement.nextSibling);
        }
    });
</script>