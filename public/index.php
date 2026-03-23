<?php

declare(strict_types=1);

// 1. Importaciones esenciales de PSR-7 y Slim
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();

// 2. Ajuste para subdirectorios en Laragon
$app->setBasePath('/static');

// 3. Conexión PDO a SQLite
// Asegurate de que la carpeta 'database' exista un nivel arriba de 'public'
$dbPath = __DIR__ . '/../database/app.sqlite';
try {
    $pdo = new PDO("sqlite:" . $dbPath);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Inicializar tabla si no existe
    $pdo->exec("CREATE TABLE IF NOT EXISTS tasks (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        title TEXT NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

// 4. Middlewares
$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();
$app->addErrorMiddleware(true, true, true);

// --- RUTAS ---

// 1. Ruta GET: Servir la UI
$app->get('/', function (Request $request, Response $response) {
    $htmlPath = __DIR__ . '/ui.html';
    if (file_exists($htmlPath)) {
        $html = file_get_contents($htmlPath);
        $response->getBody()->write($html);
    } else {
        $response->getBody()->write("Error: No se encuentra el archivo ui.html en " . __DIR__);
    }
    return $response->withHeader('Content-Type', 'text/html');
});

// 2. Ruta GET: Obtener datos (API)
$app->get('/api/tasks', function (Request $request, Response $response) use ($pdo) {
    $stmt = $pdo->query("SELECT * FROM tasks ORDER BY id DESC");
    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $response->getBody()->write(json_encode($tasks));
    return $response->withHeader('Content-Type', 'application/json');
});

// 3. Ruta POST: Guardar dato (API)
$app->post('/api/tasks', function (Request $request, Response $response) use ($pdo) {
    $data = $request->getParsedBody();
    
    if (isset($data['title'])) {
        $stmt = $pdo->prepare("INSERT INTO tasks (title) VALUES (:title)");
        $stmt->execute(['title' => $data['title']]);
        $payload = json_encode(['status' => 'success']);
    } else {
        $payload = json_encode(['status' => 'error', 'message' => 'Falta el titulo']);
    }
    
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->run();