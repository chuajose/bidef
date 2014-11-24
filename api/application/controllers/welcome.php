<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Welcome
 *
 * Este es un documento de ejemplo para ver como conectarse con 
 * la libreria rest. 
 * 
 * Puesdes utlizar el addon https://addons.mozilla.org/es/firefox/addon/restclient/ para las pruebas
 *
 * @package		CodeIgniter
 * @subpackage	Rest Server
 * @category	Controller
 * @author		Phil Sturgeon
 * @link		http://philsturgeon.co.uk/code/
*/

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require APPPATH.'/libraries/REST_Controller.php';

class Welcome extends REST_Controller
{

    /*
    |
    | En caso de enviar por get, selecciona el "METHOD" => GET
    | Para probarla ejecuta sobre el base url index.php/welcome/index/ para hacerlo sin id
    | y index.php/welcome/index/id/1 para pasar el parametro id
    |
     */
	function index_get() {

        if($this->get('id')) {

            $nombre   ="Jose";
            $apellido = "Suarez";
            
        } else {

            $nombre   = "Prueba";
            $apellido = " sin id";
        }

		$this->response(array('nombre'=>$nombre,'apellido'=>$apellido), 200);
	}

    /*
    |
    | En caso de enviar por post, selecciona el "METHOD" => POST modifica las cabeceras en el addon de restclient  "Header"->"Custom Headers"
    | Pon en “name” = “Content-Type” y  “value” = “application/x-www-form-urlencoded”.
    | Los parametos se los puedes pasar en el body del estio id=2&tipo=5
    | Para probarla ejecuta sobre el base url index.php/welcome/index/ para hacerlo sin id
    |
     */
    function index_post() {

        if($this->post('id')) {

            $nombre   ="Jose";
            $apellido = "Suarez";

        } else {

            $nombre   = "Prueba";
            $apellido = " sin id";
        }

        $this->response(array('nombre'=>$nombre,'apellido'=>$apellido), 200);
    }

}
