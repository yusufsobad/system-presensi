<?php

// Layout Login
	require 'style.php';
?>
	<!-- BEGIN LOGO
	<div class="logo">
		 <img src="asset/img/logo-big.png" alt="">
	</div>
	 END LOGO -->
	<!-- BEGIN LOGIN -->
	<div class="table-layout">
		<div class="content">
		<?php
			require 'layout.php';
		?>
		</div>
	</div>
	<!-- END LOGIN -->
	<!-- BEGIN COPYRIGHT -->
	<div class="copyright">
		<?php print(date('Y')) ;?> © <?php print(constant('company')) ;?>
	</div>
	<!-- END COPYRIGHT -->
<?php