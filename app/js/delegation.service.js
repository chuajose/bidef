var utilsDelegation = function ($http) {

  return {

    ListarDelegations: function ListarDelegations() {
      console.log('entra service delegation');
      return $http({ method: 'GET', url: '../api/index.php/delegation/get_delegations/'});
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