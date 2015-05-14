'use strict';

/**
 * @ngdoc function
 * @name elementalApp.controller:UserCtrl
 * @description
 * # UserCtrl
 * Controller of the elementalApp
 */
angular.module('elementalApp').controller('UserCtrl', ['$scope', '$route', 'User',  function($scope, $route, User){
    $scope.users = User.all();

    if($route.current.params.id) {
        $scope.user = User.edit({id: $route.current.params.id});
    }

    $scope.addUser = function(user) {
        User.save(user, function(res){
            $scope.user.ok = true;
            $scope.user.errors = null;
        }, function(err){
            $scope.user.errors = err.data.errors;
            $scope.user.ok = false;
        }); 
        
    };

    $scope.deleteUser = function(user) {
        $scope.users.errors = null;

        if(confirm('Are you sure you want to delete this user?')) {

            User.delete({id: user.id}, function(res) {
                var index = $scope.users.indexOf(user);
                $scope.users.splice(index, 1);
            }, 
            function(err) {
                $scope.users.errors = err.data.errors[0];
                $scope.users.ok = false;
            });
            
        }
    };

    $scope.updateUser = function(user){
        User.update({id: user.id}, user, function(res){
            $scope.user.ok = true;
            $scope.user.errors = null;
        }, function(err){
            $scope.user.errors = err.data.errors;
            $scope.user.ok = false;
        }); 
    };


}]);