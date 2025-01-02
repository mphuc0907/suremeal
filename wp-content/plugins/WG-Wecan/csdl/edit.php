<?php
include __DIR__ . "/../includes/padding.php";
global $wpdb;

$id = (int)($_GET['id']);
if (isset($_REQUEST['edit_action']) && (int)$_REQUEST['edit_action'] == 1) {

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

    // Input
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
    $sqlCheck = "SELECT * FROM useragency where loaitaikhoan=".$loaitaikhoan." and ".$dkKey."= '" . $dkValue . "' and id !=".$id;
    $userCheck = $wpdb->get_results($sqlCheck);
    // check validate
    $checkdata = $wpdb->get_row(
        "SELECT * FROM useragency where id = '" . $id . "'");
    $phonenumberdl = $wpdb->get_row(
        "SELECT * FROM useragency where phonenumber = '" . $phonenumber . "' AND phonenumber NOT IN('" . $checkdata->phonenumber . "')");
    $emaildl = $wpdb->get_row(
        "SELECT * FROM useragency where email = '" . $email . "'  AND email NOT IN('" . $checkdata->email . "')");

    if(!empty($userCheck)){
        show_result(0, 'Loại tài khoản đã tồn tại');
    }else{
        if ($phonenumberdl != null) {
            show_result(0, 'Số điện thoại đã tồn tại');
        } elseif ($emaildl != null) {
            show_result(0, 'Email đã tồn tại');
        } else {
            if(!empty($username) && !empty($name) && !empty($address) && !empty($phonenumber) && !empty($rolesJson)) {
                $sql = "update useragency set name= '$name',address = '$address',phonenumber = '$phonenumber',co_quan= '$co_quan',text_co_quan= '$text_co_quan',dia_phuong= '$dia_phuong',text_dia_phuong= '$text_dia_phuong',dovi_khac= '$dovi_khac',text_donvi_khac= '$text_donvi_khac',loaitaikhoan= '$loaitaikhoan', roles_json='$rolesJson' where id=" . $id;
                $resp = $wpdb->query($sql);
                show_result(1);
            }
        }
    }
}

$row = $wpdb->get_row("SELECT * FROM   useragency  where id=" . $id);

$roles_json = json_decode($row->roles_json);
$checkboxed_1 = $roles_json->checkboxed_1;
$checkboxed_2 = $roles_json->checkboxed_2;
$checkboxed_3 = $roles_json->checkboxed_3;
$checkboxed_4 = $roles_json->checkboxed_4;
$checkboxed_5 = $roles_json->checkboxed_5;
$checkboxed_6 = $roles_json->checkboxed_6;
//pr($roles_json);

add_admin_css('main.css');
add_admin_js('jquery-2.2.4.min.js');
//$city = file_get_contents(plugin_dir_path(__FILE__) . 'vn_city.json');
//$json = json_decode($city, true);

$loaitk = arrayLoaiTK();
$term_tinh = get_terms('tinh',
    array(
        'orderby' => 'name',
        'order' => 'DESC',
        'hide_empty' => false,
    )
);
$term_coquan = get_terms('coquan',
    array(
        'orderby' => 'name',
        'order' => 'DESC',
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
        <?php show_admin_box_edit_title($mdlconf, $module_path); ?>
    </h1>
    <form id="adddaily" method="post" action="<?php echo $module_path . '&sub=edit&edit_action=1&id=' . $id; ?>"
          name="post">
        <div id="poststuff">
            <input type="hidden" value="<?php echo $id; ?>" name="id"/>
            <div class="metabox-holder columns-2" id="post-body">
                <!---left-->
                <div id="post-body-content" class="pos1">
                    <div class="postbox">
                        <h2 class="hndle ui-sortable-handle api-title">Thông tin</h2>
                        <div class="inside">
                            <!--                            <input type="hidden" value="-->
                            <?php //echo $id; ?><!--" name="id"/>-->
                            <table class="form-table ft_metabox leftform">
                                <tr>
                                    <td style="width:150px;">
                                        Tài khoản <span style="color: red">*</span>
                                    </td>
                                    <td>
                                        <input type="text" readonly name="username" size="50" value="<?= $row->username ?>">
                                    </td>
                                </tr>
                                <?php
                                show_list_row('Họ và Tên', array('name', $row->name, 'text'));
                                show_list_row('số điện thoại', array('phonenumber', $row->phonenumber, 'text'));
                                show_list_row('Địa chỉ', array('address', $row->address, 'text'));
                                ?>
                                <tr>
                                    <td style="width:150px;">
                                        Email <span style="color: red">*</span>
                                    </td>
                                    <td>
                                        <input type="text" readonly name="email" size="50" value="<?= $row->email ?>">
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label for="loaitaikhoan">Loại tài khoản <span
                                                    style="color: red">*</span></label>
                                    </td>
                                    <td>
                                        <select name="loaitaikhoan" id="loaitaikhoan" class="form-control">
                                            <option value="">Chọn loại tài khoản</option>
                                            <?php foreach ($loaitk as $value): ?>
                                                <option class="select-items" <?= ($value['key'] == $row->loaitaikhoan) ? 'selected' : '' ?>
                                                        value="<?= $value['key'] ?>"><?= $value['value'] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>

                                    </td>
                                    <td>
                                        <div class="tab-detail tab-detail-1 <?= $row->loaitaikhoan == 1 ? '' : 'd-none' ?>">
                                            <div class="item-detail-1">
                                                <select name="bonganh" id="bonganh" class="form-control">
                                                    <?php foreach ($term_coquan as $key => $tc): ?>
                                                        <option class="select-items" <?= ($tc->slug == $row->co_quan) ? 'selected' : '' ?>
                                                                value="<?= $tc->slug ?>"><?= $tc->name ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="tab-detail tab-detail-2 <?= $row->loaitaikhoan == 2 ? '' : 'd-none' ?>">
                                            <div class="item-detail-2">
                                                <select name="diaphuong" id="diaphuong" class="form-control">
                                                    <?php foreach ($term_tinh as $key => $tc): ?>
                                                        <option class="select-items" <?= ($tc->slug == $row->dia_phuong) ? 'selected' : '' ?>
                                                                value="<?= $tc->slug ?>"><?= $tc->name ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="tab-detail tab-detail-3 <?= $row->loaitaikhoan == 3 ? '' : 'd-none' ?>">
                                            <div class="item-detail-3">
                                                <input type="text" name="donvikhac" id="donvikhac" size="50"
                                                       value="<?= $row->text_donvi_khac ?>"/>
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
                                                    <input id="check-all-1" class="checkboxAll" data-id="1" type="checkbox"  />
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
                                                                <input id="check-state-1-<?= $dataParent ?>" value="<?= $dataParent ?>" class="checkboxed-1" name="checkboxed_1[]" type="checkbox" <?= in_array($dataParent, $checkboxed_1)?'checked':'' ?> />
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
                                                                <input id="check-state-1-<?= $dataParent ?>" value="<?= $dataParent ?>" class="checkboxed-1" name="checkboxed_1[]" type="checkbox"  <?= in_array($dataParent, $checkboxed_1)?'checked':'' ?>/>
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
                                                                <input id="check-state-1-<?= $dataParent ?>" value="<?= $dataParent ?>" class="checkboxed-1" name="checkboxed_1[]" type="checkbox"  <?= in_array($dataParent, $checkboxed_1)?'checked':'' ?>/>
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
                                                                <input id="check-state-1-<?= $dataParent ?>" value="<?= $dataParent ?>" class="checkboxed-1" name="checkboxed_1[]" type="checkbox"  <?= in_array($dataParent, $checkboxed_1)?'checked':'' ?>/>
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
                                                                <input id="check-state-1-<?= $dataParent ?>" value="<?= $dataParent ?>" class="checkboxed-1" name="checkboxed_1[]" type="checkbox"  <?= in_array($dataParent, $checkboxed_1)?'checked':'' ?>/>
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
                                                                <input id="check-state-1-<?= $dataParent ?>" value="<?= $dataParent ?>" class="checkboxed-1" name="checkboxed_1[]" type="checkbox"  <?= in_array($dataParent, $checkboxed_1)?'checked':'' ?>/>
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
                                                    <input id="check-all-2" class="checkboxAll" data-id="2" name="check-state-checked" type="checkbox" />
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
                                                                <input id="check-state-2-<?= $dataParent ?>" value="<?= $dataParent ?>" class="checkboxed-2" name="checkboxed_2[]" type="checkbox"  <?= in_array($dataParent, $checkboxed_2)?'checked':'' ?> />
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
                                                                <input id="check-state-2-<?= $dataParent ?>" value="<?= $dataParent ?>" class="checkboxed-2" name="checkboxed_2[]" type="checkbox"  <?= in_array($dataParent, $checkboxed_2)?'checked':'' ?>/>
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
                                                                <input id="check-state-2-<?= $dataParent ?>" value="<?= $dataParent ?>" class="checkboxed-2" name="checkboxed_2[]" type="checkbox"  <?= in_array($dataParent, $checkboxed_2)?'checked':'' ?>/>
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
                                                                <input id="check-state-2-<?= $dataParent ?>" value="<?= $dataParent ?>" class="checkboxed-2" name="checkboxed_2[]" type="checkbox"  <?= in_array($dataParent, $checkboxed_2)?'checked':'' ?>/>
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
                                                                <input id="check-state-2-<?= $dataParent ?>" value="<?= $dataParent ?>" class="checkboxed-2" name="checkboxed_2[]" type="checkbox"  <?= in_array($dataParent, $checkboxed_2)?'checked':'' ?>/>
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
                                                                <input id="check-state-2-<?= $dataParent ?>" value="<?= $dataParent ?>" class="checkboxed-2" name="checkboxed_2[]" type="checkbox"  <?= in_array($dataParent, $checkboxed_2)?'checked':'' ?>/>
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
                                                                <input id="check-state-2-<?= $dataParent ?>" value="<?= $dataParent ?>" class="checkboxed-2" name="checkboxed_2[]" type="checkbox"  <?= in_array($dataParent, $checkboxed_2)?'checked':'' ?>/>
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
                                                                <input id="check-state-2-<?= $dataParent ?>" value="<?= $dataParent ?>" class="checkboxed-2" name="checkboxed_2[]" type="checkbox"  <?= in_array($dataParent, $checkboxed_2)?'checked':'' ?>/>
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
                                                                <input id="check-state-2-<?= $dataParent ?>" value="<?= $dataParent ?>" class="checkboxed-2" name="checkboxed_2[]" type="checkbox"  <?= in_array($dataParent, $checkboxed_2)?'checked':'' ?>/>
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
                                                    <input id="check-all-3" class="checkboxAll" data-id="3" type="checkbox" />
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
                                                                <input id="check-state-3-<?= $dataParent ?>" value="<?= $dataParent ?>" class="checkboxed-3" name="checkboxed_3[]" type="checkbox"  <?= in_array($dataParent, $checkboxed_3)?'checked':'' ?>/>
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
                                                                <input id="check-state-3-<?= $dataParent ?>" value="<?= $dataParent ?>" class="checkboxed-3" name="checkboxed_3[]" type="checkbox"  <?= in_array($dataParent, $checkboxed_3)?'checked':'' ?>/>
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
                                                    <input id="check-all-4" class="checkboxAll" data-id="4" type="checkbox" />
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
                                                                <input id="check-state-4-<?= $dataParent ?>" value="<?= $dataParent ?>" class="checkboxed-4" name="checkboxed_4[]" type="checkbox"  <?= in_array($dataParent, $checkboxed_4)?'checked':'' ?>/>
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
                                                    <input id="check-all-5" class="checkboxAll" data-id="5" type="checkbox" />
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
                                                                <input id="check-state-5-<?= $dataParent ?>" value="<?= $dataParent ?>" class="checkboxed-5" name="checkboxed_5[]" type="checkbox"  <?= in_array($dataParent, $checkboxed_5)?'checked':'' ?>/>
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
                                                                <input id="check-state-5-<?= $dataParent ?>" value="<?= $dataParent ?>" class="checkboxed-5" name="checkboxed_5[]" type="checkbox"  <?= in_array($dataParent, $checkboxed_5)?'checked':'' ?>/>
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
                                                    <input id="check-all-6" class="checkboxAll" data-id="6" type="checkbox" />
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
                                                                <input id="check-state-6-<?= $dataParent ?>" value="<?= $dataParent ?>" class="checkboxed-6" name="checkboxed_6[]" type="checkbox"  <?= in_array($dataParent, $checkboxed_6)?'checked':'' ?>/>
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

                    <?php
                    if (1 == 2) {
                        $wp_editor_setting = array('wpautop' => false);
                        $content = $row->content;
                        $idname = 'content';
                        wp_editor($content, $idname, $wp_editor_setting);
                    }
                    ?>
                </div>


                <!--right-->
                <div class="postbox-container" id="postbox-container-1">
                    <div class="meta-box-sortables ui-sortable" id="side-sortables">

                        <?php
                        show_admin_btn_save($row->active);

                        //---anh-----
                        //show_admin_featured_image($row->image);
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
        $('#district').html('<option class="select-items"  value="">Chọn</option>');
        var tp = "<?= $row->province ?>";
        var district = "<?= $row->district ?>";
        $.getJSON("<?= get_template_directory_uri() ?>/vn_city.json", function (data) {
            vietnam = data.items;
            vietnam.forEach(function (e) {
                var city = e.name;
                var ct = city.replace('Thành phố ', '');
                var ct1 = ct.replace('Tỉnh ', '');

                if (ct1 == tp) {
                    var huyen = [];
                    huyen = e.huyen;
                    for (var i = 0; i < huyen.length; i++) {
                        $('#district').append('<option class="select-items" data-h="' + i + '" value="' + huyen[i].name + '">' + huyen[i].name + '</option>');
                        if (district != null) {
                            $('#district').find('option').each(function (i, e) {
                                if ($(e).val() == district) {
                                    $('#district').prop('selectedIndex', i);
                                }
                            });
                        }
                    }
                }
            })
        });
        $('#city').on('change', function () {
            var value = $('#city option:selected').val();
            $('#district').html('<option class="select-items"  value="">Chọn</option>');
            $.getJSON("<?= get_template_directory_uri() ?>/vn_city.json", function (data) {
                vietnam = data.items;
                vietnam.forEach(function (e) {
                    var city = e.name;
                    var ct = city.replace('Thành phố ', '');
                    var ct1 = ct.replace('Tỉnh ', '');

                    if (ct1 == value) {
                        var huyen = [];
                        huyen = e.huyen;
                        for (var i = 0; i < huyen.length; i++) {
                            $('#district').append('<option class="select-items" data-h="' + i + '" value="' + huyen[i].name + '">' + huyen[i].name + '</option>');
                        }
                    }
                })
            });

        })
    });
</script>
<script>
    $(document).ready(function () {
        $("#adddaily").validate({
            rules: {
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
                loaitaikhoan: {
                    required: true
                },
            },
            messages: {
                name: {
                    required: "Vui lòng nhập họ tên",
                    minlength: "Tối thiểu 3 ký tự",
                },
                address: {
                    required: "Vui lòng nhập địa chỉ",
                    minlength: "Tối thiểu 3 ký tự",
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
    });
</script>
<script>
    $(document).ready(function () {
        $('#gena').on('click', function () {
            var value = random(8);
            $('#password').val(value);
            $('#pass_gen').val(value);
            $('#pass_gen').css('display', 'inline-block');
        });
    });

    function random(length) {
        var result = '';
        var characters = '0123456789abcdefghijklmnopqrstuvwxyz';
        var charactersLength = characters.length;
        for (var i = 0; i < length; i++) {
            result += characters.charAt(Math.floor(Math.random() * charactersLength));
        }
        return result;
    }
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
