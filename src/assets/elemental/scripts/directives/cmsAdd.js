'use strict';

/**
 * @ngdoc directive
 * @name elementalApp.directive:cmsAdd
 * @description
 * # cmsAdd
 */
angular.module('elementalApp').directive('cmsAdd', ['Component', 'Collection', 'Element', function (Component, Collection, Element) {
    return {
        restrict: 'EA',
        controller: 
            function($scope) {
                $scope.loadComponent = function(prototype, type) {
                    return Component.get({prototype: prototype, type: type});
                }
            },
        scope: {
            collection: '='
        },
        link: function(scope, element) {

            element.on('click', function(){
                var prototype = scope.collection.component.attachablePrototype;
                var type = scope.collection.component.attachableComponent;
                var emptyChild = {};                                 
                var newComponent = scope.loadComponent(prototype, type);
                var service;

                if(prototype == 'collection') {
                    service  = Collection;
                } else {
                    service = Element;
                }
                
                newComponent.$promise.then(function(data){

                    var nickname = prompt("Please provide a nickname (only used as an identifier in the CMS)");
                    var slug = nickname.replace(/\s+/g, '_').toLowerCase();

                    if(!nickname) {
                        alert('You must provide a nickname for the new item.')
                        return false;
                    }

                    emptyChild.parent = scope.collection.slug;
                    emptyChild.nickname = nickname;
                    emptyChild.slug = slug;
                    emptyChild.type = type;

                    if(prototype == 'collection') {
                        emptyChild.addable = scope.collection.component.attachableAddable;
                        emptyChild.reorderable = scope.collection.component.attachableReorderable;
                    }

                    emptyChild.attributes = {};                    

                    angular.forEach(newComponent.fields, function(value, key){
                        var attrVal = '';
                        var attachableKey = 'attachable_'+key;
                        this.attributes[key]  = '';
                         //pull matching attributes down from collection (eg, for attaching images to a gallery, all with the same dimensions)
                        for(var i=0; i<scope.collection.attributes.length; i++) {
                            if(attachableKey == scope.collection.attributes[i].key) {
                                this.attributes[key] = scope.collection.attributes[i].value;
                            }
                        }                       
                    }, emptyChild);
            
                    // console.log('creating new item in colleciton');
                    // console.log(emptyChild);

                    service.save(emptyChild, function(child){
                        scope.collection.addError = false;
                        scope.collection.children.push(child[0]);
                    }, function(err){
                        scope.collection.addError = true;                        
                    });

                    
                });
            
                                
                //TODO
                //handle generic component selection/ creation

            });

        }       
    };
}]);

