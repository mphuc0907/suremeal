<?php
include __DIR__ . "/../includes/padding.php";

//-------del-------------
action_list_del("wp_dealer");
$pagesize = 20;
$s = '';
$rs = $wpdb->get_results("SELECT id FROM wp_dealer");

$my_str = "WHERE 1=1";

$recordcount = count_total_db("wp_dealer", $my_str);
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
</style>
<div class="divgif">
    <img class="iconloadgif" src="<?php echo get_template_directory_uri(); ?>/ajax/images/loading2.gif" alt="">
</div>
<div class="wrap">
    <h1 style="margin-bottom:15px;">List <?php echo $mdlconf['title']; ?>
        <a class="page-title-action" href="admin.php?page=dealer_manager&amp;sub=add">Add new dealer</a>
    </h1>

    <ul class="subsubsub">
        <li class="all"><a class="current" href="<?php echo $module_path; ?>">All <span
                    class="count">(<?php echo $recordcount; ?>)</span></a></li>
    </ul>
    
    <?php
    $myrows = $wpdb->get_results("SELECT *
        FROM wp_dealer 
        ORDER BY id DESC 
        LIMIT " . $beginpaging[0] . ",$pagesize");
    ?>
    <form class="" method="POST" action="<?php echo $module_path; ?>">

        <table class="wp-list-table widefat fixed striped posts">
            <thead>
                <tr class="headline">
                    <th style="width:30px;text-align:center;">STT</th>
                    <th>Dealer name</th>
                    <th>Phone number</th>
                    <th>Open at</th>
                    <th>Close at</th>
                    <th>State</th>
                    <th>City</th>
                    <th>Address</th>
                    <th rowspan="2">Action</th>
                </tr>
            </thead>
            <tfoot>
                <tr class="headline">
                    <th style="width:30px;text-align:center;">STT</th>
                    <th>Dealer name</th>
                    <th>Phone number</th>
                    <th>Open at</th>
                    <th>Close at</th>
                    <th>State</th>
                    <th>City</th>
                    <th>Address</th>
                    <th rowspan="2">Action</th>
                </tr>
            </tfoot>

            <?php
            foreach ($myrows as $key => $dealer) {
                $stt = $key + 1;
                $editlink = $module_path . '&sub=edit&id=' . $dealer->id;
                ?>
                <tr>
                    <td><?= $tt ?></td>
                    <td><?= $dealer->dealer_name ?></td>
                    <td><?= $dealer->phone ?></td>
                    <td><?= $dealer->open_at ?></td>
                    <td><?= $dealer->close_at ?></td>
                    <td><?= $dealer->state ?></td>
                    <td><?= $dealer->city ?></td>
                    <td><?= $dealer->address ?></td>
                    <td>
                        <a href="<?= $editlink ?>" class="detail-action" data-id="<?= $dealer->id ?>">Edit</a>
                        | 
                        <a href="javascript:void(0)" onclick="deleteDealer(<?= $dealer->id ?>)" class="delete-action" style="color: red;">Delete</a>
                    </td>
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
function deleteDealer(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading animation
            document.querySelector('.divgif').style.display = 'block';
            
            // Send delete request
            jQuery.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'delete_dealer',
                    dealer_id: id,
                    security: ajax_object.nonce
                },
                success: function(response) {
                    document.querySelector('.divgif').style.display = 'none';
                    
                    if (response.success) {
                        Swal.fire(
                            'Deleted!',
                            'Dealer has been deleted.',
                            'success'
                        ).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire(
                            'Error!',
                            'Something went wrong.',
                            'error'
                        );
                    }
                },
                error: function() {
                    document.querySelector('.divgif').style.display = 'none';
                    Swal.fire(
                        'Error!',
                        'Something went wrong.',
                        'error'
                    );
                }
            });
        }
    });
}
</script>