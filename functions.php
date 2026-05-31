<?php

defined('DEFAULT_TEMPLATE') || define('DEFAULT_TEMPLATE', 'home');
defined('NOT_FOUND_TEMPLATE') || define('NOT_FOUND_TEMPLATE', '404');
defined('DEFAULT_LAYOUT') || define('DEFAULT_LAYOUT', 'main');
defined('ADMIN_LAYOUT') || define('ADMIN_LAYOUT', 'admin');
define('TYPE_MAP', [
	'normal' => 'normal',
	'fire' => 'fuego',
	'water' => 'agua',
	'electric' => 'eléctrico',
	'grass' => 'planta',
	'ice' => 'hielo',
	'fighting' => 'lucha',
	'poison' => 'veneno',
	'ground' => 'tierra',
	'flying' => 'volador',
	'psychic' => 'psíquico',
	'bug' => 'bicho',
	'rock' => 'roca',
	'ghost' => 'fantasma',
	'dragon' => 'dragón',
	'dark' => 'siniestro',
	'steel' => 'acero',
	'fairy' => 'hada',
]);

function e(string $string): string
{
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

function get_poke_type (string $type): string
{
	return TYPE_MAP[$type] ?? $type;
}

function get_request_path(): string
{
    $uri = $_SERVER['REQUEST_URI'] ?? '/';
    $path = parse_url($uri, PHP_URL_PATH) ?: '/';

    $finale = trim(rawurldecode($path), '/');
    $finale = preg_replace('/\.php$/i', '', $finale);

    if ($finale === '' || strtolower($finale) === 'index') {
        return 'home';
    }

    return $finale;
}

function route_to_template(string $path): string
{
    if ($path === '') {
        return DEFAULT_TEMPLATE;
    }

    $path = strtolower($path);
    $path = preg_replace('/[^a-z0-9_\-\/]/', '', $path);
    $segments = array_filter(explode('/', $path), function (string $segment): bool {
        return $segment !== '' && $segment !== '.' && $segment !== '..';
    });

    return implode('/', $segments) ?: DEFAULT_TEMPLATE;
}

function template_path(string $template): ?string
{
    $template = trim($template, '/');
    $paths = [
        VIEWS_PATH . '/' . $template . '.php',
        VIEWS_PATH . '/' . $template . '/index.php',
    ];

    foreach ($paths as $path) {
        if (is_file($path)) {
            return $path;
        }
    }

    return null;
}

function layout_path(string $layout): ?string
{
    $layout = trim($layout, '/');
    $path = LAYOUTS_PATH . '/' . $layout . '.php';

    if (is_file($path)) {
        return $path;
    }

    return null;
}

function layout_for_template(string $template): string
{
    return strpos(trim($template, '/'), 'admin/') === 0 ? ADMIN_LAYOUT : DEFAULT_LAYOUT;
}

function render_template(string $template, string $layout = DEFAULT_LAYOUT): void
{
    $path = template_path($template);

    if ($path === null) {
        http_response_code(404);
        $path = template_path(NOT_FOUND_TEMPLATE);
    }

    if ($path === null) {
        throw new Error('No se encontró la plantilla de error 404.', 500);
    }

    ob_start();
    try {
        require $path;
    } catch (Throwable $e) {
        ob_end_clean();
        Flash::add('Error: ' . $e->getMessage(), 'error');

        $previousMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $_SERVER['REQUEST_METHOD'] = 'GET';

        ob_start();
        try {
            require $path;
        } finally {
            $_SERVER['REQUEST_METHOD'] = $previousMethod;
        }
    }
    $content = ob_get_clean();

    if ($layout === false || $layout === null) {
        echo $content;
        return;
    }

    $layoutFile = layout_path($layout);
    if ($layoutFile !== null)
        require $layoutFile;
}

function is_user_logged_in(): bool
{
    return DEV ? true : isset($_SESSION['user']['id']);
}

function get_session_user(): mixed
{
    if (DEV) return array(
        'id' => 1,
        'username' => 'admin',
        'email' => 'admin@example.com'
    );

    if (!isset($_SESSION['user']['id'])) return false;
    return $_SESSION['user'];
}

function set_session_cookie(string $key, string $value, int $lifetime = 60 * 60 * 24 * 30): void
{
    $params = session_get_cookie_params();
    setcookie(
        $key,
        $value,
        time() + $lifetime,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

function get_server_method(): string
{
    return $_SERVER['REQUEST_METHOD'] ?? 'GET';
}
function is_post_request(): bool
{
    return get_server_method() === 'POST';
}

function debug(array $args, bool $exit = true): void
{
    print_r('<pre class="debug">' . print_r($args, true) . '</pre>');
    if ($exit) exit;
}

function redirect(string $url): void
{
    header('Location: ' . $url);
    exit;
}
function response(string $content, int $statusCode = 200, array $data = []): void
{
	$code = $statusCode >= 100 && $statusCode < 600 ? $statusCode : 200;
	$resp = array(
		'code' => $code,
		'message' => $content,
		'error' => $code >= 300,
		'success' => $code >= 200 && $code < 300,
		'data' => $data
	);
	header('Content-Type: application/json');
	http_response_code($code);
	die(json_encode($resp));
}
