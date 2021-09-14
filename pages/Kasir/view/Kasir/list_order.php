<?php

function listOrder_head_title(){
	$args = array(
		'title'	=> 'Account <small>data account</small>',
		'link'	=> array(
			0	=> array(
				'func'	=> 'listOrder_balance',
				'label'	=> 'kategori'
			)
		),
		'date'	=> false
	);
	
	return $args;
}

// ----------------------------------------------------------
// Layout category  ------------------------------------------
// ----------------------------------------------------------
function order_kasir(){
	return listOrder_layout(1);
}

function listOrder_table($start=1){
	$lang = constant('sochick_language');
	$data = array();
	$args = array('ID','title','contact','type','payment','order_date','var','status');
	$y = date('Y');$m = date('m');$d = date('d');
	$whr = "AND YEAR(`soc-order`.order_date)='$y' AND MONTH(`soc-order`.order_date)='$m' AND DAY(`soc-order`.order_date)='$d'";
	$sort = " ORDER BY title ASC ";

	$limit = 'LIMIT '.intval(($start - 1) * 10).',10';
	$where = $whr.$sort.$limit;
	$order = new sochick_order();
	$args = $order->get_orders($args,$where);
	$sum_data = $order->get_orders(array('ID'),$whr);
	
	$data['class'] = '';
	$data['table'] = array();
	$data['page'] = array(
		'func'	=> '_pagination',
		'data'	=> array(
			'start'		=> $start,
			'qty'		=> count($sum_data),
			'limit'		=> 10,
			'func'		=> 'listOrder_pagination'
		)
	);
	
	$transaksi = new sochick_transaksi();
	$no = 0;

	foreach($args as $key => $val){
		$no += 1;
		$view = array(
			'ID'	=> 'view_'.$val['ID'],
			'func'	=> 'view_listOrder',
			'color'	=> 'yellow',
			'icon'	=> 'fa fa-eye',
			'label'	=> 'view'
		);

		$sts = '';
		if($val['status']==2){
			$sts = 'disabled';
		}

		$cancel = array(
			'ID'	=> 'cancel_'.$val['ID'],
			'func'	=> 'cancel_listOrder',
			'color'	=> 'red',
			'icon'	=> '',
			'label'	=> 'cancel',
			'status'=> $sts
		);

		$receipt = sprintf('%04d',$val['title']);

		$total = 0;
		$nom = $transaksi->get_orders($val['ID'],array('qty','price','discount'));
		foreach ($nom as $ky => $vl) {
			$total += $vl['qty'] * ($vl['price'] - $vl['discount']);
		}

		$customer = 'Pembeli';
		if($val['contact']!=0){
			$customer = $val['contact_name'];
		}
		
		$data['table'][$key]['tr'] = array('');
		$data['table'][$key]['td'] = array(
			'no'			=> array(
				'center',
				'5%',
				$no,
				true
			),
			'receipt'		=> array(
				'left',
				'10%',
				$receipt,
				true
			),
			'pelanggan'		=> array(
				'left',
				'auto',
				$customer,
				true
			),
			'type'			=> array(
				'left',
				'10%',
				$val['type_order'],
				true
			),
			'Pembayaran'	=> array(
				'left',
				'10%',
				$val['pay_name'],
				true
			),
			'nominal'		=> array(
				'right',
				'15%',
				'Rp. '.format_nominal($lang,$total),
				true
			),
			'tanggal'		=> array(
				'center',
				'15%',
				$val['order_date'],
				true
			),
			'status'		=> array(
				'center',
				'10%',
				$val['status']<=1?$val['var']:'cancel',
				true
			),
			'cancel'		=> array(
				'center',
				'5%',
				hapus_button($cancel),
				false
			),
			'view'			=> array(
				'center',
				'5%',
				edit_button($view),
				false
			)
			
		);
	}
	
	return $data;
}

function listOrder_layout($start){
	$data = listOrder_table($start);
	
	$box = array(
		'label'		=> 'Data account Sochick',
		'tool'		=> '',
		'action'	=> '',
		'func'		=> 'sobad_table',
		'data'		=> $data
	);
	
	$opt = array(
		'title'		=> listOrder_head_title(),
		'style'		=> '',
		'script'	=> array('')
	);
	
	return portlet_admin($opt,$box);
}

// ----------------------------------------------------------
// Form data list order -------------------------------------
// ----------------------------------------------------------

// ---- Form Get type_selling in Admin -------//

// ----------------------------------------------------------
// Function list order to database --------------------------
// ----------------------------------------------------------
function listOrder_pagination($idx){
	if($idx==0){
		die('');
	}
	
	$table = listOrder_table($idx);
	return table_admin($table);
}

function view_listOrder($id){
	$id = str_replace('view_','',$id);
	intval($id);
	
	$args = array('ID','barang','qty','price','discount','note');
	
	$transaksi = new sochick_transaksi();
	$q = $transaksi->get_orders($id,$args);
	
	if($q===0){
		return '';
	}
	
	return typeSelling_view_form($q);
}

function cancel_listOrder($id){
	$lang = constant('sochick_language');
	$id = str_replace('cancel_','',$id);
	intval($id);
	
	$args = array('status' => 2);
	
	$db = new sochick_order();
	$item = new sochick_item();
	$q = $db->_update_single($id,'soc-order',$args);
	$q = $db->_update_multiple("title='$id'",'soc-order',$args);
	
// Update saldo akun
	$ords = $db->get_paids(array('ID','payment'),"AND `soc-order`.title='$id'");
	$pay = $ords[0]['payment'];
	$saldo = $ords[0]['paid_cash'] - $ords[0]['paid_short'];

	$komisi = 0;
	$ords = $db->get_gratuities(array('ID','payment'),"AND `soc-order`.title='$id'");
	$check = array_filter($ords);
	if(!empty($check)){
		$komisi = $ords[0]['paid_cash'] - $ords[0]['paid_short'];
	}

	$saldo -= $komisi;
// GET stock item
	$ord = $db->get_order($id,array('ID','paid'));
	foreach ($ord as $ky => $val) {
		$stc = $item->get_item($val['paid_brng'],array('stock','note'));
		//$stc = $stc[0]['stock'] + $val['paid_qty'];

		$bahan = json_decode($stc[0]['note'],true);
		if(isset($bahan['bahan'])){
			sochick_set_stock($bahan,$val['paid_qty'],$val['paid_brng']); // Bahan pembuat
		}

		//$q = $db->_update_single($val['paid_brng'],'soc-item',array('stock'	=> $stc));
	}

	$akun = new sochick_account();
	$saldo_a = $akun->get_account($pay,array('balance')); // Get Saldo awal

	$q = ambil_saldo_account($pay,$saldo);

	// Buat History cashflow
	$saldo_b = $akun->get_account($pay,array('balance')); // Get Saldo Akhir
	$cash = array(
		'payment'		=> $saldo,
		'saldo_awal'	=> $saldo_a[0]['balance'],
		'saldo_akhir'	=> $saldo_b[0]['balance'],
		'account'		=> $pay,
		'note'			=> 'Reset Saldo dari Order (Komisi : '.format_nominal($lang,$komisi).')'
	);
	$flow = new sochick_cashflow();
	$flow = $flow->set_cancelOrder($id,$cash);

	if($q===0){
		return '';
	}
	
	$pg = isset($_POST['page'])?$_POST['page']:1;
	$table = listOrder_table($pg);
	return table_admin($table);
}