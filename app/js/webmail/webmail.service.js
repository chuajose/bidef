var utilsWebmail = function ($http) {

  return {

    ListarWebmail: function ListarWebmail(page, mailbox) {
    //  console.log(id);
      return $http({ 
        method: 'GET', 
        url: APIURL+'email/mails',
        params: {page: page, mailbox: mailbox}
         }); 
    },   

    SearchWebmail: function SearchWebmail(search ,page, mailbox) {
    //  console.log(id);

      return $http({ 
        method: 'POST', 
        url: APIURL+'email/mails',
        data    : "word="+search.word+"&useen="+search.useen+"&end="+search.end+"&start="+search.start+"&body="+search.body+"&subject="+search.subject+"&to="+search.to+"&page="+page+"&mailbox="+mailbox,
        headers : {'Content-Type': 'application/x-www-form-urlencoded'}
         }); 
    },   

    UpdateMail: function UpdateMail(id,action,mailbox) {
    //  console.log(id);
      return $http({ 
        method: 'PUT', 
        url: APIURL+'email/mail',
        data: {id: id, action:action, mailbox:mailbox}
         }); 
    },   


    DeleteMail: function DeleteMail(id,action,mailbox) {
    //  console.log(id);
      return $http({ 
        method: 'DELETE', 
        url: APIURL+'email/mail',
        //data: {id: id},
        //headers : {'Content-Type': 'application/x-www-form-urlencoded'}
        params: {id: id}
         }); 
    },   

    VerMail: function VerMail(id,mailbox) {
    //  console.log(id);
      return $http({ 
        method: 'GET', 
        url: APIURL+'email/mail',
        params: {id: id, mailbox:mailbox}
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
    }, 

    EnviarMail: function EnviarMail(asunto,mensaje,destinatario,adjuntos,borrador,draft){

               var data = $.param({ 'subject':asunto,'message':mensaje,'to':destinatario,'borrador':borrador,'draft':draft,'attachements':adjuntos});

      return $http({
                url     : "../api/index.php/email/mail",
                method  : "POST",
              //  data    : "subject="+asunto+"&message="+mensaje+"&to="+destinatario+"&borrador="+borrador+"&draft="+draft,
                data : data,
                headers : {'Content-Type': 'application/x-www-form-urlencoded'}
            })
            .success(function(data){
            
            })
            .error(function(){
                //$location.path("/")
        })
     
    },

    addMailbox: function addMailbox(mailbox){
      console.log('add '+mailbox);
      return $http({
                url     : "../api/index.php/email/mailbox",
                method  : "POST",
                data    : "mailbox="+mailbox,
                headers : {'Content-Type': 'application/x-www-form-urlencoded'}
            })
            .success(function(data){
            
            })
            .error(function(){
                //$location.path("/")
        })
     
    },

    UpdateMailbox: function addMailbox(mailbox,newmailbox){
      console.log('add '+mailbox);
      return $http({
                url     : "../api/index.php/email/mailbox",
                method  : "PUT",
                data    : "mailbox="+mailbox+"&mailbox_new="+newmailbox,
                headers : {'Content-Type': 'application/x-www-form-urlencoded'}
            })
            .success(function(data){
            
            })
            .error(function(){
                //$location.path("/")
        })
     
    },
    ListarMailbox: function ListarMailbox(){
      return $http({
                url     : "../api/index.php/email/mailbox",
                method  : "GET",
                //data    : 
               // headers : {'Content-Type': 'application/x-www-form-urlencoded'}
            })
            .success(function(data){
            
            })
            .error(function(){
                //$location.path("/")
        })


    },

    DeleteMailbox: function DeleteMailbox(mailbox){
      return $http({
                url     : "../api/index.php/email/mailbox",
                method  : "delete",
                data    : "mailbox="+mailbox,
                //data    : 
               // headers : {'Content-Type': 'application/x-www-form-urlencoded'}
            })
            .success(function(data){
            
            })
            .error(function(){
                //$location.path("/")
        })


    }

  }
}

//Definimos los servicios
angular.module('bidef.webmail.services', [

])
.factory('utilsWebmail', utilsWebmail)