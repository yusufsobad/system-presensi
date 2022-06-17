<?php

(!defined('DEFPATH'))?exit:'';

function format_currency(){
	$current = get_locale();
	$args = array(
		'id_ID'	=> 'Rp',
		'en_US'	=> '$',
	);
	
	return $args[$current];
}

function format_nominal($nominal){
	$current = get_locale();
	$args = array(
		'id_ID'	=> array(0,',','.'),
		'en_US'	=> array(2,'.',','),
	);
	
	$val = $args[$current];
	
	return number_format($nominal,$val[0],$val[1],$val[2]);
}

function format_decimal($nominal){
	$current = get_locale();
	$args = array(
		'id_ID'	=> array(1,',','.'),
		'en_US'	=> array(1,'.',','),
	);
	
	$val = $args[$current];
	
	return number_format($nominal,$val[0],$val[1],$val[2]);
}

function format_quantity($nominal){
	$current = get_locale();
	$args = array(
		'id_ID'	=> array(2,',','.'),
		'en_US'	=> array(2,'.',','),
	);
	
	$val = $args[$current];
	
	return number_format($nominal,$val[0],$val[1],$val[2]);
}

function clear_format($nominal){
	$current = get_locale();
	$args = array(
		'id_ID'	=> array(2,',','.'),
		'en_US'	=> array(2,'.',','),
	);

	$val = $args[$current];
	$nominal = str_replace($val[2],'', $nominal);
	$nominal = str_replace($val[1],'.', $nominal);

	return floatval($nominal);
}

function format_number_currency($current,$nominal){
	$format = format_currency($current);
	$format .= '<span class="sobad_currency"> ';
	$format .= format_nominal($current,$nominal);
	$format .= '</span>';
	
	return $format;
}

function format_date_id($date){
	$date = strtotime($date);
	$date = date('Y-m-d',$date);
	$date = explode('-',$date);
	
	$y = $date[0];
	$m = conv_month_id($date[1]);
	$d = $date[2];
	
	return $d.' '.$m.' '.$y;
}

function format_time_id($date){
	$date = strtotime($date);
	$date = date('H:i',$date);
	
	return $date;
}

function sum_days($month=1,$year=1){
	return cal_days_in_month(CAL_GREGORIAN, $month, $year);
}

function conv_day_id($date){
	$date = strtotime($date);
	$int = date('w',$date);
	
	$args = array(
		'Minggu',
		'Senin',
		'Selasa',
		'Rabu',
		'Kamis',
		'Jum\'at',
		'Sabtu'
	);
	
	return $args[$int];
}

function conv_month_id($int=''){
	$int = intval($int);
	
	$args = array(
		1 => 'Januari',
		'Februari',
		'Maret',
		'April',
		'Mei',
		'Juni',
		'Juli',
		'Agustus',
		'September',
		'Oktober',
		'November',
		'Desember'
	);

	if(!empty($int)){
		$args = $args[$int];
	}
	
	return $args;
}

function format_terbilang($x=0) {
  $angka = ["", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas"];

  if ($x < 12)
    return " " . $angka[$x];
  elseif ($x < 20)
    return format_terbilang($x - 10) . " belas";
  elseif ($x < 100)
    return format_terbilang($x / 10) . " puluh" . format_terbilang($x % 10);
  elseif ($x < 200)
    return "seratus" . format_terbilang($x - 100);
  elseif ($x < 1000)
    return format_terbilang($x / 100) . " ratus" . format_terbilang($x % 100);
  elseif ($x < 2000)
    return "seribu" . format_terbilang($x - 1000);
  elseif ($x < 1000000)
    return format_terbilang($x / 1000) . " ribu" . format_terbilang($x % 1000);
  elseif ($x < 1000000000)
    return format_terbilang($x / 1000000) . " juta" . format_terbilang($x % 1000000);
}

function format_terbilang_usd($number)
{
	$hyphen      = '-';
	$conjunction = ' and ';
	$separator   = ' ';
	$negative    = 'negative ';
	$decimal     = ' point ';
	$dictionary  = array(
		0                   => 'zero',
		1                   => 'one',
		2                   => 'two',
		3                   => 'three',
		4                   => 'four',
		5                   => 'five',
		6                   => 'six',
		7                   => 'seven',
		8                   => 'eight',
		9                   => 'nine',
		10                  => 'ten',
		11                  => 'eleven',
		12                  => 'twelve',
		13                  => 'thirteen',
		14                  => 'fourteen',
		15                  => 'fifteen',
		16                  => 'sixteen',
		17                  => 'seventeen',
		18                  => 'eighteen',
		19                  => 'nineteen',
		20                  => 'twenty',
		30                  => 'thirty',
		40                  => 'fourty',
		50                  => 'fifty',
		60                  => 'sixty',
		70                  => 'seventy',
		80                  => 'eighty',
		90                  => 'ninety',
		100                 => 'hundred',
		1000                => 'thousand',
		1000000             => 'million',
		1000000000          => 'billion',
		1000000000000       => 'trillion',
		1000000000000000    => 'quadrillion',
		1000000000000000000 => 'quintillion'
	);

	if (!is_numeric($number)) {
		return false;
	}

	if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
		trigger_error(
			'convert_number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
			E_USER_WARNING
		);
		return false;
	}

	if ($number < 0) {
		return $negative . format_terbilang_usd(abs($number));
	}

	$string = $fraction = null;
	if (strpos($number, '.') !== false) {
		list($number, $fraction) = explode('.', $number);
	}

	switch (true) {
		case $number < 21:
			$string = $dictionary[$number];
			break;
		case $number < 100:
			$tens   = ((int) ($number / 10)) * 10;
			$units  = $number % 10;
			$string = $dictionary[$tens];
			if ($units) {
				$string .= $hyphen . $dictionary[$units];
			}
			break;
		case $number < 1000:
			$hundreds  = $number / 100;
			$remainder = $number % 100;
			$string    = $dictionary[$hundreds] . ' ' . $dictionary[100];
			if ($remainder) {
				$string .= $conjunction . format_terbilang_usd($remainder);
			}
			break;
		default:
			$baseUnit     = pow(1000, floor(log($number, 1000)));
			$numBaseUnits = (int) ($number / $baseUnit);
			$remainder    = $number % $baseUnit;
			$string       = format_terbilang_usd($numBaseUnits) . ' ' . $dictionary[$baseUnit];
			if ($remainder) {
				$string .= $remainder < 100 ? $conjunction : $separator;
				$string .= format_terbilang_usd($remainder);
			}
			break;
	}

	if (null !== $fraction && is_numeric($fraction)) {
		$string .= $decimal;
		$words = array();
		foreach (str_split((string) $fraction) as $number) {
			$words[] = $dictionary[$number];
		}
		$string .= implode(' ', $words);
	}

	return ucwords($string);
}