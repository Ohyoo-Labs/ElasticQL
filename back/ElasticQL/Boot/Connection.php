<?php 
namespace ElasticQL\Boot;
defined('BASEPATH') OR exit('No direct script access allowed');
require_once '../elasticload.php';
use ElasticQL\Boot\DBoot;
use \PDO;
// PDO connection manager
class Connection extends DBoot {
    private ?PDO $connection = null;
    private ?string $host = null;
    private ?string $user = null;
    private ?string $password = null;
    private ?string $database = null;
    private ?string $charset = null;

    public function __construct() {
        parent::__construct('local', 'mysql');
    }

    public function getConn() {
        if ($this->connection == null) {
            $this->host = parent::$host;
            $this->user = parent::$user;
            $this->password = parent::$password;
            $this->database = parent::$database;
            $this->charset = parent::$charset;

            $dsn = "mysql:host=$this->host;dbname=$this->database;charset=$this->charset";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            try {
                $this->connection = new PDO($dsn, $this->user, $this->password, $options);
            } catch (\PDOException $e) {
                throw new \PDOException($e->getMessage(), (int)$e->getCode());
            }
        }
        return $this->connection;
    }

    public function closeConn() {
        $this->connection = null;
    }

    public function __destruct() {
        if ($this->connection != null) {
            $this->closeConn();
        }
        $this->host = null;
        $this->user = null;
        $this->password = null;
        $this->database = null;
        $this->charset = null;
    }
  }