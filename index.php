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

$isHttp = DEV ? true : isset($_SERVER['REQUEST_SCHEME']) && $_SERVER['REQUEST_SCHEME'] === 'http';
define('BASE_URI', '/');
define('BASE_HOST', ($isHttp ? 'http' : 'https') . '://' . $_SERVER['HTTP_HOST']);
define('JS_URI', BASE_URI . 'assets/js');
define('CSS_URI', BASE_URI . 'assets/css');
define('CURRENT_URL', $_SERVER['REQUEST_URI']);
define('PATHNAME', rtrim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/'));
define('DEFAULT_IMAGE', 'https://placeholdit.com/400x400/dddddd/999999');
define('IS_POST_REQUEST', $_SERVER['REQUEST_METHOD'] === 'POST');

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
    if (starts_with($currentRoute, 'api')) {
        header('Content-Type: application/json');
        $apiRoute = get_api_route($currentRoute);
        if (!$apiRoute['is_file']) response('API endpoint not found', 404);
        require_once $apiRoute['path'];
        exit;
    }
    // once we check the routes -> load the templates
    $template = route_to_template($currentRoute);
    $layout = layout_for_template($template);

    render_template($template, $layout);
} catch (Throwable $e) {
    Flash::add($e->getMessage(), 'error');
}
