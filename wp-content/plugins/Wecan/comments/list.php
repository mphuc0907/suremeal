<?php
include __DIR__ . "/../includes/padding.php";

//-------del-------------
action_list_del("wp_review");
$pagesize = 20;
$s = '';
$rs = $wpdb->get_results("SELECT id FROM wp_review");

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
            OR email LIKE '%" . $wpdb->esc_like($keyword) . "%' 
            OR id_user LIKE '%" . $wpdb->esc_like($keyword) . "%'
            OR comment LIKE '%" . $wpdb->esc_like($keyword) . "%' 
            OR user_login LIKE '%" . $wpdb->esc_like($keyword) . "%'
        )";
    }

    // Lọc theo trạng thái
    if ($status !== 0) {
        $my_str .= " AND status = " . $status;
    }

    // Lọc theo đánh giá
    if ($rating > 0) {
        $my_str .= " AND rating = " . $rating;
    }
}

$recordcount = count_total_db("wp_review", $my_str);
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
    <h1 style="margin-bottom:15px;"><?php echo $mdlconf['title']; ?>
    </h1>

    <ul class="subsubsub">
        <li class="all"><a class="current" href="<?php echo $module_path; ?>">All <span
                    class="count">(<?php echo $recordcount; ?>)</span></a></li>
    </ul>
    <form class="search-box flr" method="GET" action="<?php echo admin_url('admin.php?page=comment_manager'); ?>">
        <input type="hidden" name="page" value="comment_manager">
        <span style="line-height: 24px; margin-right: 10px">Status: </span>
        <select name="status" id="status">
            <option value="" selected>All</option>
            <option value="0">Hide</option>
            <option value="1">Display</option>
        </select>

        <span style="line-height: 24px; margin-right: 10px">Rating: </span>
        <select name="rating">
            <option value="">All</option>
            <?php for ($i = 1; $i <= 5; $i++): ?>
                <option value="<?php echo $i; ?>" <?php echo ($rating == $i) ? 'selected' : ''; ?>><?php echo $i; ?> star
                </option>
            <?php endfor; ?>
        </select>

        <input class="sear_2" value="<?php if (isset($keyword))
            echo $keyword; ?>" type="text" name="keyword"
            placeholder="Keywords">

        <input type="submit" name="search" value="Filter" class="button" />
    </form>
    <?php
    $myrows = $wpdb->get_results("SELECT r.*, u.user_login 
        FROM wp_review r 
        LEFT JOIN wp_users u ON r.id_user = u.ID 
        " . $my_str . " 
        ORDER BY r.id DESC 
        LIMIT " . $beginpaging[0] . ",$pagesize");
    ?>
    <form class="" method="POST" action="<?php echo $module_path; ?>">

        <table class="wp-list-table widefat fixed striped posts">
            <thead>
                <tr class="headline">
                    <th style="width:30px;text-align:center;">STT</th>
                    <th>Account</th>
                    <th>Name</th>
                    <th>Product</th>
                    <th>Content</th>
                    <th>Likes</th>
                    <th>Rating</th>
                    <th>Status</th>

                </tr>
            </thead>
            <tfoot>
                <tr class="headline">
                    <th style="width:30px;text-align:center;">STT</th>
                    <th>Account</th>
                    <th>Name</th>
                    <th>Product</th>
                    <th>Content</th>
                    <th>Likes</th>
                    <th>Rating</th>
                    <th>Status</th>

                </tr>
            </tfoot>

            <?php
            $i = 0;
            foreach ($myrows as $comment) {
                $i++;
                ?>
                <tr>
                    <td><?= $i ?></td>
                    <td><?= $comment->id_user == 0 ? 'Customer' : $comment->user_login ?></td>
                    <td><?= $comment->name ?></td>
                    <td><?= get_the_title($comment->id_product) ?></td>
                    <td><?= $comment->comment ?></td>
                    <td><?= $comment->likes ?></td>
                    <td><?= $comment->rating ?></td>
                    <td>
                        <select class="statusComment" data-order-id="<?php echo $comment->id; ?>">
                            <option value="0" <?php selected($comment->status, 0); ?>>Hide</option>
                            <option value="1" <?php selected($comment->status, 1); ?>>Display</option>
                        </select>
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
jQuery(document).ready(function($) {
    $('.statusComment').on('change', function() {
        var commentId = $(this).data('order-id');
        var newStatus = $(this).val();

        // Hiển thị loading
        $('.divgif').show();

        $.ajax({
            url: ajaxurl, // Wordpress AJAX URL
            type: 'POST',
            data: {
                action: 'update_comment_status', // Tên action để xử lý trong function
                comment_id: commentId,
                status: newStatus
            },
            success: function(response) {
                // Ẩn loading
                $('.divgif').hide();

                // Kiểm tra kết quả từ server
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Update successful!',
                        showConfirmButton: false,
                        timer: 1500
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Update failed!',
                        text: response.data
                    });
                }
            },
            error: function() {
                // Ẩn loading
                $('.divgif').hide();

                Swal.fire({
                    icon: 'error',
                    title: 'An error occurred!',
                    text: 'Unable to update the status'
                });
            }
        });
    });
});
</script>