angular.module('login')
  //factoria para guardar y eliminar sesiones con sessionStorage
.factory("sesionesControl", function(){
    return {
        //obtenemos una sesión //getter
        get : function(key) {
            return sessionStorage.getItem(key)
        },
        //creamos una sesión //setter
        set : function(key, val) {
            return sessionStorage.setItem(key, val)
        },
        //limpiamos una sesión
        unset : function(key) {
            return sessionStorage.removeItem(key)
        }
    }
})
//esto simplemente es para lanzar un mensaje si el login falla, se puede extender para darle más uso
 angular.module('login')
.factory("mensajesFlash", function($rootScope){
    return {
        show : function(message){
            $rootScope.flash = message;
        },
        clear : function(){
            $rootScope.flash = "";
        }
    }
});
 
//factoria para loguear y desloguear usuarios en angularjs
angular.module('login')
.factory("authUsers", function($rootScope, $http, $location, sesionesControl, mensajesFlash){

    var cacheSession = function(email,id_empresa,id_usuario){
        sesionesControl.set("userLogin", true);
        sesionesControl.set("email", email);
        sesionesControl.set("id_usuario", id_usuario);
        $rootScope.email = email;
    }
    var unCacheSession = function(){
        sesionesControl.unset("userLogin");
        sesionesControl.unset("email");
        sesionesControl.unset("id_usuario");
        $rootScope.email = "Sin login";
    }
 
    return {
        //retornamos la función login de la factoria authUsers para loguearnos correctamente
        login : function(login){
            return $http({
                url: "api/login/index",
                method: "POST",
                data : "identity="+login.identity+"&password="+login.password,
                headers: {'Content-Type': 'application/x-www-form-urlencoded'}
            }).success(function(data){
            //  console.log(data.mensaje);
                if(data.error == 0){
                    //si todo ha ido bien limpiamos los mensajes flash
                    mensajesFlash.clear();
                    //creamos la sesión con el email del usuario
                    cacheSession(login.identity,data.id_empresa,data.id_usuario);
                    //mandamos a la home
                    window.location = "index.html"
                    //$state.go('operaciones');

                }else if(data.error == 1){
                    mensajesFlash.show(data.mensaje);
                }else if(data.error == 2){
                    mensajesFlash.show(data.mensaje);
                }
                //onsole.log(data);
            }).error(function(){
                $location.path("/")
            })
        },
        //función para cerrar la sesión del usuario
        logout : function(){
            return $http({
                  url: "api/login/logout",
            }).success(function(){
                //eliminamos la sesión de sessionStorage
                unCacheSession();
                $location.path("/login");
            });
        },
        //función que comprueba si la sesión userLogin almacenada en sesionStorage existe
        isLoggedIn : function(){
            return sesionesControl.get("userLogin");
        },

        id_usuario: sesionesControl.get('id_usuario'),
    }
});

function in_array(needle, haystack, argStrict){
  var key = '',
  strict = !! argStrict;
 
  if(strict){
    for(key in haystack){
      if(haystack[key] === needle){
        return true;
      }
    }
  }else{
    for(key in haystack){
      if(haystack[key] == needle){
        return true;
      }
    }
  }
  return false;
}