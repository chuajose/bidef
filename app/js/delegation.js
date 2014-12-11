angular.module('bidef.delegation', [
  'ui.router',
  'ui-highcharts',
  'bidef.delegation.service',
])
.config(
  [          '$stateProvider', '$urlRouterProvider',
    function ($stateProvider,   $urlRouterProvider) {
      $stateProvider
        .state('delegation', {
          url: '/delegation',
          templateUrl: 'views/delegation/index.html',
          controller: 'DelegationCtrl'
        })
        .state('delegation.profile_delegation', {
          url: '/:id',
          templateUrl: 'views/delegation/profile.html',
          controller: 'DelegationCtrl'
        });
    }
  ]
);