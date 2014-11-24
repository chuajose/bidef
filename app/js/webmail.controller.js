var WebmailCtrl = function($scope, $http, $state, $stateParams,utilsWebmail){
	$scope.mensajes = "";
	utilsWebmail.ListarWebmail('').success(function (response) { 

		$scope.bandejas = response.bandejas;
		$scope.mensajes = response.mensajes;
		console.log(response);
	});

}

var WebmailBandejaCtrl = function($scope, $http, $state, $stateParams,utilsWebmail){
	$scope.mensajes      = "";
	$scope.currentPage   = $stateParams.pagina;
	$scope.bigTotalItems = 0;
	$scope.palabra       = "";
	utilsWebmail.ListarMensajes($stateParams.bandejaId, $scope.currentPage,$scope.palabra).success(function (response) { 
		$scope.mensajes      = response.mensajes;
		$scope.bandeja       = $stateParams.bandejaId;
		$scope.bigTotalItems = response.total
		
		
	});

	$scope.buscar = function () {
	    utilsWebmail.ListarMensajes($stateParams.bandejaId, $scope.currentPage, $scope.palabra).success(function (response) { 
			$scope.mensajes      = response.mensajes;
			$scope.bandeja       = $stateParams.bandejaId;
			$scope.mensaje       = false;
			$scope.bigTotalItems = response.total
			
		});
	};

 		$scope.hasPendingRequests = function () {
 			$scope.loading = parseInt(100)-($http.pendingRequests.length*parseInt(10));
 			console.log($scope.loading);
            return $http.pendingRequests.length > 0;
        };

		  $scope.currentPage = 1;

		  $scope.setPage = function (pageNo) {
		    $scope.currentPage = pageNo;
		  };

		  $scope.pageChanged = function() {
		    console.log('Page changed to: ' + $scope.currentPage);
		    utilsWebmail.ListarMensajes($stateParams.bandejaId, $scope.currentPage,$scope.palabra).success(function (response) { 
				$scope.mensajes      = response.mensajes;
				$scope.bandeja       = $stateParams.bandejaId;
				$scope.bigTotalItems = response.total
				
			});
		  };

		  $scope.maxSize = 5;


}

//Definimos los controladores
angular
    .module('bidef')
    .controller('WebmailCtrl ', WebmailCtrl)