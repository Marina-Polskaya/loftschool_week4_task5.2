<?php
namespace Base;
include_once 'config.php';

final class Db
{
    /** @var \PDO */
    private $pdo;
    private $log = [];
    private static $instance;

    private function __construct()
    {
        
    }

    private function __clone() : void
    {
        
    }

    public static function getInstance() : self
    {    
        if(!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function getConnection() : object
    {
        try {
            if (!$this->pdo) {
                $host = HOST;
                $dbName = DB_NAME;
                $user = USER;
                $password = PASSWORD;
                $options = [
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
                ];
                $this->pdo = new \PDO("mysql:host=$host;dbname=$dbName", $user, $password, $options);
                }
                return $this->pdo;
        } catch (PDOException $e) {
            echo 'Невозможно установить соединение с базой данных <br />' . $e . '<br />';
        }
    } 
    
    public function exec(string $query, array $params = [], string $method = '') : bool|int
    {
        $startTime = microtime(true);
        $this->pdo= $this->getConnection();
        $prepared = $this->pdo->prepare($query);
        $ret = $prepared->execute($params);
        $time = microtime(true) - $startTime;
        
        if(!$ret) {
            if($prepared->errorCode()) {
                $errorInfo = $prepared->errorInfo();
                trigger_error("{$errorInfo[0]}#{$errorInfo[1]}: ". $errorInfo[2]);
            }
            return false;
        }
        
        $this->log[] = [
            'query' => $prepared,
            'time' => $time,
            'method' => $method
        ];
        
        return $prepared->rowCount();
    }
    
    public function fetchOne(string $query, array $params = [], $_method = '') : bool|array
    {
        $startTime = microtime(true);
        $prepared = $this->getConnection()->prepare($query);
        $ret = $prepared->execute($params);
        
        if (!$ret) {
            $errorInfo = $prepared->errorInfo();
            trigger_error("{$errorInfo[0]}#{$errorInfo[1]}: " . $errorInfo[2]);
            return [];
        }
        
        $data = $prepared->fetchAll(\PDO::FETCH_ASSOC);
        $affectedRows = $prepared->rowCount();
        
        $this->log[] = [$query, microtime(true) - $startTime, $_method, $affectedRows];
        if (!$data) {
            return false;
        }
        return reset($data);
    }
    
    public function fetchAll(string $query, $_method = '', array $params = []) : bool|array
    {
        $startTime = microtime(true);
        $prepared = $this->getConnection()->prepare($query);
        $ret = $prepared->execute($params);
        
        if (!$ret) {
            $errorInfo = $prepared->errorInfo();
            trigger_error("{$errorInfo[0]}#{$errorInfo[1]}: " . $errorInfo[2]);
            return [];
        }
        $data = $prepared->fetchAll(\PDO::FETCH_ASSOC);
        $affectedRows = $prepared->rowCount();
        $this->log[] = [$query, microtime(true) - $startTime, $_method, $affectedRows];
        if (!$data) {
            return false;
        }
        return $data;
    }
    
    public function getLogHTML() : ?array
    {
        if (!$this->log) {
            return '';
        }
        $result = '';
        foreach ($this->log as $elem) {
            $result = $elem[1] . ':' . $elem[0] . '(' . $elem[2] . ') [' . $elem[3] . "] \n";
        }
        return '<pre>'. $result . '</pre>';
    }
    
    public function lastInsertId() : int
    {
        $this->getConnection();
        return $this->pdo->lastInsertId();
    }
    
}