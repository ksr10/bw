(function() {
    'use strict';
    
    angular
        .module('app.bet')
        .service('betService', betService);
        
    betService.$inject = ['$http'];
        
    function betService($http) {
        var betService = {
            getUsers: getUsers,
            getSuccessUsers: getSuccessUsers,
            getFailureUsers: getFailureUsers,
            showBets: showBets
        };
        
        return betService;    
        
        function getUsers(betCtrl) {
            $http.get('/get-own-users').
            success(function(data) {
                betCtrl.ownUsers = data.users;
                betCtrl.ownUsersTotal = data.usersTotal;
                
                betCtrl.positiveOwnUsersTotal = data.positiveOwnUsersTotal;
                betCtrl.negativeOwnUsersTotal = data.negativeOwnUsersTotal;
                betCtrl.lostOwnUsersTotal = data.lostOwnUsersTotal;
            });
        }
        
        function getSuccessUsers(betCtrl) {
            $http.get('/get-success-users').
            success(function(data) {
                betCtrl.successUsers = data.users;   
                betCtrl.sucUsersTotal = data.sucUsersTotal;
                betCtrl.usersTotal = data.usersTotal;
                betCtrl.statisticsByRuleSuc = data.statisticsByRule;
            });
        }
        
        function getFailureUsers(betCtrl) {
            $http.get('/get-failure-users').
            success(function(data) {
                betCtrl.failureUsers = data.users;   
                betCtrl.failUsersTotal = data.failUsersTotal;                
                betCtrl.statisticsByRuleFail = data.statisticsByRule;
            });
        }
        
        function showBets(betCtrl) {
            $http.get('/bets/'+betCtrl.data.userId).
            success(function(data) {
                betCtrl.bets = data.bets;
                betCtrl.user = data.user;
            });
        }
    }   
})();