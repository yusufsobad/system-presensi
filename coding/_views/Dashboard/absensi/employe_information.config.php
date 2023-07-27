<?php
$base_url = SITE . '://' . HOSTNAME . '/' . URL;
$base_url = $base_url .  "/theme/absensi/assets/image/icon/";
$config = [
    'color'     => 'light',
    'class'     => 'pl-sm pt-xs pr-sm pb-xs card-information',
    'func'      => 'sobad_grid',
    'data'      => [
        [
            'col'   => '12',
            'func'  => 'sobad_grid',
            'data'  => [
                [
                    'col'   => '2',
                    'class' => 'w-20 space',
                    'func'  => 'card',
                    'data'  => [
                        'color' => 'light-grey',
                        'class' => 'pt-xs pb-xs text-center',
                        'func'  => 'free_html',
                        'data'  => ' <p class="black">Cuti</p>
                                            <h2 id="ammount-cuti" class="purple">0</h2>'
                    ]
                ],
                [
                    'col'   => '2',
                    'class' => 'w-20 space',
                    'func'  => 'card',
                    'data'  => [
                        'color' => 'light-grey',
                        'class' => 'pt-xs pb-xs text-center',
                        'func'  => 'free_html',
                        'data'  => ' <p class="black">Sakit</p>
                                            <h2 id="ammount-sick" class="purple">0</h2>'
                    ]
                ],
                [
                    'col'   => '2',
                    'class' => 'w-20 space',
                    'func'  => 'card',
                    'data'  => [
                        'color' => 'light-grey',
                        'class' => 'pt-xs pb-xs text-center',
                        'func'  => 'free_html',
                        'data'  => ' <p class="black">Izin</p>
                                            <h2 id="ammount-permit" class="purple">0</h2>'
                    ]
                ],
                [
                    'col'   => '2',
                    'class' => 'w-20 space',
                    'func'  => 'card',
                    'data'  => [
                        'color' => 'light-grey',
                        'class' => 'pt-xs pb-xs text-center',
                        'func'  => 'free_html',
                        'data'  => ' <p class="black">Luar Kota</p>
                                            <h2 id="ammount-outcity" class="purple">0</h2>'
                    ]
                ],
                [
                    'col'   => '2',
                    'class' => 'w-20 space',
                    'func'  => 'card',
                    'data'  => [
                        'color' => 'light-grey',
                        'class' => 'pt-xs pb-xs text-center',
                        'func'  => 'free_html',
                        'data'  => ' <p class="black">Tugas Luar</p>
                                            <h2 class="purple">0</h2>'
                    ]
                ],
            ]
        ],
        [
            'col'   => '12 mt-xs',
            'func'  => 'sobad_grid',
            'data'  => [
                [
                    'col'   => '4',
                    'class' => 'space',
                    'func'  => 'card',
                    'data'  => [
                        'color' => 'light-grey',
                        'class' => 'p-xs',
                        'func'  => 'free_html',
                        'data'  => '<p class="black">Total</p>
                                            <h6 class="black">Karyawan</h6>
                                            <div class="row pl-xs pr-xs pt-xs">
                                                <div class="col-xs-5 space">
                                                    <img width="90%" src="' . $base_url . 'ic-employe.png" alt="">
                                                </div>
                                                <div class="col-xs-7 text-center space">
                                                    <h2 class="purple bold">0</h2>
                                                </div>
                                            </div>'
                    ]
                ],
                [
                    'col'   => '4',
                    'class' => 'space',
                    'func'  => 'card',
                    'data'  => [
                        'color' => 'light-grey',
                        'class' => 'p-xs',
                        'func'  => 'free_html',
                        'data'  => '<p class="black">Total</p>
                                            <h6 class="black">Internship</h6>
                                            <div class="row pl-xs pr-xs pt-xs">
                                                <div class="col-xs-5 space">
                                                    <img width="90%" src="' . $base_url . 'ic-internship.png" alt="">
                                                </div>
                                                <div class="col-xs-7 text-center space">
                                                    <h2 class="purple bold">0</h2>
                                                </div>
                                            </div>'
                    ]
                ],
                [
                    'col'   => '4',
                    'class' => 'space',
                    'func'  => 'card',
                    'data'  => [
                        'color' => 'light-purple',
                        'class' => 'p-xs',
                        'func'  => 'free_html',
                        'data'  => '<p class="black">Team</p>
                                            <h6 class="black">Masuk</h6>
                                            <div class="row pl-xs pr-xs pt-xs">
                                                <div class="col-xs-5 space">
                                                    <img width="90%" src="' . $base_url . 'ic-team.png" alt="">
                                                </div>
                                                <div class="col-xs-7 text-center space">
                                                    <h2 id="ammount-work" class="purple bold">0</h2>
                                                </div>
                                            </div>'
                    ]
                ],
            ]
        ]
    ]

];
