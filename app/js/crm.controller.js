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
            url: APIURL+'crm/municipalities',
            type: 'POST'
        })
        // or here
        .withDataProp('data')
        .withOption('serverSide', true)
        .withOption('rowCallback', function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
            // Unbind first in order to avoid any duplicate handler (see https://github.com/l-lin/angular-datatables/issues/87)
            $('td', nRow).unbind('click');
            $('td', nRow).bind('click', function() {
                $scope.$apply(function() {
                    $state.go('municipalities.details',{id:aData.id_municipio});
                });
            });
            return nRow;
        })
        .withPaginationType('full_numbers');
    $scope.dtColumns = [
        DTColumnBuilder.newColumn('id_municipio').withTitle('ID'),
        DTColumnBuilder.newColumn('nombre_municipio').withTitle('Municipio'),
        DTColumnBuilder.newColumn('nombre_provincia').withTitle('Provincia')
    ];
}
function municipalityCtrl($scope, $http, $state, $stateParams,utilsCrm){
    $scope.municipality=[];
    utilsCrm.Municipality($stateParams.id).success(function (response){
        $scope.municipality=response;
    });

    $scope.open_contact_form=function(){
        $scope.success_contact_form=0;
        $scope.show_contact_form=1;
        document.getElementById("new_contact_name").focus();
    }
    $scope.send_contact_form=function(){
        utilsCrm.InsertMunicipalityContact($scope.new_contact,$scope.municipality.id_municipio).success(function (response){
            $scope.reset_contact_form();
            $scope.success_contact_form=1;
            utilsCrm.ListMunicipalityContacts($scope.municipality.id_municipio).success(function (response){
                $scope.municipality.contacts=response;
            });
        }).error(function(response){
            console.log(response);
        });
        return true;
    }
    $scope.reset_contact_form=function(){
        $scope.new_contact=[];
        $scope.show_contact_form=0;
        return true;
    }
}

//Definimos los controladores
angular
    .module('bidef')
    .controller('crmCtrl ', crmCtrl)
    .controller('municipalityCtrl ', municipalityCtrl)