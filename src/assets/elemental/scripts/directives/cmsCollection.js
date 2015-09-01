'use strict';

/**
 * @ngdoc directive
 * @name elementalApp.directive:cmsCollection
 * @description
 * # cmsCollection
 */
angular.module('elementalApp').directive('cmsCollection', function () {
    return {
        restrict: 'E',
        controller: 'CollectionCtrl',
        templateUrl: 'js/elemental/views/snippets/collection_edit.html',
        scope: {
            collection: '=',
        }
        // link: function(scope, element) {
        //     scope.fetchCollection(scope.slug);
        // }       
    };
});

