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
require APPPATH.'controllers/auth.php';

class Delegation extends Auth
{
    function __construct()
    {
        parent::__construct();
        $this->load->library('delegation_library');
        $this->load->model('user_model');
        $this->form_validation->set_error_delimiters('', '');
    }

    function index_get(){
        $this->response(array('respuesta' => 33), 200);
    }

    function insert_delegation_post()
    {   
        $this->form_validation->set_rules('delegation_name', 'delegation_name', 'trim|required|is_unique[delegation.delegation_name]');
        $this->form_validation->set_rules('color', 'color', 'trim|required|is_unique[delegation.color]');
        if ($this->form_validation->run() === FALSE)
        {
            $error = array(
                                'delegation_name' => $this->form_validation->error('delegation_name'),
                                'color' => $this->form_validation->error('color'),
                            );
            $this->response($error, 200);
        }

        if($delegation_inserted = $this->delegation_library->insert_delegation($this->post('delegation_name'), $this->post('color'))) $this->response(array('respuesta'=>$delegation_inserted), 200);
        else  $this->response(array('respuesta'=>-1), 200);
    }
    /**
     * get_delegations?id[]=1&id[]=2
     * @return bool|array|int [description]
     */
    function get_delegations_get()
    {
        if($delegations_list = $this->user_model->list_delegations($this->get('id'))) $this->response($delegations_list, 200);
        else  $this->response(array('respuesta'=>29), 200);   
    }

    function get_delegation_get()
    {
        if($delegation_data = $this->user_model->get_delegation_data($this->get('id'))) $this->response($delegation_data, 200);
        else  $this->response(array('respuesta'=>29), 200);   
    }

    function update_delegation_post()
    {
        $this->form_validation->set_rules('delegation_name', 'delegation_name', 'trim|is_unique[delegation.delegation_name]');
        $this->form_validation->set_rules('color', 'color', 'trim|is_unique[delegation.color]');
        $this->form_validation->set_rules('id', 'id', 'trim|required|is_natural_no_zero');
        if ($this->form_validation->run() === FALSE)
        {
            $error = array(
                                'delegation_name' => $this->form_validation->error('delegation_name'),
                                'color' => $this->form_validation->error('color'),
                                'id' => $this->form_validation->error('id')
                            );
            $this->response($error, 200);            
        }
        else 
        {
            if($this->user_model->update_delegation($this->post('id'), $this->post('delegation_name') , $this->post('color'))) return $this->response(array('respuesta' => 0), 200);
            else $this->response(array('respuesta'=> 500), 200);
        }
    }    
    
    /*function update_color_delegation()
    {
        if($this->user_model->update_color_delegation($this->get('id_delegation'), $this->get('color'))) return $this->response(array('respuesta' => 0, 200));
        else return $this->response(array('respuesta' => 500, 200));
    }*/
    
    /**
     * delete_delegations?id[]=1&id[]=2
     * @return bool|array|int [description]
     */
    function delete_delegations_post()
    {
        $this->form_validation->set_rules('id', 'id', 'required');
        if ($this->form_validation->run() === FALSE)
        {
            $this->response(array('id' => $this->form_validation->error('id')), 200);
        }
        else
        {
            if($this->user_model->delete_delegations($this->post('id'))) return $this->response(array('respuesta' => 0), 200);
            else $this->response(array('respuesta'=> 500), 200);
        }
    }

    function check_color_delegation_get()
    {
        if($this->user_model->check_color_delegation($this->get('color'))) $this->response(array('respuesta'=>302), 200);
        else $this->response(array('respuesta'=>301), 200);
    }

    function list_provincias_get()
    {
        if($provincias = $this->user_model->list_provincias()) return $this->response($provincias, 200);
        else $this->response(array('respuesta' => 500, ), 200);
    }

    function delegation_map_provincias_get()
    {
        if($delegacion_map = $this->user_model->delegation_map_provincias())
        {
            foreach ($delegacion_map as $key => $value)
            {
                $delegacion_map_value[$value['vectormap_code']] = $value['color'];
            }   
            return $this->response($delegacion_map_value, 200);
        }
    }

    function update_delegation_map_provincias_post()
    {
        $code_map    = explode(',', $this->post('vectormap_code'));
        $data_insert = array();        
        $data_update = array();
        $provincia   = '';        
        $error       = 0;
        if($provincias_exists = $this->user_model->provincias_has_delegations_all())
        {
            foreach ($provincias_exists as $key => $provincia) $provincia_check[] = $provincia['fid_provincia'];
        }
        foreach ($code_map as $key => $value)
        {
            
            $provincia = (int) $this->user_model->get_provincia_by_map_code($value);
            if(in_array($provincia, $provincia_check))
            {
                $data_update[$key]['fid_delegation'] = $this->post('delegation_id');
                $data_update[$key]['fid_provincia'] = $provincia;
            }
            else
            {
                $data_insert[$key]['fid_delegation'] = $this->post('delegation_id');
                $data_insert[$key]['fid_provincia'] = $provincia;
            }             
        }        
        if(!empty($data_update))
        {
            if($this->user_model->update_delegation_map_provincias($data_update)) $error = 0;//return $this->response(array('respuesta' => 0), 200);
            else $error = 1;
        }
        if(!empty($data_insert))
        {
            if($this->user_model->insert_delegation_map_provincias($data_insert)) $error = 0;
            else $error = 1;
        }
        if($error) return $this->response(array('respuesta'=> -1),200);   
        else return $this->response(array('respuesta' => 0), 200);
    }

    function update_delete_delegation_map_provincias_post()
    {
        $code_map    = explode(',', $this->post('vectormap_code'));
        foreach ($code_map as $key => $value)
        {
            
            $provincia[] = (int) $this->user_model->get_provincia_by_map_code($value);
        }
        if($this->user_model->update_delete_delegation_map_provincias($provincia)) return $this->response(array('respuesta' => 0), 200);
        else $this->response(array('respuesta'=> -1),200);
    }

}
