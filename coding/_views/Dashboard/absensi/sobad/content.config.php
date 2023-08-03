<?php

$config = [];

foreach ($data['department'] as $key => $val) {

    switch ($val['ID']) {
        case 3:
            $color = 'ppic';
            break;
        case 4:
            $color = 'enginer';
            break;
        case 7:
            $color = 'teknisi';
            break;
        case 8:
            $color = 'marketing';
            break;
        case 8:
            $color = 'production';
            break;
        case 93:
            $color = 'intern';
            break;
        default:
            $color = 'apd';
    }

    $config[] = [
        'id'        => $val['reff'] . '-' . $val['ID'],
        'class'     => $val['reff'] . '-' . $val['ID'],
        'width'     => $val['capacity'],
        'title'     => $val['name'],
        'qty'       => 0,
        'qty_in'    => '',
        'color'     => $color,
        'content'   => []
    ];
}
