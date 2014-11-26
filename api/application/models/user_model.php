<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


	class User_model extends CI_Model {
		
		function __construct()
		{
	        parent::__construct();
	    }	   

	    function insert_user($data)
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
	    }

	    

	}