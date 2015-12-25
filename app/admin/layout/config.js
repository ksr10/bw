(function() {    
    'use strict';
    
    angular
        .module('admin.layout')
        .config(layoutConfig);
        
    function layoutConfig($stateProvider, $urlRouterProvider, $sceDelegateProvider) {
        $sceDelegateProvider.resourceUrlWhitelist(['**']);
        
        $stateProvider.state('dashboard', {
            url: '/dashboard',
            templateUrl: '/app/admin/layout/dashboard.html'                      
        })        
    }
})(); 

