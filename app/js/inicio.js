angular.module('bidef.inicio', [
  'ui.router',
  /*'ui-highcharts',*/
  'bidef.inicio.service',
])
.config(
  [          '$stateProvider', '$urlRouterProvider',
    function ($stateProvider,   $urlRouterProvider) {
      console.log('entra inicio');
      $stateProvider
        .state('inicio', {

          url: '/inicio',

          views: {

            '': {
              templateUrl: 'views/inicio/index.html',
               controller: 'InicioCtrl'
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