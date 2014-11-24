angular.module('bidef.webmail', [
  'ui.router',
  'bidef.webmail.services'
])
.config(
  [          '$stateProvider', '$urlRouterProvider',
    function ($stateProvider,   $urlRouterProvider) {
      console.log('cargo');
      $stateProvider
        .state('webmail', {

          url: '/webmail',

          views: {

            '': {
              templateUrl: 'views/webmail/mailbox.html',
               controller: 'WebmailCtrl'
            },

            'mensajes@webmail': {
              templateUrl: 'views/webmail/mensajes.html',
             
            },
            'bandejas@webmail': {
              templateUrl: 'views/webmail/bandejas.html',
            },

          }

        })

        .state('webmail.bandeja', {

          url: '/:bandejaId?pagina',

         views: {

            'mensajes@webmail': {
              templateUrl: 'views/webmail/mensajes.html',
              controller: 'WebmailBandejaCtrl'
            },

          }

        })

        .state('webmail.mensajes',{

          url: '/webmail',

          templateUrl: "views/webmail/mensajes.html",

        })
        ;
    }
  ]
);
