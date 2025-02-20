<?php /* Template Name: Dealer-Bank-Info */ ?>
<?php 
$authenticated_user = validate_user_token();
if (!(isset($_COOKIE['user_token']) && $authenticated_user->type == 2)) {
    wp_redirect(home_url());
    exit;
}

global $wpdb;
$table_name = $wpdb->prefix . 'account_users';

// Lấy thông tin người dùng
$bank_name = $authenticated_user->bank_name;
$account_number = $authenticated_user->account_number;
$routing_number = $authenticated_user->routing_number;
$holder_name = $authenticated_user->holder_name;
$swift = $authenticated_user->swift;
$iban = $authenticated_user->iban;
$type = $authenticated_user->type;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $errors = [];

    $bank_name = sanitize_text_field($_POST['bank_name']);
    $account_number = sanitize_text_field($_POST['account_number']);
    $routing_number = sanitize_text_field($_POST['routing_number']);
    $holder_name = sanitize_text_field($_POST['holder_name']);
    $swift = sanitize_text_field($_POST['swift']);
    $iban = sanitize_text_field($_POST['iban']);

    if (empty($bank_name)) {
        $errors['bank_name'] = 'Enter your bank name';
    }

    if (empty($account_number)) {
        $errors['account_number'] = 'Enter your account number';
    }

    if (empty($routing_number)) {
        $errors['routing_number'] = 'Enter your routing number';
    }

    if (empty($holder_name)) {
        $errors['holder_name'] = `Enter holder's name`;
    }

    if (empty($swift)) {
        $errors['swift'] = 'Enter the swift';
    }

    if (empty($iban)) {
        $errors['iban'] = 'Enter the iban';
    }

    if (empty($errors)) {
        $update_data = [
            'bank_name' => $bank_name,
            'account_number' => $account_number,
            'routing_number' => $routing_number,
            'holder_name' => $holder_name,
            'swift' => $swift,
            'iban' => $iban
        ];

        // Thực hiện cập nhật
        $result = $wpdb->update(
            $table_name,
            $update_data,
            ['token' => $_COOKIE['user_token']]
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
                <li class="flex items-center pt-4 pb-4 pl-6 item-f dashboard">
                <a class="flex items-center no-underline" href="<?= home_url() ?>/dealer-dashboard"><img class="mr-3 w-6 default" src="<?= $url ?>/assets/dealer/img/cuida.png" alt=""><img class="mr-3 w-6 active hidden" src="<?= $url ?>/assets/dealer/img/cuida_ac.png" alt=""><span class="menu-item">Dashboard</span></a>
                </li>
                <li class="flex items-center pt-4 pb-4 pl-6 item-f my-order">
                <a class="flex items-center no-underline menu-item" href="<?= home_url() ?>/dealer-order-info"><img class="mr-3 w-6 default" src="<?= $url ?>/assets/dealer/img/cart.png" alt=""><img class="mr-3 w-6 active hidden" src="<?= $url ?>/assets/dealer/img/cart_ac.png " alt=""><span class="menu-item">My orders</span></a>
                </li>
                <?php if($type == 2): ?>
                    <li class="flex items-center pt-4 pb-4 pl-6 item-f management">
                    <a class="flex items-center no-underline menu-item" href="<?= home_url() ?>/dealer-affiliate-order"><img class="mr-3 w-6 default" src="<?= $url ?>/assets/dealer/img/tabler_shopping-bag-discount.png" alt=""><img class="mr-3 w-6 active hidden" src="<?= $url ?>/assets/dealer/img/tabler_shopping-bag-discount_ac.png" alt=""><span class="menu-item">Affiliate orders</span></a>
                    </li>
                    <li class="flex items-center pt-4 pb-4 pl-6 item-f management">
                    <a class="flex items-center no-underline menu-item" href="<?= home_url() ?>/dealer-affiliate-customization"><img class="mr-3 w-6 default" src="<?= $url ?>/assets/dealer/img/material-symbols_link.png" alt=""><img class="mr-3 w-6 hidden active" src="<?= $url ?>/assets/dealer/img/material-symbols_link_ac.png" alt=""><span class="menu-item">Affiliate link<br>customization</span></a>
                    </li>
                    <li class="flex items-center pt-4 pb-4 pl-6 item-f management">
                    <a class="flex items-center no-underline menu-item" href="<?= home_url() ?>/point-management"><img class="mr-3 w-6 default" src="<?= $url ?>/assets/dealer/img/reward.png" alt=""><img class="mr-3 w-6 hidden active" src="<?= $url ?>/assets/dealer/img/reward_ac.png" alt=""><span class="menu-item">Point management</span></a>
                    </li>
                <?php endif; ?>
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
                        <a class="no-underline text-sm" href="<?= home_url() ?>/dealer-business-informmation">Business information</a>
                    </li>
                    <?php if($type == 2): ?>
                        <li class="mt-2">
                            <a class="no-underline text-sm blue-sure" href="<?= home_url() ?>/dealer-bank-informmation">Bank account information</a>
                        </li>
                    <?php endif; ?>
                </ul>
                </li>
            </ul>
        </div>
        <div class="col-span-6 text-center md:p-8 p-4">
            <div class="mx-auto relative w-1192-full overflow-hidden border-cm">
                <div class="mb-6">
                    <h4 class="text-left m-0 color-vector text-2xl font-medium">Bank account information</h1>
                </div>
                <div class="bg-white info-border-radius md:px-10 md:py-14 p-4">
                <form method="POST" class="form-edit">
                    <input type="hidden" name="update_profile" value="1">
                    <div class="grid md:gap-10 gap-4 md:grid-cols-2 grid-cols-1 mb-6">
                        <div class="text-left">
                            <label class="block mb-0 text-base font-medium text-gray-900 dark:text-white">Bank Name</label>
                            <input type="text" name="bank_name" value="<?= $bank_name ?>" class="box-border bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Enter information here" autocomplete="off">
                            <?php if (isset($errors['bank_name'])): ?>
                                <p class="validation-error text-justify text-[#FF0000] text-sm mt-1"><?= $errors['bank_name'] ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="text-left">
                            <label class="block mb-0 text-base font-medium text-gray-900 dark:text-white">Account Number</label>
                            <input type="text" name="account_number" value="<?= $account_number ?>" class="box-border bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Enter information here" autocomplete="off">
                            <?php if (isset($errors['account_number'])): ?>
                                <p class="validation-error text-justify text-[#FF0000] text-sm mt-1"><?= $errors['account_number'] ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="grid md:gap-10 gap-4 md:grid-cols-2 grid-cols-1 mb-6">
                        <div class="text-left">
                            <label class="block mb-0 text-base font-medium text-gray-900 dark:text-white">Routing Number</label>
                            <input type="text" name="routing_number" value="<?= $routing_number ?>" class="box-border bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Enter information here" autocomplete="off">
                            <?php if (isset($errors['routing_number'])): ?>
                                <p class="validation-error text-justify text-[#FF0000] text-sm mt-1"><?= $errors['routing_number'] ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="text-left">
                            <label class="block mb-0 text-base font-medium text-gray-900 dark:text-white">Account Holder's Name</label>
                            <input type="text" name="holder_name" value="<?= $holder_name ?>" class="box-border bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Enter information here" autocomplete="off">
                            <?php if (isset($errors['holder_name'])): ?>
                                <p class="validation-error text-justify text-[#FF0000] text-sm mt-1"><?= $errors['holder_name'] ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="grid md:gap-10 gap-4 md:grid-cols-2 grid-cols-1 mb-6">
                        <div class="text-left">
                            <label class="block mb-0 text-base font-medium text-gray-900 dark:text-white">SWIFT/BIC</label>
                            <input type="text" name="swift" value="<?= $swift ?>" class="box-border bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Enter information here" autocomplete="off">
                            <?php if (isset($errors['swift'])): ?>
                                <p class="validation-error text-justify text-[#FF0000] text-sm mt-1"><?= $errors['swift'] ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="text-left">
                            <label class="block mb-0 text-base font-medium text-gray-900 dark:text-white">IBAN</label>
                            <input type="text" name="iban" value="<?= $iban ?>" class="box-border bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Enter information here" autocomplete="off">
                            <?php if (isset($errors['iban'])): ?>
                                <p class="validation-error text-justify text-[#FF0000] text-sm mt-1"><?= $errors['iban'] ?></p>
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
            const bankNameInput = document.querySelector('input[name="bank_name"]');
            const accountNumberInput = document.querySelector('input[name="account_number"]');
            const routingNumberInput = document.querySelector('input[name="routing_number"]');
            const holderNameInput = document.querySelector('input[name="holder_name"]');
            const swiftInput = document.querySelector('input[name="swift"]');
            const ibanInput = document.querySelector('input[name="iban"]');
            
            if (!bankNameInput.value.trim()) {
                createErrorMessage(bankNameInput, 'Enter your bank name');
                hasError = true;
            }
            
            if (!accountNumberInput.value.trim()) {
                createErrorMessage(accountNumberInput, 'Enter your account number');
                hasError = true;
            }
            
            if (!routingNumberInput.value.trim()) {
                createErrorMessage(routingNumberInput, 'Enter your routing number');
                hasError = true;
            }
            
            if (!holderNameInput.value.trim()) {
                createErrorMessage(holderNameInput, `Enter holder's name`);
                hasError = true;
            }
            
            if (!swiftInput.value.trim()) {
                createErrorMessage(swiftInput, 'Enter the swift');
                hasError = true;
            }
            
            if (!ibanInput.value.trim()) {
                createErrorMessage(ibanInput, 'Enter the iban');
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