(function() {
    'use strict';

    angular
        .module('admin.bet')
        .controller('BetController', BetController);
        
    BetController.$inject = ['$scope', 'betService'];

    function BetController($scope, betService) {
        var bet = this;
        
        bet.data = {
          applicantCnt: 1
        };
        
        bet.addMoreApplicants = addMoreApplicants;
        bet.removeLastApplicant = removeLastApplicant;
        bet.saveApplicants = saveApplicants;
        bet.initSelectBet = initSelectBet;
        bet.selectBet = selectBet;
        bet.saveSelectedApplicants = saveSelectedApplicants;
        bet.cancelLastSelected = cancelLastSelected;
        bet.initAddResults = initAddResults;
        bet.saveResults = saveResults;
        bet.getBets = getBets;
        
        function addMoreApplicants() {
          bet.data.applicantCnt++;
          return betService.addMoreApplicants(bet);
        }
        
        function removeLastApplicant() {
          if (bet.data.applicantCnt === 1) {
            return;
          }
          
          betService.removeLastApplicant(bet);
          bet.data.applicantCnt--;
        }
        
        function saveApplicants() {
          return betService.saveApplicants(bet);
        }
        
        function initSelectBet() {
          return betService.initSelectBet(bet);
        }
        
        function selectBet(applicantId) {
          return betService.selectBet(bet, applicantId);
        }
        
        function saveSelectedApplicants() {
          return betService.saveSelectedApplicants(bet);
        }
        
        function cancelLastSelected() {
          return betService.cancelLastSelected(bet);
        }
        
        function initAddResults() {
          return betService.initAddResults(bet);
        }
        
        function saveResults() {
          return betService.saveResults(bet);
        }
        
        function getBets() {
          return betService.getBets(bet);
        }
    }

})();