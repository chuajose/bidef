var WorkpositionCtrl = function($scope, $http, $state, $stateParams, utilsWorkposition, $translatePartialLoader, $translate, utilsDelegation){
    console.log('entra');
    $translatePartialLoader.addPart('work_position');
    $translate.refresh();
    $scope.showform              = false;
    $scope.new_wokposition       = [];
    $scope.new_wokposition.group = [];
    $scope.new_wokposition.delegations = [];

	$scope.ListWorkpositions = function(){
        utilsWorkposition.ListarWorkpositions('').success(function (response) {
                $scope.workpositions      = response.users;
                $scope.countWorkpositions = response.users.length;

	   });
    }

    $scope.ListarPermisos = function(){
        utilsWorkposition.ListarPermisos('').success(function (response) {
            //console.log(response);
            $scope.permissions = response.respuesta;
        });
    }
    $scope.addWorkPosition = function(){
        console.dir($scope.new_wokposition);
        $scope.new_wokposition.group_str = '';
        $.each($scope.new_wokposition.group, function(index, val)
        {
            $scope.new_wokposition.group_str += '&group[]='+val;
        });

        utilsWorkposition.AddNewWorkPosition($scope.new_wokposition).success(function (response) {
            $scope.workpositions.push({username:$scope.new_wokposition.username});
            $scope.countWorkpositions++;
            $scope.showform = false;
        });
    }
    $scope.ListWorkpositions();
    utilsDelegation.ListarDelegations().success(function (response){
        $scope.delegations = response;
    });
}





var WorkpositionProfileCtrl = function($scope, $http, $state, $stateParams, utilsWorkposition, $translatePartialLoader, $translate){
    $translatePartialLoader.addPart('work_position');
    $translate.refresh();
    /*$scope.name = $stateParams.id;
        utilsWorkposition.GetWorkposition($stateParams.id).success(function (response){
           // console.log(response[0].id_delegation);
           $scope.name = response[0].username;

        });*/
}


//Definimos los controladores
angular.module('bidef')
.controller('WorkpositionCtrl ', WorkpositionCtrl)
.controller('WorkpositionProfileCtrl ', WorkpositionProfileCtrl)