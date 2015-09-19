'use strict';

/**
 * @ngdoc function
 * @name elementalApp.controller:CollectionCtrl
 * @description
 * # CollectionCtrl
 * Controller of the elementalApp
 */
angular.module('elementalApp').controller('CollectionCtrl', ['$scope', '$route', '$q', '$location', 'Collection', 'Element',  function($scope, $route, $q, $location, Collection, Element){
        
    $scope.showViewToggle = false;
    $scope.viewOptions = {
        options: [
          {val: 'list', name: 'List'},
          {val: 'thumbs', name: 'Thumbs'}
        ],
        selectedOption: {val: 'list', name: 'List'} 
    };

    if(!$scope.collection) {
        var slug = $route.current.params.slug;  
    }
    $scope.collection = {};
    $q(function(resolve, reject) {
        Collection.edit({slug: slug}, function(res){
            $scope.collection = res;
            $scope.collection.errors = null;
            resolve($scope.collection);

            if(res.component.childThumbView) {
                $scope.showViewToggle = true;
                $scope.viewOptions.selectedOption = {val:'thumbs', name: "Thumbs"};
            }

            $scope.allPub = true;
            $scope.checked = '';
            _.each($scope.collection.children, function(item){
                if(item.status == 'draft') {
                    $scope.allPub = false;
                }
            });

            if($scope.allPub) {        
                $scope.checked = 'checked';
            }

            $('.async-loader').fadeOut().siblings('.container').removeClass('hidden');
        });
    });
        
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


    $scope.reorder = function(type, id, order) {
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
                $scope.reorder(type, id, order);
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