<?php

class model_absensi
{
    public static function _dummy_data()
    {
        $data = [
            "001" => [
                'group'     => '89-30',
                'image'     => 'erwin-min.png',
                'name'      => 'Erwin',
                'width'     => '20',
                'divisi'    => 'APD',
                'time'      => '',
                'punish'    => '',
            ],
            "002" => [
                'group'     => '89-30',
                'image'     => '176. Naufal.png',
                'name'      => 'Noufal',
                'width'     => '20',
                'divisi'    => 'APD',
                'time'      => '',
                'punish'    => '',
            ],
            "003" => [
                'group'     => '89-6',
                'image'     => 'Wahyu.png',
                'name'      => 'Wahyu',
                'width'     => '20',
                'divisi'    => 'IT',
                'time'      => '',
                'punish'    => '',
            ],
            "004" => [
                'group'     => '89-6',
                'image'     => 'b_gading.png',
                'name'      => 'Gading',
                'width'     => '20',
                'divisi'    => 'IT',
                'time'      => '',
                'punish'    => '',
            ],
        ];
        return $data;
    }
    public static function _data_outwork()
    {
        $whr = "AND `abs-user`.status!=0";
        $user = sobad_user::get_all(array('ID', 'divisi', '_nickname', 'no_induk', 'picture', 'work_time', 'inserted', 'status', '_resign_date', '_entry_date'), $whr);
        return $user;
    }



    public static function _get_company($id = 0)
    {
        $where = "AND meta_key='company' AND ID='$id'";
        $data = sobad_module::get_all(['ID', 'meta_value', 'meta_note'], $where);
        return $data;
    }

    public static function _get_department($id = 0)
    {
        $where = "AND meta_key='department' AND meta_reff='$id'";
        $data = sobad_module::get_all(['ID', 'meta_value', 'meta_note', 'meta_reff'], $where);
        return $data;
    }
}
