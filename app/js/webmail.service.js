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

    UpdateMail: function UpdateMail(id,action,mailbox) {
    //  console.log(id);
      return $http({ 
        method: 'PUT', 
        url: '../api/index.php/email/mail',
        data: {id: id, action:action, mailbox:mailbox}
         }); 
    },   

    VerMail: function VerMail(id) {
    //  console.log(id);
      return $http({ 
        method: 'GET', 
        url: '../api/index.php/email/mail',
        params: {id: id}
         }); 
    },   

    VerMails: function VerMails(bandeja,pagina,busqueda){

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