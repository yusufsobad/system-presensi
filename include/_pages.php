<?php
include 'pages/function.php';

abstract class _page{

	protected static $page = 1;

	protected static $search = false;

	protected static $type = ''; //Default

	protected static $data = array(); // data pencarian

	protected static $limit = 10;

	protected static $list_meta = '';

	// ----------------------------------------------------------
	// Layout Pages  --------------------------------------------
	// ----------------------------------------------------------
	protected static function _loadView($dir='',$data=array()){
		$loc = $dir;
		if(property_exists(new static, 'loc_view')){
			$loc = static::$loc_view;
			$loc .= '.'.$dir;
		}

		return sobad_asset::_loadView($loc,$data);
	}

	public static function _sidemenu(){
		return static::layout();
	}

	public static function _tabs($type){
		self::$type = $type;
		$data = static::get_box();
		
		ob_start();
		?>
			<div class="row">
				<?php theme_layout('_portlet',$data); ?>
			</div>
		<?php
		return ob_get_clean();
	}

	protected static function like_search($args=array(),$whr=''){
		$kata = '';$where = '';
		$cari = self::$data;
		$search = isset($cari['search'])?$cari['search']:'';
		$src = array();
		$src_meta = array();

		$meta = array();$tbl_meta='';
		if(property_exists(new static, 'table')){
			$post = '';
			if(property_exists(new static, 'post')){
				$post = static::$post;
			}

			$object = static::$table;

			if(property_exists(new $object, 'tbl_meta')){
				$tbl_meta = $object::$tbl_meta;
				$meta = $object::list_meta($post);
			}

			$blueprint = $object::blueprint($post);
			if(isset($blueprint['detail'])){
				foreach ($blueprint['detail'] as $key => $val) {
					if(in_array($key, $args)){
						foreach ($val['column'] as $ky => $vl) {
							$args[] = '_'.$key.'.'.$vl;
						}
					}
				}
			}
		}
		
		if(!empty($cari['words'])){
			if($search==0){
				unset($args[0]);

				$search = implode(',',$args);
				$kata = $cari['words'];
				
				foreach($args as $key => $val){
					if(in_array($val, $meta)){
						$src_meta[] = "(`$tbl_meta`.meta_key='$val' AND `$tbl_meta`.meta_value LIKE '%$kata%') ";
					}else{
						$_src = "$val LIKE '%$kata%'";

						if(is_callable(array(new static(), '_filter_search'))){
							$_xsrc = static::_filter_search($val,$kata);
							$_src = empty($_xsrc)?$_src:$_xsrc;
						}

						$src[] = $_src." ".$whr;
					}
				}
				
				$src_meta = implode(" OR ",$src_meta);
				$GLOBALS['search_meta_global'] = $src_meta;

				$src = implode(" OR ",$src);
				$where = "AND (".$src.") ";
			}else{
				$search = $args[$search];
				$kata = $cari['words'];
				if(in_array($search, $meta)){
					$where = 'AND ' . $whr;
					$GLOBALS['search_meta_global'] = "`$tbl_meta`.meta_key='$search' AND `$tbl_meta`.meta_value LIKE '%$kata%' ";
				}else{
					$_src = "$search LIKE '%$kata%'";

					if(is_callable(array(new static(), '_filter_search'))){
						$_xsrc = static::_filter_search($search,$kata);
						$_src = empty($_xsrc)?$_src:$_xsrc;
					}

					$where = "AND ".$_src." ".$whr;
				}
			}
		}else{
			$where = $whr;
		}

		$_search = isset($cari['search'])?$cari['search']:'';
		return array($where,$kata,$_search);
	}

	protected static function action(){
		$add = array(
			'ID'	=> 'add_0',
			'func'	=> 'add_form',
			'color'	=> 'btn-default',
			'icon'	=> 'fa fa-plus',
			'label'	=> 'Tambah',
			'type'	=> self::$type
		);
		
		return edit_button($add);
	}

	// ----------------------------------------------------------
	// Function Form Select wilayah -----------------------------
	// ----------------------------------------------------------

	// -------------- get value select opt ----------------------
	public static function get_provinces($id=1){
		$prov = array();
		if($id!=0){
			$prov = sobad_region::get_province_by($id);
			$prov = convToOption($prov,'ID','province');
		}
		
		return $prov;
	}

	public static function get_cities($id=0){
		$kota = array();
		if($id!=0){
			$cities = sobad_region::get_city_by($id);
			foreach($cities as $key => $kab){
				$tipe = sobad_region::_conv_type_city($kab['type']);
				$kota[$kab['ID']] = $tipe.' '.$kab['city'];
			}
		}
		
		return $kota;
	}

	public static function get_subdistricts($id=0){
		$kec = array();
		if($id!=0){
			$kec = sobad_region::get_subdistrict_by($id);
			$kec = convToOption($kec,'ID','subdistrict');
		}
		
		return $kec;
	}

	public static function get_villages($id=0){
		$pos = array();
		if($kec!=0){
			$pos = sobad_region::get_village_by($kec);
			$pos = convToOption($pos,'ID','village');
		}
		
		return $pos;
	}

	public static function get_postcodes($id=0){
		$pos = array();
		if($id!=0){
			$pos = sobad_region::get_postcodes($id);
			$pos = convToOption($pos,'postal_code','postal_code');
		}
		
		return $pos;
	}

	// -------------- option select onchange --------------------

	public static function option_province($id=1){
		$data = self::get_provinces($id);
		return self::_conv_option($data);
	}

	public static function option_city($id=0){
		$data = self::get_cities($id);
		return self::_conv_option($data);
	}

	public static function option_subdistrict($id=0){
		$data = self::get_subdistricts($id);
		return self::_conv_option($data);
	}

	public static function option_village($id=0){
		$data = self::get_villages($id);	
		return self::_conv_option($data);	
	}

	public static function option_postcode($id=0){
		$data = self::get_postcodes($id);	
		return self::_conv_option($data);	
	}

	protected function _conv_option($args=array()){
		$check = array_filter($args);
		if(empty($check)){
			return '';
		}
		
		$opt = '';
		foreach($args as $key => $val){
			$opt .= '<option value="'.$key.'">'.$val.'</option>';
		}
		
		return $opt;
	}

	// ----------------------------------------------------------
	// Function Pages to database -------------------------------
	// ----------------------------------------------------------
	protected static function _get_table($idx,$args=array()){
		if($idx==0){
			$idx = 1;
		}

		self::$page = $idx;
		self::$search = true;
		self::$data = isset($_POST['args'])?sobad_asset::ajax_conv_json($_POST['args']):$args;
		self::$type = isset($_POST['type'])?$_POST['type']:'';

		$table = static::table();
		return table_admin($table);
	}

	public static function _pagination($idx){
		return self::_get_table($idx);
	}

	public static function _search($args=array()){
		$args = sobad_asset::ajax_conv_json($args);
		return self::_get_table(1,$args);
	}

	public static function _trash($id=0, $role=true){
		global $DB_NAME;

		$id = str_replace('trash_','',$id);
		intval($id);

		$object = static::$table;
		$table = $object::$table;

		$_database = $DB_NAME;
		if(property_exists(new $object,'database')){
			$DB_NAME = $object::$database;
		}

		$q = sobad_db::_update_single($id,$table,array('ID' => $id, 'trash' => 1));

		$DB_NAME = $_database;
		if($q===1 && $role==true){
			$pg = isset($_POST['page'])?$_POST['page']:1;
			return self::_get_table($pg);
		}
	}

	public static function _recovery($id=0, $role=true){
		global $DB_NAME;

		$id = str_replace('recovery_','',$id);
		intval($id);

		$object = static::$table;
		$table = $object::$table;

		$_database = $DB_NAME;
		if(property_exists(new $object,'database')){
			$DB_NAME = $object::$database;
		}

		$q = sobad_db::_update_single($id,$table,array('ID' => $id, 'trash' => 0));

		$DB_NAME = $_database;
		if($q===1 && $role==true){
			$pg = isset($_POST['page'])?$_POST['page']:1;
			return self::_get_table($pg);
		}
	}

	public static function _delete($id=0,$role=true){
		global $DB_NAME;

		$id = str_replace('del_','',$id);
		intval($id);

		$object = static::$table;
		$table = $object::$table;

		$post = '';
		if(property_exists(new static, 'post')){
			$post = static::$post;
		}

		$schema = $object::blueprint($post);

		$_database = $DB_NAME;
		if(property_exists(new $object,'database')){
			$DB_NAME = $object::$database;
		}

		if(property_exists($object, 'tbl_meta')){
			$q = sobad_db::_delete_multiple("meta_id='$id'",$object::$tbl_meta);
		}

		if(property_exists($object, 'tbl_join')){
			if(isset($schema['joined'])){
				$reff = $schema['joined']['key'];
				$q = sobad_db::_delete_multiple($reff."='$id'",$object::$tbl_join);
			}
		}

		$q = sobad_db::_delete_single($id,$table);

		$DB_NAME = $_database;
		if($q===1 && $role==true){
			$pg = isset($_POST['page'])?$_POST['page']:1;
			return self::_get_table($pg);
		}else{
			return $id;
		}
	}

	public static function _edit($id=0,$role=true){
		$id = str_replace('edit_','',$id);
		intval($id);
		
		$args = static::_array();
		self::$type = isset($_POST['type'])?$_POST['type']:'';

		$post = '';
		if(property_exists(new static, 'post')){
			$post = static::$post;
		}

		$object = static::$table;
		$q = $object::get_id($id,$args,'',$post);
		
		if($q===0){
			return '';
		}

		if($role==false){
			return $q[0];
		}
		
		return static::edit_form($q[0]);
	}

	public static function _import(){
		$fileName = $_FILES["data"]["tmp_name"];
		
		if ($_FILES["data"]["size"] > 0) {
	        $delimiter = _detectDelimiter($fileName);
	        $file = fopen($fileName, "r");
	        
	        $status = true;$_colm = array();$files = array();
	        while (($column = fgetcsv($file, 10000, $delimiter)) !== FALSE) {
	        	foreach ($column as $key => $val) {
	        		if($status){
	        			$_colm[$key] = strtolower($val);
	        		}else{
	        			$files[$_colm[$key]] = $val;
	        		}
	        	}

	        	if(!$status){
		        	//Check data
		        	$data = array();
		        	$check = static::_check_import($files);
		        	
		        	foreach ($check['data'] as $key => $val) {
		        		$data[] = array('name' => $key, 'value' => sobad_asset::ascii_to_hexa($val));
		        	}

		        	if($check['status']){
		        		$q = self::_schema(json_encode($data),false); //Update data
		        	}else{
		        		if($check['insert']){
		        			$q = self::_schema(json_encode($data),true); // Add data
		        		}
		        	}
		        }

		        $status = false;
	        }
			
			$pg = isset($_POST['page'])?$_POST['page']:1;
			return self::_get_table($pg);
	    }
	}

	// ----------------------------------------------------------
	// Function Get Data from database --------------------------
	// ----------------------------------------------------------

	protected static function _get_db($id=0,$args=array(),$where=''){
		$post = '';
		if(property_exists(new static, 'post')){
			$post = static::$post;
		}

		$object = static::$table;
		$args = $object::get_id($id,$args,$where,$post);

		return $args;
	}

	protected static function _gets_db($args=array(),$where=''){
		$post = '';
		if(property_exists(new static, 'post')){
			$post = static::$post;
		}

		$object = static::$table;
		$args = $object::get_all($args,$where,$post);

		return $args;
	}

	protected static function _count_db($where='',$args=array()){
		$post = '';
		if(property_exists(new static, 'post')){
			$post = static::$post;
		}

		$object = static::$table;
		$args = $object::count($where,$args,$post);

		return $args;
	}

	// ----------------------------------------------------------
	// Function Update to database ------------------------------
	// ----------------------------------------------------------

	protected static function _schema($_args=array(),$add=false){
		global $DB_NAME;

		$args = sobad_asset::ajax_conv_json($_args);
		if(is_callable(array(new static(), '_callback'))){
			$args = static::_callback($args,$_args);
		}
	
		$id = $args['ID'];
		unset($args['ID']);
	
		$src = array();
		if(isset($args['search'])){
			$src = array(
				'search'	=> $args['search'],
				'words'		=> $args['words']
			);

			unset($args['search']);
			unset($args['words']);
		}

		$post = '';
		if(property_exists(new static, 'post')){
			$post = static::$post;
		}

		$object = static::$table;
		$schema = $object::blueprint($post);

		$_database = $DB_NAME;
		if(property_exists(new $object,'database')){
			$DB_NAME = $object::$database;
		}

		self::$list_meta = $object::list_meta($post);

		$data = array();
		$list = $object::_list();
		foreach ($list as $key => $val) {
			if(isset($args[$val])){
				$data[$val] = $args[$val];
			}
		}

		if($add){
			
			$idx = sobad_db::_insert_table($schema['table'],$data);
			$q = self::_add_meta_db($idx,$args,$schema);

			// Check temporary
			if(isset($schema['temporary'])){
				$temp = $schema['temporary'];
				if(isset($temp[$post])){
					$conn = sobad_db::connect();

					// insert index in temporary table
					$temp_table = "temp-" . $temp[$post]['temp'];

					$query = "INSERT INTO `$temp_table`(reff_temp) VALUES('$idx')";
					$conn->query($query) or die('Gagal insert data temporary!!!');
				}
			}

			$id = $idx;
		}else{

			$q = sobad_db::_update_single($id,$schema['table'],$data);
			$q = self::_update_meta_db($id,$args,$schema);
		}

		$DB_NAME = $_database;
		return array('index' => $id, 'data' => $q,'search' => $src,'value' => $args);
	}	

	public static function _update_db($_args=array(),$menu='default',$obj=''){
		$args = self::_schema($_args,false);
		$q = $args['data'];
		$src = $args['search'];

		$obj = empty($obj)?static::$object:$obj;
		if(is_callable(array(new static(), '_updateDetail'))){
			static::_updateDetail($args,$_args);
		}

		if($q!==0){
			if($menu=='default'){
				$pg = isset($_POST['page'])?$_POST['page']:1;
				return self::_get_table($pg,$src);
			}else{
				if(is_callable(array($obj,$menu))){
					return $obj::{$menu}($args);
				}else{
					return $args;
				}
			}
		}
	}

	protected static function _update_meta_db($idx=0,$args=array(),$schema=array()){
		$q = $idx;
		$object = static::$table;
		// Meta Table
		if(isset($schema['meta'])){
			$table = $schema['meta']['table'];

			// Insert Data Meta
			$_meta_key = $schema['meta']['key'];	
			$list = self::$list_meta;
			foreach ($list as $key => $val) {
				if(!isset($args[$val])){
					continue;
				}

				$_data = array(
					$_meta_key		=> $idx,
					'meta_key'		=> $val,
					'meta_value'	=> $args[$val]
				);

				$meta = $object::check_meta($idx,$val);
				$check = array_filter($meta); 
				if(empty($check)){
					$q = sobad_db::_insert_table($table,$_data);
				}else{
					$q = sobad_db::_update_single($meta[0]['ID'],$table,$_data);
				}
			}
		}

		return $q;
	}

	// ----------------------------------------------------------
	// Function Add to database -------------------------------
	// ----------------------------------------------------------	

	public static function _add_db($_args=array(),$menu='default',$obj=''){
		$args = self::_schema($_args,true);
		$q = $args['data'];
		$src = $args['search'];

		$obj = empty($obj)?static::$object:$obj;
		if(is_callable(array(new static(), '_addDetail'))){
			static::_addDetail($args,$_args);
		}
		
		if($q!==0){
			if($menu=='default'){
				$pg = isset($_POST['page'])?$_POST['page']:1;
				return self::_get_table($pg,$src);
			}else{
				if(is_callable(array($obj,$menu))){
					return $obj::{$menu}($args);
				}else{
					return $args;
				}
			}
		}
	}

	protected static function _add_meta_db($idx=0,$args=array(),$schema=array()){
		$q = $idx;
		// Meta Table
		if(isset($schema['meta'])){
			$table = $schema['meta']['table'];

			// Insert Data Meta
			$_meta_key = $schema['meta']['key'];
			$list = self::$list_meta;
			foreach ($list as $key => $val) {
				if(!isset($args[$val])){
					continue;
				}

				$_data = array(
					$_meta_key		=> $idx,
					'meta_key'		=> $val,
					'meta_value'	=> $args[$val]
				);

				$q = sobad_db::_insert_table($table,$_data);
			}
		}

		return $q;
	}

}