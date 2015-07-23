'use strict';

/**
 * @ngdoc directive
 * @name elementalApp.directive:cmsImage
 * @description
 * # cmsImage
 */
angular.module('elementalApp').directive('cmsImage', ['$cookies', function () {
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
            model: '='
        },
        link: function(scope, element) {

            var field = scope.attribute.key,
                sizeStr = field.substr(0,3);

            if(sizeStr != 'med' && sizeStr != 'sml' && sizeStr != 'lrg' ) {
                var widthKey = 'width';
                var heightKey = 'height';
            } else {
                var widthKey = sizeStr+'width';
                var heightKey = sizeStr+'height';
            }

            scope.attribute.width = null;
            scope.attribute.height = null;

            angular.forEach(scope.model.attributes, function(attr){
                if(attr.key == widthKey) {
                    this.width= attr.value;
                } 

                if(attr.key == heightKey) {
                    this.height = attr.value;
                } 
                
            }, scope.attribute);

            //console.log(scope.attribute);            

            var fileFlow = scope.model.flow;
            fileFlow.opts.headers = scope.getXSRF();
            fileFlow.opts.singleFile = true;

            if(scope.attribute.width) {
                fileFlow.opts.query = {
                    width: scope.attribute.width
                };
            }

            if(scope.attribute.height) {
                fileFlow.opts.query = {
                    height: scope.attribute.height
                };
            }

            if(scope.model.type == 'ResponsiveImage' || scope.model.component.extendsFrom == 'ResponsiveImageComponent') {
                angular.forEach(scope.model.attributes, function(attr){
                    if(attr.key == 'med_width') {
                        fileFlow.opts.query['med_width'] = attr.value;
                    }

                    if(attr.key == 'med_height') {
                        fileFlow.opts.query['med_height'] = attr.value;
                    }

                    if(attr.key == 'sml_width') {
                        fileFlow.opts.query['sml_width'] = attr.value;
                    }

                     if(attr.key == 'sml_height') {
                        fileFlow.opts.query['sml_height'] = attr.value;
                    }

                });
            }

            fileFlow.on('fileSuccess', function(file, message, chunk) {
                var msg = JSON.parse(message);
                scope.attribute.value = msg.path;
                if(msg.med_path) {
                     angular.forEach(scope.model.attributes, function(attr){
                        if(attr.key == 'med_path') {
                            attr.value = msg.med_path;
                        }

                        if(attr.key == 'sml_path') {
                            attr.value = msg.sml_path;
                        }
                    });
                }
                
                delete scope.model.flow;
            });


           
         //          $('#cropper').cropper({
         //              file: file,
         //              bgColor: '#fff',
         //              aspectRatio: scope.width/scope.height,
         //              //minSize: [scope.width, scope.height],
         //              maxSize: [scope.width, scope.height],
         //              // selection: '90%',
         //              onSelect: function (coords){
         //                  element.fileapi('crop', file, coords);

         //                  console.log(file);
         //              }
         //          });
                  
         //          $('.cropper-modal').modal({closable: false}).modal('show');
         //          $('.upload-proceed').on('click', function(){
         //              element.fileapi('upload');
         //          });

                    
           
        }
    };
}]);

