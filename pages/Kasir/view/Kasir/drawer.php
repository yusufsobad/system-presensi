<?php

function cash_drawer(){
	$vals = array(0);
	
	$args = array(
		'title'		=> 'Cash Drawer',
		'button'	=> '_btn_modal_save',
		'status'	=> array(
			'link'		=> 'update_cash_drawer',
			'load'		=> 'sobad_portlet'
		)
	);
	
	return cashDrawer_data_form($args,$vals);
}

function end_day(){
	$vals = array(0);
	
	$args = array(
		'title'		=> 'End Day',
		'button'	=> '_btn_modal_save',
		'status'	=> array(
			'link'		=> 'set_end_day',
			'load'		=> 'sobad_portlet'
		)
	);
	
	return cashDrawer_data_form($args,$vals);
}

function cashDrawer_data_form($args=array(),$vals=array()){
	$check = array_filter($args);
	if(empty($check)){
		return '';
	}
	
	$data = array(
		0 => array(
			'func'			=> 'opt_input',
			'type'			=> 'text',
			'key'			=> 'cash_drawer',
			'label'			=> 'Total Uang',
			'class'			=> 'input-circle money',
			'value'			=> $vals[0],
			'data'			=> 'placeholder="Uang" onkeydown="mask_money(\'.money\');"'
		)
	);
	
	$args['func'] = array('sobad_form');
	$args['data'] = array($data);
	
	return modal_admin($args);
}

function set_end_day($args=array()){
	$args = ajax_conv_json($args);
	$back = set_cash_drawer($args['cash_drawer']);

// -------------------------------------------------------
// Set End Day -------------------------------------------
// -------------------------------------------------------
	$db = new sochick_db();
	$od = new sochick_order();
	$err = new _error();

	$user = $_SESSION['sochick_user'];
	$id_usr = $_SESSION['sochick_id-user'];	

// Saldo Awal & Akun
	$q = $od->get_reconsiles(array('COUNT(ID) as cnt'),"AND status='0'",false);
	$q2 = $db->_select_table("WHERE locked='1'",'soc-account',array('balance'));
	
	$akun = array();$piutang = 0;$total = 0;
	if($q2!==0){
		$r2 = $q2[0];
		$saldo = $r2['balance'] * $q[0]['cnt'];
	}
	
// Jumlah Order Hari Ini
	$y = date('Y');$m = date('m');$d = date('d');
	$whr = "AND YEAR(order_date)='$y' AND MONTH(order_date)='$m' AND DAY(order_date)='$d'";	
	$q = $od->get_orders(array('MAX(title) AS ord'),$whr,false);
	$order = $q[0]['ord'];
	
// Get Reconsile Today
	$q = $od->get_reconsiles(array('ID'),"AND status='0'",false);
	
	$id = array();
	foreach($q as $ky => $vl){
		array_push($id,$vl['ID']);
	}
	$id = implode(',',$id);
	
	$db -> _update_multiple("ID IN ($id)",'soc-order',array('status' => 1));
	$q = $db->_select_table("WHERE reff IN ($id)",'soc-transaksi',array('price','note'));
	
	$total=0;$money=0;$short=0;$over=0;$cash=0;$virtual=0;$hutang=0;

	foreach ($q as $ky => $r){
		$dt = json_decode($r['note'],true);
		$money += $r['price'];
		$total += $dt['total'];
		$short += $dt['short'];
		$over += $dt['over'];
		$cash += $dt['cash'];
		$virtual += $dt['virtual'];
		
		if(isset($dt['hutang'])){
			$hutang += $dt['hutang'];
		}
	}

// Argument Data Print	
	$args = array(
		'saldo'		=> $saldo,
		'money'		=> $money,
		'user'		=> $user,
		'order'		=> $order,
		'total'		=> $total,
		'cash'		=> $cash,
		'piutang'	=> $virtual,
		'hutang'	=> $hutang,
		'account'	=> array(
			0	=> array(
				'name'	=> 'Begin',
				'value'	=> $saldo
			),
			1	=> array(
				'name'	=> 'Akun',
				'value'	=> ($cash + $virtual)
			)
		)
	);
	
	print_cash_drawer($args,'End Day'); // Print Out Kasir
	
	$data = array(
		'total'		=> $total,
		'short'		=> $short,
		'over'		=> $over,
		'cash'		=> $cash,
		'virtual'	=> $virtual,
		'hutang'	=> $hutang,
		'data'		=> explode(',',$id)
	);
	
	$data = json_encode($data);
	
// Insert data Reconsile -> Order
	$order = array(
		'title'		=> 1,
		'user'		=> $id_usr,
		'var'		=> 'end_day',
		'status'	=> 0,
	);

	$q = $db->_insert_table('soc-order',$order);

// Insert data Reconsile -> Transaksi
	$dt = array(
		'reff'		=> $q,
		'qty'		=> 1,
		'price'		=> $money,
		'note'		=> $data,
	);

	$q = $db->_insert_table('soc-transaksi',$dt);

// --------------------------------------
// Call Auto retur Stock ----------------
// --------------------------------------
	
	$item = new sochick_item();
	$item = $item->get_auto_retur(array('ID'));

	$check = array_filter($item);
	if(!empty($check)){
		foreach ($item as $ky => $val) {
			$re = set_retur_stock($val['ID']);
		}
	}

	if($q!==0){
		return dash_kasir();
	}

	return '';	
}

function update_cash_drawer($args=array()){
	$args = ajax_conv_json($args);
	$q = set_cash_drawer($args['cash_drawer']);
	
	if(empty($q)){
		$err = new _error();
		return $err->_alert_db('Tidak ada order hari ini!!!');
	}
	
	return $q;
}

function set_cash_drawer($money=0){
	$akun = new sochick_account();
	$db = new sochick_db();
	$od = new sochick_order();
	$err = new _error();

	$money = intval(str_replace('.', '', $money));
	$user = $_SESSION['sochick_user'];
	$id_usr = $_SESSION['sochick_id-user'];

// Jumlah Order Shift Ini
	$y = date('Y');$m = date('m');$d = date('d');
	$whr = "AND YEAR(order_date)='$y' AND MONTH(order_date)='$m' AND DAY(order_date)='$d'";
	$q = $od->get_orders(array('ID'),"AND status='0' AND user='$id_usr' ".$whr,false);

	$check = array_filter($q);
	if(empty($check)){
		return '';
	}

	$order = count($q);

// Jumlah Akun
	$ords = array();
	foreach ($q as $ky => $vl) {
		$ords[] = $vl['ID'];
	}
	$ords = implode(',', $ords);

	$q = $od->get_paids(array('payment','status'),"AND `soc-order`.title IN ($ords)");
	$q2 = $akun->get_accounts(array('ID','name','balance'),"AND locked='1'");

	$akun = array();$piutang = 0;$total = 0;$saldo = 0;
	$check = array_filter($q2);
	if(!empty($check)){
		$r2 = $q2[0];

		$akun[$r2['ID']] = array(
			'name'	=> $r2['name'],
			'value'	=> $r2['balance']
		);

		$total = $r2['balance'];
		$saldo = $r2['balance'];
	}

	$piutang = 0;$cash = 0;$hutang = 0;
	$check = array_filter($q);
	if(!empty($check)){
		foreach ($q as $ky => $r){
			$idx = $r['payment'];
			if(!isset($akun[$idx])){
				$value=0;
				$akun[$idx] = array();
			}else{
				$value = $akun[$idx]['value'];	
			}

			$paid = $r['paid_cash'];
			$chg = intval($r['paid_short']);

			if($chg>0){
				$paid -= $chg;
			}else{
				$hutang += $chg;
			}

			if($r['pay_type']==1){
				$piutang += $paid;
			}else{
				$cash += $paid;
			}

			$total += $paid;
			$value += $paid;
			
			$akun[$idx]['name'] = $r['pay_name'];
			$akun[$idx]['value'] = $value;
		}

	}

// Argumentasi Data Transaksi
	$args = array(
		'saldo'		=> $saldo, // patty cash
		'money'		=> $money, // count input
		'user'		=> $user, // admin kasir
		'order'		=> $order, // jumlah order
		'total'		=> $total, // total cash + virtual
		'cash'		=> $cash, // total cash
		'piutang'	=> $piutang, // total virtual
		'hutang'	=> -1*$hutang,
		'account'	=> $akun
	);

	print_cash_drawer($args,'Cash Drawer'); // Print Out Kasir

// ------------------------------------------------
// Update Sistem; ---------------------------------
// ------------------------------------------------
	$chk = $money-$cash;
	if($chk<0){
		$sht = -1*$chk;
		$ovr = 0;
	}else{
		$ovr = $chk;
		$sht = 0;
	}

	$data = array(
		'total'		=> $total,
		'short'		=> $sht,
		'over'		=> $ovr,
		'cash'		=> $cash,
		'virtual'	=> $piutang,
		'hutang'	=> -1*$hutang,
		'data'		=> array()
	);

	$ord = $db->_select_table("WHERE var='order' AND status='0' AND user='$id_usr'",'soc-order',array('ID'));

	foreach ($ord as $ky => $val) {
		$data['data'][$ky] = $val['ID'];
	}
	$data = json_encode($data);

// Insert data Reconsile -> Order
	$limit = "AND YEAR(order_date)='$y' AND MONTH(order_date)='$m' AND DAY(order_date)='$d'";
	$q = $od->get_reconsiles(array("COUNT(ID) AS cnt"),$limit,false);

	$order = array(
		'title'		=> $q[0]['cnt'] + 1,
		'user'		=> $id_usr,
		'var'		=> 'reconsile',
		'status'	=> 0,
	);

	$q = $db->_insert_table('soc-order',$order);

// Insert data Reconsile -> Transaksi
	$dt = array(
		'reff'		=> $q,
		'qty'		=> 1,
		'price'		=> $money,
		'note'		=> $data,
	);

	$q = $db->_insert_table('soc-transaksi',$dt);

// Update status order	-> Reconsile
	$q = $db->_update_multiple("var='order' AND status='0' AND user='$id_usr'",'soc-order',array('status' => '1'));

	if($q!==0){
		return dash_kasir();
	}

	return '';
}

function print_cash_drawer($args=array(),$title=''){
	$check = array_filter($args);
	if(empty($check)){
		$args = array(
			'saldo'		=> 0,
			'money'		=> 0,
			'user'		=> 'Admin',
			'order'		=> 1,
			'total'		=> 0,
			'cash'		=> 0,
			'piutang'	=> 0,
			'hutang'	=> 0,
			'account'	=> array(
				0	=> array(
					'name'	=> 'Cash',
					'value'	=> 0
				)
			)
		);
	}

	$order = $args['order'];
	$total = number_format($args['total'],0,',','.');
	$piutang = number_format($args['piutang'],0,',','.');
	$hutang = number_format($args['hutang'],0,',','.');

	$sht = 'over';
	$short = $args['money'] - $args['cash'];
	if($short<0){
		$sht = 'short';
		$short = -1*$short;
	}

	$money = number_format($args['money'],0,',','.');
	$short = number_format($short,0,',','.');

	// max 40 karakter;
	$mx = 40;
	$def = "%-".$mx.".".$mx."s";
	$rcp = "%-".($mx-18).".".($mx-18)."s";
	$def2 = "%".$mx.".".$mx."s";
	$nt = "%-".($mx-10).".".($mx-10)."s";

	ob_start();
		printf($def,$title);
		printf($def,' ');
		printf($def,'Solo Chicken');
		printf($def,'Jl. Mr. Sartono No. 2A');
		printf($def,'Cengklik, Nusukan');
		printf($def,'085 103 000 848');
		printf("%'-".$mx."s",'');
		printf("%-14.14s",'Total Order : ').printf("%04d",$order).printf($rcp,' ');
		printf($def,'Date/Time   : '.date('Y-m-d H:i'));
		printf($def,'Cashier     : '.$args['user']);
		printf($def,' ');
		printf($def,'Accounts');
		printf($def,' ');

		foreach ($args['account'] as $key => $val) {
			$saldo = number_format($val['value'],0,',','.');
			printf("%-".($mx-10).".".($mx-10)."s",$val['name']).printf("%10.10s",$saldo);
		}

		printf($def2,'---------------');
		printf($nt,'Total   ').printf("%10.10s",$total);
		printf($nt,'Piutang ').printf("%10.10s",$piutang);
		printf($nt,'Count ').printf("%10.10s",$money);
		printf($def,' ');
		printf($nt,$sht.' ').printf("%10.10s",$short);
		printf($def,' ');
		printf($nt,'Hutang ').printf("%10.10s",$hutang);
		printf($def,' ');
		printf($def,' ');
		printf($def,' ');
		printf($def,' ');
		printf($def,' ');
		printf($def,' ');
		printf($def,' ');
 	
 	$txt = ob_get_clean();

	soc_print_out($txt);
}