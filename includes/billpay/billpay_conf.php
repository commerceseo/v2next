<?php
if (defined('CURRENT_TEMPLATE') === false) define('CURRENT_TEMPLATE', 'dummy');

return array(
    'shop-system' => 'commerceSeo',

    'template'    => array(
        'giropay'             => array(
            'image-buttons' => false,
            'btn-back'      => array(
                'image'  => '',
                'text'   => '<span class="css_img_button">Zur&uuml;ck</span>',
                'height' => '24px',
                'width'  => '124px',
            ),
            'btn-continue'  => array(
                'image'  => '',
                'text'   => '<span class="css_wk_img_button">Weiter zu Giropay</span>',
                'height' => '24px',
                'width'  => '124px',
            ),
        ),
        'waiting-for-approve' => array(
            'img-loading'    => DIR_WS_IMAGES . 'ajax-loader.gif',
            'img-loading-ok' => DIR_WS_IMAGES . 'img/blog/tick.gif',
        ),
    ),
);