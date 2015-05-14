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

            scope.settings = Collection.query({type: 'settings', isArray:true});
            scope.menus = Collection.query({type: 'menu', isArray:true});
            scope.pages = Collection.query({type: 'page', isArray:true});
            scope.thank_you_pages = Collection.query({type: 'thank-you-page-list', isArray:true});
            scope.feature_list = Collection.query({type: 'feature-list', isArray:true});
            scope.resource_list = Collection.query({type: 'resource-list', isArray:true});
            scope.casestudy_list = Collection.query({type: 'case-study-list', isArray:true});
            scope.products = Collection.query({type: 'product_category', isArray:true});
            scope.blogs = Collection.query({type: 'blog', isArray:true});
            scope.tags = Collection.query({type: 'tags', isArray:true});
            scope.redirects = Collection.query({type: 'redirects', isArray:true});
            scope.tracking_codes = Collection.query({type: 'tracking-code-list', isArray:true});
        },

        templateUrl: 'js/elemental/views/snippets/sidebar.html'

    };
}]);

