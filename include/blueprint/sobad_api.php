<?php
class sobad_api extends _class
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

    public static function _get_users_active($args = [], $limit = '', $type = '')
    {
        $data = array(
            'object'    => 'abs_user',
            'func'      => '_get_users_active',
            'data'      => array($args, $limit, $type)
        );
        return self::send_curl($data);
    }

    public static function _get_users($args = [], $limit = '', $type = '')
    {
        $data = array(
            'object'    => 'abs_permit',
            'func'      => '_get_users',
            'data'      => array($args, $limit, $type)
        );
        return self::send_curl($data);
    }

    public static function _get_groups()
    {
        $data = array(
            'object'    => 'abs_module',
            'func'      => '_get_groups',
            'data'      => array()
        );
        return self::send_curl($data);
    }

    public static function _gets($key = '', $args = array(), $limit = '', $type = '')
    {
        $data = array(
            'object'    => 'abs_module',
            'func'      => '_gets',
            'data'      => array($key, $args, $limit, $type)
        );
        return self::send_curl($data);
    }

    public static function user_get_all($args = array(), $limit = '', $type = '')
    {
        $data = array(
            'object'    => 'abs_user',
            'func'      => 'get_all',
            'data'      => array($args, $limit, $type)
        );
        return self::send_curl($data);
    }

    public static function get_absen($args = array(), $date = '', $limit = '')
    {
        $data = array(
            'object'    => 'abs_user',
            'func'      => 'get_absen',
            'data'      => array($args, $date, $limit)
        );
        return self::send_curl($data);
    }

    public static function permit_get_all($args = array(), $limit = '')
    {
        $data = array(
            'object'    => 'abs_permit',
            'func'      => 'get_all',
            'data'      => array($args, $limit)
        );
        return self::send_curl($data);
    }

    public static function work_get_id($id, $args = array(), $limit = '')
    {
        $data = array(
            'object'    => 'abs_work',
            'func'      => 'get_id',
            'data'      => array($id, $args, $limit)
        );
        return self::send_curl($data);
    }

    public static function _get_group($divisi = '',  $status = '')
    {
        $data = array(
            'object'    => 'abs_module',
            'func'      => '_get_group',
            'data'      => array($divisi, $status)
        );
        return self::send_curl($data);
    }

    public static function _get_birthdays()
    {
        $data = array(
            'object'    => 'abs_user',
            'func'      => '_get_birthdays',
            'data'      => array()
        );
        return self::send_curl($data);
    }

    // API INSERT DATA ================================================================
    public static function _insert_table($table = '', $args = array())
    {
        $data = array(
            'database'  => 'absen2020',
            'object'    => 'sobad_db',
            'func'      => '_insert_table',
            'data'      => array($table, $args)
        );
        return self::send_curl($data);
    }

    // API UPDATE DATA =================================================================
    public static function _update_single($id = '', $table = '', $args = [])
    {
        $data = array(
            'database'  => 'absen2020',
            'object'    => 'sobad_db',
            'func'      => '_update_single',
            'data'      => array($id, $table, $args)
        );
        return self::send_curl($data);
    }

    // API (CONVERSION,CHECK) DATA ============================================================

    public static function _nik_internship($id = 0)
    {
        $data = array(
            'object'    => 'request_curl',
            'func'      => '_nik_internship',
            'data'      => array($id)
        );
        return self::send_curl($data);
    }

    public static function _check_holiday($date = '')
    {
        $data = array(
            'object'    => 'request_curl',
            'func'      => '_check_holiday',
            'data'      => array($date)
        );
        return self::send_curl($data);
    }

    public static function _check_shift($user = 0, $worktime = 0, $date = '')
    {
        $data = array(
            'object'    => 'abs_permit',
            'func'      => '_check_shift',
            'data'      => array($user, $worktime, $date)
        );
        return self::send_curl($data);
    }

    public static function _check_punish($user = '', $date = '')
    {
        $data = array(
            'object'    => 'abs_user_log',
            'func'      => '_check_punish',
            'data'      => array($user, $date)
        );
        return self::send_curl($data);
    }

    public static function _check_noInduk($id)
    {
        $data = array(
            'object'    => 'request_curl',
            'func'      => '_check_noInduk',
            'data'      => array($id)
        );
        return self::send_curl($data);
    }

    public static function _statusGroup($data)
    {
        $data = array(
            'object'    => 'request_curl',
            'func'      => '_statusGroup',
            'data'      => array($data)
        );
        return self::send_curl($data);
    }

    // API COUNT DATA
    public static function user_count($data)
    {
        $data = array(
            'object'    => 'abs_user',
            'func'      => 'count',
            'data'      => array($data)
        );
        return self::send_curl($data);
    }

    public static function go_work($data)
    {
        $data = array(
            'object'    => 'abs_user',
            'func'      => 'go_work',
            'data'      => array($data)
        );
        return self::send_curl($data);
    }

    public static function go_home($data)
    {
        $data = array(
            'object'    => 'abs_user',
            'func'      => 'go_home',
            'data'      => array($data)
        );
        return self::send_curl($data);
    }

    public static function go_permit($data)
    {
        $data = array(
            'object'    => 'abs_user',
            'func'      => 'go_permit',
            'data'      => array($data)
        );
        return self::send_curl($data);
    }

    public static function go_holiday($data)
    {
        $data = array(
            'object'    => 'abs_user',
            'func'      => 'go_holiday',
            'data'      => array($data)
        );
        return self::send_curl($data);
    }

    public static function go_outCity($data)
    {
        $data = array(
            'object'    => 'abs_user',
            'func'      => 'go_outCity',
            'data'      => array($data)
        );
        return self::send_curl($data);
    }

    public static function go_sick($data)
    {
        $data = array(
            'object'    => 'abs_user',
            'func'      => 'go_sick',
            'data'      => array($data)
        );
        return self::send_curl($data);
    }

    public static function go_tugas($data)
    {
        $data = array(
            'object'    => 'abs_user',
            'func'      => 'go_tugas',
            'data'      => array($data)
        );
        return self::send_curl($data);
    }

    public static function go_holiwork($data)
    {
        $data = array(
            'object'    => 'abs_user',
            'func'      => 'go_holiwork',
            'data'      => array($data)
        );
        return self::send_curl($data);
    }

    public static function get_rule_absen($timeA = '', $timeB = '', $worktime = '', $day = '')
    {
        $data = array(
            'object'    => 'request_curl',
            'func'      => 'get_rule_absen',
            'data'      => array($timeA, $timeB, $worktime, $day)
        );
        return self::send_curl($data);
    }

    public static function count_company($id = 0)
    {
        $data = array(
            'object'    => 'abs_user',
            'func'      => 'count_company',
            'data'      => array($id)
        );
        return self::send_curl($data);
    }
}
