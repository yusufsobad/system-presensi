<?php

function reg_hormenu(){
	$args = array();
	$args['view-pos'] = array(
		'status'	=> 'active',
		'icon'		=> 'icon-home',
		'label'		=> 'Order',
		'func'		=> 'dash_kasir',
		'child'		=> null
	);
	
	$args['list_order'] = array(
		'status'	=> '',
		'icon'		=> 'fa fa-dashboard',
		'label'		=> 'List Order',
		'func'		=> 'order_kasir',
		'child'		=> null
	);

	$args['stock'] = array(
		'status'	=> '',
		'icon'		=> 'fa fa-dashboard',
		'label'		=> 'Stock',
		'func'		=> 'stock_kasir', 
		'child'		=> null
	);

	$args['purchase'] = array(
		'status'	=> '',
		'icon'		=> 'fa fa-money',
		'label'		=> 'Purcahse',
		'func'		=> 'type_purchase', // function di Admin Purchase.php
		'child'		=> null
	);

	$args['absensi'] = array(
		'status'	=> '', 
		'icon'		=> 'fa fa-dashboard',
		'label'		=> 'Absensi',
		'func'		=> 'absensi_kasir',
		'child'		=> null
	);

	$args['about'] = array(
		'status'	=> '',
		'icon'		=> 'fa fa-dashboard',
		'label'		=> 'About',
		'func'		=> '',
		'child'		=> null
	);
	
	return $args;
}