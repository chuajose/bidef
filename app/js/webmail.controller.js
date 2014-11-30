var WebmailCtrl = function($scope, $http, $state, $stateParams,utilsWebmail){
	$scope.mensajes = "";
	$scope.maxSize = 5;
  	$scope.bigCurrentPage = 1;
  	console.log('entro en WebmailCtrl');
	utilsWebmail.ListarWebmail(1,'inbox').success(function (response) { 

		$scope.bandejas = response.bandejas;
		$scope.mensajes = response.emails;
		$scope.bigTotalItems = response.total

		console.log(response);
	});

  	$scope.pageChanged = function(page) {
	  	$scope.currentPage = page;
	    utilsWebmail.ListarWebmail(page,'inbox').success(function (response) { 

			$scope.bandejas = response.bandejas;
			$scope.mensajes = response.emails;
			$scope.bigTotalItems = response.total
		});
    };

}

var WebmailBandejaCtrl = function($scope, $http, $state, $stateParams,utilsWebmail){
	$scope.mensajes      = "";
	$scope.currentPage   = $stateParams.pagina;
	$scope.bigTotalItems = 0;
	$scope.palabraListarWebmail= "";
	utilsWebmail.ListarWebmail( $scope.currentPage,$stateParams.bandejaId,$scope.palabra).success(function (response) { 
		$scope.mensajes      = response.emails;
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

var WebmailMensajeCtrl = function($scope, $http, $state, $stateParams,utilsWebmail){
	$scope.mensaje="";
	utilsWebmail.verMail($stateParams.id).success(function (response) { 
		console.log(response);
		
			$scope.mensaje      = response.view;
			$scope.subject	= response.header.subject;
			$scope.from	= response.header.fromAddress;
			$scope.fecha	= response.header.date;
			$scope.adjuntos	= response.adjuntos;
			$scope.adjuntoslength = response.adjuntos.length;
		
		var ficheros = response.adjuntos;
 //console.log(ficheros.length());

		document.getElementById('iframe').contentWindow.updatedata($scope.mensaje);

		
	});

}
//Definimos los controladores
angular
    .module('bidef')
    .controller('WebmailCtrl ', WebmailCtrl)
    .controller('WebmailMensajeCtrl',WebmailMensajeCtrl)