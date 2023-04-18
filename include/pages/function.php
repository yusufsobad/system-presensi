<?php
// include function external ----------
require 'file_manager.php';
require 'form_product.php';
require 'form_new_product.php';
require 'form_list_product.php';
require 'layout_admin.php';
require 'layout_pdf.php';
// ------------------------------------
// ---------- List Function -----------
// ------------------------------------

function convToOption($args=array(),$id,$value){
	$check = array_filter($args);
	if(empty($check)){
		return array();
	}
	
	$option = array();
	$check = array_filter($args);
	if(!empty($check)){
		foreach($args as $key => $val){
			$option[$val[$id]] = $val[$value];
		}
	}else{
		$option[0] = 'Tidak Ada';
	}
	
	return $option;
}

function convToGroup($args=array(),$data=array()){
	$check = array_filter($args);
	if(empty($check)){
		return array();
	}
	
	$group = array();
	$check = array_filter($args);
	if(!empty($check)){
		foreach($args as $key => $val){
			foreach ($data as $ky) {
				$group[$ky][] = $val[$ky];
			}
		}
	}else{
		$group[0] = array();
	}
	
	return $group;
}

function switch_toggle($val=array()){
	$id = isset($val['id']) ? $val['id'] : '';
	$label = isset($val['label']) ? $val['label'] : '';

	$btn = '
		<div class="form-check '.$val['class'].'">
            <label>
              <input id="'.$id.'" type="checkbox" name="'.$val['key'].'" value="'.$val['value'].'"><span>'.$label.'</span>
            </label>
        </div>
	';

	return $btn;
}

function newpage_button($val){
	$val['func'] = isset($val['func']) ? $val['func'] : '_sidemenu';
	$val['load'] = 'here_content';
	$val['script'] = 'sobad_newpage(this)';
	return _click_button($val,'');
}

function hapus_button($val){
	return _click_button($val);
}

function _click_button($val){
	$check = array_filter($val);
	if(empty($check)){
		return '';
	}

	$load = isset($val['load'])?$val['load']:'sobad_portlet';
	
	$val['toggle'] = '';
	$val['load'] = $load;
	$val['href'] = 'javascript:;';
	
	return buat_button($val);
}

function print_button($val){
	$check = array_filter($val);
	if(empty($check)){
		return '';
	}
	
	$val['toggle'] = '';
	$val['load'] = 'sobad_preview';
	$val['script'] = isset($val['script'])?$val['script']:'sobad_report(this)';
	$val['href'] = 'javascript:;';
	
	return buat_button($val);
}

function edit_button($val){
	return _modal_button($val,'');
}

function apply_button($val){	
	return _modal_button($val,2);
}

function _modal_button($val,$no=''){
	$check = array_filter($val);
	if(empty($check)){
		return '';
	}
	
	$val['toggle'] = 'modal';
	$val['load'] = 'here_modal'.$no;
	$val['href'] = '#myModal'.$no;
	$val['spin'] = false;
	
	return buat_button($val);
}

function edit_button_custom($val){
	$check = array_filter($val);
	if(empty($check)){
		return '';
	}
	
	return buat_button($val);
}

function editable_click($args=array()){
	$check = array_filter($args);
	if(empty($check)){
		return '';
	}
	
	$edit = '<a href="javascript:;" id="'.$args['key'].'" class="edit_input_txt" data-type="text" data-sobad="'.$args['func'].'" data-name="'.$args['name'].'" data-title="'.$args['title'].'" class="editable editable-click">'.$args['label'].'</a>';
	
	return $edit;
}

function editable_value($args=array()){
	$check = array_filter($args);
	if(empty($check)){
		return '';
	}

	if(!isset($args['class'])){
		$args['class'] = '';
	}

	if(!isset($args['data'])){
		$args['data'] = 'style="width:100%;"';
	}
	
	if(!isset($_SESSION[_prefix.'input_form'])){
		$_SESSION[_prefix.'input_form'] = array();
	}

	array_merge($_SESSION[_prefix.'input_form'],array($args['key'] => $args['type']));

	$edit = create_form::get_option('input',$args,0,12);
	//$edit = '<input style="width:100%;" type="'.$args['type'].'" name="'.$args['key'].'" value="'.$args['value'].'" '.$args['status'].'>';
	
	return $edit;
}

function buat_button($val=array()){
	$check = array_filter($val);
	if(empty($check)){
		return '';
	} 
	
	$status = '';
	if(isset($val['status'])){
		$status = $val['status'];
	}

	$type = '';
	if(isset($val['type'])){
		$type = $val['type'];
	}

	$alert = false;
	if(isset($val['alert'])){
		$alert = $val['alert'];
	}

	$class = 'btn-xs';
	if(isset($val['class'])){
		$class = $val['class'];
	}

	$spin = 1;
	if(isset($val['spin'])){
		$spin = $val['spin']?1:0;
	}

	$uri = '';
	if(isset($val['uri'])){
		$uri = $val['uri'];
	}
	
	$onclick = 'sobad_button(this,'.$spin.')';
	if(isset($val['script'])){
		$onclick = $val['script'];
	}
	
	$btn = '
	<a id="'.$val['ID'].'" data-toggle="'.$val['toggle'].'" data-sobad="'.$val['func'].'" data-load="'.$val['load'].'" data-type="'.$type.'" data-alert="'.$alert.'" href="'.$val['href'].'" class="btn '.$class.' '.$val['color'].' btn_data_malika" data-uri="'.$uri.'" onclick="'.$onclick.'" '.$status.'>
		<i class="'.$val['icon'].'"></i> '.$val['label'].'
	</a>';
	
	return $btn;
}

function dropdown_action($args=array()){
	$args['label'] = isset($args['label']) ? $args['label'] : '<img src="theme/sasi/asset/img/dot-dropdown.png" alt="">';
	$args['angle'] = '';

	return dropdown_button($args);
}

function dropdown_icon($args = array())
{
	$icon = isset($args['icon']) ? '<i class="' . $args['icon'] . '" aria-hidden="true"></i>' : '';
	$label = isset($args['label']) ? $args['label'] : '';

	$args['label'] = $icon . $label;
	return dropdown_button($args);
}

function dropdown_button($args=array()){
	$check = array_filter($args);
	if(empty($check)){
		return '';
	}

	$btn = '';
	foreach ($args['button'] as $ky => $val) {
		if($val!='divider'){
			$btn .= '<li>'.$val.'</li>';
		}else{
			$btn .= '<li class="divider"></li>';
		}
	}

	$angle = isset($args['angle']) ? $args['angle'] : 'fa-angle-down';
	$angle = '<i class="fa '.$angle.'"></i>';

	$drop = '
		<div class="btn-group btn-group-solid">
			<button type="button" class="btn '.$args['color'].' dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-delay="1000" data-close-others="true">
				'.$args['label'].' '.$angle.'
			</button>
			<ul class="dropdown-menu" role="menu">
				'.$btn.'
			</ul>
		</div>
	';

	return $drop;
}

function button_toggle_block($val=array()){
	$check = array_filter($val);
	if(empty($check)){
		return '';
	}
	
	$val['toggle'] = 'modal';
	$val['load'] = 'here_modal';
	$val['href'] = '#myModal';
	
	return button_dash_block($val);
}

function button_direct_block($val=array()){
	$check = array_filter($val);
	if(empty($check)){
		return '';
	}
	
	$val['toggle'] = '';
	$val['load'] = 'sobad_portlet';
	$val['href'] = 'javascript:;';
	$val['script'] = 'sobad_sidemenu(this)';
	
	return button_dash_block($val);
}

function button_dash_block($val=array()){
	$status = '';
	if(isset($val['status'])){
		$status = $val['status'];
	}

	$onclick = 'sobad_button(this,false)';
	if(isset($val['script'])){
		$onclick = $val['script'];
	}

	$button = '
		<a id="'.$val['ID'].'" class="more" data-toggle="'.$val['toggle'].'" data-sobad="'.$val['func'].'" data-load="'.$val['load'].'" href="'.$val['href'].'" onclick="'.$onclick.'" '.$status.'>
			View more <i class="m-icon-swapright m-icon-white"></i>
		</a>';

	return $button;	
}

function _detectDelimiter($csvFile){
    $delimiters = array(
        ';' => 0,
        ',' => 0,
        "\t" => 0,
        "|" => 0
    );

    $handle = fopen($csvFile, "r");
    $firstLine = fgets($handle);
    fclose($handle); 
    foreach ($delimiters as $delimiter => &$count) {
        $count = count(str_getcsv($firstLine, $delimiter));
    }

    return array_search(max($delimiters), $delimiters);
}

function _conv_date($awal='', $akhir=''){
	$awal = empty($awal) ? date('Y-m-d') : $awal;
	$akhir = empty($akhir) ? date('Y-m-d') : $akhir;

	$tgl1 = strtotime($awal); 
	$tgl2 = strtotime($akhir); 

	$jarak = $tgl2 - $tgl1;
	$hari = $jarak / 60 / 60 / 24;

	return $hari;
}

function _conv_time($awal='00:00:00', $akhir='00:00:00', $conv=1){
	// conv 1 = detik , 2 = menit , 3 = jam , 4 = Jam : Menit , 5 = Jam : menit : detik

	$waktu_awal		= strtotime($awal);
	$waktu_akhir	= strtotime($akhir); // bisa juga waktu sekarang now()

	//menghitung selisih dengan hasil detik
	$diff	= $waktu_akhir - $waktu_awal;
        
	//membagi detik menjadi jam
	$jam	= floor($diff / (60 * 60));
        
	//membagi detik menjadi menit
	$menit 	= floor($diff / 60);;

	switch ($conv) {
		case 1:
			return number_format($diff,0,",",".");
			break;

		case 2:
			return $menit;
			break;

		case 3:
			return $jam;
			break;

		case 4:
			$menit = $diff - $jam * (60 * 60);
			return $jam . ' Jam '. floor($menit/60) . ' Menit';
			break;
		
		default:
			return number_format($diff,0,",",".");
			break;
	}
}

function _calc_time($time='',$code='1 minutes'){
	$time = empty($time)?date('H:i:s'):$time;

	$time = date_create($time);
	date_add($time, date_interval_create_from_date_string($code));
	$time = date_format($time,'H:i:s');

	return $time;
}

function _calc_date($date='',$code='+1 days'){
	$date = empty($date)?date('Y-m-d'):$date;

	$date = strtotime($date);
	$date = date('Y-m-d',strtotime($code,$date));

	return $date;
}

function script_chart(){
	?>
	<script>
		$(".chart_malika").ready(function(){
			var ajx = $('.chart_malika').attr('data-sobad');
			var id = $('.chart_malika').attr('data-load');
			
			data = "ajax="+ajx+"&data=2019";
			sobad_ajax(id,data,sobad_chart);
		});
	</script>
	<?php
}

function _importPage($page='',$class=''){
	$loc = dirname(__FILE__) . "/../../coding/_pages/";

	if(!class_exists($class)){
		$dir = $loc . $page . '/view';
		if(is_dir($dir)){
			require $dir . '/' . $class . '.php';
		}else{
			die($class.'::Class not found!!!');
		}
	}
}