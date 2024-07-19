<?php
namespace ElasticQL;

defined('BASEPATH') or exit('No direct script access allowed');
header("Content-Type: application/json");
require_once './elasticload.php';
use ElasticQL\Boot\Connection;

class ElasticManager extends Connection
{
  private ?\PDO $pdo = null;

  /* public function __construct($host, $db, $user, $pass) {
      $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
      $options = [
          \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
          \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
          \PDO::ATTR_EMULATE_PREPARES   => false,
      ];
      try {
          $this->pdo = new \PDO($dsn, $user, $pass, $options);
      } catch (\PDOException $e) {
          throw new \PDOException($e->getMessage(), (int)$e->getCode());
      }
  } */

  public function __construct($host = null, $db = null, $user = null, $pass = null)
  {
    parent::__construct($host, $db, $user, $pass);
    $this->pdo = $this->getConn();
  }

  protected function destructuredByMethod($method, $params)
  {
    try {
      return $destructured = match ($method) {
        'GET' => ['schema' => $schema, 'fields' => $fields, 'where' => $where] = $params,
        'POST' => ['schema' => $schema, 'fields' => $fields, 'values' => $values, 'data' => $data] = $params,
        'PUT' => ['schema' => $schema, 'fields' => $fields, 'values' => $values, 'data' => $data] = $params,
        'DELETE' => ['schema' => $schema, 'where' => $where] = $params,
        default => []
      };
      /* $schema = $params['schema'] ?? '';
      $fields = $method === 'GET' ? explode(',', $params['fields'] ?? '*') : $params['keyBinds'];
      $values = $params['values'];
      $where = $params['where'] ?? '';
      return [$schema, $fields, $where]; */
      //return $destructured;
    } catch (\Exception $e) {
      throw new \Exception($e->getMessage());
    }
  }

  public function query($schema, $fields, $where = '')
  {
    $fields = implode(', ', $fields);
    $sql = "SELECT $fields FROM $schema";
    if ($where) {
      $sql .= " WHERE $where";
    }
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
  }

  public function create(?string $schema = null, ?array $fields = null, ?array $values = null, ?array $data = null): int
  {
    $fields = implode(', ', array_keys($data));
    $values = ':' . implode(', :', array_keys($data));
    $sql = "INSERT INTO $schema ($fields) VALUES ($values)";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute($data);
    return $this->pdo->lastInsertId();
  }

  public function update($schema, $data, $where)
  {
    $set = [];
    foreach ($data as $key => $value) {
      $set[] = "$key = :$key";
    }
    $set = implode(', ', $set);
    $sql = "UPDATE $schema SET $set WHERE $where";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute($data);
    return $stmt->rowCount();
  }

  public function delete($schema, $where)
  {
    $sql = "DELETE FROM $schema WHERE $where";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute();
    return $stmt->rowCount();
  }
}

// Inicializar el DatabaseManager
$elastic = new ElasticManager(); //'localhost', 'nombre_db', 'usuario', 'contraseña'

// Procesar la solicitud
$method = $_SERVER['REQUEST_METHOD'];
$params = $method === 'GET' ? $_GET : json_decode(file_get_contents('php://input'), true);

/* if ($method === 'GET') {
    $params = $_GET;
} else {
    $params = json_decode(file_get_contents('php://input'), true);
} */

//Los vamos a manejar dentro de un builder de consulta, porque esto solamente sirven para las consultas GET
/* $schema = $params['schema'] ?? '';
$fields = explode(',', $params['fields'] ?? '*');
$where = $params['where'] ?? ''; */

try {
  $paramsByMethod = $elastic->destructuredByMethod($method, $params);
  $result = [];
  /* $result = match ($method) {
       'GET' => $elastic->query($schema, $fields, $where),
       'POST' => ['id' => $elastic->create($schema, $params['data'])],
       'PUT' => ['affected' => $elastic->update($schema, $params['data'], $where)],
       'DELETE' => ['affected' => $elastic->delete($schema, $where)],
       default => []
   }; */
  $result = match ($method) {
    'GET' => $elastic->query($schema, $fields, $where),
    'POST' => ['id' => $elastic->create($schema, $params['data'])],
    'PUT' => ['affected' => $elastic->update($schema, $params['data'], $where)],
    'DELETE' => ['affected' => $elastic->delete($schema, $where)],
    default => []
  };
  echo json_encode(['success' => true, 'data' => $result]);
} catch (\Exception $e) {
  http_response_code(500);
  echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}