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
	    function insert_delegation($delegation_name, $color, $fid_provincia = FALSE)
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
	    		$this->db->insert('delegation', array('delegation_name' => $delegation_name, 'color' => $color));
		    	if($id = $this->db->insert_id()) return $id;
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
	    function update_delegation($id, $name = FALSE, $color= FALSE){
	    	$this->db->where('id_delegation', $id);
	    	$data = array();
	    	if($name) $data['delegation_name'] = $name;
	    	if($color) $data['color'] = $color;
	    	$return = $this->db->update('delegation', $data);
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

	    function check_color_delegation($color)
	    {
	    	$this->db->where('color', $color);
	    	$query = $this->db->get('delegation');
	    	if($query->num_rows() > 0) return TRUE;
	    	else return FALSE;

	    }

	    function list_provincias()
	    {
	    	$query = $this->db->get('provincia');
	    	if($query->num_rows() > 0) return $query->result();
	    	else return FALSE;
	    }

	    function delegation_map_provincias()
	    {
	    	$this->db->select('color, vectormap_code');
	    	//$this->db->select('*');
			$this->db->from('delegation_has_provincia');
			$this->db->join('provincia', 'delegation_has_provincia.fid_provincia = provincia.id_provincia', 'inner');
			$this->db->join('delegation', 'delegation_has_provincia.fid_delegation = delegation.id_delegation', 'inner');
			$query = $this->db->get();
	    	$query = $query->result_array();
	    	return $query;
	    }
	    /**
	     * [update_delegation_map_provincias description]
	     * @param  int $id_delegation
	     * @param  array $code_map  codigos de las provincias (code_amp en tabla provincia)
	     * @return bool                
	     */
	    function update_delegation_map_provincias($data)
	    {
	    	$this->db->insert_batch('delegation_has_provincia', $data);
	    }

	    function get_provincia_by_map_code($map_code)
	    {
	    	$this->db->select('id_provincia');
	    	$this->db->where('vectormap_code', $map_code);
	    	$query = $this->db->get('provincia');
	    	$query = $query->row('id_provincia');
	    	return $query;
	    }

	}