<?php
include 'model_absensi.php';

class dashboard_absensi extends _page
{
    protected static $object = 'dashboard_absensi';

    protected static $loc_view = 'Dashboard/absensi';

    public static function index()
    {
        $notwork_data = model_absensi::_dummy_data();
        $birthday_data = model_absensi::_dummy_data_birthday();
        $announcement_data = model_absensi::_dummy_data_announcement();
        $args = [
            'notwork_data'      => $notwork_data,
            'work_data'         => [],
            'outcity_data'      => [],
            'permit_data'       => [],
            'cuti_data'         => [],
            'sick_data'         => [],
            'birthday_data'     => $birthday_data,
            'announcement_data' => $announcement_data,
        ];
        self::scan();
        $title = '
                    <h2 class="bold grey">This is</h2>
                    <h1 class="bold black">Our Teams</h1>
        ';
        $grid_config = self::grid();

        $config = [
            'title'     => $title,
            'func'      => 'sobad_grid',
            'data'      => $grid_config,
            'script'    => self::script(),
            'args'      => $args
        ];
        return $config;
    }

    public static function grid()
    {
        $data = [];
        $permision_information =  self::_loadView('employe_information', $data);
        $birthday_information = self::_loadView('birthday_information', $data);
        $employe_information = self::_loadView('permit_information', $data);

        $sag_content = self::sobad_group_content();
        $sobad_content = self::sobad_content();
        $kmi_content = self::kmi_content();
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

    public static function sobad_group_content()
    {
        $base_url = self::base_url();
        $base_url = $base_url .  "image/icon/";

        $data_company = model_absensi::_dummy_data_company();
        $data_company = $data_company[0];

        $data_department = model_absensi::_dummy_data_dpartment()[0];

        $data = [
            'department'    => $data_department,
        ];
        $content = self::_loadView('sobad_group/content', $data);

        $base_url = self::base_url();
        $base_url = $base_url .  "image/icon/";
        $config = [
            'color' => 'light',
            'title' => $data_company['meta_value'],
            'logo'  => $base_url . $data_company['meta_note'],
            'count' => 0,
            'func'  => 'card_divisi',
            'data'  => $content
        ];
        return $config;
    }

    public static function sobad_content()
    {
        $base_url = self::base_url();
        $base_url = $base_url .  "image/icon/";

        $data_company = model_absensi::_dummy_data_company();
        $data_company = $data_company[1];

        $data_department = model_absensi::_dummy_data_dpartment()[1];
        $data = [
            'department'    => $data_department,
        ];
        $content = self::_loadView('sobad/content', $data);

        $base_url = self::base_url();
        $base_url = $base_url .  "image/icon/";
        $config = [
            'color' => 'light',
            'title' => $data_company['meta_value'],
            'logo'  => $base_url . $data_company['meta_note'],
            'count' => 0,
            'func'  => 'card_divisi',
            'data'  => $content
        ];
        return $config;
    }

    public static function kmi_content()
    {
        $base_url = self::base_url();
        $base_url = $base_url .  "image/icon/";

        $data_company = model_absensi::_dummy_data_company();
        $data_company = $data_company[2];

        $data_department = model_absensi::_dummy_data_dpartment()[2];

        $data = [
            'department'    => $data_department,
        ];
        $content = self::_loadView('kmi/content', $data);

        $base_url = self::base_url();
        $base_url = $base_url .  "image/icon/";
        $config = [
            'color'     => 'light',
            'title'     => $data_company['meta_value'],
            'logo'      => $base_url . $data_company['meta_note'],
            'count'     => 0,
            'func'      => 'card_divisi',
            'size_logo' => '123px',
            'data'      => $content
        ];
        return $config;
    }


    public static function scan()
    {
?>
        <form>
            <div class="row m-sm">
                <div class="col-xs-3">
                    <div class="input-group">
                        <input id="nik" type="text" class="form-control" placeholder="Insert NIK">
                        <span class="input-group-btn">
                            <button class="btn btn-default" type="button" onclick="scan(this)">Submit</button>
                        </span>
                    </div>
                </div>
            </div>
        </form>
    <?php
    }

    public static function _check_scan()
    {
        $nik = $_POST['nik'];
        $args = json_decode($_POST['args'], true);
        $time_now =  date("H:i:s");
        $time_in = date("H:i");
        $time_default = "08:00:00";
        $data_args = model_absensi::_dummy_data();

        // CEK ADA APAKAH ADA NIK 
        if (isset($data_args[$nik])) {
            $data = $data_args[$nik];
            // CEK APAKAH TELAT ABSEN
            if ($time_now >= $time_default) {
                $data['type'] = 1;
            } else {
                $data['type'] = 0;
            }
        }
        $data['time'] = $time_in;

        $data = [
            'data' => $data,
            'nik'   => $nik,
        ];

        return $data;
    }

    public static function _go_out_city()
    {
        $nik = $_POST['nik'];
        $data_args = model_absensi::_dummy_data();
        $data = $data_args[$nik];

        $data = [
            'data' => $data,
            'nik'   => $nik,
        ];
        return $data;
    }

    public static function _permit()
    {
        $nik = $_POST['nik'];
        $data_args = model_absensi::_dummy_data();
        $data = $data_args[$nik];

        $data = [
            'data' => $data,
            'nik'   => $nik,
        ];
        return $data;
    }

    public static function _sick_permit()
    {
        $nik = $_POST['nik'];
        $data_args = model_absensi::_dummy_data();
        $data = $data_args[$nik];

        $data = [
            'data' => $data,
            'nik'   => $nik,
        ];
        return $data;
    }

    public static function _cuti()
    {
        $nik = $_POST['nik'];
        $data_args = model_absensi::_dummy_data();
        $data = $data_args[$nik];

        $data = [
            'data' => $data,
            'nik'   => $nik,
        ];
        return $data;
    }

    public static function _permit_change_time()
    {
        $nik = $_POST['nik'];
        $data_args = model_absensi::_dummy_data();
        $data = $data_args[$nik];

        $data = [
            'data' => $data,
            'nik'   => $nik,
        ];
        return $data;
    }

    public static function script()
    {
        ob_start();

    ?>
        <script>
            // SCAN DATA MASUK
            function scan(data) {
                var _nik = $('#nik').val();
                var ajx = '_check_scan';
                var id = '';
                var nik = _nik;
                var args = JSON.stringify(notwork_data);
                var object = 'dashboard_absensi';
                data = "ajax=" + ajx + "&object=" + object + "&nik=" + nik + "&args=" + args;
                sobad_ajax(id, data, _dom_scan_work, false);
            }

            // DOM SCAN MASUK
            function _dom_scan_work(args) {
                var data = args.data;
                var nik = args.nik;
                in_work = nik in work_data;
                out_city = nik in outcity_data;
                sick = nik in sick_data;
                _permit = nik in permit_data;
                _cuti = nik in cuti_data;
                time_work = "08.00";
                time_go_home = "17.00";
                trial = "#" + nik + "-work"
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
                            alert_success_scan_home(data);
                        } else { // JIKA SCAN SEBELUM JAM PULANG
                            alert_scan(nik, work_data[nik]);
                        }
                    } else {
                        alert_already_scan(data);
                    }
                } else { //JIKA NIK TIDAK ADA DI WORK_DATA
                    if (out_city) { // JIKA NIK ADA DI OUTCITY_DATA
                        if (data.time <= time_go_home) { // JIKA SCAN SEBELUM JAM PULANG
                            var workhtml = work_html(nik, data);
                            $("#" + data.group + "").append(workhtml);
                            work_data[nik] = data;
                            delete outcity_data[nik];
                            // REINIT ===============================
                            reinit_carousel(data.width)
                            $('.permit-carousel').slick('slickRemove');
                            $("." + nik + "-permit").remove();
                            $('.permit-carousel').slick('slickAdd');
                            reinit_carousel('permit')
                            // END REINIT ===========================
                            dom_ammount_outcity();
                            alert_success_scan(data);
                        } else { // JIKA SCAN SESUDAH JAM PULANG
                            notwork_data[nik] = data;
                            delete outcity_data[nik];
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
                            alert_success_scan_home(data);
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
                            alert_success_scan_home(data);
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
                            alert_success_scan_home(data);
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
                            alert_success_scan_home(data);
                        }
                    } else { // JIKA NIK TIDAK ADA DI OUTCITY_DATA & SICK_DATA & WORK_DATA
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
                dom_ammount_work();
                dom_count_team(data.group);
            }

            // ACTION KETIKA USER MEMILIH LUAR KOTA
            function go_out_city(data) {
                nik = $('#alert_data').val();
                $('#alert_global').fadeOut();
                var ajx = '_go_out_city';
                var id = '';
                var nik = nik;
                var object = 'dashboard_absensi';
                data = "ajax=" + ajx + "&object=" + object + "&nik=" + nik;
                sobad_ajax(id, data, _dom_out_city, false);
            }

            // DOM CONTENT LUAR KOTA
            function _dom_out_city(args) {
                var data = args.data;
                var nik = args.nik;

                check_nik = nik in work_data;
                if (check_nik) {
                    outcity_data[nik] = data
                    var data = work_data[nik];
                    $("#out_city_content").append(outcity_html(nik, data));
                    delete work_data[nik];
                    // RE INIT CAROUSEL
                    reinit_carousel('permit')
                    $('.' + data.width + '-carousel').slick('slickRemove');
                    $("." + nik + "-work").remove();
                    $('.' + data.width + '-carousel').slick('slickAdd');
                    reinit_carousel(data.width)
                    alert_success_scan(data);
                }
                dom_ammount_work();
                dom_ammount_outcity();
                dom_count_team(data.group);
            }

            // ACTION KETIKA USER MEMILIH IZIN
            function permit(data) {
                nik = $('#alert_data').val();
                $('#alert_global').fadeOut();
                var ajx = '_permit';
                var id = '';
                var nik = nik;
                var object = 'dashboard_absensi';
                data = "ajax=" + ajx + "&object=" + object + "&nik=" + nik;
                sobad_ajax(id, data, _dom_permit, false);
            }

            // DOM CONTENT IZIN
            function _dom_permit(args) {
                var data = args.data;
                var nik = args.nik;
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
                $('#alert_global').fadeOut();
                var ajx = '_sick_permit';
                var id = '';
                var nik = nik;
                var object = 'dashboard_absensi';
                data = "ajax=" + ajx + "&object=" + object + "&nik=" + nik;
                sobad_ajax(id, data, _dom_sick_permit, false);
            }

            function _dom_sick_permit(args) {
                var data = args.data;
                var nik = args.nik;
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
                }
                dom_ammount_work();
                dom_ammount_sickpermit();
                dom_count_team(data.group);
            }

            function cuti() {
                nik = $('#alert_data').val();
                $('#alert_global').fadeOut();
                var ajx = '_cuti';
                var id = '';
                var nik = nik;
                var object = 'dashboard_absensi';
                data = "ajax=" + ajx + "&object=" + object + "&nik=" + nik;
                sobad_ajax(id, data, _dom_cuti, false);
            }

            function _dom_cuti(args) {
                var data = args.data;
                var nik = args.nik;
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
                }
                dom_ammount_work();
                dom_ammount_cuti();
                dom_count_team(data.group);
            }

            function permit_change_time() {
                nik = $('#alert_data').val();
                $('#alert_global').fadeOut();
                var ajx = '_permit_change_time';
                var id = '';
                var nik = nik;
                var object = 'dashboard_absensi';
                data = "ajax=" + ajx + "&object=" + object + "&nik=" + nik;
                sobad_ajax(id, data, _dom_permit_changetime, false);
            }

            function _dom_permit_changetime(args) {
                var data = args.data;
                var nik = args.nik;
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
                dom_ammount_work();
                dom_count_team(data.group);
            }

            var settimer = 0;
            setInterval(function() {
                if (settimer === 1) {
                    $('.birthday').show(500);
                    $('#announcement-title').show(500);
                    $('#announ-info').hide(500);
                    settimer = 0;
                } else {
                    $('#announcement-title').hide(500);
                    $('.birthday').hide(500);
                    $('#announ-info').show(500);
                    settimer = 1;
                }
            }, 30000);
        </script>
<?php
        $contents = ob_get_clean();
        return $contents;
    }

    private function base_url()
    {
        return SITE . '://' . HOSTNAME . '/' . URL . '/theme/' . _theme_folder . '/assets/';
    }
}
