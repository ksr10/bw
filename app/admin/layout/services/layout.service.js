(function() {
    'use strict';
    
    angular
        .module('admin.layout')
        .service('layoutService', layoutService);
        
    layoutService.$inject = ['$http', 'localStorageService', '$state'];
        
    function layoutService($http, localStorageService, $state) {
        var layout = {
          
          initLayout: initLayout,
          changePage: changePage
        };
        
        return layout;
        
        function initLayout() {
          layout.changePage('dashboard');
        }
        
        function changePage(stateName, params) {
            if (params) {
                $state.go(stateName, params);
            } else {
                $state.go(stateName);
            }            
        }
    }   
})();