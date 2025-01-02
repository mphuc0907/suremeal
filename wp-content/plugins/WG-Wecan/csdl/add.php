<?php
global $wpdb;
require_once __DIR__ . '/../includes/function.php';

//$myrows = $wpdb->get_results( "SELECT * FROM ".$wpdb->prefix."useragency" );
//$module_path = 'admin.php?page=daily';
$module_pathadd = 'admin.php?page=adddaily';
$module_short_url = str_replace('admin.php?page=', '', $module_pathadd);
$mess = '';
$mdlconf = array('title' => 'Đại lý');
include __DIR__ . "/../includes/padding.php";

if (isset($_REQUEST['add_action']) && (int)$_REQUEST['add_action'] == 1) {

    // Roles
    $checkboxed_1 = $_POST['checkboxed_1'];
    $checkboxed_2 = $_POST['checkboxed_2'];
    $checkboxed_3 = $_POST['checkboxed_3'];
    $checkboxed_4 = $_POST['checkboxed_4'];
    $checkboxed_5 = $_POST['checkboxed_5'];
    $checkboxed_6 = $_POST['checkboxed_6'];

    $roles = [
            "checkboxed_1"=>$checkboxed_1,
            "checkboxed_2"=>$checkboxed_2,
            "checkboxed_3"=>$checkboxed_3,
            "checkboxed_4"=>$checkboxed_4,
            "checkboxed_5"=>$checkboxed_5,
            "checkboxed_6"=>$checkboxed_6,
    ];
    $rolesJson = json_encode($roles, JSON_UNESCAPED_UNICODE);
    // End Roles -------

    $username = $_POST['username'];
    $name = $_POST['name'];
    $address = $_POST['address'];
    $phonenumber = $_POST['phonenumber'];
    $email = $_POST['email'];
    $loaitaikhoan = $_POST['loaitaikhoan'];
    $bonganh = $_POST['bonganh'];
    $diaphuong = $_POST['diaphuong'];
    $donvikhac = $_POST['donvikhac'];

    // Xử lý loại tài khoản
    $co_quan = '';
    $text_co_quan = '';
    $dia_phuong = '';
    $text_dia_phuong = '';
    $dovi_khac = '';
    $text_donvi_khac = '';
    // Điều kiện
    $dkKey = '';
    $dkValue = '';
    //
    if($loaitaikhoan == 1){
        $termCheck = get_term_by('slug',$bonganh,'coquan');
        $co_quan = $termCheck->slug;
        $text_co_quan = $termCheck->name;
        $dkKey = 'co_quan';
        $dkValue = $termCheck->slug;
    }elseif ($loaitaikhoan == 2){
        $termCheck = get_term_by('slug',$diaphuong,'tinh');
        $dia_phuong = $termCheck->slug;
        $text_dia_phuong = $termCheck->name;
        $dkKey = 'dia_phuong';
        $dkValue = $termCheck->slug;
    }else{
        $dovi_khac = sanitize_title($donvikhac);
        $text_donvi_khac = $donvikhac;
        $dkKey = 'dovi_khac';
        $dkValue = $dovi_khac;
    }
    // Kiểm tra tồn tại loại tài khoản này chưa
    $userCheck = $wpdb->get_results("SELECT * FROM useragency where loaitaikhoan=".$loaitaikhoan." and ".$dkKey."= '" . $dkValue . "'");
    if(!empty($userCheck)){
        show_result(0, 'Loại tài khoản đã tồn tại');
    }else{
        // Check validate
        $usernamedl = $wpdb->get_row(
            "SELECT * FROM useragency where username = '" . $username . "'");
        $phonenumberdl = $wpdb->get_row(
            "SELECT * FROM useragency where phonenumber = '" . $phonenumber . "'");
        $emaildl = $wpdb->get_row(
            "SELECT * FROM useragency where email = '" . $email . "'");
        if ($usernamedl != null) {
            show_result(0, 'Tài khoản đã tồn tại');
        } elseif ($emaildl != null){
            show_result(0, 'Email đã tồn tại');
        } else{

            if(!empty($username) && !empty($name) && !empty($address) && !empty($phonenumber) && !empty($rolesJson)) {
                $ranStr = generateRandom(8);
                $password = hash('sha256', $ranStr . key_auth);
                $queryStr = "insert into useragency(`username`, `password`, `name`, `phonenumber`, `email`, `address`, `co_quan`, `text_co_quan`, `dia_phuong`, `text_dia_phuong`, `dovi_khac`, `text_donvi_khac`, `loaitaikhoan`, `roles_json`)  
        values('" . $username . "','" . $password . "','" . $name . "','" . $phonenumber . "','" . $email . "','" . $address . "','" . $co_quan . "',  '" . $text_co_quan . "',  '" . $dia_phuong . "',  '" . $text_dia_phuong . "','" . $dovi_khac . "','" . $text_donvi_khac . "','".$loaitaikhoan."','".$rolesJson."')";
                $resp = $wpdb->query($queryStr);
                $rs_new_id = get_ID_last('useragency');
                if ($resp || (is_int($resp) && $resp >= 0)) {
                    // Gửi mail
                    $headers[] = "Content-type:text/html;charset=utf-8" . "\r\n";
                    $titleMail = get_field('title_email_send_pass', 'option');
                    $body = get_field('email_send_pass', 'option');
                    $body = str_replace('__fullname__', $name, $body);
                    $body = str_replace('__username__', $username, $body);
                    $body = str_replace('__password__', $ranStr, $body);
                    $body = str_replace('__timecreate__', date('H:i d/m/Y'), $body);
                    $body = str_replace('__link__', home_url() . '/dashboard/', $body);
                    wp_mail($email, 'Thông tin đăng nhập tài khoản', $body, $headers);
                    // Save mail
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
}
wp_enqueue_script('jquery');// jQuery
add_admin_css('main.css');
//$city = file_get_contents(plugin_dir_path(__FILE__) . 'vn_city.json');
//$json = json_decode($city, true);
$loaitk = arrayLoaiTK();
$term_tinh = get_terms('tinh',
    array(
        'orderby' => 'name',
        'order'      => 'DESC',
        'hide_empty' => false,
    )
);
$term_coquan = get_terms('coquan',
    array(
        'orderby' => 'name',
        'order'      => 'DESC',
        'hide_empty' => false,
    )
);

$id1 = getIdPage("danhgia-01");
$id2 = getIdPage("danhgia-02");
$id3 = getIdPage("danhgia-03");
$id4 = getIdPage("danhgia-04");
$id5 = getIdPage("danhgia-05");
$id6 = getIdPage("danhgia-06");

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
                                show_list_row('Tài khoản', array('username', $_POST['username'], 'text'));
                                show_list_row('Họ và Tên', array('name',$_POST['name'], 'text'));
                                show_list_row('Số điện thoại', array('phonenumber', $_POST['phonenumber'], 'text'));
                                show_list_row('Địa chỉ', array('address', $_POST['address'], 'text'));
                                ?>
                                <tr>
                                    <td style="width:150px;">
                                        Email <span style="color: red">*</span>
                                    </td>
                                    <td>
                                        <input type="email" name="email" id="email" size="50" value="<?= $_POST['email'] ?>">
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label for="loaitaikhoan">Loại tài khoản <span style="color: red">*</span></label>
                                    </td>
                                    <td>
                                        <select name="loaitaikhoan" id="loaitaikhoan" class="form-control">
                                            <option value="">Chọn loại tài khoản</option>
                                            <?php foreach ($loaitk as $value): ?>
                                                <option class="select-items" <?= ($value['key'] == $_REQUEST['loaitaikhoan'])?'selected':'' ?> value="<?= $value['key'] ?>"><?= $value['value'] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                </tr>

                                <tr>
                                    <td>

                                    </td>
                                    <td>
                                        <div class="tab-detail tab-detail-1 <?= $_POST['loaitaikhoan']==1?'':'d-none' ?>">
                                            <div class="item-detail-1">
                                                <select name="bonganh" id="bonganh" class="form-control">
                                                    <?php foreach ($term_coquan as $key => $tc): ?>
                                                        <option class="select-items" <?= ($value['slug'] == $_POST['bonganh'])?'selected':'' ?> value="<?= $tc->slug ?>"><?= $tc->name ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="tab-detail tab-detail-2 <?= $_POST['loaitaikhoan']==2?'':'d-none' ?>">
                                            <div class="item-detail-2">
                                                <select name="diaphuong" id="diaphuong" class="form-control">
                                                    <?php foreach ($term_tinh as $key => $tc): ?>
                                                        <option class="select-items" <?= ($value['slug'] == $_POST['diaphuong'])?'selected':'' ?> value="<?= $tc->slug ?>"><?= $tc->name ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="tab-detail tab-detail-3 <?= $_POST['loaitaikhoan']==3?'':'d-none' ?>">
                                            <div class="item-detail-3">
                                                <input type="text" name="donvikhac" id="donvikhac" size="50" value="<?= $_POST['donvikhac'] ?>" />
                                            </div>
                                        </div>
                                    </td>
                                </tr>


                                <tr>
                                    <td style="vertical-align: text-bottom;">Phân quyền báo cáo</td>
                                    <td>
                                        <div class="roles-report">

                                    <!-- Báo cáo 1 -->
                                            <div class="item-role">
                                                <div class="role-title-1">
                                                    I. <?php the_field('title', $id1) ?>
                                                </div>

                                                <div class="br-checkbox">
                                                    <input id="check-all-1" class="checkboxAll" data-id="1" type="checkbox" checked />
                                                    <label for="check-all-1">Tất cả</label>
                                                </div>
                                                <?php
                                                    $content = get_field('content', $id1);
                                                ?>
                                                <div class="table__wrapper">

                                                    <div class="role-title-2">
                                                        <?php the_field('muc1', $id1) ?>
                                                    </div>

                                                    <div class="checkbox-all">

                                                        <?php foreach ($content as $key => $ct): ?>
                                                        <?php
                                                            $dataParent = '1'.($key+1);
                                                            $dataText = '1.'.($key+1);
                                                        ?>
                                                            <div class="br-checkbox">
                                                                <input id="check-state-1-<?= $dataParent ?>" value="<?= $dataParent ?>" class="checkboxed-1" name="checkboxed_1[]" type="checkbox"  checked/>
                                                                <label for="check-state-1-<?= $dataParent ?>">Mục <?= $dataText ?></label>
                                                            </div>
                                                        <?php endforeach ?>

                                                    </div>
                                                </div>

                                                <?php
                                                $content = get_field('content2', $id1);
                                                ?>
                                                <div class="table__wrapper">
                                                    <div class="role-title-2">
                                                        <?php the_field('muc2', $id1) ?>
                                                    </div>
                                                    <div class="checkbox-all">

                                                        <?php foreach ($content as $key => $ct): ?>
                                                            <?php
                                                            $dataParent = '2'.($key+1);
                                                            $dataText = '2.'.($key+1);
                                                            ?>
                                                            <div class="br-checkbox">
                                                                <input id="check-state-1-<?= $dataParent ?>" value="<?= $dataParent ?>" class="checkboxed-1" name="checkboxed_1[]" type="checkbox"  checked/>
                                                                <label for="check-state-1-<?= $dataParent ?>">Mục <?= $dataText ?></label>
                                                            </div>
                                                        <?php endforeach ?>

                                                    </div>
                                                </div>

                                                <?php
                                                $content = get_field('content3', $id1);
                                                ?>
                                                <div class="table__wrapper">
                                                    <div class="role-title-2">
                                                        <?php the_field('muc3', $id1) ?>
                                                    </div>
                                                    <div class="checkbox-all">

                                                        <?php foreach ($content as $key => $ct): ?>
                                                            <?php
                                                            $dataParent = '3'.($key+1);
                                                            $dataText = '3.'.($key+1);
                                                            ?>
                                                            <div class="br-checkbox">
                                                                <input id="check-state-1-<?= $dataParent ?>" value="<?= $dataParent ?>" class="checkboxed-1" name="checkboxed_1[]" type="checkbox"  checked/>
                                                                <label for="check-state-1-<?= $dataParent ?>">Mục <?= $dataText ?></label>
                                                            </div>
                                                        <?php endforeach ?>

                                                    </div>
                                                </div>

                                                <?php
                                                $content = get_field('content4', $id1);
                                                ?>
                                                <div class="table__wrapper">
                                                    <div class="role-title-2">
                                                        <?php the_field('muc4', $id1) ?>
                                                    </div>
                                                    <div class="checkbox-all">

                                                        <?php foreach ($content as $key => $ct): ?>
                                                            <?php
                                                            $dataParent = '4'.($key+1);
                                                            $dataText = '4.'.($key+1);
                                                            ?>
                                                            <div class="br-checkbox">
                                                                <input id="check-state-1-<?= $dataParent ?>" value="<?= $dataParent ?>" class="checkboxed-1" name="checkboxed_1[]" type="checkbox"  checked/>
                                                                <label for="check-state-1-<?= $dataParent ?>">Mục <?= $dataText ?></label>
                                                            </div>
                                                        <?php endforeach ?>

                                                    </div>
                                                </div>

                                                <?php
                                                $content = get_field('content5', $id1);
                                                ?>
                                                <div class="table__wrapper">
                                                    <div class="role-title-2">
                                                        <?php the_field('muc5', $id1) ?>
                                                    </div>
                                                    <div class="checkbox-all">

                                                        <?php foreach ($content as $key => $ct): ?>
                                                            <?php
                                                            $dataParent = '5'.($key+1);
                                                            $dataText = '5.'.($key+1);
                                                            ?>
                                                            <div class="br-checkbox">
                                                                <input id="check-state-1-<?= $dataParent ?>" value="<?= $dataParent ?>" class="checkboxed-1" name="checkboxed_1[]" type="checkbox"  checked/>
                                                                <label for="check-state-1-<?= $dataParent ?>">Mục <?= $dataText ?></label>
                                                            </div>
                                                        <?php endforeach ?>

                                                    </div>
                                                </div>

                                                <?php
                                                $content = get_field('content6', $id1);
                                                ?>
                                                <div class="table__wrapper">
                                                    <div class="role-title-2">
                                                        <?php the_field('muc6', $id1) ?>
                                                    </div>
                                                    <div class="checkbox-all">

                                                        <?php foreach ($content as $key => $ct): ?>
                                                            <?php
                                                            $dataParent = '6'.($key+1);
                                                            $dataText = '6.'.($key+1);
                                                            ?>
                                                            <div class="br-checkbox">
                                                                <input id="check-state-1-<?= $dataParent ?>" value="<?= $dataParent ?>" class="checkboxed-1" name="checkboxed_1[]" type="checkbox"  checked/>
                                                                <label for="check-state-1-<?= $dataParent ?>">Mục <?= $dataText ?></label>
                                                            </div>
                                                        <?php endforeach ?>

                                                    </div>
                                                </div>

                                            </div>
                                    <!-- Báo cáo 2 -->
                                            <div class="item-role">
                                                <div class="role-title-1">
                                                    II. <?php the_field('title', $id2) ?>
                                                </div>

                                                <div class="br-checkbox">
                                                    <input id="check-all-2" class="checkboxAll" data-id="2" name="check-state-checked" type="checkbox" checked />
                                                    <label for="check-all-2">Tất cả</label>
                                                </div>
                                                <?php
                                                $content = get_field('content', $id2);
                                                ?>
                                                <div class="table__wrapper">

                                                    <div class="role-title-2">
                                                        <?php the_field('muc1', $id2) ?>
                                                    </div>

                                                    <div class="checkbox-all">

                                                        <?php foreach ($content as $key => $ct): ?>
                                                            <?php
                                                            $dataParent = '1'.($key+1);
                                                            $dataText = '1.'.($key+1);
                                                            ?>
                                                            <div class="br-checkbox">
                                                                <input id="check-state-2-<?= $dataParent ?>" value="<?= $dataParent ?>" class="checkboxed-2" name="checkboxed_2[]" type="checkbox"  checked/>
                                                                <label for="check-state-2-<?= $dataParent ?>">Mục <?= $dataText ?></label>
                                                            </div>
                                                        <?php endforeach ?>

                                                    </div>
                                                </div>

                                                <?php
                                                $content = get_field('content2', $id2);
                                                ?>
                                                <div class="table__wrapper">
                                                    <div class="role-title-2">
                                                        <?php the_field('muc2', $id2) ?>
                                                    </div>
                                                    <div class="checkbox-all">

                                                        <?php foreach ($content as $key => $ct): ?>
                                                            <?php
                                                            $dataParent = '2'.($key+1);
                                                            $dataText = '2.'.($key+1);
                                                            ?>
                                                            <div class="br-checkbox">
                                                                <input id="check-state-2-<?= $dataParent ?>" value="<?= $dataParent ?>" class="checkboxed-2" name="checkboxed_2[]" type="checkbox"  checked/>
                                                                <label for="check-state-2-<?= $dataParent ?>">Mục <?= $dataText ?></label>
                                                            </div>
                                                        <?php endforeach ?>

                                                    </div>
                                                </div>

                                                <?php
                                                $content = get_field('content3', $id2);
                                                ?>
                                                <div class="table__wrapper">
                                                    <div class="role-title-2">
                                                        <?php the_field('muc3', $id2) ?>
                                                    </div>
                                                    <div class="checkbox-all">

                                                        <?php foreach ($content as $key => $ct): ?>
                                                            <?php
                                                            $dataParent = '3'.($key+1);
                                                            $dataText = '3.'.($key+1);
                                                            ?>
                                                            <div class="br-checkbox">
                                                                <input id="check-state-2-<?= $dataParent ?>" value="<?= $dataParent ?>" class="checkboxed-2" name="checkboxed_2[]" type="checkbox"  checked/>
                                                                <label for="check-state-2-<?= $dataParent ?>">Mục <?= $dataText ?></label>
                                                            </div>
                                                        <?php endforeach ?>

                                                    </div>
                                                </div>

                                                <?php
                                                $content = get_field('content4', $id2);
                                                ?>
                                                <div class="table__wrapper">
                                                    <div class="role-title-2">
                                                        <?php the_field('muc4', $id2) ?>
                                                    </div>
                                                    <div class="checkbox-all">

                                                        <?php foreach ($content as $key => $ct): ?>
                                                            <?php
                                                            $dataParent = '4'.($key+1);
                                                            $dataText = '4.'.($key+1);
                                                            ?>
                                                            <div class="br-checkbox">
                                                                <input id="check-state-2-<?= $dataParent ?>" value="<?= $dataParent ?>" class="checkboxed-2" name="checkboxed_2[]" type="checkbox"  checked/>
                                                                <label for="check-state-2-<?= $dataParent ?>">Mục <?= $dataText ?></label>
                                                            </div>
                                                        <?php endforeach ?>

                                                    </div>
                                                </div>

                                                <?php
                                                $content = get_field('content5', $id2);
                                                ?>
                                                <div class="table__wrapper">
                                                    <div class="role-title-2">
                                                        <?php the_field('muc5', $id2) ?>
                                                    </div>
                                                    <div class="checkbox-all">

                                                        <?php foreach ($content as $key => $ct): ?>
                                                            <?php
                                                            $dataParent = '5'.($key+1);
                                                            $dataText = '5.'.($key+1);
                                                            ?>
                                                            <div class="br-checkbox">
                                                                <input id="check-state-2-<?= $dataParent ?>" value="<?= $dataParent ?>" class="checkboxed-2" name="checkboxed_2[]" type="checkbox"  checked/>
                                                                <label for="check-state-2-<?= $dataParent ?>">Mục <?= $dataText ?></label>
                                                            </div>
                                                        <?php endforeach ?>

                                                    </div>
                                                </div>

                                                <?php
                                                $content = get_field('content6', $id2);
                                                ?>
                                                <div class="table__wrapper">
                                                    <div class="role-title-2">
                                                        <?php the_field('muc6', $id2) ?>
                                                    </div>
                                                    <div class="checkbox-all">

                                                        <?php foreach ($content as $key => $ct): ?>
                                                            <?php
                                                            $dataParent = '6'.($key+1);
                                                            $dataText = '6.'.($key+1);
                                                            ?>
                                                            <div class="br-checkbox">
                                                                <input id="check-state-2-<?= $dataParent ?>" value="<?= $dataParent ?>" class="checkboxed-2" name="checkboxed_2[]" type="checkbox"  checked/>
                                                                <label for="check-state-2-<?= $dataParent ?>">Mục <?= $dataText ?></label>
                                                            </div>
                                                        <?php endforeach ?>

                                                    </div>
                                                </div>


                                                <?php
                                                $content = get_field('content7', $id2);
                                                ?>
                                                <div class="table__wrapper">
                                                    <div class="role-title-2">
                                                        <?php the_field('muc7', $id2) ?>
                                                    </div>
                                                    <div class="checkbox-all">

                                                        <?php foreach ($content as $key => $ct): ?>
                                                            <?php
                                                            $dataParent = '7'.($key+1);
                                                            $dataText = '7.'.($key+1);
                                                            ?>
                                                            <div class="br-checkbox">
                                                                <input id="check-state-2-<?= $dataParent ?>" value="<?= $dataParent ?>" class="checkboxed-2" name="checkboxed_2[]" type="checkbox"  checked/>
                                                                <label for="check-state-2-<?= $dataParent ?>">Mục <?= $dataText ?></label>
                                                            </div>
                                                        <?php endforeach ?>

                                                    </div>
                                                </div>

                                                <?php
                                                $content = get_field('content8', $id2);
                                                ?>
                                                <div class="table__wrapper">
                                                    <div class="role-title-2">
                                                        <?php the_field('muc8', $id2) ?>
                                                    </div>
                                                    <div class="checkbox-all">

                                                        <?php foreach ($content as $key => $ct): ?>
                                                            <?php
                                                            $dataParent = '8'.($key+1);
                                                            $dataText = '8.'.($key+1);
                                                            ?>
                                                            <div class="br-checkbox">
                                                                <input id="check-state-2-<?= $dataParent ?>" value="<?= $dataParent ?>" class="checkboxed-2" name="checkboxed_2[]" type="checkbox"  checked/>
                                                                <label for="check-state-2-<?= $dataParent ?>">Mục <?= $dataText ?></label>
                                                            </div>
                                                        <?php endforeach ?>

                                                    </div>
                                                </div>

                                                <?php
                                                $content = get_field('content9', $id2);
                                                ?>
                                                <div class="table__wrapper">
                                                    <div class="role-title-2">
                                                        <?php the_field('muc9', $id2) ?>
                                                    </div>
                                                    <div class="checkbox-all">

                                                        <?php foreach ($content as $key => $ct): ?>
                                                            <?php
                                                            $dataParent = '9'.($key+1);
                                                            $dataText = '9.'.($key+1);
                                                            ?>
                                                            <div class="br-checkbox">
                                                                <input id="check-state-2-<?= $dataParent ?>" value="<?= $dataParent ?>" class="checkboxed-2" name="checkboxed_2[]" type="checkbox"  checked/>
                                                                <label for="check-state-2-<?= $dataParent ?>">Mục <?= $dataText ?></label>
                                                            </div>
                                                        <?php endforeach ?>

                                                    </div>
                                                </div>

                                            </div>
                                    <!-- Báo cáo 3 -->
                                            <div class="item-role">
                                                <div class="role-title-1">
                                                    III. <?php the_field('title', $id3) ?>
                                                </div>

                                                <div class="br-checkbox">
                                                    <input id="check-all-3" class="checkboxAll" data-id="3" type="checkbox" checked />
                                                    <label for="check-all-3">Tất cả</label>
                                                </div>
                                                <?php
                                                $content = get_field('content', $id3);
                                                ?>
                                                <div class="table__wrapper">

                                                    <div class="role-title-2">
                                                        <?php the_field('muc1', $id3) ?>
                                                    </div>

                                                    <div class="checkbox-all">

                                                        <?php foreach ($content as $key => $ct): ?>
                                                            <?php
                                                            $dataParent = '1'.($key+1);
                                                            $dataText = '1.'.($key+1);
                                                            ?>
                                                            <div class="br-checkbox">
                                                                <input id="check-state-3-<?= $dataParent ?>" value="<?= $dataParent ?>" class="checkboxed-3" name="checkboxed_3[]" type="checkbox"  checked/>
                                                                <label for="check-state-3-<?= $dataParent ?>">Mục <?= $dataText ?></label>
                                                            </div>
                                                        <?php endforeach ?>

                                                    </div>
                                                </div>

                                                <?php
                                                $content = get_field('content2', $id3);
                                                ?>
                                                <div class="table__wrapper">
                                                    <div class="role-title-2">
                                                        <?php the_field('muc2', $id3) ?>
                                                    </div>
                                                    <div class="checkbox-all">

                                                        <?php foreach ($content as $key => $ct): ?>
                                                            <?php
                                                            $dataParent = '2'.($key+1);
                                                            $dataText = '2.'.($key+1);
                                                            ?>
                                                            <div class="br-checkbox">
                                                                <input id="check-state-3-<?= $dataParent ?>" value="<?= $dataParent ?>" class="checkboxed-3" name="checkboxed_3[]" type="checkbox"  checked/>
                                                                <label for="check-state-3-<?= $dataParent ?>">Mục <?= $dataText ?></label>
                                                            </div>
                                                        <?php endforeach ?>

                                                    </div>
                                                </div>

                                            </div>
                                    <!-- Báo cáo 4 -->
                                            <div class="item-role">
                                                <div class="role-title-1">
                                                    IV. <?php the_field('title', $id4) ?>
                                                </div>

                                                <div class="br-checkbox">
                                                    <input id="check-all-4" class="checkboxAll" data-id="4" type="checkbox" checked />
                                                    <label for="check-all-4">Tất cả</label>
                                                </div>
                                                <?php
                                                $content = get_field('content', $id4);
                                                ?>
                                                <div class="table__wrapper">

                                                    <div class="role-title-2">
                                                        <?php the_field('muc1', $id4) ?>
                                                    </div>

                                                    <div class="checkbox-all">

                                                        <?php foreach ($content as $key => $ct): ?>
                                                            <?php
                                                            $dataParent = '1'.($key+1);
                                                            $dataText = '1.'.($key+1);
                                                            ?>
                                                            <div class="br-checkbox">
                                                                <input id="check-state-4-<?= $dataParent ?>" value="<?= $dataParent ?>" class="checkboxed-4" name="checkboxed_4[]" type="checkbox"  checked/>
                                                                <label for="check-state-4-<?= $dataParent ?>">Mục <?= $dataText ?></label>
                                                            </div>
                                                        <?php endforeach ?>

                                                    </div>
                                                </div>

                                            </div>
                                    <!-- Báo cáo 5 -->
                                            <div class="item-role">
                                                <div class="role-title-1">
                                                    V. <?php the_field('title', $id5) ?>
                                                </div>

                                                <div class="br-checkbox">
                                                    <input id="check-all-5" class="checkboxAll" data-id="5" type="checkbox" checked />
                                                    <label for="check-all-5">Tất cả</label>
                                                </div>
                                                <?php
                                                $content = get_field('content', $id5);
                                                ?>
                                                <div class="table__wrapper">

                                                    <div class="role-title-2">
                                                        <?php the_field('muc1', $id5) ?>
                                                    </div>

                                                    <div class="checkbox-all">

                                                        <?php foreach ($content as $key => $ct): ?>
                                                            <?php
                                                            $dataParent = '1'.($key+1);
                                                            $dataText = '1.'.($key+1);
                                                            ?>
                                                            <div class="br-checkbox">
                                                                <input id="check-state-5-<?= $dataParent ?>" value="<?= $dataParent ?>" class="checkboxed-5" name="checkboxed_5[]" type="checkbox"  checked/>
                                                                <label for="check-state-5-<?= $dataParent ?>">Mục <?= $dataText ?></label>
                                                            </div>
                                                        <?php endforeach ?>

                                                    </div>
                                                </div>

                                                <?php
                                                $content = get_field('content2', $id5);
                                                ?>
                                                <div class="table__wrapper">
                                                    <div class="role-title-2">
                                                        <?php the_field('muc2', $id5) ?>
                                                    </div>
                                                    <div class="checkbox-all">

                                                        <?php foreach ($content as $key => $ct): ?>
                                                            <?php
                                                            $dataParent = '2'.($key+1);
                                                            $dataText = '2.'.($key+1);
                                                            ?>
                                                            <div class="br-checkbox">
                                                                <input id="check-state-5-<?= $dataParent ?>" value="<?= $dataParent ?>" class="checkboxed-5" name="checkboxed_5[]" type="checkbox"  checked/>
                                                                <label for="check-state-5-<?= $dataParent ?>">Mục <?= $dataText ?></label>
                                                            </div>
                                                        <?php endforeach ?>

                                                    </div>
                                                </div>

                                            </div>
                                    <!-- Báo cáo 6 -->
                                            <div class="item-role">
                                                <div class="role-title-1">
                                                    VI. <?php the_field('title', $id6) ?>
                                                </div>

                                                <div class="br-checkbox">
                                                    <input id="check-all-6" class="checkboxAll" data-id="6" type="checkbox" checked />
                                                    <label for="check-all-6">Tất cả</label>
                                                </div>
                                                <?php
                                                $content = get_field('content', $id6);
                                                ?>
                                                <div class="table__wrapper">

                                                    <div class="role-title-2">
                                                        <?php the_field('muc1', $id6) ?>
                                                    </div>

                                                    <div class="checkbox-all">

                                                        <?php foreach ($content as $key => $ct): ?>
                                                            <?php
                                                            $dataParent = '1'.($key+1);
                                                            $dataText = '1.'.($key+1);
                                                            ?>
                                                            <div class="br-checkbox">
                                                                <input id="check-state-6-<?= $dataParent ?>" value="<?= $dataParent ?>" class="checkboxed-6" name="checkboxed_6[]" type="checkbox"  checked/>
                                                                <label for="check-state-6-<?= $dataParent ?>">Mục <?= $dataText ?></label>
                                                            </div>
                                                        <?php endforeach ?>

                                                    </div>
                                                </div>

                                            </div>
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
        var vietnam;
        $.getJSON("<?= get_template_directory_uri() ?>/vn_city.json", function (data) {
            vietnam = data.items;
            // console.log(vietnam);
            var huyen = [];
            for (var i = 0; i < vietnam.length; i++) {
                huyen[i] = vietnam[i].huyen;
            }

            $('#city').on('change', function (e) {

                var option1 = $('option:selected', this).attr('data-key');


                $('#district').html('<option class="select-items"  value="">Chọn</option>');
                var option_huyen1 = huyen[option1];
                for (var m = 0; m < option_huyen1.length; m++) {
                    $('#district').append('<option class="select-items" data-h="' + m + '" value="' + option_huyen1[m].name + '">' + option_huyen1[m].name + '</option>');
                }
            });
            var option = $('option:selected', '#city').attr('data-key');


            var option_huyen = huyen[option];
            $('#district').html('<option value="">Chọn</option>');
            for (var j = 0; j < option_huyen.length; j++) {
                var huyen = option_huyen[j].name;
                $('#district').append('<option class="select-items" data-key="' + j + '" name="huyen"  value="' + option_huyen[j].name + '">' + option_huyen[j].name + '</option>');
            }

        });
    });
</script>
<script>
    $(document).ready(function () {
        $("#adddaily").validate({
            rules: {
                username: {
                    required: true,
                    minlength: 3,
                },
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
                address: {
                    required: true,
                    minlength: 3,
                },
                email: {
                    required: true,
                    email: true,
                },
                loaitaikhoan: {
                    required: true
                },
            },
            messages: {
                username: {
                    required: "Vui lòng nhập tài khoản",
                    minlength: "Tối thiểu 3 ký tự",

                },
                name: {
                    required: "Vui lòng nhập họ tên",
                    minlength: "Tối thiểu 3 ký tự",
                },
                address: {
                    required: "Vui lòng nhập địa chỉ",
                    minlength: "Tối thiểu 3 ký tự",
                },
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
                loaitaikhoan: {
                    required: "Vui lòng chọn loại tài khoản"
                },
            }
        });


        $(".checkboxAll").click(function(){
            let key = $(this).attr("data-id");
            $('.checkboxed-'+key).not(this).prop('checked', this.checked);
        });

    });
</script>
<script>
    $("#loaitaikhoan").on('change', function () {
        let id = $(this).val();
        $(".tab-detail").hide();
        $(".tab-detail-"+id).show();
    });
</script>

