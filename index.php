<?php
require 'db.php';
require 'routes.php';

// Get the current URI
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Determine the base directory
$baseDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
$baseDir = trim($baseDir, '/');

// Adjust the URI for the base directory
if ($baseDir) {
    $route = trim(str_replace($baseDir, '', $requestUri), '/');
} else {
    $route = trim($requestUri, '/');
}

// Extract parameters from the route
$params = [];
foreach ($routes as $routePattern => $file) {
    $pattern = preg_replace('/{[^\/]+}/', '([^\/]+)', $routePattern);
    if (preg_match("#^$pattern$#", $route, $matches)) {
        array_shift($matches); // Remove full match
        $params = $matches;
        require $file;
        exit;
    }
}

http_response_code(404);
echo 'Page not found';
?>
