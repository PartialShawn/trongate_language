<?php
//The main config file
define('BASE_URL', 'http://localhost/t2_lang/');
define('ENV', 'live');
define('DEFAULT_MODULE', 'welcome');
define('DEFAULT_METHOD', 'index');
define('MODULE_ASSETS_TRIGGER', '_module');
define('ERROR_404', 'templates/error_404');

define('AVAILABLE_LANGUAGES', ['en', 'fr', 'vn']);
define('DEFAULT_LANGUAGE', 'en');

$interceptors = [
    'language' => 'before'
];
define('INTERCEPTORS', $interceptors);
