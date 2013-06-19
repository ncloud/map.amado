<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * File Auth driver.
 * Note: this Auth driver does not support roles nor auto-login.
 *
 * @from       Kohana Framework
 * @license    http://kohanaphp.com/license.html
 */
class Auth_File_Driver extends Auth_Driver {

	// User list
	protected $users;

	/**
	 * Constructor loads the user list into the class.
	 */
	public function __construct($config, $session)
	{
		parent::__construct($config, $session);
		

		// Load user list
		$this->users = $config->item('users');
	//	$this->users = empty($config->item('users')) ? array() : $config->item('users');
	}

	/**
	 * Logs a user in.
	 *
	 * @param   string   username
	 * @param   string   password
	 * @param   boolean  enable auto-login (not supported)
	 * @return  boolean
	 */
	public function login($username, $password, $remember)
	{
		if (isset($this->users[$username]) AND $this->users[$username] === $password)
		{
			// Complete the login
			return $this->complete_login($username);
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
	public function force_login($username)
	{
		// Complete the login
		return $this->complete_login($username);
	}

	/**
	 * Get the stored password for a username.
	 *
	 * @param   mixed   username
	 * @return  string
	 */
	public function password($username)
	{
		return isset($this->users[$username]) ? $this->users[$username] : FALSE;
	}

} // End Auth_File_Driver