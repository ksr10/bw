(function() {
    'use strict';

    angular
        .module('admin.layout')
        .controller('LayoutController', LayoutController);
        
    LayoutController.$inject = ['$scope', 'layoutService'];

    function LayoutController($scope, layoutService) {
        
        layoutService.initLayout();
    }

})();