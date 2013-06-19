<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Abstract Auth driver, must be extended by all drivers.
 *
 * @from   	   Kohana Framework
 * @license    http://kohanaphp.com/license.html
 */
abstract class Auth_Driver {

	// Session instance
	protected $session;

	// Configuration
	protected $config;

	/**
	 * Creates a new driver instance, loading the session and storing config.
	 *
	 * @param   array  configuration
	 * @return  void
	 */
	public function __construct($config, $session)
	{
		// Load Session
		$this->session = $session;

		// Store config
		$this->config = $config;
	}

	/**
	 * Checks if a session is active.
	 *
	 * @param   string   role name (not supported)
	 * @return  boolean
	 */
	public function logged_in($role)
	{
		return $this->session->userdata($this->config->item('session_key'));
	}

	/**
	 * Gets the currently logged in user from the session.
	 * Returns FALSE if no user is currently logged in.
	 *
	 * @return  mixed
	 */
	public function get_user()
	{
		if ($this->logged_in(NULL))
		{
			return $this->session->userdata($this->config->item('session_key'));
		}

		return FALSE;
	}

	public function update_user($user)
	{
		if ($this->logged_in(NULL))
		{
			$data = $this->session->set_userdata($this->config->item('session_key'), $user);
		}

		return FALSE;
	}

	/**
	 * Logs a user in.
	 *
	 * @param   string   username
	 * @param   string   password
	 * @param   boolean  enable auto-login
	 * @return  boolean
	 */
	abstract public function login($username, $password, $remember);

	/**
	 * Forces a user to be logged in, without specifying a password.
	 *
	 * @param   mixed    username
	 * @return  boolean
	 */
	abstract public function force_login($username);

	/**
	 * Logs a user in, based on stored credentials, typically cookies.
	 * Not supported by default.
	 *
	 * @return  boolean
	 */
	public function auto_login()
	{
		return FALSE;
	}

	/**
	 * Log a user out.
	 *
	 * @param   boolean  completely destroy the session
	 * @return  boolean
	 */
	public function logout($destroy)
	{
		if ($destroy === TRUE)
		{
			// Destroy the session completely
			$this->session->sess_destroy();
		}
		else
		{
			// Remove the user from the session
			$this->session->unset_userdata($this->config->item('session_key'));

			// Regenerate session_id
			$this->session->sess_create();
		}

		// Double check
		return ! $this->logged_in(NULL);
	}

	/**
	 * Get the stored password for a username.
	 *
	 * @param   mixed   username
	 * @return  string
	 */
	abstract public function password($username);

	/**
	 * Completes a login by assigning the user to the session key.
	 *
	 * @param   string   username
	 * @return  TRUE
	 */
	protected function complete_login($user)
	{
		// Regenerate session_id
		$this->session->sess_create();

		// Store username in session
		$this->session->set_userdata($this->config->item('session_key'), $user);

		return TRUE;
	}

} // End Auth_Driver