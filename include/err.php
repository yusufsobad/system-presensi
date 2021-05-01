<?php

class _error{
	public function _page404(){
		header('Location: include/404.php');
	}
	
	public function _page500(){
		header('Location: include/500.php');
	}
	
	public static function _connect(){
		$err = self::_alert_db("server: koneksi gagal");
		die($err);
	}
	
	public static function _database(){
		$err = self::_alert_db("server: database tidak ditemukan");
		die($err);
	}
	
	public static function _user_login(){
		$err = self::_alert_db("Username atau password anda salah");
		die($err);
	}
	
	public static function _alert_db($msg){
		$ajax = array(
			'status' => "error",
			'msg'	 => $msg,
			'func'	 => ""
		);
		$ajax = json_encode($ajax);
		
		return $ajax;
	}

	public static function _alert_msg($msg,$data,$inner){
		$ajax = array(
			'status' => "success",
			'msg'	 => $msg,
			'data'   => $data,
			'inner'	 => $inner,
			'func'	 => ""
		);
		$ajax = json_encode($ajax);
		
		return $ajax;
	}
}