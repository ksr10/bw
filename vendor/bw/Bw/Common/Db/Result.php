<?php

namespace Bw\Common\Db;

use Bw\Common\Db;

class Result extends Db
{
    const RESULTTABLENAME = 'results';    
    
    static protected $instance;
    
    public static function i()
    {
        return isset(static::$instance) ? static::$instance : (static::$instance = new static());
    }
    
    public function saveResult($data)
    {
        $resultId = null;
        $tableName = self::RESULTTABLENAME;
        
        $writeConnection = $this->getWriteConnection();
        $query = "INSERT INTO $tableName (ratio_id, host_team, guest_team, event_date, score, status) VALUES ";
        $query .= "(:ratioId, :hostTeam, :guestTeam, :eventDate, :score, :status)";

        $statement = $writeConnection->prepare($query);                

        $statement->bindValue(':ratioId', $data['ratio_id'], \PDO::PARAM_INT);
        $statement->bindValue(':hostTeam', $data['host_team'], \PDO::PARAM_STR);
        $statement->bindValue(':guestTeam', $data['guest_team'], \PDO::PARAM_STR);
        $statement->bindValue(':eventDate', $data['event_date'], \PDO::PARAM_STR);
        $statement->bindValue(':score', $data['score'], \PDO::PARAM_STR);
        $statement->bindValue(':status', $data['status'], \PDO::PARAM_STR);
        
        if ($statement->execute()) {
            $resultId = $writeConnection->lastInsertId();                    
        }
        
        return $resultId;
    }
    
    public function getResultByTeam($hostTeam, $guestTeam)
    {
        $readConnection = $this->getReadConnection(); 
        $tableName = self::RESULTTABLENAME;
        $result = array();
               
        $query = "SELECT * FROM $tableName WHERE status = 'new' AND host_team LIKE :hostTeam";
        $query .= " AND guest_team LIKE :guestTeam LIMIT 1";
        
        $statement = $readConnection->prepare($query);
        $statement->bindValue(':hostTeam', $hostTeam, \PDO::PARAM_STR);
        $statement->bindValue(':guestTeam', $guestTeam, \PDO::PARAM_STR);
        
        if ($statement->execute()) {
            $result = $statement->fetch(\PDO::FETCH_ASSOC);            
        }
        
        return $result;
    }
    
    public function deleteAllResults()
    {
        $tableName = self::RESULTTABLENAME;
        $result = false;
        
        $writeConnection = $this->getWriteConnection();
        $query = "DELETE FROM $tableName";
        
        $statement = $writeConnection->prepare($query);
        
        if ($statement->execute()) {
            $result = true;            
        }
        
        return $result;
    }
}