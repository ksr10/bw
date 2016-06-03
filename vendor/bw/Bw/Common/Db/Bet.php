<?php

namespace Bw\Common\Db;

use Bw\Common\Db;

class Bet extends Db
{
    const BETTABLENAME = 'bets';    
       
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
}