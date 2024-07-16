<?php 
namespace ElasticQL\Enums;
defined('BASEPATH') OR exit('No direct script access allowed');
enum HttpVerb: string {
  case GET = 'GET';
  case POST = 'POST';
  case PUT = 'PUT';
  case PATCH = 'PATCH';
  case DELETE = 'DELETE';

  public static function fromMethod(string $method): ?self {
      return self::tryFrom($method);
  }
}

enum MySqlQueryType: string {
  case SELECT = 'SELECT';
  case INSERT = 'INSERT';
  case UPDATE = 'UPDATE';
  case DELETE = 'DELETE';

  public static function fromType(string $type): ?self {
      return self::tryFrom($type);
  }
}

enum TableSchema: string {
  case USERS = 'users';
  case PRODUCTS = 'products';
  case ORDERS = 'orders';

  public function tableName(): string {
      return match($this) {
          self::USERS => 'user_table',
          self::PRODUCTS => 'product_table',
          self::ORDERS => 'order_table',
      };
  }

  public static function fromSchema(string $schema): ?self {
      return self::tryFrom($schema);
  }
}
