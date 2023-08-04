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
        'id'        => $val['company'] . '-' . $val['ID'],
        'class'     => $val['company'] . '-' . $val['ID'],
        'width'     => $val['capacity'],
        'title'     => $val['name'],
        'qty'       => $val['qty_user'],
        'qty_in'    => '',
        'color'     => $color,
        'content'   => []
    ];
}
