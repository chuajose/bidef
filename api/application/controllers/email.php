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

class Email extends REST_Controller
{

    function __construct(){

        parent::__construct();

        $this->load->library('imap');

        $this->login = $this->imap->connect();//usuario y password

        $this->data['error']=0;
        
    }

    /**
     * Devuelve las bandejas 
     *
     * @return array
     */
    function bandejas_get()
    {
        $bandejas = $this->imap->get_listing_folders();

        $this->data['bandejas']=$bandejas;

        $this->response($this->data, 200);

    }
    /**
     * Devuelve los email solicitados
     *
     * @param int $page parametro get con el valor de la pagina a cargar
     * @return array
     */
	function mails_get() {

        if($this->get('page')) {

            $page   = $this->get('page');
            
        } else {

            $page = 1;
        }

        $emails = $this->imap->paginate_mails($page,10);

        $this->data['emails'] = $emails;

		$this->response($this->data, 200);
	}

    /**
     * Devuelve los email solicitados
     *
     * @param int $page parametro post con el valor de la pagina a cargar
     * @param int $search parametro post con el valor de la busqueda a realizar
     * @return array
     */
    function mails_post() {

        if($this->post('page')) {

            $page   = $this->post('page');
            
        } else {

            $page = 1;
        }

        if($this->post('search')) {

            $criteria   = $this->post('search');
            
        } else {

            $criteria = 'ALL';
        }

        $emails = $this->imap->search_mails($criteria,$page,10);

        list($messages,$total) = $emails;

        $this->data['emails'] = $messages;

        $this->data['total'] = $total;

        $this->response($this->data, 200);
    }

}
