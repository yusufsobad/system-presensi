<?php

class sochick_cashflow extends _class{

	public static $table = 'soc-cashflow';

	public static function blueprint(){
		$args = array(
			'type'		=> 'cashflow',
			'table'		=> self::$table,
			'detail'	=> array(
				'account'	=> array(
					'key'		=> 'ID',
					'table'		=> 'soc-account',
					'column'	=> array('name','balance')
				),
			),
		);

		return $args;
	}

	public function get_cashflows($date='',$type=''){
		
		$whr = '';
		if(empty($date) && empty($type)){
			$whr = "1=1";
		}
		
		if(!empty($date)){
			$whr .= " date_log='$date'";
		}
		
		if(!empty($date) && !empty($type)){
			$whr .= " AND ";
		}
		
		if(!empty($type)){
			$whr .= " type='$type'";
		}
		
		return self::_check_join($whr,$args);
	}
	
	public static function get_add_order($date=''){
		return self::_get_cashflows($date,'add_order');
	}
	
	public static function get_update_order($date=''){
		return self::_get_cashflows($date,'update_order');
	}
	
	public static function get_cancel_order($date=''){
		return self::_get_cashflows($date,'cancel_order');
	}
	
	public static function get_transfer_dana($date=''){
		return self::_get_cashflows($date,'transfer_dana');
	}
	
	public static function get_refrain_dana($date=''){
		return self::_get_cashflows($date,'refrain_dana');
	}
	
	public static function get_salary($date=''){
		return self::_get_cashflows($date,'salary');
	}
	
	public static function get_purchase($date=''){
		return self::_get_cashflows($date,'purchase');
	}
	
// --------------------------------------------------------------
// ------------- Insert Cashflow --------------------------------
// --------------------------------------------------------------	
	public static function set_addOrder($id=0,$args=array()){
		$data = array(
			'reff'		=> $id,
			'type'		=> 'add_order',
			'status'	=> 1
		);
		
		$args = array_merge($args,$data);
		return self::_set_cashflow($args);
	}
	
	public static function set_komisiOrder($id=0,$args=array()){
		$data = array(
			'reff'		=> $id,
			'type'		=> 'komisi_order',
			'status'	=> 1
		);
		
		$args = array_merge($args,$data);
		return self::_set_cashflow($args);
	}
	
	public static function set_cancelOrder($id=0,$args=array()){
		$data = array(
			'reff'		=> $id,
			'type'		=> 'cancel_order',
			'status'	=> 0
		);
		
		$args = array_merge($args,$data);
		return self::_set_cashflow($args);
	}
	
	public static function set_transfer($id=0,$args=array(),$status=1){
		$data = array(
			'reff'		=> $id,
			'type'		=> 'transfer_dana',
			'status'	=> $status
		);
		
		$args = array_merge($args,$data);
		return self::_set_cashflow($args);
	}
	
	public static function set_refrain($id=0,$args=array(),$status=1){
		$data = array(
			'reff'		=> $id,
			'type'		=> 'refrain_dana',
			'status'	=> $status
		);
		
		$args = array_merge($args,$data);
		return self::_set_cashflow($args);
	}

	public static function set_cancel_dana($id=0,$args=array()){
		$data = array(
			'reff'		=> $id,
			'type'		=> 'cancel_dana',
			'status'	=> 0
		);
		
		$args = array_merge($args,$data);
		return self::_set_cashflow($args);
	}
	
	public static function set_salary($id=0,$args=array(),$status=1){
		$data = array(
			'reff'		=> $id,
			'type'		=> 'salary',
			'status'	=> $status
		);
		
		$args = array_merge($args,$data);
		return self::_set_cashflow($args);
	}
	
	public static function set_purchase($id=0,$args=array(),$status=1){
		$args = $this->check_array_set($args);
		$data = array(
			'reff'		=> $id,
			'type'		=> 'purchase',
			'status'	=> $status
		);
		
		$args = array_merge($args,$data);
		return self::_set_cashflow($args);
	}

	public static function set_kasIn($id=0,$args=array()){
		$data = array(
			'reff'		=> $id,
			'type'		=> 'kasIn',
			'status'	=> 1
		);
		
		$args = array_merge($args,$data);
		return self::_set_cashflow($args);
	}	
	
	public static function set_hutang($id=0,$args=array()){
		$data = array(
			'reff'		=> $id,
			'type'		=> 'hutang',
			'status'	=> 1
		);
		
		$args = array_merge($args,$data);
		return self::_set_cashflow($args);
	}
	
	public static function set_piutang($id=0,$args=array()){
		$data = array(
			'reff'		=> $id,
			'type'		=> 'piutang',
			'status'	=> 1
		);
		
		$args = array_merge($args,$data);
		return self::_set_cashflow($args);
	}
	
	public static function set_balance($id=0,$args=array()){
		$data = array(
			'reff'		=> $id,
			'type'		=> 'balance',
			'status'	=> 2
		);
		
		$args = array_merge($args,$data);
		return self::_set_cashflow($args);
	}
	
	private static function _set_cashflow($args=array()){
		$args['note'] = isset($args['note'])?$args['note']:'Error track record';
		$q = sobad_db::_insert_table(self::$table,$args);
		return $q;
	}
}