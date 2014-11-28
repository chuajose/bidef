var InsertClient = function($scope, $http, $state, $stateParams,utilsWebmail){
	console.log($scope.newClient);
	console.log($scope.billingData);
	// $scope.mensajes = "";
	// utilsWebmail.ListarWebmail('').success(function (response) {

	// 	$scope.bandejas = response.bandejas;
	// 	$scope.mensajes = response.mensajes;
	// 	console.log(response);
	// });

}

//Definimos los controladores
angular
    .module('crm')
    .controller('InsertClient ', InsertClient)