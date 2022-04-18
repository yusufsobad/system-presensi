<?php
define('AUTHPATH',$_SERVER['SERVER_NAME']);

require "include/config/hostname.php";

// Check Hostname yang mengakses
new hostname();

require_once 'include/url_asset.php';
require_once 'include/class_db.php';
require_once 'include/class_db/sync_db.php';

// Create table temporary
$temporary = unserialize(_temp_table);
foreach ($temporary as $key => $val) {
	if(is_callable(array($val,'temporary'))){
		$args = $val::temporary();
		foreach ($args as $_key => $_val) {
			sobad_db::_create_temporary_table($_val);
		}
	}
}

// Create table list
$schema = unserialize(SCHEMA);

$status = sobad_db::_create_file_list($schema);
if($status==false){
	$status = sobad_db::_update_file_list($schema);
}

if($status){
	header('Location: '.SITE.'://'.HOSTNAME.'/'.URL.'/');
}else{
	echo 'Gagal Update !!!';
}