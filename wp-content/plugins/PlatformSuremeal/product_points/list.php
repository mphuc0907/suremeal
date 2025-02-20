<?php
include __DIR__ . "/../includes/padding.php";
$url = get_template_directory_uri();
//-------del-------------
action_list_del("product_points");
$pagesize = 20;
$s = '';
$rs = $wpdb->get_results("SELECT id FROM product_points");

$my_str = "WHERE 1=1";

$keyword = isset($_REQUEST['keyword']) ? $_REQUEST['keyword'] : '';
$status = isset($_REQUEST['status']) ? (int) $_REQUEST['status'] : 0;
$rating = isset($_REQUEST['rating']) ? (int) $_REQUEST['rating'] : 0;

if (isset($_REQUEST['search'])) {
    $keyword = fixqQ($_REQUEST['keyword']);
    $status = (int) $_REQUEST['status'];
    $rating = (int) $_REQUEST['rating'];

    // Mở rộng điều kiện tìm kiếm
    if (!empty($keyword)) {
        $my_str .= " AND (
            name LIKE '%" . $wpdb->esc_like($keyword) . "%' 
        )";
    }
}

$recordcount = count_total_db("product_points", $my_str);
$paged = 0;

if (isset($_GET['paged'])) {
    $paged = (int) $_GET['paged'];
}

if ($paged == 0) {
    $paged = 1;
}
$beginpaging = beginpaging($pagesize, $recordcount, $paged);
add_admin_css('main.css');
add_admin_js('jquery-2.2.4.min.js');

// Loại tài khoản

?>
<style>
    .flr {
        display: flex;
        float: right;
    }

    .d-none {
        display: none;
    }

    .divgif {
        position: fixed;
        width: 100%;
        height: 100%;
        z-index: 1100;
        display: none;
        background: #dedede;
        opacity: 0.5;
        top: 0;
        left: 0;
    }

    .iconloadgif {
        top: 0;
        right: 0;
        left: 0;
        bottom: 0;
        position: absolute;
        margin: auto;
        width: 150px;
        height: 150px;
    }

    .avatar {
        border-radius: 50%;
        width: 3rem;
        height: 3rem;
    }
</style>
<div class="divgif">
    <img class="iconloadgif" src="<?php echo get_template_directory_uri(); ?>/ajax/images/loading2.gif" alt="">
</div>
<div class="wrap">
    <h1 style="margin-bottom:15px;"><?php echo $mdlconf['title']; ?>
        <a class="page-title-action" href="admin.php?page=product_point_manager&amp;sub=add">Add new product</a>
    </h1>

    <ul class="subsubsub">
        <li class="all"><a class="current" href="<?php echo $module_path; ?>">All <span
                    class="count">(<?php echo $recordcount; ?>)</span></a></li>
    </ul>
    <form class="search-box flr" method="POST" action="<?php echo $module_path ?>">
        <input class="sear_2" value="<?php if (isset($keyword))
            echo $keyword; ?>" type="text" name="keyword"
               placeholder="Keywords">

        <input type="submit" name="search" value="Filter" class="button" />
    </form>
    <?php
    $myrows = $wpdb->get_results("
        SELECT * 
        FROM product_points 
        " . $my_str . " 
        ORDER BY id DESC 
        LIMIT " . $beginpaging[0] . ",$pagesize
    ");
//    print_r($myrows);die();
    ?>
    <form class="" method="POST" action="<?php echo $module_path; ?>">

        <table class="wp-list-table widefat fixed striped posts">
            <thead>
            <tr class="headline">
                <th style="width:30px;text-align:center;">#</th>
                <th>Name product</th>
                <th>Image</th>
                <th>Type</th>
                <th>Point</th>
                <th>Purchases</th>
                <th>Creation time</th>
                <th>Expiration time</th>
                <th>Action</th>
            </tr>
            </thead>
            <tfoot>
            <tr class="headline">
                <th style="width:30px;text-align:center;">#</th>
                <th>Name product</th>
                <th>Image</th>
                <th>Type</th>
                <th>Point</th>
                <th>Purchases</th>
                <th>Creation time</th>
                <th>Expiration time</th>
                <th>Action</th>
            </tr>
            </tfoot>

            <?php
            $i = 0;
            foreach ($myrows as $customer) {
                $i++;
                $rowlink = $module_path . '&sub=edit&id=' . $customer->id;
                ?>
                <tr>
                    <td><?= $i ?></td>
                    <td><?= $customer->name ?></td>
                    <td><img class="avatar" src="<?= $customer->image ? $customer->image : $url . '/assets/image/dashboard/avatar-80.svg' ?>" alt=""></td>
                    <td><?php if ($customer->type == 0) : ?>
                Product
                <?php else:?>
                Voucher
                <?php endif;?>
                    </td>
                    <td><?= $customer->point ?></td>
                    <td><?= $customer->purchases ?></td>
                    <td><?= date("M d,Y H:i:s", $customer->created_at); ?></td>
                    <td><?= date("M d,Y H:i:s", $customer->expiration_date); ?></td>
                    <td><a href="<?php echo $rowlink; ?>" target="_blank">View detail</a></td>
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>