<?php

class sobad_region{

	private static $database = 'sasi_region';

	private static $table = '';
	
	private static function _list(){
		$list = sobad_table::_get_list(self::$table);
		$list[] = 'ID';

		return $list;
	}

	public static function get_country($id=0){
		self::$table = 'sasi-country';
		$args = self::_list();

		$where = "WHERE ID='$id' $limit";
		return self::_get_wilayah($where,$args);
	}

	public static function get_countries($limit="1=1"){
		self::$table = 'sasi-country';
		$args = self::_list();

		$where = "WHERE $limit";
		return self::_get_wilayah($where,$args);
	}

	// ----------------------------------------------------
	// Province -------------------------------------------
	// ----------------------------------------------------

	public static function get_province($id=0){
		self::$table = 'sasi-province';
		$args = self::_list();
		
		$where = "WHERE ID='$id'";
		return self::_get_wilayah($where,$args);
	}
	
	public static function get_province_by($id=0){
		self::$table = 'sasi-province';
		$args = self::_list();
		
		$where = "WHERE id_country='$id'";
		return self::_get_wilayah($where,$args);
	}

	public static function get_provinces($limit='1=1'){
		self::$table = 'sasi-province';
		$args = self::_list();
		
		$where = "WHERE $limit";
		return self::_get_wilayah($where,$args);
	}

	// ----------------------------------------------------
	// City -----------------------------------------------
	// ----------------------------------------------------
	
	public static function get_city($id=0){
		self::$table = 'sasi-city';
		$args = self::_list();
		
		$where = "WHERE ID='$id'";
		return self::_get_wilayah($where,$args);
	}

	public static function get_city_by($id=0){
		self::$table = 'sasi-city';
		$args = self::_list();
		
		$where = "WHERE id_province='$id'";
		return self::_get_wilayah($where,$args);
	}

	public static function get_cities($limit='1=1'){
		self::$table = 'sasi-city';
		$args = self::_list();
		
		$where = "WHERE $limit";
		return self::_get_wilayah($where,$args);
	}

	// ----------------------------------------------------
	// Subdistrict ----------------------------------------
	// ----------------------------------------------------
	
	public static function get_subdistrict($id=0){
		self::$table = 'sasi-subdistrict';
		$args = self::_list();
		
		$where = "WHERE ID='$id'";
		return self::_get_wilayah($where,$args);
	}

	public static function get_subdistrict_by($id=0){
		self::$table = 'sasi-subdistrict';
		$args = self::_list();
		
		$where = "WHERE id_city='$id'";
		return self::_get_wilayah($where,$args);
	}

	public static function get_subdistricts($limit=''){
		self::$table = 'sasi-subdistrict';
		$args = self::_list();
		
		$where = "WHERE $limit";
		return self::_get_wilayah($where,$args);
	}

	// ----------------------------------------------------
	// Village --------------------------------------------
	// ----------------------------------------------------
	
	public static function get_village($id=0){
		self::$table = 'sasi-village';
		$args = self::_list();
		
		$where = "WHERE ID='$id'";
		return self::_get_wilayah($where,$args);
	}

	public static function get_village_by($id=0){
		self::$table = 'sasi-village';
		$args = self::_list();
		
		$where = "WHERE id_subdistrict='$id'";
		return self::_get_wilayah($where,$args);
	}

	public static function get_villages($limit=''){
		self::$table = 'sasi-village';
		$args = self::_list();
		
		$where = "WHERE $limit";
		return self::_get_wilayah($where,$args);
	}

	public static function get_postcodes($id=0){
		self::$table = 'sasi-village';
		$args = array('DISTINCT postal_code');
		
		$where = "WHERE id_subdistrict='$id'";
		return self::_get_wilayah($where,$args);
	}

	// ----------------------------------------------------
	// Conversi Address -----------------------------------
	// ----------------------------------------------------

	public static function _conv_address($address='',$args=array()){
		$data = array();
		$keys = array(
			'village'		=> 'village',
			'subdistrict'	=> 'subdistrict',
			'city'			=> 'city',
			'province'		=> 'province'
		);

		$add = array();
		$add[] = $address;

		$data['address'] = $address;
		$data['postcode'] = isset($args['postcode'])?$args['postcode']:'';
		foreach ($keys as $ky => $vl) {
			if(isset($args[$ky]) && !empty($args[$ky])){
				$func = 'get_' . $ky;
				$lokasi = self::{$func}($args[$ky]);
				$_lokasi = $lokasi[0][$vl];

				if($ky=='city'){
					$_city = isset($lokasi[0][$vl]) ? $lokasi[0][$vl] : '';

					$_lokasi = self::_conv_type_city($lokasi[0]['type']);
					$_lokasi .= ' '.$_city;
				}

				$data[$ky] = $_lokasi;
				$add[] = $data[$ky];
			}else{
				$data[$ky] = '';
			}
		}

		$_address = implode(', ',$add);
		if(isset($args['postcode']) && !empty($args['postcode'])){
			$_address .= ' - '. $args['postcode'];
		}

		$data['result'] = $_address;
		return $data;
	}

	public static function _conv_type_city($type=0){
		$_lokasi = '';
		if($type==1){
			$_lokasi = 'kab.';
		}else if($_lokasi==2){
			$_lokasi = 'kota';
		}

		return $_lokasi;
	}
	
	private static function _get_wilayah($where='',$args=array()){
		global $DB_NAME;
		$wilayah = array();

		$_database = $DB_NAME;
		$DB_NAME = self::$database;
		
		$db = new sobad_db();
		$q = $db->_select_table($where,self::$table,$args);
		if($q!==0){
			while($r=$q->fetch_assoc()){
//				$item = array();
//				foreach($r as $key => $val){
//					$item[$key] = $val;
//				}
				
				$wilayah[] = $r;//$item;
			}
		}
		
		$DB_NAME = $_database;
		return $wilayah;
	}
	
}