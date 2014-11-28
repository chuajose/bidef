angular.module('bidef.crm', [
  'ui.router',
  'bidef.crm.services'
]).config(
  ['$stateProvider', '$urlRouterProvider',
    function ($stateProvider,   $urlRouterProvider) {
      $stateProvider
        .state('client_form', {
            url: "/client_form",
            templateUrl: "views/form_client.html",
            data: { pageTitle: 'Crear ayuntamiento' }
        })
        ;
    }
  ]
);
