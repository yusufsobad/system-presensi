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
            $color = 'grey';
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
