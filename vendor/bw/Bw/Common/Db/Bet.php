<?php

namespace Bw\Common\Db;

use Bw\Common\Db;

class Bet extends Db
{
    const BETTABLENAME = 'bets';
    const RATIOTABLENAME = 'ratios';
    const USERTABLENAME = 'virtual_users';   
    const RULETABLENAME = 'rules';   
        
    static protected $instance;
    
    public static function i()
    {
        return isset(static::$instance) ? static::$instance : (static::$instance = new static());
    }
    
    public function getLastBet($userId)
    {
        $readConnection = $this->getReadConnection(); 
        $tableName = self::BETTABLENAME;
        $result = array();
               
        $statement = $readConnection->prepare("SELECT * FROM $tableName ORDER BY id");
        
        if ($statement->execute()) {
            $result = $statement->fetch(\PDO::FETCH_ASSOC);            
        }
        
        return $result;
    }
    
    public function saveBet($data)
    {
        $betId = null;
        $tableName = self::BETTABLENAME;
        
        $writeConnection = $this->getWriteConnection();
        $query = "INSERT INTO $tableName (rule_id, user_id, ratio_id, bet_amount, result, event_data) VALUES ";
        $query .= "(:ruleId, :userId, :ratioId, :betAmount, :result, :eventData)";

        $statement = $writeConnection->prepare($query);                

        $statement->bindValue(':ruleId', $data['rule_id'], \PDO::PARAM_INT);
        $statement->bindValue(':userId', $data['user_id'], \PDO::PARAM_INT);
        $statement->bindValue(':ratioId', $data['ratio_id'], \PDO::PARAM_INT);
        $statement->bindValue(':betAmount', $data['bet_amount'], \PDO::PARAM_STR);
        $statement->bindValue(':result', $data['result'], \PDO::PARAM_INT);
        $statement->bindValue(':eventData', $data['event_data'], \PDO::PARAM_STR);
        
        if ($statement->execute()) {
            $betId = $writeConnection->lastInsertId();                    
        }
        
        return $betId;
    }
    
    public function getNewBets($status)
    {
        $readConnection = $this->getReadConnection(); 
        $tableName = self::BETTABLENAME;
        $ratioTableName = self::RATIOTABLENAME;
        $userTableName = self::USERTABLENAME;
        
        $bets = array();
               
        $query = "SELECT b.*, ra.host_team, ra.guest_team, u.total_amount FROM $tableName AS b LEFT JOIN $ratioTableName AS ra ON b.ratio_id = ra.id";
        $query .= " LEFT JOIN $userTableName AS u ON b.user_id = u.id WHERE b.result = $status";
        
        $statement = $readConnection->prepare($query);
        
        $statement->bindValue(':status', $status, \PDO::PARAM_INT);
                
        if ($statement->execute()) {
            $bets = $statement->fetchAll(\PDO::FETCH_ASSOC);            
        }
        
        return $bets;
    }
    
    public function updateBet($betId, $betData)
    {
        $tableName = self::BETTABLENAME;
        $result = false;
        
        $writeConnection = $this->getWriteConnection();
        $query = "UPDATE $tableName SET rule_id = :ruleId, user_id = :userId, ratio_id = :ratioId,";
        $query .= "  bet_amount = :betAmount, result = :result, event_data = :eventData WHERE id = :betId";
        
        $statement = $writeConnection->prepare($query);                

        $statement->bindValue(':ruleId', $betData['rule_id'], \PDO::PARAM_INT);
        $statement->bindValue(':userId', $betData['user_id'], \PDO::PARAM_INT);
        $statement->bindValue(':ratioId', $betData['ratio_id'], \PDO::PARAM_INT);
        $statement->bindValue(':betAmount', $betData['bet_amount'], \PDO::PARAM_STR);
        $statement->bindValue(':result', $betData['result'], \PDO::PARAM_INT);
        $statement->bindValue(':eventData', $betData['event_data'], \PDO::PARAM_STR);
        $statement->bindValue(':betId', $betId, \PDO::PARAM_INT);
        
        if ($statement->execute()) {
            $result = true;
        }
        
        return $result;
    }
    
    public function updateUserTotal($total, $userId)
    {        
        $tableName = self::USERTABLENAME;
        $result = false;
        
        $writeConnection = $this->getWriteConnection();
        $query = "UPDATE $tableName SET total_amount = :totalAmount WHERE id = :userId";
                
        $statement = $writeConnection->prepare($query);
        $statement->bindValue(':userId', $userId, \PDO::PARAM_INT);
        $statement->bindValue(':totalAmount', $total, \PDO::PARAM_STR);
        
        if ($statement->execute()) {
            $result = true;
        }
        
        return $result;
    }
    
    public function getOwnUsers()
    {
        $readConnection = $this->getReadConnection(); 
        $userTableName = self::USERTABLENAME;
                
        $users = array();
               
        $query = "SELECT * FROM $userTableName";
        $query .= " WHERE name LIKE '%bogaenko%' ORDER BY (total_amount - total_origin) DESC";
        
        $statement = $readConnection->prepare($query);
                       
        if ($statement->execute()) {
            $users = $statement->fetchAll(\PDO::FETCH_ASSOC);            
        }
        
        return $users;
    }
    
    public function getSuccessUsers()
    {
        $readConnection = $this->getReadConnection(); 
        $userTableName = self::USERTABLENAME;
                
        $users = array();
               
        $query = "SELECT id, name, total_amount, total_origin, status FROM $userTableName";
        $query .= " WHERE name NOT LIKE '%bogaenko%' AND total_amount > total_origin ORDER BY (total_amount - total_origin) DESC LIMIT 20";
        
        $statement = $readConnection->prepare($query);
                       
        if ($statement->execute()) {
            $users = $statement->fetchAll(\PDO::FETCH_ASSOC);            
        }
        
        return $users;
    }
    
    public function getFailureUsers()
    {
        $readConnection = $this->getReadConnection(); 
        $userTableName = self::USERTABLENAME;
                
        $users = array();
               
        $query = "SELECT id, name, total_amount, total_origin, status FROM $userTableName";
        $query .= " WHERE total_amount < total_origin ORDER BY (total_amount - total_origin) ASC LIMIT 20";
        
        $statement = $readConnection->prepare($query);
                       
        if ($statement->execute()) {
            $users = $statement->fetchAll(\PDO::FETCH_ASSOC);            
        }
        
        return $users;
    }
    
    public function getTotalSuccessUsers()
    {
        $readConnection = $this->getReadConnection(); 
        $userTableName = self::USERTABLENAME;
                
        $total = 0;
               
        $query = "SELECT COUNT(id) AS count FROM $userTableName";
        $query .= " WHERE total_amount > total_origin";
        
        $statement = $readConnection->prepare($query);
                       
        if ($statement->execute()) {
            $total = $statement->fetch(\PDO::FETCH_ASSOC);            
        }
        
        return $total['count'];
    }
    
    public function getTotalFailureUsers()
    {
        $readConnection = $this->getReadConnection(); 
        $userTableName = self::USERTABLENAME;
                
        $total = 0;
               
        $query = "SELECT COUNT(id) AS count FROM $userTableName";
        $query .= " WHERE total_amount < total_origin";
        
        $statement = $readConnection->prepare($query);
                       
        if ($statement->execute()) {
            $total = $statement->fetch(\PDO::FETCH_ASSOC);            
        }
        
        return $total['count'];
    }
    
    public function getTotalUsers()
    {
        $readConnection = $this->getReadConnection(); 
        $userTableName = self::USERTABLENAME;
                
        $total = 0;
               
        $query = "SELECT COUNT(id) AS count FROM $userTableName";
                        
        $statement = $readConnection->prepare($query);
                       
        if ($statement->execute()) {
            $total = $statement->fetch(\PDO::FETCH_ASSOC);            
        }
        
        return $total['count'];
    }
    
    public function getStatisticsByRule($type)
    {
        $readConnection = $this->getReadConnection(); 
        $userTableName = self::USERTABLENAME;
        $RuleTableName = self::RULETABLENAME;
        $statistics = null;
                
        $where = '';
        
        if ($type === 'positive') {
            $where = "WHERE u.total_amount > u.total_origin";
        } else if ($type === 'negative') {
            $where = "WHERE u.total_amount < u.total_origin";
        }
        
        $query = "SELECT rl.name AS ruleName, COUNT(u.id) AS userTotal FROM $RuleTableName AS rl";
        $query .= " LEFT JOIN $userTableName AS u ON rl.id = u.rule_id $where";
        $query .= " GROUP BY u.rule_id";
                
        $statement = $readConnection->prepare($query);
                       
        if ($statement->execute()) {
            $statistics = $statement->fetchAll(\PDO::FETCH_ASSOC);            
        }
        
        return $statistics;
    }
    
    public function getUserBets($userId)
    {
        $readConnection = $this->getReadConnection(); 
        $tableName = self::BETTABLENAME;
               
        $bets = array();
               
        $query = "SELECT b.* FROM $tableName AS b WHERE user_id = :userId";
                
        $statement = $readConnection->prepare($query);
        
        $statement->bindValue(':userId', $userId, \PDO::PARAM_INT);
                
        if ($statement->execute()) {
            $bets = $statement->fetchAll(\PDO::FETCH_ASSOC);            
        }
        
        return $bets;
    }
    
    public function getUser($userId)
    {
        $readConnection = $this->getReadConnection(); 
        $tableName = self::USERTABLENAME;
               
        $user = null;
               
        $query = "SELECT * FROM $tableName WHERE id = :userId";
                
        $statement = $readConnection->prepare($query);
        
        $statement->bindValue(':userId', $userId, \PDO::PARAM_INT);
                
        if ($statement->execute()) {
            $user = $statement->fetch(\PDO::FETCH_ASSOC);            
        }
        
        return $user;
    }
}