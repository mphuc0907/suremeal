<?php
include __DIR__ . "/../includes/padding.php";

//-------del-------------
action_list_del("np_report_parent");
$pagesize = 20;
$s = '';
$rs = $wpdb->get_results("SELECT id,name FROM np_report_parent where parent = 0 ");
$my_str = "WHERE (np_report_parent.id_user = useragency.id AND status=3)";

if (isset($_REQUEST['search'])) {
    $keyword = fixqQ($_REQUEST['keyword']);
    $loaitaikhoan = fixqQ($_REQUEST['loaitaikhoan']);
    $nam_baocao_f = fixqQ($_REQUEST['nam_baocao_f']);
    $coquan = fixqQ($_REQUEST['coquan']);
    $diaphuong = fixqQ($_REQUEST['diaphuong']);
    $s .= '&search=1';

    // Điều kiện
    $dkKey = '';
    $dkValue = '';
    //
    if($loaitaikhoan == 1){
        $dkKey = 'useragency.co_quan';
        $dkValue = $coquan;
    }elseif ($loaitaikhoan == 2){
        $dkKey = 'useragency.dia_phuong';
        $dkValue = $diaphuong;
    }
    if($loaitaikhoan != null){
        $my_str .= ' AND useragency.loaitaikhoan='.$loaitaikhoan;
    }
    if (!empty($dkKey)) {
        $my_str .= ' AND '.$dkKey.' like "%' . $dkValue . '%"';
    }
    if (!empty($nam_baocao_f)) {
        $my_str .= ' AND np_report_parent.nam_baocao_f="'.$nam_baocao_f.'"';
    }
    if (!empty($keyword)) {
        $my_str .= ' AND (useragency.name like "%' . $keyword . '%" 
        OR useragency.text_donvi_khac like "%' . $keyword . '%" 
        OR useragency.text_co_quan like "%' . $keyword . '%"
        OR useragency.text_dia_phuong like "%' . $keyword . '%"
        OR np_report_parent.phone like "%' . $keyword . '%" 
        OR np_report_parent.name_tonghop like "%' . $keyword . '%" 
        OR np_report_parent.email like "%' . $keyword . '%" )';
    }
//    pr($my_str);
}

$recordcount = count_total_db_edit("np_report_parent.id","np_report_parent, useragency", $my_str);
$paged = (int)$_GET['paged'];
if ($paged == 0) {
    $paged = 1;
}
$beginpaging = beginpaging($pagesize, $recordcount, $paged);
add_admin_css('main.css');
add_admin_js('jquery-2.2.4.min.js');
$city = file_get_contents(plugin_dir_path(__FILE__) . 'vn_city.json');
$json = json_decode($city, true);

// Loại tài khoản
$loaitk = arrayLoaiTK();
$term_tinh = get_terms('tinh',
    array(
        'orderby' => 'name',
        'order'      => 'DESC',
        'hide_empty' => false,
    )
);
$term_coquan = get_terms('coquan',
    array(
        'orderby' => 'name',
        'order'      => 'DESC',
        'hide_empty' => false,
    )
);

// List user by dvk loaitaikhoan = 3
$dvk = $wpdb->get_results("SELECT id,name,dovi_khac,text_donvi_khac FROM useragency where loaitaikhoan = 3 ");

?>
<style>
    .flr{
        display: flex;
        float: right;
    }
    .d-none{
        display: none;
    }
    .iconloadgif {
        top: 0;
        right: 0;
        left: 0;
        bottom: 0;
        position: absolute;
        margin: auto;
    }
    /*.divgif {*/
    /*    position: fixed;*/
    /*    width: 100%;*/
    /*    height: 100%;*/
    /*    z-index: 1100;*/
    /*    display: none;*/
    /*    background: #dedede;*/
    /*    opacity: 0.5;*/
    /*}*/
    .grecaptcha-badge{
        display: none !important;
    }
    .mr-5{
        margin-right: 5px !important;
    }
</style>
<link rel="stylesheet" href="<?= get_template_directory_uri() ?>/pl2/style.css" >
<input type="hidden" id="urlAjax" value="<?= admin_url() ?>admin-ajax.php">
<input type="hidden" id="setting_captcha" value="<?= get_field("setting_captcha","option")["site_key"] ?>">
<input type="hidden" id="urlTheme" value="<?= get_template_directory_uri() ?>">
<div class="divgif">
    <img class="iconloadgif" src="<?= get_template_directory_uri() ?>/pl2/img/loading2.gif" alt="">
</div>
<div class="wrap">
    <h1 style="margin-bottom:15px;"><?php echo $mdlconf['title']; ?>
<!--        <a class="page-title-action" href="--><?php //echo $module_pathadd; ?><!--">Thêm mới</a>-->
    </h1>

    <ul class="subsubsub">
        <li class="all"><a class="current" href="<?php echo $module_path; ?>">Tất cả <span
                        class="count">(<?php echo $recordcount; ?>)</span></a></li>
    </ul>
    <form class="search-box flr" method="POST" action="<?php echo $module_path; ?>">
        <input class="sear_2" value="<?php if (isset($keyword)) echo $keyword; ?>" type="text" name="keyword"
               placeholder="Từ khóa">
        <select name="nam_baocao_f" id="nam_baocao_f" class="form-control">
            <option class="select-items" value="">Chọn năm báo cáo</option>
            <?php for ($i=2020; $i < 2051; $i++): ?>
                <option <?= ($i == $_REQUEST['nam_baocao_f'])?'selected':'' ?> value="<?= $i ?>"><?= $i ?></option>
            <?php endfor; ?>
        </select>

        <select name="loaitaikhoan" id="loaitaikhoan" class="form-control">
            <option class="select-items" value="">Chọn loại tài khoản</option>
            <?php foreach ($loaitk as $value): ?>
                <option class="select-items" <?= ($value['key'] == $_REQUEST['loaitaikhoan'])?'selected':'' ?> value="<?= $value['key'] ?>"><?= $value['value'] ?></option>
            <?php endforeach; ?>
        </select>
        <select name="coquan" id="coquan" class="form-control tab-detail tab-detail-1 <?= $_REQUEST['loaitaikhoan']==1?'':'d-none' ?>">
            <option class="select-items" value="">Chọn Cơ quan</option>
            <?php foreach ($term_coquan as $value): ?>
                <option class="select-items" <?= ($value->slug == $_REQUEST['coquan'])?'selected':'' ?> value="<?= $value->slug; ?>"><?= $value->name; ?></option>
            <?php endforeach; ?>
        </select>
        <select name="diaphuong" id="diaphuong" class="form-control tab-detail tab-detail-2 <?= $_REQUEST['loaitaikhoan']==2?'':'d-none' ?>">
            <option class="select-items" value="">Chọn Địa phương</option>
            <?php foreach ($term_tinh as $value): ?>
                <option class="select-items" <?= ($value->slug == $_REQUEST['diaphuong'])?'selected':'' ?> value="<?= $value->slug; ?>"><?= $value->name; ?></option>
            <?php endforeach; ?>
        </select>
        <select name="donvikhac" id="donvikhac" class="form-control tab-detail tab-detail-3 <?= $_REQUEST['loaitaikhoan']==3?'':'d-none' ?>">
            <option class="select-items" value="">Chọn đơn vị khác</option>
            <?php foreach ($dvk as $value): ?>
                <option class="select-items" <?= ($value->id == $_REQUEST['donvikhac'])?'selected':'' ?> value="<?= $value->id; ?>"><?= $value->text_donvi_khac; ?></option>
            <?php endforeach; ?>
        </select>

        <input type="submit" name="search" value="Tìm kiếm" class="button mr-5"/>
        <input type="button" name="exportTotal" id="exportTotal" value="Xuất Excel" class="button mr-5"/>
        <input style="display: none" type="button" name="excel" value="Xuất Excel" id="xuat_excel" class="button mr-5"/>
        <input type="button" name="mapDetail" id="mapDetail" value="Biểu đồ chi tiết" class="button mr-5"/>
        <input type="button" name="mapCompare" id="mapCompare" value="Biểu đồ so sánh" class="button"/>
    </form>
    <?php
    $myrows = $wpdb->get_results("SELECT 
      np_report_parent.*, useragency.id as iduseragency, useragency.name, useragency.co_quan, useragency.text_co_quan, 
      useragency.dia_phuong, useragency.text_dia_phuong, useragency.dovi_khac, useragency.text_donvi_khac, useragency.loaitaikhoan
    FROM `np_report_parent`,`useragency` ". $my_str ." ORDER BY np_report_parent.id DESC LIMIT  " . $beginpaging[0] . ",$pagesize");

    ?>


    <form class="" method="POST" action="<?php echo $module_path; ?>">
        <div class="tablenav top" style="display: none">
            <div class="alignleft actions bulkactions">
                <select id="bulk-action-selector-top" name="action">
                    <option value="-1">Tác vụ</option>
                    <option value="1">Xóa</option>
                </select>

                <input type="submit" value="Áp dụng" class="button action" id="doaction" name="doaction">
            </div>
        </div>

        <table class="wp-list-table widefat fixed striped posts">
            <thead>
            <tr class="headline">
                <td class="manage-column column-cb check-column" id="cb">
                    <input type="checkbox" id="cb-select-all-1"></td>
                <th style="width:30px;text-align:center;">STT</th>
                <th>Năm báo cáo</th>
                <th>Loại tài khoản</th>
                <th>Đơn vị báo cáo</th>
                <th>Người tổng hợp</th>
                <th>Số điện thoại</th>
                <th>Email</th>
                <th>Thời gian nộp báo cáo</th>
                <th>Xem báo cáo</th>
                <th></th>
            </tr>
            </thead>
            <tfoot>
            <tr class="headline">
                <td class="manage-column column-cb check-column" id="cb">
                    <input type="checkbox" id="cb-select-all-1"></td>
                <th style="width:30px;text-align:center;">STT</th>
                <th>Năm báo cáo</th>
                <th>Loại tài khoản</th>
                <th>Đơn vị báo cáo</th>
                <th>Người tổng hợp</th>
                <th>Số điện thoại</th>
                <th>Email</th>
                <th>Thời gian nộp báo cáo</th>
                <th>Xem báo cáo</th>
                <th></th>
            </tr>
            </tfoot>

            <?php
            $i = 0;
            foreach ($myrows as $item) {
                $i++;
                $rowlink = $module_path . '&sub=edit&id=' . $item->id;
//                $user = $wpdb->get_row("select * from  ".$wpdb->prefix."account where  id = '".$item->id_user."'")
                ?>
                <tr>
                    <th class="check-column" scope="row">
                        <input type="checkbox" value="<?php echo $item->id; ?>" name="post[]"/>
                    </th>
                    <td><?php echo get_list_order($pagesize, $paged, $i); ?></td>
                    <td><a href="javascript:void(0)" target="blank"><?= $item->nam_baocao_f ?></a></td>
                    <td> <?php echo showTextLoaiTK($item->loaitaikhoan) ?></td>
                    <td><?php echo showTextDonVi($item->loaitaikhoan, $item->text_co_quan, $item->text_dia_phuong, $item->text_donvi_khac) ?></td>
                    <td><?= $item->name_tonghop ?> </td>
                    <td><?= $item->phone ?> </td>
                    <td><?= $item->email ?> </td>
                    <td><?= (!empty($item->time_confirm))?date('d/m/Y', $item->time_confirm):"" ?> </td>
                    <td>
                        <a target="_blank" href="<?= get_permalink(getIdPage('chitiet-baocao')) ?>?id=<?= $item->id ?>">View</a>
                    </td>
                    <td><a href="javascript:void(0)" class="remove-report" data-id="<?= $item->id ?>">Xóa</a></td>
                </tr>
            <?php } ?>
        </table>

    </form>

    <?php echo paddingpage($module_short_url, $beginpaging[1], $beginpaging[2], $beginpaging[3], $paged, $pagesize, $recordcount, $s); ?>

</div>
<?php
    $arrayReport = listTitleReport();
?>
<div class="box-alert"></div>
<div class="modal fade" id="myModal-3" role="dialog">
    <div class="modal-dialog" style="">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button style="margin-top: -10px;" type="button" data-close="myModal-3" class="close close-notdata" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="grid simple">
                            <div class="grid-title">

                                <div class="submit-bt">
                                    <h3>Chọn loại báo cáo</h3>
                                    <div class="report-pt">
                                        <p>Báo cáo tổng hợp</p>
                                        <div class="btn-button">
                                            <button class="button-item" data-id="1">Export</button>
                                        </div>
                                    </div>

                                    <div class="report-pt">
                                        <p>Báo cáo phân tích</p>
                                        <?php foreach ($arrayReport as $key => $ar): ?>
                                            <div class="br-checkbox">
                                                <input id="check-state-1-<?= $key ?>" value="<?= $ar["key"] ?>" class="checkboxed" name="checkboxed_1[]" type="checkbox"  checked/>
                                                <label for="check-state-1-<?= $key ?>"><?= $ar["title"] ?></label>
                                            </div>
                                        <?php endforeach; ?>
                                        <div class="btn-button">
                                            <button class="button-item" data-id="2">Export</button>
                                        </div>
                                    </div>

                                    <div class="report-pt">
                                        <p>Báo cáo Chưa có số liệu</p>
                                        <div class="btn-button">
                                            <button class="button-item" data-id="3">Export</button>
                                        </div>
                                    </div>

                                </div>

                            </div>

                            <div class="grid-body no-border">
                                <div class="row" style="z-index: 100; position: relative;">
                                    <div class="col-lg-12 col-md-12">
                                        <div class="panel-white body-load-user">

                                        </div>
                                    </div>
                                    <div class="col-lg-12 col-md-12" style="text-align: right;">

                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default close-notdata" data-close="myModal-3" data-dismiss="modal">Thoát</button>
            </div>
        </div>

    </div>
</div>
<style>
    .overlay {
        position: fixed;
        visibility: hidden;
        opacity: 0;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 99;
        background: rgba(0, 0, 0, 0.5);
        -webkit-transition: opacity 0.2s ease;
        transition: opacity 0.2s ease;
    }

    .overlay.overlay-active {
        opacity: 1;
        visibility: visible;
    }

    .close-notdata{
        border: unset;
        background: unset;
        cursor: pointer;
    }
    .submit-bt{

    }
    .submit-bt h3{

    }
    .submit-bt .report-pt p{
        margin: 0;
        padding-top: 15px;
        padding-bottom: 15px;
        font-size: 16px;
    }
    .submit-bt .report-pt .br-checkbox{
        padding-bottom: 8px;
    }
    .submit-bt .report-pt{
        border-bottom: 1px solid #ccc;
        padding-bottom: 10px;
    }
    .btn-button{
        padding-top: 10px;
        padding-bottom: 10px;
    }
    .btn-button .button-item{
        border: unset;
        background: #019fe2;
        border-radius: 6px;
        color: #fff;
        padding: 8px 30px;
        cursor: pointer;
    }
</style>
<div class="overlay"></div>
<?php
add_admin_js('common.js');
?>
<script src="https://www.google.com/recaptcha/api.js?render=<?= get_field("setting_captcha","option")["site_key"] ?>"></script>
<link rel="stylesheet" href="<?= get_template_directory_uri() ?>/sweetalert2/dist/sweetalert2.min.css">
<script src="<?= get_template_directory_uri() ?>/sweetalert2/dist/sweetalert2.min.js"></script>

<script src="<?= get_template_directory_uri() ?>/pl2/js/jszip.js"></script>
<script src="<?= get_template_directory_uri() ?>/pl2/js/jszip-utils.js"></script>
<script src="<?= get_template_directory_uri() ?>/pl2/js/pspdfkit.js"></script>


<script>
    $(document).ready(function () {
        var vietnam ;
        $('#district').html('<option class="select-items"  value="">Chọn Quận Huyện</option>');
        var tp = "<?= $_REQUEST['city'] ?>";
        var district = "<?= $_REQUEST['district'] ?>";
        $.getJSON("<?= get_template_directory_uri() ?>/vn_city.json", function (data) {
            vietnam = data.items;
            vietnam.forEach(function (e) {
                var city = e.name;
                var ct = city.replace('Thành phố ','');
                var ct1 = ct.replace('Tỉnh ','');

                if(ct1 == tp){
                    var huyen =[];
                    huyen = e.huyen;
                    for (var i = 0; i<huyen.length;i++) {
                        $('#district').append('<option class="select-items" data-h="' + i + '" value="' + huyen[i].name + '">' + huyen[i].name + '</option>');
                        if (district != null) {
                            $('#district').find('option').each(function (i, e) {
                                if ($(e).val() == district) {
                                    $('#district').prop('selectedIndex', i);
                                }
                            });
                        }
                    }
                }
            })
        });
        $('#city').on('change',function () {
            var value = $('#city option:selected').val();
            $('#district').html('<option class="select-items"  value="">Chọn Quận Huyện</option>');
            $.getJSON("<?= get_template_directory_uri() ?>/vn_city.json", function (data) {
                vietnam = data.items;
                vietnam.forEach(function (e) {
                    var city = e.name;
                    var ct = city.replace('Thành phố ','');
                    var ct1 = ct.replace('Tỉnh ','');

                    if(ct1 == value){
                        var huyen =[];
                        huyen = e.huyen;
                        for (var i = 0; i<huyen.length;i++) {
                            $('#district').append('<option class="select-items" data-h="' + i + '" value="' + huyen[i].name + '">' + huyen[i].name + '</option>');
                        }
                    }
                })
            });

        })

        $("#loaitaikhoan").on('change', function () {
            let id = $(this).val();
            $(".tab-detail").hide();
            $(".tab-detail-"+id).show();
        });

    });
</script>

<script>
    // Close popup
    $(".close-notdata").on("click", function () {
        let close = $(this).attr("data-close");
        $(".overlay").removeClass("overlay-active");
        $("#" + close).remove("in");
        $("#" + close).hide();
    })

    // Info url and captcha
    let setting_captcha = $("#setting_captcha").val();
    let urlAjax = $("#urlAjax").val();

    // Remove Report
    $(".remove-report").on("click", function () {
        var id = $(this).attr('data-id');

        Swal.fire({
            title: 'Xóa báo cáo?',
            text: "Bạn có chắc chắn muốn xóa báo cáo này",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Xác nhận',
            cancelButtonText: 'Hủy'
        }).then((result) => {
            if (result.value) {

                grecaptcha.ready(function() {
                    grecaptcha.execute(setting_captcha, {action: 'subscribe_removeReport'}).then(function(token) {
                        $.ajax({
                            url: urlAjax,
                            type: 'POST',
                            cache: false,
                            dataType: "json",
                            data: {
                                id,
                                action: 'removeReport',
                                action1: "subscribe_removeReport",
                                token1: token
                            },
                            beforeSend: function () {
                                $('.divgif').css('display', 'block');
                            },
                            success: function (rs) {
                                $('.divgif').css('display', 'none');
                                if (rs.status == '0') {
                                    Swal.fire({
                                        icon: 'success',
                                        text: rs.mess,
                                    });
                                    setTimeout(function() {
                                        window.location.reload();
                                    }, 1000);
                                }else {
                                    Swal.fire({
                                        icon: 'error',
                                        text: rs.mess,
                                    });
                                }
                            }
                        });
                        return false;
                    });
                });

            }
        });
        return false;

    });

    // Export report excel
    $("body").on("click", "#exportTotal", function () {
        var year = $("#nam_baocao_f").val();
        if(year) {
            // Modal
            $("#myModal-3").addClass("in");
            $("#myModal-3").slideDown(500);
            $(".overlay").addClass("overlay-active");
            // End Modal
        }else{
            Swal.fire({
                icon: 'error',
                text: 'Vui lòng chọn năm báo cáo!',
            });
        }

    })

    // Export detail
    $("body").on("click", ".button-item", function (event) {
        var id = $(this).attr("data-id");
        var year = $("#nam_baocao_f").val();
        var loaitaikhoan = $("#loaitaikhoan").val();
        var coquan = $("#coquan").val();
        var diaphuong = $("#diaphuong").val();
        var donvikhac = $("#donvikhac").val();
        if(year) {
            if(id == 1) { // Báo cáo tổng hợp
                grecaptcha.ready(function () {
                    grecaptcha.execute(setting_captcha, {action: 'subscribe_exportReportTotal'}).then(function (token) {
                        $.ajax({
                            url: urlAjax,
                            type: 'POST',
                            cache: false,
                            dataType: "json",
                            data: {
                                year,
                                loaitaikhoan,
                                coquan,
                                diaphuong,
                                action: 'exportReportTotal',
                                action1: "subscribe_exportReportTotal",
                                token1: token
                            },
                            beforeSend: function () {
                                $('.divgif').css('display', 'block');
                            },
                            success: function (rs) {
                                $('.divgif').css('display', 'none');
                                if (rs.status == '0') {
                                    Swal.fire({
                                        icon: 'success',
                                        text: rs.mess,
                                    });
                                    window.location.href = rs.link;

                                    // setTimeout(function() {
                                    //     window.location.reload();
                                    // }, 1000);
                                } else {

                                    Swal.fire({
                                        icon: 'error',
                                        text: rs.mess,
                                    });
                                }
                            }
                        });
                        return false;
                    });
                });
            }else if(id == 2){
                // Báo cáo phân tích
                var selected = [];
                $('.checkboxed:checked').each(function() {
                    selected.push($(this).attr('value'));
                });
                grecaptcha.ready(function() {
                    grecaptcha.execute(setting_captcha, {action: 'subscribe_exportReport'}).then(function(token) {
                        $.ajax({
                            url: urlAjax,
                            type: 'POST',
                            cache: false,
                            dataType: "json",
                            data: {
                                id,
                                year,
                                loaitaikhoan,
                                coquan,
                                diaphuong,
                                selected,
                                action: 'exportReport',
                                action1: "subscribe_exportReport",
                                token1: token
                            },
                            beforeSend: function () {
                                $('.divgif').css('display', 'block');
                            },
                            success: function (rs) {
                                $('.divgif').css('display', 'none');
                                if (rs.status == '0') {

                                    var zipFilename = "Bao-cao-phan-tich-" + year;
                                    var zip = new JSZip();
                                    var count = 0;
                                    let link = rs.link;
                                    // link.forEach((item, index)=> {
                                    //     window.open(item);
                                    // })
                                    link.forEach(function(url){
                                        let strName = url.split("/");
                                        var filename = strName[strName.length - 1];
                                        // loading a file and add it in a zip file
                                        JSZipUtils.getBinaryContent(url, function (err, data) {
                                            if(err) {
                                                throw err; // or handle the error
                                            }
                                            zip.file(filename, data, {binary:true});
                                            count++;
                                            if (count == link.length) {
                                                zip.generateAsync({type:'blob'}).then(function(content) {
                                                    saveAs(content, zipFilename);
                                                });
                                            }
                                        });
                                    });

                                    Swal.fire({
                                        icon: 'success',
                                        text: rs.mess,
                                    });

                                    // setTimeout(function() {
                                    //     window.location.reload();
                                    // }, 1000);
                                }else {

                                    Swal.fire({
                                        icon: 'error',
                                        text: rs.mess,
                                    });
                                }
                            }
                        });
                        return false;
                    });
                });
            }else if(id == 3){

                var check = 1;
                if(!loaitaikhoan){
                    // check = 2;
                    // Swal.fire({
                    //     icon: 'error',
                    //     text: 'Vui lòng chọn loại tài khoản!',
                    // });
                }
                if(loaitaikhoan == 1){
                    // if(!coquan){
                    //     check = 2;
                    //     Swal.fire({
                    //         icon: 'error',
                    //         text: 'Vui lòng chọn cơ quan!',
                    //     });
                    // }
                }else if(loaitaikhoan == 2){
                    // if(!diaphuong){
                    //     check = 2;
                    //     Swal.fire({
                    //         icon: 'error',
                    //         text: 'Vui lòng chọn địa phương!',
                    //     });
                    // }
                }else if(loaitaikhoan == 3){
                    // if(!donvikhac){
                    //     check = 2;
                    //     Swal.fire({
                    //         icon: 'error',
                    //         text: 'Vui lòng chọn đơn vị khác!',
                    //     });
                    // }
                }
                if(check == 1){
                    // Báo cáo không có số liệu
                    grecaptcha.ready(function() {
                        grecaptcha.execute(setting_captcha, {action: 'subscribe_exportNoReport'}).then(function(token) {
                            $.ajax({
                                url: urlAjax,
                                type: 'POST',
                                cache: false,
                                dataType: "json",
                                data: {
                                    id,
                                    year,
                                    loaitaikhoan,
                                    coquan,
                                    diaphuong,
                                    donvikhac,
                                    selected,
                                    action: 'exportNoReport',
                                    action1: "subscribe_exportNoReport",
                                    token1: token
                                },
                                beforeSend: function () {
                                    $('.divgif').css('display', 'block');
                                },
                                success: function (rs) {
                                    $('.divgif').css('display', 'none');
                                    if (rs.status == '0') {

                                        var zipFilename = "Export-Report-No-Data-" + year + "-" + makeid(5);
                                        var zip = new JSZip();
                                        var count = 0;
                                        let link = rs.link;
                                        // link.forEach((item, index)=> {
                                        //     window.open(item);
                                        // })
                                        link.forEach(function(url){
                                            let strName = url.split("/");
                                            var filename = strName[strName.length - 1];
                                            // loading a file and add it in a zip file
                                            JSZipUtils.getBinaryContent(url, function (err, data) {
                                                if(err) {
                                                    throw err; // or handle the error
                                                }
                                                zip.file(filename, data, {binary:true});
                                                count++;
                                                if (count == link.length) {
                                                    zip.generateAsync({type:'blob'}).then(function(content) {
                                                        saveAs(content, zipFilename);
                                                    });
                                                }
                                            });
                                        });

                                        Swal.fire({
                                            icon: 'success',
                                            text: rs.mess,
                                        });


                                        // window.location.href = rs.link;

                                        // setTimeout(function() {
                                        //     window.location.reload();
                                        // }, 1000);
                                    }else {

                                        Swal.fire({
                                            icon: 'error',
                                            text: rs.mess,
                                        });
                                    }
                                }
                            });
                            return false;
                        });
                    });
                }


            }
        }else{
            Swal.fire({
                icon: 'error',
                text: 'Vui lòng chọn năm báo cáo!',
            });
        }
    })

    function makeid(length) {
        var result           = '';
        var characters       = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        var charactersLength = characters.length;
        for ( var i = 0; i < length; i++ ) {
            result += characters.charAt(Math.floor(Math.random() * charactersLength));
        }
        return result;
    }

    // Map detail
    $("body").on("click", "#mapDetail", function () {
        var loaitaikhoan = $("#loaitaikhoan").val();
        var coquan = $("#coquan").val();
        var diaphuong = $("#diaphuong").val();
        var donvikhac = $("#donvikhac").val();

        var check = 1;
        var value = "";
        if(loaitaikhoan){
            if(loaitaikhoan == 1){
                if(!coquan){
                    check = 2;
                    Swal.fire({
                        icon: 'error',
                        text: 'Vui lòng chọn cơ quan!',
                    });
                }
                value = coquan;
            }else if(loaitaikhoan == 2){
                if(!diaphuong){
                    check = 2;
                    Swal.fire({
                        icon: 'error',
                        text: 'Vui lòng chọn địa phương!',
                    });
                }
                value = diaphuong;
            }else if(loaitaikhoan == 3){
                if(!donvikhac){
                    check = 2;
                    Swal.fire({
                        icon: 'error',
                        text: 'Vui lòng chọn đơn vị khác!',
                    });
                }
                value = donvikhac;
            }

        }else{
            check = 2;
            Swal.fire({
                icon: 'error',
                text: 'Vui lòng chọn loại tài khoản !',
            });
        }
        if(check == 1){
            let url = "<?= get_permalink(getIdPage('Bieu-do')) ?>?id=" + value;
            window.open(url);
        }
    });

    // Map Comper
    $("body").on("click", "#mapCompare", function () {
        var loaitaikhoan = $("#loaitaikhoan").val();
        var nam_baocao_f = $("#nam_baocao_f").val();

        var check = 1;
        if(!nam_baocao_f){
            Swal.fire({
                icon: 'error',
                text: 'Vui lòng chọn năm báo cáo !',
            });
            check = 2;
        }
        if(!loaitaikhoan){
            Swal.fire({
                icon: 'error',
                text: 'Vui lòng chọn loại tài khoản !',
            });
            check = 2;
        }
        if(check == 1){
            let url = "<?= get_permalink(getIdPage('Bieu-do-so-sanh')) ?>?yearn=" + nam_baocao_f + "&ltk=" + loaitaikhoan;
            window.open(url);
        }

    });

</script>
