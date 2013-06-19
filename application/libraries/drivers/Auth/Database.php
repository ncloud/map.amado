<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * ORM Auth driver.
 *
 * @from	   Kohana Framework
 * @license    http://kohanaphp.com/license.html
 */
class Auth_Database_Driver extends Auth_Driver {
	protected $obj;
	
	/**
	 * Constructor loads the user list into the class.
	 */
	public function __construct($config, $session)
	{
		parent::__construct($config, $session);
		
		$this->obj =& get_instance();
	}
	
	/**
	 * Checks if a session is active.
	 *
	 * @param   string   role name
	 * @param   array    collection of role names
	 * @return  boolean
	 */
	public function logged_in($role)
	{
		$this->obj->load->model('m_role');
		$this->obj->load->model('m_role_user');
		$this->obj->load->model('m_user');
		
		$status = FALSE;

		// Get the user from the session
		$user = $this->session->userdata($this->config->item('session_key'));

		if (is_object($user))
		{
			// Everything is okay so far
			$status = TRUE;

			if ( ! empty($role))
			{

				// If role is an array
				if (is_array($role))
				{
					// Check each role
					foreach ($role as $role_iteration)
					{
						if ( ! is_object($role_iteration))
						{
							$role_iteration = $this->obj->m_role->get($role_iteration);
						}
						// If the user doesn't have the role
						if( ! $this->obj->m_role_user->check($user->id, $role_iteration->id))
						{
							// Set the status false and get outta here
							$status = FALSE;
							break;
						}
					}
				}
				else
				{
				// Else just check the one supplied roles
					if ( ! is_object($role))
					{
						// Load the role
						$role = $this->obj->m_role->get($role);
					}

					// Check that the user has the given role
					$status = $this->obj->m_role_user->check($user->id, $role->id);
				}
			}
		}

		return $status;
	}

	/**
	 * Logs a user in.
	 *
	 * @param   string   username
	 * @param   string   password
	 * @param   boolean  enable auto-login
	 * @return  boolean
	 */
	public function login($user, $password, $remember)
	{
		$this->obj->load->model('m_role');
		$this->obj->load->model('m_role_user');
		$this->obj->load->model('m_user');
		$this->obj->load->model('m_user_token');

		$this->obj->load->helper('cookie');
        
		if ( ! is_object($user))
		{
			// Load the user
			$user = $this->obj->m_user->get_by_username($user);
		}
		
		$role = $this->obj->m_role->get('login');
		
		// If the passwords match, perform a login
		if ($this->obj->m_role_user->check($user->id, $role->id) && $user->password === $password)
		{
			if ($remember === TRUE)
			{	
				$token = $this->obj->m_user_token->update($user->id, $this->config->item('lifetime'));

				// Set the autologin cookie
				set_cookie('authautologin', $token->token, $this->config->item('lifetime'));
			}
			
			// Finish the login
			$this->complete_login($user);

			return TRUE;
		}

		// Login failed
		return FALSE;
	}

	/**
	 * Forces a user to be logged in, without specifying a password.
	 *
	 * @param   mixed    username
	 * @return  boolean
	 */
	public function force_login($user)
	{
		$this->obj->load->model('m_user');
			
		if ( ! is_object($user))
		{
			// Load the user
			$user = $this->obj->m_user->get($user);
		}

		// Mark the session as forced, to prevent users from changing account information
		$_SESSION['auth_forced'] = TRUE;

		// Run the standard completion
		$this->complete_login($user);
	}

	/**
	 * Logs a user in, based on the authautologin cookie.
	 *
	 * @return  boolean
	 */
	public function auto_login()
	{
		$this->obj->load->library('user_agent');
		$this->obj->load->model('m_user');
		$this->obj->load->model('m_user_token');
		$this->obj->load->helper('cookie');
			
		if ($token = get_cookie('authautologin'))
		{
			// Load the token and user
			$token = $this->obj->m_user_token->get($token);

			if (!empty($token))
			{
				if ($token->user_agent === sha1($this->agent->agent_string()))
				{
					// Save the token to create a new unique token
					$this->obj->db->where('id', $token->id);
					$this->obj->db->update('users_tokens', $token);

					// Set the new token
					set_cookie('authautologin', $token->token, $token->expires - time());
					
					$user = $this->obj->m_user->get($token->user_id);
					
					// Complete the login with the found data
					$this->complete_login($user);

					// Automatic login was successful
					return TRUE;
				}

				// Token is invalid
				$this->obj->m_user_token->delete($token->id);
			}
		}

		return FALSE;
	}

	/**
	 * Log a user out and remove any auto-login cookies.
	 *
	 * @param   boolean  completely destroy the session
	 * @return  boolean
	 */
	public function logout($destroy)
	{
		$this->obj->load->model('m_user_token');
		$this->obj->load->helper('cookie');
	
		if ($token = get_cookie('authautologin'))
		{
			// Delete the autologin cookie to prevent re-login
			delete_cookie('authautologin');
			
			// Clear the autologin token from the database
			$token = $this->m_user_token->get($token);
			
			if (!empty($token))
			{
				$this->obj->m_user_token->delete($token->id);
			}
		}

		return parent::logout($destroy);
	}

	/**
	 * Get the stored password for a username.
	 *
	 * @param   mixed   username
	 * @return  string
	 */
	public function password($user)
	{		
		$this->obj->load->model('m_user');
	
		if ( ! is_object($user))
		{
			// Load the user
			$user = $this->obj->m_user->get_by_username($user);
		}
		return $user->password;
	}

	/**
	 * Complete the login for a user by incrementing the logins and setting
	 * session data: user_id, username, roles
	 *
	 * @param   object   user model object
	 * @return  void
	 */
	protected function complete_login($user)
	{
		$this->obj->load->model('m_user');
		
		$this->obj->m_user->update_login_count($user->id);
		
		return parent::complete_login($user);
	}

} // End Auth_ORM_Driver