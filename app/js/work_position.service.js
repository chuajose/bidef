var utilsWorkposition = function ($http) {

  return {

    ListarWorkpositions: function ListarWorkpositions()
    {
      //console.log('entra service delegation');
      return $http({ method: 'GET', url: '../api/index.php/auth/get_users/'});
    },

    /*InsertDelegation: function InsertDelegation(delegation, color)
    {
      //console.log('entra service delegation');
      return $http({
                      method: 'POST',
                      url: '../api/index.php/delegation/insert_delegation/',
                      data: "delegation_name="+delegation+"&color="+color,
                      headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                    });
    },*/   
  } 
};

//Definimos los servicios
angular.module('bidef.work_position.service', [

])
.factory('utilsWorkposition', utilsWorkposition)