var utilsCrm = function ($http) {

  return {

    InsertClient: function InsertClient(client,billingData){
        return $http({
                url     : "/crm/crm/client",
                method  : "POST",
                data    : {data:client,billing:billingData},
                headers : {'Content-Type': 'application/x-www-form-urlencoded'}
            })
            .success(function(data){
                console.log(data);
            })
            .error(function(){
                //$location.path("/")
        })

      },

    ListarMensajes: function ListarMensajes(bandeja,pagina,busqueda){

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
        })
    }

  };

}

//Definimos los servicios
angular.module('bidef.crm.services', [

])
.factory('utilsCrm', utilsCrm)