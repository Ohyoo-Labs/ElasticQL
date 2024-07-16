<?php
namespace ElasticQL;
defined('BASEPATH') OR exit('No direct script access allowed');
header('Content-Type: application/json');
require_once '../elasticload.php';
use ElasticQL\Build\BuilderQL;
use ElasticQL\Boot\Connection;


$request = json_decode(file_get_contents('php://input'), true);

if ($request) {
    try {
        $data = buildQuery($request);
        echo json_encode(['data' => $data]);
    } catch (\Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'Invalid request']);
}

/* 
Estos son los triggers para las vrrigicaciones de los datos y operaciones recibidas en la consulta
$httpMethod = $_SERVER['REQUEST_METHOD']; // Esto obtiene el método HTTP de las cabeceras de la solicitud
$jsonData = json_encode([
    'queryType' => 'SELECT',
    'schema' => 'users'
]);

$data = json_decode($jsonData, true);
$data['httpVerb'] = $httpMethod; // Agregar el método HTTP al array de datos

// Crear la instancia de AllowedActions a partir de los datos JSON
$action = AllowedActions::fromJson($data);

if ($action && $action->isAllowed()) {
    echo $action->getDescription(); // Outputs: HTTP Verb: GET (o lo que sea que sea el método)
} else {
    echo "Action not allowed.";
}
*/