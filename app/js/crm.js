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
        }).state('municipalities', {
            abstract: true,
            url: "/municipalities",
            template: '<ui-view/>',
            data: { pageTitle: 'Municipis' }
        }).state('municipalities.list', {
            url: "/list",
            templateUrl: "views/table_municipalities.html",
            controller: "crmCtrl",
            data: { pageTitle: 'Municipios' }
        }).state('municipalities.details', {
            url: "/:id",
            templateUrl: "views/profile_municipality.html",
            controller: "crmCtrl",
            data: { pageTitle: 'Municipios' }
        })
        ;
    }
  ]
);
