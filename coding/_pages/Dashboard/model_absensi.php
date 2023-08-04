<?php

class model_absensi
{
    private static $_group = array();

    private static $_company = array();



    public static function _dummy_data_announcement()
    {
        $data = [
            'title'     => 'Speak Training Bulan Juli',
            'date'      => 'Sabtu, 29 Juli 2023',
            'start'     => '12:00',
            'end'       => '13:00',
            'location'  => 'Unit 2'
        ];
        return $data;
    }

    public static function employe_data()
    {
        $date = date('Y-m-d');
        $whr = "AND `abs-user`.status!=0";
        $user = sobad_api::user_get_all(['ID', 'company', 'divisi', '_nickname', 'no_induk', 'picture', 'work_time', 'inserted', 'status', '_resign_date', '_entry_date'], $whr);

        $permit = sobad_api::_get_users(array('user', 'type'), "AND type!='9' AND start_date<='$date' AND range_date>='$date' OR start_date<='$date' AND range_date='0000-00-00' AND num_day='0.0'");

        // $group = sobad_api::_gets('group', array('`abs-module`.ID', '`abs-module`.meta_value', '`abs-module`.meta_note', '`abs-module`.meta_reff', '`abs-module`.meta_user'));
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

        self::$_group = $group;

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
                $_libur = sobad_api::_check_holiday();
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
                                'user'         => $idx,
                                'shift'     => $val['work_time'],
                                'type'        => $_permit[$idx],
                                '_inserted'    => $date,
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
        $day = date('w');
        $args = self::employe_data();

        // $_group = array();
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
        // $group[0]['ID'] = "0";
        // $group[0]['name'] = 'Internship';
        // $group[0]['group'] = 2;
        // $group[0]['capacity'] = 100;
        // $group[0]['reff'] = 128;
        // $group[0]['punish'] = 1;
        // $_group[0] = array(0);




        // foreach ($args['group'] as $key => $val) {



        // $group[$val['ID']] = array(
        // 'ID'        => $val['ID'],
        // 'name'      => $val['meta_value'],
        // 'capacity'  => self::conversion_capacity($capacity),
        // 'reff'      => $val['meta_user'],
        // );



        // if (isset($data['status'])) {
        //     if (in_array(2, $data['status'])) {
        //         $group[$val['ID']]['group'] = 2;
        //     } else {
        //         $group[$val['ID']]['group'] = 1;
        //     }

        //     if (in_array(3, $data['status'])) {
        //         $group[$val['ID']]['punish'] = 1;
        //     } else {
        //         $group[$val['ID']]['punish'] = 0;
        //     }
        // }
        // }

        $pos = 0;

        foreach ($args['user'] as $key => $val) {
            $shift = sobad_api::_check_shift($val['ID'], $val['work_time'], date('Y-m-d'));
            $divisi_group = self::_get_group($val['divisi']);
            $capacity = self::conversion_capacity($group[$divisi_group]['capacity']);

            if (empty($val['type']) || $val['type'] == 2) {
                $notwork[$val['no_induk']] = array(
                    'group'     => $val['company'] . '-' . $divisi_group,
                    'name'      => empty($val['_nickname']) ? 'no name' : $val['_nickname'],
                    'image'     => !empty($val['notes_pict']) ? $val['notes_pict'] : 'no-profile.jpg',
                    'divisi'    => $val['meta_value_divi'],
                    'width'     => $capacity,
                    'time'      => '',
                    'shift'     => isset($shift['time_in']) ? $shift : ['time_in'    => '08:00:00', 'time_out'    => '16:00:00']
                );
            }


            if ($val['type'] == 1) {
                $punish_type = sobad_api::_check_punish($val['ID'], date('Y-m-d'));
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

                if (!isset($work[$grp])) {
                    $work[$grp] = array();
                }

                $time = substr($val['time_in'], 0, 5);
                $waktu = $time;

                $pos = $val['note']['pos_user'];

                if ($_work['status']) {
                    if ($val['time_in'] >= $_work['time_in']) {
                        $waktu = '<span style="color:red;">' . $time . '</span>';
                    }
                }

                $work[$grp][$val['no_induk']] = array(
                    'name'      => empty($val['_nickname']) ? 'no name' : $val['_nickname'],
                    'time'      => $waktu,
                    'image'     => !empty($val['notes_pict']) ? $val['notes_pict'] : 'no-profile.jpg',
                    'position'  => $pos,
                    'group'     => $val['company'] . '-' . $divisi_group,
                    'divisi'    => $val['meta_value_divi'],
                    'width'     => $capacity,
                    'type'      => $punish_type,
                );

                $group[$grp]['position'] = isset($val['note']['pos_group']) ? $val['note']['pos_group'] : 1;
            }

            if ($val['type'] == 3) {
                $dayoff[$val['no_induk']] = array(
                    'group'     => $val['company'] . '-' . $divisi_group,
                    'name'      => empty($val['_nickname']) ? 'no name' : $val['_nickname'],
                    'image'     => !empty($val['notes_pict']) ? $val['notes_pict'] : 'no-profile.jpg',
                    'divisi'    => $val['meta_value_divi'],
                    'width'     => $capacity,
                    'time'      => '',
                    'shift'     => isset($shift['time_in']) ? $shift : ['time_in'    => '08:00:00', 'time_out'    => '16:00:00']
                );
            }

            if ($val['type'] == 4) {
                $permit[$val['no_induk']] = array(
                    'group'     => $val['company'] . '-' . $divisi_group,
                    'name'      => empty($val['_nickname']) ? 'no name' : $val['_nickname'],
                    'image'     => !empty($val['notes_pict']) ? $val['notes_pict'] : 'no-profile.jpg',
                    'divisi'    => $val['meta_value_divi'],
                    'width'     => $capacity,
                    'time'      => '',
                    'shift'     => isset($shift['time_in']) ? $shift : ['time_in'    => '08:00:00', 'time_out'    => '16:00:00']
                );
            }

            if ($val['type'] == 5) {
                $outcity[$val['no_induk']] = array(
                    'group'     => $val['company'] . '-' . $divisi_group,
                    'name'      => empty($val['_nickname']) ? 'no name' : $val['_nickname'],
                    'image'     => !empty($val['notes_pict']) ? $val['notes_pict'] : 'no-profile.jpg',
                    'divisi'    => $val['meta_value_divi'],
                    'width'     => $capacity,
                    'time'      => '',
                    'shift'     => $shift,
                );
            }

            if ($val['type'] == 6) {
                $libur[$val['no_induk']] = array(
                    'group'     => $val['company'] . '-' . $divisi_group,
                    'name'      => empty($val['_nickname']) ? 'no name' : $val['_nickname'],
                    'image'     => !empty($val['notes_pict']) ? $val['notes_pict'] : 'no-profile.jpg',
                    'divisi'    => $val['meta_value_divi'],
                    'width'     => $capacity,
                    'time'      => '',
                    'shift'     => isset($shift['time_in']) ? $shift : ['time_in'    => '08:00:00', 'time_out'    => '16:00:00']
                );
            }

            if ($val['type'] == 7) {
                $tugas[$val['no_induk']] = array(
                    'name'    => empty($val['_nickname']) ? 'no name' : $val['_nickname'],
                    'image'    => !empty($val['notes_pict']) ? $val['notes_pict'] : 'no-profile.jpg',
                    'group'    => $divisi_group,
                    'class'    => 'col-md-6'
                );
            }

            if ($val['type'] == 8) {
                $sick[$val['no_induk']] = array(
                    'group'     => $val['company'] . '-' . $divisi_group,
                    'name'      => empty($val['_nickname']) ? 'no name' : $val['_nickname'],
                    'image'     => !empty($val['notes_pict']) ? $val['notes_pict'] : 'no-profile.jpg',
                    'divisi'    => $val['meta_value_divi'],
                    'width'     => $capacity,
                    'time'      => '',
                    'shift'     => isset($shift['time_in']) ? $shift : ['time_in'    => '08:00:00', 'time_out'    => '16:00:00']
                );
            }

            if ($val['type'] == 10) {
                $wfh[$val['no_induk']] = array(
                    'group'     => $val['company'] . '-' . $divisi_group,
                    'name'      => empty($val['_nickname']) ? 'no name' : $val['_nickname'],
                    'image'     => !empty($val['notes_pict']) ? $val['notes_pict'] : 'no-profile.jpg',
                    'divisi'    => $val['meta_value_divi'],
                    'width'     => $capacity,
                    'time'      => '',
                    'shift'     => isset($shift['time_in']) ? $shift : ['time_in'    => '08:00:00', 'time_out'    => '16:00:00']
                );
            }
        }

        $data = [
            'company'       => $args['company'],
            'group'         => $group,
            'notwork_data'  => $notwork,
            'work_data'     => $work,
            'outcity_data'  => $outcity,
            'cuti_data'     => $dayoff,
            'permit_data'   => $permit,
            'sick_data'     => $sick,
            'outside_work'  => $tugas,
            'wfh'           => $wfh,
            'libur'         => $libur,
        ];

        return $data;
    }

    private static function _get_group($divisi = 0)
    {
        $group = self::$_group;

        foreach ($group as $key => $val) {
            if (isset($val['data'][0])) {
                if (in_array($divisi, $val['data'])) {
                    return $key;
                }
            }
        }
        return 0;
    }

    private static function _get_width($group = [], $id = 0)
    {
        $width = [];

        foreach ($group as $val) {
        }
    }

    public static function conversion_capacity($capacity)
    {
        switch ($capacity) {
            case 1:
                $_capacity = '20';
                break;
            case 2:
                $_capacity = '20';
                break;
            case 3:
                $_capacity = '20';
                break;
            case 4:
                $_capacity = '20';
                break;
            case 5:
                $_capacity = '30';
                break;
            case 6:
                $_capacity = '30';
                break;
            case 7:
                $_capacity = '40';
                break;
            case 8:
                $_capacity = '50';
                break;
            case 9:
                $_capacity = '50';
                break;
            case 10:
                $_capacity = '50';
                break;
            case 11:
                $_capacity = '60';
                break;
            case 12:
                $_capacity = '60';
                break;
            case 13:
                $_capacity = '60';
                break;
            case 14:
                $_capacity = '70';
                break;
            case 15:
                $_capacity = '70';
                break;
            case 16:
                $_capacity = '70';
                break;
            case $capacity >= 17:
                $_capacity = '100';
                break;
        }
        return $_capacity;
    }
}
