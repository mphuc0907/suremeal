<?php

//include __DIR__ .'/../includes/image_up_conf.php';

if (isset($_REQUEST['add_action']) && (int)$_REQUEST['add_action'] == 1) {
    if(!empty( $_POST['code_city'])) {
        $code_city = $_POST['code_city'];
        $name_vi = $_POST['name_vi'];
        $name_en = $_POST['name_en'];
        $name_zhhans = $_POST['name_zhhans'];
        $resp = $wpdb->query("insert into cangvandon_city (code_city,name_vi,name_en,name_zhhans) values ('$code_city','$name_vi','$name_en', '$name_zhhans')");
        $rs_new_id = get_ID_last('cangvandon_city');
        if ($resp) {
            show_result(1, 'Thành công. <a href="' . $module_path . '&sub=edit&id=' . $rs_new_id . '">Xem chi tiết</a>');
        } else {
            show_result(0, 'Không thể thực hiện. Vui lòng xem lại.');
            echo '<script>location="' . $module_path . '&sub=add";</script>';
            exit();

        }

    } else {
        show_result(0, 'Không thể thực hiện. Vui lòng xem lại.');
        echo '<script>location="' . $module_path . '&sub=add";</script>';
        exit();

    }


}

wp_enqueue_script('jquery');// jQuery
wp_enqueue_media();// This will enqueue the Media Uploader script


add_admin_css('main.css');

?>
<style>
    #gallery-metabox-list li {
        float: left;
        width: 150px;
        text-align: center;
        margin: 10px 10px 10px 0;
        cursor: move;
    }
</style>

<div class="wrap">
    <?php show_admin_box_add_title($mdlconf, $module_path); ?>

    <form id="post" method="post" action="<?php echo $module_path . '&sub=add&add_action=1'; ?>" name="post">
        <div id="poststuff">
            <div class="metabox-holder columns-2" id="post-body">

                <!---left-->
                <div id="post-body-content" class="pos1">
                    <div class="postbox">
                        <h2 class="hndle ui-sortable-handle api-title">Thông tin</h2>
                        <div class="inside">
                            <table class="form-table ft_metabox leftform" id="list">
                                <?php

                                show_list_row('Mã Thành phố(IATA)', array('code_city', $row->code_city, 'text'));
                                show_list_row('Tên thành phố (Tiếng Việt)', array('name_vi', $row->name_vi, 'text'));
                                show_list_row('Tên thành phố (Tiếng Anh)', array('name_en', $row->name_en, 'text'));
                                show_list_row('Tên thành phố (Tiếng Trung)', array('name_zhhans', $row->name_zhhans, 'text'));
                                ?>
                            </table>
                        </div>
                    </div>
                </div>


                <!--right-->
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
<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<script>
    jQuery(document).ready(function ($) {
        var myplugin_media_upload;
        $('#upload_image_button').click(function (e) {
            e.preventDefault();
            // If the uploader object has already been created, reopen the dialog
            if (myplugin_media_upload) {
                myplugin_media_upload.open();
                return;
            }
            // Extend the wp.media object
            myplugin_media_upload = wp.media.frames.file_frame = wp.media({
                //button_text set by wp_localize_script()
                title: 'Choose Image',
                button: {text: 'Choose Image'},
                multiple: true //allowing for multiple image selection

            });
            /**
             *THE KEY BUSINESS
             *When multiple images are selected, get the multiple attachment objects
             *and convert them into a usable array of attachments
             */
            myplugin_media_upload.on('select', function () {
                var attachments = myplugin_media_upload.state().get('selection').map(
                    function (attachment) {
                        attachment.toJSON();
                        return attachment;
                    });
                //loop through the array and do things with each attachment

                var i = 0;

                $("#obal").after(
                    "<div style='float:left' id='image-" + attachments[i].id + "'>" +
                    "<img style='\n" +
                    "    margin-right: 10px;width: 57px;height: 57px' src=" + attachments[i].attributes.url + ">"
                    + "<input type='hidden' id='image-input-" + attachments[i].id + "' name='id_range[]' value='" + attachments[i].id + "' >"
                    + "<input type='hidden'  name='icon' value='" + attachments[i].attributes.url + "' >" +
                    "<div style='    position: relative;\n" +
                    "    top: -73px;\n" +
                    "    right: -42px;' class='actions acf-soh-target'>\n" +
                    "<a  class='acf-icon -cancel dark acf-gallery-remove remove-image' onclick='return false' href='#' id='remove' data-id='" + attachments[i].id + "' title='Remove'></a>\n" +
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
                $('#image-' + id).remove();
                $('#hide_add').show();
            })
        }
    });
</script>
