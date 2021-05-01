<?php

class kmi_user extends _class{
	public static $table = 'abs-user';

	protected static $database = 'absen2020';

	public static function _list(){
		$list = array(
			'ID',
			'name',
			'no_induk',
			'divisi',
			'status'
		);

		return $list;
	}

	public static function blueprint(){
		$args = array(
			'type'		=> 'employee',
			'table'		=> self::$table,
			'detail'	=> array(
				'divisi'	=> array(
					'key'		=> 'ID',
					'table'		=> 'abs-module',
					'column'	=> array('meta_value','meta_note')
				),
				'picture'	=> array(
					'key'		=> 'ID',
					'table'		=> 'abs-post',
					'column'	=> array('notes')
				)
			),
		);

		return $args;
	}

	public static function check_login($user='',$pass=''){
		$conn = conn::connect();
		$args = array('`abs-user`.ID','`abs-user`.name','`abs-module`.meta_note AS dept');

		$user = $conn->real_escape_string($user);
		$pass = $conn->real_escape_string($pass);

		$inner = "LEFT JOIN `abs-module` ON `abs-user`.divisi = `abs-module`.ID ";
		$where = $inner."WHERE `abs-user`.username='$user' AND `abs-user`.password='$pass' AND `abs-user`.status IN ('1','2','3','4')";

		$data = parent::_get_data($where,$args);
		$check = array_filter($data);
		if(empty($check)){
			return $data;
		}

		//Check module -> departement
		$return = array();
		$module = sobad_module::get_all(array('meta_name','detail'),"AND detail!=''");
		foreach ($module as $key => $val) {
			$detail = unserialize($val['detail']);
			$detail = $detail['access'];

			if(in_array($data[0]['ID'],$detail)){
				$return = $data;
				$return[0]['dept'] = $val['meta_name'];

				return $return;
				break;
			}
		}

		die(_error::_alert_db('Anda tidak punya Akses !!!'));
	}

	public static function get_sales($args=array(),$limit=''){
		$where = "WHERE divisi='8' $limit";
		return parent::_get_data($where,$args);
	}
}