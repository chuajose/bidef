var utilsDelegation = function ($http) {

  return {

    ListarDelegations: function ListarDelegations()
    {
      //console.log('entra service delegation');
      return $http({ method: 'GET', url: '../api/index.php/delegation/get_delegations/'});
    },

    ListarProvincias: function ListarProvincias()
    {
      //console.log('entra service delegation');
      return $http({ method: 'GET', url: '../api/index.php/delegation/list_provincias/'});
    },

    InsertDelegation: function InsertDelegation(delegation, color)
    {
      //console.log('entra service delegation');
      return $http({
                      method: 'POST',
                      url: '../api/index.php/delegation/insert_delegation/',
                      data: "delegation_name="+delegation+"&color="+color,
                      headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                    });
    },

    DeleteDelegation: function DeleteDelegation(id_delegation)
    {
      //console.log('entra service delegation');
      return $http({
                      method: 'POST',
                      url: '../api/index.php/delegation/delete_delegations/',
                      data: "id="+id_delegation+"",
                      headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                    });
    },

    GetDelegation: function GetDelegation(id_delegation)
    {
      //console.log('entra service delegation');
      return $http({
                      method: 'GET',
                      url: '../api/index.php/delegation/get_delegation/?id='+id_delegation,
                      //headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                    });
    },

    checkDelegationRepeatColor: function checkDelegationRepeatColor(color)
    {
      return $http({
                      method: 'GET',
                      url: '../api/index.php/delegation/check_color_delegation/?color='+color,
                      //headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                    });
    },
    UpdateDelegation: function UpdateDelegation(id_delegation, delegation_name, color)
    {
      var data_string = "id="+id_delegation;
      if(delegation_name) data_string += "&delegation_name="+delegation_name;
      if(color) data_string += "&color="+color;
      return $http({
                      method: 'POST',
                      url: '../api/index.php/delegation/update_delegation',
                      data: data_string,
                      headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                    });
    },
    MapaDelegacionProvincias: function MapaDelegacionProvincias()
    {
      return $http({
                      method: 'GET',
                      url: '../api/index.php/delegation/delegation_map_provincias'
                    });
    },
    UpdateMapaDelegacionProvincias: function UpdateMapaDelegacionProvincias(id_delegation, vectormap_code)
    {
      return $http({
                      method: 'POST',
                      url: '../api/index.php/delegation/update_delegation_map_provincias',
                      data: 'delegation_id='+id_delegation+'&vectormap_code='+vectormap_code,
                      headers: {'Content-Type': 'application/x-www-form-urlencoded'}

                    });
    },
    UpdateDeleteMapaDelegacionProvincias: function UpdateDeleteMapaDelegacionProvincias(vectormap_code)
    {
      return $http({
                      method: 'POST',
                      url: '../api/index.php/delegation/update_delete_delegation_map_provincias',
                      data: 'vectormap_code='+vectormap_code,
                      headers: {'Content-Type': 'application/x-www-form-urlencoded'}

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