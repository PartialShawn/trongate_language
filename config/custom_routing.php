<?php
$routes = [
    'tg-admin' => 'trongate_administrators/login',
    'tg-admin/submit_login' => 'trongate_administrators/submit_login',
    '([a-z]{2})/(:any)/(:any)' => '$2/$3',
    '([a-z]{2})/(:any)' => '$2'
];
define('CUSTOM_ROUTES', $routes);
