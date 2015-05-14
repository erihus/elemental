'use strict';

/**
 * @ngdoc directive
 * @name elementalApp.directive:passwordConfirm
 * @description
 * # passwordConfirm
 */
angular.module('elementalApp').directive('passwordConfirm', function () {
    return {
        restrict: 'A', // only activate on element attribute
        require: '?ngModel', // get a hold of NgModelController
        link: function(scope, elem, attrs, ngModel) {
            if(!ngModel) return; // do nothing if no ng-model
           
            // watch own value and re-validate on change
            scope.$watch(attrs.ngModel, function() {
              validate();
            }); 
            // observe the other value and re-validate on change
            attrs.$observe('passwordConfirm', function (val) {
              validate();
            });

            var validate = function() {
                // values
                var val1 = ngModel.$viewValue;
                var val2 = attrs.passwordConfirm;
                // set validity
                ngModel.$setValidity('passwordConfirm', ! val1 || ! val2 || val1 === val2);
            };
        }
    }
});
