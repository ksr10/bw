(function() {
    'use strict';
    
    angular
        .module('admin.bet')
        .service('betService', betService);
        
    betService.$inject = ['$http', 'localStorageService', 'layoutService'];
        
    function betService($http, localStorageService, layoutService) {
        var betService = {
          
          apiUrl: '',
          
          addMoreApplicants: addMoreApplicants,
          removeLastApplicant: removeLastApplicant,
          saveApplicants: saveApplicants,
          initSelectBet: initSelectBet,
          getRandomArbitrary: getRandomArbitrary,
          selectBet: selectBet,
          saveSelectedApplicants: saveSelectedApplicants,
          cancelLastSelected: cancelLastSelected,
          initAddResults: initAddResults,
          saveResults: saveResults,
          getBets: getBets
        };
        
        return betService;
        
        function addMoreApplicants(betCtrl) {
          var formContent = '<div id="applicantWrapper'+betCtrl.data.applicantCnt+'" style="clear:both; padding-top:10px;"></div>',
              prevBlockId = betCtrl.data.applicantCnt - 1;
      
            $('#applicantWrapper'+prevBlockId).after(formContent);  
            localStorageService.set('applicantIndex', prevBlockId);
            
            $http.get('/app/admin/bet/views/applicant-fields.html').
            success(function(data) {                
               AngularHelper.Compile($('#applicantWrapper'+betCtrl.data.applicantCnt), data, 'admin');                
            });
        }
        
        function removeLastApplicant(betCtrl) {
          $('#applicantWrapper'+betCtrl.data.applicantCnt).remove();
        }
        
        function saveApplicants(betCtrl) {
          $http({
              method: 'POST',
              url: betService.apiUrl+'/applicant/save',
              headers: {'Content-Type': 'application/x-www-form-urlencoded'},              
              data: betCtrl.data
          }).then(function (response) {
            if (response.data.status) {
              layoutService.changePage('bet-select');
            }
          });
        }
        
        function initSelectBet(betCtrl) {
          $http.get(betService.apiUrl+'/applicant/list-new').
            success(function(response) {
              if (response.status) {
                betCtrl.data.applicants = response.applicants;
                
                angular.forEach(betCtrl.data.applicants, function(elem) {
                  elem.randomDigit = betService.getRandomArbitrary(100, 10000);
                });
                
                betCtrl.data.maxSelections = 1;
                
                if (betCtrl.data.applicants.length > 10) {
                  betCtrl.data.maxSelections = 3;
                } else if (betCtrl.data.applicants.length > 5) {
                  betCtrl.data.maxSelections = 2;
                }
                
                betCtrl.data.selectedApplicants = [];
              }
            });
        }
        
        function getRandomArbitrary(min, max) {
            return Math.round(Math.random() * (max - min) + min);
        }
        
        function selectBet(betCtrl, applicantId) {          
          if (betCtrl.data.selectedApplicants.length === betCtrl.data.maxSelections){
            return;
          }
          
          betCtrl.data.selectedApplicants.push(applicantId);
          $('#applicantBtn'+applicantId).addClass('applicantBtnSelected');
          $('#applicantInfo'+applicantId).show();
        }
        
        function saveSelectedApplicants(betCtrl) {
          if (betCtrl.data.selectedApplicants.length !== betCtrl.data.maxSelections){
            alert('Сделайте еще выбор!');
            return;
          }
          
          $http({
              method: 'POST',
              url: betService.apiUrl+'/applicant/save-selected',
              headers: {'Content-Type': 'application/x-www-form-urlencoded'},              
              data: betCtrl.data.selectedApplicants
          }).then(function (response) {
            if (response.data.status) {
              layoutService.changePage('add-applicant');
            } else {
              alert(response.data.msg);
            }
          });
        }
        
        function cancelLastSelected(betCtrl) {
          var applicantId = betCtrl.data.selectedApplicants.pop();
          $('#applicantInfo'+applicantId).hide();
          $('#applicantBtn'+applicantId).removeClass('applicantBtnSelected');
        }
        
        function initAddResults(betCtrl) {
          $http.get(betService.apiUrl+'/applicant/list-wait-result').
            success(function(response) {
              if (response.status) {
                betCtrl.data.applicantsForResult = response.applicants;
                betCtrl.data.resultBet = [];
                betCtrl.data.resultApplicantId = [];
                
                angular.forEach(betCtrl.data.applicantsForResult, function(elem) {
                  betCtrl.data.resultBet.push(elem.bet);
                  betCtrl.data.resultApplicantId.push(elem.id);
                });
              }
            });
        }
        
        function saveResults(betCtrl) { 
          var results = [];
          
          angular.forEach(betCtrl.data.resultScore, function(elem, key) {
              var item = {};
              
              if (elem) {
                item.result = elem;
                
                if (betCtrl.data.resultApplicantId[key]) {
                  item.applicantId = betCtrl.data.resultApplicantId[key];
                }
                
                if (betCtrl.data.resultBet[key]) {
                  item.bet = betCtrl.data.resultBet[key];
                }
                
                results.push(item);
              }
            });
            
            $http({
              method: 'POST',
              url: betService.apiUrl+'/applicant/save-results',
              headers: {'Content-Type': 'application/x-www-form-urlencoded'},              
              data: results
          }).then(function (response) {
            if (response.data.status) {
              layoutService.changePage('dashboard');
            } else {
              alert(response.data.msg);
            }
          });
        }
        
        function getBets(betCtrl) {
          var url = betService.apiUrl+'/bet/list/';
          
          if (betCtrl.data.date) {
            url += betCtrl.data.date;
          } else {
            url += '0';
          }
          
          $http.get(url).
            success(function(response) {
              if (response.status) {
                betCtrl.data.groupFirstBets = response.applicants.groupFirstBets;
                betCtrl.data.groupSecondBets = response.applicants.groupSecondBets;
                betCtrl.data.groupThirdBets = response.applicants.groupThirdBets;
                
                if (betCtrl.data.groupFirstBets.length) {
                  betCtrl.data.groupFirstBets.dataExist = true;
                }
                
                if (betCtrl.data.groupSecondBets.length) {
                  betCtrl.data.groupSecondBets.dataExist = true;
                }
                
                if (betCtrl.data.groupThirdBets.length) {
                  betCtrl.data.groupThirdBets.dataExist = true;
                }      
                
                if (!betCtrl.data.date) {
                  betCtrl.data.date = response.applicants.currentDate;
                }
              }
            });
        }
    }   
})();