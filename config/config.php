<?php
//The main config file
define('BASE_URL', 'http://localhost/t2_lang/');
define('ENV', 'dev');
define('DEFAULT_MODULE', 'welcome');
define('DEFAULT_METHOD', 'index');
define('MODULE_ASSETS_TRIGGER', '_module');
define('ERROR_404', 'templates/error_404');

define('AVAILABLE_LANGUAGES', ['en', 'fr', 'vn']);
define('DEFAULT_LANGUAGE', 'en');
define('SHOW_MISSING_LANGUAGE_MESSAGE', true);

$interceptors = [
    'language' => 'init'
];
define('INTERCEPTORS', $interceptors);
