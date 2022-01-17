<?php

// Layout Login

?>

<!-- BEGIN LOGIN FORM -->
<form class="login-form" data-sobad="<?php print($func) ;?>" action="javascript:void(0)" method="post">
	<div class="form-logo">
		<img src="asset/img/sasi-logo.png">
	</div>
	<h3 class="form-title">Login to your account</h3>
	<div class="alert alert-danger display-hide">
		<button class="close" data-close="alert"></button>
		<span>
		Enter any username and password. </span>
	</div>
	<div class="form-group">
		<!--ie8, ie9 does not support html5 placeholder, so we just show field title for that-->
		<label class="control-label visible-ie8 visible-ie9">Username</label>
		<div class="input-icon">
			<input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="Username" name="username"/>
		</div>
	</div>
	<div class="form-group">
		<label class="control-label visible-ie8 visible-ie9">Password</label>
		<div class="input-icon">
			<input class="form-control placeholder-no-fix" type="password" autocomplete="off" placeholder="Password" name="password"/>
		</div>
	</div>
	<div class="form-actions">
		<label class="checkbox" style="display: block !important;color:#666;">
		<input type="checkbox" name="remember" value="1"/> <?php print(__e('remember me')) ;?> </label>
		<div style="text-align: center;margin-top: 25px;">
			<button id="btn_login_submit" data-sobad="<?php print($func) ;?>" type="submit" class="btn">
				Login
			</button>
		</div>
	</div>
</form>

<!-- END LOGIN FORM -->
<?php