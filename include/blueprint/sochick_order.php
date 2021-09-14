<?php

class sochick_order extends _class{ 

	public static $table = 'soc-order';

	public static $tbl_join = 'soc-transaksi';

	protected static $join = "joined.reff ";

	public static function blueprint(){
		$args = array(
			'type'		=> 'order',
			'table'		=> self::$table,
			'detail'	=> array(
				'user'		=> array(
					'key'		=> 'ID',
					'table'		=> 'soc-user',
					'column'	=> array('name','no_hp')
				),
				'contact'	=> array(
					'key'		=> 'ID',
					'table'		=> 'soc-company',
					'column'	=> array('name','no_hp')
				),
				'type'		=> array(
					'key'		=> 'ID',
					'table'		=> 'soc-meta',
					'column'	=> array('meta_value')
				),
				'payment'	=> array(
					'key'		=> 'ID',
					'table'		=> 'soc-account',
					'column'	=> array('name','balance')
				),
			),
			'joined'	=> array(
				'key'		=> 'reff',
				'table'		=> self::$tbl_join,
			)
		);

		return $args;
	}

	public static function get_reconsiles($args=array(),$limit=''){
		$where = "WHERE var='reconsile' ".$limit;
		return self::_check_join($where,$args);
	}

	public static function get_endDays($args=array(),$limit=''){
		$where = "WHERE var='end_day' ".$limit;
		return self::_check_join($where,$args);
	}	

	public static function get_paids($args=array(),$limit=''){
		$where = "WHERE var='paid' ".$limit;
		return self::_check_join($where,$args);
	}

	public static function get_gratuities($args=array(),$limit=''){
		$where = "WHERE var='komisi' ".$limit;
		return self::_check_join($where,$args);
	}

	public static function get_purchases($args=array(),$limit=''){
		$where = "WHERE var='purchase' ".$limit;
		return self::_check_join($where,$args);
	}

	public static function get_debits($args=array(),$limit=''){
		$where = "WHERE var='debit' ".$limit;
		return self::_check_join($where,$args);
	}	

	public static function get_pays($args=array(),$limit=''){
		$where = "WHERE var='pay' ".$limit;
		return self::_check_join($where,$args);
	}

	public static function get_salaries($args=array(),$limit=''){
		$where = "WHERE var='salary' ".$limit;
		return self::_check_join($where,$args);
	}

	public static function get_pay_salary($title='',$args=array(),$limit=''){
		$where = "WHERE title='$title' AND var='pay_salary' ".$limit;
		return self::_check_join($where,$args);
	}

	public static function sum_salaries_month($year=0,$month=0,$limit=''){
		$args = array('SUM(transaksi.price) AS sum');

		$join = "LEFT JOIN `soc-transaksi` AS transaksi ON `soc-order`.ID = transaksi.reff ";
		$where = $join."WHERE `soc-order`.var='salary' AND YEAR(`soc-order`.order_date)='$year' AND MONTH(`soc-order`.order_date)='$month' $limit";

		return $this->_get_order($where,$args);
	}	

	public static function sum_pay_salaries_month($year=0,$month=0,$limit=''){
		$args = array('SUM(transaksi.price) AS sum');

		$join = "LEFT JOIN `soc-order` AS pay_salary ON `soc-order`.ID = pay_salary.title ";
		$join .= "LEFT JOIN `soc-transaksi` AS transaksi ON pay_salary.ID = transaksi.reff ";
		$where = $join."WHERE `soc-order`.var='salary' AND YEAR(`soc-order`.order_date)='$year' AND MONTH(`soc-order`.order_date)='$month' $limit";

		return $this->_get_order($where,$args);
	}

	public static function get_kasOuts($args=array(),$limit=''){
		$check = array_filter($args);
		if(empty($check)){
			$args = $this->list_order();
		}
		
		$args[] = 'paid';

		$where = "WHERE var IN ('pay','pay_salary','komisi','kasOut') ".$limit;
		return $this->_get_join_order($args,$where);
	}

	public static function count_kasOut($limit=''){		
		$args = array('COUNT(ID) AS count');
		$where = "WHERE var IN ('pay','pay_salary','komisi','kasOut') ".$limit;
		$data = $this->_get_order($where,$args);
		return $data[0]['count'];
	}

	public static function get_kasIns($args=array(),$limit=''){
		$check = array_filter($args);
		if(empty($check)){
			$args = $this->list_order();
		}
		
		$args[] = 'paid';

		$where = "WHERE var IN ('paid','kasIn','debit') ".$limit;
		return $this->_get_join_order($args,$where);
	}

	public static function count_kasIn($limit=''){		
		$args = array('COUNT(ID) AS count');
		$where = "WHERE var IN ('paid','kasIn','debit') ".$limit;
		$data = $this->_get_order($where,$args);
		return $data[0]['count'];
	}

	public static function get_transfers($args=array(),$limit=''){
		$check = array_filter($args);
		if(empty($check)){
			$args = $this->list_order();
		}
		
		$args[] = 'paid';

		$where = "WHERE var='kasOut' ".$limit;
		return $this->_get_join_order($args,$where);
	}

	public static function count_transfer($limit=''){		
		$args = array('COUNT(ID) AS count');
		$where = "WHERE var='kasOut' ".$limit;
		$data = $this->_get_order($where,$args);
		return $data[0]['count'];
	}

	public static function get_logAccount($id=0,$args=array(),$limit=''){
		$check = array_filter($args);
		if(empty($check)){
			$args = $this->list_order();
		}
		
		$args[] = 'paid';

		$where = "WHERE var IN ('paid','pay','kasOut','kasIn') AND payment='$id'".$limit;
		return $this->_get_join_order($args,$where);
	}

	public static function get_addStock($args=array(),$limit=''){
		$check = array_filter($args);
		if(empty($check)){
			$args = $this->list_order();
		}
		
		$args[] = 'paid';

		$where = "WHERE var='add_stock'".$limit;
		return $this->_get_join_order($args,$where);
	}

	public static function get_subtractStock($args=array(),$limit=''){
		$check = array_filter($args);
		if(empty($check)){
			$args = $this->list_order();
		}
		
		$args[] = 'paid';

		$where = "WHERE var='subtract_stock'".$limit;
		return $this->_get_join_order($args,$where);
	}

	public static function get_returStock($args=array(),$limit=''){
		$check = array_filter($args);
		if(empty($check)){
			$args = $this->list_order();
		}
		
		$args[] = 'paid';

		$where = "WHERE var='retur_stock'".$limit;
		return $this->_get_join_order($args,$where);
	}

	public static function get_eatingStock($args=array(),$limit=''){
		$check = array_filter($args);
		if(empty($check)){
			$args = $this->list_order();
		}
		
		$args[] = 'paid';

		$where = "WHERE var='eating_stock'".$limit;
		return $this->_get_join_order($args,$where);
	}	

// --------------------------------------------------------------
// ------------- Function Sum -----------------------------------
// --------------------------------------------------------------	

	public static function sum_order_date($year='',$month='',$day='',$limit=''){
		return $this->_sum_by_date('order',$year,$month,$day,$limit);
	}

	public static function sum_purchase_date($year='',$month='',$day='',$limit=''){
		return $this->_sum_by_date('purchase',$year,$month,$day,$limit);
	}

	public static function sum_paid_date($year='',$month='',$day='',$limit=''){
		return $this->_get_by_date("'paid'",$year,$month,$day,$limit);
	}

	public static function sum_pay_date($year='',$month='',$day='',$limit=''){
		return $this->_get_by_date("'pay','salary'",$year,$month,$day,$limit);
	}

	public static function sum_kredit($user='',$limit=''){
		$user = $user==''?'':"AND contact='$user'";
		$q = $this->get_paids(array('ID','title'),"AND status='0' $user $limit");
		return $this->_debt_convTo_sum($q);
	}

	public static function sum_debit($user='',$limit=''){
		$user = $user==''?'':"AND contact='$user'";
		$q = $this->get_pays(array('ID','title'),"AND status='0' $user $limit");
		return $this->_debt_convTo_sum($q);
	}

	public static function sum_modal($limit=''){
		$q = $this->get_debits(array('ID','title','paid'),"AND type='1' $limit");
		return $this->_modal_convTo_sum($q);
	}

	private function _sum_by_date($type,$year,$month,$day,$limit=''){
		$year = empty($year)?date('Y'):$year;
		$month = empty($month)?'':"AND MONTH(`soc-order`.updated)='$month'";
		$day = empty($day)?'':"AND DAY(`soc-order`.updated)='$day'";

		if($type!='purchase'){
			$args = array('SUM((price*qty)-(discount*qty)) AS sum');
		}else{
			$args = array('SUM((price-discount)) AS sum');
		} 

		$inner = "LEFT JOIN `soc-transaksi` ON `soc-order`.ID = `soc-transaksi`.reff ";
		$where = $inner."WHERE `soc-order`.var='$type' AND `soc-order`.status!='2' AND YEAR(`soc-order`.updated)='$year' $month $day $limit";

		return $this->_get_order($where,$args);
	}

	private function _get_by_date($type,$year,$month,$day,$limit=''){
		$year = empty($year)?date('Y'):$year;
		$month = empty($month)?'':"AND MONTH(`soc-order`.updated)='$month'";
		$day = empty($day)?'':"AND DAY(`soc-order`.updated)='$day'";

		$args = array('`soc-transaksi`.price,`soc-transaksi`.note');
		$inner = "LEFT JOIN `soc-transaksi` ON `soc-order`.ID = `soc-transaksi`.reff ";
		$where = $inner."WHERE `soc-order`.var IN ($type) AND `soc-order`.status!='2' AND YEAR(`soc-order`.updated)='$year' $month $day $limit";

		$q = $this->_get_order($where,$args);
		return $this->_pay_convTo_sum($q);
	}

	private function _pay_convTo_sum($args=array()){
		$check = array_filter($args);
		if(empty($check)){
			return array(
				0 => array(
					'sum'	=> 0
				)
			);
		}

		$total = 0;
		foreach ($args as $ky => $val) {
			$note = $val['note'];
			intval($note);

			$price = $val['price'];
			if($note>=0){
				$price -= $note;
			}

			$total += $price;
		}

		return array(
			0	=> array(
				'sum'	=> $total
			)
		);
	}

	private function _debt_convTo_sum($args=array()){
		$check = array_filter($args);
		if(empty($check)){
			return array(
				0 => array(
					'sum'	=> 0
				)
			);
		}

		$debt = array();
		foreach ($args as $ky => $val) {
			$idx = $val['title'];
			$debt[$idx] = $val['paid_short'];
		}

		$total = 0;
		foreach ($debt as $ky => $val) {
			$total += -1* intval($val);
		}

		return array(
			0	=> array(
				'sum'	=> $total
			)
		);
	}

	private function _modal_convTo_sum($args=array()){
		$check = array_filter($args);
		if(empty($check)){
			return array(
				0 => array(
					'sum'	=> 0
				)
			);
		}

		$total = 0;
		foreach ($args as $ky => $val) {
			$total += $val['paid_cash'];
		}

		return array(
			0	=> array(
				'sum'	=> $total
			)
		);
	}

// --------------------------------------------------------------
// ------------- Join Order -------------------------------------
// --------------------------------------------------------------

	private function _get_join_order($args=array(),$where=''){
		$inner = '';
		foreach($args as $key => $val){
			$args[$key] = "`soc-order`.$val";
		}

		if(in_array("`soc-order`.paid",$args)){
			foreach($args as $key => $val){
				if($val=="`soc-order`.paid"){
					unset($args[$key]);
				}
			}

			array_push($args,"transaksi.barang AS paid_brng");
			array_push($args,"transaksi.qty AS paid_qty");
			array_push($args,"transaksi.price AS paid_cash");
			array_push($args,"transaksi.discount AS paid_disc");
			array_push($args,"transaksi.unit AS paid_unit");
			array_push($args,"transaksi.note AS paid_short");

			$inner .= "LEFT JOIN `soc-transaksi` AS transaksi ON `soc-order`.ID = transaksi.reff ";
		}

		if(in_array("`soc-order`.user",$args)){
			array_push($args,"user.name AS user_name");
			array_push($args,"user.no_hp AS user_phone");
			
			$inner .= "LEFT JOIN `soc-user` AS user ON `soc-order`.user = user.ID ";
		}

		if(in_array("`soc-order`.contact",$args)){
			array_push($args,"contact.name AS contact_name");
			array_push($args,"contact.no_hp AS contact_phone");
			
			$inner .= "LEFT JOIN `soc-company` AS contact ON `soc-order`.contact = contact.ID ";
		}

		if(in_array("`soc-order`.type",$args)){
			array_push($args,"type.meta_value AS type_order");
			
			$inner .= "LEFT JOIN `soc-meta` AS type ON `soc-order`.type = type.ID ";
		}

		if(in_array("`soc-order`.payment",$args)){
			array_push($args,"payment.name AS pay_name");
			array_push($args,"payment.balance AS pay_saldo");
			
			$inner .= "LEFT JOIN `soc-account` AS payment ON `soc-order`.payment = payment.ID ";
		}

		$where = $inner.$where;
		return $this->_get_order($where,$args);
	}

	private function _get_order($where='',$args=array()){
		$user = array();
		
		$q = $this->_select_table($where,'soc-order',$args);
		if($q!==0){
			while($r=$q->fetch_assoc()){
				$item = array();
				foreach($r as $key => $val){
					$item[$key] = $val;
				}
				
				$user[] = $item;
			}
		}
		
		return $user;
	}
}