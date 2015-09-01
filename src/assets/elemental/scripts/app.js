'use strict';

/**
 * @ngdoc overview
 * @name elementalAppApp
 * @description
 * # elementalAppApp
 *
 * Main module of the application.
 */
angular.module('elementalApp', [
    'ngAnimate',
    'ngCookies',
    'ngResource',
    'ngRoute',
    'ngSanitize',
    'ngTouch',
    'ui.sortable',
    'angular-redactor',
    'selectize',
    '720kb.datepicker',
    'flow'
]).config(function ($routeProvider) {
    $routeProvider
      .when('/', {
        controller: 'DashCtrl',
        templateUrl: '/js/elemental/views/dash.html',
      })
      .when('/users', {
        controller: 'UserCtrl',
        templateUrl: '/js/elemental/views/users.html'
      })
      .when('/users/add', {
        controller: "UserCtrl",
        method: 'add',
        templateUrl: '/js/elemental/views/snippets/user_add.html'
      })
      .when('/users/:id/edit', {
        controller: "UserCtrl",
        method: 'edit',
        templateUrl: '/js/elemental/views/snippets/user_edit.html'
      })
      .when('/collection/:slug/edit', {
        controller: 'CollectionCtrl',
        templateUrl: '/js/elemental/views/snippets/collection_edit.html',
      })
      .when('/element/:slug/edit', {
        controller: 'ElementCtrl',
        templateUrl: '/js/elemental/views/snippets/element_edit.html',
      });
      
}).config( function(redactorOptions) {
   
    var $cookies;
    angular.injector(['ngCookies']).invoke(function(_$cookies_) {
        $cookies = _$cookies_;
    });

    redactorOptions.focus = true;
    redactorOptions.replaceDivs = false;
    redactorOptions.definedLinks = 'api/page-list';
    redactorOptions.imageManagerJson = 'api/file/list';
    redactorOptions.imageUpload = 'api/file/redactor-upload';
    redactorOptions.uploadHeaders = { 'X-XSRF-TOKEN': $cookies.get('XSRF-TOKEN') };
    redactorOptions.plugins = ['video', 'definedlinks', 'table', 'imagemanager', 'fullscreen'];
});  