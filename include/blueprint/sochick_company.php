<?php

class sochick_company extends _class{

	public static $table = 'soc-company';

	public static $tbl_meta = 'soc-company-meta';

	protected static $group = " GROUP BY `soc-company-meta`.meta_id";

	protected static $list_meta = '';

	public static function set_listmeta(){
		$type = parent::$_type;

		switch ($type) {
			case 'part':
				self::$list_meta = array(
					'_warning_stock',
					'_note',
					'_sync_item',
					'_sync_status',
					'_finishing',
					'_unit',
					'_image',
					'_material'
				);
				break;

			case 'stdPart':
				self::$list_meta = array(
					'_warning_stock',
					'_note',
					'_sync_item',
					'_unit'
				);
				break;

			case 'assembly':
				self::$list_meta = array(
					'_warning_stock',
					'_note',
					'_detail',
					'_unit',
					'_image'
				);
				break;
			
			default:
				self::$list_meta = array(
					'_warning_stock',
					'_shape',
					'_dimension',
					'_note',
					'_detail',
					'_unit'
				);
				break;
		}
	}

	public static function blueprint($key='item'){
		self::set_listmeta();

		$args = array(
			'type'		=> $key,
			'table'		=> self::$table,
			'detail'	=> array(
				'category'	=> array(
					'key'		=> 'ID',
					'table'		=> 'sdn-meta',
					'column'	=> array('meta_value','meta_note')
				),
			),
			'meta'		=> array(
				'key'		=> 'meta_id',
				'table'		=> self::$tbl_meta,
			)
		);

		return $args;
	}

	private function list_company(){
		$list = array(
			'ID',
			'name',
			'no_hp',
			'type',
			'inserted',
		);
		
		return $list;
	}
	
	private function list_company_meta(){
		$list = array(
			'ID',
			'meta_id',
			'meta_key',
			'meta_value'
		);
		
		return $list;
	}
	
	public function get_company_meta($id=0,$limit=''){
		$args = $this->list_company_meta();
		
		$where = "WHERE meta_id='$id' $limit";
		return $this->_get_company_meta($where,$args);
	}
	
	public function get_company($id,$args=array()){
		$check = array_filter($args);
		if(empty($check)){
			$args = $this->list_company();
		}
		
		$where = "WHERE ID='$id'";
		return $this->_get_companies($where,$args);
	}
	
	public function get_customers($args=array(),$limit=''){
		$check = array_filter($args);
		if(empty($check)){
			$args = $this->list_customer();
		}
		
		$where = "WHERE type='0' $limit";
		return $this->_get_companies($where,$args);
	}
	
	public function get_suppliers($args=array(),$limit=''){
		$check = array_filter($args);
		if(empty($check)){
			$args = $this->list_customer();
		}
		
		$where = "WHERE type='1' $limit";
		return $this->_get_companies($where,$args);
	}
	
	private function _get_companies($where='',$args=array()){
		$company = array();
		
		$q = $this->_select_table($where,'soc-company',$args);
		if($q!==0){
			while($r=$q->fetch_assoc()){
				$item = array();
				foreach($r as $key => $val){
					$item[$key] = $val;
				}
				
				$company[] = $item;
			}
		}
		
		return $company;
	}
	
	private function _get_company_meta($where='',$args=array()){
		$company = array();
		
		$q = $this->_select_table($where,'soc-company-meta',$args);
		if($q!==0){
			while($r=$q->fetch_assoc()){
				$company[$r['meta_key']] = $r['meta_value'];
			}
		}
		
		return $company;
	}
}