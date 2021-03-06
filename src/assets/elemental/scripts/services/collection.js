'use strict';

/**
 * @ngdoc service
 * @name elementalApp.Collection
 * @description
 * # Collection
 * Factory in the elementalApp.
 */
angular.module('elementalApp').factory('Collection', function ($resource) {
  return $resource('api/collection', null,
      {
      	'getByType' : {method: 'GET', url: 'api/collection/type/:type', isArray: true},
      	'getBySlug' : {method: 'GET', url: 'api/collection/slug/:slug', isArray: true},
        //'associated': {method: 'GET', url: 'collections/:parentSlug/:children/:childType', isArray:true},
        'children' : {method: 'GET', url: 'api/collection/:lookup/:type/children/', isArray:true},
        'edit' : {method: 'GET', url: 'api/collection/:slug/edit'},
        'update': { method:'PUT', url: 'api/collection/:slug' },
        'delete': { method: 'DELETE', url: 'api/collection/:slug'}
      }); 
});
