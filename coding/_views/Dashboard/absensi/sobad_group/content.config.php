<?php

$config = [];

foreach ($data['department'] as $key => $val) {
    $color = '#7c7c7c';
    if (isset($val['color'])) {
        $color = empty($val['color']) ? $color : $val['color'];
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
