<?php
include __DIR__ . "/../includes/padding.php";
// Include XLSX generator library

$url = get_template_directory_uri();

//-------del-------------
action_list_del("wp_account_users");
$pagesize = 20;
$s = '';
$rs = $wpdb->get_results("SELECT id FROM wp_account_users");

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
            first_name LIKE '%" . $wpdb->esc_like($keyword) . "%' 
            OR last_name LIKE '%" . $wpdb->esc_like($keyword) . "%' 
            OR email LIKE '%" . $wpdb->esc_like($keyword) . "%'
        )";
    }      
}

$recordcount = count_total_db("wp_account_users", $my_str);
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

// Export ex

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
        <input class="sear_2" value="<?php if (isset($keyword))
            echo $keyword; ?>" type="text" name="keyword"
            placeholder="Keywords">

        <input type="submit" name="search" value="Filter" class="button" />
    </form>
    <?php
    $myrows = $wpdb->get_results("
        SELECT * 
        FROM wp_account_users 
        " . $my_str . "
        ORDER BY id DESC 
        LIMIT " . $beginpaging[0] . ",$pagesize
    ");
    ?>
    <div class="wrap" style="margin-top: 60px;">
        <form method="POST">
            <?php wp_nonce_field('export_csv_action', 'export_csv_nonce'); ?>
            <input type="submit" name="export_csv" value="Export CSV" class="button button-primary">
        </form>
<!--        <a href="export.php" class="btn btn-success"></a>-->
    </div>
    <form class="" method="POST" action="<?php echo $module_path; ?>" style="margin-top: 20px;"> 
        <table class="wp-list-table widefat fixed striped posts">
            <thead>
                <tr class="headline">
                    <th style="width:30px;text-align:center;">STT</th>
                    <th>First name</th>
                    <th>Last name</th>
<!--                    <th>Avatar</th>-->
                    <th>Email</th>
                    <th>Provider</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tfoot>
                <tr class="headline">
                    <th style="width:30px;text-align:center;">STT</th>
                    <th>First name</th>
                    <th>Last name</th>
<!--                    <th>Avatar</th>-->
                    <th>Email</th>
                    <th>Provider</th>
                    <th>Action</th>
                </tr>
            </tfoot>

            <?php
            $i = 0;
            $inactiveCustomers = [];

            foreach ($myrows as $customer) {
                if ($customer->status == 0) {
                    $inactiveCustomers[] = $customer;
                    continue;
                }
                $i++;
                $rowlink = $module_path . '&sub=detail&id=' . $customer->id;
                ?>
                <tr>
                    <td><?= $i ?></td>
                    <td><?= $customer->first_name ?></td>
                    <td><?= $customer->last_name ?></td>
                    <td><?= $customer->email ?></td>
                    <td><?= $customer->provider ?></td>
                    <td>
                        <input type="button" value="Deactivate user" data-idUser="<?= $customer->id ?>" class="button btn-remover-user" >
                    </td>
                    <td><a href="<?php echo $rowlink; ?>" target="_blank">View detail</a></td>
                </tr>
            <?php }

            foreach ($inactiveCustomers as $customer) {
                $i++;
                $rowlink = $module_path . '&sub=detail&id=' . $customer->id;
                ?>
                <tr style="background: #ff000099">
                    <td><?= $i ?></td>
                    <td><?= $customer->first_name ?></td>
                    <td><?= $customer->last_name ?></td>
                    <td><?= $customer->email ?></td>
                    <td><?= $customer->provider ?></td>
                    <td></td>
                    <td><a href="<?php echo $rowlink; ?>" target="_blank">View detail</a></td>
                </tr>
            <?php } ?>
        </table>

    </form>

    <?php echo paddingpage($module_short_url, $beginpaging[1], $beginpaging[2], $beginpaging[3], $paged, $pagesize, $recordcount, $s); ?>

</div>

<div class="box-alert"></div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.14.5/sweetalert2.min.css" integrity="sha512-Xxs33QtURTKyRJi+DQ7EKwWzxpDlLSqjC7VYwbdWW9zdhrewgsHoim8DclqjqMlsMeiqgAi51+zuamxdEP2v1Q==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.14.5/sweetalert2.min.js" integrity="sha512-JCDnPKShC1tVU4pNu5mhCEt6KWmHf0XPojB0OILRMkr89Eq9BHeBP+54oUlsmj8R5oWqmJstG1QoY6HkkKeUAg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<?php
add_admin_js('common.js');
?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>

    $('.btn-remover-user').on('click', function() {
        let id_user = $(this).attr('data-idUser');
        Swal.fire({
            title: 'Are you sure it\'s disabled?',
            showDenyButton: true,
            confirmButtonText: `Deactivate`,
            denyButtonText: `No`,
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    type: 'POST',
                    data: {
                        'id_user': id_user,
                        'action': "deleteUser",
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 1) {
                            Swal.fire({
                                icon: 'success',
                                text: response.mess,
                            }).then(() => {
                                location.reload();  // Reload the page after the alert closes
                            });
                        } else {
                            Swal.fire({
                                icon: 'warning',
                                text: response.mess,
                            });
                        }
                    }
                });
            }
        });
    });
</script>