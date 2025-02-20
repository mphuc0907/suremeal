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


//$arg_product = array(
//    'post_type' => 'product',
//    'posts_per_page' => -1,
//);
//$query_product = new WP_Query($arg_product);
//$products = $query_product->posts;

$sql = "SELECT * FROM wp_posts WHERE post_type = 'product' AND post_status = 'publish'";
$products = $wpdb->get_results($sql);
$jsonproduct = json_encode($products);
// var_dump($products);
// foreach($products as $product){
//     echo $product->ID;
//     echo $product->post_title;
//     echo '</br>';
// }

$sqlVoucher = "SELECT * FROM wp_voucher";
$vouchers = $wpdb->get_results($sqlVoucher);
// var_dump($vouchers);
// foreach($vouchers as $voucher){
//     echo $voucher->id;
//     echo $voucher->voucher_name;
//     echo '</br>';
// }

$sqlGift = "SELECT * FROM wp_2_posts WHERE post_type = 'product_gift' AND post_status = 'publish'";
$gifts = $wpdb->get_results($sqlGift);
// var_dump($gifts);
// foreach($gifts as $gift){
//     echo $gift->ID;
//     echo $gift->post_title;
//     echo '</br>';
// }
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

    #datepicker {
        margin-bottom: 20px;
    }
    input[type="text"] {
        height: 30px;
    }
    .ui-datepicker-hover a, .ui-datepicker-hover:after {
        background-color: #8c9baf !important;
        color: #fff !important;
    }
    .ui-datepicker-select a {
        background-color: #434f5f;
        color: #fff !important;
    }
    .ui-datepicker {
        background: #fff;
        padding: 10px;
    }
    .ui-datepicker:after {
        display: block;
        content: '';
        height: 0;
        overflow: hidden;
        clear: both;
    }
    .ui-datepicker .ui-datepicker-group {
        float: left;
        width: 50%;
    }
    .ui-datepicker .ui-datepicker-header {
        color: #62748c;
        font: 14px/25px 'Arial';
        position: relative;
        text-align: center;
        text-transform: uppercase;
    }
    .ui-datepicker .ui-datepicker-header .ui-datepicker-prev, .ui-datepicker .ui-datepicker-header .ui-datepicker-next {
        background: #000;
        cursor: pointer;
        height: 15px;
        overflow: hidden;
        position: absolute;
        text-indent: -100em;
        top: 5px;
        user-select: none;
        width: 15px;
    }
    .ui-datepicker .ui-datepicker-header .ui-datepicker-prev:hover, .ui-datepicker .ui-datepicker-header .ui-datepicker-next:hover {
        background-color: #050505;
    }
    .ui-datepicker .ui-datepicker-header .ui-datepicker-prev.ui-state-disabled, .ui-datepicker .ui-datepicker-header .ui-datepicker-next.ui-state-disabled {
        opacity: 0.3;
    }
    .ui-datepicker .ui-datepicker-header .ui-datepicker-prev {
        left: 10px;
    }
    .ui-datepicker .ui-datepicker-header .ui-datepicker-next {
        right: 10px;
    }
    .ui-datepicker .ui-datepicker-calendar {
        border-collapse: collapse;
        font-size: 12px;
        cursor: default;
    }
    .ui-datepicker .ui-datepicker-calendar th, .ui-datepicker .ui-datepicker-calendar td {
        text-align: center;
        vertical-align: middle;
        width: 36px;
        height: 30px;
        line-height: 30px;
        padding: 0;
        z-index: 1;
    }
    .ui-datepicker .ui-datepicker-calendar th {
        color: #62748c;
        font: 14px/30px 'Arial';
    }
    .ui-datepicker .ui-datepicker-calendar td {
        color: #8d8d8d;
        padding: 2px 0;
        position: relative;
    }
    .ui-datepicker .ui-datepicker-calendar td:before, .ui-datepicker .ui-datepicker-calendar td:after {
        bottom: 2px;
        content: '';
        left: 0;
        top: 2px;
        position: absolute;
        width: 100%;
        z-index: -1;
    }
    .ui-datepicker .ui-datepicker-calendar td:before {
        background: #eee;
    }
    .ui-datepicker .ui-datepicker-calendar td:first-child:before {
        border-radius: 15px 0 0 15px;
    }
    .ui-datepicker .ui-datepicker-calendar td:last-child:before {
        border-radius: 0 15px 15px 0;
    }
    .ui-datepicker .ui-datepicker-calendar td.ui-datepicker-other-month, .ui-datepicker .ui-datepicker-calendar td.ui-state-disabled {
        color: #d0d0d0;
    }
    .ui-datepicker .ui-datepicker-calendar a {
        color: inherit;
        display: inline-block;
        height: 30px;
        text-decoration: none;
        width: 30px;
    }
    .ui-datepicker .ui-datepicker-calendar .ui-datepicker-highlight:after {
        background: #51c197;
    }
    .ui-datepicker .ui-datepicker-calendar .ui-checkin:after {
        left: 50%;
        width: 50%;
    }
    .ui-datepicker .ui-datepicker-calendar .ui-checkout:after {
        right: 50%;
        width: 50%;
    }
    .ui-datepicker .ui-datepicker-calendar .ui-datepicker-highlight a {
        color: #fff;
    }
    .ui-datepicker .ui-datepicker-calendar .ui-checkin a, .ui-datepicker .ui-datepicker-calendar .ui-checkout a {
        background-color: red;
        border-radius: 50%;
    }
    .ui-datepicker .ui-datepicker-calendar .ui-checkin.ui-datepicker-hover .ui-state-hover {
        border-radius: 50%;
    }
    .ui-datepicker .ui-datepicker-calendar .ui-datepicker-hover .ui-state-hover {
        border-radius: 0 50% 50% 0;
    }
    .ui-datepicker .ui-datepicker-calendar .ui-datepicker-today a {
        background: #62748c;
        border-radius: 50%;
        color: #fff;
    }
    .ui-datepicker-trigger {
        float: left;
        height: 15px;
        margin: 13px 0 0 -35px;
        overflow: hidden;
        text-indent: -100em;
        width: 16px;
    }

</style>
<!-- swiper -->
<div class="divgif">
    <img class="iconloadgif" src="<?= get_template_directory_uri() ?>/ajax/images/loading2.gif" alt="">
</div>
<input type="hidden" id="urlAjax" value="<?= admin_url() ?>admin-ajax.php">

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
<div class="wrap">
    <input type="hidden" id="urlAjax" value="<?= admin_url() ?>admin-ajax.php">
    <!--    <input type="hidden" id="site_key" value="-->
    <? //= get_field("setting_captcha", "option")["site_key"] ?><!--">-->
    <!--    <input type="hidden" id="success_code" value="--><?php //= success_code ?><!--">-->
    <h1>
        Add new product
    </h1>
    <form id="adddform" method="post" action="<?php echo $module_pathadd . '&add_action=1'; ?>" name="post">
        <div id="poststuff">
            <div class="metabox-holder columns-2" id="post-body">
                <!---left-->
                <div id="post-body-content" class="pos1">
                    <div class="postbox">
                        <h2 class="hndle ui-sortable-handle api-title">Basic information</h2>
                        <div class="inside">
                            <div class="mb-3">
                                <label for="name" class="form-label">Name product</label>
                                <input type="text" class="form-control" id="nameProduct" name="nameProduct" placeholder="Enter name">
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="name" class="form-label">Select type product</label>
                                    <select class="form-select w-100" id="productType" name="type" aria-label="Default select example">
                                        <option selected>Select type product</option>
                                        <option value="product">Product</option>
                                        <option value="voucher">Voucher</option>
                                        <option value="gift">Gift</option>

                                    </select>
                                </div>

                                <div class="col-md-4 ">
                                    <label for="name" class="form-label">Select Product</label>
                                    <select class="form-select w-100" name="productID" id="productID" aria-label="Default select example">
                                        <option selected>-----------------</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="name" class="form-label">Select status</label>
                                    <select class="form-select w-100" name="status" id="status" aria-label="Default select example">
                                        <option selected>Select status</option>
                                        <option value="1">Active</option>
                                        <option value="0">Not activated</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="note" class="form-label">Exchange points</label>
                                <input type="number" class="form-control" id="exchange_points" name="exchange_points"  placeholder="Enter points">
                            </div>
                            <div class="mb-3">
                                <label for="note" class="form-label">Note</label>
                                <textarea class="form-control" id="note" name="note" rows="3" placeholder="Enter note"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="inputGroupFile01">Upload image</label>
                                <input type="file" name="image" accept="image/png, image/jpeg" class="form-control" id="inputGroupFile01">
                            </div>
                            <div class="mb-3">
                                <label for="purchases">Purchases</label>
                                <input type="number" class="form-control" id="purchases" name="purchases"  placeholder="Enter purchases">
                            </div>
                            <div class="row mb-3">
                                <label for="">Date of existence
                                </label>
                                <div id="datepicker"></div>
                                <div class="mb-3">
                                <input type="text" disabled class="form-control" placeholder="" id="dates" />
                                </div>
                                <input type="hidden" id="created_at" name="created_at" />
                                <input type="hidden" name="expiration_date" id="expiration_date"  />
                            </div>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.0/jquery-ui.min.js"></script>
<script src="<?= plugin_path ?>/assets/cds_datepicker/js/jquery-ui.min.js"></script>
<script src="<?= plugin_path ?>/assets/cds_datepicker/js/jquery-ui-timepicker-addon.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
    jQuery(function(){

        var startDate, endDate;

        var datepicker = {
            container: jQuery("#datepicker"),
            dateFormat: 'mm/dd/yy',
            dates: [null, null],
            status: null,
            inputs: {
                checkin: jQuery('#created_at'),
                checkout: jQuery('#expiration_date'),
                dates: jQuery('#dates')
            }
        };

        datepicker.container.datepicker({
            numberOfMonths: 2,
            dateFormat: datepicker.dateFormat,
            minDate: 0,
            maxDate: null,

            beforeShowDay: function(date) {
                var highlight = false,
                    currentTime = date.getTime(),
                    selectedTime = datepicker.dates,
                    checkin_date = selectedTime[0] ? new Date(selectedTime[0]) : null,
                    checkout_date = selectedTime[1] ? new Date(selectedTime[1]) : null,
                    checkin_timestamp,
                    checkout_timestamp,
                    classes = 'ui-datepicker-highlight';

                date.setHours(0);
                date.setMinutes(0);
                date.setSeconds(0);
                date.setMilliseconds(0);

                currentTime = date.getTime();

                // CHECKIN/CHECKOUT CLASSES
                if(checkin_date) {
                    checkin_date.setHours(0);
                    checkin_date.setMinutes(0);
                    checkin_date.setSeconds(0);
                    checkin_date.setMilliseconds(0);
                    checkin_timestamp = checkin_date.getTime();

                    startDate = checkin_timestamp;
                }

                if(checkout_date) {
                    checkout_date.setHours(0);
                    checkout_date.setMinutes(0);
                    checkout_date.setSeconds(0);
                    checkout_date.setMilliseconds(0);
                    checkout_timestamp = checkout_date.getTime();

                    endDate = checkout_timestamp;
                }

                if ( checkin_timestamp && currentTime == checkin_timestamp ) {
                    classes = 'ui-datepicker-highlight ui-checkin';
                } else if (checkout_timestamp && currentTime == checkout_timestamp) {
                    classes = 'ui-datepicker-highlight ui-checkout';
                }

                // Highlight date range
                if ((selectedTime[0] && selectedTime[0] == currentTime) || (selectedTime[1] && (currentTime >= selectedTime[0] && currentTime <= selectedTime[1]))) highlight = true;

                return [true, highlight ? classes : ""];
            },
            onSelect: function(dateText) {

                if (!datepicker.dates[0] || datepicker.dates[1] !== null) {
                    // CHOOSE FIRST DATE

                    // fill dates array with first chosen date
                    datepicker.dates[0] = $.datepicker.parseDate(datepicker.dateFormat, dateText).getTime();
                    datepicker.dates[1] = null;

                    // clear all inputs
                    datepicker.inputs.checkin.val('');
                    datepicker.inputs.checkout.val('');
                    datepicker.inputs.dates.val('');

                    // set current datepicker state
                    datepicker.status = 'checkin-selected';

                    // create mouseover for table cell
                    jQuery('#datepicker').delegate('.ui-datepicker td', 'mouseover', function(){

                        // if it doesn't have year data (old month or unselectable date)
                        if ($(this).data('year') == undefined) return;

                        // datepicker state is not in date range select, depart date wasn't chosen, or return date already chosen then exit
                        if (datepicker.status != 'checkin-selected') return;

                        // get date from hovered cell
                        var hoverDate = jQuery(this).data('year')+'-'+(jQuery(this).data('month')+1)+'-'+jQuery('a',this).html();

                        // parse hovered date into milliseconds
                        hoverDate = $.datepicker.parseDate('yy-mm-dd', hoverDate).getTime();

                        jQuery('#datepicker td').each(function(){

                            // compare each table cell if it's date is in date range between selected date and hovered
                            if (jQuery(this).data('year') == undefined) return;

                            var year = jQuery(this).data('year'),
                                month = jQuery(this).data('month'),
                                day = jQuery('a', this).html();

                            var cellDate = jQuery(this).data('year')+'-'+(jQuery(this).data('month')+1)+'-'+jQuery('a',this).html();

                            // convert cell date into milliseconds for further comparison
                            cellDate = $.datepicker.parseDate('yy-mm-dd', cellDate).getTime();

                            if ( (cellDate >= datepicker.dates[0] && cellDate <= hoverDate) || (cellDate <= datepicker.dates[0] && cellDate >= hoverDate) ) {
                                jQuery(this).addClass('ui-datepicker-hover');
                            } else {
                                jQuery(this).removeClass('ui-datepicker-hover');
                            }

                        });
                    });

                } else {
                    // CHOOSE SECOND DATE

                    // push second date into dates array
                    datepicker.dates[1] = $.datepicker.parseDate(datepicker.dateFormat, dateText).getTime();

                    // sort array dates
                    datepicker.dates.sort();

                    var checkInDate = $.datepicker.parseDate('@', datepicker.dates[0]);
                    var checkOutDate = $.datepicker.parseDate('@', datepicker.dates[1]);

                    datepicker.status = 'checkout-selected';

//fill input fields
                    datepicker.inputs.checkin.val($.datepicker.formatDate(datepicker.dateFormat, checkInDate));
                    datepicker.inputs.checkout.val($.datepicker.formatDate(datepicker.dateFormat, checkOutDate)).change();
                    datepicker.inputs.dates.val(datepicker.inputs.checkin.val() + ' - ' + datepicker.inputs.checkout.val());

                }
            }
        });
    });
</script>

<script>
    let urlAjax = $("#urlAjax").val();
    jQuery(document).ready(function() {
        var productJson = '<?= $jsonproduct ?>';
        console.log(productJson);
        jQuery('#productType').on('change', function () {
            var selectedType = jQuery(this).val();

            // Clear the existing options in #productID
            jQuery('#productID').empty();

            // Check the selected type and update the #productID options accordingly
            if (selectedType === 'product') {
                jQuery('#productID').append('<option selected>Select product</option>');
                <?php foreach($products as $product): ?>
                    jQuery('#productID').append('<option value="<?= $product->ID ?>"><?= $product->post_title ?></option>');
                <?php endforeach; ?>
            } else if (selectedType === 'voucher') {
                jQuery('#productID').append('<option selected>Select voucher</option>');
                <?php foreach($vouchers as $voucher): ?>
                    jQuery('#productID').append('<option value="<?= $voucher->id ?>"><?= $voucher->voucher_name ?></option>');
                <?php endforeach; ?>
            } else if (selectedType === 'gift'){
                jQuery('#productID').append('<option selected>Select gift</option>');
                <?php foreach($gifts as $gift): ?>
                    jQuery('#productID').append('<option value="<?= $gift->ID ?>"><?= $gift->post_title ?></option>');
                <?php endforeach; ?>
            } else {
                jQuery('#productID').append('<option selected>------</option>');
            }
        });

        jQuery('#publish').on('click', function () {
            var formData = new FormData();

            // Lấy dữ liệu từ form và thêm vào FormData
            formData.append('name', jQuery("#nameProduct").val());
            formData.append('id_value', jQuery("#productID").val());
            formData.append('selectedValue', jQuery('#productType').val());
            formData.append('points', jQuery('#exchange_points').val());
            formData.append('status', jQuery('#status').val());
            formData.append('created_at', jQuery('#created_at').val());
            formData.append('expiration_date', jQuery('#expiration_date').val());
            formData.append('note', jQuery("#note").val());
            formData.append('purchases', jQuery("#purchases").val());
            formData.append('fileInput', jQuery('#inputGroupFile01')[0].files[0]);  // Gửi file
            formData.append('action', 'add_points');

            jQuery.ajax({
                url: urlAjax,
                type: 'POST',
                data: formData,
                processData: false,  // Không xử lý dữ liệu thành chuỗi query
                contentType: false,  // Không đặt content-type mặc định
                dataType: "json",
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
                },
                error: function (xhr, status, error) {
                    Swal.fire({
                        icon: 'error',
                        text: 'Something went wrong. Please try again!'
                    });
                    console.error('Error:', error);
                }
            });
            return false;
        });

    });
</script>





