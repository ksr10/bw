<?php

namespace Bw\Common;

class Db
{
    protected $connected = false;
    
    protected $writeConnection;
    
    protected $readConnection;    
    
    public function setConfig($settings)
    {
        if (isset($settings['db'])) {
            try {
                $this->writeConnection = new \PDO(
                    "mysql:dbname=" . $settings['db']['dbname'] . ";host=" . $settings['db']['host'],
                    $settings['db']['username'],
                    $settings['db']['password'],
                    array(\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8", \PDO::ATTR_PERSISTENT => true)
                );

                $this->readConnection = new \PDO(
                    "mysql:dbname=" . $settings['db']['dbname'] . ";host=" . $settings['db']['host'],
                    $settings['db']['username'],
                    $settings['db']['password'],
                    array(\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8", \PDO::ATTR_PERSISTENT => true)
                );
                
                $this->connected = true;
            } catch (\PDOException $e) {                
                $this->connected = false;
            }
        }
    }
    
    public function getReadConnection()
    {        
        return $this->readConnection;
    }
    
    public function getWriteConnection()
    {
        return $this->writeConnection;
    }
}