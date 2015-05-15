'use strict';

/**
 * @ngdoc directive
 * @name elementalApp.directive:cmsSidebar
 * @description
 * # cmsSidebar
 */
angular.module('elementalApp').directive('cmsSidebar', ['Collection', function (Collection) {
    return {
        restrict: 'EA',
        link: function(scope, element) {
            function hideSidebar() {
                element.sidebar('hide');
            }

            element.sidebar('attach events', '.sidebar-toggle');

            scope.$on('$routeChangeStart', function() {
               hideSidebar();
            });

            scope.$on('$routeChangeSuccess', hideSidebar());
            scope.$on('$routeChangeError', hideSidebar());


            //uncomment this after you have added pages
            // follow similar pattern for adding other resource types to sidebar
            // scope.pages = Collection.query({type: 'page', isArray:true});            
        },

        templateUrl: 'js/elemental/views/snippets/sidebar.html'

    };
}]);

