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

class User extends REST_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->library('user_library');
        $this->form_validation->set_error_delimiters('', '');
    }

	function get_user_get() {
        $user_id = $this->form_validation->required($this->get('user'));
        if(!$user_id) $this->response(array('respuesta' => 33), 200);
        if($user = $this->user_library->get_user($user_id))	$this->response($user, 200);
        else $this->response(array('respuesta' => 32), 204);
	}

    function insert_user_post()
    {
        $this->form_validation->set_rules('dni', 'dni', 'trim|dni');
        $this->form_validation->set_rules('name', 'name', 'trim|required');
        $this->form_validation->set_rules('first_name', 'first_name', 'trim|required');
        $this->form_validation->set_rules('last_name', 'last_name', 'trim|required');
        $this->form_validation->set_rules('email', 'email', 'trim|valid_email|required|is_unique[user.email]');
        $this->form_validation->set_rules('bank_acount', 'bank_acount', 'trim|is_unique[user.bank_acount]');
        if ($this->form_validation->run() === FALSE)
        {
            $error = array(
                                'dni'         => $this->form_validation->error('dni'),
                                'name'        => $this->form_validation->error('name'),
                                'first_name'  => $this->form_validation->error('first_name'),
                                'last_name'   => $this->form_validation->error('last_name'),
                                'email'       => $this->form_validation->error('email'),
                                'bank_acount' => $this->form_validation->error('bank_acount'),
                            );
            $this->response($error, 200);
        }

        if($user_inserted = $this->user_library->insert_user($this->post())) $this->response(array('respuesta'=>0), 200);
        else  $this->response(array('respuesta'=>1), 200);
    }

    function list_users_get()
    {
        if($user_list = $this->user_library->list_users()) $this->response($user_list, 200);
        else  $this->response(array('respuesta'=>1), 200);
    }

}
