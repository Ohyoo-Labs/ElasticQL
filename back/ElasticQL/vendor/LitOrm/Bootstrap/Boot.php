<?php 
namespace LitOrm\Bootstrap;

class Boot 
{
  const SETTINGS = [
    'ENVIROMENT' => 'development',
  ];

  const DB_SETTINGS = [
    'HOST' => '',
    'DBNAME' => '',
    'USERNAME' => '',
    'PASSWORD' => '',
    'CHARSET' => '',
  ];
  public function boot (): void
  {
    foreach (Boot::SETTINGS as $key => $value){
      if (!defined($key))
        define($key, $value);
    }
  }
}
