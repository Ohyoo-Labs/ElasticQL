<?php
/* 
 * ORM class for MySql Databases
 */
namespace LitOrm;

defined('BASEPATH') or exit('No direct script access allowed');
require_once './litautoload.php';
use LitOrm\LitExecutor;
use LitORM\Boostrap\LitConnection;
use LitORM\Bootstrap\Boot;

class Lit extends LitExecutor
{
  private $host;
  private $dbname;
  private $username;
  private $password;
  private $charset;
  protected $db = null;
  protected ?string $sentence = '';

  public ?string $table = null;

  public function __construct(?string $host, ?string $dbname, ?string $username, ?string $password = '', ?string $charset)
  {
    parent::__construct();
    $this->host = $host ?? Boot::DB_SETTINGS['HOST'];
    $this->dbname = $dbname ?? Boot::DB_SETTINGS['DBNAME'];
    $this->username = $username ?? Boot::DB_SETTINGS['USERNAME'];
    $this->password = $password ?? Boot::DB_SETTINGS['PASSWORD'];
    $this->charset = $charset ?? Boot::DB_SETTINGS['CHARSET'];
    $boost = new Boot;
    $boost->boot();
    $this->LitDB();
  }

  //Inicia la conexion y setea la variable $db
  public function LitDB(): mixed
  {
    try {
      $newDb = new LitConnection($this->host, $this->dbname, $this->username, $this->password, $this->charset );
      $this->db = $newDb->connect();
      return $this;
    } catch (\Exception $e) {
      if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
        return $this->sendError($e->getMessage());
      }
      return $this->sendError('Error en la conexion a la base de datos');
    }
  }

  protected function composeSentence(string $sentence): ?string
  {
    $this->sentence .= $sentence;
    return $this->sentence;
  }

  protected function sendError($message): array
  {
    return [
      'error' => true,
      'message' => $message
    ];
  }

  public function __destruct()
  {
    $this->db = null;
  }
}