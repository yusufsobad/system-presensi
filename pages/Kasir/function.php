<?php
// function

if(isset($_SESSION['sochick_dept'])){
	// tampilan
	require 'view/Kasir.php';
	
	// sidemenu
	require 'sidemenu/Kasir.php';
}