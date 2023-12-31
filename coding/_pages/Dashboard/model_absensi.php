<?php

class model_absensi
{
    private static $_groups = array();

    private static $_company = array();

    public static function _dummy_data_announcement()
    {
        $data = [
            'title'     => 'Speak Training',
            'date'      => 'Sabtu, 23 Sept 2023',
            'start'     => '12:00',
            'end'       => '13:00',
            'location'  => 'Unit 4'
        ];
        return $data;
    }

    public static function employe_data()
    {
        $date = date('Y-m-d');
        $whr = "AND `abs-user`.status!=0";
        $user = sobad_api::user_get_all(['ID', 'company', 'divisi', '_nickname', 'no_induk', 'picture', 'work_time', 'inserted', 'status', '_resign_date', '_entry_date', 'punish', 'no_rfid', 'id_join'], $whr);
        // $permit = sobad_api::_get_users();
        $whr = "AND type!='9' AND start_date<='$date' AND range_date>='$date' OR start_date<='$date' AND range_date='0000-00-00' AND num_day='0.0'";
        $permit = sobad_api::permit_get_all(['user,type'], $whr);
        $group = sobad_api::_get_groups();
        $company = sobad_api::_gets('company', array('ID', 'meta_value', 'meta_note', 'meta_reff'));

        $_group = array();
        foreach ($group as $key => $val) {
            $data = $val['data'];
            $group[$key]['meta_note'] = $val['data'];

            if (isset($val['data'][0])) {
                foreach ($data as $vl) {
                    array_push($_group, $vl);
                }
            }
        }

        self::$_groups = $group;
        $_permit = array(0 => 0);
        foreach ($permit as $key => $val) {
            if (!in_array($val['type'], array(3, 5, 6, 7, 8, 10))) {
                $val['type'] = 4;
            }
            $_permit[$val['user']] = $val['type'];
        }

        foreach ($user as $key => $val) {
            if (isset($val['_entry_date'])) {
                if ($date < $val['_entry_date']) {
                    unset($user[$key]);
                    continue;
                }
            }

            if ($val['status'] != 7) {
                if (!in_array($val['divisi'], $_group)) {
                    unset($user[$key]);
                    continue;
                }
            } else {
                $_date = date($val['inserted']);
                $user[$key]['no_induk'] = sobad_api::_nik_internship($val['ID']);
                $user[$key]['divisi'] = 0;
                if (isset($val['_resign_date'])) {
                    if ($date > $val['_resign_date']) {
                        sobad_api::_update_single($val['ID'], 'abs-user', array('ID' => $val['ID'], 'status' => 0, 'end_status' => 7));
                        unset($user[$key]);
                        continue;
                    }
                }
            }

            $idx = $val['ID'];
            $log = sobad_api::user_get_all(array('type', 'id_join', 'shift', 'time_in', 'time_out', 'note'), "AND `abs-user`.ID='$idx' AND `abs-user-log`._inserted='$date'");
            $_log = true;
            $check = array_filter($log);
            if (empty($check)) {
                $log[0] = array(
                    'type'        => NULL,
                    'shift'        => 0,
                    'time_in'    => NULL,
                    'time_out'    => NULL,
                    'note'        => array(
                        'pos_user'    => 1,
                        'pos_group'    => 1
                    )
                );

                $_log = false;
            } else {
                $log[0]['note'] = unserialize($log[0]['note']);
            }
            if (array_key_exists($idx, $_permit)) {
                $_libur = sobad_api::_check_holiday($date);
                if (!$_libur) {
                    $log[0]['type'] = $_permit[$idx];
                }

                if ($_log) {
                    //	sobad_db::_update_single($log[0]['id_join'],'abs-user-log',array(
                    //			'user' 		=> $idx,
                    //			'type'		=> $_permit[$idx],
                    //		)
                    //	);
                } else {
                    if (!$_libur) {
                        sobad_api::_insert_table(
                            'abs-user-log',
                            array(
                                'user'      => $idx,
                                'shift'     => $val['work_time'],
                                'type'      => $_permit[$idx],
                                '_inserted' => $date,
                                'time_in'   => '00:00:00',
                                'time_out'  => '00:00:00',
                            )
                        );
                    }
                }
            }
            $user[$key] = array_merge($user[$key], $log[0]);
        }

        return array('user' => $user, 'group' => $group, 'company' => $company);
    }

    public static function presensi_data()
    {
        $date = date('Y-m-d');
        $day = date('w');
        $args = self::employe_data();

        $group = $args['group'];
        $work = array();
        $notwork = array();
        $outcity = array();
        $dayoff = array();
        $permit = array();
        $sick = array();
        $tugas = array();
        $libur = array();
        $wfh = array();
        self::$_company = $args['company'];
        $pos = 0;


        foreach ($args['user'] as $key => $val) {
            //Check Setting Auto Shift
            $_userid = $val['ID'];
            $worktime = $val['work_time'];
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
            $_work_time = sobad_api::work_get_id($worktime, array('time_in', 'time_out', 'status'), "AND days='$day'");

            $check = array_filter($_work_time);
            if (empty($check)) {
                $_work_time = array(
                    'time_in'    => '08:00:00',
                    'time_out'    => '16:00:00'
                );
            } else {
                $_work_time = $_work_time[0];
            }

            $check_punish = sobad_api::_check_punish($val['ID'], $date);

            //check group
            $_group = self::_get_group(intval($val['divisi']));
            $_group = isset($_group[0]) ? $_group[0] : [];

            $divisi_group = isset($_group['ID']) ? $_group['ID'] : 0;
            $_capacity = isset($_group['capacity']) ? $_group['capacity'] : 0;
            $capacity = self::conversion_capacity($_capacity);
            $status_group = isset($_group['status']) ? $_group['status'] : 0;

            $grp_exclude = $status_group['group'];
            $grp_punish = $status_group['punish'];

            if ($check_punish == "1") {
                if ($grp_punish == 0) {
                    $punish = 0;
                } else {
                    $punish = 1;
                }
            } else {
                $punish = $check_punish;
            }


            $exclude = 0;
            if ($grp_exclude == 1) {
                $exclude = 1;
            }

            if (empty($val['type']) || $val['type'] == 2) {
                $notwork[$val['no_induk']] = array(
                    'group'     => $val['company'] . '-' . $divisi_group,
                    'name'      => empty($val['_nickname']) ? 'no name' : $val['_nickname'],
                    'image'     => !empty($val['notes_pict']) ? $val['notes_pict'] : 'no-profile.jpg',
                    'divisi'    => $val['meta_value_divi'],
                    'width'     => $capacity,
                    'punish'    => $punish,
                    'exclude'   => $exclude,
                    'time'      => '',
                    'shift'     => $_work_time,
                    'type'      => $val['type'],
                    'no_rfid'   => $val['no_rfid'],
                    'id_divi'   => $val['divisi'],
                    'work_time' => $val['work_time'],
                    'id_join'   => $val['id_join'],
                );
            }


            if ($val['type'] == '1') {
                $_worktime = empty($val['shift']) ? $val['work_time'] : $val['shift'];
                $_work = sobad_api::work_get_id($_worktime, array('time_in', 'time_out', 'status'), "AND days='$day'");
                $grp = $divisi_group;
                $check = array_filter($_work);
                if (empty($check)) {
                    $_work = array(
                        'time_in'    => '08:00:00',
                        'time_out'    => '16:00:00'
                    );
                } else {
                    $_work = $_work[0];
                }

                $time = substr($val['time_in'], 0, 5);
                $waktu = $time;

                $pos = $val['note']['pos_user'];


                $work[$val['no_induk']] = array(
                    'name'      => empty($val['_nickname']) ? 'no name' : $val['_nickname'],
                    'time'      => $time,
                    'image'     => !empty($val['notes_pict']) ? $val['notes_pict'] : 'no-profile.jpg',
                    'position'  => $pos,
                    'group'     => $val['company'] . '-' . $divisi_group,
                    'divisi'    => $val['meta_value_divi'],
                    'width'     => $capacity,
                    'punish'    => $punish,
                    'exclude'   => $exclude,
                    'shift'     => $_work_time,
                    'type'      => $val['type'],
                    'no_rfid'   => $val['no_rfid'],
                    'id_divi'   => $val['divisi'],
                    'work_time' => $val['work_time'],
                    'id_join'   => $val['id_join'],
                );


                $group[$grp]['position'] = isset($val['note']['pos_group']) ? $val['note']['pos_group'] : 1;
            }


            if ($val['type'] == 3) {
                $dayoff[$val['no_induk']] = array(
                    'group'     => $val['company'] . '-' . $divisi_group,
                    'name'      => empty($val['_nickname']) ? 'no name' : $val['_nickname'],
                    'image'     => !empty($val['notes_pict']) ? $val['notes_pict'] : 'no-profile.jpg',
                    'divisi'    => $val['meta_value_divi'],
                    'punish'    => $punish,
                    'exclude'   => $exclude,
                    'width'     => $capacity,
                    'time'      => '',
                    'shift'     => $_work_time,
                    'type'      => $val['type'],
                    'no_rfid'   => $val['no_rfid'],
                    'id_divi'   => $val['divisi'],
                    'work_time' => $val['work_time'],
                    'id_join'   => $val['id_join'],
                );
            }

            if ($val['type'] == 4) {
                $permit[$val['no_induk']] = array(
                    'group'     => $val['company'] . '-' . $divisi_group,
                    'name'      => empty($val['_nickname']) ? 'no name' : $val['_nickname'],
                    'image'     => !empty($val['notes_pict']) ? $val['notes_pict'] : 'no-profile.jpg',
                    'divisi'    => $val['meta_value_divi'],
                    'width'     => $capacity,
                    'punish'    => $punish,
                    'exclude'   => $exclude,
                    'time'      => '',
                    'shift'     => $_work_time,
                    'type'      => $val['type'],
                    'no_rfid'   => $val['no_rfid'],
                    'id_divi'   => $val['divisi'],
                    'work_time' => $val['work_time'],
                    'id_join'   => $val['id_join'],
                );
            }

            if ($val['type'] == 5) {
                $outcity[$val['no_induk']] = array(
                    'group'     => $val['company'] . '-' . $divisi_group,
                    'name'      => empty($val['_nickname']) ? 'no name' : $val['_nickname'],
                    'image'     => !empty($val['notes_pict']) ? $val['notes_pict'] : 'no-profile.jpg',
                    'divisi'    => $val['meta_value_divi'],
                    'width'     => $capacity,
                    'punish'    => $punish,
                    'exclude'   => $exclude,
                    'time'      => '',
                    'shift'     => $_work_time,
                    'type'      => $val['type'],
                    'no_rfid'   => $val['no_rfid'],
                    'id_divi'   => $val['divisi'],
                    'work_time' => $val['work_time'],
                    'id_join'   => $val['id_join'],
                );
            }

            if ($val['type'] == 6) {
                $libur[$val['no_induk']] = array(
                    'group'     => $val['company'] . '-' . $divisi_group,
                    'name'      => empty($val['_nickname']) ? 'no name' : $val['_nickname'],
                    'image'     => !empty($val['notes_pict']) ? $val['notes_pict'] : 'no-profile.jpg',
                    'divisi'    => $val['meta_value_divi'],
                    'width'     => $capacity,
                    'punish'    => $punish,
                    'exclude'   => $exclude,
                    'time'      => '',
                    'shift'     => $_work_time,
                    'type'      => $val['type'],
                    'no_rfid'   => $val['no_rfid'],
                    'id_divi'   => $val['divisi'],
                    'work_time' => $val['work_time'],
                    'id_join'   => $val['id_join'],
                );
            }

            if ($val['type'] == 7) {
                $tugas[$val['no_induk']] = array(
                    'group'     => $val['company'] . '-' . $divisi_group,
                    'name'      => empty($val['_nickname']) ? 'no name' : $val['_nickname'],
                    'image'     => !empty($val['notes_pict']) ? $val['notes_pict'] : 'no-profile.jpg',
                    'divisi'    => $val['meta_value_divi'],
                    'width'     => $capacity,
                    'punish'    => $punish,
                    'exclude'   => $exclude,
                    'time'      => '',
                    'shift'     => $_work_time,
                    'type'      => $val['type'],
                    'no_rfid'   => $val['no_rfid'],
                    'id_divi'   => $val['divisi'],
                    'work_time' => $val['work_time'],
                    'id_join'   => $val['id_join'],
                );
            }

            if ($val['type'] == 8) {
                $sick[$val['no_induk']] = array(
                    'group'     => $val['company'] . '-' . $divisi_group,
                    'name'      => empty($val['_nickname']) ? 'no name' : $val['_nickname'],
                    'image'     => !empty($val['notes_pict']) ? $val['notes_pict'] : 'no-profile.jpg',
                    'divisi'    => $val['meta_value_divi'],
                    'punish'    => $punish,
                    'exclude'   => $exclude,
                    'time'      => '',
                    'shift'     => $_work_time,
                    'type'      => $val['type'],
                    'no_rfid'   => $val['no_rfid'],
                    'id_divi'   => $val['divisi'],
                    'work_time' => $val['work_time'],
                    'id_join'   => $val['id_join'],
                );
            }

            if ($val['type'] == 10) {
                $wfh[$val['no_induk']] = array(
                    'group'     => $val['company'] . '-' . $divisi_group,
                    'name'      => empty($val['_nickname']) ? 'no name' : $val['_nickname'],
                    'image'     => !empty($val['notes_pict']) ? $val['notes_pict'] : 'no-profile.jpg',
                    'divisi'    => $val['meta_value_divi'],
                    'width'     => $capacity,
                    'punish'    => $punish,
                    'exclude'   => $exclude,
                    'time'      => '',
                    'shift'     => $_work_time,
                    'type'      => $val['type'],
                    'no_rfid'   => $val['no_rfid'],
                    'id_divi'   => $val['divisi'],
                    'work_time' => $val['work_time'],
                    'id_join'   => $val['id_join'],
                );
            }
        }

        $data = [
            'company'       => $args['company'],
            'group'         => $group,
            'notwork_data'  => $notwork,
            'work_data'     => $work,
            'outcity_data'  => $outcity,
            'tugas_data'    => $tugas,
            'workout_data'  => array_replace($outcity, $tugas),
            'cuti_data'     => $dayoff,
            'permit_data'   => $permit,
            'sick_data'     => $sick,
            'wfh'           => $wfh,
            'libur'         => $libur,
        ];

        return $data;
    }

    public static function _get_group($divisi = 0)
    {
        $_group = [];
        $group = self::$_groups;
        foreach ($group as $key => $val) {
            if (isset($val['ID']) && isset($val['data'])) {
                if (is_array($val['data']) && $val['data'] !== 0) {
                    if (in_array($divisi, $val['data'])) {
                        $_group[] = $val;
                    }
                } else {
                    $_group[] = $val;
                }
            }
        }
        return $_group;
    }

    public static function conversion_capacity($capacity)
    {
        switch ($capacity) {
            case 1:
            case 2:
            case 3:
                $_capacity = '20';
                break;
            case 4:
                $_capacity = '26';
                break;
            case 5:
                $_capacity = '33';
                break;
            case 6:
                $_capacity = '40';
                break;
            case 7:
                $_capacity = '46';
                break;
            case 8:
                $_capacity = '53';
                break;
            case 9:
                $_capacity = '60';
                break;
            case 10:
                $_capacity = '66';
                break;
            case 11:
                $_capacity = '73';
                break;
            case 12:
                $_capacity = '80';
                break;
            case 13:
                $_capacity = '86';
                break;
            case 14:
                $_capacity = '93';
                break;
            case 15:
                $_capacity = '100';
                break;

            default:
                $_capacity = '100';
                break;
        }

        if ($capacity >= 15) {
            $_capacity = '100';
        }



        return $_capacity;
    }
}
