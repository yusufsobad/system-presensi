<?php
include 'model_absensi.php';

class dashboard_absensi extends _page
{
    protected static $object = 'dashboard_absensi';

    protected static $loc_view = 'Dashboard/absensi';

    public static function index()
    {
        $args = model_absensi::_dummy_data();
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
            'color' => 'light',
            'title' => $data_company['meta_value'],
            'logo'  => $base_url . $data_company['meta_note'],
            'count' => 0,
            'func'  => 'card_divisi',
            'data'  => $content
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

        return $nik;
    }

    public static function _permit()
    {
        $nik = $_POST['nik'];

        return $nik;
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
                time_work = "08.00";
                time_go_home = "16.00";
                trial = "#" + nik + "-work"

                if (in_work) { // JIKA NIK ADA DI WORK_DATA
                    if (data.time >= time_work) { // JIKA SCAN LEBIH DARI JAM MASUK
                        if (data.time >= time_go_home) { // JIKA SCAN SESUDAH JAM PULANG
                            notwork_data[nik] = data;
                            delete work_data[nik];
                            $("#" + nik + "-work").remove()
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
                            $("#" + nik + "-permit").remove();
                            dom_ammount_outcity();
                            alert_success_scan(data);
                        } else { // JIKA SCAN SESUDAH JAM PULANG
                            notwork_data[nik] = data;
                            delete work_data[nik];
                            $("#" + nik + "-permit").remove()
                            alert_success_scan_home(data);
                            destroyCarousel('footer');
                        }
                    } else { // JIKA NIK TIDAK ADA DI OUTCITY_DATA
                        var workhtml = work_html(nik, data);
                        $("#" + data.group + "").append(workhtml);
                        work_data[nik] = data;
                        delete notwork_data[nik];
                        $("#" + nik + "-notwork").remove();
                        alert_success_scan(data);
                        destroyCarousel(data.width);
                    }
                }

                dom_ammount_work();
                dom_count_team();
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
                destroyCarousel('permit');
            }

            // DOM CONTENT LUAR KOTA
            function _dom_out_city(args) {
                var nik = args;
                check_nik = nik in work_data;
                if (check_nik) {
                    outcity_data[nik] = data
                    var data = work_data[nik];
                    $("#out_city_content").append(outcity_html(nik, data));
                    delete work_data[nik];
                    // RE INIT CAROUSEL
                    $("#" + nik + "-work").remove();
                }
                dom_ammount_work();
                dom_ammount_outcity();
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
                var nik = args;
                check_nik = nik in work_data;
                if (check_nik) {
                    permit_data[nik] = data
                    var data = work_data[nik];
                    $("#permit_content").append(permit_html(nik, data));
                    delete work_data[nik];


                    // RE INIT CAROUSEL
                    if ($(".permit-split-carousel").hasClass('slick-initialized')) {
                        $(".permit-split-carousel").slick('unslick');
                        $("#" + nik + "-work").remove();
                        $(".permit-split-carousel").slick({
                            slidesToShow: 2,
                            slidesToScroll: 1,
                            autoplay: true,
                            autoplaySpeed: 2000,
                            arrows: false,
                        });
                    }
                }
                dom_ammount_work();
                dom_ammount_permit();
            }

            function home_permit(args) {
                nik = $('#alert_data').val();
                check_nik = nik in work_data;
                if (check_nik) {
                    $('#alert_global').fadeOut();
                    second_alert_scan(nik, work_data[nik]);
                }
            }
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