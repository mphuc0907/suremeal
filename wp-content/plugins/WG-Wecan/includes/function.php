<?php
function fixqQ($str)
{
    $str = str_replace("\\", "", trim($str));

    $str = str_replace("'", "\'", trim($str));
    $str = str_replace('"', '\"', trim($str));

    return $str;
}


function fixquotes($str)
{
    $str = str_replace("\\", "", trim($str));
    $str = str_replace("'", "", trim($str));
    return trim(($str));
}

function fixqContent($str)
{
    $str = str_replace("\\", "", trim($str));

    $str = str_replace("'", "\'", trim($str));
    //$str=str_replace('"','\"',trim($str));
    $str = trim($str);

    return $str;
}

function show_result($status, $mess = 'Không thành công, vui lòng kiểm tra lại.')
{
    if ($status == 1) {
        if ($mess == 'Không thành công, vui lòng kiểm tra lại.') {
            $mess = 'Thành công';
        }
        echo '<div class="notice notice-success is-dismissible" id="message">
				<p>' . $mess . '</p>
			</div>';

    } else {
        echo '<div class="notice notice-warning is-dismissible" id="message">
				<p>' . $mess . '</p>
			</div>';

    }
}


if (!function_exists('vietdecode')) {
    function vietdecode($str)
    {
        $coDau = array(
            "à",
            "á",
            "ạ",
            "ả",
            "ã",
            "â",
            "ầ",
            "ấ",
            "ậ",
            "ẩ",
            "ẫ",
            "ă",
            "ằ",
            "ắ",
            "ặ",
            "ẳ",
            "ẵ",
            "è",
            "é",
            "ẹ",
            "ẻ",
            "ẽ",
            "ê",
            "ề",
            "ế",
            "ệ",
            "ể",
            "ễ",
            "ì",
            "í",
            "ị",
            "ỉ",
            "ĩ",
            "ò",
            "ó",
            "ọ",
            "ỏ",
            "õ",
            "ô",
            "ồ",
            "ố",
            "ộ",
            "ổ",
            "ỗ",
            "ơ",
            "ờ",
            "ớ",
            "ợ",
            "ở",
            "ỡ",
            "ù",
            "ú",
            "ụ",
            "ủ",
            "ũ",
            "ư",
            "ừ",
            "ứ",
            "ự",
            "ử",
            "ữ",
            "ỳ",
            "ý",
            "ỵ",
            "ỷ",
            "ỹ",
            "đ",
            "À",
            "Á",
            "Ạ",
            "Ả",
            "Ã",
            "Â",
            "Ầ",
            "Ấ",
            "Ậ",
            "Ẩ",
            "Ẫ",
            "Ă",
            "Ằ",
            "Ắ",
            "Ặ",
            "Ẳ",
            "Ẵ",
            "È",
            "É",
            "Ẹ",
            "Ẻ",
            "Ẽ",
            "Ê",
            "Ề",
            "Ế",
            "Ệ",
            "Ể",
            "Ễ",
            "Ì",
            "Í",
            "Ị",
            "Ỉ",
            "Ĩ",
            "Ò",
            "Ó",
            "Ọ",
            "Ỏ",
            "Õ",
            "Ô",
            "Ồ",
            "Ố",
            "Ộ",
            "Ổ",
            "Ỗ",
            "Ơ",
            "Ờ",
            "Ớ",
            "Ợ",
            "Ở",
            "Ỡ",
            "Ù",
            "Ú",
            "Ụ",
            "Ủ",
            "Ũ",
            "Ư",
            "Ừ",
            "Ứ",
            "Ự",
            "Ử",
            "Ữ",
            "Ỳ",
            "Ý",
            "Ỵ",
            "Ỷ",
            "Ỹ",
            "Đ",
            "ê",
            "ù",
            "à"
        );

        $khongDau = array(
            "a",
            "a",
            "a",
            "a",
            "a",
            "a",
            "a",
            "a",
            "a",
            "a",
            "a",
            "a",
            "a",
            "a",
            "a",
            "a",
            "a",
            "e",
            "e",
            "e",
            "e",
            "e",
            "e",
            "e",
            "e",
            "e",
            "e",
            "e",
            "i",
            "i",
            "i",
            "i",
            "i",
            "o",
            "o",
            "o",
            "o",
            "o",
            "o",
            "o",
            "o",
            "o",
            "o",
            "o",
            "o",
            "o",
            "o",
            "o",
            "o",
            "o",
            "u",
            "u",
            "u",
            "u",
            "u",
            "u",
            "u",
            "u",
            "u",
            "u",
            "u",
            "y",
            "y",
            "y",
            "y",
            "y",
            "d",
            "A",
            "A",
            "A",
            "A",
            "A",
            "A",
            "A",
            "A",
            "A",
            "A",
            "A",
            "A",
            "A",
            "A",
            "A",
            "A",
            "A",
            "E",
            "E",
            "E",
            "E",
            "E",
            "E",
            "E",
            "E",
            "E",
            "E",
            "E",
            "I",
            "I",
            "I",
            "I",
            "I",
            "O",
            "O",
            "O",
            "O",
            "O",
            "O",
            "O",
            "O",
            "O",
            "O",
            "O",
            "O",
            "O",
            "O",
            "O",
            "O",
            "O",
            "U",
            "U",
            "U",
            "U",
            "U",
            "U",
            "U",
            "U",
            "U",
            "U",
            "U",
            "Y",
            "Y",
            "Y",
            "Y",
            "Y",
            "D",
            "e",
            "u",
            "a"
        );

        return str_replace($coDau, $khongDau, $str);
    }
}
function link_encode($title)
{
    $title = vietdecode($title);
    $title = trim(strtolower($title));
    $a1 = array(
        '     ',
        '    ',
        '   ',
        '  ',
        ',',
        '#',
        ';',
        ':',
        "'",
        '>',
        '<',
        '@',
        '!',
        '^',
        '*',
        '|',
        '/',
        '"',
        '%',
        '?',
        ']',
        '[',
        '}',
        '{',
        '&',
        '_',
        '---',
        '--',
        '$',
        '#',
        '\\'
    );

    $a2 = array(
        ' ',
        ' ',
        ' ',
        ' ',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '',
        '-',
        '-',
        '-',
        '',
        '',
        ''
    );
    $title = str_replace($a1, $a2, $title);
    $title = str_replace(' ', '-', $title);
    $title = str_replace('----', '-', $title);
    $title = str_replace('---', '-', $title);
    $title = str_replace('--', '-', $title);

    return $title;
}

function valid_email($email)
{
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return true;
    } else {
        return false;
    }

}

function notPermission()
{
    echo '<h3>Bạn không đủ quyền để thực hiện thao tác này.</h3>';
}

function get_faculty_id()
{
    global $wpdb;
    $userid = get_current_user_id();
    $faculty_row = $wpdb->get_results("SELECT id,facultyid FROM faculty_user WHERE userid='" . $userid . "' ORDER BY id ASC LIMIT 0,1");
    $my_facultyid = 0;
    if (count($faculty_row) > 0) {
        $my_facultyid = $faculty_row[0]->facultyid;
    }

    //var_dump($my_facultyid);exit();
    return $my_facultyid;
}

function findPosition($userid)
{

    $userdate = get_userdata($userid);
    $userRoles = $userdate->roles;

    //var_dump($userdate);exit();
    $re = '';
    if ($userRoles[0] == 'per100') {
        $re = 'Trường khoa';
    } else if ($userRoles[0] == 'per105') {
        $re = 'Nhân viên';
    }

    return $re;
}


/*---------------------------*/


function show_select_category($post_type, $categoryid, $facultyid, $parentid = 0, $space = '')
{
    global $wpdb;
    $id = (int)$categoryid;
    //---get parentid---
    $parent = $wpdb->get_results("SELECT id,parentid FROM faculty_category WHERE active=1 and id='$id' ORDER BY id ASC");
    $my_parentid = $parent[0]->parentid;

    $sql = "SELECT id,title FROM faculty_category WHERE active=1 and post_type='$post_type' and parentid='" . $parentid . "' and facultyid='$facultyid' and id<>'$id' ORDER BY id ASC";
    $faculty = $wpdb->get_results($sql);


    foreach ($faculty as $n) {
        $sel = '';
        if ($n->id == $my_parentid) {
            $sel = " selected";
        }
        echo '<option value="' . $n->id . '" ' . $sel . '>' . $space . $n->title . '</option>';

        show_select_category($post_type, $id, $facultyid, $n->id, $space . '--');
    }
}


function show_select_category_post($post_type, $categoryid, $facultyid, $parentid = 0, $space = '')
{
    global $wpdb, $userPer;
    $id = (int)$categoryid;
    $post_type = getCatePostType($post_type);

    $str = '';
    if (!get_sas_supper_user($userPer)) {
        $str = " and facultyid='$facultyid' ";
    }

    $sql = "SELECT id,title,facultyid FROM faculty_category WHERE active=1 and post_type='$post_type' and parentid='" . $parentid . "'  $str   ORDER BY id ASC";
    $faculty = $wpdb->get_results($sql);


    foreach ($faculty as $n) {
        $faculty_title = '';
        if (get_sas_supper_user($userPer)) {
            $faculty_title = 'Khoa ' . get_faculty($n->id) . ' - ';
        }

        $sel = '';
        if ($n->id == $id) {
            $sel = " selected";
        }
        echo '<option value="' . $n->id . '" ' . $sel . '>' . $space . $faculty_title . $n->title . '</option>';

        show_select_category($post_type, $id, $facultyid, $n->id, $space . '--');
    }
}

function convertTime($mytime)
{
    $am = ' sáng';
    $t1 = (int)($mytime / 60);
    $t2 = $mytime - $t1 * 60;
    if ($t2 < 10) {
        $t2 = '0' . $t2;
    }
    if ($t1 >= 12) {
        $am = ' chiều';
    }
    if ($t1 > 12) {
        $t1 = $t1 - 12;
    }

    return $t1 . ':' . $t2 . $am;
}

function get_faculty($facultyid)
{
    global $wpdb;
    $faculty_row = $wpdb->get_results("SELECT id,title FROM hospital_faculty WHERE id='" . $facultyid . "' ORDER BY id ASC LIMIT 0,1");

    return $faculty_row[0]->title;

}

function show_select_faculty($facultyid = 0)
{
    global $wpdb, $userPer;

    if (!get_sas_supper_user($userPer)) {
        return '';
    }

    $a = '<div class="postbox " id="categorydiv">
		<button aria-expanded="true" class="handlediv button-link" type="button">
			<span class="screen-reader-text">Toggle panel: Khoa</span>
			<span aria-hidden="true" class="toggle-indicator"></span>
		</button>

		<h2 class="hndle ui-sortable-handle"><span>Khoa</span></h2>

		<div class="inside">		
				<select name="facultyid">
					<option value="0">-- Chọn --</option>
					';

    $rs = $wpdb->get_results("SELECT id,title FROM hospital_faculty WHERE active=1 ORDER BY id DESC");

    foreach ($rs as $row) {
        $act = '';
        if ($facultyid == $row->id) {
            $act = ' selected=""';
        }
        $a .= '<option value="' . $row->id . '" ' . $act . '>' . $row->title . '</option>';
    }

    $a .= '</select>
			
		</div>
	</div>';

    return $a;
}


function show_search_select_faculty($facultyid = 0)
{
    global $wpdb, $userPer;

    if (!get_sas_supper_user($userPer)) {
        return '';
    }

    $a = '<select name="facultyid">
			<option value="0">-- Chọn --</option>';

    $rs = $wpdb->get_results("SELECT id,title FROM hospital_faculty WHERE active=1 ORDER BY id DESC");
    foreach ($rs as $row) {
        $act = '';
        if ($facultyid == $row->id) {
            $act = ' selected=""';
        }
        $a .= '<option value="' . $row->id . '" ' . $act . '>' . $row->title . '</option>';
    }

    $a .= '</select>';

    return $a;
}


function get_array_str($s, $charactor = ",")
{
    $return = array();
    $s = explode($charactor, trim($s));
    foreach ($s as $v) {
        $v = (int)trim($v);
        if ($v > 0) {
            $return[] = $v;
        }
    }

    return $return;
}

function htmlview($s)
{
    $s = str_replace('\\', '', trim($s));
    $s = htmlentities($s, ENT_QUOTES);

    return $s;
}

function action_list_del($table)
{
    global $wpdb;

    if ($table == '') {
        show_result(0, 'Lỗi table');

        return '';
    }

    if (isset($_POST['doaction'])) {
        $action = (int)$_POST['action'];
        if ($action == 1) {
            $delid = $_POST['post'];
            if (is_array($delid)) {
                $str_del = '';
                foreach ($delid as $did) {
                    if ($did > 0) {
                        $str_del .= ' or id=' . $did;
                    }
                }

                $str_del = trim($str_del);
                if ($str_del != '') {
                    $str_del = ltrim($str_del, 'or');
                }

                if ($str_del != '') {
                    $resp = $wpdb->query("DELETE FROM " . $table . " WHERE " . $str_del);

                    if ($resp) {
                        show_result(1, 'Xóa thành công');
                    } else {
                        show_result(0, 'Không thể thực hiện. Vui lòng xem lại.');
                    }
                }

            }
        }
    }

}
function show_l_row($title = '', $number ='',$arr = array(0 => '', 1 => '', 2 => '', 3 => '')){
    if ($arr[2] == 'hocphantext') {
        echo '<tr class="rowTerm">
					<td style="width:150px;">' . $title . '</td>
					<td><input type="text" name="' . $arr[0] . '" value="' . $arr[1] . '" size="50" /><button type="button" class="button button-cancel Removechild" id="Removechild'.$number.'"  >Remove</button></td>
				</tr>';

    }
}
function show_list_row($title = '', $arr = array(0 => '', 1 => '', 2 => '', 3 => ''))
{

    if ($arr[2] == 'text') {
        echo '<tr>
					<td style="width:150px;">' . $title . ' <span style="color: red">*</span></td>
					<td><input '. $arr[3].' type="text" id="' . $arr[0] . '" name="' . $arr[0] . '" value="' . $arr[1] . '" size="50" /></td>
				</tr>';

    }
    if ($arr[2] == 'hocphan') {
        echo '<tr>
                    <td style="width:150px;">Học phần đã hoàn thành</td>
					<td></td>
				</tr>';

    }

    if ($arr[2] == 'number') {
        echo '<tr>
					<td style="width:150px;">' . $title . '</td>
					<td><input  style="width:341px;" type="number" name="' . $arr[0] . '" value="' . $arr[1] . '" size="50" /></td>
				</tr>';

    }
    if ($arr[2] == 'date') {
        echo '<tr>
					<td style="width:150px;">' . $title . '</td>
					<td><input style="width:341px;" type="date" name="' . $arr[0] . '" value="' . $arr[1] . '" size="50" /></td>
				</tr>';

    }

    if ($arr[2] == 'textarea') {
        echo '<tr>
					<td style="width:150px;">' . $title . '</td>
					<td><textarea name="' . $arr[0] . '" rows="2" cols="48">' . $arr[1] . '</textarea></td>
				</tr>';

    }


}


function show_admin_featured_image($image)
{
    if ($image != '') {
        $img = '<img src="' . get_site_url() . '/' . $image . '">';
    }
    echo '<div class="postbox " id="postimagediv">
	<button aria-expanded="true" class="handlediv button-link" type="button">
		<span class="screen-reader-text">Toggle panel: Ảnh chức năng cho bài viết</span>
		<span aria-hidden="true" class="toggle-indicator"></span>
	</button>
	<h2 class="hndle ui-sortable-handle"><span>Ảnh chức năng cho bài viết</span></h2>
	<div class="inside">
		<input type="hidden" name="image" class="image_url" value="' . $image . '" />
		<div class="image_show">' . $img . '</div>
		<p><a class="image_clk">Chọn ảnh tiêu biểu</a></p>
	</div>
</div>';
}

function show_admin_btn_save($active = 0)
{
    $active = (int)$active;
    $checked = '';
    if ($active == 1) {
        $checked = ' checked=""';
    }

    echo '<div class="postbox " id="submitdiv">
	<h2 class="hndle ui-sortable-handle"><span>Cập nhật</span></h2>

	<div class="inside">
		<div id="submitpost" class="submitbox">
			<div id="minor-publishing" style="display:none;">
				<div id="misc-publishing-actions">
					<div class="misc-pub-section misc-pub-post-status">
						<label for="post_status">Trạng thái:</label>
						<label class="mrgl"><input type="checkbox" name="active" value="1" ' . $checked . '  /> Hiện</label>
					</div>
				</div>
				<div class="clear"></div>
			</div>

			<div id="major-publishing-actions">
				<div id="publishing-action">
					<input type="submit" value="Cập nhật" id="publish" class="button button-primary button-large" name="save">
				</div>
				<div class="clear"></div>
			</div>
		</div>
	</div>
</div>';
}

function add_admin_css($css)
{
    echo '<link rel="stylesheet" href="' . plugin_path . '/assets/css/' . $css . '" type="text/css" media="all" />';
}

function add_admin_js($js)
{
    echo '<script type="text/javascript" src="' . plugin_path . '/assets/js/' . $js . '"></script>';
}

function get_ID_last($table)
{
    global $wpdb;

    $rs_new = $wpdb->get_results("SELECT id FROM " . $table . " ORDER BY id DESC LIMIT 0,1");
    $row = $rs_new[0];

    return (int)$row->id;
}


function count_total_db($table, $str)
{
    global $wpdb;
    $row_count = $wpdb->get_results("SELECT count(id) AS total FROM  " . $table . " " . $str);
    $recordcount = (int)$row_count[0]->total;

    return $recordcount;
}
function count_total_db_edit($select,$table, $str)
{
    global $wpdb;
    $row_count = $wpdb->get_results("SELECT count(".$select.") AS total FROM  " . $table . " " . $str);
    $recordcount = (int)$row_count[0]->total;

    return $recordcount;
}
function count_total_db_air($table, $str)
{
    global $wpdb;
    $row_count = $wpdb->get_results("SELECT count(id) AS total FROM  " . $table . " " . $str);
    $recordcount = (int)$row_count[0]->total;

    return $recordcount;
}

function row_db($get, $table, $str)
{
    global $wpdb;
    $row_count = $wpdb->get_results("SELECT " . $get . " FROM  " . $table . " " . $str);

    return $row_count;
}

function show_admin_box_add_title($mdlconf, $module_path)
{
    echo '<h1>Thêm ' . $mdlconf['title'] . ' mới
			<a class="page-title-action" href="' . $module_path . '&sub=add">Thêm ' . $mdlconf['title'] . ' mới</a>
		  </h1>';
}


function show_admin_box_edit_title($mdlconf, $module_path)
{
    echo '<h1>Sửa ' . $mdlconf['title'] . '
		 </h1>';
}


function show_payment_cart($id)
{
    global $wpdb;
    $text = "";

    if ($id == 0) {
        $id = 3;
    }

    $rs = $wpdb->get_results("SELECT * FROM table_payment_cart where id=$id");
    foreach ($rs as $row) {
        $text .= '<span style="color:' . $row->color . ';">' . $row->title . '</span>';
    }

    return $text;
}

function danh_sach_huong_bao_hiem($id, $path_gcn)
{
    global $wpdb;

    if (trim($path_gcn) == "giaychungnhan/dulichtrongnuoc/chungnhan_theods.php") {
        $url = "../giaychungnhan/dulichtrongnuoc/danhsachnguoihuongbaohiem.php?id=" . $id;
    }

    if (trim($path_gcn) == "giaychungnhan/dulichquocte/chungnhan_theods.php") {
        $url = "../giaychungnhan/dulichquocte/danhsachnguoihuongbaohiem.php?id=" . $id;
    }

    if (trim($path_gcn) == "giaychungnhan/tainan2424/chungnhan_theods.php") {
        $url = "../giaychungnhan/tainan2424/danhsachnguoihuongbaohiem.php?id=" . $id;
    }

    if (isset($url)) {
        $url = "<a href=" . $url . " target=\"blank\">Danh sách hưởng BH</a>";
    }

    return $url;
}

function get_list_order($pagesize, $paged, $stt)
{
    return $pagesize * ($paged - 1) + $stt;
}

function curency_vn($s)
{
    return number_format($s, 0, "", ".");
}


function get_option_db($get1, $get2, $table, $str)
{
    global $wpdb;
    $rows = $wpdb->get_results("SELECT " . $get1 . ", " . $get2 . " FROM  " . $table . " " . $str);
    $str = array(array('', '  '));
    foreach ($rows as $row) {
        $str[] = array($row->$get1, $row->$get2);
    }

    return $str;
}

function show_select_option($active, $name, $table, $arr, $str, $chon = '---Chọn---')
{
    global $wpdb;
    $rows = $wpdb->get_results("SELECT " . $arr[0] . ", " . $arr[1] . " FROM  " . $table . " " . $str);
    $return = '<select name="' . $name . '"><option value="">' . $chon . '</option>';
    foreach ($rows as $row) {
        $sel = '';
        if ($active == $row->$arr[0]) {
            $sel = ' selected=""';
        }
        $return .= '<option value="' . $row->$arr[0] . '"' . $sel . '>' . $row->$arr[1] . '</option>';
    }

    return $return . '</select>';
}
add_action('init', 'init_theme_method');

function init_theme_method()
{
    add_thickbox();
}

?>
