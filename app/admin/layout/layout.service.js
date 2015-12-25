(function() {
    'use strict';
    
    angular
        .module('admin.layout')
        .service('layoutService', layoutService);
        
    layoutService.$inject = ['$http', 'localStorageService', '$state'];
        
    function layoutService($http, localStorageService, $state) {
        
    }   
})();