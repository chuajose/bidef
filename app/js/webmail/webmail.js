angular.module('bidef.webmail', [
  'ui.router',
  'ui.bootstrap',
  'bidef.webmail.services',
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

          url: '/:mailbox?pagina',

         views: {

            'mensajes@webmail': {
              templateUrl: 'views/webmail/mensajes.html',
              controller: 'WebmailCtrl'
            },

          }

        })
        .state('webmail.ver', {

          url: '/ver/:mailbox/:id',

         views: {

            'mensajes@webmail': {
              templateUrl: 'views/webmail/mail_detail.html',
              controller: 'WebmailMensajeCtrl'
            },

          }

        })

        .state('webmail.mensajes',{

          url: '/webmail',

          templateUrl: "views/webmail/mensajes.html",

        })

        .state('webmail.compose', {

          url: '/compose/:id',

         views: {

            'mensajes@webmail': {
              templateUrl: 'views/webmail/mail_compose.html',
              controller: 'WebmailComposeCtrl'
            },

          }

        })
        ;
    }
  ]
);
