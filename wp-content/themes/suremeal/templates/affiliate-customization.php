<?php /* Template Name: affiliate customization */ ?>
<?php
get_header('dealer');
$url = get_template_directory_uri();
global $wpdb;
$characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
$length = 18;
do {
    $distribution_code = '';
    for ($i = 0; $i < $length; $i++) {
        $distribution_code .= $characters[rand(0, strlen($characters) - 1)];
    }
    $distribution_code .= rand(0, 9);
    $exists = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM wp_affiliate WHERE distribution_code = %s", $distribution_code));
    if ($exists) $length += 2;
} while ($exists);

$charactersa = 'abcdefghijklmnopqrstuvwxyz0123456789';
$lengths = 9;
do {
    $shortened_code = '';
    for ($i = 0; $i < $lengths; $i++) {
        $shortened_code .= $charactersa[rand(0, strlen($charactersa) - 1)];
    }
    $shortened_code .= rand(0, 9);
    $exists = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM wp_affiliate WHERE shortened_code = %s", $shortened_code));
    if ($exists) $length += 2;
} while ($exists);
$authenticated_user = validate_user_token();
$id_user = $authenticated_user->ID;
$curron_aff = $wpdb->get_row($wpdb->prepare("SELECT * FROM wp_affiliate WHERE id_user = %s", $id_user));
if ($curron_aff) {
    $distribution_code = $curron_aff->distribution_code;
    $shortened_code = $curron_aff->shortlink;
    $domain = $curron_aff->domain;
    $product_infomation = $domain->product_infomation;
}
?>
<input type="hidden" id="urlAjax" value="<?= admin_url() ?>admin-ajax.php">
    <div class="text-center md:p-8 p-4">
        <?php if ($product_infomation) :?>
        <div class="w-1047 text-left mb-8">
            <div class="bg-white md:px-10 px-4 py-6 rounded-xl">
                <h4 class="text-left m-0 color-vector text-28 font-semibold mb-6">Products affiliate</h4>
                <div class="mb-6 text-left">
                    <h2 for="link" class="block mb-1 text-base font-medium text-gray-900 dark:text-white">Distribution link</h2>
                </div>
            </div>
        </div>
        <?php endif;?>
        <div class="w-1047 text-left mb-8">
            <div class="bg-white md:px-10 px-4 py-6 rounded-xl">
                <h4 class="text-left m-0 color-vector text-28 font-semibold mb-6">Affiliate link customization</h4>
                <div class="mb-6 text-left">
                    <label for="link" class="block mb-1 text-base font-medium text-gray-900 dark:text-white">Distribution link</label>
                    <div class="relative">
                        <!-- <img src="<?= $url ?>/dist/img/icon_edit.png" alt="" class="w-6 absolute right-4 top-3"> -->
                        <input type="text" id="link" class="box-border bg-input border border-gray-300 text-gray-900 text-base rounded-lg block w-full pd-11-cus pr-40" name="distribution_link" value="http://suremealdev.wecan-group.info/product?distribution_code=<?= $distribution_code ?>" disabled placeholder="http://suremealdev.wecan-group.info/product?distribution_code=<?= $distribution_code ?>" autocomplete="off">
                    </div>
                </div>
                <div class="mb-4 text-left">
                    <label for="link" class="block mb-1 text-base font-medium text-gray-900 dark:text-white">Shorten link</label>
                    <div class="relative md:flex block items-center">
                        <div class="w-297 float-left">
                            <div class="relative">
                                <!-- <img src="<?= $url ?>/dist/img/icon_edit.png" alt="" class="w-6 absolute right-4 top-3"> -->
                                <input class="box-border bg-input border border-gray-300 text-gray-900 text-base rounded-l-lg block w-full pd-11-cus pr-40" type="text" name="domain"  <?php if ($curron_aff) :?> disabled<?php endif;?> value="http://suremealdev.wecan-group.info/" placeholder="http://suremealdev.wecan-group.info/">
                            </div>
                        </div>
                        <div class="w-460 float-left">
                            <input class="box-border bg-input border border-gray-300 text-gray-900 text-base rounded-r-lg block w-full pd-11-cus" type="text"  name="shortened_code"    <?php if ($curron_aff) :?> value="<?= $shortened_code ?>" disabled<?php endif;?> placeholder="">
                        </div>
                        <div class="md:pl-6 pl-0 md:pt-0 pt-2 md:w-auto w-full">
                            <button type="submit" id="sumbit_link" class="w-full text-white bg-red-sure border-none font-medium rounded-lg text-base w-full btn-reset py-3 text-center my-0 md:mt-0 mt-2 ml-0">Generate link</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php if ($curron_aff) :?>
            <div class="w-1047 text-left">
                <div class="bg-white md:px-10 px-4 py-6 rounded-xl">
                    <h4 class="text-left m-0 aff-color text-28 font-semibold mb-6">Affiliate link customization</h4>
                    <div class="mb-6 text-left">
                        <label for="link" class="block mb-1 text-base font-medium text-gray-900 dark:text-white">Distribution link</label>
                        <div class="relative cursor-pointer focus:ring">
                            <img src="<?= $url ?>/dist/img/copy_edit.png" alt="" class="w-6 absolute right-4 top-3" onclick="copyToClipboard('referral_code')">
                            <input type="text" id="referral_code" class="box-border bg-input border border-gray-300 text-gray-900 text-base rounded-lg block w-full pd-11-cus pr-40" placeholder="<?= $domain ?>product?type=affiliate&distribution_code=<?= $distribution_code ?>" autocomplete="off" readonly value="<?= $domain ?>product?type=affiliate&distribution_code=<?= $distribution_code ?>" discebo>
                        </div>
                    </div>
                    <div class="mb-6 text-left">
                        <label for="link" class="block mb-1 text-base font-medium text-gray-900 dark:text-white">Shortened link</label>
                        <div class="relative cursor-pointer focus:ring">
                            <img src="<?= $url ?>/dist/img/copy_edit.png" alt="" class="w-6 absolute right-4 top-3" onclick="copyToClipboard('referral_code1')">
                            <input type="text" id="referral_code1" class="box-border bg-input border border-gray-300 text-gray-900 text-base rounded-lg block w-full pd-11-cus pr-40" placeholder="<?= $domain ?><?= $shortened_code ?>" autocomplete="off" readonly value="<?= $domain ?><?= $shortened_code ?>" discebo>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif;?>
    </div>
    <?php get_footer('dealer') ?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<?php if (!$curron_aff) :?>
<script !src="">
    $(document).ready(function () {
        // Set giá trị cho input có name là 'shortened_code'
        $('input[name="shortened_code"]').val('<?= $shortened_code ?>');

        // Lấy giá trị các input trong sự kiện click
        $('#sumbit_link').on('click', function (e) {
            e.preventDefault(); // Ngăn chặn hành vi mặc định của nút submit

            // Lấy giá trị từ các input
            const distribution_code = '<?= $distribution_code ?>';
            const shortened_code = $("input[name='shortened_code']").val();
            const domain = $("input[name='domain']").val();
            let urlAjax = $("#urlAjax").val(); // Url ajax

            // Kiểm tra giá trị trước khi gửi ajax
            console.log('Distribution Code:', distribution_code);
            console.log('Shortened Code:', shortened_code);
            console.log('Domain:', domain);

            $.ajax({
                url: urlAjax,
                type: 'POST',
                dataType: "json",
                data: {
                    action: 'submit_shortlink',
                    distribution_code,
                    shortened_code,
                    domain,
                },
                beforeSend: function () {
                    Swal.fire({
                        title: 'Processing',
                        html: 'Please wait...',
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                },
                success: function (response) {
                    Swal.close(); // Đóng loading nếu thành công
                    if (response.status === 1) {
                        Swal.fire({
                            icon: 'success',
                            text: response.mess,
                        });
                        setTimeout(function () {
                            window.location.reload();
                        }, 1000);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            text: response.mess,
                        });
                    }
                },
                error: function (xhr, status, error) {
                    Swal.fire({
                        icon: 'error',
                        text: 'Something went wrong. Please try again.',
                    });
                }
            });
        });
    });



</script>
<?php endif;?>
<script>
    function copyToClipboard(inputId) {
        const input = document.getElementById(inputId);
        if (input) {
            input.select();
            input.setSelectionRange(0, 99999); // For mobile devices

            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(input.value).then(() => {
                    alert('Copied to clipboard!');
                }).catch(err => {
                    console.error('Failed to copy text: ', err);
                });
            } else {
                // Fallback for browsers that do not support navigator.clipboard
                try {
                    document.execCommand('copy');
                    alert('Copied to clipboard!');
                } catch (err) {
                    console.error('Fallback: Failed to copy text', err);
                }
            }
        }
    }
</script>

