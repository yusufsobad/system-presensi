<?php
//(!defined('AUTHPATH'))?exit:'';

// ---------------------------------------
// Set Time Jakarta ----------------------
// ---------------------------------------
ini_set('date.timezone', 'Asia/Jakarta');

// Database -------------------------------------------

// set Server
define('SERVER',"localhost");

// set Username
define('USERNAME',"root");

// set Password
define('PASSWORD','');

// set Database
define('DB_NAME','absen2020');
$GLOBALS['DB_NAME'] = DB_NAME;

// set rule database
define('SCHEMA',array(
		0 => array(
			'db' 	=> DB_NAME, // nama database
			'where'	=> '' // TABLE_NAME= . . .
		)
	)
);

// URL web --------------------------------------------

// set hostname
define('SITE','http');

// set hostname
define('HOSTNAME',$_SERVER['SERVER_NAME']);

// set name url
define('URL','cordova-v3');

// set check table
define('ABOUT','');

// Setting -------------------------------------------

// prefix SESSION
define('_prefix','cordova_');
		
// authentic include
define('AUTH_KEY','qJB0rGtInG03efyCpWs');

// PATH default
define('DEFPATH',dirname(__FILE__));

// set Multiple language
define('language',true);

// set nama Perusahaan
define('company','Cordova');

// set judul Website
define('title','Cordova');

// Library ------------------------------------------

define('_library',array(
			// name folder 		=> lokasi file,
			'createpdf'			=> 'html2pdf/html2pdf.class.php'
		));
