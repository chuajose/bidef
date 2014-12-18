var WorkpositionCtrl = function($scope, $http, $state, $stateParams, utilsWorkposition, $translatePartialLoader, $translate){
    console.log('entra');
    $translatePartialLoader.addPart('work_position');
    $translate.refresh();
    
	$scope.ListWorkpositions = function(){
        utilsWorkposition.ListarWorkpositions('').success(function (response) {
            
                $scope.workpositions = response.users;
                $scope.countWorkpositions = response.users.length;
            
	   });
    }
    $scope.ListWorkpositions();
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