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
            controller: "crmCtrl",
            data: { pageTitle: 'Crear ayuntamiento' }
        }).state('municipalities_list', {
            url: "/municipalities_list",
            templateUrl: "views/table_municipalities.html",
            controller: "crmCtrl",
            data: { pageTitle: 'Municipios' }
        })
        ;
    }
  ]
);
