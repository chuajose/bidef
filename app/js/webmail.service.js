var utilsWebmail = function ($http) {

  return {

    ListarWebmail: function ListarWebmail(page, mailbox) {
    //  console.log(id);
      return $http({ 
        method: 'GET', 
        url: '../api/index.php/email/mails',
        params: {page: page, mailbox: mailbox}
         }); 
    },   

    verMail: function ListarWebmail(id) {
    //  console.log(id);
      return $http({ 
        method: 'GET', 
        url: '../api/index.php/email/mail',
        params: {id: id}
         }); 
    },   

    ListarMensajes: function ListarMensajes(bandeja,pagina,busqueda){

      return $http({
                url     : "index.php/api/email/mails",
                method  : "get",
                data    : "bandeja ="+bandeja+"&pagina="+pagina+"&busqueda="+busqueda,
               // headers : {'Content-Type': 'application/x-www-form-urlencoded'}
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