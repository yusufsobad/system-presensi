<?php

function absensi_head_title(){
	$args = array(
		'title'	=> 'Absensi <small>data absensi</small>',
		'link'	=> array(
			0	=> array(
				'func'	=> 'absensi_admin',
				'label'	=> 'absensi'
			)
		),
		'date'	=> false
	);
	
	return $args;
}

// ----------------------------------------------------------
// Layout category  ------------------------------------------
// ----------------------------------------------------------
function absensi_kasir(){
	return absensi_layout(1);
}

function absensi_table($start=1,$search=false,$cari=array()){
	$data = array();
	$args = array('ID','name');
	
	$kata = '';$where = '';
	if($search){
		$src = like_pencarian($args,$cari);
		$cari = $src[0];
		$where = $src[0];
		$kata = $src[1];
	}else{
		$cari='';
	}
	
	$limit = 'LIMIT '.intval(($start - 1) * 10).',10';
	$where .= $limit;
	$user = new sochick_user();
	$args = $user->get_users($args,$where);
	$sum_data = $user->get_users(array('ID'),$cari);
	
	$data['data'] = array('search_absensi',$kata);
	$data['search'] = array('Semua','nama','no. HP');
	$data['class'] = '';
	$data['table'] = array();
	$data['page'] = array(
		'func'	=> '_pagination',
		'data'	=> array(
			'start'		=> $start,
			'qty'		=> count($sum_data),
			'limit'		=> 10,
			'func'		=> 'absensi_pagination'
		)
	);
	
	$date = date("Y-m-d");
	foreach($args as $key => $val){
		$log = $user->check_user_log($date,$val['ID']);

		$check = array_filter($log);
		if(empty($check)){
			$sts = 0;
		}else{
			$sts = $log[0]['status'];
		}

		$status = '';
		switch ($sts) {
			case 0:
				$check = array(
					'ID'	=> 'check_'.$val['ID'],
					'func'	=> 'checkIn_absensi',
					'color'	=> 'green',
					'icon'	=> 'fa fa-sign-in',
					'label'	=> 'Masuk',
					'script'=> 'sochick_check_button(this,'.$val['ID'].')'
				);

				$disable = '';$shft=1;$as_log=1;
				$check = hapus_button($check);
				break;

			case 1:
				$check = array(
					'ID'	=> 'check_'.$log[0]['ID'],
					'func'	=> 'checkOut_absensi',
					'color'	=> 'red',
					'icon'	=> 'fa fa-sign-out',
					'label'	=> 'Pulang',
					'script'=> 'sochick_check_button(this,'.$val['ID'].')'
				);

				$disable = 'disabled';$shft=$log[0]['shift'];$as_log=$log[0]['note'];
				$check = hapus_button($check);
				$status = 'Masuk  : '.$log[0]['date_in'];
				break;

			case 2:
				$disable = 'disabled';$shft=$log[0]['shift'];$as_log=$log[0]['note'];
				$check = '';
				$status = '
					Masuk  : '.$log[0]['date_in'].'<br>
					Pulang : '.$log[0]['date_out'];
				break;
			
			default:
				$check = '';
				break;
		}

		$shift = '<div class="checkbox-list">';
		for($i=1;$i<=3;$i++){
			$chk = '';
			if($i==$shft){
				$chk = 'checked=""';
			}

			$shift .= '
				<label>
					<input type="radio" name="shift_'.$val['ID'].'" value="'.$i.'" '.$chk.' onclick="change_status_user(\'shift\','.$val['ID'].','.$i.')" '.$disable.'>
					Shift '.$i.'
				</label>
			';
		}
		$shift .= '</div>';

		$as_arr = array('Kasir','Waitres','Koki','Admin');
		$_as = '<div class="checkbox-list">';
		for($i=1;$i<=4;$i++){
			$chk = '';
			if($i==$as_log){
				$chk = 'checked=""';
			}

			$_as .= '
				<label>
					<input id="inp_as'.$i.'" type="radio" name="as_login_'.$val['ID'].'" value="'.$i.'" '.$chk.' onclick="change_status_user(\'login\','.$val['ID'].','.$i.')" '.$disable.'>
					'.$as_arr[$i-1].'
				</label>
			';
		}
		$_as .= '</div>';
		
		$data['table'][$key]['tr'] = array('');
		$data['table'][$key]['td'] = array(
			'Nama'			=> array(
				'left',
				'auto',
				$val['name'],
				true
			),
			'Shift'			=> array(
				'left',
				'20%',
				$shift,
				true
			),
			'As'			=> array(
				'left',
				'20%',
				$_as,
				true
			),
			'Check'			=> array(
				'center',
				'10%',
				$check,
				false
			),
			'Status'		=> array(
				'left',
				'20%',
				$status,
				true
			),
		);
	}
	
	return $data;
}

function absensi_layout($start){
	$data = absensi_table($start);
	
	$box = array(
		'label'		=> 'Absensi Sochick',
		'tool'		=> '',
		'action'	=> '',
		'func'		=> 'sobad_table',
		'data'		=> $data
	);
	
	$opt = array(
		'title'		=> absensi_head_title(),
		'style'		=> '',
		'script'	=> array('script_absensi')
	);
	
	return portlet_admin($opt,$box);
}

function checkIn_absensi($id){
	return check_absensi($id,1);
}

function checkOut_absensi($id){
	return check_absensi($id,2);
}

function check_absensi($id,$check){
	$id = str_replace('check_', '', $id);
	$args = $_POST['args'];
	$args = json_decode($args,true);

	$data = array(
		'user'		=> $id,
		'shift'		=> $args['shift'],
		'note'		=> $args['login'],
		'status'	=> $check,
		'date_in'	=> date("Y-m-d H:i:s")
	);

	$out = array(
		'status'	=> $check,
		'date_out'	=> date("Y-m-d H:i:s")
	);

	$db = new sochick_db();
	if($check==1){
		$q = $db->_insert_table('soc-user-log',$data);
	}else{
		$q = $db->_update_single($id,'soc-user-log',$out);
	}

	if($q!==0){
		$table = absensi_table();
		return table_admin($table);
	}
}

function script_absensi(){
	$user = new sochick_user();
	$args = $user->get_users(array('ID','name'),'');

	$user = array();
	foreach ($args as $ky => $val) {
		$user[$val['ID']] = array(
			'ID'	=> $val['ID'],
			'shift'	=> 1,
			'login'	=> 1
		);
	}

	$user = json_encode($user);
	?>
		<script>
			var user = <?php print($user) ;?>;

			function change_status_user(key,id,val){
				user[id][key] = val;
			}

			function sochick_check_button(val,idx){
				var id = $(val).attr('data-load');
				var ajx = $(val).attr("data-sobad");
				var lbl = $(val).attr('id');

				// loading	
				var html = $(val).html();
				if($(val).attr('href')!='#myModal' && $(val).attr('href')!='#myModal2'){
					$(val).html('<i class="fa fa-spinner fa-spin"></i>');
					$(val).attr('disabled','');
				}

				data = JSON.stringify(user[idx]);
				data = "ajax="+ajx+"&data="+lbl+"&args="+data;
				sobad_ajax('#'+id,data,'html',false,val,html);
			}

		</script>
	<?php
}