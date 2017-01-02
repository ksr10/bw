(function() {    
    'use strict';
    
    angular
        .module('admin.layout')
        .config(layoutConfig);
        
    function layoutConfig($stateProvider, $urlRouterProvider, $sceDelegateProvider) {
        $sceDelegateProvider.resourceUrlWhitelist(['**']);
        
        $stateProvider.state('dashboard', {
            url: '/dashboard',
            templateUrl: '/app/admin/layout/views/dashboard.html'                      
        })        
        .state('add-applicant', {
            url: '/add-applicant',
            templateUrl: '/app/admin/bet/views/add-applicant.html'                      
        })
        .state('bet-select', {
            url: '/bet-select',
            templateUrl: '/app/admin/bet/views/bet-select.html'                      
        })
        .state('add-results', {
            url: '/add-results',
            templateUrl: '/app/admin/bet/views/add-results.html'                      
        })
        .state('bets', {
            url: '/bets',
            templateUrl: '/app/admin/bet/views/bets.html'                      
        });
    }
})(); 

