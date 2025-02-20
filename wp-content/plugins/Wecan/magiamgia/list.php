<?php
include __DIR__ . "/../includes/padding.php";

//-------del-------------
action_list_del("voucher");
$pagesize = 20;
$s = '';
$my_str = "WHERE 1=1";
if (isset($_REQUEST['search'])) {
    $keyword = fixqQ($_REQUEST['keyword']);
    $special_member = (int)$_REQUEST['specialMember'];
    $s .= '&search=1';

    // Điều kiện
    $dkKey = '';
    $dkValue = '';
    //

    if (!empty($keyword)) {
        $my_str .= ' AND (ten_chuong_trinh like "%' . $keyword . '%"
        OR ma_voucher like "%' . $keyword . '%" )';
    }
    if ($special_member != 2) {
        $my_str .= ' AND voucher_for_special_member = ' . $special_member;
    }
//    pr($my_str);
}

$recordcount = count_total_db("wp_voucher", $my_str);
$paged = (int)$_GET['paged'];
if ($paged == 0) {
    $paged = 1;
}
$beginpaging = beginpaging($pagesize, $recordcount, $paged);
add_admin_css('main.css');
add_admin_js('jquery-2.2.4.min.js');
global $wpdb;
$list_voucher = $wpdb->get_results("SELECT * FROM `wp_voucher` ");

?>
<style>

    .iconloadgif {
        top: 0;
        right: 0;
        left: 0;
        bottom: 0;
        position: absolute;
        margin: auto;
    }
    .table-container {
        width: 100%;
        overflow-x: auto;
    }

    .wp-list-table {
        min-width: 1800px; /* Đảm bảo bảng rộng hơn để có thể cuộn */
        table-layout: fixed;
    }

    .divgif {
        position: fixed;
        width: 100%;
        height: 100%;
        z-index: 1100;
        display: none;
        background: #dedede;
        opacity: 0.5;
    }
</style>
<link rel="stylesheet" href="<?= get_template_directory_uri() ?>/pl2/style.css">
<input type="hidden" id="urlAjax" value="<?= admin_url() ?>admin-ajax.php">
<input type="hidden" id="setting_captcha" value="<?= get_field("setting_captcha", "option")["site_key"] ?>">
<input type="hidden" id="urlTheme" value="<?= get_template_directory_uri() ?>">
<div class="divgif">
    <img class="iconloadgif" src="<?= get_template_directory_uri() ?>/ajax/images/loading2.gif" alt="">
</div>
<div class="wrap">
    <h1 style="margin-bottom:15px;">List <?php echo $mdlconf['title']; ?>
        <a class="page-title-action" href="admin.php?page=discount_price&amp;sub=add">Add new voucher</a>
    </h1>

    <ul class="subsubsub">
        <li class="all"><a class="current" href="<?php echo $module_path; ?>">All<span
                        class="count">(<?php echo $recordcount; ?>)</span></a></li>
    </ul>

    <?php
    $myrows = $wpdb->get_results("
    SELECT * FROM `wp_voucher` 
    $my_str 
    ORDER BY 
        CASE WHEN status = 2 THEN 0 ELSE 1 END, 
        id DESC 
    LIMIT $beginpaging[0], $pagesize
");


    ?>
    <form class="" method="POST" action="<?php echo $module_path; ?>" style="overflow-x:auto ">
        <div class="tablenav top">
            <div class="alignleft actions bulkactions">
                <select id="bulk-action-selector-top" name="action">
                    <option value="-1">Task</option>
                    <option value="1">Delete</option>
                </select>
                <input type="submit" value="Apply" class="button action" id="doaction" name="doaction">
            </div>
        </div>

        <table class="wp-list-table widefat table-container striped posts">
            <thead>
            <tr class="headline">
                <td class="manage-column column-cb check-column" id="cb">
                    <input type="checkbox" id="cb-select-all-1"></td>
                <th style="width:30px;text-align:center;">STT</th>
                <th>Voucher name</th>
                <th>Voucher code</th>
                <th>Code usage time</th>
                <th>Discount type</th>
                <th>Discount Amount</th>
                <th>Maximum discount amount</th>
                <th>Quantity</th>
                <th>Number of uses</th>
                <!--                <th>Kiểu áp dụng</th>-->
                <th rowspan="2">Action</th>
            </tr>
            </thead>
            <tfoot>
            <tr class="headline">
                <td class="manage-column column-cb check-column" id="cb">
                    <input type="checkbox" id="cb-select-all-1"></td>
                <th style="width:30px;text-align:center;">STT</th>
                <th>Voucher name</th>
                <th>Voucher code</th>
                <th>Code usage time</th>
                <th>Discount type</th>
                <th>Discount Amount</th>
                <th>Maximum discount amount</th>
                <th>Quantity</th>
                <th>Number of uses</th>
                <!--                <th>Kiểu áp dụng</th>-->
                <th rowspan="3">Action</th>
            </tr>
            </tfoot>

            <?php
            $i = 0;
            foreach ($myrows as $key => $item) {
                $stt = $key + 1;
                $rowlink = $module_path . '&sub=edit&id=' . $item->id;
//                if ($item->status == 0) {
//                    continue;
//                }
                ?>
                <tr>
                    <th class="check-column" scope="row">
                        <input type="checkbox" class="checkbox-item" value="<?php echo $item->id; ?>" name="post[]"/>
                    </th>
                    <td><?= $stt ?></td>
                    <td><a href="<?= $rowlink ?>" target="blank"><?= $item->voucher_name ?></a></td>
                    <td> <?php echo $item->voucher_code ?></td>
                    <td><?php echo $item->start_date . ' <br> ' . $item->end_date ?></td>
                    <td><?= $item->discount_type == 1 ? 'According to the amount' : 'By percentage' ?> </td>
                    <?php if ($item->discount_type == 1) { ?>
                        <td><?= number_format($item->discount_amount, 0, ',', '.') . ' USD' ?></td>
                            <td>Not Applicable</td>
                        <?php } else { ?>
                            <td><?= $item->discount_amount . '%' ?></td>
                            <?php if ($item->max_discount == 0) { ?>
                                <td>Unlimited</td>
                            <?php } else { ?>
                                <td><?= number_format($item->max_discount, 0, ',', '.') . ' USD' ?></td>
                            <?php } ?>
                        <?php } ?>
                        <td><?= $item->number_of_vouchers ?> </td>
                        <td><?= $item->number_of_uses ?> </td>
                    <td>
                        <select class="statusVoucher" data-voucher-id="<?= $item->id ?>">
                            <?= getStatusDealed($item->status) ?>
                        </select>
                    </td>
                        <td><a href="javascript:void(0)" class="remove-action" data-id="<?= $item->id ?>">Remove</a></td>
                        <td><a href="<?= $rowlink ?>" class="detail-action" data-id="<?= $item->id ?>">View detail</a></td>
                    </tr>
                <?php } ?>
            </table>

    </form>

    <?php echo paddingpage($module_short_url, $beginpaging[1], $beginpaging[2], $beginpaging[3], $paged, $pagesize, $recordcount, $s); ?>

</div>


<?php
add_admin_js('common.js');
?>
<script src="https://www.google.com/recaptcha/api.js?render=<?= get_field("setting_captcha", "option")["site_key"] ?>"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.14.5/sweetalert2.min.css" integrity="sha512-Xxs33QtURTKyRJi+DQ7EKwWzxpDlLSqjC7VYwbdWW9zdhrewgsHoim8DclqjqMlsMeiqgAi51+zuamxdEP2v1Q==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.14.5/sweetalert2.min.js" integrity="sha512-JCDnPKShC1tVU4pNu5mhCEt6KWmHf0XPojB0OILRMkr89Eq9BHeBP+54oUlsmj8R5oWqmJstG1QoY6HkkKeUAg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
    let urlAjax = $("#urlAjax").val();
    let site_key = $("#site_key").val();
    let success_code = $("#success_code").val();
    $('.remove-action').on('click', function () {
        let voucherId = $(this).attr('data-id');

        Swal.fire({
            title: 'Confirm voucher deletion',
            text: "Are you sure you want to delete this voucher?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Delete'
        }).then((result) => {
            if (result.isConfirmed) {

                $.ajax({
                    url: urlAjax,
                    type: 'POST',
                    cache: false,
                    dataType: "json",
                    data: {
                        id: voucherId,
                        action: 'hide_voucher',
                        // action1: "delete_MGG",
                        // token1: token
                    },
                    beforeSend: function () {
                        $('.divgif').css('display', 'block');
                    },
                    success: function (rs) {
                        $('.divgif').css('display', 'none');
                        if (rs.status == 'success_code') {
                            Swal.fire({
                                icon: 'success',
                                text: rs.message,
                            });
                            setTimeout(function () {
                                window.location.reload();
                            }, 1000);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                text: rs.message,
                            });
                        }
                    }
                });
                return false;

            }
        })
    });
</script>

<script>
    $(document).ready(function () {
        // Select/Deselect all checkboxes
        $('#check-all').on('change', function () {
            $('.checkbox-item').prop('checked', $(this).prop('checked'));
        });

        // Handle bulk action submission
        $('#doaction').on('click', function (e) {
            e.preventDefault(); // Prevent default form submission

            const action = $('#bulk-action-selector-top').val();
            if (action === "1") { // Delete action
                const selectedIds = [];
                $('input[name="post[]"]:checked').each(function () {
                    selectedIds.push($(this).val());
                });

                if (selectedIds.length === 0) {
                    alert('Please select at least one item to delete.');
                    return;
                }

                // Confirm deletion
                if (confirm('Are you sure you want to delete the selected items?')) {
                    // Perform the delete action via AJAX
                    $.ajax({
                        url: urlAjax,
                        type: 'POST',
                        cache: false,
                        dataType: "json",
                        data: {
                            ids: selectedIds,
                            action: 'remover_select',
                        },
                        beforeSend: function () {
                            $('.divgif').css('display', 'block');
                        },
                        success: function (response) {
                            alert('Items deleted successfully.');
                            location.reload(); // Reload the page after deletion
                        },
                        error: function () {
                            alert('An error occurred while deleting items.');
                        }
                    });
                }
            } else {
                alert('Please select a valid action.');
            }
        });
    });

    $('.statusVoucher').on('change', function () {
        let newStatus = $(this).val();
        let voucherId = $(this).attr('data-voucher-id');

        $.ajax({
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            type: 'POST',
            cache: false,
            dataType: "text",
            data: {
                status: newStatus,
                voucherId: voucherId,
                action: 'changeVoucherStatus',
            },
            beforeSend: function() {
                $('.divgif').css('display', 'block');
            },
            success: function(rs) {
                $('.divgif').css('display', 'none');
                rs = JSON.parse(rs);
                if (rs.status == 'success_code') {
                    Swal.fire({
                        icon: 'success',
                        text: rs.mess,
                    });
                    location.reload(); // Reload the page after deletion
                } else {
                    Swal.fire({
                        icon: 'error',
                        text: rs.mess,
                    });
                }
            }
        });
    });
</script>
