'use strict';

/**
 * @ngdoc directive
 * @name elementalApp.directive:cmsCheckbox
 * @description
 * # cmsCheckbox
 */
angular.module('elementalApp').directive('cmsCheckbox', function () {
    return {
        restrict: 'EA',
         scope: {
            owner: '='
        },
        link: function(scope, element) {
            element.checkbox({
                onChange: function() {
                    var val = $(this).val();
                    scope.owner.value = val;
                    scope.$apply();
                }
            });
        }
    };
});

