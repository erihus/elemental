'use strict';

/**
 * @ngdoc directive
 * @name elementalApp.directive:cmsStatus
 * @description
 * # cmsStatus
 */
angular.module('elementalApp').directive('cmsStatus', function () {
    return {
        restrict: 'EA',
        scope: {
            owner: '='
        }, 
        link: function(scope, element, attrs) {
            element.checkbox({
                fireOnInit: false,
                onChange: function() {
                    scope.owner.status = (this[0].checked) ? 'published' : 'draft';
                    scope.$emit('statusChange', scope.owner.status);
                }
            });

        }
    };
});
