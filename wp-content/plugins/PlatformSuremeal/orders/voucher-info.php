<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 5/18/2023
 * Time: 4:46 PM
 */
    global $wpdb;
    $code = $_GET['code'];

    // Lay thong tin ma voucher
    $voucher = $wpdb->get_row("SELECT * FROM tt_vouchers WHERE ma_voucher = '{$code}'");

    // Lay danh sach don hang su dung voucher
    $orders = $wpdb->get_results("SELECT * FROM tt_orders WHERE code_voucher = '{$code}'");

    // Tong tin
    $total = 0;
?>
<div class="wrap">
    <h1>
        Chi tiết voucher
    </h1>
    <div id="poststuff">
        <input type="hidden" value="<?php echo $id; ?>" name="id"/>
        <div class="metabox-holder columns-2" id="post-body">
            <!---left-->
            <div id="post-body-content" class="pos1">
                <div class="postbox">
                    <h2 class="hndle ui-sortable-handle api-title">Thông tin</h2>
                    <div class="inside">
                        <table class="form-table ft_metabox leftform">
                            <tr>
                                <td style="font-weight: 700">Loại voucher</td>
                                <?php if($voucher->kieu_voucher == 1): ?>
                                    <td>Voucher dành cho sản phẩm</td>
                                <?php else: ?>
                                    <?php if(count(json_decode($voucher->voucher_user)) != 1): ?>
                                        <td>Voucher dành cho tất cả thành viên</td>
                                    <?php else: ?>
                                        <td>Voucher dành cho thành viên đặc biệt</td>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </tr>
                            <?php if(count(json_decode($voucher->voucher_user)) == 1): ?>
                                <tr>
                                    <td style="font-weight: 700">Thành viên đặc biệt</td>
                                    <?php
                                        $user_id = json_decode($voucher->voucher_user)[0];
                                        $getUser = $wpdb->get_row("SELECT * FROM useragency WHERE id={$user_id}");
                                        $rowlinkUser = 'admin.php?page=daily&sub=edit&id=' . $user_id;
                                    ?>
                                    <td><a href="<?php echo $rowlinkUser; ?>"><?php echo $getUser->name ?></a></td>
                                </tr>
                            <?php endif; ?>
                            <tr>
                                <td style="font-weight: 700">Số lượt sử dụng voucher</td>
                                <td><?php echo $voucher->total_use; ?></td>
                            </tr>
                        </table>
                    </div>

                    <h2 class="hndle ui-sortable-handle api-title">Danh sách đơn hàng sử dụng voucher</h2>
                    <table class="form-table ft_metabox leftform" border="1">
                        <tr>
                            <th>STT</th>
                            <th>Mã đơn hàng</th>
                            <th>Người mua</th>
                            <th>Trạng thái đơn hàng</th>
                            <th>Tổng tiền thanh toán</th>
                            <th>Ngày đặt</th>
                        </tr>
                        <?php foreach ($orders as $key => $order): ?>
                            <?php
                                $delivery_info = json_decode($order->delivery_information);
                                $rowlink = 'admin.php?page=order_manager&sub=edit&id=' . $order->id;
                                $total += (int)$order->price_payment;
                            ?>
                            <tr>
                                <td><?php echo $key; ?></td>
                                <td><a href="<?php echo $rowlink; ?>"><?php echo $order->order_code ?></a></td>
                                <td><?php echo $delivery_info->fullname ?></td>
                                <td>
                                    <?php
                                        if($order->status_transport == 1) {
                                            echo 'Chờ xác nhận';
                                        } elseif ($order->status_transport == 2) {
                                            echo 'Chờ lấy hàng';
                                        } elseif ($order->status_transport == 3) {
                                            echo 'Đang giao hàng';
                                        } elseif ($order->status_transport == 4) {
                                            echo 'Đã giao hàng';
                                        }  elseif ($order->status_transport == 5) {
                                            echo 'Đã hủy';
                                        }
                                    ?>
                                </td>
                                <td><?php echo number_format($order->price_payment, 0, ',', '.') ?>đ</td>
                                <td><?php echo date('H:i d/m/Y', $order->time_order) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                    <div style="font-size: 18px; font-weight: 600; margin: 20px; text-align: right">
                        Tổng: <?php echo number_format($total, 0, ',', '.') ?>đ
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
