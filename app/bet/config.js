(function() {    
    'use strict';
    
    angular
        .module('app.bet')
        .config(betConfig);
        
    function betConfig($stateProvider, $urlRouterProvider, $sceDelegateProvider) {
        $sceDelegateProvider.resourceUrlWhitelist(['**']);
                
        $stateProvider.state('users', {
            url: '/users',
            templateUrl: '/app/bet/views/users.html'            
        })
        .state('bets', {
            url: '/bets/:userId',
            templateUrl: '/app/bet/views/bets.html'                      
        });        
        
        $urlRouterProvider.otherwise('/users');
    }
})(); 

