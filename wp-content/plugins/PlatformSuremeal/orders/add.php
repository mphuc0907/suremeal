<?php
global $wpdb;
require_once __DIR__ . '/../includes/function.php';

//$myrows = $wpdb->get_results( "SELECT * FROM ".$wpdb->prefix."useragency" );
//$module_path = 'admin.php?page=daily';
$module_pathadd = 'admin.php?page=adddaily';
$module_short_url = str_replace('admin.php?page=', '', $module_pathadd);
$mess = '';
$mdlconf = array('title' => 'Tài khoản');
include __DIR__ . "/../includes/padding.php";

if (isset($_REQUEST['add_action']) && (int)$_REQUEST['add_action'] == 1) {


//    $username = $_POST['username'];
    $name = $_POST['name'];
    $phonenumber = $_POST['phonenumber'];
    $email = $_POST['email'];
    $phonenumberdl = $wpdb->get_row(
        "SELECT * FROM useragency where phonenumber = '" . $phonenumber . "'");
    $emaildl = $wpdb->get_row(
        "SELECT * FROM useragency where email = '" . $email . "'");
    if ($emaildl != null) {
        show_result(0, 'Email đã tồn tại');
    } elseif ($phonenumberdl != null) {
        show_result(0, 'Số điện thoại đã tồn tại');
    }  else{
        if(!empty($email)  && !empty($phonenumber)) {
            $ranStr = generateRandom(8);
            $password = hash('sha256', $ranStr . key_auth);
            $queryStr = "insert into useragency(`password`, `name`, `phonenumber`, `email`)  
    values('" . $password . "','" . $name . "','" . $phonenumber . "','" . $email . "')";
            $resp = $wpdb->query($queryStr);
            $rs_new_id = get_ID_last('useragency');
            if ($resp || (is_int($resp) && $resp >= 0)) {
                // Gửi mail
                $headers[] = "Content-type:text/html;charset=utf-8" . "\r\n";
                $titleMail = get_field('title_email_send_pass', 'option');
                $body = get_field('email_send_pass', 'option');
                $body = str_replace('__fullname__', $name, $body);
                $body = str_replace('__username__', $phonenumber, $body);
                $body = str_replace('__password__', $ranStr, $body);
                $body = str_replace('__timecreate__', date('H:i d/m/Y'), $body);
                $body = str_replace('__link__', get_permalink(getIdPage("login")), $body);
                wp_mail($email, 'Thông tin đăng nhập tài khoản', $body, $headers);
//                     Save mail
                $queryStrMail = "insert into history_email(`email`, `status`, `title`, `time`, `content`, `status_type`)
            values('" . $email . "','0','" . $titleMail . "','" . date('H:i d/m/Y') . "','" . $body . "','4')";
                $wpdb->query($queryStrMail);
                show_result(1, 'Thêm tài khoản thành công');
                unset($_POST);
            } else {
                show_result(0, 'Không thể thực hiện. Vui lòng xem lại.');
            }
        }{
//                show_result(0, 'Không thể thực hiện. Vui lòng xem lại 2.');
        }

    }

}
wp_enqueue_script('jquery');// jQuery
add_admin_css('main.css');

?>
<style>
    input {
        width: 100%;
    }
    .d-none{
        display: none;
    }

    .roles-report{

    }
</style>

<style>
    .roles-report{

    }
    .roles-report .item-role .table__wrapper{
        padding-bottom: 10px;
    }
    .roles-report .item-role{
        padding-bottom: 10px;
        border-bottom: 1px solid #ccc;
        padding-top: 10px;
    }

    .role-title-1{
        font-size: 20px;
        font-weight: 600;
        margin-bottom: 15px;
        text-transform: uppercase;
    }
    .role-title-2{
        font-size: 16px;
        padding-bottom: 10px;
        font-weight: 600;
    }
    .br-checkbox{
        padding-bottom: 10px;
    }
    .br-checkbox label{
        font-weight: 500;
    }
    .br-checkbox input{
        margin: 0;
    }
    .checkbox-all{
        padding-left: 15px;
    }
</style>

<div class="wrap">
    <h1>
        <?php show_admin_box_add_title($mdlconf, $module_pathadd); ?>
    </h1>

    <form id="adddaily" method="post" action="<?php echo $module_pathadd . '&add_action=1'; ?>" name="post">
        <div id="poststuff">
            <div class="metabox-holder columns-2" id="post-body">

                <!---left-->
                <div id="post-body-content" class="pos1">
                    <div class="postbox">
                        <h2 class="hndle ui-sortable-handle api-title">Thông tin</h2>
                        <div class="inside">
                            <!--                            <input type="hidden" value="-->
                            <?php //echo $id; ?><!--" name="id"/>-->
                            <table class="form-table ft_metabox leftform">

                                <?php
//                                show_list_row('Tài khoản', array('username', $_POST['username'], 'text'));
                                show_list_row('Họ và Tên', array('name',$_POST['name'], 'text'));
                                show_list_row('Số điện thoại', array('phonenumber', $_POST['phonenumber'], 'text'));
                                ?>
                                <tr>
                                    <td style="width:150px;">
                                        Email <span style="color: red">*</span>
                                    </td>
                                    <td>
                                        <input type="email" name="email" id="email" size="50" value="<?= $_POST['email'] ?>">
                                    </td>
                                </tr>

                            </table>
                        </div>
                    </div>
                </div>

                <!--right-->
                <div class="postbox-container" id="postbox-container-1">
                    <div class="meta-box-sortables ui-sortable" id="side-sortables">
                        <?php
                            show_admin_btn_save($row->active);
                        ?>
                    </div>
                </div>

            </div>
        </div>
    </form>

</div>

<?php
//add_admin_js('image.upload.js');
add_admin_js('jquery.min.js');
add_admin_js('jquery.validate.min.js');
?>

<script>
    $(document).ready(function () {
        $("#adddaily").validate({
            rules: {
                // username: {
                //     required: true,
                //     minlength: 3,
                // },
                name: {
                    required: true,
                    minlength: 3,
                },
                phonenumber: {
                    required: true,
                    minlength: 10,
                    maxlength: 11,
                    number: true
                },
                // address: {
                //     required: true,
                //     minlength: 3,
                // },
                email: {
                    required: true,
                    email: true,
                },
            },
            messages: {
                // username: {
                //     required: "Vui lòng nhập tài khoản",
                //     minlength: "Tối thiểu 3 ký tự",
                //
                // },
                name: {
                    required: "Vui lòng nhập họ tên",
                    minlength: "Tối thiểu 3 ký tự",
                },
                // address: {
                //     required: "Vui lòng nhập địa chỉ",
                //     minlength: "Tối thiểu 3 ký tự",
                // },
                email: {
                    required: "Vui lòng nhập email",
                    email: "Email không đúng định dạng",
                },
                phonenumber: {
                    required: "Vui lòng nhập số điện thoại",
                    number: "Số điện thoại không đúng định dạng",
                    minlength: "Số điện thoại không đúng định dạng",
                    maxlength: "Số điện thoại không đúng định dạng",
                },
            }
        });

        $(".checkboxAll").click(function(){
            let key = $(this).attr("data-id");
            $('.checkboxed-'+key).not(this).prop('checked', this.checked);
        });

    });
</script>
