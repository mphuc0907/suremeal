<?php
global $wpdb;
require_once __DIR__ . '/../includes/function.php';

//$myrows = $wpdb->get_results( "SELECT * FROM ".$wpdb->prefix."useragency" );
//$module_path = 'admin.php?page=daily';
$module_pathadd = 'admin.php?page=discount_price';
$module_short_url = str_replace('admin.php?page=', '', $module_pathadd);
$mess = '';
$mdlconf = array('title' => 'Mã giảm giá');
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
        font-family: OpenSans-Bold, sans-serif; }
    .pagnition .page-numbers:hover {
        background-color: #ee4d2d;
        color: #fff; }
    .pagnition span {
        margin-left: 0 !important; }
    .pagnition .prev i {
        font-size: 12px;
        padding: 3px 3px; }
    .pagnition .next i {
        font-size: 12px;
        padding: 3px 3px; }
    .pagnition .current {
        background-color: #ee4d2d;
        color: #fff; }

    .ov-hiden{
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
</style>
<div class="divgif">
    <img class="iconloadgif" src="<?= get_template_directory_uri() ?>/ajax/images/loading2.gif" alt="">
</div>
<div class="wrap">
    <input type="hidden" id="urlAjax" value="<?= admin_url() ?>admin-ajax.php">
<!--    <input type="hidden" id="site_key" value="--><?//= get_field("setting_captcha", "option")["site_key"] ?><!--">-->
    <input type="hidden" id="success_code" value="<?= success_code ?>">
    <h1>
        <?php show_admin_box_add_title($mdlconf, $module_pathadd); ?>
    </h1>
    <form id="adddform" method="post" action="<?php echo $module_pathadd . '&add_action=1'; ?>" name="post">
        <div id="poststuff">
            <div class="metabox-holder columns-2" id="post-body">
                <!---left-->
                <div id="post-body-content" class="pos1">
                    <div class="postbox">
                        <h2 class="hndle ui-sortable-handle api-title">Thông tin cơ bản</h2>
                        <div class="inside">
                            <!--                            <input type="hidden" value="-->
                            <?php //echo $id; ?><!--" name="id"/>-->
                            <table class="form-table ft_metabox leftform">
                                <tr>
                                    <td style="width: 250px;">Tên chương trình giảm giá <span
                                                class="red-validate">*</span>
                                    </td>
                                    <td>
                                        <input type="text" class="validate-input" id="ten_chuong_trinh"
                                               name="ten_chuong_trinh" value="" size="50" placeholder="Nhập vào">
                                        <label id="ten_chuong_trinh-error" class="error d-none" for="ten_chuong_trinh">Không
                                            được để trống ô</label>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Mã voucher <span class="red-validate">*</span>
                                    </td>
                                    <td>
                                        <input type="text" class="validate-input" id="ma_voucher" name="ma_voucher"
                                               value="" size="50" placeholder="Nhập vào">
                                        <label id="ma_voucher-error" class="error d-none" for="ma_voucher">Không được để
                                            trống ô</label>
                                        <p class="reminder-line">Vui lòng chỉ nhập các kí tự chữ cái (A-Z), số (0-9);
                                            tối đa 10 kí tự.</p>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        Thời gian sử dụng mã <span class="red-validate">*</span>
                                    </td>
                                    <td>
                                        <div class="date-time">
                                            <div class="time-start">
                                                <input readonly type="text" id="time_start" name="time_start"
                                                       value="<?= date('d/m/Y H:i', $datetime); ?>">
                                            </div>
                                            <div class="time-space">-</div>
                                            <div class="time-end">
                                                <input readonly type="text" id="time_end" name="time_end"
                                                       value="<?= date('d/m/Y H:i', $datetime + 3600); ?>">
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2"><h2 class="title-mgg">Thiết lập mã giảm giá</h2></td>
                                </tr>

                                <tr>
                                    <td>
                                        Loại giảm giá | Mức giảm<span class="red-validate">*</span>
                                    </td>
                                    <td>
                                        <div class="loaigiamgia">
                                            <div class="type-gg">
                                                <select name="" id="loai_giam_gia">
                                                    <option value="1">Theo số tiền</option>
                                                    <option value="2">Theo phần trăm</option>
                                                </select>
                                            </div>
                                            <div class="price-gg">
                                                <div class="type-input type-1">
                                                    <input type="number" class="" id="muc_giam" value=""
                                                           placeholder="Nhập vào">
                                                    <label id="muc_giam-error" class="error d-none" for="muc_giam">Không
                                                        được để trống ô</label>
                                                </div>
                                                <div class="type-input type-2 d-none">
                                                    <input type="number" class="" id="muc_giam_2" value=""
                                                           placeholder="Nhập giá trị lớn hơn 1%">
                                                    <label id="muc_giam_2-error" class="error d-none" for="muc_giam_2">Không
                                                        được để trống ô</label>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="tr-mucgiamtoida d-none">
                                    <td>
                                        Mức giảm tối đa<span class="red-validate">*</span>
                                    </td>
                                    <td>
                                        <div class="mucgiamtd">
                                            <div class="type-mgtd">
                                                <div class="typetd">
                                                    <input type="radio" class="checkboxmg" name="checkboxmg"
                                                           id="muc_giam_toi_da_1" value="1" checked> <label
                                                            for="muc_giam_toi_da_1">Giới hạn</label>
                                                </div>
                                                <div class="typetd">
                                                    <input type="radio" class="checkboxmg" name="checkboxmg"
                                                           id="muc_giam_toi_da_2" value="2"> <label
                                                            for="muc_giam_toi_da_2">Không giới hạn</label>
                                                </div>
                                            </div>
                                            <div class="price-mgtd">
                                                <input type="number" class="inputnumber" id="muc_giam_co_gioi_han"
                                                       value="0" placeholder="Nhập vào">
                                                <label id="muc_giam_co_gioi_han-error" class="error d-none"
                                                       for="muc_giam_co_gioi_han">Không được để trống ô</label>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr CLASS="d-none">
                                    <td>
                                        Giá trị đơn hàng tối thiểu <span class="red-validate">*</span>
                                    </td>
                                    <td>
                                        <div class="">
                                            <input type="number" class="inputnumber validate-input"
                                                   id="gia_tri_toi_thieu" value="1" placeholder="Nhập vào">
                                            <label id="gia_tri_toi_thieu-error" class="error d-none"
                                                   for="gia_tri_toi_thieu">Không được để trống ô</label>
                                            <label id="gia_tri_toi_thieu-error-2" class="error d-none">Giá trị voucher
                                                không thể vượt quá giá trị tối thiểu của đơn hàng.</label>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Tổng lượt sử dụng tối đa <span class="red-validate">*</span>
                                    </td>
                                    <td>
                                        <div class="">
                                            <input type="number" class="inputnumber validate-input"
                                                   id="tong_luot_sd_toi_da" value="" placeholder="Nhập vào">
                                            <label id="tong_luot_sd_toi_da-error" class="error d-none"
                                                   for="tong_luot_sd_toi_da">Không được để trống ô</label>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Lượt sử dụng tối đa/Người mua <span class="red-validate">*</span>
                                    </td>
                                    <td>
                                        <div class="">
                                            <input type="number" class="inputnumber validate-input"
                                                   id="luot_sd_toi_da_nguoi_mua" value="1" placeholder="Nhập vào">
                                            <label id="luot_sd_toi_da_nguoi_mua-error" class="error d-none"
                                                   for="luot_sd_toi_da_nguoi_mua">Không được để trống ô</label>
                                        </div>
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
                            <h2 class="hndle ui-sortable-handle"><span>Cập nhật</span></h2>
                            <div class="inside">
                                <div id="submitpost" class="submitbox">
                                    <div id="major-publishing-actions">
                                        <div id="publishing-action">
                                            <input type="button" value="Cập nhật" id="publish"
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

<div class="popup_delivery_information">
    <div class="modal-address">
        <div class="popup__content">
            <div class="title">
                <h2 class="title-change">Chọn sản phẩm</h2>
            </div>
            <button type="button" class="d-none ffchoice-button ffchoice-button--primary ffchoice-button--applyallpro check-column"
                    data-id="1">
                Áp dụng cho tất cả sản phẩm
            </button>
            <form id="memberSearch">
                <div class="member-search">
                    <input type="search" name="keyword" placeholder="Nhập họ tên, số điện thoại hoặc email">
                    <input type="submit" class="button button-primary" value="Tìm kiếm">
                </div>
            </form>
            <div style="margin-bottom: 20px; margin-top: 20px;" class="special-member-container">
                <input type="checkbox" name="specialMember" id="specialMember">
                <label for="specialMember">Áp dụng cho thành viên đặc biệt</label>
            </div>
            <div class="chang-form">
                <table class="wp-list-table widefat fixed striped posts">
                    <thead>
                    <tr class="headline">
                        <td class="manage-column column-cb check-column" id="cb">
                            <input type="checkbox" id="cb-select-all-1"></td>
                        <th style="width:40%;" class="t1">Sản Phẩm</th>
                        <th class="t2">Giá</th>
                        <th class="t3">Kho hàng</th>
                    </tr>
                    </thead>
                    <tfoot>
                    <tr class="headline">
                        <td class="manage-column column-cb check-column" id="cb">
                            <input type="checkbox" id="cb-select-all-1"></td>
                        <th class="t1">Sản Phẩm</th>
                        <th class="t2">Giá</th>
                        <th class="t3">Kho hàng</th>
                    </tr>
                    </tfoot>
                    <tbody class="body-list_product">
                        <tr class="d-none">
                            <th class="check-column" scope="row">
                                <input type="checkbox" value="1" name="post[]">
                            </th>
                            <td>
                                <div class="title-san_pham">
                                    <div class="i-m-g">
                                        <img src="https://cf.shopee.vn/file/ab0753c81663fd8c51af5badca5f281d_tn" alt="">
                                    </div>
                                    <div class="info-san_pham">
                                        <p>Bọc tai nghe cute Airpods (New)</p>
                                    </div>
                                </div>
                            </td>
                            <td><span>đ 25000</span> - <span>đ 30000</span></td>
                            <td><span>2.000</span></td>
                        </tr>
                    </tbody>
                </table>

                <div class="pagnition d-none">
                    <span class="page-numbers current">1</span>
                    <span class="page-numbers">2</span>
                    <span class="page-numbers">3</span>
                </div>
            </div>
            <div class="footer-pup">
                <div class="ffchoice-modal__footer-buttons">
                    <button type="button" class="ffchoice-button ffchoice-button--normal ffchoice-button-close">
                        <span>Hủy</span>
                    </button>
                    <button type="button"
                            class="ffchoice-button ffchoice-button--primary ffchoice-button--normal ffchoice-button-change">
                        <span>Xác nhận</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
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
                start: {}, // start picker options
                end: {}, // end picker options
                controlType: 'select',
                timeText: 'Thời gian',
                hourText: 'Giờ',
                minuteText: 'Phút',
                closeText: 'Xác nhận',
            },
        );
    });
</script>
<script>
    $(document).ready(function () {
        var keyword = '';
        var dataChange = [];
        var statuskieuapdung=1,statusmucgiamtoida=1; // statuskieuapdung,1: Áp dụng cho sản phẩm, 2: Áp dụng cho thành viên
        // statusmucgiamtoida: 1: Giới hạn, 2: Không giới hạn
        // statuskieuapdung: 1: Áp dụng cho sản phẩm, 2: Áp dụng cho user
        let urlAjax = $("#urlAjax").val();
        let site_key = $("#site_key").val();
        let success_code = $("#success_code").val();
        $("body").append('<div class="bg__overlay"></div>');

        // Popup nhận hàng
        $("body").on("click", ".delivery_information", function () {
            $("body").addClass("ov-hiden");
            var id = $(this).attr("data-id");
            $("#checkkey").val(id);
            var checkData = $("#checkData").val();
            $(".popup_delivery_information").addClass("active_delivery");
            $(".bg__overlay").addClass("d-block");
            if(checkData == 1){
                loadDefault();
            }

        });
        $("body").on("click", ".ffchoice-button-close", function () {
            closePopup();
        })
        function closePopup(){
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
                $(this).val(inputVal.replace(/[^A-Z0-9]/g, '').substring(0, 10)); //remove any non-alphanumeric characters and limit to 5 characters
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
            }else{ // TH giảm giá theo %

            }
            // End TH giảm giá
            // Check chọn sản phẩm
            if(dataChange.length == 0){
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

                var muc_giam_co_gioi_han = $("#muc_giam_co_gioi_han").val();
                var gia_tri_toi_thieu = $("#gia_tri_toi_thieu").val();
                var tong_luot_sd_toi_da = $("#tong_luot_sd_toi_da").val();
                var luot_sd_toi_da_nguoi_mua = $("#luot_sd_toi_da_nguoi_mua").val();
                var special_member = $('#specialMember').prop('checked');


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
                                tong_luot_sd_toi_da,
                                luot_sd_toi_da_nguoi_mua,
                                statuskieuapdung:statuskieuapdung,
                                statusmucgiamtoida:statusmucgiamtoida,
                                loai_giam_gia,
                                // dataChange,
                                // special_member,
                                action: 'add_voucher',
                                // action1: "subscribe_save_mgg",
                                // token1: token
                            },
                            beforeSend: function () {
                                $('.divgif').css('display', 'block');
                            },
                            success: function (rs) {
                                $('.divgif').css('display', 'none');
                                if (rs.status == success_code) {
                                    Swal.fire({
                                        icon: 'success',
                                        text: rs.mess,
                                    });
                                    setTimeout(function () {
                                        window.location.reload();
                                    }, 1000);
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        text: rs.mess,
                                    });
                                }
                            }
                        });
                        return false;


            }
        });

        // Mức giảm theo phần trăm
        $("body").on("keyup","#muc_giam_2", function () {
            var val = parseInt($(this).val());
            if(val < 0){
                $(this).val(0);
            }
            if(val > 69){
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

            if(arrayID.length != 0){
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
                $(".t1").text("Sản phẩm");
                $(".t2").text("Giá");
                $(".t3").text("Kho hàng");
                $(".title-change").text("Chọn sản phẩm");
                $(".ffchoice-button--applyallpro").text("Áp dụng cho tất cả sản phẩm");
            } else {
                $('.member-search').removeClass('d-none');
                $('.special-member-container').removeClass('d-none');
                $(".t1").text("Họ và tên");
                $(".t2").text("Số điện thoại");
                $(".t3").text("Email");
                $(".title-change").text("Chọn thành viên");
                $(".ffchoice-button--applyallpro").text("Áp dụng cho tất cả thành viên");
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





