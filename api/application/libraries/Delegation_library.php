<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CodeIgniter User Library
 *
 * Take all information about a user, and several activities more like, create, delete, etc
 *
 * @package        	CodeIgniter
 * @subpackage    	Libraries
 * @category    	Libraries
 * @author        	Cristobal Terceiro  
 */
class Delegation_library
{
	private $CI;
	var $id_delegation;
	var $delegation_name;
	var $id_provincia;
	var $nombre_provincia;
	var $fid_comunidad;

	/**
	 * Constructor function
	 * @todo Document more please.
	 */
	public function __construct($user_id = FALSE)
	{
		$this->CI =& get_instance();
		$this->CI->load->model('user_model');
		if($user_id) $this->get($user_id);
	}

	function get_delegation($delegation_id)
	{
		if($user_data = $this->CI->user_model->get_delegation_data($user_id))
		{
			foreach ($user_data as $key => $data)
			{
				$user[$key] = $data;
				$this->$key = $data;
			}
			return $user;
		}
	}
	
	
	function insert_delegation($delegation_name, $color)
	{		
		if($delegation_inserted = $this->CI->user_model->insert_delegation($delegation_name, $color)) return $delegation_inserted;
		else return FALSE;
	}
}
