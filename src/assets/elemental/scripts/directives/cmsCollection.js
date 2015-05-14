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
            slug: '@slug',
        },
        link: function(scope, element) {
            var collection = scope.fetchCollection(scope.slug);
            collection.then(function(collection) {
                angular.forEach(scope.collection.component.fields, function(field) {
                    if(field != 'meta') {
                        scope.editableFields = true;
                    }
                });
            });
        }       
    };
});

