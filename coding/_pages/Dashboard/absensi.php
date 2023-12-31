<?php
include 'model_absensi.php';

class dashboard_absensi extends _page
{
    protected static $object = 'dashboard_absensi';

    protected static $loc_view = 'Dashboard/absensi';

    public static function index()
    {
        $data = model_absensi::presensi_data();
        $birthday_data =  sobad_api::_get_birthdays();
        $announcement_data = model_absensi::_dummy_data_announcement();
        $args = [
            'notwork_data'      => $data['notwork_data'],
            'work_data'         => $data['work_data'],
            'outcity_data'      => $data['outcity_data'],
            'workout_data'      => $data['workout_data'],
            'tugas_data'        => $data['tugas_data'],
            'permit_data'       => $data['permit_data'],
            'cuti_data'         => $data['cuti_data'],
            'sick_data'         => $data['sick_data'],
            'birthday_data'     => $birthday_data,
            'announcement_data' => $announcement_data,

            'count_tugas'       => self::_tugas(),
            'count_employes'    => self::_employees(),
            'count_internship'  => self::_internship(),
        ];
        self::scan();
        $title = '
                    <h2 class="bold grey">This is</h2>
                    <h1 class="bold black">Our Teams</h1>
        ';
        $grid_config = self::grid($data);

        $config = [
            'title'     => $title,
            'func'      => 'sobad_grid',
            'data'      => $grid_config,
            'script'    => self::script(),
            'args'      => $args
        ];
        return $config;
    }

    public static function grid($data = [])
    {
        $permision_information =  self::_loadView('employe_information', $data);
        $birthday_information = self::_loadView('birthday_information', $data);
        $employe_information = self::_loadView('permit_information', $data);

        $sag_content = self::sobad_group_content($data);
        $sobad_content = self::sobad_content($data);
        $kmi_content = self::kmi_content($data);
        $carousel_user = [];

        $config = [
            [
                'col'   => '6',
                'class' => 'space',
                'func'  => 'card',
                'data'  =>  $permision_information
            ],
            [
                'col'   => '3',
                'class' => 'space',
                'func'  => 'card_birthday',
                'data'  => $birthday_information
            ],
            [
                'col'   => '3',
                'class' => 'space',
                'func'  => 'card',
                'data'  => $employe_information
            ],
            [
                'col'   => '12',
                'class' => '',
                'func'  => 'card_company',
                'data'  =>  $sag_content,
            ],
            [
                'col'   => '12',
                'class' => '',
                'func'  => 'card_company',
                'data'  =>  $sobad_content,
            ],
            [
                'col'   => '12',
                'class' => '',
                'func'  => 'card_company',
                'data'  =>  $kmi_content,
            ],
            [
                'col'   => '12',
                'class' => '',
                'func'  => 'carousel_user',
                'data'  =>  $carousel_user,
            ],
        ];
        return $config;
    }

    public static function sobad_group_content($data = [])
    {
        $data_company = $data['company'][0];
        $company_id = $data_company['ID'];
        $_data_depart = [];
        $data_department = $data['group'];


        foreach ($data_department as $val) {
            if (isset($val['company'])) {
                if ($val['company'] == $company_id && $val['status']['status'] == 1) {
                    $_data_depart[] = $val;
                }
            }
        }

        $data = [
            'department'    => $_data_depart,
        ];
        $content = self::_loadView('sobad_group/content', $data);

        $count_employe = sobad_api::count_company($data_company['ID']);
        $base_url = self::base_url();
        $base_url = $base_url .  "img/upload/";
        $config = [
            'color' => 'light',
            'title' => $data_company['meta_value'],
            'logo'  => $base_url . $data_company['notes_meta'],
            'count' => $count_employe,
            'func'  => 'card_divisi',
            'data'  => $content
        ];
        return $config;
    }

    public static function sobad_content($data = [])
    {
        $data_company = $data['company'][1];
        $company_id = $data_company['ID'];
        $_data_depart = [];
        $data_department = $data['group'];
        foreach ($data_department as $val) {
            if (isset($val['company'])) {
                if ($val['company'] == $company_id && $val['status']['status'] == 1) {
                    $_data_depart[] = $val;
                }
            }
        }

        $data = [
            'department'    => $_data_depart,
        ];
        $content = self::_loadView('sobad/content', $data);
        $count_employe = sobad_api::count_company($data_company['ID']);
        $base_url = self::base_url();
        $base_url = $base_url .  "img/upload/";
        $config = [
            'color' => 'light',
            'title' => $data_company['meta_value'],
            'logo'  => $base_url . $data_company['notes_meta'],
            'count' => $count_employe,
            'func'  => 'card_divisi',
            'data'  => $content
        ];
        return $config;
    }

    public static function kmi_content($data = [])
    {
        $data_company = $data['company'][2];
        $company_id = $data_company['ID'];
        $_data_depart = [];
        $data_department = $data['group'];
        foreach ($data_department as $val) {
            if (isset($val['company'])) {
                if ($val['company'] == $company_id && $val['status']['status'] == 1) {
                    $_data_depart[] = $val;
                }
            }
        }

        $data = [
            'department'    => $_data_depart,
        ];
        $content = self::_loadView('kmi/content', $data);
        $count_employe = sobad_api::count_company($data_company['ID']);
        $base_url = self::base_url();
        $base_url = $base_url .  "img/upload/";
        $config = [
            'color'     => 'light',
            'title'     => $data_company['meta_value'],
            'logo'      => $base_url . $data_company['notes_meta'],
            'count'     => $count_employe,
            'func'      => 'card_divisi',
            'size_logo' => '123px',
            'data'      => $content
        ];
        return $config;
    }


    public static function scan()
    {
?>
        <input id="qrscanner" type="text" style="opacity:0;position: absolute;">
    <?php
    }

    public static function _check_scan()
    {
        $no_rfid = $_POST['no_rfid'];
        $date = date('Y-m-d');
        $day = date('w');
        $time_now =  date("H:i:s");
        $time_in = date("H:i");

        $check_user = sobad_api::_check_noInduk($no_rfid);
        $_userid = $check_user['ID'];


        $nik = $check_user['no_induk'];
        if ($check_user['status'] == 7) {
            $check_user['no_induk'] = sobad_api::_nik_internship($check_user['ID']);
        }

        if (isset($check_user['ID']) && $check_user['ID'] != 17) {
            $whr = "AND no_induk='$nik'";
            $users = sobad_api::user_get_all(array('ID', 'divisi', 'status', 'work_time'), $whr . " AND status!='0'");
            $user_log = sobad_api::get_absen(array('_nickname', 'id_join', 'type', 'time_in', 'time_out', 'history'), $date, $whr);
            $user_log = isset($user_log[0]) ? $user_log[0] : [];
            $_group = sobad_api::_get_group($check_user['divisi']);

            $check = array_filter($users);
            if (!empty($check)) {
                //Check Setting Auto Shift
                $_userid = $users[0]['ID'];
                $worktime = $users[0]['work_time'];
                $shift = sobad_api::permit_get_all(array('user', 'note'), "AND ( (user='$_userid' AND type='9') OR (user='0' AND note LIKE '" . $worktime . ":%') ) AND start_date<='$date' AND range_date>='$date'");

                $check = array_filter($shift);
                if (!empty($check)) {
                    if ($shift[0]['user'] == 0) {
                        $_nt = explode(':', $shift[0]['note']);
                        $worktime = $_nt[1];
                    } else {
                        $worktime = $shift[0]['note'];
                    }
                }
                $work = sobad_api::work_get_id($worktime, array('time_in', 'time_out', 'status'), "AND days='$day'");
            }

            $check = array_filter($work);
            if (empty($check)) {
                $work = array(
                    'time_in'    => '08:00:00',
                    'time_out'    => '16:00:00'
                );
            } else {
                $work = $work[0];
            }
            if ($time_now >= $work['time_in']) {
                $punish = 1;
            }
            $grp = sobad_api::_statusGroup($_group['status']);
            $grp_punish = $grp['punish'];

            if ($grp_punish == 0) {
                $punish = 0;
            }

            $_libur = sobad_api::_check_holiday($date);

            if ($_libur) {
                $punish = 0;
            }

            $check = array_filter($user_log);

            if (empty($check)) {
                if ($time_now >= $work['time_out']) {
                } else {
                    // INSERT DATA 
                    //Check Permit
                    $permit = sobad_api::permit_get_all(array('ID', 'user', 'type'), "AND user='$_userid' AND type!='9' AND start_date<='$date' AND range_date>='$date' OR user='$_userid' AND start_date<='$date' AND range_date='0000-00-00' AND num_day='0.0'");

                    $check = array_filter($permit);
                    if (!empty($check)) {
                        $pDate = date('Y-m-d');
                        $pDate = sobad_api::_get_end_permit($pDate);
                        sobad_api::_update_single($permit[0]['ID'], 'abs-permit', array('range_date' => $pDate));
                    }
                    sobad_api::_insert_table(
                        'abs-user-log',
                        array(
                            'user'      => $_userid,
                            'type'      => 1,
                            'shift'     => $users[0]['work_time'],
                            '_inserted' => $date,
                            'time_in'   => $time_now,
                            'time_out'  => '00:00:00',
                            'note'      => serialize(array('pos_user' => $users[0]['ID'], 'pos_group' => $_group['ID'])),
                            'punish'    => $punish,
                            'history'   => serialize(array('logs' => array(0 => array('type' => 1, 'time' => $time_in))))
                        )
                    );
                }
            } else {
                $idx = $user_log['id_join'];
                $history = unserialize($user_log['history']);
                $history['logs'][] = array('type' => 1, 'time' => $time_now);
                $history = serialize($history['logs']);
                if ($time_now <= $work['time_out']) {
                    if ($user_log['type'] !== '1') {

                        $permit = sobad_api::permit_get_all(array('ID', 'user', 'type'), "AND user='$_userid' AND type!='9' AND start_date<='$date' AND range_date>='$date' OR user='$_userid' AND start_date<='$date' AND range_date='0000-00-00' AND num_day='0.0'");
                        $check = array_filter($permit);
                        if (!empty($check)) {
                            $pDate = strtotime($date);
                            $pDate = date('Y-m-d', strtotime('-1 days', $pDate));
                            sobad_api::_update_single($permit[0]['ID'], 'abs-permit', array('range_date' => $pDate));
                        }
                        $_args = [
                            'type'      => 1,
                            'time_in'   => $time_now,
                            'history'   => $history
                        ];

                        switch ($user_log['type']) {
                            case '3':
                            case '4':
                                $type = 1;
                                $time_in = date('H:i', strtotime($user_log['time_in']));
                                $punish = 0;
                                if ($time_in >= $work['time_in']) {
                                    $punish = 1;
                                }

                                $history = unserialize($user_log['history']);
                                $history['logs'][] = array('type' => $type, 'time' => $time_now);
                                $history = serialize($history);

                                $_args = array('type' => $type, 'history' => $history);
                                if ($user_log['time_out'] != '00:00:00') {
                                    $timeB = $time_now;
                                    if ($work['status']) {
                                        $times = date("H:i");
                                        if ($times >= $work['time_out']) {
                                            $timeB = $work['time_out'];
                                        }
                                    }
                                    $timeA = $user_log['time_out'];
                                    $ganti = sobad_api::get_rule_absen($timeA, $timeB, $worktime, $day);
                                    if ($ganti['type'] != 0) {
                                        sobad_api::_insert_table('abs-log-detail', array(
                                            'log_id'        => $user_log['id_join'],
                                            'date_schedule' => date('Y-m-d'),
                                            'times'         => $ganti['time'],
                                            'type_log'      => 2
                                        ));
                                    }
                                }

                                sobad_api::_update_single($idx, 'abs-user-log', $_args);
                                break;
                            case '5':
                            case '6':
                            case '7':
                            case '8':
                            case '10':
                                $type = 1;
                                $time_in = date('H:i', strtotime($user_log['time_in']));
                                $punish = 0;
                                if ($time_in >= $work['time_in']) {
                                    $punish = 1;
                                }

                                if ($work['status']) {
                                    $time_in = date("H:i");
                                    if ($time_in >= $work['time_out']) {
                                        $type = 2;
                                        $_label = 'time_out';
                                    }
                                }

                                $history = unserialize($user_log['history']);
                                $history['logs'][] = array('type' => $type, 'time' => $time_now);
                                $history = serialize($history);

                                $_args = array('type' => $type, 'history' => $history);
                                if (empty($user_log['history'])) {
                                    $_args['time_in'] = $time_now;

                                    if ($time_now >= $work['time_in']) {
                                        $_args['punish'] = 1;
                                    }
                                    $punish = 1;
                                }
                                sobad_api::_update_single($user_log['id_join'], 'abs-user-log', $_args);

                                break;
                        }
                    }
                } else { // SCAN PULANG
                    if ($user_log['type'] !== '2') {
                        if ($user_log['time_in'] == "00:00:00") {
                            $_args = [
                                'type'      => 2,
                                'time_in'   => $work['time_in'],
                                'time_out'  => $time_now,
                                'history'   => $history
                            ];
                            sobad_api::_update_single($idx, 'abs-user-log', $_args);
                        } else {
                            $_args = [
                                'type'      => 2,
                                'time_out'  => $time_now,
                                'history'   => $history
                            ];
                            sobad_api::_update_single($idx, 'abs-user-log', $_args);
                        }
                    }
                }
            }
        }

        $data = [
            'data'  => [
                'time'      => $time_in,
                'punish'    => isset($punish) ? $punish : 0,
            ],
            'nik'   => isset($check_user['no_induk']) ? $check_user['no_induk'] : 0,
            'rfid'  =>  $no_rfid
        ];

        return $data;
    }



    public static function _go_out_city()
    {
        $no_rfid = $_POST['no_rfid'];
        $date = date('Y-m-d');
        $times = date('H:i:s');
        $time_in = date("H:i");
        $type = 5; // ? 

        $check_user = sobad_api::_check_noInduk($no_rfid);
        $nik = $check_user['no_induk'];

        if ($check_user['status'] == 7) {
            $check_user['no_induk'] = sobad_api::_nik_internship($check_user['ID']);
        }

        $_whr = "AND no_induk='$nik'";
        $user = sobad_api::user_get_all(array('ID', 'work_time', 'dayOff', '_nickname', 'id_join', 'history'), $_whr . " AND `abs-user-log`._inserted='$date'");
        $idx = $user[0]['id_join'];
        $_id = $user[0]['ID'];

        $user = unserialize($user[0]['history']);
        $user['logs'][] = array('type' => $type, 'time' => $times);
        $_args = array('type' => $type, 'time_out' => $times, 'history' => serialize($user));
        // Luar Kota
        sobad_api::_insert_table('abs-permit', array(
            'user'          => $_id,
            'start_date'    => date('Y-m-d'),
            'range_date'    => date('Y-m-d'),
            'num_day'       => 1,
            'type_date'     => 1,
            'type'          => 5,
        ));
        sobad_api::_update_single($idx, 'abs-user-log', $_args);

        $data['count'] = self::_outCity();

        $data = [
            'data'  => [
                'time'      => $time_in,
            ],
            'nik' => isset($check_user['no_induk']) ? $check_user['no_induk'] : 0,
            'rfid'  =>  $no_rfid
        ];
        return $data;
    }

    public static function _workout()
    {
        $no_rfid = $_POST['no_rfid'];
        $date = date('Y-m-d');
        $times = date('H:i:s');
        $time_in = date("H:i");
        $type = 7; // ? 

        $check_user = sobad_api::_check_noInduk($no_rfid);
        $nik = $check_user['no_induk'];

        if ($check_user['status'] == 7) {
            $check_user['no_induk'] = sobad_api::_nik_internship($check_user['ID']);
        }

        $_whr = "AND no_induk='$nik'";
        $user = sobad_api::user_get_all(array('ID', 'work_time', 'dayOff', '_nickname', 'id_join', 'history'), $_whr . " AND `abs-user-log`._inserted='$date'");
        $idx = $user[0]['id_join'];
        $_id = $user[0]['ID'];

        sobad_api::_insert_table('abs-permit', array(
            'user'          => $_id,
            'start_date'    => date('Y-m-d'),
            'range_date'    => date('Y-m-d'),
            'num_day'       => 1,
            'type_date'     => 1,
            'type'          => $type,
        ));

        $user = unserialize($user[0]['history']);
        $user['logs'][] = array('type' => $type, 'time' => $times);

        $_args = array('type' => $type, 'time_out' => $times, 'history' => serialize($user));
        sobad_api::_update_single($idx, 'abs-user-log', $_args);

        $data = [
            'data'  => [
                'time'      => $time_in,
            ],
            'nik' => isset($check_user['no_induk']) ? $check_user['no_induk'] : 0,
            'rfid'  =>  $no_rfid
        ];
        return $data;
    }

    public static function _permit()
    {
        $no_rfid = $_POST['no_rfid'];
        $date = date('Y-m-d');
        $times = date('H:i:s');
        $time_in = date("H:i");
        $type = 4; // ? 

        $check_user = sobad_api::_check_noInduk($no_rfid);
        $nik = $check_user['no_induk'];
        if ($check_user['status'] == 7) {
            $check_user['no_induk'] = sobad_api::_nik_internship($check_user['ID']);
        }

        $_whr = "AND no_induk='$nik'";
        $user = sobad_api::user_get_all(array('ID', 'work_time', 'dayOff', '_nickname', 'id_join', 'history'), $_whr . " AND `abs-user-log`._inserted='$date'");
        $_id = $user[0]['ID'];
        $idx = $user[0]['id_join'];
        $_permit = sobad_api::permit_get_all(array('ID'), "AND user='$_id' AND start_date='$date'");
        $check = array_filter($_permit);
        if (empty($check)) {
            sobad_api::_insert_table('abs-permit', array(
                'user'          => $_id,
                'start_date'    => $date,
                'range_date'    => $date,
                'num_day'       => 1,
                'type'          => 4,
                'note'          => 'Izin Keluar Sebentar'
            ));
        }

        $user = unserialize($user[0]['history']);
        $user['logs'][] = array('type' => $type, 'time' => $times);

        $_args = array('type' => $type, 'time_out' => $times, 'history' => serialize($user));
        sobad_api::_update_single($idx, 'abs-user-log', $_args);

        $data = [
            'data'  => [
                'time'      => $time_in,
            ],
            'nik' => isset($check_user['no_induk']) ? $check_user['no_induk'] : 0,
            'rfid'  =>  $no_rfid
        ];
        return $data;
    }

    public static function _sick_permit()
    {
        $no_rfid = $_POST['no_rfid'];
        $date = date('Y-m-d');
        $check_user = sobad_api::_check_noInduk($no_rfid);
        $times = date('H:i:s');
        $time_in = date("H:i");

        $nik = $check_user['no_induk'];
        if ($check_user['status'] == 7) {
            $check_user['no_induk'] = sobad_api::_nik_internship($check_user['ID']);
        }
        $_whr = "AND no_induk='$nik'";
        $user = sobad_api::user_get_all(array('ID', 'work_time', 'dayOff', '_nickname', 'id_join', 'history'), $_whr . " AND `abs-user-log`._inserted='$date'");
        $_id = $user[0]['ID'];
        $idx = $user[0]['id_join'];

        $type = 8;
        sobad_api::_insert_table('abs-permit', array(
            'user'          => $_id,
            'start_date'    => date('Y-m-d'),
            'range_date'    => '00:00:00',
            'type'          => $type,
        ));

        $user = unserialize($user[0]['history']);
        $user['logs'][] = array('type' => $type, 'time' => $times);
        $_args = array('type' => $type, 'time_out' => $times, 'history' => serialize($user));
        sobad_api::_update_single($idx, 'abs-user-log', $_args);


        $data = [
            'data'  => [
                'time'      => $time_in,
            ],
            'nik' => isset($check_user['no_induk']) ? $check_user['no_induk'] : 0,
            'rfid'  =>  $no_rfid
        ];
        return $data;
    }

    public static function _cuti()
    {
        $no_rfid = $_POST['no_rfid'];
        $date = date('Y-m-d');
        $check_user = sobad_api::_check_noInduk($no_rfid);
        $times = date('H:i:s');
        $time_in = date("H:i");
        $nik = $check_user['no_induk'];

        if ($check_user['status'] == 7) {
            $check_user['no_induk'] = sobad_api::_nik_internship($check_user['ID']);
        }

        $_whr = "AND no_induk='$nik'";
        $user = sobad_api::user_get_all(array('ID', 'work_time', 'dayOff', '_nickname', 'id_join', 'history'), $_whr . " AND `abs-user-log`._inserted='$date'");
        $_id = $user[0]['ID'];
        $idx = $user[0]['id_join'];

        $_args['type'] = 3;
        $type = 3;
        sobad_api::_insert_table('abs-permit', array(
            'user'          => $_id,
            'start_date'    => date('Y-m-d'),
            'range_date'    => '00:00:00',
            'type'          => $type,
        ));

        $user = unserialize($user[0]['history']);
        $user['logs'][] = array('type' => $type, 'time' => $times);
        $_args = array('type' => $type, 'time_out' => $times, 'history' => serialize($user));
        sobad_api::_update_single($idx, 'abs-user-log', $_args);

        $data = [
            'data'  => [
                'time'      => $time_in,
            ],
            'nik' => isset($check_user['no_induk']) ? $check_user['no_induk'] : 0,
            'rfid'  =>  $no_rfid
        ];
        return $data;
    }

    public static function _permit_change_time()
    {
        $date = date('Y-m-d');
        $no_rfid = $_POST['no_rfid'];
        $times = date('H:i:s');
        $time_in = date("H:i");

        $day = date('w');
        $check_user = sobad_api::_check_noInduk($no_rfid);
        $nik = $check_user['no_induk'];
        if ($check_user['status'] == 7) {
            $check_user['no_induk'] = sobad_api::_nik_internship($check_user['ID']);
        }

        $_whr = "AND no_induk='$nik'";
        $user = sobad_api::user_get_all(array('ID', 'work_time', 'dayOff', '_nickname', 'id_join', 'history'), $_whr . " AND `abs-user-log`._inserted='$date'");
        $_worktime = $user[0]['work_time'];
        $work = sobad_api::work_get_id($_worktime, array('time_out'), "AND days='$day'");
        $work = $work[0]['time_out'];

        $idx = $user[0]['id_join'];

        $type = 2;
        $ganti = sobad_api::get_rule_absen($times, $work, $_worktime, $day);
        if ($ganti['type'] != 0) {
            sobad_api::_insert_table('abs-log-detail', array(
                'log_id'        => $idx,
                'date_schedule' => date('Y-m-d'),
                'times'         => $ganti['time'],
                'type_log'      => $type
            ));
        }

        $user = unserialize($user[0]['history']);
        $user['logs'][] = array('type' => $type, 'time' => $times);

        $_args = array('type' => $type, 'time_out' => $times, 'history' => serialize($user));
        sobad_api::_update_single($idx, 'abs-user-log', $_args);

        $data = [
            'data'  => [
                'time'      => $time_in,
            ],
            'nik' => isset($check_user['no_induk']) ? $check_user['no_induk'] : 0,
            'rfid'  =>  $no_rfid
        ];
        return $data;
    }

    public static function _employees()
    {
        $work = sobad_api::user_count("status NOT IN ('0','7')");
        return $work;
    }

    public static function _internship()
    {
        $work = sobad_api::user_count("status IN ('7')");
        return $work;
    }

    public static function _inWork()
    {
        $date = date('Y-m-d');
        $work = sobad_api::go_work(array('id_join'), "AND `abs-user-log`._inserted='$date'");
        return count($work);
    }

    public static function _outWork()
    {
        $date = date('Y-m-d');
        $work = sobad_api::go_home(array('id_join'), "AND `abs-user-log`._inserted='$date'");
        return count($work);
    }

    public static function _permitWork()
    {
        $date = date('Y-m-d');
        $work = sobad_api::go_permit(array('id_join'), "AND `abs-user-log`._inserted='$date'");
        return count($work);
    }

    public static function _holidayWork()
    {
        $date = date('Y-m-d');
        $work = sobad_api::go_holiday(array('id_join'), "AND `abs-user-log`._inserted='$date'");
        return count($work);
    }

    public static function _outCity()
    {
        $date = date('Y-m-d');
        $work = sobad_api::count_outCity(array('id_join'), "AND `abs-user-log`._inserted='$date'");
        return $work;
    }

    public static function _sick()
    {
        $date = date('Y-m-d');
        $work = sobad_api::go_sick(array('id_join'), "AND `abs-user-log`._inserted='$date'");
        return count($work);
    }

    public static function _tugas()
    {
        $date = date('Y-m-d');
        $work = sobad_api::count_tugas(array('id_join'), "AND `abs-user-log`._inserted='$date'");
        return $work;
    }

    public static function _holiday()
    {
        $date = date('Y-m-d');
        $work = sobad_api::go_holiwork(array('id_join'), "AND `abs-user-log`._inserted='$date'");
        return count($work);
    }

    public static function script()
    {
        ob_start();

    ?>
        <script>
            // SCAN DATA MASUK
            function scan(val) {
                var _no_rfid = $(val).val();
                var ajx = '_check_scan';
                var id = '';
                var no_rfid = _no_rfid;
                var object = 'dashboard_absensi';

                if (_no_rfid == '') {
                    alert_failed_scan('Scan ID kosong!!!');
                }

                $(val).val('');
                data = "ajax=" + ajx + "&object=" + object + "&no_rfid=" + no_rfid;
                sobad_ajax(id, data, _dom_scan_work, false);
            }

            jQuery(document).ready(function() {
                $("#qrscanner").focus();
                $("#qrscanner").on('change', function() {
                    scan(this);
                });
            });

            $('body.dashboard').on('click', function() {
                $("#qrscanner").focus();
            });

            // DOM SCAN MASUK
            function _dom_scan_work(args) {
                var time_scan = args.data.time;
                var punish = args.data.punish;
                var nik = args.nik;
                var rfid = args.rfid;

                _notwork = nik in notwork_data
                in_work = nik in work_data;
                sick = nik in sick_data;
                _permit = nik in permit_data;
                _cuti = nik in cuti_data;
                _workout = nik in workout_data;


                // CHECK NIK ADA ATAU TIDAK 
                if (_notwork || in_work || sick || _permit || _cuti || _workout) {
                    var data = notwork_data[nik] || work_data[nik] || sick_data[nik] || permit_data[nik] || cuti_data[nik] || workout_data[nik];
                    time_work = data.shift.time_in;
                    time_go_home = data.shift.time_out;
                    data.time = time_scan;
                    if (punish !== null) {
                        data.punish = punish
                    }
                    if (in_work) { // JIKA NIK ADA DI WORK_DATA
                        if (data.time >= time_work) { // JIKA SCAN LEBIH DARI JAM MASUK
                            if (data.time >= time_go_home) { // JIKA SCAN SESUDAH JAM PULANG
                                notwork_data[nik] = data;
                                delete work_data[nik];
                                var notworkhtml = notwork_html(nik, data);
                                $(".footer-carousel").append(notworkhtml);
                                // REINIT ===============================
                                reinit_carousel('footer')
                                $('.' + data.width + '-carousel').slick('slickRemove');
                                $("." + nik + "-work").remove()
                                $('.' + data.width + '-carousel').slick('slickAdd');
                                reinit_carousel(data.width)
                                // END REINIT ===========================
                                alert_success_scan(data);
                            } else { // JIKA SCAN SEBELUM JAM PULANG
                                alert_scan(nik, work_data[nik]);
                            }
                        } else {
                            alert_already_scan(data);
                        }
                    } else { //JIKA NIK TIDAK ADA DI WORK_DATA
                        if (_workout) { // JIKA NIK ADA DI OUTCITY_DATA
                            if (data.time <= time_go_home) { // JIKA SCAN SEBELUM JAM PULANG
                                var workhtml = work_html(nik, data);
                                $("#" + data.group + "").append(workhtml);
                                work_data[nik] = data;
                                delete workout_data[nik];
                                delete outcity_data[nik];
                                delete tugas_data[nik];
                                // REINIT ===============================
                                reinit_carousel(data.width)
                                $('.permit-carousel').slick('slickRemove');
                                $("." + nik + "-permit").remove();
                                $('.permit-carousel').slick('slickAdd');
                                reinit_carousel('permit')
                                // END REINIT ===========================
                                dom_ammount_outcity();
                                dom_ammount_workout();
                                alert_success_scan(data);
                            } else { // JIKA SCAN SESUDAH JAM PULANG
                                notwork_data[nik] = data;
                                delete workout_data[nik];
                                delete outcity_data[nik];
                                delete tugas_data[nik];
                                var notworkhtml = notwork_html(nik, data);
                                $(".footer-carousel").append(notworkhtml);
                                // REINIT ===============================
                                reinit_carousel('footer');
                                $('.permit-carousel').slick('slickRemove');
                                $("." + nik + "-permit").remove();
                                $('.permit-carousel').slick('slickAdd');
                                reinit_carousel('permit')
                                // END REINIT ===========================
                                dom_ammount_outcity();
                                dom_ammount_workout();
                                alert_success_scan(data);
                            }
                        } else if (sick) { // JIKA NIK ADA DI SICK DATA
                            if (data.time <= time_go_home) { // JIKA SCAN SEBELUM JAM PULANG
                                var workhtml = work_html(nik, data);
                                $("#" + data.group + "").append(workhtml);
                                work_data[nik] = data;
                                delete sick_data[nik];
                                // REINIT ===============================
                                reinit_carousel(data.width)
                                $('.permit-carousel').slick('slickRemove');
                                $("." + nik + "-permit").remove();
                                $('.permit-carousel').slick('slickAdd');
                                reinit_carousel('permit')
                                // END REINIT ===========================
                                dom_ammount_sickpermit();
                                alert_success_scan(data);
                            } else { // JIKA SCAN SESUDAH JAM PULANG
                                notwork_data[nik] = data;
                                delete sick_data[nik];
                                var notworkhtml = notwork_html(nik, data);
                                $(".footer-carousel").append(notworkhtml);
                                // REINIT ===============================
                                reinit_carousel('footer');
                                $('.permit-carousel').slick('slickRemove');
                                $("." + nik + "-permit").remove();
                                $('.permit-carousel').slick('slickAdd');
                                reinit_carousel('permit')
                                // END REINIT ===========================
                                dom_ammount_sickpermit();
                                alert_success_scan(data);
                            }
                        } else if (_permit) { // JIKA NIK ADA DI PERMIT DATA
                            if (data.time <= time_go_home) { // JIKA SCAN SEBELUM JAM PULANG
                                var workhtml = work_html(nik, data);
                                $("#" + data.group + "").append(workhtml);
                                work_data[nik] = data;
                                delete permit_data[nik];
                                // REINIT ===============================
                                reinit_carousel(data.width)
                                $('.permit-split-carousel').slick('slickRemove');
                                $("." + nik + "-permit").remove();
                                $('.permit-split-carousel').slick('slickAdd');
                                reinit_carousel('permit-split')
                                // END REINIT ===========================
                                dom_ammount_permit();
                                alert_success_scan(data);
                            } else { // JIKA SCAN SESUDAH JAM PULANG
                                notwork_data[nik] = data;
                                delete permit_data[nik];
                                var notworkhtml = notwork_html(nik, data);
                                $(".footer-carousel").append(notworkhtml);
                                // REINIT ===============================
                                reinit_carousel('footer');
                                $('.permit-split-carousel').slick('slickRemove');
                                $("." + nik + "-permit").remove();
                                $('.permit-split-carousel').slick('slickAdd');
                                reinit_carousel('permit-split')
                                // END REINIT ===========================
                                dom_ammount_permit();
                                alert_success_scan(data);
                            }
                        } else if (_cuti) { // JIKA NIK ADA DI PERMIT DATA
                            if (data.time <= time_go_home) { // JIKA SCAN SEBELUM JAM PULANG
                                var workhtml = work_html(nik, data);
                                $("#" + data.group + "").append(workhtml);
                                work_data[nik] = data;
                                delete cuti_data[nik];
                                // REINIT ===============================
                                reinit_carousel(data.width)
                                $('.permit-split-carousel').slick('slickRemove');
                                $("." + nik + "-permit").remove();
                                $('.permit-split-carousel').slick('slickAdd');
                                reinit_carousel('permit-split')
                                // END REINIT ===========================
                                dom_ammount_cuti();
                                alert_success_scan(data);
                            } else { // JIKA SCAN SESUDAH JAM PULANG
                                notwork_data[nik] = data;
                                delete cuti_data[nik];
                                var notworkhtml = notwork_html(nik, data);
                                $(".footer-carousel").append(notworkhtml);
                                // REINIT ===============================
                                reinit_carousel('footer');
                                $('.permit-split-carousel').slick('slickRemove');
                                $("." + nik + "-permit").remove();
                                $('.permit-split-carousel').slick('slickAdd');
                                reinit_carousel('permit-split')
                                // END REINIT ===========================
                                dom_ammount_cuti();
                                alert_success_scan(data);
                            }
                        } else { // JIKA NIK TIDAK ADA DI OUTCITY_DATA & SICK_DATA & WORK_DATA
                            if (data.time >= time_go_home) { // JIKA ABSEN SESUDAH JAM Pulang
                                sasi_alert('Sudah Jam Pulang', 'danger_scan');
                            } else {
                                var workhtml = work_html(nik, data);
                                $("#" + data.group + "").append(workhtml);
                                work_data[nik] = data;
                                delete notwork_data[nik];
                                reinit_carousel(data.width)
                                $('.footer-carousel').slick('slickRemove');
                                $("." + nik + "-notwork").remove();
                                $('.footer-carousel').slick('slickAdd');
                                reinit_carousel('footer')
                                alert_success_scan(data);
                            }
                        }
                    }
                } else {
                    alert_failed_scan(data);
                }
                dom_ammount_work();
                dom_count_team(data.group);

            }

            function out(args) {
                nik = $('#alert_data').val();
                check_nik = nik in work_data;
                if (check_nik) {
                    $('#alert_global').fadeOut();
                    out_alert_scan(nik, work_data[nik]);
                }
            }

            // ACTION KETIKA USER MEMILIH LUAR KOTA
            function go_out_city(data) {
                nik = $('#alert_data').val();
                args = work_data[nik]

                $('#alert_global').fadeOut();
                var ajx = '_go_out_city';
                var id = '';
                var no_rfid = args.no_rfid;
                var object = 'dashboard_absensi';
                data = "ajax=" + ajx + "&object=" + object + "&no_rfid=" + no_rfid;
                sobad_ajax(id, data, _dom_out_city, false);
            }

            // DOM CONTENT LUAR KOTA
            function _dom_out_city(args) {
                var nik = args.nik;
                var data = work_data[nik];
                check_nik = nik in work_data;
                if (check_nik) {
                    workout_data[nik] = data
                    outcity_data[nik] = data
                    var data = work_data[nik];
                    $("#workout_content").append(workout_html(nik, data));
                    delete work_data[nik];
                    // RE INIT CAROUSEL
                    reinit_carousel('permit')
                    $('.' + data.width + '-carousel').slick('slickRemove');
                    $("." + nik + "-work").remove();
                    $('.' + data.width + '-carousel').slick('slickAdd');
                    reinit_carousel(data.width)
                    alert_success_scan(data);
                }
                dom_count_team(data.group);
                dom_ammount_work();
                dom_ammount_outcity(data.count);

            }

            // ACTION KETIKA USER MEMILIH Tugas Luar
            function workout(data) {
                nik = $('#alert_data').val();
                args = work_data[nik]

                $('#alert_global').fadeOut();
                var ajx = '_workout';
                var id = '';
                var no_rfid = args.no_rfid;
                var object = 'dashboard_absensi';
                data = "ajax=" + ajx + "&object=" + object + "&no_rfid=" + no_rfid;
                sobad_ajax(id, data, _dom_workout, false);
            }

            function _dom_workout(args) {
                var nik = args.nik;
                var data = work_data[nik]

                check_nik = nik in work_data;
                if (check_nik) {
                    workout_data[nik] = data;
                    tugas_data[nik] = data;
                    var data = work_data[nik];
                    $("#workout_content").append(workout_html(nik, data));
                    delete work_data[nik];
                    // RE INIT CAROUSEL
                    reinit_carousel('permit')
                    $('.' + data.width + '-carousel').slick('slickRemove');
                    $("." + nik + "-work").remove();
                    $('.' + data.width + '-carousel').slick('slickAdd');
                    reinit_carousel(data.width)
                    alert_success_scan(data);
                }
                dom_count_team(data.group);
                dom_ammount_work();
                dom_ammount_workout();
            }

            // ACTION KETIKA USER MEMILIH IZIN
            function permit(data) {
                nik = $('#alert_data').val();
                args = work_data[nik]
                $('#alert_global').fadeOut();
                var ajx = '_permit';
                var id = '';
                var no_rfid = args.no_rfid;
                var object = 'dashboard_absensi';
                data = "ajax=" + ajx + "&object=" + object + "&no_rfid=" + no_rfid;
                sobad_ajax(id, data, _dom_permit, false);
            }

            // DOM CONTENT IZIN
            function _dom_permit(args) {
                var nik = args.nik;
                var data = work_data[nik]
                var rfid = args.rfid;
                check_nik = nik in work_data;
                if (check_nik) {
                    permit_data[nik] = data
                    var data = work_data[nik];
                    $("#permit_content").append(permit_html(nik, data));
                    delete work_data[nik];
                    // RE INIT CAROUSEL
                    reinit_carousel('permit-split');
                    $('.' + data.width + '-carousel').slick('slickRemove');
                    $("." + nik + "-work").remove();
                    $('.' + data.width + '-carousel').slick('slickAdd');
                    reinit_carousel(data.width)

                    alert_success_scan(data);
                }
                dom_ammount_work();
                dom_ammount_permit();
                dom_count_team(data.group);
            }

            function home_permit(args) {
                nik = $('#alert_data').val();
                check_nik = nik in work_data;
                if (check_nik) {
                    $('#alert_global').fadeOut();
                    second_alert_scan(nik, work_data[nik]);
                }
            }

            function sick_permit() {
                nik = $('#alert_data').val();
                args = work_data[nik]
                $('#alert_global').fadeOut();
                var ajx = '_sick_permit';
                var id = '';
                var no_rfid = args.no_rfid;
                var object = 'dashboard_absensi';
                data = "ajax=" + ajx + "&object=" + object + "&no_rfid=" + no_rfid;
                sobad_ajax(id, data, _dom_sick_permit, false);
            }

            function _dom_sick_permit(args) {
                var nik = args.nik;
                var data = work_data[nik]
                check_nik = nik in work_data;
                if (check_nik) {
                    sick_data[nik] = data
                    var data = work_data[nik];
                    $("#sick_content").append(permit_html(nik, data));
                    delete work_data[nik];
                    // RE INIT CAROUSEL
                    reinit_carousel('permit');
                    $('.' + data.width + '-carousel').slick('slickRemove');
                    $("." + nik + "-work").remove();
                    $('.' + data.width + '-carousel').slick('slickAdd');
                    reinit_carousel(data.width)

                    alert_success_scan(data);
                }
                dom_ammount_work();
                dom_ammount_sickpermit();
                dom_count_team(data.group);
            }

            function cuti() {
                nik = $('#alert_data').val();
                args = work_data[nik]
                $('#alert_global').fadeOut();
                var ajx = '_cuti';
                var id = '';
                var no_rfid = args.no_rfid;
                var object = 'dashboard_absensi';
                data = "ajax=" + ajx + "&object=" + object + "&no_rfid=" + no_rfid;
                sobad_ajax(id, data, _dom_cuti, false);
            }

            function _dom_cuti(args) {
                var nik = args.nik;
                var data = work_data[nik]
                check_nik = nik in work_data;
                if (check_nik) {
                    cuti_data[nik] = data
                    var data = work_data[nik];
                    $("#cuti_content").append(permit_html(nik, data));
                    delete work_data[nik];
                    // RE INIT CAROUSEL
                    reinit_carousel('permit-split');
                    $('.' + data.width + '-carousel').slick('slickRemove');
                    $("." + nik + "-work").remove();
                    $('.' + data.width + '-carousel').slick('slickAdd');
                    reinit_carousel(data.width)

                    alert_success_scan(data);
                }
                dom_ammount_work();
                dom_ammount_cuti();
                dom_count_team(data.group);
            }

            function permit_change_time() {
                nik = $('#alert_data').val();
                args = work_data[nik]
                $('#alert_global').fadeOut();
                var ajx = '_permit_change_time';
                var id = '';
                var nik = args.no_rfid;;
                var object = 'dashboard_absensi';
                data = "ajax=" + ajx + "&object=" + object + "&no_rfid=" + nik;
                sobad_ajax(id, data, _dom_permit_changetime, false);
            }

            function _dom_permit_changetime(args) {
                var nik = args.nik;
                var data = work_data[nik]
                check_nik = nik in work_data;
                notwork_data[nik] = data;
                var notworkhtml = notwork_html(nik, data);
                $(".footer-carousel").append(notworkhtml);
                delete work_data[nik];
                // RE INIT CAROUSEL
                reinit_carousel('footer');
                $('.' + data.width + '-carousel').slick('slickRemove');
                $("." + nik + "-work").remove();
                $('.' + data.width + '-carousel').slick('slickAdd');
                reinit_carousel(data.width)

                alert_success_scan(data);
                dom_ammount_work();
                dom_count_team(data.group);
            }

            var settimer = 0;
            setInterval(function() {
                if (settimer === 1) {
                    $('#announcement-title').hide(1000);
                    $('#birth').slideUp(1000);
                    $('#announ-info').slideDown(1000);
                    settimer = 0;
                } else {
                    $('#announcement-title').show(1000);
                    $('#birth').slideDown(1000);
                    $('#announ-info').slideUp(1000);
                    settimer = 1;
                }
            }, 10000);
        </script>
<?php
        $contents = ob_get_clean();
        return $contents;
    }

    private static function base_url()
    {
        // return SITE . '://' . HOSTNAME . '/' . URL . '/theme/' . _theme_folder . '/assets/';
        return "http://soloabadi-server.ddns.net/system-sobad-group/asset/";
    }
}
