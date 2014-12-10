var DelegationCtrl = function($scope, $http, $state, $stateParams, utilsDelegation){
	//$scope.delegations = "";
	console.log('entra ctrl delegacion');
	$scope.data = [{
        name: 'percent values',
        data: [1.34, 22.3, 4.4, 8.55, 16.1, 3.2, 6.4]
    }];
    $scope.options = {
        chart: {
            spacing: 40,
            height: 360
        },
        legend : {
            enabled : false
        },
        xAxis : [{
            categories : ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
        }]
    };
	utilsDelegation.ListarDelegations('').success(function (response) { 
		$scope.delegations = response;		
	});

}

//Definimos los controladores
angular.module('bidef').controller('DelegationCtrl ', DelegationCtrl)