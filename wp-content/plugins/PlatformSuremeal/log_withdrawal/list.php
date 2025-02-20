<?php
include __DIR__ . "/../includes/padding.php";

//pr(123);
global $wpdb;
action_list_del("log_withdrawal");
$pagesize = 20;
$s = '';
$my_str = "WHERE 1=1";

date_default_timezone_set("America/Chicago");
$image_no = get_field('image_no_image', 'option');
$recordcount = count_total_db("log_withdrawal", $my_str);
$paged = isset($_GET['paged']) ? (int)$_GET['paged'] : 1;

$beginpaging = beginpaging($pagesize, $recordcount, $paged);
add_admin_css('main.css');
add_admin_js('jquery-2.2.4.min.js');
$city = file_get_contents(plugin_dir_path(__FILE__) . 'vn_city.json');
$json = json_decode($city, true);
$user_data = $wpdb->get_results("SELECT * FROM wp_account_users ORDER BY id DESC");
// Loại tài khoản

?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<!--<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">-->
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet">

<style>
    .flr{
        display: flex;
        float: right;
    }
    .d-none{
        display: none;
    }
    .select2-container--default .select2-selection--single {
        height: 40px;
    }
    .select2-container {
        width: 100% !important;
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
    .date-time {
        display: flex;
        flex-wrap: wrap;
    }

    .time-space {
        padding-left: 10px;
        padding-right: 10px;
        font-size: 20px;
    }
    .select2-results__option img {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        margin-right: 10px;
    }
    .select2-selection__rendered img {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        margin-right: 10px;
    }
</style>
<div class="divgif">
    <img class="iconloadgif" src="<?php echo get_template_directory_uri(); ?>/ajax/images/loading2.gif" alt="">
</div>
<div class="wrap">
    <h1 style="margin-bottom:15px;">List <?php echo $mdlconf['title']; ?>
    </h1>
    <input type="button" value="Add Transaction" class="button" data-bs-toggle="modal" data-bs-target="#userModal">
    <ul class="subsubsub">
        <li class="all"><a class="current" href="<?php echo $module_path; ?>">All <span
                    class="count">(<?php echo $recordcount; ?>)</span></a></li>
    </ul>

    <?php
    $myrows = $wpdb->get_results("SELECT * FROM log_withdrawal ". $my_str ." ORDER BY id DESC LIMIT  " . $beginpaging[0] . ",$pagesize");
    ?>
    <!--    --><?php // if ( $mess != '' ) { ?>
    <!--        <div class="notice notice-warning is-dismissible" id="message">-->
    <!--            <p>--><?php //echo $mess; ?><!--</p>-->
    <!--        </div>-->
    <!--    --><?php // } ?>
    <!-- Modal -->
    <div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="userModalLabel">Add Transaction</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
<!--                        <span aria-hidden="true">&times;</span>-->
                    </button>
                </div>
                <div class="modal-body">
                    <form id="form-add-transaction" action="">
                        <div class="form-group">
                            <label for="exampleInputEmail1">Select user</label>
                            <div>
                                <select class="form-control" name="id_user" id="id_user">
                                    <option>----------</option>
                                    <?php foreach ($user_data as $key => $value):?>
                                        <option value="<?= $value->id ?>" data-image="<?= $value->avatar ?>"><?= $value->first_name . " " . $value->last_name ?> </option>
                                    <?php endforeach;?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="exampleInputPassword1">Withdrawal amount</label>
                            <input type="number" name="withdrawal_amount"  readonly class="form-control" id="withdrawal_amount" placeholder="Withdrawal amount">
                        </div>

                        <div class="form-group">
                            <label for="exampleFormControlFile1">Transaction image</label>
                            <input type="file" name="transaction_img" class="form-control-file" id="transaction_img">
                        </div>
                        <div class="form-group">
                            <label for="exampleInputPassword1">Transaction notes</label>
                            <textarea name="action_user" class="form-control" id="" cols="30" rows="10" placeholder="Transaction notes"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" aria-label="Close">Close</button>

                </div>
            </div>
        </div>
    </div>
    <form class="" method="POST" action="<?php echo $module_path; ?>">

        <table class="wp-list-table widefat fixed striped posts">
            <thead>
            <tr class="headline">
                <th>No.</th>
                <th>Admin user</th>
                <th>User name</th>
                <th>Withdrawal amount</th>
                <th>Transaction notes</th>
                <th>Transaction images</th>
<!--                <th>Status</th>-->
                <th>Creation date</th>
            </tr>
            </thead>
            <tfoot>
            <tr class="headline">
                <th >No.</th>
                <th>Admin user</th>
                <th>User name</th>
                <th>Withdrawal amount</th>
                <th>Transaction notes</th>
                <th>Transaction images</th>
<!--                <th>Status</th>-->
                <th>Creation date</th>
            </tr>
            </tfoot>


            <?php
            $i = 0;
            foreach ($myrows as $order) {
                $id_u = $order->id_user;
                $i++;

                $rowlinkUser = 'admin.php?page=daily&sub=edit&id=' . $id_u;
                $user_data_only = $wpdb->get_row(
                    $wpdb->prepare("SELECT * FROM wp_account_users WHERE id = %d", $id_u)
                );
                $rowlink = 'admin.php?page=customer_dealers_manager&sub=detail&id=' . $user_data_only->id;
//                print_r($user_data_only);die();
                ?>
                <tr>
                    <td><?= $i ?></td>
                    <td><?= $order->name_admin ?></td>
                    <td><a href="<?php echo $rowlink; ?>" target="_blank"><?= $user_data_only->first_name . " " . $user_data_only->last_name ?></a></td>
                    <td><?= formatBalance($order->withdrawal_amount) ?></td>
                    <td><?= $order->action_user ?></td>
                    <td>
                        <img src="<?= $order->transaction_img ?>" alt="" style="    width: 100%;
    max-height: 300px;
    object-fit: contain;">
                    </td>
                    <td><?= $order->created_at ?></td>
<!--                    <td>--><?//= date('H:i m/d/Y', $order->updated_at) ?><!--</td></tr>-->
            <?php } ?>
        </table>

    </form>

    <?php echo paddingpage($module_short_url, $beginpaging[1], $beginpaging[2], $beginpaging[3], $paged, $pagesize, $recordcount, $s); ?>

</div>

<div class="box-alert"></div>
<?php
add_admin_js('common.js');
?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.14.5/sweetalert2.min.css" integrity="sha512-Xxs33QtURTKyRJi+DQ7EKwWzxpDlLSqjC7VYwbdWW9zdhrewgsHoim8DclqjqMlsMeiqgAi51+zuamxdEP2v1Q==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.14.5/sweetalert2.min.js" integrity="sha512-JCDnPKShC1tVU4pNu5mhCEt6KWmHf0XPojB0OILRMkr89Eq9BHeBP+54oUlsmj8R5oWqmJstG1QoY6HkkKeUAg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
    $(document).ready(function() {
        function formatOption(option) {
            if (!option.id) {
                return option.text;
            }
            var image_no = '<?= $image_no ?>';
            var imageUrl = $(option.element).data('image');
            if (imageUrl) {
                var $option = $(
                    '<span>' + option.text + '</span>'
                );
            }else {
                var $option = $(
                    '<span>' + option.text + '</span>'
                );
            }

            return $option;
        }

        $("#id_user").select2({
            templateResult: formatOption,
            templateSelection: formatOption,
            allowClear: true,
            placeholder: "Chọn người dùng",
            dropdownParent: $("#userModal") // 🔥 Fix lỗi dropdown không click được
        });

        // 🔥 Fix lỗi Bootstrap auto focus chặn Select2
        $('#userModal').on('shown.bs.modal', function () {
            $('#id_user').select2('open');
        });

        $('#userModal').on('select2:opening', function (e) {
            e.stopPropagation(); // Ngăn modal chặn Select2
        });
    });
</script>
<script>
    $(document).ready(function () {
        $('#id_user').on('change', function () {
            let selectedUserId = $(this).val();

            $.ajax({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                type: 'POST',
                data: {
                    id_user: selectedUserId,
                    action: "getCommission"
                },
                success: function (response) {
                    let data = response.data;

                    if (response.success) {
                        $('#withdrawal_amount').val(data.withdrawal_amount);
                    } else {
                        $('#withdrawal_amount').val('0'); // Nếu không có dữ liệu, hiển thị 0
                    }
                },
                error: function () {
                    console.log("Lỗi khi gửi dữ liệu.");
                }
            });
        });
        $('#form-add-transaction').on('submit', function (event) {
            event.preventDefault();

            var formData = new FormData(this); // Lấy toàn bộ dữ liệu form
            formData.append('action', 'AddTransaction'); // Thêm action cho Ajax request

            $.ajax({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                type: 'POST',
                cache: false,
                dataType: "json",
                processData: false,  // Bắt buộc để `FormData` hoạt động
                contentType: false,  // Không đặt Content-Type để gửi file
                data: formData,
                success: function(rs) {
                    $('.divgif').hide(); // Ẩn loader (nếu có)

                    if (rs.status == 1) {
                        Swal.fire({
                            icon: 'success',
                            text: rs.mess,
                        }).then(() => {
                            location.reload();  // Reload the page after the alert closes
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            text: rs.mess,
                        });
                    }
                },
            });
        });
    });

</script>