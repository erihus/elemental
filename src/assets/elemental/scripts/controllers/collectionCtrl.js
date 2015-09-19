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

            if(res.component.batchCreate) {
                $scope.showBatchCreate = true;
                $scope.collection.bulkAddOk = false;
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

    $scope.base_name = function(str) {
       var base = new String(str).substring(str.lastIndexOf('/') + 1); 
        if(base.lastIndexOf(".") != -1)       
            base = base.substring(0, base.lastIndexOf("."));
       return base;
        
    };

    $scope.loadComponent =  function(prototype, type) {
        return Component.get({prototype: prototype, type: type});
    };

    $scope.bulkCreate = function(collection){
        var dirField;
        var newChildCount = 0;
        var nicknamePattern = _.findWhere(collection.attributes, {key: 'nickname_pattern'});
        var prototype = collection.component.attachablePrototype;
        var type = collection.component.attachableComponent;
        var newComponent = $scope.loadComponent(prototype, type);
        //console.log(nicknamePattern);
        _.each(collection.component.batchCreateFields, function(type, field){
            if(type == 'directory') {
                dirField = field;
            }
        });

        newComponent.$promise.then(function(component){

            _.each(collection.newChildren, function(child){
                newChildCount++;
                var childCount = collection.children.length;
                var filename = $scope.base_name(child[dirField].filename); 
                var nickname = nicknamePattern.value.replace(/#/g, childCount+newChildCount).replace(/\[filename\]/, filename);
                var slug = nickname.replace(/\s+/g, '_').toLowerCase();
                var service;
                var newChild = {};

                

                //populate object for saving
                newChild.parent = collection.slug;
                newChild.nickname = nickname;
                newChild.slug = slug;
                newChild.type = type;

                if(prototype == 'collection') {
                    newChild.addable = collection.component.attachableAddable;
                    newChild.reorderable = collection.component.attachableReorderable;
                }

                newChild.attributes = {};
                angular.forEach(component.fields, function(value, key){
                    var attrVal = '';
                    var attachableKey = 'attachable_'+key;
                    this.attributes[key]  = '';
                     //pull matching attributes down from collection (eg, for attaching images to a gallery, all with the same dimensions)
                    for(var i=0; i<collection.attributes.length; i++) {
                        if(attachableKey == collection.attributes[i].key) {
                            this.attributes[key] = collection.attributes[i].value;
                        }
                    }

                    if(key == dirField) {
                        this.attributes[key] = child[dirField].path
                    }

                    this.attributes.media_type = 'image' // hardcoding this for now


                }, newChild);        

                //determine api endpoint
                if(prototype == 'collection') {
                    service  = Collection;
                } else {
                    service = Element;
                }

                //save it!
                service.save(newChild, function(child){
                    collection.addError = false;
                    collection.children.push(child[0]);
                    collection.bulkAddOk = true;
                }, function(err){
                    collection.addError = true;
                    collection.bulkAddOk = false;                        
                });
            });
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