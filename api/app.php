<?php
declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use App\Controller\ProductoController;
use App\DB\Database;
use App\Service\ProductoService;

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

function productoIdFromPath(string $path): ?int
{
    if (preg_match('#^/productos/(\d+)$#', $path, $matches) !== 1) {
        return null;
    }
    return (int) $matches[1];
}

function responseStatus(array $result, int $successStatus = 200): int
{
    if (!($result['success'] ?? false)) {
        $errors = $result['errors'] ?? [];
        if (in_array('Producto no encontrado', $errors, true)) {
            return 404;
        }
        return 400;
    }
    return $successStatus;
}

$dbDsn = env('DB_DSN', 'pgsql:host=postgres;port=5432;dbname=product_db');
$dbUser = env('DB_USER', 'admin');
$dbPass = env('DB_PASS', 'adminpassword');

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$path = requestPath();
$productoId = productoIdFromPath($path);

$producto_controller = new ProductoController(
    new ProductoService($dbDsn, $dbUser, $dbPass)
);

try {
    if ($method === 'GET' && $path === '/') {
        jsonResponse([
            'name' => 'Product Registration API',
            'version' => '1.0',
            'routes' => [
                'GET /productos' => 'List products',
                'GET /productos/{id}' => 'Get product',
                'POST /productos' => 'Create product',
                'PUT /productos/{id}' => 'Update product',
                'DELETE /productos/{id}' => 'Delete product',
                'GET /health' => 'Database health check',
            ],
        ]);
    }

    if ($method === 'GET' && $path === '/health') {
        Database::getInstance()->connect($dbDsn, $dbUser, $dbPass);
        jsonResponse(['status' => 'ok', 'database' => 'connected']);
    }

    if ($method === 'GET' && $path === '/productos') {
        $result = $producto_controller->index();
        jsonResponse($result, responseStatus($result));
    }

    if ($method === 'GET' && $productoId !== null) {
        $result = $producto_controller->show($productoId);
        jsonResponse($result, responseStatus($result));
    }

    if ($method === 'POST' && $path === '/productos') {
        $result = $producto_controller->create(parseJsonBody());
        jsonResponse($result, responseStatus($result, 201));
    }

    if ($method === 'PUT' && $productoId !== null) {
        $result = $producto_controller->update($productoId, parseJsonBody());
        jsonResponse($result, responseStatus($result));
    }

    if ($method === 'DELETE' && $productoId !== null) {
        $result = $producto_controller->delete($productoId);
        jsonResponse($result, responseStatus($result));
    }

    jsonResponse(['error' => 'Not found'], 404);
} catch (Throwable $e) {
    jsonResponse(['success' => false, 'errors' => [$e->getMessage()]], 500);
}
