<?php
class api_monitoring extends _class
{
    private static $url = "http://localhost/connect-monitoring/backend_api.php";
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

    public static function _checkConnect($data='')
    {
        $data = array(
            'code'    => $data,
        );

        return self::send_curl($data);
    }
}
