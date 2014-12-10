angular.module('bidef.delegation', [
  'ui.router',
  'ui-highcharts',
  'bidef.delegation.service',
])
.config(
  [          '$stateProvider', '$urlRouterProvider',
    function ($stateProvider,   $urlRouterProvider) {
      console.log('entra delegation');
      $stateProvider
        .state('delegation', {

          url: '/delegation',

          views: {

            '': {
              templateUrl: 'views/delegation/index.html',
               controller: 'DelegationCtrl'
            }

            /*'mensajes@webmail': {
              templateUrl: 'views/webmail/mensajes.html',
             
            },
            'bandejas@webmail': {
              templateUrl: 'views/webmail/bandejas.html',
            },*/

          }

        });
    }
  ]
);