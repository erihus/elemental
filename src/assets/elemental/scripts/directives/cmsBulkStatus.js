'use strict';

/**
 * @ngdoc directive
 * @name elementalApp.directive:cmsBulkStatus
 * @description
 * # cmsBulkStatus
 */
angular.module('elementalApp').directive('cmsBulkStatus',[ 'Collection', 'Element', function (Collection, Element) {
    return {
        restrict: 'EA',
        scope: {
            owner: '='
        },
        link: function(scope, element, attrs) {
            //init semantic checkbox and handle toggle
            element.checkbox({
                fireOnInit: true,
                onChange: function() {
                    var input = this[0];                    
                    _.each(scope.owner.children, function(item){
                        item.status = (input.checked) ? 'published' : 'draft';
                        var service;

                       
                        if(item.component.prototype == 'collection') {
                            service  = Collection;
                        } else {
                            service = Element;
                        }

                        service.update({slug: item.slug}, {status: item.status}, function(res) {
                            item.errors = null;
                        }, 
                        function(err) {
                            item.errors = err.data.errors[0];
                        });
                        
                    });                    
                }
            });

        }
    };
}]);
