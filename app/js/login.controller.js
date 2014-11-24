'use strict';

/**
 * @ngdoc function
 * @name login.controller:LoginCtrl
 * @description
 * # MainCtrl
 * Controller of the login
 */
angular.module('login')
  .controller('LoginCtrl', function ($scope, $http, $state, $stateParams,authUsers) {

    $scope.login = { email : "", password : "" }

    authUsers.flash = "";
    //función que llamamos al hacer sumbit al formulario
    $scope.login = function(){

        authUsers.login($scope.login);

    }

    $scope.logout = function(){
        
        authUsers.logout();
    }

  });
