<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Imap_model extends CI_Model {
	
	function __construct()
	{
        parent::__construct();

        $this->field_usuario = "fid_usuario";
    }	   

    /**
     * Devuelve los datos de webmail del usuario
     *
     * @param int $user_id usuario de la aplicacion
     * @return object | FALSE en caso de no existir usuario
     */
    function get($user_id)
    {
    	$this->db->where($this->field_usuario, $user_id);
    	$query = $this->db->get('webmail');
    	$query = $query->row();
    	return $query;
    }    

}