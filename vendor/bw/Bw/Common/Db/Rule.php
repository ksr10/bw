<?php

namespace Bw\Common\Db;

use Bw\Common\Db;

class Rule extends Db
{
    const RULETABLENAME = 'rules';    
    const USERTABLENAME = 'virtual_users';   
    
    static protected $instance;
    
    public static function i()
    {
        return isset(static::$instance) ? static::$instance : (static::$instance = new static());
    }
    
    public function getRules()
    {
        $readConnection = $this->getReadConnection(); 
        $tableName = self::RULETABLENAME;
        $result = array();
               
        $statement = $readConnection->prepare("SELECT * FROM $tableName ORDER BY id");
        
        if ($statement->execute()) {
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);            
        }
        
        return $result;
    }
    
    public function saveUser($user)
    {
        $writeConnection = $this->getWriteConnection();
        $tableName = self::USERTABLENAME;
        $userId = null;
        
        $statement = $writeConnection->prepare("INSERT INTO $tableName (name, rule_id, total_amount, total_origin) VALUES (:userName, :ruleId, :totalAmount, :totalOrigin)");                

        $statement->bindValue(':userName', $user['name'], \PDO::PARAM_STR);
        $statement->bindValue(':ruleId', $user['rule_id'], \PDO::PARAM_INT);
        $statement->bindValue(':totalAmount', $user['total_amount'], \PDO::PARAM_STR);
        $statement->bindValue(':totalOrigin', $user['total_origin'], \PDO::PARAM_STR);

        if ($statement->execute()) {
            $userId = $writeConnection->lastInsertId();
        }
        
        return $userId;
    }
    
    public function getUsersByRule($ruleId, $active)
    {
        $readConnection = $this->getReadConnection(); 
        $tableName = self::USERTABLENAME;
        $result = array();
               
        $query = "SELECT * FROM $tableName WHERE rule_id = $ruleId";
        
        if ($active) {
            $query .= " AND status = 'active'";
        }
        
        $query .= " ORDER BY id";
        
        $statement = $readConnection->prepare($query);
        
        if ($statement->execute()) {
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);            
        }
        
        return $result;
    }
    
    public function getUsers($active = true)
    {
        $readConnection = $this->getReadConnection(); 
        $tableName = self::USERTABLENAME;
        $result = array();
               
        $query = "SELECT * FROM $tableName";
        
        if ($active) {
            $query .= " WHERE status = 'active'";
        }
        
        $query .= " ORDER BY id";
        
        $statement = $readConnection->prepare($query);
        
        if ($statement->execute()) {
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);            
        }
        
        return $result;
    }
    
    public function updateUserActive($userId)
    {
        $tableName = self::USERTABLENAME;
        $result = false;
        
        $writeConnection = $this->getWriteConnection();
        $query = "UPDATE $tableName SET status = 'lost' WHERE id = :userId";
                
        $statement = $writeConnection->prepare($query);
        $statement->bindValue(':userId', $userId, \PDO::PARAM_INT);
                
        if ($statement->execute()) {
            $result = true;
        }
        
        return $result;
    }
}