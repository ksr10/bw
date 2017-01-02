(function() {
    'use strict';

    angular
        .module('admin.bet')
        .controller('ApplicantController', ApplicantController);
        
    ApplicantController.$inject = ['$scope', 'localStorageService'];

    function ApplicantController($scope, localStorageService) {
        var applicant = this;
        
        applicant.index = 0;
        
        applicant.initApplicant = initApplicant;
        
        applicant.initApplicant();
        
        function initApplicant() {
          applicant.index = +localStorageService.get('applicantIndex');         
          localStorageService.remove('applicantIndex');
        }
    }

})();