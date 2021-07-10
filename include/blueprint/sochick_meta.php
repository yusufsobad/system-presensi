<?php

class sochick_meta extends _class{

	public static $table = 'soc-meta'; 

	public static function blueprint(){
		$args = array(
			'type'		=> 'meta',
			'table'		=> self::$table
		);

		return $args;
	}

	private static function _check_type($type=''){
		if(!empty($type)){
			$args = array(
				'category',
				'category_bahan',
				'type_order',
				'expense'
			);

			if(in_array($type, $args)){
				return true;
			}
		}

		return false;
	}
	
	public static function _gets($type='',$args=array(),$limit=''){
		if(self::_check_type($type)){
			$where = "WHERE meta_key='$type' $limit";
			return self::_check_join($where,$args,$type);
		}

		return array();
	}
}