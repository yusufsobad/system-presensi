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
        'id'        => '91' . '-' . $val['ID'],
        'class'     => '91' . '-' . $val['ID'],
        'width'     => $val['meta_note'],
        'title'     => $val['meta_value'],
        'qty'       => 0,
        'qty_in'    => '',
        'color'     => $color,
        'content'   => []
    ];
}
