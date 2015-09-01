'use strict';

/**
 * @ngdoc directive
 * @name elementalApp.directive:cmsModal
 * @description
 * # cmsModal
 */
angular.module('elementalApp').directive('cmsModal', function () {
    return {
        restrict: 'EA',
        link: function(scope, element) {
            element.modal({observeChanges: true});
        }

    };
});
