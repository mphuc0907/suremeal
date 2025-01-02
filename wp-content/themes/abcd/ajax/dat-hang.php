<?php

include '../../../../wp-config.php';
session_start();
global $wpdb;
$id = no_sql_injection(xss($_POST['id']));


$ip_user = (!empty($_SESSION['ip_user'])) ? $_SESSION['ip_user'] : 0;


$soluong_1 = no_sql_injection(xss($_POST['soluong']));
