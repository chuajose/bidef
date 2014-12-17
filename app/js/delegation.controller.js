var DelegationCtrl = function($scope, $http, $state, $stateParams, utilsDelegation, $translatePartialLoader, $translate){
    $translatePartialLoader.addPart('delegation');
    $translate.refresh();
    $scope.countDelegation = 0;
    $scope.delegations = [];
    $scope.provincias = {};
    $scope.delegation = {};
    $scope.delegation.selected = '';
    //$scope.delegation.selected = '';
    //$scope.disabled = undefined;
	$scope.ListDelegations = function(){
        utilsDelegation.ListarDelegations('').success(function (response) {
            if(response.respuesta != 29)
            {
                $scope.delegations = response;
                $scope.countDelegation = response.length;
            }
	   });
    }
    $scope.ListDelegations();

    utilsDelegation.ListarProvincias('').success(function (response) {
        $scope.provincias = response;
    });

    /*-mapa-*/
    /**
     * vectorMap - Directive for Vector map plugin
     */
    map_draw = utilsDelegation.MapaDelegacionProvincias('').success(function (response) {
        $scope.mapdata = response;
    });

    $scope.Save = function(delegation, callback)
    {
        var insertarColor=function(color)
        {
            utilsDelegation.InsertDelegation(delegation, color).success(function (response){
                console.log(response);
                if(response.respuesta >  -1)
                {
                    $scope.delegations.push({delegation_name: delegation, color: color, id_delegation: response.respuesta});
                    $scope.countDelegation = $scope.delegations.length;
                }
            });
        }
        GenerateRandomDelegationColor(insertarColor);

    }
    GenerateRandomDelegationColor = function(callback)
    {
      color = randomColor();
      utilsDelegation.checkDelegationRepeatColor(color).success(function (response){
          if(parseInt(response.respuesta) != 301) GenerateRandomDelegationColor(callback);
          callback(color);
      });
    }

    function randomColor()
    {
      var r = function () { return Math.floor(Math.random()*256) };
      return "rgb(" + r() + "," + r() + "," + r() + ")";
    }

    $scope.Remove = function(index, id_delegation)
    {
        utilsDelegation.DeleteDelegation(id_delegation).success(function (response){});
        $scope.delegations.splice(index,1);
        $scope.countDelegation = $scope.delegations.length;
        utilsDelegation.MapaDelegacionProvincias('').success(function (response) {
            $scope.mapdata = response;
        });
    }
    $scope.UpdateDelegation = function(index, id_delegation, delegation_name, color)
    {
        console.log('name: '+delegation_name+' - color: '+color)
        utilsDelegation.UpdateDelegation(id_delegation, delegation_name, color).success(function (response)
        {
            if(response.respuesta ===  0)
            {
                console.log(response);
                if(delegation_name) $scope.delegations[index].delegation_name = delegation_name;
                if(color)
                {
                    $scope.delegations[index].color = color;
                    utilsDelegation.MapaDelegacionProvincias('').success(function (response) {
                        $scope.mapdata = response;
                    });
                }
            }
        });
    }
    $scope.UpdateMapaDelegacionProvincias = function(id_delegation)
    {
        console.log(id_delegation + ' - vector_code: ' + window.selectedAreasMap);
        if(selectedAreasMap)
        {
            utilsDelegation.UpdateMapaDelegacionProvincias(id_delegation, selectedAreasMap).success(function (response)
            {
                utilsDelegation.MapaDelegacionProvincias('').success(function (response) {
                    $scope.mapdata = response;
                    console.dir($scope.mapdata);
                });
            });
        }

    }
    $scope.UpdateDeleteMapaDelegacionProvincias = function()
    {
        if(selectedAreasMap)
        {
            utilsDelegation.UpdateDeleteMapaDelegacionProvincias(selectedAreasMap).success(function (response)
            {
               utilsDelegation.MapaDelegacionProvincias('').success(function (response) {
                    $scope.mapdata = response;
                    console.dir($scope.mapdata);
                });
            });
        }
    }

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
            title: {
                text: 'Euros'
            },
            labels: {
                formatter: function () {
                    return this.value;
                }
            }
        },
        tooltip: {
            shared: true,
        },
        plotOptions: {
            area: {
                /*stacking: 'normal',
                lineColor: '#ffffff',
                lineWidth: 1,*/
                marker: {
                    enabled: false,
                    symbol: 'circle',
                    radius: 2,
                    states: {
                        hover: {
                            enabled: true
                        }
                    }
                }
            }
        }
    }

    $scope.dataDelegation = [{
            name: 'Facturación',
            data: [5020, 6350, 8090, 9470, 14020, 36340, 52680]
        },{
            name: 'Rendimiento',
            data: [1630, 2030, 2760, 4080, 5470, 7290, 6280]
        }];

    /*-table delegaciones-*/
    var delegation_data = [
                   {            'delegacion_name': 'Delegacion Norte',
                                'delegacion_facturacion': 1500,
                                'delegacion_gastos': 250,
                                'delegacion_beneficios': 1250,
                                'delegacion_rendimiento': 200,
                                'delegacion_proyectos': 4,
                                'delegacion_alumnos': 34
                    },
                    {           'delegacion_name': 'Delegacion Sur',
                                'delegacion_facturacion': 1500,
                                'delegacion_gastos': 250,
                                'delegacion_beneficios': 1250,
                                'delegacion_rendimiento': 200,
                                'delegacion_proyectos': 4,
                                'delegacion_alumnos': 34
                    }
                            ];

    $scope.delegations_table = delegation_data;

}





var DelegationProfileCtrl = function($scope, $http, $state, $stateParams, utilsDelegation, $translatePartialLoader, $translate){
    $translatePartialLoader.addPart('delegation');
    $translate.refresh();
    $scope.name = $stateParams.id;
        utilsDelegation.GetDelegation($stateParams.id).success(function (response){
           // console.log(response[0].id_delegation);
           $scope.name = response[0].delegation_name;
           /*-area-*/
           $scope.optionsDelegation = {
                chart: {
                    type: 'area'
                },
                title: {
                    text: 'Desarrolo de la Delegación'+$scope.name,
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
                            return this.value+'€';
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
                    text: 'Facturación de con respecto al conjunto de delegaciones',
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
                            name: $scope.name,
                            y: 12.8,
                            sliced: true,
                            selected: true
                        },
                        ['Resto',    8.5]
                    ]
                }];

                });
}


//Definimos los controladores
angular.module('bidef')
.controller('DelegationCtrl ', DelegationCtrl)
.controller('DelegationProfileCtrl ', DelegationProfileCtrl)