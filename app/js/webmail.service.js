var utilsWebmail = function ($http) {

  return {

    ListarWebmail: function ListarWebmail(callback) {
    //  console.log(id);
      return $http({ method: 'GET', url: 'api/email/index/bandeja/'+callback }); 
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
angular.module('bidef.webmail.services', [

])
.factory('utilsWebmail', utilsWebmail)