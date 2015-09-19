'use strict';

/**
 * @ngdoc directive
 * @name elementalApp.directive:cmsBulkAdd
 * @description
 * # cmsBulkAdd
 */
angular.module('elementalApp').directive('cmsBulkAdd', ['Component', 'Collection', 'Element', function (Component, Collection, Element) {
    return {
        restrict: 'EA',           
        controller:  function($scope, $cookies) {

            $scope.getXSRF = function() {
              var token = $cookies.get('XSRF-TOKEN');
              return { 'X-XSRF-TOKEN': token };
            }
        },
        scope: {
            attribute: '=',
            model: '=',
            fieldname: '='
        },
        link: function(scope, element) {

            //init fileflow
            var count = 0;
            var fileFlow = scope.model.flow;
            fileFlow.opts.headers = scope.getXSRF();
            scope.model.newChildren = {};
            
            //setup new object to create children from
            var dirField;
            _.each(scope.model.component.batchCreateFields, function(type, field){
                if(type == 'directory') {
                    dirField = field;
                }
            });
            
            
            fileFlow.on('fileSuccess', function(file, message, chunk) {
                var msg = JSON.parse(message);
                count++;
                scope.model.newChildren[count] = {};
                // _.each(dirFields, function(field){
                //     scope.model.newChildren[count][field] = [];    
                // });
                scope.model.newChildren[count][dirField] = [];
                scope.model.newChildren[count][dirField] = {path: msg.path, filename: file.name};
                //console.log(scope.model);
                delete scope.model.flow;
            });

        }



    };
}]);

