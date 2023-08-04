<?php

$config = [];

foreach ($data['department'] as $key => $val) {
    $color = '#7c7c7c';
    if (isset($val['meta_note'])) {
        $color = empty($val['meta_note']) ? $color : $val['meta_note'];
    }
    $config[] = [
        'id'        => $val['company'] . '-' . $val['ID'],
        'class'     => $val['company'] . '-' . $val['ID'],
        'width'     => model_absensi::conversion_capacity($val['capacity']),
        'title'     => $val['name'],
        'qty'       => $val['qty_user'],
        'qty_in'    => '',
        'color'     => $color,
        'content'   => []
    ];
}
