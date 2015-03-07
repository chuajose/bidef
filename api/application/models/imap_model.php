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

    function delete_draft($id)
    {
        $this->db->where('id_webmail_msg',$id);
        return $this->db->delete('webmail_msg');
    }


    function get_draft($user_id)
    {
        $this->db->where($this->field_usuario, $user_id);
        $query = $this->db->get('webmail_msg');
        return $query->result();

    }

    function add_draft($datos)
    {


        $this->db->insert('webmail_msg',$datos);

        return $this->db->insert_id();
    }

    function update_draft($id,$datos)
    {

        $this->db->where('id_webmail_msg',$id);
        $this->db->update('webmail_msg',$datos);

        return $id;
    }


    function get_rules($user_id=FALSE, $condition=FALSE)
    {
        if($condition) $this->db->where('condition',$condition);
        if($user_id)$this->db->where($this->field_usuario, $user_id);
        $query = $this->db->get('webmail_rules');
        if($query){
        $query = $query->row();
        }else $query = false;

        return $query;
    }    

}