<?php
declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

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

$dbDsn = env('DB_DSN', 'pgsql:host=postgres;port=5432;dbname=product_db');
$dbUser = env('DB_USER', 'admin');
$dbPass = env('DB_PASS', 'adminpassword');

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$path = requestPath();

try {
    // GET /
    if ($method === 'GET' && $path === '/') {
        jsonResponse([
            'name' => 'Product Registration API',
            'version' => '1.0',
        ]);
    }

    // GET /health — check database connection
    if ($method === 'GET' && $path === '/health') {
        new PDO($dbDsn, $dbUser, $dbPass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        jsonResponse(['status' => 'ok', 'database' => 'connected']);
    }

    // POST /productos — create a product
    if ($method === 'POST' && $path === '/productos') {
        $body = json_decode(file_get_contents('php://input') ?: '{}', true);
        if (!is_array($body)) {
            jsonResponse(['success' => false, 'errors' => ['Invalid JSON body']], 400);
        }

        $service = new ProductoService();
        $service->initializeDatabaseConnection($dbDsn, $dbUser, $dbPass);
        $result = $service->create($body);
        jsonResponse($result, ($result['success'] ?? false) ? 201 : 400);
    }

    jsonResponse(['error' => 'Not found'], 404);
} catch (Throwable $e) {
    jsonResponse(['success' => false, 'errors' => [$e->getMessage()]], 500);
}
