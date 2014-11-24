angular.module('login', [
  'ui.router',
  
])
.config(
  [          '$stateProvider', '$urlRouterProvider',
    function ($stateProvider,   $urlRouterProvider) {
      $stateProvider
       
        .state('/', {

          url: '/',

          //templateUrl: 'app/usuarios/login.html',

          controller: 'LoginCtrl',

        })

    }
  ]
);

