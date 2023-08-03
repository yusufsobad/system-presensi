<?php

$config = [];

foreach ($data['department'] as $key => $val) {
    switch ($val['ID']) {
        case 94:
            $color = 'ppic';
            break;
        case 95:
            $color = 'enginer';
            break;
        case 96:
            $color = 'teknisi';
            break;
        case 97:
            $color = 'marketing';
            break;
        case 98:
            $color = 'production';
            break;
        default:
            $color = 'deep-grey';
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
