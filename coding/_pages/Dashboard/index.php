<?php
(!defined('DEFPATH')) ? exit : '';
require dirname(__FILE__) . '/absensi.php';

class dashboard
{
    public function _reg()
    {
        $GLOBALS['body'] = 'dashboard';
        self::_script();
    }

    public function _page($args = [])
    {
        $data = dashboard_absensi::index();
        theme_layout('load_here', $data);
    }

    private function _script()
    {
        $script = new vendor_script();
        
        $css = new theme_css();
        $js = new theme_js();

        // url script jQuery - Vendor
        $get_jquery = $script->_get_('_js_core',array('jquery-core'));
        $head[0] = '<script src="'.$get_jquery['jquery-core'].'"></script>';

        // url script css ----->
        $css = array_merge(
            $css->_get_('_bootstrap_css'),
            $css->_get_('_sasi_css'),
            $css->_get_('_plugin_css')
        );

        // url script css ----->
        $js = array_merge(
            // $js->_get_('_vendor_js'),
            $js->_get_('_bootstrap_js'),
            $js->_get_('_plugin_js'),
            $js->_get_('_sasi_js')

        );

        reg_hook("reg_script_head",$head);
        reg_hook("reg_script_css", $css);
        reg_hook("reg_script_js", $js);
    }
}
