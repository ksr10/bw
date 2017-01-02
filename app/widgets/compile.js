var AngularHelper = (function () {
    var AngularHelper = function () { };

    /**
     * ApplicationName : Default application name for the helper
     */
    var defaultApplicationName = "app";

    /**
     * Compile : Compile html with the rootScope of an application
     *  and replace the content of a target element with the compiled html
     * @$targetDom : The dom in which the compiled html should be placed
     * @htmlToCompile : The html to compile using angular
     * @applicationName : (Optionnal) The name of the application (use the default one if empty)
     */
    AngularHelper.Compile = function ($targetDom, htmlToCompile, applicationName) {
        var $injector = angular.injector(["ng", applicationName || defaultApplicationName]);

        $injector.invoke(["$compile", "$rootScope", function ($compile, $rootScope) {
            //Get the scope of the target, use the rootScope if it does not exists
            var $scope = $targetDom.html(htmlToCompile).scope();
            $compile($targetDom)($scope || $rootScope);
            $rootScope.$digest();
        }]);
    }

    return AngularHelper;
})();