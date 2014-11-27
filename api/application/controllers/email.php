<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Email
 *
 * Conexión al buzon imap
 * 
 * Puesdes utlizar el addon https://addons.mozilla.org/es/firefox/addon/restclient/ para las pruebas
 *
 * @package		Imap
 * @subpackage	Email
 * @category	Controller
 * @author		Jose Manuel Súarez Bravo
 * @see         https://github.com/chuajose
*/

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require APPPATH.'/libraries/REST_Controller.php';

class Email extends REST_Controller
{

    function __construct(){

        parent::__construct();

        $this->load->library('imap');

        $this->login         = $this->imap->connect();//usuario y password
        
        $this->data['error'] = 0;
        
    }

    /**
     * Devuelve las bandejas 
     *
     * @return array
     */
    function mailbox_get()
    {
        $bandejas               = $this->imap->get_listing_folders();
        
        $this->data['bandejas'] =$bandejas;

        $this->response($this->data, 200);

    }

    function mailbox_rename_post()
    {
        if( $this->post( 'mailbox' ) && $this->post( 'mailbox_new') ) {

            $this->form_validation->set_rules('mailbox_new', 'mailbox_new', 'trim|require');

            $this->form_validation->set_rules('mailbox', 'mailbox', 'trim|require');

            if ($this->form_validation->run() === TRUE)
            {

                $mailbox               = $this->post( 'mailbox' );
                
                $mailbox_new           = $this->post( 'mailbox_new' );
                
                $this->data['mailbox'] = $this->imap->rename_mailbox($mailbox, $mailbox_new);

            }

            $this->response($this->data, 200);

        } else {

            $this->response($this->data, 403);

        }

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

        $emails               = $this->imap->paginate_mails($page,10);
        
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

            $page = $this->post('page');
            
        } else {

            $page = 1;
        }

        if($this->post('search')) {

            $criteria = $this->post('search');
            
        } else {
            
            $criteria = 'ALL';
        }

        $emails                = $this->imap->search_mails($criteria,$page,10);
        
        list($messages,$total) = $emails;
        
        $this->data['emails']  = $messages;
        
        $this->data['total']   = $total;

        $this->response($this->data, 200);
    }

}
