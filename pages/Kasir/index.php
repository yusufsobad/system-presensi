<?php
(!defined('DEFPATH'))?exit:'';

require dirname(__FILE__).'/function.php';

$args = array();
$args['kasir'] = array(
	'page'		=> 'view_POS',
	'home'		=> true
);
reg_hook('reg_page',$args);

class view_POS{

	public function _reg(){
		$GLOBALS['body'] = 'page-header-fixed page-quick-sidebar-over-content page-full-width';

		if(!isset($_SESSION['sochick_dept'])){
			$err = new error();
			$err->_page404();
		}
		
		self::_script();
		reg_hook('reg_language',array());
		reg_hook('reg_sidebar',reg_hormenu());
	}

	public function _page(){
		metronic_layout::load_here('_hor_menu');
	}

	private function _script(){
		$script = new vendor_script();
		$theme = new theme_script();

		// url script css ----->
		$css = array_merge(
				$script->_get_('_css_global'),
				$script->_get_('_css_page_level',array('bootstrap-datepicker','fullcalender','bootstrap-editable')),
				$script->_get_('_css_datatable',array('datatable')),
				$script->_get_('_css_tags_input'),
				$theme->_get_('_css_page_level',array('themes-search')),
				$theme->_get_('_css_page'),
				$theme->_get_('_css_theme')
			);
		
		// url script css ----->
		$js = array_merge(
				$script->_get_('_js_core'),
				$script->_get_('_js_page_level',array('bootstrap-toastr','bootstrap-datepicker')),
				$script->_get_('_js_mask_money'),
				$script->_get_('_js_tags_input'),
				//$script->_get_('_js_form_editable'),
				//$script->_get_('_js_page_modal'),
				$theme->_get_('_js_page_level')
			);
		
	// insert jQuery in Head	
		$style['jQuery'] = '<script src="'.$js['jquery-core'].'"></script>';

	//	unset($script['bootstrap-modal']);
		unset($js['themes-modal']);	
		unset($js['themes-login-soft']);
		unset($js['themes-editable']);
		unset($js['jquery-core']);

		ob_start();
		self::_style();
		$style['login'] = ob_get_clean();

		ob_start();
		self::load_script();
		$custom['login'] = ob_get_clean();

		reg_hook("reg_script_head",$style);
		reg_hook("reg_script_css",$css);
		reg_hook("reg_script_js",$js);
		reg_hook("reg_script_foot",$custom);
	}

	private function _style(){
		?>
			<style type="text/css">
				#here_content div.theme-panel,
				#here_content div.page-bar,
				#here_content h3.page-title {
	    			display: none;
				}
				.portlet{
					margin-bottom: 0px !important;
				}
			</style>
		<?php
	}

	private function load_script(){
		?>
			<script>
			jQuery(document).ready(function() {    
			   Metronic.init(); // init metronic core componets
			   Layout.init(); // init layout
			   QuickSidebar.init(); // init quick sidebar
			Demo.init(); // init demo features
			   Index.init();   
			   Index.initDashboardDaterange();
			//   Index.initJQVMAP(); // init index page's custom scripts
			   Index.initCalendar(); // init index page's custom scripts
			//   Index.initCharts(); // init index page's custom scripts
			//   Index.initChat();
			//   Index.initMiniCharts();
			   Tasks.initDashboardWidget();
			//   UIExtendedModals.init();
			//FormEditable.init();
			});
			</script>
		<?php
	}
}