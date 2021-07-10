<?php
require 'Kasir/include.php';

class dash_kasir extends _page{
	protected static $object = 'dash_kasir';

	// ----------------------------------------------------------
	// Layout category  ------------------------------------------
	// ----------------------------------------------------------

	private static function head_title(){
		$args = array(
			'title'	=> 'Order <small>order</small>',
			'link'	=> array(
				0	=> array(
					'func'	=> 'dash_kasir',
					'label'	=> 'dashboard'
				)
			),
			'date'	=> false
		);
		
		return $args;
	}

	protected static function layout(){
		$label = array();
		$data = array();
		
		$data[] = array(
			'style'		=> array(),
			'script'	=> array(),
			'func'		=> 'view_pos',
			'object'	=> self::$object,
			'data'		=> ''
		);
		
		$title = self::head_title();
		
		ob_start();
		metronic_layout::_head_content($title);
		metronic_layout::_content('_panel',$data);
		return ob_get_clean();
	}

	public static function view_pos(){

	}
}