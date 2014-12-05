var WebmailCtrl = function($scope, $http, $state, $stateParams,utilsWebmail){
	$scope.mensajes = "";
	$scope.maxSize = 5;
  	$scope.bigCurrentPage = 1;
  	$scope.select = {};//almaceno los uids de los mensaje checkeados
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

    	$scope.hasPendingRequests = function () {
 			$scope.loading = parseInt(100)-($http.pendingRequests.length*parseInt(10));
 			console.log($scope.loading);
            return $http.pendingRequests.length > 0;
        };
    $scope.check_important = function() {
    	//console.log($scope.mensajes);
    	var id=[];
    	var indices=[];
    	$.each($scope.select, function(index, val) {
    		//
    		//console.log(val);
    		if(val===true) {

    			//console.log($scope.mensajes[index]);
    		//	console.log($scope.mensajes[index].uid);
    			id.push($scope.mensajes[index].uid);
    			console.log(index);
    			indices.push(index);
    			
    		}
    		//$scope.mensajes[index].data.push({flagged : true});   
    	});


    	utilsWebmail.UpdateMail(id,'important','asd').success(function (response) { 
    	
			$.each(indices, function(key, indice) {
				//console.log(indice);
			   $scope.mensajes[indice].flagged=true;
			});
			
		});
    }


    $scope.check_read = function() {
    	//console.log($scope.mensajes);
    	var id=[];
    	var indices=[];
    	$.each($scope.select, function(index, val) {
    		//
    		//console.log(val);
    		if(val===true) {

    			//console.log($scope.mensajes[index]);
    		//	console.log($scope.mensajes[index].uid);
    			id.push($scope.mensajes[index].uid);
    			console.log(index);
    			indices.push(index);
    			
    		}
    		//$scope.mensajes[index].data.push({flagged : true});   
    	});


    	utilsWebmail.UpdateMail(id,'unread','asd').success(function (response) { 
    	
			$.each(indices, function(key, indice) {
				//console.log(indice);
			   $scope.mensajes[indice].seen=false;
			});
			
		});
    }

        $scope.check_unread = function() {
    	//console.log($scope.mensajes);
    	var id=[];
    	var indices=[];
    	$.each($scope.select, function(index, val) {
    		//
    		//console.log(val);
    		if(val===true) {

    			//console.log($scope.mensajes[index]);
    		//	console.log($scope.mensajes[index].uid);
    			id.push($scope.mensajes[index].uid);
    			console.log(index);
    			indices.push(index);
    			
    		}
    		//$scope.mensajes[index].data.push({flagged : true});   
    	});


    	utilsWebmail.UpdateMail(id,'read','asd').success(function (response) { 
    	
			$.each(indices, function(key, indice) {
				//console.log(indice);
			   $scope.mensajes[indice].seen=true;
			});
			
		});
    }


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
	utilsWebmail.VerMail($stateParams.id).success(function (response) { 
		console.log(response);
		
			$scope.mensaje      = response.view;
			$scope.subject	= response.header.subject;
			$scope.from	= response.header.fromAddress;
			$scope.fecha	= response.header.date;
			$scope.adjuntos	= response.adjuntos;
			$scope.adjuntoslength = response.adjuntos.length;
		
	//	var ficheros = response.adjuntos;
 //console.log(ficheros.length());

		document.getElementById('iframe').contentWindow.updatedata($scope.mensaje);

		
	});

}
//Definimos los controladores
angular
    .module('bidef')
    .controller('WebmailCtrl ', WebmailCtrl)
    .controller('WebmailMensajeCtrl',WebmailMensajeCtrl)