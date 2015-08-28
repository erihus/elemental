'use strict';

/**
 * @ngdoc directive
 * @name elementalApp.directive:cmsPopup
 * @description
 * # cmsPopup
 */
angular.module('elementalApp').directive('cmsPopup', function () {
    return {
        restrict: 'EA',
        link: function(scope, element) {
            element.popup();
        }

    };
});
