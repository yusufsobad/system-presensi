<?php

class sochick_account extends _class{

	public static $table = 'soc-account';

	public static function blueprint(){
		$args = array(
			'type'		=> 'account',
			'table'		=> self::$table,
		);

		return $args;
	}

	public function get_payments($args=array(),$limit=''){
		$where = "AND locked!='1' ".$limit;
		return self::get_all($args,$where);
	}
}