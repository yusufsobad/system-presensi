<?php

function stock_head_title(){
	$args = array(
		'title'	=> 'Stock <small>data stock</small>',
		'link'	=> array(
			0	=> array(
				'func'	=> 'stock_kasir',
				'label'	=> 'Stock'
			)
		),
		'date'	=> false
	); 
	
	return $args;
}

function get_portlet_stock($start,$type){
	$data = stock_table($start,$type);
	
	$label = 'Makan Karyawan';
	if($type!=10){
		if($type!=1){
			$label = bahan_in_type($type);
		}

		$label = 'Bahan Baku';
	}

	$box = array(
		'label'		=> 'Data '.$label.' Sochick',
		'tool'		=> '',
		'action'	=> '',
		'func'		=> 'sobad_table',
		'data'		=> $data
	);
	
	return $box;
}

// ----------------------------------------------------------
// Layout category  ------------------------------------------
// ----------------------------------------------------------
function stock_kasir(){
	return stock_layout(1,1);
}

function portlet_stock($type){
	$type = str_replace('tab_','',$type);
	$data = get_portlet_stock(1,$type);
	
	ob_start();
	?>
		<div class="row">
			<?php sobad_content('sobad_portlet',$data); ?>
		</div>
	<?php
	return ob_get_clean();
}

function stock_table($start=1,$tabs='',$search=false,$cari=array()){
	$lang = constant('sochick_language');
	$data = array();
	$args = array('ID','name','unit','stock','note','type');

	$where = '';
	if($tabs==1){
		$func = 'primer_product';
	}else if($tabs!=10){
		$func = bahan_in_type($tabs);
		$where = "AND status_stock='1'";
	}else{
		$func = 'recipe';
	}

	$kata = '';
	if($search){
		$src = like_pencarian($args,$cari,$where);
		$cari = $src[0];
		$where = $src[0];
		$kata = $src[1];
	}else{
		$cari=$where;
	}
	
	$limit = 'LIMIT '.intval(($start - 1) * 10).',10';
	$where .= $limit;
	$item = new sochick_item();

	$_func = 'get_'.$func.'s';
	$args = $item->{$_func}($args,$where);
	$sum_data = $item->{$_func}(array('ID'),$cari);
	
	$data['data'] = array('search_stock',$kata,'sobad_portlet',$tabs);
	$data['search'] = array('Semua','nama');
	$data['class'] = '';
	$data['table'] = array();
	$data['page'] = array(
		'func'	=> '_pagination',
		'data'	=> array(
			'start'		=> $start,
			'qty'		=> count($sum_data),
			'limit'		=> 10,
			'func'		=> 'stock_pagination',
			'type'		=> $tabs
		)
	);
	
	$no = ($start - 1) * 10;
	foreach($args as $key => $val){
		$no += 1;
		$note = json_decode($val['note'],true);
		$stock = 0;//sochick_stock_available($note); 

		$status = '';
	//	if($stock<=0){
	//		$status = 'disabled';
	//	}

		$status2 = '';
		if($val['stock']<=0){
			$status2 = 'disabled';
		}

		$add = array(
			'ID'	=> 'add_'.$val['ID'],
			'func'	=> 'stock_add_form',
			'color'	=> 'green',
			'icon'	=> 'fa fa-sign-in',
			'label'	=> 'add',
			'status'=> $status,
			'type'	=> $tabs
		);

		$edit = array(
			'ID'	=> 'edit_'.$val['ID'],
			'func'	=> 'edit_stock',
			'color'	=> 'blue',
			'icon'	=> 'fa fa-edit',
			'label'	=> 'edit',
			'type'	=> $tabs
		);

		$detail = array(
			'ID'	=> 'detail_'.$val['ID'],
			'func'	=> 'detail_stock',
			'color'	=> 'yellow',
			'icon'	=> 'fa fa-eye',
			'label'	=> 'Detail',
			'type'	=> $tabs
		);

		$food = array(
			'ID'	=> 'food_'.$val['ID'],
			'func'	=> 'stock_food_form',
			'color'	=> 'default',
			'icon'	=> 'fa fa-flask',
			'label'	=> 'food',
			'type'	=> $tabs
		);

		$reject = array(
			'ID'	=> 'reject_'.$val['ID'],
			'func'	=> 'stock_reject_form',
			'color'	=> 'green meadow',
			'icon'	=> 'fa fa-eject',
			'label'	=> 'reject',
			'type'	=> $tabs
		);	

		$retur = array(
			'ID'	=> 'retur_'.$val['ID'],
			'func'	=> 'retur_stock',
			'color'	=> 'red',
			'icon'	=> 'fa fa-undo',
			'label'	=> 'retur',
			'status'=> $status2,
			'type'	=> $tabs
		);

		$data['table'][$key]['tr'] = array('');
		$data['table'][$key]['td'] = array(
			'no'			=> array(
				'center',
				'5%',
				$no,
				true
			),
			'Name'			=> array(
				'left',
				'auto',
				$val['name'],
				true
			),
			'Ready'			=> array(
				'left',
				'10%',
				format_nominal($lang,$val['stock']),
				true
			),
			'Available'		=> array(
				'left',
				'10%',
				format_nominal($lang,$stock),
				true
			),
			'Unit'			=> array(
				'left',
				'10%',
				$val['unit'],
				true
			),
			'Value'			=> array(
				'left',
				'10%',
				'<input class="money" type="text" name="val_'.$val['ID'].'" onkeydown="mask_money(\'.money\')">',
				true
			),
			'Tambah'		=> array(
				'center',
				'8%',
				edit_button($add),
				false
			),
			'Edit'			=> array(
				'center',
				'8%',
				edit_button($edit),
				false
			),
			'History'		=> array(
				'center',
				'8%',
				edit_button($detail),
				false
			),
			'Food'			=> array(
				'center',
				'8%',
				edit_button($food),
				false
			),
			'Reject'		=> array(
				'center',
				'8%',
				edit_button($reject),
				false
			),
			'Retur'			=> array(
				'center',
				'8%',
				hapus_button($retur),
				false
			)	
		);

		unset($data['table'][$key]['td']['Available']);

		if($tabs==1){
			unset($data['table'][$key]['td']['Value']);
			unset($data['table'][$key]['td']['Food']);
			unset($data['table'][$key]['td']['Tambah']);
			unset($data['table'][$key]['td']['Edit']);
			unset($data['table'][$key]['td']['History']);
			unset($data['table'][$key]['td']['Retur']);
		}else if($tabs==2){
			unset($data['table'][$key]['td']['Value']);
			unset($data['table'][$key]['td']['Reject']);
			unset($data['table'][$key]['td']['Food']);
		}else if($tabs==7){
			unset($data['table'][$key]['td']['Food']);
			unset($data['table'][$key]['td']['Tambah']);
			unset($data['table'][$key]['td']['Edit']);
			unset($data['table'][$key]['td']['History']);
			unset($data['table'][$key]['td']['Reject']);
			unset($data['table'][$key]['td']['Retur']);
		}else if($tabs!=10){
			unset($data['table'][$key]['td']['Value']);
			unset($data['table'][$key]['td']['Retur']);
			unset($data['table'][$key]['td']['Food']);
		}else{
			unset($data['table'][$key]['td']['Value']);
			unset($data['table'][$key]['td']['Tambah']);
			unset($data['table'][$key]['td']['Edit']);
			unset($data['table'][$key]['td']['History']);
			unset($data['table'][$key]['td']['Reject']);
			unset($data['table'][$key]['td']['Retur']);
		}
	}

	if($tabs==7){
		$itm = $item->get_item(447,array('ID','name','stock','unit'));
		$itm = $itm[0];

		$data['table'][$no]['td'] = array(
			'no'			=> array(
				'center',
				'auto',
				'<strong> Bumbu yang Di pakai</strong>',
				true,
				5
			),
		);

		$data['table'][$no+1]['td'] = array(
			'no'			=> array(
				'center',
				'5%',
				1,
				true
			),
			'Name'			=> array(
				'left',
				'auto',
				$itm['name'],
				true
			),
			'Ready'			=> array(
				'left',
				'10%',
				format_nominal($lang,$itm['stock']),
				true
			),
			'Unit'			=> array(
				'left',
				'10%',
				$itm['unit'],
				true
			),
			'Value'			=> array(
				'left',
				'10%',
				'<input class="money" type="text" name="season_'.$itm['ID'].'" onkeydown="mask_money(\'.money\')">',
				true
			),
		);
		$data['table'][$no+2]['td'] = array(
			'no'			=> array(
				'center',
				'5%',
				'',
				true,
				4
			),
			'Value'			=> array(
				'center',
				'10%',
				'<button data-sobad="tambah_stock_marinade" data-load="sobad_portlet" data-type="7" type="button" class="btn green" data-dismiss="modal" onclick="sochick_submit_marinade(this)">Buat</button>',
				true
			),
		);
	}
	
	return $data;
}

function stock_layout($start,$type){
	$item = new sochick_item();	
	$box = get_portlet_stock($start,$type);

	$grp = bahan_in_type();
	$grp[1] = 'primer_product';
	$grp[10] = 'Makan Karyawan';

	$tab = array();
	foreach($grp as $key => $val){
		$limit = '';
		if($key==1){
			$label = 'Bahan Baku';
		}else if($key!=10){
			
			if($key==2){
				$label = 'Fried';
			}else{
				$label = $val;
			}

			$limit = "AND category='1'";

		}else{
			$label = 'Makan Karyawan';
			$val = 'recipe';
		}

		$func = 'get_'.$val.'s';
		$itm = $item->{$func}(array('COUNT(ID) AS cnt'),$limit);

		$tb = array(
			'key'	=> 'tab_'.$key,
			'func'	=> 'portlet_stock',
			'label'	=> $label,
			'active'=> $key==1?true:false,
			'info'	=> 'badge-success',
			'qty'	=> $itm[0]['cnt']
		);
		array_push($tab,$tb);
	}

	$tabs = array(
		'tab'	=> $tab,
		'func'	=> 'sobad_portlet',
		'data'	=> $box
	);
	
	$opt = array(
		'title'		=> stock_head_title(),
		'style'		=> '',
		'script'	=> array('_script_stock')
	);
	
	return tabs_admin($opt,$tabs);
}

// ----------------------------------------------------------
// Form data inventory --------------------------------------
// ----------------------------------------------------------
function stock_food_form($id=0){
	$type = isset($_POST['type'])?$_POST['type']:'';
	$id = str_replace('food_', '', $id);
	$vals = array($id,0);
	
	$args = array(
		'title'		=> 'Tambah data makan',
		'button'	=> '_btn_modal_save',
		'status'	=> array(
			'link'		=> 'kurang_stock',
			'load'		=> 'sobad_portlet',
			'type'		=> $type
		)
	);
	
	return stock_data_form($args,$vals);
}

function stock_reject_form($id=0){
	$type = isset($_POST['type'])?$_POST['type']:'';
	$id = str_replace('reject_', '', $id);
	$vals = array($id,0);
	
	$args = array(
		'title'		=> 'Tambah data reject',
		'button'	=> '_btn_modal_save',
		'status'	=> array(
			'link'		=> 'reject_stock',
			'load'		=> 'sobad_portlet',
			'type'		=> $type
		)
	);
	
	return stock_data_form($args,$vals);
}

function stock_add_form($id=0,$func='tambah_stock',$load='sobad_portlet'){
	$type = isset($_POST['type'])?$_POST['type']:'';
	$id = str_replace('add_', '', $id);
	$vals = array($id,0);
	
	$args = array(
		'title'		=> 'Tambah data stock',
		'button'	=> '_btn_modal_save',
		'status'	=> array(
			'link'		=> $func,
			'load'		=> $load,
			'type'		=> $type
		)
	);
	
	return stock_data_form($args,$vals);
}

function stock_edit_form($vals=array()){
	$check = array_filter($vals);
	if(empty($check)){
		return '';
	}
	
	$type = isset($_POST['type'])?$_POST['type']:'';
	$vals = array(
		$vals['ID'],
		$vals['paid_qty']
	);
	
	$args = array(
		'title'		=> 'Edit data stock',
		'button'	=> '_btn_modal_save',
		'status'	=> array(
			'link'		=> 'update_stock',
			'load'		=> 'sobad_portlet',
			'type'		=> $type
		)
	);
	
	return stock_data_form($args,$vals);
}

function stock_data_form($args=array(),$vals=array()){
	$check = array_filter($args);
	if(empty($check)){
		return '';
	}

	$item = new sochick_item();
	$item = $item->get_item($vals[0],array('name'));
	$item = $item[0]['name'];

	$lang = constant('sochick_language');
	$data = array(
		0 => array(
			'func'			=> 'opt_hidden',
			'type'			=> 'hidden',
			'key'			=> 'ID',
			'value'			=> $vals[0]
		),
		1 => array(
			'func'			=> 'opt_input',
			'type'			=> 'text',
			'key'			=> 'stock',
			'label'			=> 'Stock '.$item,
			'class'			=> 'input-circle money',
			'value'			=> format_nominal($lang,$vals[1]),
			'data'			=> 'placnumberder="Stock" onkeydown="mask_money(\'.money\')"'
		)
	);
	
	$args['func'] = array('sobad_form');
	$args['data'] = array($data);
	
	return modal_admin($args);
}

function _stock_add_form($id){
	return stock_add_form($id,'_tambah_stock','here_modal');
}

function _tambah_stock($args=array()){
	$tp = isset($_POST['type'])?$_POST['type']:'';
	return tambah_stock($args,'detail_stock',$tp);
}
// ----------------------------------------------------------
// Function category to database -----------------------------
// ----------------------------------------------------------
function _get_stock_table($idx,$args=array()){
	if($idx==0){
		$idx = 1;
	}

	$args = isset($_POST['args'])?ajax_conv_json($_POST['args']):$args;	
	$tp = isset($_POST['type'])?$_POST['type']:'';

	$table = stock_table($idx,$tp,true,$args);
	return table_admin($table);
}

function stock_pagination($idx){
	return _get_stock_table($idx);
}

function search_stock($args=array()){
	$args = ajax_conv_json($args);
	return _get_stock_table(1,$args);
}

function detail_stock($id){
	return detail_bahan($id,'_stock_add_form');
}

function edit_stock($id){
	$id = str_replace('edit_','',$id);
	intval($id);
	
	$args = array('ID');
	
	$meta = new sochick_order();
	$q = $meta->get_addStock($args,"AND title='$id'");
	
	if($q===0){
		return '';
	}
	
	$lg = count($q)-1;
	return stock_edit_form($q[$lg]);
}

function retur_stock($id){
	$q = set_retur_stock($id);

	if($q!==0){
		$pg = isset($_POST['page'])?$_POST['page']:1;
		return _get_stock_table($pg);
	}
}

function update_stock($args=array()){
	$lang = constant('sochick_language');
	$args = ajax_conv_json($args);
	$id = $args['ID'];
	unset($args['ID']);
	
	if(isset($args['search'])){
		$src = array(
			'search'	=> $args['search'],
			'words'		=> $args['words']
		);

		unset($args['search']);
		unset($args['words']);
	}
	
	$stocks = clear_format($lang,$args['stock']);
	$id_usr = $_SESSION['sochick_id-user'];

	$db = new sochick_item();
	$conversi = new sochick_conversi();
	$order = new sochick_order();

// Tambah Stock item
	//Get before stock
	$add = $order->get_order($id,array('ID','paid'));

	$ids = $add[0]['paid_brng'];
	$items = $db->get_item($ids,array('stock','unit','note'));

// -----------------------
	$bahan = json_decode($items[0]['note'],true);
	$qty_bhn = isset($bahan['qty'])?$bahan['qty']:1;
	$stock = $stocks / $qty_bhn;	

	$stck = array(
		'ID'		=> $ids,
		'stock'		=> ($items[0]['stock']-$add[0]['paid_qty']) + $stocks
	);

	$q = $db->_update_single($ids,'soc-item',$stck);

// Update Log History penambahan
	$add_1 = array(
		'barang'	=> $ids,
		'qty'		=> $stocks
	);

	$q = $db->_update_multiple("reff='$id'",'soc-transaksi',$add_1);

// Kurangi Stock bahan	
	$bahans = $order->get_subtractStock(array('ID'),"AND title='$id'");
	$id_subt = $bahans[0]['ID'];

	$bahan = $bahan['bahan'];
	foreach ($bahans as $ky => $val) {
		$idx = $val['paid_brng'];
		// Pengurangan Stock
		$bhn = $db->get_item($idx,array('stock','unit'));

		$conv_qty = $conversi->get_conv_unit($ky,$bahan[$idx]['unit'],$bhn[0]['unit']);	
		$conv_qty *= $bahan[$idx]['qty'];

		$_stock_ = $bhn[0]['stock'] + $val['paid_qty'];
		$_stock = $stock * $conv_qty;

		$stck = array(
			'ID'		=> $idx,
			'stock'		=> $_stock_ - $_stock
		);
	
		$q = $db->_update_single($idx,'soc-item',$stck);

		// Pembuatan Log
		$subt_1 = array(
			'barang'	=> $idx,
			'qty'		=> $_stock
		);

		$whr = "reff='$id_subt' AND barang='$idx'";
		$q = $db->_update_multiple($whr,'soc-transaksi',$subt_1);
	}
	
	if($q!==0){
		$pg = isset($_POST['page'])?$_POST['page']:1;
		return _get_stock_table($pg,$src);
	}
}

function tambah_stock($args=array(),$callback='',$data_back=''){
	$lang = constant('sochick_language');
	$args = ajax_conv_json($args);
	$id = $args['ID'];
	unset($args['ID']);
	
	if(isset($args['search'])){
		$src = array(
			'search'	=> $args['search'],
			'words'		=> $args['words']
		);

		unset($args['search']);
		unset($args['words']);
	}
	
	$stocks = clear_format($lang,$args['stock']);
	$id_usr = $_SESSION['sochick_id-user'];

	$db = new sochick_item();
	$conversi = new sochick_conversi();
	$items = $db->get_item($id,array('stock','unit','note'));

	$bahan = json_decode($items[0]['note'],true);
	$qty_bhn = isset($bahan['qty'])?$bahan['qty']:1;
	$stock = $stocks / $qty_bhn;
// Tambah Stock item
	$stck = array(
		'ID'		=> $id,
		'stock'		=> $items[0]['stock'] + $stocks
	);

	$q = $db->_update_single($id,'soc-item',$stck);

// Buat Log History penambahan
	$add = array(
		'title'	=> $id,
		'user'	=> $id_usr,
		'var'	=> 'add_stock',
		'note'	=> 'Stock Penjualan'
	);

	$id_add = $db->_insert_table('soc-order',$add);

	$add_1 = array(
		'reff'		=> $id_add,
		'barang'	=> $id,
		'unit'		=> $items[0]['unit'],
		'qty'		=> $stocks,
		'note'		=> $items[0]['stock']
	);

	$q = $db->_insert_table('soc-transaksi',$add_1);

// Kurangi Stock bahan	
	// Buat Log History Pengurangan 
	$subtract = array(
		'title'	=> $id_add,
		'user'	=> $id_usr,
		'var'	=> 'subtract_stock',
		'note'	=> 'Membuat Recipe'
	);

	$id_subt = $db->_insert_table('soc-order',$subtract);

	foreach ($bahan['bahan'] as $ky => $val) {
		// Pengurangan Stock
		$bhn = $db->get_item($ky,array('stock','unit'));

		$conv_qty = $conversi->get_conv_unit($ky,$val['unit'],$bhn[0]['unit']);
		$conv_qty *= $val['qty'];
		$_stock = $stock * $conv_qty;

		$stck = array(
			'ID'		=> $ky,
			'stock'		=> $bhn[0]['stock'] - $_stock
		);
	
		$q = $db->_update_single($ky,'soc-item',$stck);

		// Pembuatan Log
		$subt_1 = array(
			'reff'		=> $id_subt,
			'barang'	=> $ky,
			'qty'		=> $_stock,
			'unit'		=> $bhn[0]['unit'],
			'note'		=> $bhn[0]['stock']
		);

		$q = $db->_insert_table('soc-transaksi',$subt_1);
	}

	if($q!==0){
		if(is_callable($callback)){
			return $callback($data_back);
		}else{
			$pg = isset($_POST['page'])?$_POST['page']:1;
			return _get_stock_table($pg,$src);
		}
	}
}

function tambah_stock_marinade($args=array()){
	$lang = constant('sochick_language');
	$args = ajax_conv_json($args);
	$_ing = array();
	
	if(isset($args['search'])){
		$src = array(
			'search'	=> $args['search'],
			'words'		=> $args['words']
		);

		unset($args['search']);
		unset($args['words']);
	}
	
	$id_usr = $_SESSION['sochick_id-user'];

	$db = new sochick_item();
	$conversi = new sochick_conversi();

// Buat Log History penambahan
	$add = array(
		'title'	=> 0,
		'user'	=> $id_usr,
		'var'	=> 'add_stock_marinade',
		'note'	=> 'Stock Marinade'
	);

	$id_add = $db->_insert_table('soc-order',$add);

// Tambah Data Stock
	$marinade = $db->get_marinades(array('ID','name','stock','unit','note'));

	foreach ($marinade as $key => $val) {
		$bahan = json_decode($val['note'],true);
		$stock = $args['val_'.$val['ID']];

		$add_1 = array(
			'reff'		=> $id_add,
			'barang'	=> $val['ID'],
			'unit'		=> $val['unit'],
			'qty'		=> $stock,
			'note'		=> $val['stock']
		);

		$q = $db->_insert_table('soc-transaksi',$add_1);

	// Tambah Stock item
		$stck = array(
			'ID'		=> $val['ID'],
			'stock'		=> $val['stock'] + $stock
		);

		$q = $db->_update_single($val['ID'],'soc-item',$stck);

	// Get Item type 1
		foreach ($bahan['bahan'] as $key => $val) {
			$_itm = $db->get_item($key,array('type'));
			if($_itm[0]['type']==1){
				$_ing[] = array($key,$stock);
			}
		}
	}

	$_ing[] = array(447,$args['season_447']);
// Kurangi Stock bahan	
	// Buat Log History Pengurangan 
	$subtract = array(
		'title'	=> $id_add,
		'user'	=> $id_usr,
		'var'	=> 'subtract_stock_marinade',
		'note'	=> 'Membuat Marinade'
	);

	$id_subt = $db->_insert_table('soc-order',$subtract);

	foreach ($_ing as $ky => $val) {
		// Pengurangan Stock
		$bhn = $db->get_item($val[0],array('stock','unit'));
		$_stock = $bhn[0]['stock'] - $val[1];

		$stck = array(
			'ID'		=> $val[0],
			'stock'		=> $_stock
		);
	
		$q = $db->_update_single($val[0],'soc-item',$stck);

		// Pembuatan Log
		$subt_1 = array(
			'reff'		=> $id_subt,
			'barang'	=> $val[0],
			'qty'		=> $val[1],
			'unit'		=> $bhn[0]['unit'],
			'note'		=> $bhn[0]['stock']
		);

		$q = $db->_insert_table('soc-transaksi',$subt_1);
	}

	if($q!==0){
		$pg = isset($_POST['page'])?$_POST['page']:1;
		return _get_stock_table($pg,$src);
	}
}

function reject_stock($args=array()){
	$lang = constant('sochick_language');
	$args = ajax_conv_json($args);
	$id = $args['ID'];
	unset($args['ID']);
	
	if(isset($args['search'])){
		$src = array(
			'search'	=> $args['search'],
			'words'		=> $args['words']
		);

		unset($args['search']);
		unset($args['words']);
	}
	
	$stocks = clear_format($lang,$args['stock']);
	$id_usr = $_SESSION['sochick_id-user'];

	$db = new sochick_item();
	$items = $db->get_item($id,array('stock','unit'));

// Kurang Stock item
	$stck = array(
		'ID'		=> $id,
		'stock'		=> $items[0]['stock'] - $stocks
	);

	$q = $db->_update_single($id,'soc-item',$stck);

// Buat Log History penambahan
	$decrease = array(
		'title'	=> $id,
		'user'	=> $id_usr,
		'var'	=> 'reject_stock',
		'note'	=> 'Reject Stock'
	);

	$id_decrease = $db->_insert_table('soc-order',$decrease);

	$decrease_1 = array(
		'reff'		=> $id_decrease,
		'barang'	=> $id,
		'unit'		=> $items[0]['unit'],
		'qty'		=> $stocks,
		'note'		=> $items[0]['stock']
	);

	$q = $db->_insert_table('soc-transaksi',$decrease_1);

	if($q!==0){
		$pg = isset($_POST['page'])?$_POST['page']:1;
		return _get_stock_table($pg,$src);
	}
}

function kurang_stock($args=array()){
	$lang = constant('sochick_language');
	$args = ajax_conv_json($args);
	$id = $args['ID'];
	unset($args['ID']);
	
	if(isset($args['search'])){
		$src = array(
			'search'	=> $args['search'],
			'words'		=> $args['words']
		);

		unset($args['search']);
		unset($args['words']);
	}
	
	$stocks = clear_format($lang,$args['stock']);
	$id_usr = $_SESSION['sochick_id-user'];

	$db = new sochick_item();
	$items = $db->get_item($id,array('stock','unit'));

// Kurang Stock item
	$stck = array(
		'ID'		=> $id,
		'stock'		=> $items[0]['stock'] - $stocks
	);

	$q = $db->_update_single($id,'soc-item',$stck);

// Buat Log History penambahan
	$decrease = array(
		'title'	=> $id,
		'user'	=> $id_usr,
		'var'	=> 'eating_stock',
		'note'	=> 'Untuk Makan'
	);

	$id_decrease = $db->_insert_table('soc-order',$decrease);

	$decrease_1 = array(
		'reff'		=> $id_decrease,
		'barang'	=> $id,
		'unit'		=> $items[0]['unit'],
		'qty'		=> $stocks,
		'note'		=> $items[0]['stock']
	);

	$q = $db->_insert_table('soc-transaksi',$decrease_1);

	if($q!==0){
		$pg = isset($_POST['page'])?$_POST['page']:1;
		return _get_stock_table($pg,$src);
	}
}

function set_retur_stock($id){
	$id = str_replace('retur_', '', $id);
	intval($id);

	$item = new sochick_item();
	$stock = $item->get_item($id,array('stock','note'));
	$note = $stock[0]['note'];
	$stock = $stock[0]['stock'];

	$harga = 0;
	$note = json_decode($note,true);
	if(isset($note['bahan'])){
		$harga = sochick_price_modal($note);
	}

	//Reset Stock
	$item->_update_single($id,'soc-item',array('ID' => $id,'stock' => 0));

	//Buat History
	$order = array(
		'title'		=> $id,		
		'user'		=> $_SESSION['sochick_id-user'],
		'var'		=> 'retur_stock',
		'status'	=> 1
	);

	$idx = $item->_insert_table('soc-order',$order);

	$trans = array(
		'reff'		=> $idx,
		'barang'	=> $id,
		'qty'		=> $stock,
		'price'		=> $harga
	);

	$q = $item->_insert_table('soc-transaksi',$trans);

	return $q;
}

// ------------------------------------------------------------------------
// --- Script -------------------------------------------------------------
// ------------------------------------------------------------------------

function _script_stock(){
	?>
		<script type="text/javascript">
			function sochick_submit_marinade(val){
				var ajx = $(val).attr("data-sobad");
				var load = $(val).attr("data-load");
				var tp = $(val).attr('data-type');

				var pg = $('#dash_pagination li.disabled a').attr('data-qty');
				var data = $("input").serializeArray();
				data = conv_array_submit(data);
			
				data = "ajax="+ajx+"&data="+data+"&type="+tp+"&page="+pg;
				sobad_ajax('#'+load,data,'html',true,'','');
			}
		</script>
	<?php
}