(function() {    
    'use strict';
    
    angular
        .module('app.widgets')
        .directive('bwDatepicker', bwDatepicker);
        
    function bwDatepicker() {
        var directive = {
            link: link,
            restrict: 'A',
            require: 'ngModel',
            scope: {
                onChange: '&'           
            }
        };
        
        return directive;
        
        function link(scope, element, attrs, ngModel) {
            element.datepicker({
                dateFormat: 'yy-mm-dd',
                onClose: function(selectedDate) {                          
                    ngModel.$setViewValue(selectedDate);
                    ngModel.$render();
                    scope.$apply();
                }
            });
        }        
    }
    
})();