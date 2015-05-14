'use strict';

/**
 * @ngdoc function
 * @name elementalApp.controller:CollectionCtrl
 * @description
 * # CollectionCtrl
 * Controller of the elementalApp
 */
angular.module('elementalApp').controller('CollectionCtrl', ['$scope', '$route', '$q', 'Collection', 'Element',  function($scope, $route, $q, Collection, Element){
        
    
    $scope.fetchCollection = function(slug) {
        if(!slug) {
            slug = $route.current.params.slug;  
        }
        $scope.collection = {};
        return $q(function(resolve, reject) {
            Collection.edit({slug: slug}, function(res){
                $scope.collection = res;
                $scope.collection.errors = null;
                resolve($scope.collection);
            });
        });
        
        
        
    }   


    $scope.updateCollection = function(collection) {
        $scope.collection = collection;
        $scope.collection.ok = false;
        Collection.update({slug: collection.slug}, collection, function(res) {
            $scope.collection.ok = true;
            $scope.collection.errors = null;
        }, 
        function(err) {
            $scope.collection.errors = err.data.errors[0];
            $scope.collection.ok = false;
        
        });
    };


    $scope.deleteItem = function(model) {
        var service;
        $scope.collection.errors = null;

        if(confirm('Are you sure you want to delete this item?')) {

            if(model.component.prototype == 'element') {
                service = Element;
            } else if(model.component.prototype == 'collection') {
                service = Collection;
            }

            service.delete({slug: model.slug},function(res) {
                var index = $scope.collection.children.indexOf(model);
                $scope.collection.children.splice(index, 1);
            }, 
            function(err) {
                $scope.collection.errors = err.data.errors[0];
                $scope.collection.ok = false;
            
            });
            
        }
    };   

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
    

    $scope.reorderElement = function(type, id, order) {
        $scope.collection.reorderOK = null;
        Collection.update({slug: $scope.collection.slug}, {type: type, id:id, order: order}, function(res) {
            $scope.collection.reorderOk = true;
        }, 
        function(err) {
            $scope.collection.errors = err.data.errors[0];
            $scope.collection.reorderOk = false;
        });
        
    };

    $scope.dragControlListeners = {
        accept: function (sourceItemHandleScope, destSortableScope) {
            return sourceItemHandleScope.itemScope.sortableScope.$id === destSortableScope.$id;
        },
        orderChanged: function(event) {
             var listModels = event.dest.sortableScope.modelValue;

             angular.forEach(listModels, function(model) {
                var type = model.component.prototype;
                var id = model.id;
                var order =listModels.indexOf(model)+1;
                $scope.reorderElement(type, id, order);
            });

        }
    };

    $scope.$on('statusChange', function(event, data){
        event.stopPropagation();
        var model = event.targetScope.owner;
        var service;
        if(model.component.prototype == 'collection') {
            service  = Collection;
        } else {
            service = Element;
        }

        service.update({slug: model.slug}, {status: model.status}, function(res) {
            model.errors = null;
        }, 
        function(err) {
            model.errors = err.data.errors[0];
        });
        
    });


}]);