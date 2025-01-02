<?php

//include __DIR__ .'/../includes/image_up_conf.php';
//global $wpdb;
if(isset($_REQUEST['edit_action']) && (int) $_REQUEST['edit_action']==1){

        $id = (int)$_POST['id'];
        $name_user = $_POST['name_user'];
        $email_user = $_POST['email_user'];
        $phone_user = $_POST['phone_user'];
        $soluongsp = $_POST['soluongsp'];
        $adress = $_POST['address'];
        $status = $_POST['status'];

        $sql ="update `". $wpdb->prefix ."order` set `name_user`= '$name_user',`email_user`= '$email_user', `phone_user` = '$phone_user',
`soluongsp` = '$soluongsp',`address`= '$adress',`status`= '$status'
 where `id`=" . $id;
        $resp = $wpdb->query($sql);
        if ($resp ) {
            show_result(1);
        } else {
            show_result(0, 'Không thể thực hiện. Vui lòng xem lại.');
        }
    echo '<script>location="'.$module_path.'&sub=edit&id='.$id.'";</script>';exit();
    exit();
}

$id = (int) ($_GET['id']);

$row = $wpdb->get_row("SELECT * FROM ". $wpdb->prefix ."order where id=".$id);

//print_r($row->birthday);exit();
wp_enqueue_script('jquery');// jQuery
wp_enqueue_media();// This will enqueue the Media Uploader script


add_admin_css('main.css');

?>

<div class="wrap">
    <?php show_admin_box_edit_title($mdlconf,$module_path);?>

    <form id="post" method="post" action="<?php echo $module_path.'&sub=edit&edit_action=1';?>" name="post">
        <div id="poststuff">
            <div class="metabox-holder columns-2" id="post-body">

                <!---left-->
                <div id="post-body-content" class="pos1">
                    <div class="postbox">
                        <h2 class="hndle ui-sortable-handle api-title">Thông tin</h2>
                        <div class="inside">
                            <input type="hidden" value="<?php echo $id; ?>" name="id" />
                            <table class="form-table ft_metabox leftform" id="list">
                                <tr>
                                    <td>Mã đơn hàng:</td>
                                    <td> <span style="color: #0d84e3; font-weight: bold"><?= getmdh($row->id) ?></span>
                                    </td>
                                </tr>
                                <?php
                                show_list_row('Họ và tên', array('name_user',$row->name_user,'text'));
                                show_list_row('Email', array('email_user',$row->email_user,'text'));
                                show_list_row('Số điện thoại', array('phone_user',$row->phone_user,'text'));
                                show_list_row('Số lượng đặt mua', array('soluongsp',$row->soluongsp,'number'));
                                show_list_row('Địa chỉ nhận hàng', array('address',$row->address,'text'));
                                ?>
                                <tr>
                                    <td>Giá tiền</td>
                                    <td> <span style="color: red; font-weight: bold"><?= number_format($row->price).' VND' ?></span>
                                        <input type="hidden" name="price" value="<?= $row->price ?>">
                                    </td>
                                </tr>
                                <tr>
                                    <td>Trạng thái thanh toán</td>
                                    <td><select style="width: 100%" name="status" id="">
                                            <option value="0" <?= ($row->status == '0') ? 'selected' :'' ?>>Mới tạo</option>
                                            <option value="1" <?= ($row->status == '1') ? 'selected' :'' ?>>Đã tiếp nhận</option>
                                            <option value="2" <?= ($row->status == '2') ? 'selected' :'' ?>>Đã giao</option>
                                            <option value="3" <?= ($row->status == '3') ? 'selected' :'' ?>>Hủy</option>
                                        </select> </td>
                                </tr>
                            </table>
                        </div>

                    </div>
                </div>



                <div class="postbox-container" id="postbox-container-1">
                    <div class="meta-box-sortables ui-sortable" id="side-sortables">

                        <?php
                        show_admin_btn_save($row->active);
                        ?>


                    </div>
                </div>

            </div>
        </div>
    </form>

</div>

<?php
add_admin_js('image.upload.js');
?>
<?php if ($row->icon != '') {
    echo "<script>
$('#hide_add').hide();
</script>";
} ?>
<script  src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<script>
    jQuery(document).ready( function( $ ) {
        var myplugin_media_upload;
        $('#upload_image_button').click(function(e) {
            e.preventDefault();
            // If the uploader object has already been created, reopen the dialog
            if( myplugin_media_upload ) {
                myplugin_media_upload.open();
                return;
            }
            // Extend the wp.media object
            myplugin_media_upload = wp.media.frames.file_frame = wp.media({
                //button_text set by wp_localize_script()
                title: 'Choose Image',
                button: { text: 'Choose Image' },
                multiple: true //allowing for multiple image selection

            });
            /**
             *THE KEY BUSINESS
             *When multiple images are selected, get the multiple attachment objects
             *and convert them into a usable array of attachments
             */
            myplugin_media_upload.on( 'select', function(){
                var attachments = myplugin_media_upload.state().get('selection').map(
                    function( attachment ) {
                        attachment.toJSON();
                        return attachment;
                    });
                //loop through the array and do things with each attachment

                var i = 0;

                $("#obal").after(
                    "<div style='float:left' id='image-" + attachments[i].id  + "'>" +
                    "<img style='\n" +
                    "    margin-right: 10px;width: 57px;height: 57px' src=" +attachments[i].attributes.url+">"
                    + "<input type='hidden' id='image-input-" + attachments[i].id  + "' name='id_range[]' value='"+ attachments[i].id +"' >"
                    + "<input type='hidden'  name='icon' value='"+ attachments[i].attributes.url +"' >" +
                    "<div style='    position: relative;\n" +
                    "    top: -73px;\n" +
                    "    right: -42px;' class='actions acf-soh-target'>\n" +
                    "<a  class='acf-icon -cancel dark acf-gallery-remove remove-image' onclick='return false' href='#' id='remove' data-id='"+ attachments[i].id +"' title='Remove'></a>\n" +
                    "</div>" +
                    "</div>"
                );
                $('#hide_add').hide();
                //sample function 2: add hidden input for each image
            });
            myplugin_media_upload.open();
        });

        romve_image();
        function romve_image() {
            $('body').on('click', '.remove-image', function () {
                var id = $(this).attr('data-id');
                $('#image-'+id).remove();
                $('#hide_add').show();
            })
        }
    });
</script>
