<?php
include __DIR__ . "/../includes/padding.php";

$pagesize = 20;
$s = '';

//-------del-------------
$db = $wpdb->prefix . 'order';
action_list_del($db);


$my_str = "WHERE 1=1";
//
if( isset($_REQUEST['search']) ){
    $keyword = fixqQ($_REQUEST['keyword']);
    $s .= '&search=1';
    if($keyword!=''){
        $my_str .= " and city like '%".$keyword."%' or district like '%".$keyword."%'  ";
        $s .= '&keyword='.urlencode($keyword);
    }

}

//if (isset($_REQUEST['submit'])) {
//    if ($_POST['submit'] == 'delete') {
//        $sql = "DELETE FROM  " . $wpdb->prefix . "order WHERE id = " . $_POST['id'];
//    }
//    if ($_POST['submit'] == 'duyet') {
//        $sql = "UPDATE `" . $db . "` SET `status`=1 WHERE id = " . $_POST['id'];
//    }
//    $wpdb->query($sql);
//    echo "<script type='text/javascript'>
//alert('Cập nhật thành công');
//        window.location=document.location.href;
//        </script>";
//}


$recordcount = count_total_db_air($db, $my_str);

$paged = (int)$_GET['paged'];
if ($paged == 0) $paged = 1;

$beginpaging = beginpaging($pagesize, $recordcount, $paged);


add_admin_css('main.css');
add_admin_js('jquery-2.2.4.min.js');
?>
<style>
    .buttton_duyet {
        color: white;
        font-size: 1.22rem;
        margin-top: 0.5rem;
        border: none;
        background: #00a0d2;
    }

    .buttton_xoa {
        color: white;
        font-size: 1.22rem;
        margin-top: 0.5rem;
        border: none;
        background: #bb3a3a;
    }
</style>
<div class="wrap">
    <h1 style="margin-bottom:15px;">Danh sách <?php echo $mdlconf['title']; ?>
        <!--            <a class="page-title-action" href="--><?php //echo $module_path; ?><!--&sub=add">Thêm mới</a>-->
    </h1>

    <ul class="subsubsub">
        <li class="all"><a class="current" href="<?php echo $module_path; ?>">Tất cả <span
                        class="count">(<?php echo $recordcount; ?>)</span></a></li>
    </ul>

    <!--        <form class="search-box flr" style="text-align: right" method="POST" action="-->
    <?php //echo $module_path; ?><!--">-->
    <!--            <input class="sear_2" value="-->
    <?php //if(isset($keyword)) echo $keyword ;?><!--" type="text" name="keyword" placeholder="Từ khóa">-->
    <!---->
    <!--            <input type="submit" name="search" value="Xem" class="button" />-->
    <!--        </form>-->


    <?php
    $sql = "SELECT * FROM  " . $wpdb->prefix . "order " . $my_str . " ORDER BY id DESC limit " . $beginpaging[0] . ",$pagesize";
    $rs = $wpdb->get_results($sql);
    ?>

    <?php if ($mess != '') { ?>
        <div class="notice notice-warning is-dismissible" id="message">
            <p><?php echo $mess; ?></p>
        </div>
    <?php } ?>

    <form class="" method="POST" action="<?php echo $module_path; ?>">
        <div class="tablenav top">
            <div class="alignleft actions bulkactions">
                <select id="bulk-action-selector-top" name="action">
                    <option value="-1">Tác vụ</option>
                    <!--                        <option value="1">Xóa</option>-->
                </select>

                <input type="submit" value="Áp dụng" class="button action" id="doaction" name="doaction">
            </div>
        </div>

        <table class="wp-list-table widefat fixed striped posts">
            <thead>
            <tr class="headline">
                <td class="manage-column column-cb check-column" id="cb"><input type="checkbox" id="cb-select-all-1">
                </td>
                <th style="width:30px;text-align:center;">STT</th>
                <th>Mã đơn hàng</th>
                <th>Đơn giá</th>
                <th>Họ tên</th>
                <th>Số điện thoại</th>
                <th>Email</th>
                <th>Tình trạng đơn hàng</th>
                <th>Ngày đặt hàng</th>
            </tr>
            </thead>
            <tfoot>
            <tr class="headline">
                <td class="manage-column column-cb check-column" id="cb"><input type="checkbox" id="cb-select-all-1">
                </td>
                <th>STT</th>
                <th>Mã đơn hàng</th>
                <th>Đơn giá</th>
                <th>Họ tên</th>
                <th>Số điện thoại</th>
                <th>Email</th>
                <th>Tình trạng đơn hàng</th>
                <th>Ngày đặt hàng</th>
            </tr>
            </tfoot>
            <?php
            $i = 0;
            foreach ($rs as $row) {
                $i++;
                $rowlink = $module_path . '&sub=edit&id=' . $row->id;
                ?>
                <tr>
                    <th class="check-column" scope="row">
                        <input type="checkbox" value="<?php echo $row->id; ?>" name="post[]"/>
                    </th>
                    <td><?php echo get_list_order($pagesize, $paged, $i); ?></td>
                    <td><a href="<?php echo $rowlink; ?>" target="_blank"><?php echo getmdh($row->id); ?></a></td>
                    <td><?php echo number_format($row->price); ?>đ</td>
                    <td><?php echo $row->name_user; ?></td>
                    <td><?= $row->phone_user ?></td>
                    <td><?= $row->email_user ?></td>

                    <?php
                    if ($row->status == 0) {
                        ?>
                        <td style="color: #0d84e3">Mới tạo</td>
                        <?php
                    }
                    if ($row->status == 1) {
                        ?>
                        <td style="color: #00f964">Đã tiếp nhận</td>
                        <?php
                    }
                    if ($row->status == 2) {
                        ?>
                        <td style="color: magenta">Đã giao</td>
                        <?php
                    }
                    if ($row->status == 3) {
                        ?>
                        <td style="color: red">Hủy</td>
                        <?php
                    }
                    ?>
                    <td><?= date('d/m/Y H:s:i', $row->time_create) ?></td>

                </tr>
            <?php } ?>
        </table>

    </form>

    <?php echo paddingpage($module_short_url, $beginpaging[1], $beginpaging[2], $beginpaging[3], $paged, $pagesize, $recordcount, $s); ?>

</div>

<div class="box-alert"></div>

<?php
add_admin_js('common.js');
?>
