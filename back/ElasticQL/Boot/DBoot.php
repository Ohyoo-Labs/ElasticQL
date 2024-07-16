<?php
namespace ElasticQL\Boot;
defined('BASEPATH') OR exit('No direct script access allowed');
class DBoot {
    protected ?string $host = null;
    protected ?string $user = null;
    protected ?string $password = null;
    protected ?string $database = null;
    protected ?string $charset = null;

    const LOCAL_MYSQL = [       
        'host' => 'localhost',
        'user' => 'root',
        'password' => '',
        'database' => 'test',
        'charset' => 'utf8'
    ];
    public function __construct(?string $switch = null, ?string $client = null) {
        if ($switch == 'local' && $client == 'mysql') {
            $this->host = self::LOCAL_MYSQL['host'];
            $this->user = self::LOCAL_MYSQL['user'];
            $this->password = self::LOCAL_MYSQL['password'];
            $this->database = self::LOCAL_MYSQL['database'];
            $this->charset = self::LOCAL_MYSQL['charset'];
        }
        
    }    
}