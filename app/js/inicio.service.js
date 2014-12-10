var utilsInicio = function ($http) {

  return {

     

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
angular.module('bidef.inicio.service', [

])
.factory('utilsInicio', utilsInicio)