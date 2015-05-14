'use strict';

/**
 * @ngdoc service
 * @name elementalApp.User
 * @description
 * # User
 * Factory in the elementalApp.
 */
angular.module('elementalApp').factory('User', function ($resource) {
  return $resource('api/user', null,
      {
        'all'  : {method: 'GET', url: 'api/user', isArray: true},
        'edit' : {method: 'GET', url: 'api/user/:id/edit'},
        'update': { method:'PUT', url: 'api/user/:id' },
        'delete': { method: 'DELETE', url: 'api/user/:id'}
      }); 
});
