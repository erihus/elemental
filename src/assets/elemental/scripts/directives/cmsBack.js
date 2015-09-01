'use strict';

/**
 * @ngdoc directive
 * @name elementalApp.directive:cmsBack
 * @description
 * # cmsBack
 */
angular.module('elementalApp').directive('cmsBack', ['$location', function ($location) {
    return {
      restrict: 'EA',

      link: function(scope, element, attrs) {
        var _el = element;
        element.bind('click', goBack);

        function goBack() {
          history.back();
          scope.$apply();
        }
      }
    }
}]);