<?php
$routes = [
    'tg-admin' => 'trongate_administrators/login',
    'tg-admin/submit_login' => 'trongate_administrators/submit_login',
    'en/(:any)/(:any)' => '$1/$2',
    'fr/(:any)/(:any)' => '$1/$2',
    'vn/(:any)/(:any)' => '$1/$2'
];
define('CUSTOM_ROUTES', $routes);
