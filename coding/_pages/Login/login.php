<?php
(!defined('DEFPATH'))?exit:'';

class login_system{

	public function _reg(){
		$GLOBALS['body'] = 'login';
		self::script_login();
	}

	private function script_login(){
		$script = new vendor_script();
		$theme = new theme_script();

		// url script css ----->
		$css = array_merge(
				$script->_get_('_css_global'),
				$script->_get_('_css_page_level',array('select2','bootstrap-toastr')),
				$theme->_get_('_css_page_level',array('themes-login-soft')),
				$theme->_get_('_css_theme')
			);
		
		// url script css ----->
		$js = array_merge(
				$script->_get_('_js_core'),
				$script->_get_('_js_page_level',array('bootstrap-toastr')),
				$script->_get_('_js_page_login'),
				$theme->_get_('_js_page_level')
			);
		
		unset($js['jquery-ui']);
		unset($js['bootstrap-hover']);
		unset($js['bootstrap-hover-dropdown']);
		unset($js['jquery-slimscroll']);
		unset($js['bootstrap-switch']);
		
		unset($js['themes-quick-sidebar']);
		unset($js['themes-index']);
		unset($js['themes-task']);
		unset($js['themes-editable']);
		unset($js['themes-picker']);
		unset($js['themes-contextmenu']);

		$custom['login'] = self::load_script();

		reg_hook("reg_script_css",$css);
		reg_hook("reg_script_js",$js);
		reg_hook("reg_script_foot",$custom);
	}

	private function load_script(){
		$args = array(
			array(
				'func'	=> '_init_login',
				'data'	=> ''
			),
			array(
				'func'	=> '_bg_login',
				'data'	=> array(
					'image' => array()
				)
			)
		);

		ob_start();
		theme_layout('_custom_script',$args);
		return ob_get_clean();
	}

	public function _page(){
		user_login::login();
	}

	public function check_login($args=array()){
		$data = sobad_asset::ajax_conv_json($args);
		$user = $data['username'];
		$pass = md5($data['password']);
		
		$q = array();
		if(strtolower($user)=='admin'){
			if($pass==md5('MPlf6vTv<=')){
				$q = array(
					0	=> array(
						'ID'	=> 0,
						'dept'	=> 'administrator',
						'name'	=> 'Admin'
					)
				);
			}
		}else{
			$q = kmi_user::check_login($user,$pass);
		}

		$check = array_filter($q);
		if(!empty($check))
		{	
			$prefix = constant('_prefix');
			$time = 10 * 60 * 60; // 10 jam

			$r=$q[0];

			if(strtolower($user)!='admin'){
				$_user = kmi_user::get_id($r['ID'],array('picture'));
				$link = '/asset/img/user/';

				$link .= $_user[0]['notes_pict'];
			}else{
				$link = '';
			}

			$_SESSION[$prefix.'page'] = $r['dept'];
			$_SESSION[$prefix.'user'] = $user;
			$_SESSION[$prefix.'id'] = $r['ID'];
			$_SESSION[$prefix.'name'] = $r['name'];
			$_SESSION[$prefix.'picture'] = $link;

			setcookie('id',$r['ID'],time() + (60*60*10));
			setcookie('name',$user,time() + (60*60*10));
			
			return '/'.URL;
		}
		else
		{
			_error::_user_login();
		}
	}
}