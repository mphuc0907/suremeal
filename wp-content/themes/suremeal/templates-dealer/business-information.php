<?php /* Template Name: Business information */ ?>
<?php
$authenticated_user = validate_user_token();

global $wpdb;
$table_name = 'wp_account_users';

// Lấy thông tin người dùng
$business_name = $authenticated_user->business_name;
$business_email = $authenticated_user->business_email;
$business_network = $authenticated_user->business_network;
$business_website = $authenticated_user->business_website;
$plan = $authenticated_user->plan;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $errors = [];

    $business_name = sanitize_text_field($_POST['business_name']);
    $business_email = sanitize_text_field($_POST['business_email']);
    $business_network = sanitize_text_field($_POST['business_network']);
    $business_website = sanitize_text_field($_POST['business_website']);
    $plan = $_POST['plan'];

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
?>
<?php
get_header('dealer');
?>
<div class="text-center md:py-8 md:px-10 p-5">
    <div class="mx-auto relative w-1192-full overflow-hidden">
        <div class="mb-bs">
            <h4 class="text-left m-0 color-vector text-2xl font-medium">Business information</h4>
        </div>
        <div class="bg-white info-border-radius md:px-10 px-4 md:py-15 py-4">
            <form method="POST" class="form-edit">
                <input type="hidden" name="update_profile" value="1">
                <div class="grid md:gap-10 gap-4 md:grid-cols-2 grid-cols-1 mb-6">
                    <div class="text-left">
                        <label class="block mb-1 text-base font-medium">Business name</label>
                        <input type="text" name="business_name" value="<?= $business_name ?>" class="box-border bg-input border border-gray-300 text-gray-900 text-base rounded-lg block w-full pd-11-cus" placeholder="Account Number" autocomplete="off">
                        <?php if (isset($errors['business_name'])): ?>
                            <p class="validation-error text-justify text-red-500 text-sm mt-1"><?= $errors['business_name'] ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="text-left">
                        <label class="block mb-1 text-base font-medium ">Business email</label>
                        <input type="text" name="business_email" value="<?= $business_email ?>" class="box-border bg-input border border-gray-300 text-gray-900 text-base rounded-lg block w-full pd-11-cus" placeholder="Enter information here" autocomplete="off">
                        <?php if (isset($errors['business_email'])): ?>
                            <p class="validation-error text-justify text-red-500 text-sm mt-1"><?= $errors['business_email'] ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="grid md:gap-10 gap-4 md:grid-cols-2 grid-cols-1 mb-6">
                    <div class="text-left">
                        <label class="block mb-1 text-base font-medium">Business network</label>
                        <input type="text" name="business_network" value="<?= $business_network ?>" class="box-border bg-input border border-gray-300 text-gray-900 text-base rounded-lg block w-full pd-11-cus" placeholder="Enter information here" autocomplete="off">
                        <?php if (isset($errors['business_network'])): ?>
                            <p class="validation-error text-justify text-red-500 text-sm mt-1"><?= $errors['business_network'] ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="text-left">
                        <label class="block mb-1 text-base font-medium">Business website</label>
                        <input type="text" name="business_website" value="<?= $business_website ?>" class="box-border bg-input border border-gray-300 text-gray-900 text-base rounded-lg block w-full pd-11-cus" placeholder="Enter information here" autocomplete="off">
                        <?php if (isset($errors['business_website'])): ?>
                            <p class="validation-error text-justify text-red-500 text-sm mt-1"><?= $errors['business_website'] ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="grid md:gap-10 gap-4 md:grid-cols-1 grid-cols-1 mb-10">
                    <div class="text-left">
                        <label class="block mb-1 text-base font-medium">How you plan to sell our products</label>
                        <textarea name="plan" class="box-border bg-input border border-gray-300 text-gray-900 text-base rounded-lg block w-full resize-none p-area" placeholder="How you plan to sell our products" id="plan-to-sell" rows="3"><?= $plan ?></textarea>
                        <?php if (isset($errors['plan'])): ?>
                            <p class="validation-error text-justify text-red-500 text-sm mt-1"><?= $errors['plan'] ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="flex items-start">
                    <button id="edit-update" class="text-white bg-red-sure border-none font-semibold rounded-lg text-base w-full sm:w-auto px-16 py-4 text-center my-0 btn-save-bussiness">Save information</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php
get_footer('dealer');
?>
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
            errorElement.classList.add('validation-error', 'text-justify', 'text-red-500', 'text-sm', 'mt-1');
            errorElement.textContent = message;
            inputElement.parentNode.insertBefore(errorElement, inputElement.nextSibling);
        }
    });
</script>