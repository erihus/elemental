'use strict';

/**
 * @ngdoc service
 * @name elementalApp.Component
 * @description
 * # Component
 * Factory in the elementalApp.
 */
angular.module('elementalApp').factory('Component', function ($resource) {
  return $resource('api/component/:prototype/:type');
});
