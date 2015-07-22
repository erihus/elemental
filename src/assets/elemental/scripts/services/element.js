'use strict';

/**
 * @ngdoc service
 * @name elementalApp.Collection
 * @description
 * # Collection
 * Factory in the elementalApp.
 */
angular.module('elementalApp').factory('Element', function ($resource) {
  return $resource('api/element', null,
      {
      	'getByType' : {method: 'GET', url: 'api/element/type/:type', isArray: true},
      	'getBySlug' : {method: 'GET', url: 'api/element/slug/:slug', isArray: true},
        'children' : {method: 'GET', url: 'api/element/type/:type/children', isArray: true},
        'edit' : {method: 'GET', url: 'api/element/:slug/edit'},
        'update': { method:'PUT', url: 'api/element/:slug' },
        'delete': { method: 'DELETE', url: 'api/element/:slug'}
      }); 
});
