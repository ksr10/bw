<?php

namespace Bw\Api\Service;

use Bw\Api\Service\Rule as RuleService;
use Bw\Api\Service\Ratio as RatioService;
use Bw\Common\Db\Bet as BetDb;

class Bet extends Base
{
    const USER_STATUS_ACTIVE = 'active';
    const VIRTUAL_USERS_DAY_AIM = 10;
    const WAITING_RESULT = 3;
    
    public function __construct() 
    {
        parent::__construct();
        BetDb::i()->setConfig($this->dbConfig);      
    }
    
    public function generateBets() 
    {
        $ruleService = new RuleService();
        $rules = $ruleService->getRules();
        
        foreach ($rules as $rule) {
            $this->generateRuleBet($ruleService, $rule);
        }        
    }
    
    protected function generateRuleBet($ruleService, $rule)
    {
        $nameParts = explode(' ', $rule['name']);
        $ratioRange = array_pop($nameParts);
        $ratioRangeParts = explode('-', $ratioRange);
        
        list($from, $to) = $ratioRangeParts;
        $to = (float) $to;
        $from = (float) $from;
               
        $users = $ruleService->getUsersByRule($rule['id']);
        $ratioService = new RatioService();
        
        foreach ($users as $user) {
            if ($user['status'] === Bet::USER_STATUS_ACTIVE) {
                $ratio = $ratioService->getRatioForBet($from, $to);
                
                if ($ratio) {
                    $this->saveBet($ratio, $user, $rule);
                }                
            }            
        }        
    }
    
    protected function saveBet($ratio, $user, $rule)
    {
        $eventData = array(
            'league_id' => $ratio['league_id'],
            'host_team' => $ratio['host_team'],
            'host_team_odds' => $ratio['host_team_odds'],
            'draw_odds' => $ratio['draw_odds'],
            'guest_team' => $ratio['guest_team'],
            'guest_team_odds' => $ratio['guest_team_odds'],
            'event_date' => $ratio['event_date']
        );
        
        $data = array(
            'rule_id' => $rule['id'],
            'user_id' => $user['id'],
            'ratio_id' => $ratio['id'],
            'bet_amount' => $this->calcuteBetAmount($ratio, $user),
            'result' => self::WAITING_RESULT,
            'event_data' => serialize($eventData)
        );
        
        BetDb::i()->saveBet($data);
    }
    
    protected function calcuteBetAmount($ratio, $user)
    {
        $lastBet = $this->getLastBet($user['id']);
        $betAim = self::VIRTUAL_USERS_DAY_AIM;
        
        if ($lastBet && !$lastBet['result']) {
            $betAim = (int) (self::VIRTUAL_USERS_DAY_AIM + ceil($lastBet['bet_amount']));
        }
        
        $hostOdd = (float) $ratio['host_team_odds'];
        $hostOdd = $hostOdd + 1;
        $betSum = ($betAim * 100) / (($hostOdd * 100) - 100);
        
        return number_format($betSum, 2);        
    }
    
    public function getLastBet($userId)
    {
        return BetDb::i()->getLastBet($userId);
    }
}