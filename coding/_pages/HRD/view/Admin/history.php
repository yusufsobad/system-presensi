<?php

class history_absen extends _page{

	protected static $object = 'history_absen';

	protected static $table = 'sobad_logDetail';

	// ----------------------------------------------------------
	// Layout category  ------------------------------------------
	// ----------------------------------------------------------

	protected function _where($now=''){


		$status = str_replace('history_', '', parent::$type);
		intval($status);

		$status = $status==0?1:$status;
		$whr = '';
		if($status==1){
			$whr = "AND (`abs-log-detail`.log_history LIKE '%$now%') ";
		}

		if($status==2){
			$whr = "AND (`abs-log-detail`.date_schedule LIKE '%$now%') ";
		}

		if($status==3){
			$rangeD = report_absen::get_range($now);
			$sDate = $rangeD['start_date'];
			$fDate = $rangeD['finish_date'];

			$whr = "AND (`abs-log-detail`.date_schedule BETWEEN '$sDate' AND '$fDate') ";
		}

		$where = "AND `abs-log-detail`.type_log='$status' $whr";

		return $where;
	}

	protected function table($now=''){
		$now = empty($now)?date('Y-m'):$now;

		if(parent::$type=='history_4'){
			return self::table_reward($now);
		}

		$data = array();
		$args = array();

		$start = intval(parent::$page);
		$nLimit = intval(parent::$limit);
		
		$where = self::_where($now);

		$kata = '';
		if(parent::$search){
			$_args = array('ID','log_id');
			$src = parent::like_search($_args,$where);	
			$cari = $src[0];
			$where = $src[0];
			$kata = $src[1];
		}else{
			$cari=$where;
		}
	
		$limit = 'ORDER BY date_schedule DESC ';//'LIMIT '.intval(($start - 1) * $nLimit).','.$nLimit;
		$where .= $limit;

		$object = self::$table;
		$args = $object::get_all($args,$where);
		//$sum_data = $object::count("1=1 ".$cari);
		
		$data['data'] = array('data' => $kata, 'type' => parent::$type);
		$data['search'] = array('Semua','nama');
		$data['class'] = '';
		$data['table'] = array();

		$users = array();
		foreach($args as $key => $val){
			$idx = $val['user_log_'];

			if(empty($val['name_user'])){
				$hist = unserialize($val['log_history']);
				if(isset($hist['user'])){
					$idx = $hist['user'];
					$guser = sobad_user::get_id($hist['user'],array('name'));
					$val['name_user'] = $guser[0]['name'];
				}
			}

			if(isset($users[$idx])){
				$user[$idx] = array();
			}

			$users[$idx][] = $val;
		}

		$no = ($start-1) * $nLimit;
		foreach($users as $key => $val){
			$no += 1;

			$lama = 0;
			$status = 1;
			$total = 0;
			foreach ($val as $_key => $_val) {
				switch ($_val['status']) {
					case 1:
						if($status!=1){
							$status = 2;
						}else{
							$status = 1;
						}
						break;
					
					default:
						$status = $_val['status'];
						break;
				}

				$lama += $_val['times'];

				if(self::$type=='history_3'){
					$holiday = holiday_absen::_check_holiday($_val['date_schedule']);
					if($holiday){
						$total += ($_val['times'] * 2);
					}else{
						if($_val['times']<=2){
							$total += $_val['times'] * 1.5;
						}else{
							$total += ($_val['times'] * 2) - 1;
						}
					}
				}else{
					$total = $lama;
				}
			}

			$nm_user = $val[0]['name_user'];

			if(self::$type!='history_3'){
				$total = round($total / 60,1);
			}

			switch ($status) {
				case 0:
					$status = '#666;';
					break;

				case 1:
					$status = '#26a69a;';
					break;

				case 2:
					$status = '#f5b724;';
					break;
				
				default:
					$status = '#fff;';
					break;
			}

			$status = '<i class="fa fa-circle" style="color:'.$status.'"></i>';

			$_history = array(
				'ID'	=> 'history_'.$key,
				'func'	=> '_history',
				'color'	=> 'yellow',
				'icon'	=> 'fa fa-eye',
				'label'	=> 'History',
				'type'	=> self::$type.'#'.$now
			);

			$data['table'][$no - 1]['tr'] = array('');
			$data['table'][$no - 1]['td'] = array(
				'No'			=> array(
					'center',
					'5%',
					$no,
					true
				),
				'Name'			=> array(
					'left',
					'auto',
					$nm_user,
					true
				),
				'Lama'	=> array(
					'right',
					'15%',
					$lama .' Jam',
					true
				),
				'Total'	=> array(
					'right',
					'15%',
					$total .' Jam',
					true
				),
				'Status'		=> array(
					'center',
					'10%',
					$status,
					true
				),
				'History'		=> array(
					'center',
					'10%',
					_modal_button($_history),
					true
				),
			);

			if(self::$type=='history_3'){
				unset($data['table'][$no-1]['td']['Status']);
				//unset($data['table'][$key]['td']['History']);
			}else{
				unset($data['table'][$no-1]['td']['Lama']);
			}
		}

		return $data;
	}

	protected function table_reward($now=''){
		$now = empty($now)?date('Y-m'):$now;
		$now = strtotime($now);
		$now = date('Y-m',$now);

		$data = array();
		$args = array('ID','name','divisi','work_time');

		$start = intval(parent::$page);
		$nLimit = intval(parent::$limit);
		
		$status = str_replace('history_', '', parent::$type);
		intval($status);

		$kata = '';$where = "AND status NOT IN ('0','7')";
		if(parent::$search){
			$src = parent::like_search($args,$where);	
			$cari = $src[0];
			$where = $src[0];
			$kata = $src[1];
		}else{
			$cari=$where;
		}
	
		$limit = '';
		$where .= $limit;

		$args = sobad_user::get_all($args,$where);
		//$sum_data = $object::count("1=1 ".$cari);
		
		$data['data'] = array('data' => $kata, 'type' => parent::$type);
		$data['search'] = array('Semua','nama');
		$data['class'] = '';
		$data['table'] = array();

		$_users = array();$users = array();
		foreach($args as $key => $val){
			$_users[] = $val['ID'];
			$users[$val['ID']] = array(
				'name'		=> $val['name'],
				'divisi'	=> $val['meta_value_divi'],
				'worktime'	=> $val['name_work'],
			);
		}

		$default = $now.'-01';
		$default = strtotime($default);
		$rangeD = report_absen::get_range($now);

		if($rangeD['finish_month'] == date('m') && $rangeD['finish_year'] == date('Y')){
			if($rangeD['finish_day']>date('d')){
				$rangeD['finish_day'] = date('d');
			}
		}

		if($rangeD['start_month'] == date('m') && $rangeD['start_year'] == date('Y')){
			if($rangeD['start_day']<=date('d')){
				$rangeD['finish_day'] = $rangeD['number_day'] + (date('d') - $rangeD['start_day']);
			}
		}

		for($i=$rangeD['number_day'];$i<=$rangeD['finish_day'];$i++){
			$_now = date('Y-m-d',strtotime($i.' days',$default));
			$_day = date('w',strtotime($i.' days',$default));
			$holiday = holiday_absen::_check_holiday($_now);
			if($holiday){
				continue;
			}

			// Check absen masuk
			$user = implode(',', $_users);
			$_users = array();

			if(empty($user)){
				break;
			}

			$check = sobad_user::get_logs(array('user','shift','type','time_in'),"_inserted='$_now' AND user IN ($user)");
			foreach ($check as $key => $val) {
				$work = sobad_work::get_id($val['shift'],array('time_in'),"AND days='$_day'");
				$work = $work[0]['time_in'];
				$time_in = _calc_time($work,'-15 minutes');

				if(in_array($val['type'],array('1','2')) && $val['time_in']<=$time_in){
					$_users[] = $val['user'];
				}
			}
		}

		$no = ($start-1) * $nLimit;
		foreach($_users as $key => $val){
			$no += 1;

			$_history = array(
				'ID'	=> 'history_'.$val,
				'func'	=> '_historyReward',
				'color'	=> 'yellow',
				'icon'	=> 'fa fa-eye',
				'label'	=> 'History',
				'type'	=> self::$type
			);

			$data['table'][$key]['tr'] = array('');
			$data['table'][$key]['td'] = array(
				'No'			=> array(
					'center',
					'5%',
					$no,
					true
				),
				'Name'			=> array(
					'left',
					'auto',
					$users[$val]['name'],
					true
				),
				'Jabatan'		=> array(
					'center',
					'15%',
					$users[$val]['divisi'],
					true
				),
				'Jam Kerja'		=> array(
					'center',
					'15%',
					$users[$val]['worktime'],
					true
				),
				'History'		=> array(
					'center',
					'10%',
					_modal_button($_history),
					true
				),
			);
		}

		return $data;
	}

	private function head_title(){
		$args = array(
			'title'	=> 'History <small>data history</small>',
			'link'	=> array(
				0	=> array(
					'func'	=> self::$object,
					'label'	=> 'history'
				)
			),
			'date'	=> false,
			'modal'	=> 3
		); 
		
		return $args;
	}

	protected function get_box(){
		$data = self::table();
		
		$type = str_replace("history_", '', parent::$type);
		switch ($type) {
			case 1:
				$label = 'Punishment';
				break;

			case 2:
				$label = 'Ganti Jam';
				break;

			case 3:
				$label = 'Lembur';
				break;

			case 4:
				$label = 'Reward';
				break;
			
			default:
				$label = 'Punishment';
				break;
		}

		$action = self::action();
		$action .= in_array($type,array('2','3'))?self::action2():'';
		$action .= in_array($type,array('3','4'))?self::action3():'';

		$box = array(
			'label'		=> 'History '.$label,
			'tool'		=> '',
			'action'	=> $action,
			'func'		=> 'sobad_table',
			'data'		=> $data
		);

		return $box;
	}

	protected function layout(){
		self::$type = 'history_1';
		$box = self::get_box();

		$tabs = array(
			'tab'	=> array(
				0	=> array(
					'key'	=> 'history_1',
					'label'	=> 'Punishment',
					'qty'	=> ''
				),
				1	=> array(
					'key'	=> 'history_2',
					'label'	=> 'Ganti Jam',
					'qty'	=> ''
				),
				2	=> array(
					'key'	=> 'history_3',
					'label'	=> 'Lembur',
					'qty'	=> ''
				),
				3	=> array(
					'key'	=> 'history_4',
					'label'	=> 'Reward',
					'qty'	=> ''
				)
			),
			'func'	=> '_portlet',
			'data'	=> $box
		);
		
		$opt = array(
			'title'		=> self::head_title(),
			'style'		=> array(''),
			'script'	=> array(self::$object,'_script')
		);

		return tabs_admin($opt,$tabs);
	}

	protected static function action(){
		$type = self::$type;
		$date = date('Y-m');
		ob_start();
		?>
			<div style="display: inline-flex;" class="input-group input-medium date date-picker" data-date-format="yyyy-mm" data-date-viewmode="months">
				<input id="monthpicker" type="text" class="form-control" value="<?php print($date); ?>" data-sobad="_filter" data-load="sobad_portlet" data-type="<?php print($type) ;?>" name="filter_date" onchange="sobad_filtering(this)">
			</div>
			<script type="text/javascript">
				if(jQuery().datepicker) {
		            $("#monthpicker").datepicker( {
					    format: "yyyy-mm",
					    viewMode: "months", 
					    minViewMode: "months",
					    rtl: Metronic.isRTL(),
			            orientation: "right",
			            autoclose: true
					});
		        };
			</script>
		<?php
		$date = ob_get_clean();	
		
		return $date;
	}

	protected function action2(){
		$manual = array(
			'ID'	=> 'manual_0',
			'func'	=> '_manual',
			'color'	=> 'btn-default',
			'icon'	=> 'fa fa-gear',
			'label'	=> 'Manual',
			'type'	=> parent::$type
		);

		return _modal_button($manual);
	}

	protected function action3(){
		$print = array(
			'ID'	=> 'preview_0',
			'func'	=> '_preview',
			'color'	=> 'btn-default',
			'icon'	=> 'fa fa-print',
			'label'	=> 'Print',
			'type'	=> parent::$type
		);	

		return print_button($print);
	}

	public function _script(){
		?>
			<script type="text/javascript">
				function print_gantijam(val){
					var pre = $(val).attr('data-sobad');
					var tp = $(val).attr('data-type');

					var arr = [];
					$("input:checkbox[name=checked_ids]:checked").each(function(){
					    arr.push($(this).val());
					});

					arr = {"ID":$(val).attr('id'),"data":arr};
					var data = JSON.stringify(arr);
					data = "page="+pre+"&object="+object+"&data="+data+"&type="+tp+"&filter="+filter;

			        sobad_preview(url_preview,data,'');
			    }
			</script>
		<?php
	}

// ------------------------------------------------------------
// Form History -----------------------------------------------
// ------------------------------------------------------------	

	public function _filter($date=''){
		ob_start();
		self::$type = $_POST['type'];
		$table = self::table($date);
		metronic_layout::sobad_table($table);
		return ob_get_clean();
	}

	public function _manual($id=0){
		$id = str_replace('manual_', '', $id);
		$vals = array($id,'');

		if($_POST['type']=='history_3'){
			return self::_manual_lembur($id);
		}
		
		$args = array(
			'title'		=> 'Tambah aktifitas ganti jam (Manual)',
			'button'	=> '_btn_modal_save',
			'status'	=> array(
				'link'		=> '_add_manual',
				'load'		=> 'sobad_portlet',
				'type'		=> $_POST['type']
			)
		);
		
		return punishment_absen::_manual_form($args,$vals);
	}

	public function _manual_lembur($id=0){
		$vals = array($id,date('Y-m-d'),0,0,'');
		if($id!=0){
			$logs = sobad_logDetail::get_id($id,array('log_id','date_schedule','times','log_history'));
			$logs = $logs[0];

			$history = unserialize($logs['log_history']);
			if(isset($history['note'])){
				$vals[4] = $history['note'];
			}

			$vals[1] = $logs['date_schedule'];
			$vals[2] = $logs['name_user'];
			$vals[3] = $logs['times'];
			$vals[5] = $logs['user_log_'];

			if(empty($vals[2])){
				if(isset($history['user'])){
					$guser = sobad_user::get_id($history['user'],array('name'));
					$vals[2] = $guser[0]['name'];
					$vals[5] = $history['user'];
				}
			}
		}

		$args = array(
			'title'		=> 'Tambah aktifitas Lembur (Manual)',
			'button'	=> '_btn_modal_save',
			'status'	=> array(
				'link'		=> '_add_lembur',
				'load'		=> 'hist_table_portlet',
				'type'		=> $_POST['type']
			)
		);
		
		return self::_lembur_form($args,$vals);
	}

	public function _lembur_form($args=array(),$vals=array()){
		$check = array_filter($args);
		if(empty($check)){
			return '';
		}

		$user = sobad_user::get_employees(array('ID','name'));
		$user = convToOption($user,'ID','name');

		$data = array(
			0 => array(
				'func'			=> 'opt_hidden',
				'type'			=> 'hidden',
				'key'			=> 'ID',
				'value'			=> $vals[0]
			),
			array(
				'func'			=> 'opt_input',
				'type'			=> 'date',
				'key'			=> 'date',
				'label'			=> 'Tanggal',
				'class'			=> 'input-circle',
				'value'			=> $vals[1],
				'data'			=> 'placeholder="Tanggal"'
			),
			array(
				'func'			=> 'opt_select_tags',
				'data'			=> $user,
				'key'			=> 'user',
				'label'			=> 'Nama',
				'class'			=> 'input-circle',
				'select'		=> array()
			),
			array(
				'func'			=> 'opt_input',
				'type'			=> 'price',
				'key'			=> '_time',
				'label'			=> 'Waktu (Jam)',
				'class'			=> 'input-circle',
				'value'			=> $vals[3],
				'data'			=> 'placeholder="Waktu"'
			),
			array(
				'func'			=> 'opt_input',
				'type'			=> 'text',
				'key'			=> 'note',
				'label'			=> 'Catatan',
				'class'			=> 'input-circle',
				'value'			=> $vals[4],
				'data'			=> 'placeholder="ngapain?"'
			),
		);

		if($vals[0]!=0){
			$data[2] = array(
				'func'			=> 'opt_input',
				'type'			=> 'text',
				'key'			=> 'name',
				'label'			=> 'Name',
				'class'			=> 'input-circle',
				'value'			=> $vals[2],
				'data'			=> 'placeholder="Name" disabled'
			);

			$data[5] = array(
				'func'			=> 'opt_hidden',
				'type'			=> 'hidden',
				'key'			=> 'user',
				'value'			=> $vals[5]
			);
		}
		
		$args['func'] = array('sobad_form');
		$args['data'] = array($data);
		
		return modal_admin($args);
	}

// --------------------------------------------------------------
// Form Ganti Jam -----------------------------------------------
// --------------------------------------------------------------
	public function _editGantiJam($_data=0){
		$_data = str_replace('edit_', '', $_data);
		$_data = explode('_', $_data);
		
		$args = array(
			'title'		=> 'Edit ganti jam',
			'button'	=> '_btn_modal_save',
			'status'	=> array(
				'link'		=> '_update_gantiJam',
				'load'		=> 'here_modal2'
			)
		);
		
		return self::_gantiJam_form($args,$_data);
	}

	protected static function _gantiJam_form($args=array(),$vals=array()){
		$check = array_filter($args);
		if(empty($check)){
			return '';
		}

		$logs = sobad_logDetail::get_id($vals[0],array('log_history'));
		$logs = unserialize($logs[0]['log_history']);
		$logs = $logs['history'];

		$_data = $logs[$vals[1]];

		$data = array(
			0 => array(
				'func'			=> 'opt_hidden',
				'type'			=> 'hidden',
				'key'			=> '_ID',
				'value'			=> $vals[0]
			),
			array(
				'func'			=> 'opt_hidden',
				'type'			=> 'hidden',
				'key'			=> '_key',
				'value'			=> $vals[1]
			),
			array(
				'func'			=> 'opt_input',
				'type'			=> 'date',
				'key'			=> 'date',
				'label'			=> 'Tanggal',
				'class'			=> 'input-circle',
				'value'			=> $_data['date'],
				'data'			=> 'placeholder="Tanggal"'
			),
			array(
				'func'			=> 'opt_input',
				'type'			=> 'price',
				'key'			=> 'time',
				'label'			=> 'Waktu (menit)',
				'class'			=> 'input-circle',
				'value'			=> $_data['time'],
				'data'			=> 'placeholder="Waktu"'
			),
			array(
				'func'			=> 'opt_input',
				'type'			=> 'text',
				'key'			=> 'note',
				'label'			=> 'Catatan',
				'class'			=> 'input-circle',
				'value'			=> $_data['note'],
				'data'			=> 'placeholder="ngapain?"'
			),
		);
		
		$args['func'] = array('sobad_form');
		$args['data'] = array($data);
		
		return modal_admin($args);
	}

	public function _update_gantiJam($args=array()){
		$args = sobad_asset::ajax_conv_json($args);
		$_idx = $args['_ID'];
		$_key = $args['_key'];

		$logs = sobad_logDetail::get_id($args['_ID'],array('times','date_actual','log_history'));
		$history = unserialize($logs[0]['log_history']);
		$actual = explode(',', $logs[0]['date_actual']);
	
	//Update tanggal aktual		
		$count = count($actual);
		$count2 = count($history['history']);

		$i = $count - $count2;
		$actual[$_key+$i] = $args['date'];

	//Calculasi waktu
		$extime = $history['extime'];
		$waktuA = $history['history'][$_key]['time'];
		$waktuB = $args['time'];

		$reset = $waktuA + $extime;
		$waktuT = $waktuB - $waktuA;
		$extime -= $waktuT;

		if($extime<=0){
			$extime = 0;
			$status = 1;

			$args['time'] = $reset;
		}else{
			$status = 2;
		}


	//Update History
		$history['history'][$_key]['date'] = $args['date']; //tanggal
		$history['history'][$_key]['time'] = $args['time']; //waktu
		$history['history'][$_key]['note'] = $args['note']; //kerjaan

		$history['extime'] = $extime;

	//Update extime tiap history
		$_times = $logs[0]['times'];
		foreach ($history['history'] as $key => $val) {
			$_waktu = $val['time'];
			$_times -= $_waktu;

			$history['history'][$key]['extime'] = $_times;
		}	

		$history = serialize($history);
		$actual = implode(',', $actual);

		$q = sobad_db::_update_single($_idx,'abs-log-detail',array(
			'date_actual'		=> $actual,
			'log_history'		=> $history,
			'status'			=> $status
		));

		if($q!==0){
			return self::_historyDetail($_idx);
		}
	}

// --------------------------------------------------------------
// Database -----------------------------------------------------
// --------------------------------------------------------------
	public function _history($id=0){
		$table = self::_historyTable($id);
		$print = array(
			'ID'	=> 'print_'.$id,
			'func'	=> '_print',
			'color'	=> 'btn-default',
			'icon'	=> 'fa fa-print',
			'label'	=> 'Print',
			'script'=> 'print_gantijam(this)'
		);

		$type = explode('#', $_POST['type']);
		$type = str_replace("history_", '', $type[0]);
		switch ($type) {
			case 1:
				$label = 'Punishment';
				break;

			case 2:
				$label = 'Ganti Jam';
				break;

			case 3:
				$label = 'Lembur';
				break;

			case 4:
				$label = 'Reward';
				break;
			
			default:
				$label = 'Punishment';
				break;
		}

		$data = array(
			'ID'		=> 'hist_table_portlet',
			'label'		=> $label,
			'tool'		=> '',
			'action'	=> print_button($print),
			'func'		=> 'sobad_table',
			'data'		=> $table
		);

		$args = array(
			'id'		=> 'history_portlet',
			'title'		=> 'History ',
			'button'	=> '_btn_modal_save',
			'status'	=> array(),
			'func'		=> array('_portlet'),
			'data'		=> array($data)
		);
		
		return modal_admin($args);
	}

	public function _historyTable($id=0){
		$id = str_replace('history_', '', $id);
		intval($id);

		$_type = $_POST['type'];
		$_type = explode('#', $_type);

		parent::$type = $_type[0];
		$where = self::_where($_type[1]);

		if($_type[0]=='history_3'){
			$whr = "AND (_log_id.user='$id' OR `abs-log-detail`.log_id='0') ";
		}else{
			$whr = "AND _log_id.user='$id' ";
		}

		$history = sobad_logDetail::get_all(array(),$whr.$where);

		$data = array();
		$data['class'] = '';
		$data['table'] = array();

		$no = 0;
		foreach ($history as $key => $val) {
			$no += 1;

			$lama = '';
			$note = ($val['type_log']==3)?'Jam':'Menit';

			$date = date($val['_inserted_log_']);
			$date = strtotime($date);
			$days = date('w',$date);

			$work = sobad_work::get_workTime($val['ID_shif'],"AND `abs-work-normal`.days='$days'");
			$worktime = format_time_id($work[0]['time_in']).' - '.format_time_id($work[0]['time_out']);

			$masuk = 'Masuk';$pulang = 'Pulang';$extime = $val['times'].' menit';
			if(self::$type=='history_2'){
				//Check kekurangan
				$history = unserialize($val['log_history']);
				if(isset($history['extime'])){
					$extime = $history['extime'].' menit';
				}

				//Check history time
				$masuk = 'Keluar';$pulang = 'Kembali';
				$history = unserialize($val['history_log_']);

				$val['time_in_log_'] = '-';
				$val['time_out_log_'] = '-';

				$_idx = 0;
				if(isset($history['logs'])){
					foreach ($history['logs'] as $ky => $vl) {
						if(in_array($vl['type'],array('4','8'))){
							if($ky!=0){
								$val['time_in_log_'] = $vl['time'];
								$_idx = $ky;
							}
							break;
						}
					}

					if(isset($history['logs'][$_idx + 1])){
						$val['time_out_log_'] = $history['logs'][$_idx + 1]['time'];
					}
				}

				$first = $val['time_in_log_']=='-'?$work[0]['time_in']:$val['time_in_log_'];
				$last = $val['time_out_log_']=='-'?$work[0]['time_out']:$val['time_out_log_'];
				$lama = _conv_time($first,$last,2);
			}

			if(self::$type=='history_3'){
				$masuk = 'Mulai';
				$val['time_in_log_'] = $work[0]['time_out']=='00:00:00'?$val['time_in_log_']:format_time_id($work[0]['time_out']);
			}

			$status = '';
			switch ($val['status']) {
				case 0:
					$status = '#666;';
					break;

				case 1:
					$status = '#26a69a;';
					break;

				case 2:
					$status = '#f5b724;';
					break;
				
				default:
					$status = '#fff;';
					break;
			}

			$status = '<i class="fa fa-circle" style="color:'.$status.'"></i>';

			$_history = array(
				'ID'	=> 'history_'.$val['ID'],
				'func'	=> '_historyDetail',
				'color'	=> 'yellow',
				'icon'	=> 'fa fa-eye',
				'label'	=> 'History',
				'type'	=> self::$type.'#'.$_type[1]
			);

			$tanggal = format_date_id($val['date_schedule']);
			$holiday = holiday_absen::_check_holiday($val['date_schedule']);
			if($holiday){
				$tanggal = '<span style="color:red;">'.$tanggal.'</span>';
			}

			if(self::$type=='history_3'){
				$lama = $val['times'];
				if($lama<=2){
					$val['times'] = $lama * 1.5;
				}else{
					$val['times'] = ($lama * 2) - 1;
				}

				if($holiday){
					$val['times'] = ($lama * 2);
				}
			}

			$data['table'][$key]['tr'] = array('');
			$data['table'][$key]['td'] = array(
				'Check'			=> array(
					'center',
					'5%',
					$val['ID'],
					false
				),
				'No'			=> array(
					'center',
					'5%',
					$no,
					true
				),
				'Tanggal'		=> array(
					'left',
					'15%',
					$tanggal,
					true
				),
				'Jam Kerja'		=> array(
					'center',
					'15%',
					$worktime,
					true
				),
				$masuk			=> array(
					'center',
					'10%',
					$val['time_in_log_'],
					true
				),
				$pulang			=> array(
					'center',
					'10%',
					$val['time_out_log_'],
					true
				),
				'Lama'	=> array(
					'right',
					'10%',
					$lama .' '. $note,
					true
				),
				'Total'	=> array(
					'right',
					'10%',
					$val['times'] .' '. $note,
					true
				),
				'Waktu'	=> array(
					'right',
					'10%',
					$extime,
					true
				),
				'Status'		=> array(
					'center',
					'7%',
					$status,
					true
				),
				'View'		=> array(
					'center',
					'10%',
					_modal_button($_history,2),
					true
				),
			);

			if(self::$type=='history_3'){
				unset($data['table'][$key]['td']['Status']);
				//unset($data['table'][$key]['td']['History']);
			}else{
				if(self::$type!='history_2'){
					unset($data['table'][$key]['td']['Lama']);
				}
			}

			if(self::$type!='history_2'){
				unset($data['table'][$key]['td']['Waktu']);
				unset($data['table'][$key]['td']['Check']);
			}
		}

		return $data;
	}

	public function _historyDetail($id=0){
		$id = str_replace('history_', '', $id);
		intval($id);

		$data = explode('#', $_POST['type']);
		self::$type = $type = $data[0];
		if($type=='history_1'){
			return punishment_absen::_history($id);
		}

		if($type=='history_3'){
			return self::_manual_lembur($id);
		}

		//View Ganti Jam
		$args = sobad_logDetail::get_id($id,array('times','log_history'));
		$history = unserialize($args[0]['log_history']);

		if(isset($history['history'])){
			$history = $history['history'];
		}else{
			$history = array();
		}

		$data['class'] = '';
		$data['table'] = array();

		$no = 0;
		foreach ($history as $key => $val) {
			$no += 1;

			$_edit = array(
				'ID'	=> 'edit_'.$id.'_'.$key,
				'func'	=> '_editGantiJam',
				'color'	=> 'blue',
				'icon'	=> 'fa fa-edit',
				'label'	=> 'Edit',
				'type'	=> self::$type
			);

			$_date = $val['date'];
			$note = isset($val['note']) || !empty($val['note'])?$val['note']:'Telah mengganti jam';

			$data['table'][$no-1]['tr'] = array('');
			$data['table'][$no-1]['td'] = array(
				'no'			=> array(
					'center',
					'5%',
					$no,
					true
				),
				'Actual'		=> array(
					'left',
					'15%',
					format_date_id($_date),
					true
				),
				'Waktu'		=> array(
					'left',
					'15%',
					$val['time'].' Menit',
					true
				),
				'Keterangan'	=> array(
					'left',
					'auto',
					$note,
					true
				),
				'Edit'	=> array(
					'center',
					'10%',
					_modal_button($_edit,3),
					true
				),
			);
		}

		$extime = $args[0]['times'];
		if(isset($history['extime'])){
			$extime = $history['extime'];
		}

		$args = array(
			'title'		=> 'History (masih : '.$extime.' Menit )',
			'button'	=> '_btn_modal_save',
			'status'	=> array(),
			'func'		=> array('sobad_table'),
			'data'		=> array($data)
		);
		
		return modal_admin($args);
	}

	public function _historyReward($id=0){
		$id = str_replace('history_', '', $id);
		intval($id);

		$filter = isset($_POST['filter'])?$_POST['filter']:date('Y-m');
		$filter = strtotime($filter);
		$filter = date('Y-m',$filter);

		$rangeD = report_absen::get_range($filter);
		$sDate = $rangeD['start_date'];
		$fDate = $rangeD['finish_date'];

		$args = sobad_user::get_id($id,array('name','shift','time_in','time_out','_inserted'),"AND _inserted BETWEEN '$sDate' AND '$fDate'");

		$data['class'] = '';
		$data['table'] = array();

		$no = 0;
		foreach ($args as $key => $val) {
			$no += 1;

			$_now = strtotime($val['_inserted']);
			$days = date('w',$_now);
			
			$work = sobad_work::get_workTime($val['shift'],"AND `abs-work-normal`.days='$days'");
			$worktime = format_time_id($work[0]['time_in']).' - '.format_time_id($work[0]['time_out']);

			$data['table'][$no-1]['tr'] = array('');
			$data['table'][$no-1]['td'] = array(
				'no'			=> array(
					'center',
					'5%',
					$no,
					true
				),
				'Tanggal'		=> array(
					'left',
					'auto',
					format_date_id($val['_inserted']),
					true
				),
				'Jam Kerja'		=> array(
					'left',
					'20%',
					$worktime,
					true
				),
				'Masuk'		=> array(
					'left',
					'15%',
					$val['time_in'],
					true
				),
				'Pulang'		=> array(
					'left',
					'10%',
					$val['time_out'],
					true
				)
			);
		}

		$args = array(
			'title'		=> 'Reward',
			'button'	=> '_btn_modal_save',
			'status'	=> array(),
			'func'		=> array('sobad_table'),
			'data'		=> array($data)
		);
		
		return modal_admin($args);
	}

	public function _add_manual($args=array()){
		$args = sobad_asset::ajax_conv_json($args);
		$users = explode(',', $args['user']);

		$date = $args['date'];
		$date = strtotime($date);
		$date = date('Y-m-d',$date);

		self::$type = 'history_2';
		$punish = $args['time'];
		foreach ($users as $ky => $vl) {
			self::_calc_gantiJam($vl,$punish,$date,$args['note']);
		}

		$table = self::table($_POST['filter']);
		return table_admin($table);
	}

	public function _add_lembur($args=array()){
		$args = sobad_asset::ajax_conv_json($args);
		$users = explode(',', $args['user']);

		$date = $args['date'];
		$date = strtotime($date);
		$date = date('Y-m-d',$date);

		self::$type = 'history_3';
		foreach ($users as $key => $val) {
			$logid = 0;
			$logs = sobad_user::get_logs(array('ID'),"_inserted='$date' AND user='$val'");
			
			$check = array_filter($logs);
			if(!empty($check)){
				$logid = $logs[0]['ID'];
			}

			$history = array(
				'user'		=> $val,
				'note'		=> $args['note']
			);

			$data = array(
				'log_id'		=> $logid,
				'date_schedule'	=> $date,
				'times'			=> $args['_time'],
				'status'		=> 1,
				'log_history'	=> serialize($history),
				'type_log'		=> 3
			);

			if($args['ID']==0){
				if($args['_time']>0){
					sobad_db::_insert_table('abs-log-detail',$data);
				}
			}else{
				if($args['_time']>0){
					sobad_db::_update_single($args['ID'],'abs-log-detail',$data);
				}else{
					self::_delete($args['ID']);
				}

				$table = self::_historyTable($val);
				return table_admin($table);
			}
		}

		$table = self::table($_POST['filter']);
		return table_admin($table);
	}

	public static function _calc_gantiJam($_id=0,$ganti=0,$dateActual='',$note='Telah mengganti Jam'){
		$dateActual = empty($dateActual)?date('Y-m-d'):$dateActual;
		$_logs = sobad_logDetail::get_all(array('ID','log_id','times','status','date_actual','log_history'),"AND _log_id.user='$_id' AND `abs-log-detail`.type_log='2' AND `abs-log-detail`.status!='1'");

		$_check = $ganti % 30;
		if($_check<=20){
			$ganti -= $_check;
		}else{
			$ganti += (30 - $_check);
		}

		foreach ($_logs as $key => $val) {
			if($ganti<=0){
				break;
			}

			// Tambah data history
			$history = unserialize($val['log_history']);
			if(!isset($history['history'])){
				$history = array();
				$history['history'] = array();
			}

			if(isset($history['extime'])){
				$val['times'] = $history['extime'];
			}

			$_status = 1;
			$_times = $val['times'];

		// Tambah data actual
			$_actual = '';
			$_actual = explode(',', $val['date_actual']);

			if(empty($_actual)){
				$_actual = array();
			}

			$_actual[] = $dateActual;
			$_actual = implode(',', $_actual);

		// Check jam
			$_check = $ganti - $val['times'];

			if($_check<0){
				$_status = 2;
				$_times = $val['times'];
				$history['extime'] = $_check * -1;
				$waktu = $ganti;
			}else{
				$history['extime'] = 0;
				$waktu = $val['times'];
			}

			$history['history'][] = array(
				'date'		=> $dateActual,
				'extime'	=> $_times,
				'time'		=> $waktu,
				'note'		=> $note
			);

			$ganti -= $val['times'];
			sobad_db::_update_single($val['ID'],'abs-log-detail',array(
				'date_actual'	=> $_actual,
				'log_history'	=> serialize($history),
				'status'		=> $_status
			));
		}
	}	

	// ----------------------------------------------------------
	// Print data history ---------------------------------------
	// ----------------------------------------------------------

	public function _preview($args=array()){
		$_SESSION[_prefix.'development'] = 0;
		parent::$type = $_GET['type'];

		switch (parent::$type) {
			case 'history_2':
				$title = 'Ganti Jam';
				break;

			case 'history_3':
				$title = 'Lembur';
				break;
			
			default:
				$title = 'Undefined';
				break;
		}

		$args = array(
			'data'		=> '',
			'style'		=> array('style_type2','style_history'),
			'object'	=> self::$object,
			'html'		=> '_html',
			'setting'	=> array(
				'posisi'	=> 'landscape',
				'layout'	=> 'A4',
			),
			'name save'	=> $title.' '.conv_month_id(date('m')).' '.date('Y')
		);

		return sobad_convToPdf($args);
	}

	public function _html(){
		parent::$type = $_GET['type'];
		$now = isset($_GET['filter']) && !empty($_GET['filter'])?$_GET['filter']:date('Y-m');
		$data = self::table($now);

		$now = strtotime($now);
		$dateM = date('m',$now);
		$dateY = date('Y',$now);

		switch (parent::$type) {
			case 'history_2':
				$title = 'GANTI JAM';
				break;

			case 'history_3':
				$title = 'LEMBUR';
				break;
			
			default:
				$title = 'Undefined';
				break;
		}

		unset($data['data']);
		unset($data['search']);
		?>
			<page backtop="5mm" backbottom="5mm" backleft="5mm" backright="5mm" pagegroup="new">
				<div style="text-align:center;width:100%;">
					<h2 style="margin-bottom: 0px;"> <?php print($title) ;?> </h2>
					<h3 style="margin-top: 0px;">Bulan : <?php echo conv_month_id($dateM).' '.$dateY ;?></h3>
				</div><br>
			<?php
				metronic_layout::sobad_table($data);
			?>
			</page>
		<?php
	}

	// ----------------------------------------------------------
	// Print data history ---------------------------------------
	// ----------------------------------------------------------

	public function _print($data=array()){
		$_SESSION[_prefix.'development'] = 0;

		$data = json_decode($data,true);	
		$args = array(
			'data'		=> $data,
			'style'		=> array('style_type2','style_gantiJam'),
			'object'	=> self::$object,
			'html'		=> '_printForm',
			'setting'	=> array(
				'posisi'	=> 'landscape',
				'layout'	=> 'A5',
			),
			'name save'	=> 'Form Ganti Jam'
		);

		return sobad_convToPdf($args);
	}	

	public function _printForm($data=array()){
		$idx = str_replace('print_history_', '', $data['ID']);
		$user = sobad_user::get_id($idx,array('name','no_induk','divisi'));
		$user = $user[0];

		$_data = implode(',', $data['data']);
		$whr = empty($_data)?"AND `abs-log-detail`.ID='0'":"AND `abs-log-detail`.ID IN ($_data)";
		$log = sobad_logDetail::get_all(array('log_id','date_schedule','times'),$whr);

		$total = 0;
		foreach ($log as $key => $val) {
			$total += round($val['times']/60,1);
		}

		?>
			<page backtop="5mm" backbottom="5mm" backleft="5mm" backright="5mm" pagegroup="new">
				<div style="text-align:center;width:100%;">
					<h3 style="margin-bottom: 0px;"> KARTU GANTI JAM PEGAWAI </h3>
					<h3 style="margin-top: 0px;">PT. SOLO ABADI INDONESIA</h3>
				</div><br>
				<table style="width:100%;font-family: calibri">
					<tr>
						<td style="width:70%;font-family: calibriBold;font-size: 18px;">NIP/NAMA : <?php echo $user['no_induk'].' / '.$user['name'] ;?></td>
						<td style="width:30%;font-family: calibriBold;font-size: 18px;">Divisi : <?php print($user['meta_value_divi']) ;?></td>
					</tr>
				</table>
				<table class="table-bordered sobad-punishment" style="width:100%;font-family: calibri">
					<thead>
						<tr>
							<th rowspan="2" style="text-align:center;width:5%;font-family: calibriBold;">No</th>
							<th rowspan="2" style="text-align:center;width:11%;font-family: calibriBold;">Tanggal Ijin</th>
							<th rowspan="2" style="text-align:center;width:15%;font-family: calibriBold;">Alasan</th>
							<th rowspan="2" style="text-align:center;width:7%;font-family: calibriBold;">Durasi Jam</th>
							<th rowspan="2" style="text-align:center;width:5%;font-family: calibriBold;">No</th>
							<th rowspan="2" style="text-align:center;width:12%;font-family: calibriBold;">Tanggal Ganti Jam</th>
							<th rowspan="2" style="text-align:center;width:22%;font-family: calibriBold;">Uraian Pekerjaan</th>
							<th colspan="2" style="text-align:center;width:10%;font-family: calibriBold;">Durasi</th>
							<th rowspan="2" style="text-align:center;width:8%;font-family: calibriBold;">TTd KDiv</th>
						</tr>
						<tr>
							<th>Mulai</th>
							<th>Berakhir</th>
						</tr>
					</thead>
					<tbody>
						<?php for($i=1;$i<=7;$i++){ ;?>
						<tr>
							<td><?php echo isset($log[$i-1])?$i.'.':''; ?></td>
							<td>
								<?php 
									$_date = isset($log[$i-1])?strtotime($log[$i-1]['date_schedule']):'';
									echo empty($_date)?'':date('d/m/Y',$_date);
								?>
							</td>
							<td>
								<?php
									if(isset($log[$i-1])){
										$note = unserialize($log[$i-1]['note_log_']);
										if(isset($note['permit']) && !empty($note['permit'])){
											$note = isset($note['permit'])?$note['permit']:'';
										}else{
											$_start = $log[$i-1]['date_schedule'];
											$permit = sobad_permit::get_all(array('note'),"AND user='$idx' AND start_date='$_start' AND type='4'");

											$check = array_filter($permit);
											if(!empty($check)){
												$note = $permit[0]['note'];
											}else{
												$note = '';
											}
										}

										echo $note;
									}
								?>
							</td>
							<td style="text-align: center;"><?php echo isset($log[$i-1])?round($log[$i-1]['times']/60,1):'' ;?></td>
							<?php for($j=5;$j<=10;$j++){ ;?>
							<td>&nbsp;</td>
							<?php } ;?>
						</tr>
						<?php } ;?>
						<tr>
							<td colspan="3" style="font-family: calibriBold;text-align: right;">Total :</td>
							<td style="text-align: center;"><?php echo empty($total)?'':$total ;?></td>
							<td colspan="3" style="font-family: calibriBold;text-align: right;">Total :</td>
							<td colspan="3" style="font-family: calibriBold;text-align: right;">Jam</td>
						</tr>
					</tbody>
				</table>
				<table style="width: 100%;margin-top: 20px;">
					<tr>
						<td style="width:30%;font-family: calibriBold;text-align: center;">Diserahkan Oleh : <br><br><br><br>(<i style="margin-left: 100px;">&nbsp;</i>)</td>
						<td style="width:40%;font-family: calibriBold;text-align: center;">&nbsp;</td>
						<td style="width:30%;font-family: calibriBold;text-align: center;">Diketahui Oleh : <br><br><br><br>HRD</td>
					</tr>
					<tr>
						<td colspan="2" style="font-family: calibri;">
							<i style="margin-left: 20px;margin-top: 10px;">&nbsp;</i>NB: Detail Pekerjaan bisa di tuliskan pada bagian belakang halaman
						</td>
					</tr>
				</table>
			</page>
		<?php
	}
}