<?php

namespace Bw\Api\Service;

use Zend\Dom\Query;

class Marathonbet extends Connect
{
    const LEAG_BLOCK = 'ul li a';    
    //const EVENTS_MAIN_BLOCK = '.main-block-events';
    const EVENTS_MAIN_BLOCK = '.grid-main';
    const LEAGUE_COUNTRY_BLOCK = '.category-header .category-label span';
    const LEAGUE_EVENT_BLOCK = '.foot-market tr.event-header';
    const TEAMS_BLOCK = '.command';
    const DATE_BLOCK = '.date';
    const RATIOS_BLOCK = '.price';
    
    CONST RESULT_PAGES_BLOCK = '.page-navigator a';
    
    const BASE_URL = 'https://www.marathonbet.com';
    
    protected $body = null; 
    
    protected $dom = null; 
    
    protected $ratioData = array();
    
    protected $forbiddenLeagueNameParts = array(
        'Specials', 'UEFA', 'FIFA', 'AFC', 'Copa',
        'Friendlies', 'International', 'UAE', 'Women', 'OFC',
        'CAF'
    );
    
    protected $uries = array(
       'base' => array(
           'uri' => 'https://www.marathonbet.com/en/prematchMenuItem.htm',
           'params' => array(
               'nodeId' => '11',
               'menuName' => 'popularSport',
               '_' => '1451052612417'
           )
       )
    );
    
    protected $resultUries = array(
       'base' => array(
           'uri' => 'https://www.marathonbet.com/en/results.htm',
           'params' => array(
               'form_name' => 'form',
               '1762798' => 'on',
               'searchQuery' => '',
               'dateFilterSelector' => 'RANGE',
               'fromDate' => '',
               'toDate' => '',
           )
       )
   );
    
    public function dispatchRatio()
    {
        foreach ($this->uries as $key => $uri) {
            $params = $uri['params'];
            $response = $this->connect($uri['uri'], false, $params);
            $this->body = $response->getBody();
            file_put_contents($GLOBALS['root_dir'].'/data/logs/ratio/base.html', $this->body);
            
            //$this->body = file_get_contents($GLOBALS['root_dir'].'/data/logs/ratio/base.html');
            $this->dom = new Query($this->body);
            
            $this->parseAll();
        }        
    }
    
    protected function parseAll()
    {
        $leagueNodeList = $this->dom->execute(self::LEAG_BLOCK);
        $leagues = $this->parseLeagueLinks($leagueNodeList);
        
        $events = $this->parseLeagueEvents($leagues);
        
        $this->ratioData = $events;        
    }
    
    protected function parseLeagueEvents($leagues) 
    {
        $result = array();
        
        foreach ($leagues as $index => $league) {
            $urlParts = explode('?', $league['link']);
            $leagueUrl = self::BASE_URL . array_shift($urlParts);
            
            $paramsStr = array_shift($urlParts);
            $paramsParts = explode('&', $paramsStr);
            
            $params = array();
            
            foreach ($paramsParts as $item) {
                $itemParts = explode('=', $item);
                $key = array_shift($itemParts);
                $value = array_shift($itemParts);
                
                $params[$key] = $value;
            }
            
            $response = $this->connect($leagueUrl, false, $params);
            $this->body = $response->getBody();
            file_put_contents($GLOBALS['root_dir'].'/data/logs/ratio/ratio'.$index.'.html', $this->body);
            //$this->body = file_get_contents($GLOBALS['root_dir'].'/data/logs/ratio/ratio'.$index.'.html');
            
            $this->dom = new Query($this->body);
            $mainBlockNodeList = $this->dom->execute(self::EVENTS_MAIN_BLOCK);
            $mainBlockElement = $mainBlockNodeList->rewind();
            
            $newDom = $this->getSearchArea($mainBlockElement);
            $leagueAndCountryList = $newDom->execute(self::LEAGUE_COUNTRY_BLOCK);
            $leagueAndCountryArr = array();
            
            foreach ($leagueAndCountryList as $node) {
                $leagueAndCountryArr[] = $this->removeSpaces($node->nodeValue);
            }
                        
            $leagueData = array(
                'country' => rtrim(array_shift($leagueAndCountryArr), '.'),
                'league_name' => rtrim(array_shift($leagueAndCountryArr), '.')
            );
            
            $leagueEventsList = $newDom->execute(self::LEAGUE_EVENT_BLOCK);
            $leagueData['events'] = $this->getLeagueEvents($leagueEventsList);
                                    
            $result[] = $leagueData;
            
            /*if ($index === 2) {
                break;
            }   */         
        }
        
        return $result;
    }
    
    protected function getLeagueEvents($leagueEventsList)
    {
        $events = array();
        
        foreach ($leagueEventsList as $node) {
            $newDom = $this->getSearchArea($node);
            $teamsNodeList = $newDom->execute(self::TEAMS_BLOCK);
            $teamsNode = $teamsNodeList->rewind();
            
            $teams = $this->getEventTeams($teamsNode);
            
            $eventData = array(
                'host_team' => array_shift($teams),
                'guest_team' => array_shift($teams),
            );
            
            $dateNodeList = $newDom->execute(self::DATE_BLOCK);
            $dateNode = $dateNodeList->rewind();
            $eventData['event_date'] = $this->convertTime($this->removeSpaces($dateNode->nodeValue));
            
            $ratiosNodeList = $newDom->execute(self::RATIOS_BLOCK);
            $ratios = $this->getEventRatios($ratiosNodeList);
            
            $eventData['host_team_odds'] = array_shift($ratios);
            $eventData['draw_odds'] = array_shift($ratios);
            $eventData['guest_team_odds'] = array_shift($ratios);
            
            $events[] = $eventData;            
        }        
        
        return $events;
    }
    
    protected function getEventRatios($ratiosNodeList)
    {
        $ratios = array();
        
        foreach ($ratiosNodeList as $node) {
            $ratioStr = $this->removeSpaces($node->nodeValue);
            $ratioParts = explode('/', $ratioStr);
            
            $firstValue = (int)$ratioParts[0];
            $secondValue = (int)$ratioParts[1];
            $ratio = $firstValue / $secondValue;
            
            $ratios[] = $ratio;
        }
        
        return $ratios;
    }
    
    protected function getEventTeams($teamsNode) 
    {
        $newDom = $this->getSearchArea($teamsNode);
        //$teamsNodeList = $newDom->execute('div');
        $teamsNodeList = $newDom->execute('div.nowrap');        
        $teams = array();
        
        foreach ($teamsNodeList as $node) {
            $teams[] = $this->removeSpaces($node->nodeValue);
        }
        
        return $teams;
    }
    
    protected function parseLeagueLinks($leagueNodeList)
    {
        $leagues = array();
        
        foreach ($leagueNodeList as $node) {
            $leagueName = $this->removeSpaces($node->nodeValue);
            
            $found = false;
            foreach ($this->forbiddenLeagueNameParts as $part) {
                if (false !== strpos($leagueName, $part)) {                    
                    $found = true;
                }
            }
            
            if (!$found) {
                $leagues[] = array(
                    'name' => $leagueName,
                    'link' => $this->removeSpaces($node->getAttribute('href'))
                );
            }                                  
        }      
        
        return $leagues;
    }
    
    protected function getSearchArea($node)
   {
       if ($node) {
            $newDoc = new \DOMDocument();
            $cloned = $node->cloneNode(true);
            $newDoc->appendChild($newDoc->importNode($cloned, true));

            $newDom = new Query($newDoc->saveHTML());
            
            return $newDom;
       }
       
       return null;
   }
   
   public function getRatioData()
   {
       return $this->ratioData;
   }
   
   protected function convertTime($time)
   {
       $parts = explode(' ', $time);
       $dateTime = null;
       
       if (count($parts) === 3) {
           $monthNumber = date('n', strtotime($parts[1]));
           $year = date('Y');
           $dateTime = $parts[0].'-'.$monthNumber.'-'.$year.' '.$parts[2];           
       } else if (count($parts) === 1) {
           $date = date('Y-m-d');
           $dateTime = $date.' '.$parts[0];
       } else {
           throw new Exception('Date format changed!');           
       }
       
       return date('Y-m-d H:i:s', strtotime($dateTime));     
   }
   
   public function dispatchResult()
   {
       foreach ($this->resultUries as $key => $uri) {
           $now = date('d.m.Y');
           $fromDate = date('d.m.Y', strtotime($now . ' -2 day'));
           
           if (isset($uri['params']['toDate'])) {
               $uri['params']['toDate'] = $now;
           }
           
           if (isset($uri['params']['fromDate'])) {
               $uri['params']['fromDate'] = $fromDate;
           }
           
           /*$response = $this->connect($uri['uri'], false, $uri['params']);
           $this->body = $response->getBody();
           file_put_contents($GLOBALS['root_dir'].'/data/logs/result/base.html', $this->body);*/
            
           $this->body = file_get_contents($GLOBALS['root_dir'].'/data/logs/result/base.html');
           $this->dom = new Query($this->body);
            
           $this->parseResultAll();
       }       
   }
   
   protected function parseResultAll()
   {       
        $pagesNodeList = $this->dom->execute(self::RESULT_PAGES_BLOCK);
        $pages = $this->parseResultPages($pagesNodeList);
        
        /*$pagesResults = $this->parseResults($pages);
        
        $this->eventResults = $pagesResults;  */
   }
   
   protected function parseResultPages($pagesNodeList)
   {
       
   }
}