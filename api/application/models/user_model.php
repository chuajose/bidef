<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


	class User_model extends CI_Model {
		
		function __construct()
		{
	        parent::__construct();
	    }	   

/*	    function insert_user($data)
	    {
	    	return $this->db->insert('user', $data);
	    }

	    function get_user_data($user_id)
	    {
	    	$this->db->where('user_id', $user_id);
	    	$query = $this->db->get('user');
	    	$query = $query->result();
	    	return $query;
	    }
	    
	    function list_users()
	    {
	    	$query = $this->db->get('user');
	    	$query = $query->result();
	    	return $query; 
	    }*/

	    /*-deleagtions-*/
	    function insert_delegation($delegation_name, $fid_provincia = FALSE)
	    {
	    	if($fid_provincia)
	    	{
	    		$this->db->trans_start();
		    	$this->db->insert('delegation', $delegation_name);
		    	$id = $this->db->insert_id();
		    	$this->db->insert('delegation_has_provincia', array('fid_delegation' => $delegation_name, 'fid_provincia' => $fid_provincia));
		    	$this->db->trans_complete();
		    	if ($this->db->trans_status() === FALSE) return FALSE;
		    	else return TRUE;	
	    	}
	    	else
	    	{
	    		$this->db->insert('delegation', array('delegation_name' => $delegation_name));
		    	if($id = $this->db->insert_id()) return TRUE;
		    	else return FALSE;	
	    	}
	    }

	    function get_delegation_data($delegation_id)
	    {
	    	$this->db->where('id_delegation', $delegation_id);
	    	$query = $this->db->get('delegation');
	    	$query = $query->result();
	    	return $query;
	    }
	    /**
		 * [list_delegations description]
		 * @param  boll|int|array  $id [description]
		 * @return bool|obj
		 */
	    function list_delegations($id)
	    {
	    	if($id) 
	    	{
	    		if(is_array($id)) $this->db->where_in('id_delegation', $id);
	    		else $this->db->where('id_delegation', $id);
	    	}
	    	$query = $this->db->get('delegation');
	    	if($query->num_rows()) return $query = $query->result();
	    	else return FALSE; 
	    }
	    function update_delegation($id, $name){
	    	$this->db->where('id_delegation', $id);	    	
	    	$return = $this->db->update('delegation', array('delegation_name' => $name));
	    	if($return) return TRUE;
	    	return FALSE;
	    }

	    function delete_delegations($id){
	    	if($id) 
	    	{
	    		if(is_array($id)) $this->db->where_in('id_delegation', $id);
	    		else $this->db->where('id_delegation', $id);
	    	}
	    	$query = $this->db->delete('delegation');
	    	if($query) return TRUE;
	    	else return FALSE; 
	    }

	    function list_provincias(){
	    	$query = $this->db->get('provincia');
	    	if($query->num_rows() > 0) return $query->result();
	    	else return FALSE;
	    }

	}