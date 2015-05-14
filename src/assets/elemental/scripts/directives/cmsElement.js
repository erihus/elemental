'use strict';

/**
 * @ngdoc directive
 * @name elementalApp.directive:cmsElement
 * @description
 * # cmsElement
 */
angular.module('elementalApp').directive('cmsElement', function () {
    return {
        restrict: 'E',
        controller: 'CollectionCtrl',
        templateUrl: 'js/elemental/views/snippets/element_edit.html',
        scope: {
            element: '='
        }       
    };
});

