angular.module('bidef.work_position', [
  'ui.router',
  'bidef.work_position.service',
  'bidef.delegation.service',
])
.config(
  [          '$stateProvider', '$urlRouterProvider',
    function ($stateProvider,   $urlRouterProvider) {
      $stateProvider
        .state('work_position', {
          url: '/work_position',
          templateUrl: 'views/work_position/index.html',
          controller: 'WorkpositionCtrl'
        })
        .state('work_position.profile', {
          url: '/profile/:id',
          views: {
            'profile': {
              templateUrl: 'views/work_position/profile.html',
              controller: 'WorkpositionProfileCtrl'
            }
          }
        });
    }
  ]
);