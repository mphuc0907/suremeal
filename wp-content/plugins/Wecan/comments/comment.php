<?php
global $wpdb;
require_once __DIR__ .'/../includes/function.php';

$myrows = $wpdb->get_results( "SELECT * FROM wp_review" );


$module_path = 'admin.php?page=comment_list';
$sub = "";
if (isset($_GET['sub'])) {
    $sub = trim($_GET['sub']);
}


$module_short_url = str_replace('admin.php?page=','', $module_path);

$mess = '';
$mdlconf = array('title'=>'Comment management');


if($sub==''){
    include_once __DIR__ .'/list.php';

}

?>


