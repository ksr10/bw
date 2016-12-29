<?php

namespace Bw\Api\Service;

use Bw\Common\Db\Rule as RuleDb;

class Rule extends Base
{
    const VIRTUAL_USERS_COUNT = 50;
    
    protected $usersBankTotals = array(
        3000, 6000, 12000, 24000, 48000
    );
    
    public function __construct() 
    {
        parent::__construct();
        RuleDb::i()->setConfig($this->dbConfig);      
    }
    
    public function generateUsers() 
    {        
        $rules = RuleDb::i()->getRules();
        
        foreach ($rules as $rule) {
            foreach ($this->usersBankTotals as $bankTotal) {
                for ($index = 0; $index < self::VIRTUAL_USERS_COUNT; $index++) {
                    $virtualUser = array();
                    $userName = strtolower(str_replace(' ', '_', $rule['name']));
                    $userNum = $index + 1;

                    $virtualUser['name'] = $userName . '_' . $userNum;
                    $virtualUser['rule_id'] = $rule['id'];
                    $virtualUser['total_amount'] = $bankTotal;
                    $virtualUser['total_origin'] = $bankTotal;
                    
                    RuleDb::i()->saveUser($virtualUser);
                }
            }            
        }
    }
    
    public function getRules()
    {
        return RuleDb::i()->getRules();
    }
    
    public function getUsersByRule($ruleId, $active = true)
    {
        return RuleDb::i()->getUsersByRule($ruleId, $active);
    }
    
    public function updateUserActive()
    {
        $activeUsers = RuleDb::i()->getUsers();
        
        foreach ($activeUsers as $user) {
            $totalAmount  = (float) $user['total_amount'];
            
            if ($totalAmount < 0) {
                RuleDb::i()->updateUserActive($user['id']);
            }
        }
    }
}