(function() {
    'use strict';
    
    angular
        .module('admin.user')
        .service('userService', userService);
        
    userService.$inject = ['localStorageService', '$http', 'connectionService'];
        
    function userService(localStorageService, $http, connectionService) {
        
    }   
})();