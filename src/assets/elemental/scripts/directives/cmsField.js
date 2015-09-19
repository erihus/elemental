'use strict';

/**
 * @ngdoc directive
 * @name elementalApp.directive:cmsField
 * @description
 * # cmsField
 */
angular.module('elementalApp').directive('cmsField', [ 'Collection', 'Element', function (Collection, Element) {
    return {
        restrict: 'EA',
        scope: {
            owner: '=',
            fieldtype: '=',
            fieldname: '=',
            form: '='
        },
        controller: function($scope, $route, $compile) {
            $scope.getOptionData = function(route) {

                var serviceName,
                    service,
                    pieces = route.split('/'),
                    query;

                 //determine service type
                serviceName = pieces[1]; //.charAt(0).toUpperCase() + pieces[1].slice(1, -1);
                if(serviceName == 'element') {
                    service = Element;
                } else {
                    service = Collection;
                }

                //determine query type and build query
                if(pieces[4] == 'children') {
                    return service.children({lookup: pieces[2], type: pieces[3], isArray:true});
                }

                if(pieces[2] == 'type') {
                    return service.getByType({type: pieces[3], isArray: true});
                }

                if(pieces[2] == 'slug') {
                    return service.getBySlug({slug: pieces[3], isArray: true});
                }
            }

            $scope.multiSelectConfig =  {
                create: false,
                delimiter: ',',
                valueField: 'value',
                labelField:  'name',
                placeholder: '',
                render: {
                    item : function(data, escape) {
                        return '<a class="ui blue tag label">'+data.name+'</a>';
                    }                    
                },
             
                onInitialize: function() {
                    $scope.loadOptions();
                }
            },

            $scope.datePickerConfig = {
                container: '.picker-drawer'
            }

            $scope.$on('TextInputChange', function(event, inputKey, inputValue){ //handle url slug generator fields
                if($scope.owner.component.slug_generator) {
                    if(inputKey == $scope.owner.component.slug_generator['url_slug']) {
                        angular.forEach($scope.owner.attributes, function(attr) {
                            if(attr.key == 'url_slug') {
                                 attr.value = inputValue.replace(/[^\w ]+/g,'').replace(/ +/g,'-').toLowerCase();
                            }
                        });
                    }
                }
            });

            $scope.loadOptions = function() {
                // console.log('loadOptions for');
                // console.log($scope.owner);
                // console.log($scope.fieldname);
                // debugger;

                //$scope.typeKey = $scope.attribute.key;
                if($scope.owner.component.options[$scope.fieldname]){
                    var setOption = $scope.attribute.value;
                    $scope.defaultOption = 'Select '+$scope.label;
                    $scope.options = $scope.owner.component.options[$scope.fieldname];

                    //console.log($scope.options);

                    if(!Array.isArray($scope.options)) {

                        var data = $scope.getOptionData($scope.options);
                        var options = [];



                        data.$promise.then(function(res){

                            angular.forEach(res, function(item) {
                                var option = {};
                                var slugAttr = _.findWhere(item.attributes, {key: 'url_slug'});
                                var titleAttr = _.findWhere(item.attributes, {key: 'display_title'});

                                // console.log(item);
                                // console.log(slugAttr);
                                // console.log(titleAttr);
                               
                                option.name = (typeof titleAttr !== 'undefined') ? titleAttr.value : item.nickname;
                                option.value = (typeof slugAttr !== 'undefined') ? slugAttr.value : item.slug;


                                //console.log(option);
                                
                                this.push(option);

                                if(option.value == setOption) {
                                    $scope.defaultOption = option.name;
                                }
                            }, options);
                            //$scope.$emit('optionsLoaded', options);
                            //console.log($scope.options);
                            $scope.options = options;
                        });
                    
                    } else if (!$scope.options[0].name) { // handle an array of remote fetchers
                        var parents = $scope.options;
                        $scope.options = [];
                        angular.forEach(parents, function(opt){
                            var data = $scope.getOptionData(opt);
                            data.$promise.then(function(res){
                                angular.forEach(res, function(item) {
                                    var option = {};
                                    var slugAttr = _.findWhere(item.attributes, {key: 'url_slug'});
                                    var titleAttr = _.findWhere(item.attributes, {key: 'display_title'});
                                    // console.log(item);
                                    option.name = titleAttr.value || item.nickname;
                                    option.value = slugAttr.value || item.slug;
                                    this.push(option);

                                    if(option.value == setOption) {
                                        $scope.defaultOption = option.name;
                                    }

                                 // console.log(this);            
                                }, $scope.options);
                            });

                        });
                    } else {
                        $scope.options = $scope.owner.component.options[$scope.fieldname];

                        angular.forEach($scope.options, function(option) {
                            if(option.value == setOption) {
                                $scope.defaultOption = option.name;
                            }
                        });
                    }

                } 
            } 
        },
        link: function (scope, $http){


           // scope.typeKey = scope.attribute.key; 
           // scope.fieldType = scope.field;//scope.owner.component.fields[scope.typeKey]; 
            scope.label = (scope.fieldset == 'batch') ? scope.owner.component.batchCreateLabels[scope.fieldname] : scope.owner.component.labels[scope.fieldname];
            scope.attribute = null;

            var field = _.findWhere(scope.owner.attributes, {key: scope.fieldname});
            
            if(!field) {
                var newAttr = {key: scope.fieldname, value: ''};
                scope.owner.attributes.push(newAttr);
            }

            scope.attribute = field || _.findWhere(scope.owner.attributes, {key: scope.fieldname});

            // console.log(scope.owner.attributes);
            // console.log(scope.attribute);
            // debugger;

            if(scope.fieldtype == 'multiselect') {
                scope.attribute.value = scope.attribute.value.split(',');
            }

            // console.log(scope.fieldtype);
            if(scope.fieldtype == 'select' || scope.fieldtype == 'radio') {
                scope.loadOptions();
            }
            
          
            scope.rules = scope.owner.component.rules[scope.fieldname];
         
            scope.required = '';
            if(scope.rules) {
                scope.required = (scope.rules.indexOf('required') > -1) ? 'required' : '';
            }


            if(scope.owner.component.slug_generator) {
            
                scope.slug_generator = scope.owner.component.slug_generator[scope.fieldname];

                if(scope.slug_generator) {
                    angular.forEach(scope.owner.attributes, function(attr) {
                        if(attr.key == scope.slug_generator) {
                             scope.attribute.value = attr.value.replace(/[^\w ]+/g,'').replace(/ +/g,'-').toLowerCase();
                        }
                    });
                }
            }
            

            scope.getTemplateUrl = function() {
                var fieldTemplate;
                switch(scope.fieldtype) {
                    case 'textarea':
                    case 'text':
                    case 'readonly':
                    case 'wysiwyg':
                    case 'image':
                    case 'directory':
                    case 'select':
                    case 'multiselect':
                    case 'radio':
                    case 'date':
                        fieldTemplate = scope.fieldtype
                        break;
                    default:
                        fieldTemplate = null;
                    break;

                }

                if(fieldTemplate != null) {
                    return '/js/elemental/views/snippets/controls/'+fieldTemplate+'_input.html';
                } else {
                    return;
                }
                
            };

            scope.getRequired = function() {
                return scope.required;
            }


        },
        template: '<div class="field" ng-class="getRequired()" ng-include="getTemplateUrl()"></div>'
    };
}]);

