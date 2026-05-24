<?php

session_set_cookie_params([
    'lifetime' => 60 * 60 * 24 * 30, // 30 días
    'path' => '/',
    'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
    'httponly' => true,
    'samesite' => 'Strict',
]);

session_start();

$directory = __DIR__;
// $app = 'poke-fame';
define('DEV', $_SERVER['SERVER_NAME'] === 'localhost' || $_SERVER['SERVER_NAME'] === '127.0.0.1');
define('BASE_PATH', $directory);
define('CLASSES_PATH', $directory . '/classes');
define('VIEWS_PATH', $directory . '/pages');
define('LAYOUTS_PATH', $directory . '/layouts');
define('STORAGE_PATH', $directory . '/storage');

define('BASE_URI', '/');
define('JS_URI', BASE_URI . 'assets/js');
define('CSS_URI', BASE_URI . 'assets/css');
define('CURRENT_URL', $_SERVER['REQUEST_URI']);
define('PATHNAME', rtrim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/'));
define('DEFAULT_IMAGE', 'https://placeholdit.com/400x400/dddddd/999999');

require BASE_PATH . '/functions.php';

spl_autoload_register(function ($class) {
	$file = CLASSES_PATH . '/' . $class . '.php';
	if (file_exists($file)) require_once $file;
});

$currentRoute = get_request_path();
// $sessionUser = get_session_user();
$isUserLogged = is_user_logged_in();

if ($currentRoute === 'admin' && !$isUserLogged)
    redirect('/login');

try {
    // once we check the routes -> load the templates
    $template = route_to_template($currentRoute);
    $layout = layout_for_template($template);

    render_template($template, $layout);
} catch (Error $e) {
    http_response_code(500);
    echo 'An error occurred: ' . $e->getMessage() . ' (Code: ' . $e->getCode() . ')';
}
