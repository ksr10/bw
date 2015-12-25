(function() {
    'use strict';

    angular
        .module('admin.user')
        .controller('UserController', UserController);
        
    UserController.$inject = ['$scope', 'layoutService', 'userService'];

    function UserController($scope, layoutService, userService) {
        
    }

})();