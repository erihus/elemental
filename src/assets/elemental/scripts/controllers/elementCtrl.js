'use strict';

/**
 * @ngdoc function
 * @name elementalApp.controller:ElementCtrl
 * @description
 * # ElementCtrl
 * Controller of the elementalApp
 */
angular.module('elementalApp').controller('ElementCtrl', ['$scope', '$route', '$q', 'Element',  function($scope, $route, $q, Element){
        
   
  
    var slug = $route.current.params.slug;  
    $scope.element = {};
    $q(function(resolve, reject) {
        Element.edit({slug: slug}, function(res){
            $scope.element = res;
            $scope.element.errors = null;
            resolve($scope.element);
            $('.async-loader').fadeOut().siblings('.container').removeClass('hidden');
        });
    });
    
    $scope.updateElement = function(element) {
        $scope.element = element;
        $scope.element.errors = null;
        Element.update({slug: element.slug}, element, function(res) {
            $scope.element.ok = true;
            $scope.element.errors = null;
        }, 
        function(err) {
            $scope.element.errors = err.data.errors[0];
            $scope.element.ok = false;
        });
    };
}]);