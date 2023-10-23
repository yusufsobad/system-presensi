<?php
class api_sobad extends _class
{
    private static $url = "soloabadi-server.ddns.net/system-sobad-group/include/curl.php";
    // private static $url = "localhost/system-sobad-group/include/curl.php";

    // API READ DATA ===============================================================
    private static function send_curl($args = array())
    {
        $url = self::$url;

        $data = sobad_curl::get_data(self::$url, $args);
        $data = json_decode($data, true);

        if ($data['status'] == 'error') {
            die(_error::_alert_db($data['msg']));
        }

        return $data['msg'];
    }

    public static function _checkAlpha()
    {
        $data = array(
            'object'    => 'request_curl',
            'func'      => '_checkAlpha',
            'data'      => array()
        );
        return self::send_curl($data);
    }

    public static function _checkGantiJam()
    {
        $data = array(
            'object'    => 'request_curl',
            'func'      => '_checkGantiJam',
            'data'      => array()
        );
        return self::send_curl($data);
    }
}
