<?php

(!defined('DEFPATH'))?exit:'';

// registry page
$args = array();
$args['dashboard'] = array(
	'home'	=> true,
	'view'	=> '',
	'page'	=> 'dashboard'
);

reg_hook('reg_page',$args);