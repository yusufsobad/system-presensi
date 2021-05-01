<?php

class unit{
	protected static $loc = "unit/";
	
	private static function _file_get_unit($unit){
		$file = self::$loc . $unit.'.json';	
		if(!file_exists($file)){
			$file = 'include/'.$file;
		}

		$unit = file_get_contents($file);
		$unit = json_decode($unit,true);
		return $unit;
	}

	public static function conv_to_option($args=array()){
		$check = array_filter($args);
		if(empty($check)){
			return array();
		}

		$unit = array();
		foreach ($args as $key => $val) {
			$unit[$key] = array();

			foreach ($val['unit'] as $ky => $vl) {
				$unit[$key][$ky] = ucwords($vl['label']." (".$vl['unit'].")");
			}
		}

		return $unit;
	}
	
	public static function _get($args=array()){
		$check = array_filter($args);
		if(empty($check)){
			$args = array('area','capacity','custom','length','speed','volume','weight');
		}
		
		$unit = array();
		foreach($args as $ky => $vl){
			$unit[$vl] = self::_file_get_unit($vl);
		}
		
		return $unit;
	}

	public static function conversi_unit($from='',$to='',$value=0){
		if(empty($from) || empty($to)){
			return 0;
		}

		$units = self::_get();

		$end = 0;
		foreach ($units as $ky => $val) {
			if($end==1){
				continue;
			}
			
			$def = $val['default'];
			$unit = $val['unit'];
			$awal = 0;$akhir=0;
		
			if(isset($unit[$from])){
				$awal = $unit[$from]['value'];
			}

			if(isset($unit[$to])){
				$akhir = $unit[$to]['value'];
				$end = 1;
			}
		}
	
		$nom = 0;
		if($awal!=0 && $akhir!=0){
			$nom = $awal / $akhir * $value;
		}

		return $nom;
	}
}