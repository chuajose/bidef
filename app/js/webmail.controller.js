var WebmailCtrl = function($modal, $scope, $http, $state, $stateParams,utilsWebmail){
	$scope.mensajes = "";
	$scope.maxSize = 5;
  	$scope.bigCurrentPage = 1;
  	$scope.select = {};//almaceno los uids de los mensaje checkeados
  	console.log('entro en WebmailCtrl');
	utilsWebmail.ListarWebmail(1,'inbox').success(function (response) { 

		$scope.bandejas = response.bandejas;
		$scope.mensajes = response.emails;
		$scope.bigTotalItems = response.total

		//console.log(response);
	});

	utilsWebmail.ListarMailbox().success(function (response) { 
		$scope.bandejas= response;
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
    $scope.checkImportant = function() {
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

    $scope.deleteMailbox = function() {
    	
    	utilsWebmail.DeleteMailbox($scope.mailbox).success(function (response) { 
    	$state.go('webmail' , $stateParams,{reload: true});
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




	$scope.buscar = function () {
	    utilsWebmail.ListarMensajes($stateParams.bandejaId, $scope.currentPage, $scope.palabra).success(function (response) { 
			$scope.mensajes      = response.mensajes;
			$scope.bandeja       = $stateParams.bandejaId;
			$scope.mensaje       = false;
			$scope.bigTotalItems = response.total
			
		});
	};

	$scope.open = function () {
        var modalInstance = $modal.open({
            templateUrl: 'views/webmail/modal_add_mailbox.html',
            controller: WebmailCreateMailboxCtrl,
        });
    };

    $scope.refresh = function() {

    	$state.go('webmail' , $stateParams,{reload: true});

    }

}

var WebmailMensajeCtrl = function($scope, $http, $state, $stateParams,utilsWebmail){

	$scope.mensaje="";
	
	if(!$stateParams.id) {
		$state.go('webmail');
		return false;
	}

	utilsWebmail.VerMail($stateParams.id).success(function (response) { 
		console.log(response);
		
			$scope.mensaje      = response.view;
			$scope.subject	= response.header.subject;
			$scope.from	= response.header.fromAddress;
			$scope.fecha	= response.header.date;
			$scope.adjuntos	= response.adjuntos;
			$scope.adjuntoslength = response.adjuntos.length;
			$scope.uid	= $stateParams.id;
		
	//	var ficheros = response.adjuntos;
 //console.log(ficheros.length());

		document.getElementById('iframe').contentWindow.updatedata($scope.mensaje);

		

		
	});

	$scope.updateMail = function(action){
			utilsWebmail.ActionMail($scope.uid, action).success(function (response) { 
				$scope.mensajes      = response.mensajes;
				$scope.bandeja       = $stateParams.bandejaId;
				$scope.bigTotalItems = response.total;
				
			});

	}

	$scope.DeleteMail = function(uid) {
    	console.log(uid);
    	utilsWebmail.DeleteMail($scope.uid).success(function (response) { 
    		$state.go('webmail' , $stateParams,{reload: true});
			
		});

    }

}

var WebmailComposeCtrl = function($scope, $http, $state, $stateParams,utilsWebmail){
	$scope.subject="";
	$scope.mensaje="";
	$scope.from="";
	$scope.uid	= "";
	if($stateParams.id) {
		
		utilsWebmail.VerMail($stateParams.id).success(function (response) { 
				if(response) {

					$scope.mensaje      = "<br><br><br><blockquote>"+response.view+"</blockquote>";
					$scope.subject	= "Re: "+response.header.subject;
					$scope.from	= response.header.fromAddress;
					$scope.fecha	= response.header.date;
					$scope.adjuntos	= response.adjuntos;
					$scope.adjuntoslength = response.adjuntos.length;
					$scope.uid	= $stateParams.id;

				}
				
		});
	}

	$scope.send = function(){

		utilsWebmail.EnviarMail($scope.subject,$scope.mensaje,$scope.from).success(function (response) { 


			if(response.error==0) $state.go('webmail');
		});
		console.log($scope.subject);
	}
}

var WebmailCreateMailboxCtrl = function($scope, $http, $state, $stateParams,utilsWebmail,$modalInstance){
	$scope.newmailbox="647";

	$scope.crear = function(){

		utilsWebmail.addMailbox($scope.newmailbox).success(function (response) { 
			console.log(response);
			if(response.error==0)  $state.go('webmail' , $stateParams,{reload: true});
			$modalInstance.close();
		});
	}
}
//Definimos los controladores
angular
    .module('bidef')
    .controller('WebmailCtrl ', WebmailCtrl)
    .controller('WebmailMensajeCtrl',WebmailMensajeCtrl)
    .controller('WebmailComposeCtrl',WebmailComposeCtrl)
    .controller('WebmailCreateMailboxCtrl',WebmailCreateMailboxCtrl)