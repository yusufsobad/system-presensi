<?php
(!defined('AUTHPATH'))?exit:'';

class sobad_table{

	public static function _get_table($func){
		$func = str_replace('-','_',$func);
				
		$obj = new self();
		if(is_callable(array($obj,$func))){
			$list = $obj::{$func}();
				return $list;
			}
		
		return false;
	}
		
	public static function _get_list($func=''){
		$list = array();
		$lists = self::_get_table($func);
		if($lists){
			foreach ($lists as $key => $val) {
				$list[] = $key;
			}
		}
		
		return $list;
	}
		

	private static function _list_table(){
		// Information data table
		
		$table = array(
				'sdn-about'		=> self::sdn_about(),
				'sdn-account'		=> self::sdn_account(),
				'sdn-cashflow'		=> self::sdn_cashflow(),
				'sdn-company'		=> self::sdn_company(),
				'sdn-company-meta'		=> self::sdn_company_meta(),
				'sdn-item'		=> self::sdn_item(),
				'sdn-item-detail'		=> self::sdn_item_detail(),
				'sdn-item-join'		=> self::sdn_item_join(),
				'sdn-item-meta'		=> self::sdn_item_meta(),
				'sdn-meta'		=> self::sdn_meta(),
				'sdn-module'		=> self::sdn_module(),
				'sdn-post'		=> self::sdn_post(),
				'sdn-post-meta'		=> self::sdn_post_meta(),
				'sdn-transaksi'		=> self::sdn_transaksi(),
				'tbl_wilayah'		=> self::tbl_wilayah(),
				'abs-user'		=> self::abs_user(),
		);
		
		return $table;
	}
		

		private static function sdn_about(){
			$list = array(
				'config_name'	=> '',
				'config_value'	=> '',
				'status'	=> 0,	
			);
			
			return $list;
		}

		private static function sdn_account(){
			$list = array(
				'name'	=> '',
				'no_rek'	=> '',
				'user'	=> 0,
				'balance'	=> 0,
				'bank'	=> 0,
				'address'	=> '',
				'updated'	=> date('Y-m-d H:i:s'),
				'trash'	=> 0,	
			);
			
			return $list;
		}

		private static function sdn_cashflow(){
			$list = array(
				'reff'	=> 0,
				'user'	=> 0,
				'type'	=> '',
				'payment'	=> 0,
				'saldo_awal'	=> 0,
				'saldo_akhir'	=> 0,
				'account'	=> 0,
				'status'	=> 0,
				'note'	=> '',
				'date_log'	=> date('Y-m-d H:i:s'),	
			);
			
			return $list;
		}

		private static function sdn_company(){
			$list = array(
				'name'	=> '',
				'phone_no'	=> '',
				'type'	=> 0,
				'inserted'	=> date('Y-m-d H:i:s'),
				'updated'	=> date('Y-m-d H:i:s'),
				'reff'	=> 0,	
			);
			
			return $list;
		}

		private static function sdn_company_meta(){
			$list = array(
				'meta_id'	=> 0,
				'meta_key'	=> '',
				'meta_value'	=> '',	
			);
			
			return $list;
		}

		private static function sdn_item(){
			$list = array(
				'name'	=> '',
				'product_code'	=> '',
				'picture'	=> '',
				'price'	=> 0,
				'category'	=> 0,
				'weight'	=> 0.00,
				'company'	=> 0,
				'type'	=> 0,
				'var'	=> 0,
				'stock'	=> 0.00,
				'inserted'	=> date('Y-m-d H:i:s'),
				'updated'	=> date('Y-m-d H:i:s'),
				'trash'	=> 0,	
			);
			
			return $list;
		}

		private static function sdn_item_detail(){
			$list = array(
				'item'	=> 0,
				'sku'	=> '',
				'off_date'	=> date('Y-m-d'),
				'notes'	=> '',
				'reff'	=> 0,
				'status'	=> 0,	
			);
			
			return $list;
		}

		private static function sdn_item_join(){
			$list = array(
				'item_id'	=> 0,
				'join_id'	=> 0,
				'status'	=> 0,	
			);
			
			return $list;
		}

		private static function sdn_item_meta(){
			$list = array(
				'meta_id'	=> 0,
				'meta_key'	=> '',
				'meta_value'	=> '',	
			);
			
			return $list;
		}

		private static function sdn_meta(){
			$list = array(
				'meta_key'	=> '',
				'meta_value'	=> '',
				'meta_note'	=> '',
				'inserted'	=> date('Y-m-d H:i:s'),
				'updated'	=> date('Y-m-d H:i:s'),
				'meta_reff'	=> 0,	
			);
			
			return $list;
		}

		private static function sdn_module(){
			$list = array(
				'name'	=> '',
				'meta_name'	=> '',
				'detail'	=> '',	
			);
			
			return $list;
		}

		private static function sdn_post(){
			$list = array(
				'title'	=> 0,
				'company'	=> 0,
				'contact'	=> 0,
				'type'	=> 0,
				'user'	=> 0,
				'payment'	=> 0,
				'post_date'	=> date('Y-m-d'),
				'status'	=> 0,
				'inserted'	=> date('Y-m-d H:i:s'),
				'updated'	=> date('Y-m-d H:i:s'),
				'var'	=> '',
				'notes'	=> '',
				'reff'	=> 0,
				'trash'	=> 0,	
			);
			
			return $list;
		}

		private static function sdn_post_meta(){
			$list = array(
				'meta_id'	=> 0,
				'meta_key'	=> '',
				'meta_value'	=> '',	
			);
			
			return $list;
		}

		private static function sdn_transaksi(){
			$list = array(
				'post'	=> 0,
				'barang'	=> 0,
				'qty'	=> 0.00,
				'unit'	=> '',
				'price'	=> 0.00,
				'discount'	=> 0,
				'note'	=> '',
				'keyword'	=> '',	
			);
			
			return $list;
		}

		private static function tbl_wilayah(){
			$list = array(
				'id_prov'	=> 0,
				'id_kab'	=> 0,
				'id_kec'	=> 0,
				'provinsi'	=> '',
				'kabupaten'	=> '',
				'kecamatan'	=> '',
				'kelurahan'	=> '',
				'tipe'	=> '',
				'kodepos'	=> 0,	
			);
			
			return $list;
		}

		private static function abs_user(){
			$list = array(
				'username'	=> '',
				'password'	=> '',
				'no_induk'	=> 0,
				'divisi'	=> 0,
				'phone_no'	=> '',
				'name'	=> '',
				'picture'	=> 0,
				'work_time'	=> 0,
				'dayOff'	=> 0.00,
				'status'	=> 0,
				'end_status'	=> 0,
				'inserted'	=> date('Y-m-d'),	
			);
			
			return $list;
		}

}