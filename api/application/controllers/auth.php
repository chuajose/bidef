<?php defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'libraries/REST_Controller.php';

class Auth extends REST_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->library('ion_auth');
		$this->load->helper(array('url','language'));
		$this->form_validation->set_error_delimiters('','');
		$this->lang->load('auth');		

		if(!$this->ion_auth->logged_in()) $this->ion_auth->login('admin@admin.com', 'password', TRUE);
	}

	//redirect if needed, otherwise display the user list
	function get_users_get()
	{

		if (!$this->ion_auth->logged_in())
		{
			//redirect them to the login page
			//redirect('auth/login', 'refresh');
			return $this->response(array('respuesta' => 2031), 203);
		}
		elseif (!$this->ion_auth->is_admin()) //remove this elseif if you want to enable this for non-admins
		{
			//redirect them to the home page because they must be an administrator to view this
			//return show_error('You must be an administrator to view this page.');
			return $this->response(array('respuesta' => 203), 203);
		}
		else
		{
			//list the users
			$this->data['users'] = $this->ion_auth->users()->result();
			foreach ($this->data['users'] as $k => $user)
			{
				$this->data['users'][$k]->groups = $this->ion_auth->get_users_groups($user->id)->result();
			}
			return $this->response($this->data,200);
		}
	}

	function get_user_get()
	{			
		if (!$this->ion_auth->logged_in())
		{
			//redirect them to the login page
			//redirect('auth/login', 'refresh');
			return $this->response(array('respuesta' => 2031), 203);
		}
		elseif (!$this->ion_auth->is_admin()) //remove this elseif if you want to enable this for non-admins
		{
			//redirect them to the home page because they must be an administrator to view this
			//return show_error('You must be an administrator to view this page.');
			return $this->response(array('respuesta' => 203), 203);
		}
		else
		{	
			if($user = $this->ion_auth->user($this->get('id'))->row()) return $this->response($user,200);
			else return $this->response(array('respuesta' => 29), 200);
		}
	}
	//log the user in
	function login_post()
	{
		$this->form_validation->set_rules('identity', 'Identity', 'required');
		$this->form_validation->set_rules('password', 'Password', 'required');

		if ($this->form_validation->run() == true)
		{			
			$remember = (bool) $this->post('remember');

			if ($this->ion_auth->login($this->post('identity'), $this->post('password'), $remember))
			{
				return $this->response(array('respuesta' => 0), 200);
			}
			else return $this->response(array('respuesta' => 500), 200);
		}
		else
		{
			$error = array(
                                'identity' => $this->form_validation->error('identity'),
                                'password'   => $this->form_validation->error('password')
                            );

			return $this->response($error, 200);
		}
	}

	//log the user out
	function logout_get()
	{
		$logout = $this->ion_auth->logout();		
		$this->response(array('respuesta' => 0),200);
	}

	//change password
	function change_password_post()
	{
		$this->form_validation->set_rules('old', $this->lang->line('change_password_validation_old_password_label'), 'required');
		$this->form_validation->set_rules('new', $this->lang->line('change_password_validation_new_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|max_length[' . $this->config->item('max_password_length', 'ion_auth') . ']|matches[new_confirm]');
		$this->form_validation->set_rules('new_confirm', $this->lang->line('change_password_validation_new_password_confirm_label'), 'required');

		if (!$this->ion_auth->logged_in())
		{
			return $this->response(array('respuesta' => 2031), 203);
		}

		$user = $this->ion_auth->user()->row();

		if ($this->form_validation->run() == false)
		{			
			$error = array(
                                'old' => $this->form_validation->error('old'),
                                'new'   => $this->form_validation->error('new'),
                                'new_confirm' => $this->form_validation->error('new_confirm')
                            );

			return $this->response($error, 200);
		}
		else
		{
			$identity = $this->session->userdata('identity');

			$change = $this->ion_auth->change_password($identity, $this->post('old'), $this->post('new'));

			if ($change)
			{
				//if the password was successfully changed
				$this->session->set_flashdata('message', $this->ion_auth->messages());
				$this->logout();
			}
			else
			{
				return $this->response(array('respuesta' => 500), 200);
			}
		}
	}

	//forgot password
	function forgot_password_post()
	{  
		//setting validation rules by checking wheather identity is username or email
		if($this->config->item('identity', 'ion_auth') == 'username' ) $this->form_validation->set_rules('email', $this->lang->line('forgot_password_username_identity_label'), 'required');	
		else  $this->form_validation->set_rules('email', $this->lang->line('forgot_password_validation_email_label'), 'required|valid_email');			
		
		if ($this->form_validation->run() == false)
		{
					
			$error = array('email' => $this->form_validation->error('email'));
			return $this->response($error, 200);

		}
		else
		{
			// get identity from username or email
			if ( $this->config->item('identity', 'ion_auth') == 'username' ) $identity = $this->ion_auth->where('username', strtolower($this->post('email')))->users()->row();
			else $identity = $this->ion_auth->where('email', strtolower($this->post('email')))->users()->row();
	        
	        if(empty($identity)) return $this->response(array('respuesta' => 27), 200);

			//run the forgotten password method to email an activation code to the user
			$forgotten = $this->ion_auth->forgotten_password($identity->{$this->config->item('identity', 'ion_auth')});

			if ($forgotten)	return $this->response(array('respuesta' => 0), 200);
			else return $this->response(array('respuesta' => 500), 200);	
		}
	}

	//reset password - final step for forgotten password
	public function reset_password_post($code = NULL)
	{
		if (!$code) return $this->response(array('respuesta' => 26), 200);

		$user = $this->ion_auth->forgotten_password_check($code);

		if ($user)
		{
			//if the code is valid then display the password reset form

			$this->form_validation->set_rules('new', $this->lang->line('reset_password_validation_new_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|max_length[' . $this->config->item('max_password_length', 'ion_auth') . ']|matches[new_confirm]');
			$this->form_validation->set_rules('new_confirm', $this->lang->line('reset_password_validation_new_password_confirm_label'), 'required');

			if ($this->form_validation->run() == false)
			{
				$error = array(
                                'new' => $this->form_validation->error('new'),
                                'new_confirm'   => $this->form_validation->error('new_confirm')
                            );

				return $this->response($error, 200);
			}
			else
			{
				// do we have a valid request?
				if ($this->_valid_csrf_nonce() === FALSE || $user->id != $this->post('user_id'))
				{

					//something fishy might be up
					$this->ion_auth->clear_forgotten_password_code($code);

					return $this->response(array('respuesta' => 500), 200);

				}
				else
				{
					$change = $this->ion_auth->reset_password($identity, $this->post('new'));

					if ($change) return $this->response(array('respuesta' => 0), 200);
					else return $this->response(array('respuesta' => 500), 200);
				}
			}
		}
		else
		{
			$this->response(array('respuesta' => 28), 200);
		}
	}


	/**
	 * Activación de un usuario
	 * @param id required
	 * @param code FALSE
	 * @return [type] [description]
	 */
	function activate_get()
	{
		if ($this->get('code') !== false) $activation = $this->ion_auth->activate($this->get('id'), $this->get('code'));
		if ($this->ion_auth->is_admin()) $activation = $this->ion_auth->activate($this->get('id'));

		if ($activation) return $this->response(array('respuesta' => 0), 200);
		else return $this->response(array('respuesta' => 500), 200);
	}

	/**
	 * Activación de un usuario
	 * @param id NULL
	 * @return [type] [description]
	 */
	function deactivate_post($id = NULL)
	{
		if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin())
		{
			//redirect them to the home page because they must be an administrator to view this
			return $this->response(array('respuesta' => 203), 203);
		}		
		$id = (int) $id;

		$this->load->library('form_validation');
		$this->form_validation->set_rules('confirm', $this->lang->line('deactivate_validation_confirm_label'), 'required');
		$this->form_validation->set_rules('id', $this->lang->line('deactivate_validation_user_id_label'), 'required|alpha_numeric');

		if ($this->form_validation->run() == FALSE)
		{
			$error = array(
                                'confirm' => $this->form_validation->error('confirm'),
                                'id'   => $this->form_validation->error('id')
                            );
			return $this->response($error,200);
		}
		else
		{
			// do we really want to deactivate?
			if ($this->post('confirm') == 'yes')
			{
				// do we have a valid request?
				if ($this->_valid_csrf_nonce() === FALSE || $id != $this->post('id'))
				{
					return $this->response(array('respuesta' => 500), 200);
				}

				// do we have the right userlevel?
				if ($this->ion_auth->logged_in() && $this->ion_auth->is_admin())
				{
					$this->ion_auth->deactivate($id);
				}
			}

			//redirect them back to the auth page
			return $this->response(array('respuesta' => 0), 200);
		}
	}

	//create a new user
	function create_user_post()
	{
		$this->data['title'] = "Create User";

		if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin())
		{
			return $this->response(array('respuesta' => 203), 203);
		}

		$tables = $this->config->item('tables','ion_auth');

		//validate form input
		$this->form_validation->set_rules('first_name', $this->lang->line('create_user_validation_fname_label'), 'required|xss_clean');
		$this->form_validation->set_rules('last_name', $this->lang->line('create_user_validation_lname_label'), 'required|xss_clean');
		$this->form_validation->set_rules('email', $this->lang->line('create_user_validation_email_label'), 'required|valid_email|is_unique['.$tables['users'].'.email]');
		$this->form_validation->set_rules('phone', $this->lang->line('create_user_validation_phone_label'), 'required|xss_clean');
		$this->form_validation->set_rules('company', $this->lang->line('create_user_validation_company_label'), 'required|xss_clean');
		$this->form_validation->set_rules('password', $this->lang->line('create_user_validation_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|max_length[' . $this->config->item('max_password_length', 'ion_auth') . ']|matches[password_confirm]');
		$this->form_validation->set_rules('password_confirm', $this->lang->line('create_user_validation_password_confirm_label'), 'required');

		if ($this->form_validation->run() == true)
		{
			$username = strtolower($this->post('first_name')) . ' ' . strtolower($this->post('last_name'));
			$email    = strtolower($this->post('email'));
			$password = $this->post('password');

			$additional_data = array(
				'first_name' => $this->post('first_name'),
				'last_name'  => $this->post('last_name'),
				'company'    => $this->post('company'),
				'phone'      => $this->post('phone'),
			);
		}
		else
		{
			$error = array(
								'first_name'       => $this->form_validation->error('first_name'),
								'last_name'        => $this->form_validation->error('last_name'),
								'email'            => $this->form_validation->error('email'),
								'phone'            => $this->form_validation->error('phone'),
								'password'         => $this->form_validation->error('password'),
								'password_confirm' => $this->form_validation->error('password_confirm')
                            );
			return $this->response($error, 200);
		}
		if ($this->form_validation->run() == true && $this->ion_auth->register($username, $password, $email, $additional_data))
		{
			return $this->response(array('respuesta' => 0), 200);
		}
		else return $this->response(array('respuesta' => 500), 200);
	}

	//edit a user
	function edit_user_post($id)
	{
		$this->data['title'] = "Edit User";

		if (!$this->ion_auth->logged_in() || (!$this->ion_auth->is_admin() && !($this->ion_auth->user()->row()->id == $id)))
		{
			return $this->response(array('respuesta' => 203), 203);
		}

		$user          = $this->ion_auth->user($id)->row();
		$groups        = $this->ion_auth->groups()->result_array();
		$currentGroups = $this->ion_auth->get_users_groups($id)->result();

		//validate form input
		$this->form_validation->set_rules('first_name', $this->lang->line('edit_user_validation_fname_label'), 'required|xss_clean');
		$this->form_validation->set_rules('last_name', $this->lang->line('edit_user_validation_lname_label'), 'required|xss_clean');
		$this->form_validation->set_rules('phone', $this->lang->line('edit_user_validation_phone_label'), 'required|xss_clean');
		$this->form_validation->set_rules('company', $this->lang->line('edit_user_validation_company_label'), 'required|xss_clean');
		$this->form_validation->set_rules('groups', $this->lang->line('edit_user_validation_groups_label'), 'xss_clean');

		if (isset($_POST) && !empty($_POST))
		{
			// do we have a valid request?
			if ($this->_valid_csrf_nonce() === FALSE || $id != $this->post('id'))
			{
				return $this->response(array('respuesta' => 26), 200);
			}

			//update the password if it was posted
			if ($this->post('password'))
			{
				$this->form_validation->set_rules('password', $this->lang->line('edit_user_validation_password_label'), 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|max_length[' . $this->config->item('max_password_length', 'ion_auth') . ']|matches[password_confirm]');
				$this->form_validation->set_rules('password_confirm', $this->lang->line('edit_user_validation_password_confirm_label'), 'required');
			}

			if ($this->form_validation->run() === TRUE)
			{
				$data = array(
					'first_name' => $this->post('first_name'),
					'last_name'  => $this->post('last_name'),
					'company'    => $this->post('company'),
					'phone'      => $this->post('phone'),
				);
				
				//update the password if it was posted
				if ($this->post('password'))
				{
					$data['password'] = $this->post('password');
				}

				

				// Only allow updating groups if user is admin
				if ($this->ion_auth->is_admin())
				{
					//Update the groups user belongs to
					$groupData = $this->post('groups');

					if (isset($groupData) && !empty($groupData)) {

						$this->ion_auth->remove_from_group('', $id);

						foreach ($groupData as $grp) {
							$this->ion_auth->add_to_group($grp, $id);
						}

					}
				}
				
			//check to see if we are updating the user
			    if($this->ion_auth->update($user->id, $data)) return $this->response(array('respuesta' => 0), 200);
			    else return $this->response(array('respuesta' => 500), 200);				
			}
			else
			{
				$error = array(
								'first_name' => $this->form_validation->error('first_name'),
								'last_name'  => $this->form_validation->error('last_name'),
								'email'      => $this->form_validation->error('email'),
								'phone'      => $this->form_validation->error('phone'),
								'company'    => $this->form_validation->error('company'),
								'groups'     => $this->form_validation->error('groups')
                            );
				return $this->response($error, 200);	
			}			
		}
		else return $this->response(array('respuesta' => 26), 200);
	}

	// create a new group
	function create_group_post()
	{
		$this->data['title'] = $this->lang->line('create_group_title');

		if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin())
		{
			return $this->response(array('respuesta' => 203), 203);
		}

		//validate form input
		$this->form_validation->set_rules('group_name', $this->lang->line('create_group_validation_name_label'), 'required|alpha_dash|xss_clean');
		$this->form_validation->set_rules('description', $this->lang->line('create_group_validation_desc_label'), 'xss_clean');

		if ($this->form_validation->run() == TRUE)
		{
			$new_group_id = $this->ion_auth->create_group($this->post('group_name'), $this->post('description'));
			if($new_group_id) return $this->response(array('respuesta' => 0), 200);
			else return $this->response(array('respuesta' => 500), 200);
		}
		else
		{
			$error = array(
								'group_name' => $this->form_validation->error('group_name'),
								'description'  => $this->form_validation->error('description')
                            );
			return $this->response($error, 200);	
		}
	}

	//edit a group
	function edit_group_post($id)
	{
		// bail if no group id given
		if(!$id || empty($id))
		{
			return $this->response(array('respuesta' => 26), 203);
		}

		$this->data['title'] = $this->lang->line('edit_group_title');

		if (!$this->ion_auth->logged_in() || !$this->ion_auth->is_admin())
		{
			return $this->response(array('respuesta' => 203), 203);
		}

		$group = $this->ion_auth->group($id)->row();

		//validate form input
		$this->form_validation->set_rules('group_name', $this->lang->line('edit_group_validation_name_label'), 'required|alpha_dash|xss_clean');
		$this->form_validation->set_rules('group_description', $this->lang->line('edit_group_validation_desc_label'), 'xss_clean');

		if (isset($_POST) && !empty($_POST))
		{
			if ($this->form_validation->run() === TRUE)
			{
				$group_update = $this->ion_auth->update_group($id, $_POST['group_name'], $_POST['group_description']);

				if($group_update) return $this->response(array('respuesta' => 0), 200);
				else return $this->response(array('respuesta' => 500), 200);
			}
			else
			{
				$error = array(
								'group_name' => $this->form_validation->error('group_name'),
								'group_description'  => $this->form_validation->error('group_description')
                            );
				return $this->response($error, 200);
			}
		}
		else return $this->response(array('respuesta' => 26), 203);
	}


	function _get_csrf_nonce()
	{
		$this->load->helper('string');
		$key   = random_string('alnum', 8);
		$value = random_string('alnum', 20);
		$this->session->set_flashdata('csrfkey', $key);
		$this->session->set_flashdata('csrfvalue', $value);

		return array($key => $value);
	}

	function _valid_csrf_nonce()
	{
		if ($this->post($this->session->flashdata('csrfkey')) !== FALSE &&
			$this->post($this->session->flashdata('csrfkey')) == $this->session->flashdata('csrfvalue'))
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

}
