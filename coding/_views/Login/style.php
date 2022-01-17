<?php
	
	// Style Login

?>

<style type="text/css">
	.login {
	    background-color: #666 !important;
	    display: table;
	}

	.login .content {
	    background-color: #fff;
	    border-radius: 25px !important;
	}

	.login .table-layout {
	    display: table-cell;
	    width: inherit;
	    height: inherit;
	    vertical-align: middle;
	}

	button#btn_login_submit {
	    background-color: #a0237a;
	    color: #fff;
	    padding: 5px 35px;
	    border-radius: 20px !important;
	    font-size: 20px;
	    font-weight: 600;
	}

	button#btn_login_submit:hover {
	    opacity: 0.7;
	}

	.login .form-logo,
	.form-logo>img {
	    width: 65px;
	}

	.login .content .form-title {
		color: #333 !important;
	    font-weight: 600;
	    margin-bottom: 25px;
	    font-size: 27px;
	    line-height: 1;
	}

	form.login-form input.form-control {
	    background-color: #e0dddd !important;
	}

	.input-icon > .form-control {
	    padding-left: 15px !important;
	}

	::placeholder {
	    color: #666 !important;
	}

	.login .copyright {
	    position: absolute;
	    width: 100%;
	    left: 0px;
	    bottom: 20px;
	    color: #333;
	}

	@media only screen and (max-width: 600px){
		.login .content {
		    padding: 30px;
		    width: 300px;
		    border-radius: 15px !important;
		}

		.login .content .form-title {
			font-size: 22px;
		}
	}
</style>