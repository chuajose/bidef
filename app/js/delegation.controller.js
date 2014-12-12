var DelegationCtrl = function($scope, $http, $state, $stateParams, utilsDelegation){
	utilsDelegation.ListarDelegations('').success(function (response) {
		$scope.delegations = response;
        $scope.countDelegation = response.length;
	});

    utilsDelegation.ListarProvincias('').success(function (response) {
        $scope.provincias = response;
    });


    /*-mapa-*/
    /**
     * vectorMap - Directive for Vector map plugin
     */
    
    var dataMap_series = {
        "ES-C": 1,
        "ES-O": 1,
        "ES-S": 1,
        "ES-LU": 1,
        "ES-OR": 1,
        "ES-PO": 1,        
        "ES-M": 2,            
    };    

    $scope.dataMap = dataMap_series;    

    $scope.Save = function(delegation){
        utilsDelegation.InsertDelegation(delegation).success(function (response){});
        $scope.delegations.push({delegation_name:delegation});
        $scope.countDelegation = $scope.delegations.length;
    }
    $scope.Remove = function(index, id_delegation){
        utilsDelegation.DeleteDelegation(id_delegation).success(function (response){});
        $scope.delegations.splice(index,1);
        $scope.countDelegation = $scope.delegations.length;
    }
    $scope.AddColor = function(id_delegation, color){
        console.log(id_delegation + ' - ' + color);
    }
}






var DelegationProfileCtrl = function($scope, $http, $state, $stateParams, utilsDelegation){
        utilsDelegation.GetDelegation($stateParams.id).success(function (response){
           // console.log(response[0].id_delegation);
            $scope.name = response[0].delegation_name;
        });

        /*-area-*/
    $scope.optionsDelegation = {
        chart: {
            type: 'area'
        },
        title: {
            text: 'Desarrolo de la Delegación'
        },
        xAxis: {
            categories: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio'],
            tickmarkPlacement: 'on',
            title: {
                enabled: false
            }
        },
        yAxis: {            
            labels: {
                formatter: function () {
                    return this.value / 1000;
                }
            }
        },
        tooltip: {
            shared: true,                   
        },
        plotOptions: {
            area: {
                stacking: 'normal',
                lineColor: '#666666',
                lineWidth: 1,
                marker: {
                    lineWidth: 1,
                    lineColor: '#666666'
                }
            }
        }    
    }    
    
    $scope.dataDelegation = [{
            name: 'Facturación',
            data: [502, 635, 809, 947, 1402, 3634, 5268]
        },{
            name: 'Rendimiento',
            data: [163, 203, 276, 408, 547, 729, 628]
        }];

    /*-pie-*/
    $scope.optionsPieDelegation = {
        chart: {
            type: 'pie'            
        },
        title: {
            text: null
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                depth: 35,
                dataLabels: {
                    enabled: true,
                    format: '{point.name}'
                }
            }
        }
    };

    $scope.dataPieDelegation = [{
            type: 'pie',
            name: 'Facturación con respecto a las demás delegaciones',
            data: [                
                {
                    name: 'Norte',
                    y: 12.8,
                    sliced: true,
                    selected: true
                },
                ['Resto',    8.5]
            ]
        }];
}


//Definimos los controladores
angular.module('bidef')
.controller('DelegationCtrl ', DelegationCtrl)
.controller('DelegationProfileCtrl ', DelegationProfileCtrl)