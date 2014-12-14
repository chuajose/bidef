var utilsCrm = function ($http) {

  return {

    InsertClient: function InsertClient(client,billingData){
        return $http({
                url     : APIURL+"crm/client",
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
    ListMunicipalities: function ListMunicipalities(){
        return $http({
                url     : APIURL+"crm/municipalities",
                method  : "GET"
            })

      },
    Municipality: function Municipality(id){
        return $http({
                url     : APIURL+"crm/municipality/"+id,
                method  : "GET"
            })

      },
    InsertMunicipalityContact: function InsertMunicipalityContact(contact,id){
        return $http({
                url     : APIURL+"crm/municipality_contact",
                method  : "POST",
                data    : {contact:contact,id:id},
                headers : {'Content-Type': 'application/x-www-form-urlencoded'}
            });
      },
    ListMunicipalityContacts: function ListMunicipalityContacts(id){
        return $http({
                url     : APIURL+"crm/municipality_contacts/"+id,
                method  : "GET"
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