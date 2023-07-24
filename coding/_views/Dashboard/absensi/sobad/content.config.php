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
        'id'        => '90' . '-' . $val['ID'],
        'class'     => '90' . '-' . $val['ID'],
        'width'     => $val['meta_note'],
        'title'     => $val['meta_value'],
        'qty'       => 0,
        'qty_in'    => '',
        'color'     => $color,
        'content'   => []
    ];
}
