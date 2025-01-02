<?php

include __DIR__ .'/../includes/image_up_conf.php';

if(isset($_REQUEST['edit_action']) && (int) $_REQUEST['edit_action']==1){
    if(!empty( $_POST['code_city'])) {
        $id = (int)$_POST['id'];
        $code_city = $_POST['code_city'];
        $name_vi = $_POST['name_vi'];
        $name_en = $_POST['name_en'];
        $name_zhhans = $_POST['name_zhhans'];
        $resp = $wpdb->query("update cangvandon_city set code_city='$code_city', name_vi='$name_vi', name_en='$name_en',name_zhhans='$name_zhhans' where id=" . $id);
        if ($resp || (is_int($resp) && $resp >= 0)) {
            show_result(1);
        } else {
            show_result(0, 'Không thể thực hiện. Vui lòng xem lại.');
        }
    }else{
        show_result(0, 'Không thể thực hiện. Vui lòng xem lại.');
    }
    echo '<script>location="'.$module_path.'&sub=edit&id='.$id.'";</script>';exit();
        exit();
}


$id = (int) ($_GET['id']);

$row = $wpdb->get_row("SELECT * FROM  cangvandon_city where id=".$id);
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
                                <?php

                                show_list_row('Mã Thành phố(IATA)', array('code_city', $row->code_city, 'text'));
                                show_list_row('Tên thành phố (Tiếng Việt)', array('name_vi', $row->name_vi, 'text'));
                                show_list_row('Tên thành phố (Tiếng Anh)', array('name_en', $row->name_en, 'text'));
                                show_list_row('Tên thành phố (Tiếng Trung)', array('name_zhhans', $row->name_zhhans, 'text'));

                                ?>
                            </table>
                        </div>

                    </div>

                    <!--                    --><?php
                    //                    if(1==2){
                    //                        $wp_editor_setting = array('wpautop' => false);
                    //                        $content = $row->content;
                    //                        $idname = 'content';
                    //                        wp_editor( $content, $idname, $wp_editor_setting );
                    //                    }
                    //                    ?>
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