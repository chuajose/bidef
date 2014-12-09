function crmCtrl($scope, $http, $state, $stateParams,utilsCrm, DTOptionsBuilder, DTColumnBuilder){
    //$scope.municipalities=[];
    $scope.insertClient = function(){
        console.log('sdf');
        utilsCrm.InsertClient($scope.newClient,$scope.billingData);
        return true;
    }/*
    utilsCrm.ListMunicipalities().success(function (response){
        $scope.municipalities=response;
        console.log(response)
    });*/


    $scope.dtOptions = DTOptionsBuilder.newOptions()
        .withOption('ajax', {
            // Either you specify the AjaxDataProp here
            // dataSrc: 'data',
            url: '/crm/crm/municipalities',
            type: 'POST'
        })
        // or here
        .withDataProp('data')
        .withOption('serverSide', true)
        .withPaginationType('full_numbers');
    $scope.dtColumns = [
        DTColumnBuilder.newColumn('id_municipio').withTitle('ID'),
        DTColumnBuilder.newColumn('nombre_municipio').withTitle('Municipio'),
        DTColumnBuilder.newColumn('nombre_provincia').withTitle('Provincia')
    ];



}

//Definimos los controladores
angular
    .module('bidef')
    .controller('crmCtrl ', crmCtrl)