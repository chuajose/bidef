angular.module('bidef.crm', [
  'bidef.crm.services'
]).config(
  ['$stateProvider', '$urlRouterProvider',
    function ($stateProvider,   $urlRouterProvider) {
      $stateProvider
        .state('client_form', {
            url: "/client_form",
            templateUrl: "views/form_client.html",
            controller : InsertClient,
            data: { pageTitle: 'Crear ayuntamiento' }
        })
        ;
    }
  ]
);
