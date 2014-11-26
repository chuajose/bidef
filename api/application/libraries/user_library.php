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
class User_Library
{
	private $CI;
	var $user_id;
	var $name;
	var $first_name;
	var $last_name;
	var $email;
	var $bank_account;
	var $photo;
	var $birth_date;
	var $discharge_date;
	var $withdrawal_date;
	var $status;

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

	function get_user($user_id)
	{
		if($user_data = $this->CI->user_model->get_user_data($user_id))
		{
			foreach ($user_data as $key => $data)
			{
				$user[$key] = $data;
				$this->$key = $data;
			}
			return $user;
		}
	}

	function list_users()
	{
		if($user_list = $this->CI->user_model->list_users()) return $user_list;
		else return FALSE;
	}

	function insert_user($data)
	{		
		if($user_inserted = $this->CI->user_model->insert_user($data)) return TRUE;
		else return FALSE;
	}
}
