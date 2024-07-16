<?php 
namespace ElasticQL\Enums;
defined('BASEPATH') OR exit('No direct script access allowed');
require_once '../elasticload.php';
use ElasticQL\Enums\HttpVerb;
use ElasticQL\Enums\MySqlQueryType;
use ElasticQL\Enums\TableSchema;
class AllowedActions {
  private HttpVerb $verb;
  private MySqlQueryType $queryType;
  private TableSchema $schema;
  private string $type;

  private function __construct(string $type, HttpVerb $verb = null, MySqlQueryType $queryType = null, TableSchema $schema = null) {
      $this->type = $type;
      $this->verb = $verb;
      $this->queryType = $queryType;
      $this->schema = $schema;
  }

  public static function http(HttpVerb $verb): self {
      return new self('http', $verb);
  }

  public static function query(MySqlQueryType $queryType): self {
      return new self('query', null, $queryType);
  }

  public static function schema(TableSchema $schema): self {
      return new self('schema', null, null, $schema);
  }

  public function isAllowed(): bool {
      return match($this->type) {
          'http' => $this->verb !== null,
          'query' => $this->queryType !== null,
          'schema' => $this->schema !== null,
      };
  }

  public function getDescription(): string {
      return match($this->type) {
          'http' => "HTTP Verb: " . $this->verb->value,
          'query' => "MySQL Query Type: " . $this->queryType->value,
          'schema' => "Table Schema: " . $this->schema->tableName(),
      };
  }

  public static function fromJson(array $json): ?self {
      $httpVerb = HttpVerb::fromMethod($json['httpVerb']);
      $queryType = MySqlQueryType::fromType($json['queryType']);
      $schema = TableSchema::fromSchema($json['schema']);

      if ($httpVerb !== null) {
          return self::http($httpVerb);
      } elseif ($queryType !== null) {
          return self::query($queryType);
      } elseif ($schema !== null) {
          return self::schema($schema);
      }

      return null;
  }
  // Agregados automaticamente
  public function toJson(): array {
      return [
          'type' => $this->type,
          'httpVerb' => $this->verb?->value,
          'queryType' => $this->queryType?->value,
          'schema' => $this->schema?->value,
      ];
  }

  public function __toString(): string {
      return $this->getDescription();
  }

  public function __get($name) {
      return $this->$name;
  }

  public function __set($name, $value) {
      $this->$name = $value;
  }

  public function __isset($name): bool {
      return isset($this->$name);
  }

  public function __unset($name) {
      unset($this->$name);
  }

  public function __debugInfo() {
      return [
          'type' => $this->type,
          'verb' => $this->verb,
          'queryType' => $this->queryType,
          'schema' => $this->schema,
      ];
  }

  public function __serialize(): array {
      return [
          'type' => $this->type,
          'verb' => $this->verb,
          'queryType' => $this->queryType,
          'schema' => $this->schema,
      ];
  }

  public function __unserialize(array $data): void {
      $this->type = $data['type'];
      $this->verb = $data['verb'];
      $this->queryType = $data['queryType'];
      $this->schema = $data['schema'];
  }

  public function __clone() {
      $this->verb = clone $this->verb;
      $this->queryType = clone $this->queryType;
      $this->schema = clone $this->schema;
  }

  public function __sleep(): array {
      return ['type', 'verb', 'queryType', 'schema'];
  }

  public function __wakeup() {
      // Re-establish the database connection
      $this->verb = HttpVerb::fromMethod((string)$this->verb);
      $this->queryType = MySqlQueryType::fromType((string)$this->queryType);
      $this->schema = TableSchema::fromSchema((string)$this->schema);
  }

  public function __invoke() {
      return $this->getDescription();
  }

  public function __set_state(array $data) {
      return new self($data['type'], $data['verb'], $data['queryType'], $data['schema']);
  }

  public function __call($name, $arguments) {
      return $this->getDescription();
  }

  public static function __callStatic($name, $arguments) {
      return self::http(HttpVerb::GET);
  }
  //Verificar funcionamiento
  private function __destruct() {
    unset($this->type);
    unset($this->verb);
    unset($this->queryType);
    unset($this->schema);
  }
  
}