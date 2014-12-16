angular.module('angularTranslateApp', ['pascalprecht.translate'])
  .config(function($translateProvider, $translatePartialLoaderProvider ) {
    $translateProvider.useLoader('$translatePartialLoader', {
      urlTemplate: 'i18n/{lang}/{part}.json'
    });

  $translateProvider.preferredLanguage('es-es');
});