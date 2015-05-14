'use strict';

/**
 * @ngdoc directive
 * @name elementalApp.directive:cmsSelect
 * @description
 * # cmsSelect
 */
angular.module('elementalApp').directive('cmsSelect', function () {
    return {
        restrict: 'EA',
        scope: {
            owner: '='
        },
        link: function(scope, element) {
            element.dropdown({
                onChange: function(value) {
                    scope.owner.value = value;
                }
            });
        }

      

    };
});

