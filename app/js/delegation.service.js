var utilsDelegation = function ($http) {

  return {

    ListarDelegations: function ListarDelegations() {
      //console.log('entra service delegation');
      return $http({ method: 'GET', url: '../api/index.php/delegation/get_delegations/'});
    },

    ListarProvincias: function ListarProvincias() {
      //console.log('entra service delegation');
      return $http({ method: 'GET', url: '../api/index.php/delegation/list_provincias/'});
    },

    InsertDelegation: function InsertDelegation(delegation) {
      //console.log('entra service delegation');
      return $http({
                      method: 'POST',
                      url: '../api/index.php/delegation/insert_delegation/',
                      data: "delegation_name="+delegation+"",
                      headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                    });
    },

    DeleteDelegation: function DeleteDelegation(id_delegation) {
      //console.log('entra service delegation');
      return $http({
                      method: 'POST',
                      url: '../api/index.php/delegation/delete_delegations/',
                      data: "id="+id_delegation+"",
                      headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                    });
    },

    GetDelegation: function GetDelegation(id_delegation) {
      //console.log('entra service delegation');
      return $http({
                      method: 'GET',
                      url: '../api/index.php/delegation/get_delegation/?id='+id_delegation,
                      //headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                    });
    }

    /*ListarMensajes: function ListarMensajes(bandeja,pagina,busqueda){

      return $http({
                url     : "api/email/mensajes",
                method  : "POST",
                data    : "bandeja ="+bandeja+"&pagina="+pagina+"&busqueda="+busqueda,
                headers : {'Content-Type': 'application/x-www-form-urlencoded'}
            })
            .success(function(data){

            })
            .error(function(){
                //$location.path("/")
        })*/
    }

  };

//Definimos los servicios
angular.module('bidef.delegation.service', [

])
.factory('utilsDelegation', utilsDelegation)