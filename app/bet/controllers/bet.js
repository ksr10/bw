(function() {
    'use strict';

    angular
        .module('app.bet')
        .controller('BetController', BetController);
        
    BetController.$inject = ['betService', '$scope', '$state', '$stateParams'];
    
    function BetController(betService, $scope, $state, $stateParams) {
        var bet = this;
        
        bet.data = {};
        
        bet.getUsers = getUsers;
        bet.getSuccessUsers = getSuccessUsers;
        bet.getFailureUsers = getFailureUsers;
        bet.showBets = showBets;
        bet.getBets = getBets;
        
        function getUsers() {
            return betService.getUsers(bet);
        }
        
        function getSuccessUsers() {
            return betService.getSuccessUsers(bet);
        }
        
        function getFailureUsers() {
            return betService.getFailureUsers(bet);
        }
        
        function showBets(event, userId) {
            event.preventDefault();
            
            $state.go('bets', {userId: userId});
        }
        
        function getBets() {
            if ($stateParams.hasOwnProperty('userId') && $stateParams.userId) {
                bet.data.userId = $stateParams.userId;
                return betService.showBets(bet);
            }
        }
    }

})();