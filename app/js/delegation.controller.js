var DelegationCtrl = function($scope, $http, $state, $stateParams, utilsDelegation){
	//$scope.delegations = "";
	console.log('entra ctrl delegacion');
	var data = [{
            'hc-key': 'es-pm',
            value: 0
        }, {
            'hc-key': 'es-va',
            value: 1
        }, {
            'hc-key': 'es-s',
            value: 2
        }, {
            'hc-key': 'es-na',
            value: 3
        }, {
            'hc-key': 'es-lu',
            value: 4
        }];

    $scope.data = [
                {
                    // Datos a mostrar en el mapa
                    data: data,
                    // Cargamos el fichero geojson que se encarga de pintar el mapa
                    mapData: Highcharts.maps['countries/es/es-all'],
                    joinBy: 'hc-key',
                    name: 'Facturación',
                    states: {
                        hover: {
                            color: '#2c3e50'
                        }
                    },                
                }];
    console.dir($scope.data);

    $scope.options = {
        title: null,
        // Colores aplicados en el mapa
        colorAxis: {
                // Rango mínimo y máximo de visitas
                min: 0,
                max: 5,         
                // Colores aplicados al rango de valores anterior
                minColor: '#eeeeee',
                maxColor: '#2980b9'
        }
    };
	utilsDelegation.ListarDelegations('').success(function (response) { 
		$scope.delegations = response;
        $scope.countDelegation = response.length;
	});

    utilsDelegation.ListarProvincias('').success(function (response) { 
        //console.dir(response);
        $scope.provincias = response;      
    });

    $scope.Save = function(delegation){
        utilsDelegation.InsertDelegation(delegation).success(function (response){});        
        $scope.delegations.push({delegation_name:$scope.nuevaDelegation.delegation_name});
        $scope.countDelegation = $scope.delegations.length;
    }
    $scope.Remove = function(index, id_delegation){
        utilsDelegation.DeleteDelegation(id_delegation).success(function (response){});                
        $scope.delegations.splice(index,1);
        $scope.countDelegation = $scope.delegations.length;
    }    
}
var DelegationProfileCtrl = function($scope, $http, $state, $stateParams, utilsDelegation){
        utilsDelegation.GetDelegation($stateParams.id).success(function(response){
            $scope.name = response;
        });
}

//Definimos los controladores
angular.module('bidef').controller('DelegationCtrl ', DelegationCtrl);
angular.module('bidef').controller('DelegationProfileCtrl ', DelegationProfileCtrl);