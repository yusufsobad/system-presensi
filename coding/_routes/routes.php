<?php

(!defined('DEFPATH'))?exit:'';

// registry page
$args = array();
$args['login'] = array(
	'home'	=> false,
	'view'	=> 'Login.login',
	'page'	=> 'login_system'
);

$args['absen'] = array(
	'page'	=> 'absen_sasi',
	'theme'	=> 'absen',
	'home'	=> false
);

$args['hrd'] = array(
	'page'		=> 'hrd_sasi',
	'home'		=> false
);

$args['dashboard'] = array(
	'home'	=> true,
	'view'	=> '',
	'page'	=> 'dashboard'
);

reg_hook('reg_page',$args);