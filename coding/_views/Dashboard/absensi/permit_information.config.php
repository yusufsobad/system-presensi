<?php

$config = [
    'color' => 'light',
    'class' => 'p-xs card-information',
    'func'  => 'sobad_grid',
    'data'  => [
        [
            'col'   => '6',
            'func'  => 'sobad_grid',
            'data'  => [
                [
                    'col'   => '12',
                    'func'  => 'free_html',
                    'data'  => '<div class="w-fit bg-light-grey radius-xs space">Cuti</div>'
                ],
                [
                    'id'    => 'cuti_content',
                    'class' => 'permit_content permit-split-carousel',
                    'col'   => '12',
                    'func'  => 'user_card',
                    'data'  => []
                ],
            ]
        ],
        [
            'col'   => '6',
            'func'  => 'sobad_grid',
            'data'  => [
                [
                    'col'   => '12',
                    'func'  => 'free_html',
                    'data'  => '<div class="w-fit bg-light-grey radius-xs space">Izin</div>'
                ],
                [
                    'id'    => 'permit_content',
                    'class' => 'permit_content permit-split-carousel',
                    'col'   => '12',
                    'func'  => 'user_card',
                    'data'  => []
                ],
            ]
        ],
        [
            'col'   => '12',
            'func'  => 'sobad_grid',
            'data'  => [
                [
                    'col'   => '12',
                    'func'  => 'free_html',
                    'data'  => '<div class="w-fit bg-light-grey radius-xs space">Luar Kota / Tugas Luar</div>'
                ],
                [
                    // 'id'    => 'out_city_content',
                    'id'    => 'workout_content',
                    'class' => 'permit_content permit-carousel',
                    'col'   => '12',
                    'func'  => 'user_card',
                    'data'  => []
                ],
            ]
        ],
        [
            'col'   => '12',
            'func'  => 'sobad_grid',
            'data'  => [
                [
                    'col'   => '12',
                    'func'  => 'free_html',
                    'data'  => '<div class="w-fit bg-light-grey radius-xs space">Sakit</div>'
                ],
                [
                    'id'    => 'sick_content',
                    'class' => 'permit_content permit-carousel',
                    'col'   => '12',
                    'func'  => 'user_card',
                    'data'  => []
                ],
            ]
        ],
    ]
];
