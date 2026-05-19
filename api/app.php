<?php
declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use App\Controller\BodegaController;
use App\Controller\MaterialController;
use App\Controller\MonedaController;
use App\Controller\ProductoController;
use App\Controller\SucursalController;
use App\DB\Database;
use App\Service\BodegaService;
use App\Service\MaterialService;
use App\Service\MonedaService;
use App\Service\ProductoService;
use App\Service\SucursalService;

header('Content-Type: application/json; charset=utf-8');

function jsonResponse(array $data, int $status = 200): never
{
    http_response_code($status);
    echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

function env(string $key, string $default = ''): string
{
    $value = getenv($key);
    return $value !== false ? $value : ($_ENV[$key] ?? $default);
}

function requestPath(): string
{
    $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    return rtrim($path, '/') ?: '/';
}

function parseJsonBody(): array
{
    $body = json_decode(file_get_contents('php://input') ?: '{}', true);
    if (!is_array($body)) {
        jsonResponse(['success' => false, 'errors' => ['Invalid JSON body']], 400);
    }
    return $body;
}

function resourceIdFromPath(string $path, string $resource): ?int
{
    if (preg_match('#^/' . preg_quote($resource, '#') . '/(\d+)$#', $path, $matches) !== 1) {
        return null;
    }
    return (int) $matches[1];
}

function responseStatus(array $result, int $successStatus = 200): int
{
    if (!($result['success'] ?? false)) {
        foreach ($result['errors'] ?? [] as $error) {
            if (is_string($error) && str_contains($error, 'no encontrad')) {
                return 404;
            }
        }
        return 400;
    }
    return $successStatus;
}

$dbDsn = env('DB_DSN', 'pgsql:host=postgres;port=5432;dbname=product_db');
$dbUser = env('DB_USER', 'admin');
$dbPass = env('DB_PASS', 'adminpassword');

$resources = [
    'productos' => [
        'controller' => new ProductoController(new ProductoService($dbDsn, $dbUser, $dbPass)),
        'writable' => true,
    ],
    'monedas' => [
        'controller' => new MonedaController(new MonedaService($dbDsn, $dbUser, $dbPass)),
    ],
    'bodegas' => [
        'controller' => new BodegaController(new BodegaService($dbDsn, $dbUser, $dbPass)),
    ],
    'materiales' => [
        'controller' => new MaterialController(new MaterialService($dbDsn, $dbUser, $dbPass)),
    ],
    'sucursales' => [
        'controller' => new SucursalController(new SucursalService($dbDsn, $dbUser, $dbPass)),
    ],
];

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$path = requestPath();

try {
    if ($method === 'GET' && $path === '/') {
        jsonResponse([
            'name' => 'Product Registration API',
            'version' => '1.0',
        ]);
    }

    if ($method === 'GET' && $path === '/health') {
        Database::getInstance()->connect($dbDsn, $dbUser, $dbPass);
        jsonResponse(['status' => 'ok', 'database' => 'connected']);
    }

    foreach ($resources as $name => $config) {
        $controller = $config['controller'];
        $basePath = '/' . $name;
        $id = resourceIdFromPath($path, $name);

        if ($method === 'GET' && $path === $basePath) {
            $result = $controller->index();
            jsonResponse($result, responseStatus($result));
        }

        if ($method === 'GET' && $id !== null) {
            $result = $controller->show($id);
            jsonResponse($result, responseStatus($result));
        }

        // No Writable (No POST, PUT, DELETE)
        if (!($config['writable'] ?? false)) {
            continue;
        }

        if ($method === 'POST' && $path === $basePath) {
            $result = $controller->create(parseJsonBody());
            jsonResponse($result, responseStatus($result, 201));
        }

        if ($method === 'PUT' && $id !== null) {
            $result = $controller->update($id, parseJsonBody());
            jsonResponse($result, responseStatus($result));
        }

        if ($method === 'DELETE' && $id !== null) {
            $result = $controller->delete($id);
            jsonResponse($result, responseStatus($result));
        }
    }

    jsonResponse(['error' => 'Not found'], 404);
} catch (Throwable $e) {
    jsonResponse(['success' => false, 'errors' => [$e->getMessage()]], 500);
}
