<?php

$GLOBALS['BE_MOD']['system']['bzapi'] = array
(
    'tables'     => array('tl_bzapi_requestlog'),
    'icon'       => 'system/themes/flexible/images/log.gif'
);

$GLOBALS['ISO_HOOKS']['postCheckout'][] = array('BzApi\BzClient', 'handlePostCheckout');