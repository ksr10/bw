<?php

namespace Bw\Common\Db;

use Bw\Common\Db;

class Ratio extends Db
{
    const LEAGETABLENAME = 'leagues';    
    const RATIOTABLENAME = 'ratios';
    const LEAGUETABLENAME = 'leagues';   
    
    static protected $instance;
    
    public static function i()
    {
        return isset(static::$instance) ? static::$instance : (static::$instance = new static());
    }
    
    public function saveLeague($item)
    {
        $readConnection = $this->getReadConnection();  
        $leagueId = null;
        $tableName = self::LEAGETABLENAME;
                
        $statement = $readConnection->prepare("SELECT * FROM $tableName WHERE name LIKE :leagueName AND country LIKE :country");
        $statement->bindValue(':leagueName', $item['league_name'], \PDO::PARAM_STR);
        $statement->bindValue(':country', $item['country'], \PDO::PARAM_STR);
        
        if ($statement->execute()) {
            $result = $statement->fetch(\PDO::FETCH_ASSOC);
            
            if ($result) {
                $leagueId = $result['id'];
            } else {
                $writeConnection = $this->getWriteConnection();
                $statement = $writeConnection->prepare("INSERT INTO $tableName (name, country) VALUES (:leagueName, :country)");                
                
                $statement->bindValue(':leagueName', $item['league_name'], \PDO::PARAM_STR);
                $statement->bindValue(':country', $item['country'], \PDO::PARAM_STR);

                if ($statement->execute()) {
                    $leagueId = $writeConnection->lastInsertId();
                }
            }
        }     
        
        return $leagueId;
    }
    
    public function saveRatio($item, $leagueId) 
    {
        $readConnection = $this->getReadConnection(); 
        $tableName = self::RATIOTABLENAME;
        $siteId = 1;
        $ratioId = null;        
        
        $statement = $readConnection->prepare("SELECT * FROM $tableName WHERE host_team LIKE :hostTeam AND guest_team LIKE :guestTeam");
        $statement->bindValue(':hostTeam', $item['host_team'], \PDO::PARAM_STR);
        $statement->bindValue(':guestTeam', $item['guest_team'], \PDO::PARAM_STR);
        
        if ($statement->execute()) {
            $result = $statement->fetch(\PDO::FETCH_ASSOC);
            
            if ($result) {
                $ratioId = $result['id'];
            } else {
                $writeConnection = $this->getWriteConnection();
                $query = "INSERT INTO $tableName (site_id, league_id, host_team, host_team_odds, draw_odds, guest_team, guest_team_odds, event_date) VALUES ";
                $query .= "(:siteId, :leagueId, :hostTeam, :hostTeamOdds, :drawOdds, :guestTeam, :guestTeamOdds, :eventDate)";
                
                $statement = $writeConnection->prepare($query);                

                $statement->bindValue(':siteId', $siteId, \PDO::PARAM_INT);
                $statement->bindValue(':leagueId', $leagueId, \PDO::PARAM_INT);
                $statement->bindValue(':hostTeam', $item['host_team'], \PDO::PARAM_STR);
                $statement->bindValue(':hostTeamOdds', $item['host_team_odds'], \PDO::PARAM_STR);
                $statement->bindValue(':drawOdds', $item['draw_odds'], \PDO::PARAM_STR);
                $statement->bindValue(':guestTeam', $item['guest_team'], \PDO::PARAM_STR);
                $statement->bindValue(':guestTeamOdds', $item['guest_team_odds'], \PDO::PARAM_STR);
                $statement->bindValue(':eventDate', $item['event_date'], \PDO::PARAM_STR);

                if ($statement->execute()) {
                    $ratioId = $writeConnection->lastInsertId();                    
                }
            }
        }     
        
        return $ratioId;
    }
    
    public function getRatioForBet($from, $to)
    {
        $readConnection = $this->getReadConnection(); 
        $tableName = self::RATIOTABLENAME;
        $leagueTableName = self::LEAGUETABLENAME;
        $result = null;
        
        $query = "SELECT r.*, l.id AS leagueId, l.name AS leagueName, l.country AS leagueCountry  FROM $tableName AS r";
        $query .= " LEFT JOIN $leagueTableName AS l ON r.league_id = l.id";
        $query .= " WHERE host_team_odds < $to AND host_team_odds >= $from ORDER BY RAND() LIMIT 1";
        
        $statement = $readConnection->prepare($query);
        
        if ($statement->execute()) {
            $result = $statement->fetch(\PDO::FETCH_ASSOC);            
        }
        
        return $result;
    }
    
    public function getRatioByTeams($hostTeam, $guestTeam)
    {
        $readConnection = $this->getReadConnection(); 
        $tableName = self::RATIOTABLENAME;
        $result = null;
               
        $statement = $readConnection->prepare("SELECT * FROM $tableName WHERE host_team LIKE :hostTeam AND guest_team LIKE :guestteam LIMIT 1");
        $statement->bindValue(':hostTeam', $hostTeam, \PDO::PARAM_STR);
        $statement->bindValue(':guestteam', $guestTeam, \PDO::PARAM_STR);
        
        if ($statement->execute()) {
            $result = $statement->fetch(\PDO::FETCH_ASSOC);            
        }
        
        if (!$result) {
            $statement = $readConnection->prepare("SELECT * FROM $tableName WHERE host_team LIKE :hostTeam LIMIT 1");
            $statement->bindValue(':hostTeam', $hostTeam, \PDO::PARAM_STR);
            
            if ($statement->execute()) {
                $result = $statement->fetch(\PDO::FETCH_ASSOC);            
            }
        }
        
        return $result;
    }
    
    public function deleteAllRatios()
    {        
        $tableName = self::RATIOTABLENAME;
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