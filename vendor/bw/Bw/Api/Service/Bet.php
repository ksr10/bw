<?php

namespace Bw\Api\Service;

use Bw\Api\Service\Rule as RuleService;
use Bw\Api\Service\Ratio as RatioService;
use Bw\Api\Service\Result as ResultService;
use Bw\Common\Db\Bet as BetDb;

class Bet extends Base
{
    const USER_STATUS_ACTIVE = 'active';
    const USER_STATUS_POSITIVE = 'positive';
    const USER_STATUS_NEGATIVE = 'negative';
    const USER_STATUS_LOST = 'lost';
    
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
            'league_id' => $ratio['leagueId'],
            'league_name' => $ratio['leagueName'],
            'league_country' => $ratio['leagueCountry'],
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
        $result = (int) $lastBet['result'];
        
        if ($lastBet && !$result) {
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
    
    public function calculateBets()
    {
        $resultService = new ResultService();
        $bets = $this->getNewBets();
        
        foreach ($bets as $bet) {
            $eventResult = $resultService->getResultByTeam($bet['host_team'], $bet['guest_team']);
            
            if ($eventResult) {
                $eventScoreResult = $this->getEventScoreResult($eventResult['score']);
                $betData = array(
                    'rule_id' => $bet['rule_id'],
                    'user_id' => $bet['user_id'],
                    'ratio_id' => $bet['ratio_id'],
                    'bet_amount' => $bet['bet_amount'],
                    'result' => $eventScoreResult,
                );
                
                $eventData = unserialize($bet['event_data']);
                $eventData['score'] = $eventResult['score'];
                
                $betData['event_data'] = serialize($eventData);
                $this->updateBet($bet['id'], $betData);
                
                $userCurrentAmount = (float) $bet['total_amount'];
                
                if ($eventScoreResult === 1) {
                    $userCurrentAmount = $userCurrentAmount + (float) $bet['bet_amount'];
                } elseif ($eventScoreResult === 0) {
                    $userCurrentAmount = $userCurrentAmount - (float) $bet['bet_amount'];
                }
                
                $this->updateUserTotal($userCurrentAmount, $bet['user_id']);                
            } else {
                file_put_contents($GLOBALS['root_dir'].'/data/logs/logger.log', print_r('Result not found', 1 ) . "\n", FILE_APPEND);
                file_put_contents($GLOBALS['root_dir'].'/data/logs/logger.log', print_r($bet, 1 ) . "\n", FILE_APPEND); die();
            }            
        }            
        
        $ratioService = new RatioService();
        $resultService->deleteAllResults();
        $ratioService->deleteAllRatios();
        
        $ruleService = new RuleService();
        $ruleService->updateUserActive();
    }
    
    protected function updateUserTotal($total, $userId)
    {
        return BetDb::i()->updateUserTotal($total, $userId);
    }
    
    public function getNewBets()
    {
        return BetDb::i()->getNewBets(self::WAITING_RESULT);
    }
    
    protected function getEventScoreResult($score)
    {
        $scoreParts = explode(':', $score);
        
        if (count($scoreParts) === 2) {
            $hostTeamGoals = (int) isset($scoreParts[0]) ? $scoreParts[0] : 0;
            $guestTeamGoals = (int) isset($scoreParts[1]) ? $scoreParts[1] : 0;

            return ($hostTeamGoals > $guestTeamGoals) ? 1 : 0;
        }        
        
        return null;
    }
    
    protected function updateBet($betId, $betData)
    {
        return BetDb::i()->updateBet($betId, $betData);
    }
    
    public function getOwnUsers()
    {
        $result = array();
        $users = BetDb::i()->getOwnUsers();
        $positiveOwnUsersTotal = count($users);
        $negativeOwnUsersTotal = 0;
        $lostOwnUsersTotal = 0;
        
        foreach ($users as $index => $user) {
            $status = self::USER_STATUS_POSITIVE;
            $user['total_amount'] = (float) $user['total_amount'];
            $user['total_origin'] = (float) $user['total_origin'];
            
            if ($user['total_amount'] < $user['total_origin']) {
                $status = self::USER_STATUS_NEGATIVE;
                $positiveOwnUsersTotal--;
                $negativeOwnUsersTotal++;
            }
            
            if ($user['total_amount'] < 0) {
                $status = self::USER_STATUS_LOST;
                $lostOwnUsersTotal++;
                $negativeOwnUsersTotal--;
            }
            
            $users[$index]['current_amount'] = number_format($user['total_amount'], 2);
            $users[$index]['origin_amount'] = number_format($user['total_origin'], 2);
            $users[$index]['status'] = $status;
        }
        
        $result['usersTotal'] = count($users);
        $result['positiveOwnUsersTotal'] = $positiveOwnUsersTotal;
        $result['negativeOwnUsersTotal'] = $negativeOwnUsersTotal;
        $result['lostOwnUsersTotal'] = $lostOwnUsersTotal;
        $result['users'] = $users;
        
        return $result;
    }
    
    public function getSuccessUsers() 
    {
        $result = array();
        $users = BetDb::i()->getSuccessUsers();
        
        foreach ($users as $index => $user) {
            $users[$index]['current_amount'] = number_format($user['total_amount'], 2);
            $users[$index]['origin_amount'] = number_format($user['total_origin'], 2);
            $users[$index]['status'] = self::USER_STATUS_POSITIVE;
        }
        
        $result['users'] = $users;
        $result['sucUsersTotal'] = BetDb::i()->getTotalSuccessUsers();
        $result['usersTotal'] = BetDb::i()->getTotalUsers();
        $result['statisticsByRule'] = BetDb::i()->getStatisticsByRule(self::USER_STATUS_POSITIVE);
        
        return $result;
    }
    
    public function getFailureUsers()
    {
        $result = array();
        $users = BetDb::i()->getFailureUsers();
        
        foreach ($users as $index => $user) {
            $users[$index]['current_amount'] = number_format($user['total_amount'], 2);
            $users[$index]['origin_amount'] = number_format($user['total_origin'], 2);
            $users[$index]['status'] = self::USER_STATUS_NEGATIVE;
        }
        
        $result['users'] = $users;
        $result['failUsersTotal'] = BetDb::i()->getTotalFailureUsers();
        $result['statisticsByRule'] = BetDb::i()->getStatisticsByRule(self::USER_STATUS_NEGATIVE);
        
        return $result;
    }
    
    public function getUserBets($userId)
    {
        $result = array();
        $bets = BetDb::i()->getUserBets($userId);
        
        foreach ($bets as $index => $bet) {
            $bets[$index]['bet_amount'] = number_format($bet['bet_amount'], 2);
            $eventData = unserialize($bet['event_data']);
            
            $bets[$index]['leagueName'] = (isset($eventData['league_name']) && isset($eventData['league_country'])) ? $eventData['league_country'] . ' ' .$eventData['league_name'] : $eventData['league_id'];
            $bets[$index]['event'] = $eventData['host_team'] . ' - ' . $eventData['guest_team'];
            $bets[$index]['score'] = $eventData['score'];
            $bets[$index]['event_date'] = $eventData['event_date'];
            $bets[$index]['ratio'] = $eventData['host_team_odds'] . ' x ' . $eventData['draw_odds'] . ' x ' . $eventData['guest_team_odds'];
            $bets[$index]['scoreCssClass'] = $bet['result'] == '1' ? 'success' : 'failure';          
            
            unset($bets[$index]['event_data']);
        }        
        
        $result['user'] = BetDb::i()->getUser($userId);
        $result['bets'] = $bets;
        
        return $result;
    }
        
}