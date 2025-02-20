<?php
include __DIR__ . "/../includes/padding.php";
$url = get_template_directory_uri();
//-------del-------------
action_list_del("wp_account_users");
$pagesize = 20;
$s = '';
$rs = $wpdb->get_results("SELECT id FROM wp_account_users WHERE type = 1");

$my_str = "WHERE 1=1";

$keyword = isset($_REQUEST['keyword']) ? $_REQUEST['keyword'] : '';
$status = isset($_REQUEST['status']) ? (int) $_REQUEST['status'] : -1; // Changed default to -1 for "All"
$date_from = isset($_REQUEST['date_from']) ? $_REQUEST['date_from'] : '';
$date_to = isset($_REQUEST['date_to']) ? $_REQUEST['date_to'] : '';

if (isset($_REQUEST['search'])) {
    $keyword = fixqQ($_REQUEST['keyword']);
    $status = (int) $_REQUEST['status'];
    $date_from = $_REQUEST['date_from'];
    $date_to = $_REQUEST['date_to'];

    // Keyword search
    if (!empty($keyword)) {
        $my_str .= " AND (
            first_name LIKE '%" . $wpdb->esc_like($keyword) . "%' 
            OR last_name LIKE '%" . $wpdb->esc_like($keyword) . "%' 
            OR email LIKE '%" . $wpdb->esc_like($keyword) . "%'
        )";
    }

    // Status filter
    if ($status >= 0) {
        $my_str .= " AND status = " . $status;
    }

    // Date range filter
    if (!empty($date_from) && !empty($date_to)) {
        $my_str .= " AND DATE(created_at) BETWEEN '" . date('Y-m-d', strtotime($date_from)) . "' AND '" . date('Y-m-d', strtotime($date_to)) . "'";
    } else if (!empty($date_from)) {
        $my_str .= " AND DATE(created_at) >= '" . date('Y-m-d', strtotime($date_from)) . "'";
    } else if (!empty($date_to)) {
        $my_str .= " AND DATE(created_at) <= '" . date('Y-m-d', strtotime($date_to)) . "'";
    }
}

$count_query = "SELECT COUNT(*) FROM wp_account_users WHERE type = 1";
$recordcount = $wpdb->get_var($wpdb->prepare($count_query, $where_params));

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
    </h1>

    <ul class="subsubsub">
        <li class="all"><a class="current" href="<?php echo $module_path; ?>">All <span
                    class="count">(<?php echo $recordcount; ?>)</span></a></li>
    </ul>
    <form class="search-box flr" method="POST" action="<?php echo $module_path ?>">
        <select name="status">
            <option value="-1">All Status</option>
            <option value="0" <?php echo $status === 0 ? 'selected' : ''; ?>>Unactivated</option>
            <option value="1" <?php echo $status === 1 ? 'selected' : ''; ?>>Activated</option>
        </select>

        <input type="date" name="date_from" value="<?php echo $date_from; ?>" placeholder="From Date">
        <input type="date" name="date_to" value="<?php echo $date_to; ?>" placeholder="To Date">

        <input class="sear_2" value="<?php echo isset($keyword) ? htmlspecialchars($keyword) : ''; ?>" type="text" name="keyword" placeholder="Keywords">

        <input type="submit" name="search" value="Filter" class="button" />
    </form>
    <?php
    $myrows = $wpdb->get_results("
    SELECT * 
    FROM wp_account_users 
    
    " . $my_str . " 
    AND type = 1 
    ORDER BY id DESC 
    LIMIT " . $beginpaging[0] . ", $pagesize
");


    ?>
    
    <form class="" method="POST" action="<?php echo $module_path; ?>">

        <table class="wp-list-table widefat fixed striped posts">
            <thead>
                <tr class="headline">
                    <th style="width:30px;text-align:center;">STT</th>
                    <th>Name</th>
<!--                    <th>Avatar</th>-->
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Created at</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tfoot>
                <tr class="headline">
                    <th style="width:30px;text-align:center;">STT</th>
                    <th>Name</th>
<!--                    <th>Avatar</th>-->
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Created at</th>
                    <th>Action</th>
                </tr>
            </tfoot>

            <?php
            $i = 0;
            foreach ($myrows as $customer) {
                $i++;
                $rowlink = $module_path . '&sub=detail&id=' . $customer->id;
                ?>
                <tr>
                    <td><?= $i ?></td>
                    <td><?= $customer->first_name ?> <?= $customer->last_name ?></td>
<!--                    <td><img class="avatar" src="--><?//= $customer->avatar ? $customer->avatar : $url . '/assets/image/dashboard/avatar-80.svg' ?><!--" alt=""></td>-->
                    <td><?= $customer->phone_number ?></td>
                    <td><?= $customer->email ?></td>
                    <td>
                        <select class="statusUser" data-user-id="<?= $customer->id ?>">
                           <option value="0" <?= $customer->status == 0 ? 'selected' : '' ?>>Unactivated</option>
                           <option value="1" <?= $customer->status == 1 ? 'selected' : '' ?>>Activated</option>
                        </select>
                    </td>
                    <td><?= $customer->created_at ?></td>
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

<script>
    // Thay doi trang thai 
document.querySelectorAll('.statusUser').forEach(element => {
    element.addEventListener('change', function () {
        let newStatus = this.value;
        let UserId = this.getAttribute('data-user-id');

        let formData = new FormData();
        formData.append('status', newStatus);
        formData.append('UserId', UserId);
        formData.append('action', 'changeUserStatus');

        document.querySelector('.divgif').style.display = 'block';

        fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            document.querySelector('.divgif').style.display = 'none';
            let rs = JSON.parse(data);
            if (rs.status === 'success_code') {
                Swal.fire({
                    icon: 'success',
                    text: rs.mess,
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    text: rs.mess,
                });
            }
        })
        .catch(error => {
            document.querySelector('.divgif').style.display = 'none';
            Swal.fire({
                icon: 'error',
                text: 'Có lỗi xảy ra!',
            });
            console.error('Error:', error);
        });
    });
});

</script>