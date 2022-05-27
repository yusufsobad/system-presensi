<?php

// ---------------------------------------------
// Create To PDF -------------------------------
// ---------------------------------------------
function sobad_convToPdf($args = array()){
	$check = array_filter($args);
	if(empty($check)){
		return '';
	}

	date_default_timezone_set('UTC');

	$format = isset($args['type_pdf']) ? $args['type_pdf'] : convToPDF;

	if ($format == "createpdf") {
		new _libs_(array('createpdf'));
		return conv_htmlToPDF($args);
	} else if ($format == "mpdf") {
		new _libs_(array('mpdf'));
		return conv_mPDF($args);
	} else {
		if(is_callable("conv_toPDF")){
			conv_toPDF($args);
		}
	}
}

function conv_htmlToVar($html='',$data='',$object=''){
	ob_start();

	if(is_callable($html)){
		$html($data);
	}

	if(!empty($object)){
		if(class_exists($object)){			
			if(is_callable(array(new $object(),$html))){
				$func = $html;
				$object::{$func}($data);
			}
		}
	}

	$html = ob_get_clean();

	return $html;
}

function conv_htmlToPDF($args=array()){
	ob_start();

	if(development==1){
		echo '<style type="text/css">';
		foreach($args['style'] as $key => $val){
			if(is_callable($val)){
				echo $val();
			}
		}
		echo '</style>';
	}else{
		echo get_style($args['style']);
	}

	if(is_callable($args['html'])){
		$args['html']($args['data']);
	}

	if(isset($args['object'])){
		if(class_exists($args['object'])){
			$object = $args['object'];
			
			if(is_callable(array(new $object(),$args['html']))){
				$func = $args['html'];
				$object::{$func}($args['data']);
			}
		}
	}
	
	$content = ob_get_clean();

	if(development==1){
		return $content;
	}

	$pos = $args['setting']['posisi'];
	$lay = $args['setting']['layout'];
	$nama = $args['name save'];
	
	try{
		$html2pdf = new HTML2PDF($pos, $lay, 'en', true, 'UTF-8',array(0,0,0,0));
		$html2pdf->pdf->SetDisplayMode('fullpage');
		$html2pdf->setTestTdInOnePage(false);
		$html2pdf->writeHTML($content, isset($_GET['vuehtml']));
		$html2pdf->Output($nama.".pdf");
	}
	catch(HTML2PDF_exception $e) {
		echo $e;
		exit;
	}
}


function conv_mPDF($args=array()){
	$data = array();

	// Setting Config Header Report MPDF
	$args['header'] = isset($args['header']) ? $args['header'] : '';
	$args['data_header'] = isset($args['data_header']) ? $args['data_header'] : '';
	$header = array();
	$type = gettype($args['header']);
	if($type=='array'){
		foreach ($args['header'] as $key => $val) {
			$object = isset($val[$key]['object'])?$val[$key]['object']:'';
			$header[] = conv_htmlToVar($val[$key]['header'],$val[$key]['data_header'],$object);
		}
	}else{
		$object = isset($args['object'])?$args['object']:'';
		$header[] = conv_htmlToVar($args['header'],$args['data_header'],$object);
	}
	
	// Setting Config Footer Report MPDF
	$args['footer'] = isset($args['footer']) ? $args['footer'] : '';
	$args['data_footer'] = isset($args['data_footer']) ? $args['data_footer'] : '';
	$footer = array();
	$type = gettype($args['footer']);
	if($type=='array'){
		foreach ($args['footer'] as $key => $val) {
			$object = isset($val[$key]['object'])?$val[$key]['object']:'';
			$footer[] = conv_htmlToVar($val[$key]['footer'],$val[$key]['data_footer'],$object);
		}
	}else{
		$object = isset($args['object'])?$args['object']:'';
		$footer[] = conv_htmlToVar($args['footer'],$args['data_footer'],$object);
	}

	// Setting Config CSS Report MPDF
	$css = array();
	if(isset($args['style'])){
		foreach($args['style'] as $key => $val){
			if(is_callable($val)){
				ob_start();
					echo $val();
				$css[] = ob_get_clean();
			}
		}
	}

	// Setting Config HTML Report MPDF
	$html = array();
	$type = gettype($args['html']);
	if($type=='array'){
		foreach ($args['html'] as $key => $val) {
			$object = isset($val[$key]['object'])?$val[$key]['object']:'';
			$html[] = conv_htmlToVar($val[$key]['html'],$val[$key]['data'],$object);
		}
	}else{
		$object = isset($args['object'])?$args['object']:'';
		$html[] = conv_htmlToVar($args['html'],$args['data'],$object);
	}

	if(development==1){
		$content = '';

		foreach ($css as $key => $val) {
			$content .= '<style type="text/css">' . $val . '</style>';
		}

		foreach ($html as $key => $val) {
			$content .= $val;
		}

		return $content . $footer;
	}

	$pos = $args['setting']['posisi'];

	$pos = strtoupper($pos)=='POTRAIT'?'P':$pos;
	$pos = strtoupper($pos)=='LANDSCAPE'?'L':$pos;

	$lay = $args['setting']['layout'];
	$nama = $args['name save'];

	$margin['top'] = $args['margin_top'];
	$margin['bottom'] = $args['margin_bottom'];
	$margin['left'] = $args['margin_left'];
	$margin['right'] = $args['margin_right'];

	$margin['auto_margin_top'] = $args['auto_margin_top'];

	// // Margin Top
	// $margin['auto_margin_top'] = isset($args['auto_margin_top']) ? $args['auto_margin_top'] : '';
	// if (isset($margin['auto_margin_top']) || $margin['auto_margin_top'] !== '') {
	// 	$config_margin_top = array(
	// 		'setAutoTopMargin' => isset($args['auto_margin_top']) ? $args['auto_margin_top'] : ''
	// 	);
	// }

	// // Margin Bottom
	// $margin['auto_margin_bottom'] = isset($args['auto_margin_bottom']) ? $args['auto_margin_bottom'] : '';
	// if (isset($margin['auto_margin_bottom']) || $margin['auto_margin_bottom'] !== '') {
	// 	$config_margin_bottom = array(
	// 		'setAutoBottomMargin' => isset($args['auto_margin_bottom']) ? $args['auto_margin_bottom'] : ''
	// 	);
	// }
	
	try{
		$mpdf = new \Mpdf\Mpdf([
		    'format'          		=> $lay, // Default Potrait (Landscape : 'A4-L')
		    'orientation'	  		=> $pos,
		    'mode'            		=> 'UTF-8', // Unicode
		    'lang'            		=> 'en', // Language
		    'margin_top'      		=> isset($margin['top'])?$margin['top']:10,
		    'margin_bottom'   		=> isset($margin['bottom'])?$margin['bottom']:10,
		    'margin_left'     		=> isset($margin['left'])?$margin['left']:10,
		    'margin_right'    		=> isset($margin['right'])?$margin['right']:10,
			'setAutoTopMargin'		=> isset($margin['auto_margin_top']) ? $margin['auto_margin_top'] : false
			// $config_margin_top,
			// $config_margin_bottom
		]);

		// $mpdf->SetFooter($footer);  
		$mpdf->SetDisplayMode('fullwidth');

		foreach ($header as $key => $val) {
			$mpdf->SetHTMLHeader($val);
		}

		foreach ($footer as $key => $val) {
			$mpdf->SetFooter($val);
		}

		foreach ($css as $key => $val) {
			$mpdf->WriteHTML($val,1);
		}

		foreach ($html as $key => $val) {
			$mpdf->WriteHTML($val);
		}

		$mpdf->Output($nama . '.pdf',"I"); // Format Preview
	}
	catch(\Mpdf\MpdfException $e) {
		echo $e->getMessage();
		exit;
	}
}