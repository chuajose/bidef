/*-
$scope.Save = function(delegation, callback)
{
    var insertarColor=function(color)
    {
        console.log('color:' + color);
        utilsDelegation.InsertDelegation(delegation, color).success(function (response){
            console.log(response);
            if(response.respuesta ===  '0')
            {
                $scope.delegations.push({delegation_name:delegation, color: color});
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
          console.log(response.respuesta);
          if(parseInt(response.respuesta) != 301) GenerateRandomDelegationColor(callback);
          callback(color);
      });
  }

  function randomColor()
  {
      var r = function () { return Math.floor(Math.random()*256) };
      return "rgb(" + r() + "," + r() + "," + r() + ")";
  }
-*/


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
     utilsDelegation.MapaDelegacionProvincias('').success(function (response) {
        //var data_mapa_array = []
        //data_mapa_array['ES-SA'] = '#000000'
        //$scope.dataMap = response;
        //console.log(typeof response);

        //console.log(typeof $scope.dataMap);
        //console.log($scope.dataMap);
        //$scope.data = response;
        $scope.mapdata = response;
        console.dir($scope.mapdata);
        //var mapobject = window.mapElement;

    });





    var colors = ['rgb(41, 128, 185)','rgb(142, 68, 173)','rgb(0, 0, 0)','rgb(44, 62, 80)','rgb(243, 156, 18)','rgb(211, 84, 0)','rgb(192, 57, 43)','rgb(127, 140, 141)']

    $scope.Save = function(delegation)
    {
        /*GenerateRandomDelegationColor(function(color)
        {*/
            var color = colors[$scope.countDelegation];
            utilsDelegation.InsertDelegation(delegation, color).success(function (response){
                if(response.respuesta > 0)
                {
                    $scope.delegations.push({id_delegation: response.respuesta, delegation_name:delegation, color: color});
                    $scope.countDelegation = $scope.delegations.length;
                }
            });
        /*});*/
    }
    $scope.Remove = function(index, id_delegation)
    {
        utilsDelegation.DeleteDelegation(id_delegation).success(function (response){});
        $scope.delegations.splice(index,1);
        $scope.countDelegation = $scope.delegations.length;
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
                if(color) $scope.delegations[index].color = color;
            }
        });
    }
    $scope.UpdateMapaDelegacionProvincias = function(id_delegation, vectorMapCode)
    {
        console.log(id_delegation + ' - vector_code: ' + window.selectedAreasMap);
        if(selectedAreasMap)
        {
            utilsDelegation.UpdateMapaDelegacionProvincias(id_delegation, selectedAreasMap).success(function (response)
            {
                console.log(response);
               utilsDelegation.MapaDelegacionProvincias('').success(function (response) {
                    //var data_mapa_array = []
                    //data_mapa_array['ES-SA'] = '#000000'
                    //$scope.dataMap = response;
                    //console.log(typeof response);

                    //console.log(typeof $scope.dataMap);
                    //console.log($scope.dataMap);
                    //$scope.data = response;
                    $scope.mapdata = response;
                    console.dir($scope.mapdata);
                    //var mapobject = window.mapElement;

                });

                /*- funcion para limpiar el mapa
                clearSelectedRegions();
                -*/
            });
        }

    }

    GenerateRandomDelegationColor = function()
    {
        color = randomColor();
        utilsDelegation.checkDelegationRepeatColor(color).success(function (response){
            console.log(response.respuesta);
            if(parseInt(response.respuesta) !== 301) GenerateRandomDelegationColor();
        });
    }

    function randomColor()
    {
        var r = function () { return Math.floor(Math.random()*256) };
        return "rgb(" + r() + "," + r() + "," + r() + ")";
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