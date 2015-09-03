'use strict';

/**
 * @ngdoc directive
 * @name elementalApp.directive:cmsThumb
 * @description
 * # cmsThumb
 */
angular.module('elementalApp').directive('cmsThumb', function () {
    return {
        restrict: 'E',
        scope: {
        	content: '=',
          thumbfield: '='
        },
        transclude: true,
        controller: function($scope) {
            var thumbAttr = _.findWhere($scope.content.attributes, {key: $scope.thumbfield});
            $scope.thumb_src = thumbAttr.value;

          	if(!$scope.thumb_src.length) {
          		$scope.thumb_src = '/js/elemental/ui/img/placeholder.png';
          	}
        },
        template: '<img class="thumb" ng-src="{{thumb_src}}" />'

    };
});
