<?php

$config = [];

foreach ($data['department'] as $key => $val) {

    switch ($val['ID']) {
        case 30:
            $color = 'apd';
            break;
        case 2:
            $color = 'hrd';
            break;
        case 6:
            $color = 'it';
            break;
        case 62:
            $color = 'mr';
            break;
        case 52:
            $color = 'other';
            break;
        default:
            $color = 'apd';
    }

    $config[] = [
        'id'        => $val['meta_reff'] . '-' . $val['ID'],
        'class'     => $val['meta_reff'] . '-' . $val['ID'],
        'width'     => $val['meta_note'],
        'title'     => $val['meta_value'],
        'qty'       => 0,
        'qty_in'    => '',
        'color'     => $color,
        'content'   => []
    ];
}
