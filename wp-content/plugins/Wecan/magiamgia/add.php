<?php
global $wpdb;
require_once __DIR__ . '/../includes/function.php';

//$myrows = $wpdb->get_results( "SELECT * FROM ".$wpdb->prefix."useragency" );
//$module_path = 'admin.php?page=daily';
$module_pathadd = 'admin.php?page=discount_price';
$module_short_url = str_replace('admin.php?page=', '', $module_pathadd);
$mess = '';
$mdlconf = array('title' => 'Discount code');
include __DIR__ . "/../includes/padding.php";


wp_enqueue_script('jquery');// jQuery
add_admin_css('main.css');
date_default_timezone_set('Asia/Ho_Chi_Minh');
$datetime = time();

$args = array(
    'post_type' => 'goi_sim',
    'posts_per_page' => 10,
    'orderby' => 'post_date',
);

$query_post = new WP_Query($args);
$posts = $query_post->posts;
$arg_product = array(
    'post_type' => 'product',
    'posts_per_page' => -1,
);
$query_product = new WP_Query($arg_product);
$products = $query_product->posts;

$list_user = $wpdb->get_results("
    SELECT * 
    FROM wp_account_users 
    ORDER BY id DESC 
");
$list_voucher = $wpdb->get_results("
    SELECT * 
    FROM wp_voucher
    ORDER BY id DESC 
");
?>
<style>
    input {
        width: 100%;
    }

    .d-none {
        display: none !important;
    }

    .roles-report .item-role .table__wrapper {
        padding-bottom: 10px;
    }

    .roles-report .item-role {
        padding-bottom: 10px;
        border-bottom: 1px solid #ccc;
        padding-top: 10px;
    }

    .role-title-1 {
        font-size: 20px;
        font-weight: 600;
        margin-bottom: 15px;
        text-transform: uppercase;
    }

    .role-title-2 {
        font-size: 16px;
        padding-bottom: 10px;
        font-weight: 600;
    }

    .br-checkbox {
        padding-bottom: 10px;
    }

    .br-checkbox label {
        font-weight: 500;
    }

    .br-checkbox input {
        margin: 0;
    }

    .checkbox-all {
        padding-left: 15px;
    }

    .title-mgg {
        font-weight: 500 !important;
        padding: 0 !important;
    }

    .red-validate {
        color: red;
    }

    .date-time {
        display: flex;
        flex-wrap: wrap;
    }

    .time-space {
        padding-left: 10px;
        padding-right: 10px;
        font-size: 20px;
    }

    .date-time input {

    }

    .date-time .time-start {

    }

    .date-time .time-end {

    }

    .loaigiamgia {
        display: flex;
        flex-wrap: wrap;
    }

    .loaigiamgia .type-gg {

    }

    #adddform {

    }

    input[type=number] {
        -webkit-appearance: none !important; /* loại bỏ giao diện mặc định */
        appearance: none !important;
    }

    .mucgiamtd .type-mgtd {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        padding-bottom: 15px;
    }

    .mucgiamtd .type-mgtd .typetd {
        padding-right: 30px;
    }

    .inputnumber {
        width: 400px !important;
    }

    .button-change {
        width: max-content;
        color: red;
        border-radius: 5px;
        border: 1px solid red;
        cursor: pointer;
    }

    .border-error {
        border: 1px solid #e91c24 !important;
    }

    .checkboxmg {
        margin: 0;
    }

    .modal-address {
        display: none;
        position: fixed;
        width: 960px;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: #ffffff;
        border-radius: 5px;
        z-index: 1001;
        padding: 10px 15px;
    }

    .bg__overlay {
        display: none;
        position: fixed;
        width: 100%;
        height: 100%;
        background: rgba(41, 43, 46, 0.5);
        z-index: 1000;
        top: 0;
    }

    .active_delivery .bg__overlay {
        display: block !important;
    }

    .active_delivery .modal-address {
        display: block !important;
    }

    .d-block {
        display: block !important;
    }

    .ffchoice-button {
        color: #333;
        background-color: #fff;
        border: 1px solid #e5e5e5;
        position: relative;
        display: -webkit-inline-box;
        display: -ms-inline-flexbox;
        display: inline-flex;
        -webkit-box-align: center;
        -ms-flex-align: center;
        align-items: center;
        -webkit-box-pack: center;
        -ms-flex-pack: center;
        justify-content: center;
        height: 32px;
        min-width: 72px;
        padding: 0 16px;
        font-family: inherit;
        font-size: 14px;
        font-weight: 500;
        text-decoration: none;
        white-space: nowrap;
        vertical-align: middle;
        cursor: pointer;
        border-radius: 4px;
        outline: none;
        -webkit-box-sizing: border-box;
        box-sizing: border-box;
        -webkit-transition: all .2s ease-in-out;
        transition: all .2s ease-in-out;
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }

    .footer-pup {
        padding-top: 30px;
        padding-bottom: 10px;
        text-align: right;
    }

    .ffchoice-button--primary {
        color: #fff;
        background-color: #ee4d2d;
        border-color: #ee4d2d;
    }

    .popup__content .title h2 {
        font-size: 25px;
    }

    .chang-form {
        height: 500px;
        overflow: auto;
    }

    .chang-form tbody tr {

    }

    .chang-form tbody tr td {
        vertical-align: middle;
    }

    .title-san_pham {
        display: flex;
    }

    .title-san_pham .i-m-g img {
        width: 50px;
        height: 50px;
        object-fit: cover;
    }

    .title-san_pham .info-san_pham {
        padding-left: 5px;
    }

    .title-san_pham .info-san_pham p {
        margin: 0;
    }

    .ffchoice-button--applyallpro {
        margin-bottom: 10px;
    }

    .iconloadgif {
        top: 0;
        right: 0;
        left: 0;
        bottom: 0;
        position: absolute;
        margin: auto;
    }

    .divgif {
        position: fixed;
        width: 100%;
        height: 100%;
        z-index: 1100;
        display: none;
        background: #dedede;
        opacity: 0.5;
    }

    .pagnition {
        text-align: center;
        padding-top: 40px;
    }

    .pagnition .page-numbers {
        padding: 8px 12px !important;
        background: #ffffff;
        color: #000;
        font-size: 18px;
        font-family: OpenSans-Bold, sans-serif;
    }

    .pagnition .page-numbers:hover {
        background-color: #ee4d2d;
        color: #fff;
    }

    .pagnition span {
        margin-left: 0 !important;
    }

    .pagnition .prev i {
        font-size: 12px;
        padding: 3px 3px;
    }

    .pagnition .next i {
        font-size: 12px;
        padding: 3px 3px;
    }

    .pagnition .current {
        background-color: #ee4d2d;
        color: #fff;
    }

    .ov-hiden {
        overflow: hidden;
    }

    .member-search {
        display: flex;
    }

    .member-search .button.button-primary {
        width: 100px;
    }

    #memberSearch {
        width: 500px;
        margin-bottom: 10px;
    }
    .dropdown-container {
        position: relative;
        display: inline-block;
    }
    .dropdown-container li {
        cursor: pointer;
    }

    .dropdown-list {
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 4px;
        overflow-y: auto;
        max-height: 200px;
        width: 500px;
    }

    .dropdown-list ul {
        list-style-type: none;
        padding: 0;
        margin: 0;
    }
    .dropdown-list li {
        padding: 10px;
    }

    .dropdown-list li.selected {
        display: none; /* Ẩn các sản phẩm đã chọn */
    }

    .dropdown-list li:hover {
        background-color: #f1f1f1;
        cursor: pointer;
    }

    .dropdown-list li button {
        margin-left: 10px;
        padding: 5px 10px;
        background-color: #ff6b6b;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }
    .dropdown-items{
        display: flex;
        width: 100%;
        flex-wrap: wrap;
        gap: 1rem;
    }
    .dropdown-item{
        padding: 14px;
        background-color: #f1f1f1;
        border-bottom: 1px solid #ccc;
        width: 35%;
    }
    .list-check{
        display: flex;
        gap: 2rem;
    }


</style>
<!-- swiper -->
<div class="divgif">
    <img class="iconloadgif" src="<?= get_template_directory_uri() ?>/ajax/images/loading2.gif" alt="">
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<div class="wrap">
    <input type="hidden" id="urlAjax" value="<?= admin_url() ?>admin-ajax.php">
    <!--    <input type="hidden" id="site_key" value="-->
    <? //= get_field("setting_captcha", "option")["site_key"] ?><!--">-->
    <!--    <input type="hidden" id="success_code" value="--><?php //= success_code ?><!--">-->
    <h1>
        Add new voucher
    </h1>
    <form id="adddform" method="post" action="<?php echo $module_pathadd . '&add_action=1'; ?>" name="post">
        <div id="poststuff">
            <div class="metabox-holder columns-2" id="post-body">
                <!---left-->
                <div id="post-body-content" class="pos1">
                    <div class="postbox">
                        <h2 class="hndle ui-sortable-handle api-title">Basic information</h2>
                        <div class="inside">
                            <!--                            <input type="hidden" value="-->
                            <?php //echo $id; ?><!--" name="id"/>-->
                            <table class="form-table ft_metabox leftform">
                                <tr>
                                    <td style="width: 250px;">Discount program name<span
                                                class="red-validate">*</span>
                                    </td>
                                    <td>
                                        <input type="text" class="validate-input" id="ten_chuong_trinh"
                                               name="ten_chuong_trinh" value="" size="50" placeholder="Enter">
                                        <label id="ten_chuong_trinh-error" class="error d-none" for="ten_chuong_trinh">This field cannot be empty</label>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Voucher Code <span class="red-validate">*</span>
                                    </td>
                                    <td>
                                        <input type="text" class="validate-input" id="ma_voucher" name="ma_voucher"
                                               value="" size="50" placeholder="Enter">
                                        <label id="ma_voucher-error" class="error d-none" for="ma_voucher">This field cannot be empty</label>
                                        <p class="reminder-line">Please enter only alphabetic characters (A-Z) and numbers (0-9); up to 10 characters.</p>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        Voucher usage <span class="red-validate">*</span>
                                    </td>
                                    <td>
                                        <div class="date-time">
                                            <div class="time-start">
                                                <input readonly type="text" id="time_start" name="time_start"
                                                       value="">
                                            </div>
                                            <div class="time-space">-</div>
                                            <div class="time-end">
                                                <input readonly type="text" id="time_end" name="time_end"
                                                       value="">
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2"><h2 class="title-mgg">Setting voucher</h2></td>
                                </tr>

                                <tr>
                                    <td>
                                        Discount type | Discount Amount<span class="red-validate">*</span>
                                    </td>
                                    <td>
                                        <div class="loaigiamgia">
                                            <div class="type-gg">
                                                <select name="" id="loai_giam_gia">
                                                    <option value="1">By amount</option>
                                                    <option value="2">By percentage</option>
                                                </select>
                                            </div>
                                            <div class="price-gg">
                                                <div class="type-input type-1">
                                                    <input type="number" class="" id="muc_giam" value=""
                                                           placeholder="Enter">
                                                    <label id="muc_giam-error" class="error d-none" for="muc_giam">This field cannot be empty</label>
                                                </div>
                                                <div class="type-input type-2 d-none">
                                                    <input type="number" class="" id="muc_giam_2" value=""
                                                           placeholder="Enter value larger than 1%">
                                                    <label id="muc_giam_2-error" class="error d-none" for="muc_giam_2">This field cannot be empty</label>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Minimum order value <span class="red-validate">*</span>
                                    </td>
                                    <td>
                                        <div class="">
                                            <input type="number" class="inputnumber validate-input"
                                                   id="gia_tri_toi_thieu" value="0" placeholder="Enter">
                                            <label id="gia_tri_toi_thieu-error" class="error d-none"
                                                   for="gia_tri_toi_thieu">This field cannot be empty</label>
                                            <!-- <label id="gia_tri_toi_thieu-error-2" class="error d-none">The voucher value cannot exceed the minimum order value.</label> -->
                                        </div>
                                    </td>
                                </tr>
                                <tr class="tr-mucgiamtoida d-none">
                                    <td>
                                        Maximum discount amount<span class="red-validate">*</span>
                                    </td>
                                    <td>
                                        <div class="mucgiamtd">
                                            <div class="type-mgtd">
                                                <div class="typetd">
                                                    <input type="radio" class="checkboxmg" name="checkboxmg"
                                                           id="muc_giam_toi_da_1" value="1" checked> <label
                                                            for="muc_giam_toi_da_1">Limited</label>
                                                </div>
                                                <div class="typetd">
                                                    <input type="radio" class="checkboxmg" name="checkboxmg"
                                                           id="muc_giam_toi_da_2" value="2"> <label
                                                            for="muc_giam_toi_da_2">Unlimited</label>
                                                </div>
                                            </div>
                                            <div class="price-mgtd">
                                                <input type="number" class="inputnumber" id="muc_giam_co_gioi_han"
                                                       value="0" placeholder="Enter">
                                                <label id="muc_giam_co_gioi_han-error" class="error d-none"
                                                       for="muc_giam_co_gioi_han">This field cannot be empty</label>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Choose product
                                    </td>
                                    <td>
                                        <input type="hidden" name="apply-filter-type" id="apply-filter-type" value="1">
                                        <div class="list-check d-flex">
                                            <div class="for-all">
                                                <label for="apply-filter-all" class="form-control all ffchoice-button ffchoice-button--applyallpro">
                                                    Apply to all products
                                                </label>
                                                <input type="radio" class="d-none" name="apply-filter" id="apply-filter-all" value="1" checked>
                                            </div>
                                            <div class="for-choose">
                                                <label for="apply-filter-choose" class="form-control ffchoice-button ffchoice-button--applyallpro">
                                                    Apply to specific products
                                                </label>
                                                <input type="radio" class="d-none" name="apply-filter" id="apply-filter-choose" value="0">
                                            </div>
                                        </div>
                                        <div class="dropdown-container">
                                            <div class="dropdown-list">
                                                <ul></ul>
                                            </div>
                                            <input type="hidden" name="producdIdChoose" id="producdIdChoose" value="">
                                            <div class="dropdown-content" id="dropdown-content">
                                                <ul class="dropdown-items">
                                                    <?php foreach ($products as $product): ?>
                                                        <li class="dropdown-item" data-value="<?= esc_attr($product->post_title) ?>" data-id="<?= esc_attr($product->ID) ?>">
                                                            <?= esc_html($product->post_title) ?>
                                                        </li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Quantity <span class="red-validate">*</span>
                                    </td>
                                    <td>
                                        <div class="">
                                            <input type="number" class="inputnumber validate-input"
                                                   id="so_luong" value="" placeholder="Enter">
                                            <label id="so_luong-error" class="error d-none"
                                                   for="so_luong">This field cannot be empty</label>
                                        </div>
                                    </td>
                                </tr>
                                <!-- <tr>
                                    <td>
                                        Apply to users
                                    </td>
                                    <td>
                                        <select name="apply_user[]" id="apply_user" multiple>
                                            <option value="all" selected>All</option> 
                                            <?php foreach($list_user as $user): ?>
                                                <option value="<?= $user->id ?>"><?= $user->first_name ?> <?= $user->last_name ?></option>
                                            <?php endforeach ?>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Apply to shipping
                                    </td>
                                    <td>
                                        <select name="apply_ship" id="apply_ship">
                                            <option value="null">Not applicable</option>
                                            <option value="0">All</option>
                                            <option value="1">United States</option>
                                            <option value="2">Canada</option>
                                            <option value="3">Vietnam</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Stack
                                    </td>
                                    <td>
                                        <select name="apply_stack[]" id="apply_stack" multiple>
                                            <option value="null" selected>Not applicable</option>
                                            <?php foreach($list_voucher as $voucher): ?>
                                                <option value="<?= $voucher->id ?>"><?= $voucher->voucher_name ?></option>
                                            <?php endforeach ?>
                                        </select>
                                    </td>
                                </tr>
                                <tr> -->
                                    <td>
                                        Reuse
                                    </td>
                                    <td>
                                        <input type="checkbox" name="reuse" id="reuse" value="1">
                                    </td>
                                </tr>
                            </table>
                        </div>

                    </div>
                </div>
                <!--right-->
                <div class="postbox-container" id="postbox-container-1">
                    <div class="meta-box-sortables ui-sortable" id="side-sortables">

                        <div class="postbox " id="submitdiv">
                            <h2 class="hndle ui-sortable-handle"><span>Add</span></h2>
                            <div class="inside">
                                <div id="submitpost" class="submitbox">
                                    <div id="major-publishing-actions">
                                        <div id="publishing-action">
                                            <input type="button" value="Add" id="publish"
                                                   class="button button-primary button-large" name="save">
                                        </div>
                                        <div class="clear"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </form>

</div>


<link rel="stylesheet" href="<?= plugin_path ?>/assets/cds_datepicker/css/jquery-ui.css">
<link rel="stylesheet" href="<?= plugin_path ?>/assets/cds_datepicker/css/jquery-ui-timepicker-addon.css">
<link rel="stylesheet" href="<?= plugin_path ?>/assets/cds_datepicker/fontawesome/css/all.min.css">

<!--<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>-->
<script src="<?= plugin_path ?>/assets/cds_datepicker/js/jquery-1.11.1.min.js"></script>
<script src="<?= plugin_path ?>/assets/cds_datepicker/js/jquery-ui.min.js"></script>
<script src="<?= plugin_path ?>/assets/cds_datepicker/js/jquery-ui-timepicker-addon.js"></script>
<script src="<?= get_template_directory_uri() ?>/ajax/js/main-ajax.js"></script>
<?php
//add_admin_js('image.upload.js');
//add_admin_js('jquery.min.js');
add_admin_js('jquery.validate.min.js');
?>

<input type="hidden" id="paged" value="1">
<input type="hidden" id="checkkey" value="1">
<input type="hidden" id="checkData" value="1">

<script>
    $(document).ready(function () {
        var startDateTextBox = $('#time_start');
        var endDateTextBox = $('#time_end');
        $.timepicker.datetimeRange(
            startDateTextBox,
            endDateTextBox,
            {
                minInterval: (1000 * 60 * 60), // 1hr
                dateFormat: 'dd/mm/yy',
                timeFormat: 'HH:mm',
                oneLine: true,
                start: {
                    minDate: null, // Cho phép chọn từ bất kỳ ngày nào
                }, // start picker options
                end: {
                    minDate: null, // Đảm bảo endDate không bị giới hạn sai
                }, // end picker options
                controlType: 'select',
                timeText: 'Time',
                hourText: 'Hour',
                minuteText: 'Minute',
                closeText: 'Confirm',
            }
        );
    });
</script>
<!-- <script>
$('#apply_user').on('change', function() {
    const $select = $(this);
    const selectedValues = $select.val() || [];
    
    // Nếu có chọn "all" và có các option khác
    if (selectedValues.includes('all') && selectedValues.length > 1) {
        // Bỏ chọn tất cả option trừ "all"
        $select.find('option').not('[value="all"]').prop('selected', false);
    }
    
    // Nếu đang chọn các option khác (không phải "all")
    if (!selectedValues.includes('all') && selectedValues.length > 0) {
        // Bỏ chọn option "all"
        $select.find('option[value="all"]').prop('selected', false);
    }
    
    // Nếu chọn "all" khi chưa có option nào
    if (selectedValues.includes('all') && selectedValues.length === 1) {
        // Bỏ chọn tất cả option khác (nếu có)
        $select.find('option').not('[value="all"]').prop('selected', false);
    }
});
$('#apply_stack').on('change', function() {
    const $select = $(this);
    const selectedValues = $select.val() || [];
    
    // Nếu có chọn "null" và có các option khác
    if (selectedValues.includes('null') && selectedValues.length > 1) {
        // Bỏ chọn tất cả option trừ "null"
        $select.find('option').not('[value="null"]').prop('selected', false);
    }
    
    // Nếu đang chọn các option khác (không phải "null")
    if (!selectedValues.includes('null') && selectedValues.length > 0) {
        // Bỏ chọn option "null"
        $select.find('option[value="null"]').prop('selected', false);
    }
    
    // Nếu chọn "null" khi chưa có option nào
    if (selectedValues.includes('null') && selectedValues.length === 1) {
        // Bỏ chọn tất cả option khác (nếu có)
        $select.find('option').not('[value="null"]').prop('selected', false);
    }
});
</script> -->
<script>
    $(document).ready(function () {
        var keyword = '';
        var dataChange = [];
        var statuskieuapdung = 1, statusmucgiamtoida = 1; // statuskieuapdung,1: Áp dụng cho sản phẩm, 2: Áp dụng cho thành viên
        // statusmucgiamtoida: 1: Giới hạn, 2: Không giới hạn
        // statuskieuapdung: 1: Áp dụng cho sản phẩm, 2: Áp dụng cho user
        let urlAjax = $("#urlAjax").val();
        let site_key = $("#site_key").val();
        $("body").append('<div class="bg__overlay"></div>');

        // chọn sp :
        let selectedProductIds = [];

        $('.dropdown-item').on('click', function() {
            var selectedProductId = $(this).data('id');
            var selectedProductTitle = $(this).data('value');
            if (!selectedProductIds.includes(selectedProductId)) {
                selectedProductIds.push(selectedProductId);
                updateInputValues(selectedProductIds);
            }
            // ẩn sản phẩm đã chọn
            $(this).hide();
        });

        $(document).on('click', '.remove-product', function() {
            var productIdToRemove = $(this).data('id');
            selectedProductIds = selectedProductIds.filter(function(id) {
                return id !== productIdToRemove;
            });
            updateInputValues(selectedProductIds);
            // sau khi xóa thì hiện sản phẩm đã chọn
            $('.dropdown-item[data-id="' + productIdToRemove + '"]').show();
        });

        function updateInputValues(selectedProductIds) {
            let selectedProducts = selectedProductIds.map(id => {
                return $('.dropdown-item[data-id="' + id + '"]').data('value');
            });

            let html = '';
            for (let i = 0; i < selectedProducts.length; i++) {
                html += '<li>' + selectedProducts[i] + '<button type="button" class="remove-product" data-id="' + selectedProductIds[i] + '">Delete</button></li>';
            }

            $('.dropdown-list').html('<ul>' + html + '</ul>');
            // lưu selectedProductIds vào mảng
            $('#producdIdChoose').val(JSON.stringify(selectedProductIds));

            adjustInputHeight();
        }

        function adjustInputHeight() {
            let input = $('.dropdown-list ul');
            input.css('height', 'auto');
            input.css('height', input[0].scrollHeight + 'px');
        }
        // Popup nhận hàng
        $("body").on("click", ".delivery_information", function () {
            $("body").addClass("ov-hiden");
            var id = $(this).attr("data-id");
            $("#checkkey").val(id);
            var checkData = $("#checkData").val();
            $(".popup_delivery_information").addClass("active_delivery");
            $(".bg__overlay").addClass("d-block");
            if (checkData == 1) {
                loadDefault();
            }

        });
        $("body").on("click", ".ffchoice-button-close", function () {
            closePopup();
        })

        function closePopup() {
            $("body").removeClass("ov-hiden");
            $(".popup_delivery_information").removeClass("active_delivery");
            $(".bg__overlay").removeClass("d-block");
        }

        // Keyup input validate
        $('body').on('keyup', '.validate-input', function () {
            var input = $(this).val();
            var id = $(this).attr("id");
            if (!input) {
                $(this).addClass("border-error");
                $("#" + id + "-error").removeClass("d-none");
            } else {
                $(this).removeClass("border-error");
                $("#" + id + "-error").addClass("d-none");
            }
        });
        // End keyup input -----

        // Mức giảm tối đa chọn radio giới hạn or không giới hạn
        $('body').on('change', '.checkboxmg', function () {
            var selectedValue = $('input[name="checkboxmg"]:checked').val();
            statusmucgiamtoida = selectedValue;
            if (selectedValue == "1") {
                $("#muc_giam_co_gioi_han").removeClass("d-none");
            } else {
                $("#muc_giam_co_gioi_han").addClass("d-none");
            }
        });
        // End mức giảm tối đa ------

        // Mã gg được áp dụng cho user hoặc sản phẩm dạng chọn radio
        // $('body').on('change', '.changetypeaplly', function () {
        //     var selectedValue = $('input[name="changetypeaplly"]:checked').val();
        //     statuskieuapdung = selectedValue;
        //     if (selectedValue == "1") {
        //         $(".product-show").removeClass("d-none");
        //         $(".user-show").addClass("d-none");
        //         $("#checkData").val(1);
        //     } else {
        //         $(".product-show").addClass("d-none");
        //         $(".user-show").removeClass("d-none");
        //         $("#checkData").val(1);
        //     }
        // });
        // End mã giảm giá -----

        //Loại giảm giá | Mức giảm* Select mức giảm giá, 1: Theo giá tiền, 2: Theo phần trăm
        $('body').on('change', '#loai_giam_gia', function () {
            var loai_giam_gia = $('#loai_giam_gia').find(":selected").val();
            if (loai_giam_gia == "1") {
                $(".type-2").addClass("d-none");
                $(".type-1").removeClass("d-none");
                $(".tr-mucgiamtoida").addClass("d-none");
            } else {
                $(".type-1").addClass("d-none");
                $(".type-2").removeClass("d-none");
                $(".tr-mucgiamtoida").removeClass("d-none");
            }
        });
        // End Loại giảm giá

        // Keyup mã voucher
        $('#ma_voucher').keyup(function () {
            var inputVal = $(this).val();
            var regex = /^[A-Z0-9]{0,10}$/; //regular expression to match only alphanumeric characters and up to 5 characters
            if (!regex.test(inputVal)) {
                // $(this).val(inputVal.replace(/[^A-Z0-9]/g, '').substring(0, 10)); //remove any non-alphanumeric characters and limit to 5 characters
            }
        });
        // Mức giảm tối đa checked áp dụng cho giảm giá theo phần trăm
        $("body").on("keyup", "#muc_giam_co_gioi_han", function () {
            // if (statuskieuapdung == "1") {
            var input = $(this).val();
            var id = $(this).attr("id");
            if (!input) {
                $(this).addClass("border-error");
                $("#" + id + "-error").removeClass("d-none");
            } else {
                $(this).removeClass("border-error");
                $("#" + id + "-error").addClass("d-none");
            }
            // }
        });

        // Giá trị đơn hàng tối thiểu
        $("#gia_tri_toi_thieu").keyup(function () {
            var selectedValue = $('input[name="checkboxmg"]:checked').val();
            if (selectedValue == "1") {
                var input = $("#gia_tri_toi_thieu").val();
                var id = $("#gia_tri_toi_thieu").attr("id");
                var mucgiam = parseInt($("#muc_giam").val());
                if (input < mucgiam) {
                    $("#gia_tri_toi_thieu").addClass("border-error");
                    $("#" + id + "-error-2").removeClass("d-none");
                } else {
                    $("#gia_tri_toi_thieu").removeClass("border-error");
                    $("#" + id + "-error-2").addClass("d-none");
                }
            }
        })

        // Save
        $("body").on("click", "#publish", function () {
            var check = 1;
            // Validate input and show mess
            var validate = $(".validate-input");
            validate.each(function (index, value) {
                var input = $(value).val();
                var id = $(value).attr("id");
                if (!input) {
                    check = 2;
                    $(this).addClass("border-error");
                    $("#" + id + "-error").removeClass("d-none");
                } else {
                    $(this).removeClass("border-error");
                    $("#" + id + "-error").addClass("d-none");
                }
            });
            // End validate input -----

            var time_start = $("#time_start").val();
            var time_end = $("#time_end").val();
            
            if (!time_start) {
                check = 2;
                $("#time_start").addClass("border-error");
                if (!$("#time_start-error").length) {
                    $("#time_start").after('<label id="time_start-error" class="error">Start time cannot be empty</label>');
                } else {
                    $("#time_start-error").removeClass("d-none");
                }
            } else {
                $("#time_start").removeClass("border-error");
                $("#time_start-error").addClass("d-none");
            }
            
            if (!time_end) {
                check = 2;
                $("#time_end").addClass("border-error");
                if (!$("#time_end-error").length) {
                    $("#time_end").after('<label id="time_end-error" class="error">End time cannot be empty</label>');
                } else {
                    $("#time_end-error").removeClass("d-none");
                }
            } else {
                $("#time_end").removeClass("border-error");
                $("#time_end-error").addClass("d-none");
            }

            var loai_giam_gia = $('#loai_giam_gia').find(":selected").val(); // 1: Giảm giá theo số tiền, 2: Giảm giá theo phần trăm
            var muc_giam_2 = $("#muc_giam_2").val();
            // Hiện thị thông báo cho giảm giá theo phần trăm và có giới hạn
            if (statusmucgiamtoida == "1" && loai_giam_gia == "2") {
                var id = "muc_giam_2";
                if (!muc_giam_2) {
                    check = 2;
                    $("#muc_giam_2").addClass("border-error");
                    $("#" + id + "-error").removeClass("d-none");
                } else {
                    $("#muc_giam_2").removeClass("border-error");
                    $("#" + id + "-error").addClass("d-none");
                }
            }
            // Check validate nếu chọn giảm theo giá tiền
            if (statusmucgiamtoida == "2") {
                var id2 = "muc_giam";
                if (!muc_giam_2) {
                    check = 2;
                    $("#muc_giam").addClass("border-error");
                    $("#" + id2 + "-error").removeClass("d-none");
                } else {
                    $("#muc_giam").removeClass("border-error");
                    $("#" + id2 + "-error").addClass("d-none");
                }
            }
            // End hiện thị -----

            //TH giảm giá theo giá tiền, Nếu giá trị đơn hàng tối thiếu nhỏ hàng giá tiền giảm thì thông báo lỗi
            if (statuskieuapdung == "1") {
                var input1 = $("#gia_tri_toi_thieu").val();
                var id1 = $("#gia_tri_toi_thieu").attr("id");
                var mucgiam = parseInt($("#muc_giam").val());
                if (input1 < mucgiam) {
                    $("#gia_tri_toi_thieu").addClass("border-error");
                    $("#" + id1 + "-error-2").removeClass("d-none");
                } else {
                    $("#gia_tri_toi_thieu").removeClass("border-error");
                    $("#" + id1 + "-error-2").addClass("d-none");
                }
            } else { // TH giảm giá theo %

            }
            // End TH giảm giá
            // Check chọn sản phẩm
            if (dataChange.length == 0) {
                $(".change-pro").removeClass("d-none");
                check == 2;
            }
            // End check chọn sản phẩm

            // Pass and save
            if (check == 1) {
                var ten_chuong_trinh = $("#ten_chuong_trinh").val();
                var ma_voucher = $("#ma_voucher").val();
                var time_start = $("#time_start").val();
                var time_end = $("#time_end").val();
                var muc_giam = $("#muc_giam").val();
                var muc_giam_2 = $("#muc_giam_2").val();

                var muc_giam_co_gioi_han = $("#muc_giam_co_gioi_han").val();
                var gia_tri_toi_thieu = $("#gia_tri_toi_thieu").val();
                var so_luong = $("#so_luong").val();
                var producdIdChoose = $("#producdIdChoose").val();
                var special_member = $('#specialMember').prop('checked');
                var type = $('#apply-filter-type').val();

                // var apply_user = $("#apply_user").val();
                // var apply_ship = $("#apply_ship").val();
                // var apply_stack = $("#apply_stack").val();
                var reuse = $("#reuse").is(":checked") ? $("#reuse").val() : 0;
                $.ajax({
                    url: urlAjax,
                    type: 'POST',
                    cache: false,
                    dataType: "json",
                    data: {
                        ten_chuong_trinh,
                        ma_voucher,
                        time_start,
                        time_end,
                        muc_giam,
                        muc_giam_2,
                        muc_giam_co_gioi_han,
                        gia_tri_toi_thieu,
                        so_luong,
                        producdIdChoose,
                        statuskieuapdung: statuskieuapdung,
                        statusmucgiamtoida: statusmucgiamtoida,
                        loai_giam_gia,
                        type,
                        // apply_user,
                        // apply_ship,
                        // apply_stack,
                        reuse,
                        // dataChange,
                        // special_member,
                        action: 'add_voucher',
                        // action1: "add_voucher",
                        // token1: token
                    },
                    beforeSend: function () {
                        Swal.fire({
                            title: 'Processing',
                            html: 'Please wait...',
                            didOpen: () => {
                                Swal.showLoading()
                            }
                        });
                    },
                    success: function (rs) {
                        console.log(rs);

                        if (rs.status == 1) {
                            Swal.fire({
                                icon: 'success',
                                text: rs.message
                            }).then(() => {
                                window.location.href = "<?= $module_path; ?>";
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                text: rs.message
                            });
                        }
                    }
                });
                return false;
            }
        });

        // Mức giảm theo phần trăm
        $("body").on("keyup", "#muc_giam_2", function () {
            var val = parseInt($(this).val());
            if (val < 0) {
                $(this).val(0);
            }
            if (val > 69) {
                $(this).val(69);
            }
        })
        // End Mức giảm theo phần trăm

        $(".checkboxAll").click(function () {
            let key = $(this).attr("data-id");
            $('.checkboxed-' + key).not(this).prop('checked', this.checked);
        });

        // Xác nhận payment
        $("body").on("click", ".ffchoice-button-change", function () {
            var validate = $(".data-checkbox:checked");
            let arrayID = [];
            // let htmlShow = "";
            validate.each(function (index, value) {
                var check = $(value).attr("data-id");
                arrayID.push(check);
            });

            if (arrayID.length != 0) {
                dataChange = [];
                dataChange = arrayID;
                $(".change-pro").addClass("d-none");
                closePopup();
            }
        });

        // pagination
        $("body").on("click", ".pagnition span", function () {
            $(".pagnition span").removeClass("current");
            $(this).addClass("current");
            var key = $(this).text(); // Lấy page
            $("#paged").val(key); // Set page
            loadDefault();
        });

        //
        // $("body").on("click", ".ffchoice-button--applyallpro",function () {
        //     $(".data-checkbox").attr("checked",true)
        // });

        // Load default
        // loadDefault();
        function loadDefault() {
            $(".body-list_product").html("");
            var id = $("#checkkey").val(); // Get page
            var specialMember = $('#specialMember').prop('checked');
            // var paged = $("#paged").val(); // Get page
            if (id == 1) {
                $('.member-search').addClass('d-none');
                $('.special-member-container').addClass('d-none');
                $(".t1").text("Product");
                $(".t2").text("Price");
                $(".t3").text("Stock");
                $(".title-change").text("Chóoe product");
                $(".ffchoice-button--applyallpro").text("Apply to all products");
            } else {
                $('.member-search').removeClass('d-none');
                $('.special-member-container').removeClass('d-none');
                $(".t1").text("Fullname");
                $(".t2").text("Phone number");
                $(".t3").text("Email");
                $(".title-change").text("Choose customer");
                $(".ffchoice-button--applyallpro").text("Apply to all customers");
            }


            $.ajax({
                url: urlAjax,
                type: 'POST',
                cache: false,
                dataType: "json",
                data: {
                    id,
                    keyword,
                    specialMember: specialMember,
                    action: 'loadListProduct',
                    action1: "subscribe_danhsach_sanpham",
                    token1: token
                },
                beforeSend: function () {
                    $('.divgif').css('display', 'block');
                },
                success: function (rs) {
                    $('.divgif').css('display', 'none');
                    if (rs.status == success_code) {
                        $("#checkData").val(2);
                        $(".body-list_product").html(rs.html);
                        arrayID = rs.listID;
                        if (id == "1") {
                            // Show price
                            var listproductID = jQuery.parseJSON(rs.listProductID);
                            listproductID.map(function (value, index) { // Tính giá và lấy giá nhỏ nhất của các sản phẩm
                                var id = value.id_product;
                                var dataplorder = jQuery.parseJSON(value.data);
                                dataplorder.map(function (valuedt, indexdt) {
                                    valuedt["datakey1"] = indexdt;
                                    return valuedt;
                                });
                                var datacheck = dataplorder[0]["check"];
                                $(".cart-" + id).attr("data-datacheck", datacheck);
                                var pricefrom, priceto, qty;
                                // Show price origin
                                if (datacheck == 1) { // Chỉ có pl1
                                    dataplorder.sort(function (a, b) {
                                        return parseInt(a["value4"]) - parseInt(b["value4"]);
                                    });
                                    var sum2 = 0;
                                    for (var i = 0; i < dataplorder.length; i++) {
                                        sum2 += parseInt(dataplorder[i]["value2"]);
                                    } //
                                    qty = sum2;
                                    pricefrom = parseInt(dataplorder[0]["value4"]);
                                    priceto = parseInt(dataplorder[dataplorder.length - 1]["value4"]);
                                }
                                if (datacheck == 2) { // Chỉ có pl2
                                    var totalqty = 0;
                                    dataplorder.map(function (value, index) { // Order ASC chil1,chil4
                                        // Lấy giá trị bé nhất trong mảng
                                        var minVal4 = Math.min.apply(null, value["chil4"]);
                                        // dataplorder[index].minprice1 = minVal1;
                                        dataplorder[index].minprice4 = minVal4;
                                        // Lấy key của giá trị bé nhất trong mảng
                                        var keyVal4 = $.inArray(minVal4.toString(), value["chil4"]);
                                        dataplorder[index].keyminprice4 = keyVal4;
                                        dataplorder[index].maxqty = value["chil2"][keyVal4];
                                        dataplorder[index].orginal_price = value["chil1"][keyVal4];
                                        // Tính tổng
                                        for (var k = 0; k < value["chil2"].length; k++) {
                                            totalqty += parseInt(value["chil2"][k]);
                                        } //
                                    });
                                    dataplorder.sort(function (a, b) { // Sort pricemin4
                                        return parseInt(a["minprice4"]) - parseInt(b["minprice4"]);
                                    });
                                    pricefrom = parseInt(dataplorder[0]["minprice4"]);
                                    priceto = parseInt(dataplorder[dataplorder.length - 1]["minprice4"]);
                                    var sum3 = 0;
                                    for (var k = 0; k < dataplorder.length; k++) {
                                        sum3 += parseInt(dataplorder[k]["sum1"]);
                                    } //
                                    qty = totalqty;
                                }
                                $(".list-price-" + id).html('đ<span>' + formatNumber(pricefrom) + '</span> - đ<span>' + formatNumber(priceto) + '</span>');
                                $(".qtyKho-" + id).html(qty);

                            });
                        }

                    } else {
                        // Swal.fire({
                        //     icon: 'error',
                        //     text: rs.mess,
                        // });
                    }
                }
            });
            return false;

        }

        $('#memberSearch').submit(function (e) {
            e.preventDefault();
            keyword = $('input[name="keyword"]').val();
            loadDefault();
        });
        $('#specialMember').on('click', function () {
            loadDefault();
        });
    });
</script>
<script>
    $(document).ready(function() {
        // ẩn sản phẩm đã chọn
        $('.dropdown-container').hide();
        $('.form-control.all').css('background-color', '#ff6b6b');
        // apply-filter-type value: 1: Áp dụng cho tất cả sản phẩm, 0: Áp dụng cho sản phẩm cụ thể
        $('.list-check label').on('click', function() {
            var inputId = $(this).attr('for');
            $('#' + inputId).prop('checked', true);
            $('.list-check label').css('background-color', '#f1f1f1');
            $(this).css('background-color', '#ff6b6b');
            if (inputId === 'apply-filter-all') {
                $('.dropdown-container').hide();
                $('#apply-filter-type').val(1);
            } else {
                $('.dropdown-container').show();
                $('#apply-filter-type').val(0);
            }
        });
    });
</script>





