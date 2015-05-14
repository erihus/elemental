'use strict';

/**
 * @ngdoc directive
 * @name elementalApp.directive:cmsCollapsable
 * @description
 * # cmsCollapsable
 */
angular.module('elementalApp').directive('cmsCollapsable', function () {
    return {
        restrict: 'EA',
        link: function(scope, element) {
            var toggle = element.find('.collapse_toggle'),
                collapser = element.find('.collapser');

            toggle.on('click', function(){
                if(collapser.hasClass('expanded')){
                    collapser.slideUp(function(){
                        $(this).removeClass('expanded').addClass('collapsed');
                    });
                    toggle.removeClass('compress').addClass('expand');
                } else {
                    collapser.slideDown().removeClass('collapsed').addClass('expanded');
                    toggle.removeClass('expand').addClass('compress');
                }
            });
        }

      

    };
});

