<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Auth 라이브러리 (로그인, 로그아웃 등..), 연결된 파일 (  libraries/drivers 폴더내 모든 파일 )
 * password hashing.
 *
 * @from	   kohana framework
 * @license    http://kohanaphp.com/license.html	
 */
class Auth {
	
	protected $obj;
	protected $driver;
	
	/**
	 * Loads Session and configuration options.
	 *
	 * @return  void
	 */
	public function __construct()
	{
		$this->obj =& get_instance();
		
		$this->obj->config->load('auth');
		$this->obj->load->library('session');
		
		// Set the driver class name
		$driver_name = $this->obj->config->item('driver');
		$driver = 'Auth_'.$driver_name.'_Driver';

		if(!in_array($driver_name, array('File','Database'))) {
			show_error('unsupport driver');
		} else {
			include_once(APPPATH . '/libraries/drivers/Auth'.EXT);
			include_once(APPPATH . '/libraries/drivers/Auth/'.$driver_name.EXT);
		}

		// Load the driver
		$driver = new $driver($this->obj->config, $this->obj->session);

		if ( ! ($driver instanceof Auth_Driver))
			show_error('not instance of Auth_Driver');

		// Load the driver for access
		$this->driver = $driver;

		log_message('debug', 'Auth Library loaded');
	}

	/**
	 * Check if there is an active session. Optionally allows checking for a
	 * specific role.
	 *
	 * @param   string   role name
	 * @return  boolean
	 */
	public function logged_in($role = NULL)
	{
		return $this->driver->logged_in($role);
	}

	/**
	 * Returns the currently logged in user, or FALSE.
	 *
	 * @return  mixed
	 */
	public function get_user()
	{
		return $this->driver->get_user();
	}

	public function update_user($user)
	{
		return $this->driver->update_user($user);
	}

	/**
	 * Attempt to log in a user by using an ORM object and plain-text password.
	 *
	 * @param   string   username to log in
	 * @param   string   password to check against
	 * @param   boolean  enable auto-login
	 * @return  boolean
	 */
	public function login($username, $password, $remember = FALSE)
	{
		if (empty($password))
			return FALSE;

		$password = $this->password($password);
		
		return $this->driver->login($username, $password, $remember);
	}

	public function password($password) 
	{
		$salt = $this->find_salt($password);
		return $this->hash_password($password, $salt);
	}

	/**
	 * Attempt to automatically log a user in.
	 *
	 * @return  boolean
	 */
	public function auto_login()
	{
		return $this->driver->auto_login();
	}

	/**
	 * Force a login for a specific username.
	 *
	 * @param   mixed    username
	 * @return  boolean
	 */
	public function force_login($username)
	{
		return $this->driver->force_login($username);
	}

	/**
	 * Log out a user by removing the related session variables.
	 *
	 * @param   boolean  completely destroy the session
	 * @return  boolean
	 */
	public function logout($destroy = FALSE)
	{
		return $this->driver->logout($destroy);
	}

	/**
	 * Creates a hashed password from a plaintext password, inserting salt
	 * based on the configured salt pattern.
	 *
	 * @param   string  plaintext password
	 * @return  string  hashed password string
	 */
	public function hash_password($password, $salt = FALSE)
	{
		if ($salt === FALSE)
		{
			// Create a salt seed, same length as the number of offsets in the pattern
			$salt = substr($this->hash(uniqid(NULL, TRUE)), 0, count($this->obj->config->item('salt_pattern')));
		}

		// Password hash that the salt will be inserted into
		$hash = $this->hash($salt.$password);

		// Change salt to an array
		$salt = str_split($salt, 1);

		// Returned password
		$password = '';

		// Used to calculate the length of splits
		$last_offset = 0;

		foreach ($this->obj->config->item('salt_pattern') as $offset)
		{
			// Split a new part of the hash off
			$part = substr($hash, 0, $offset - $last_offset);

			// Cut the current part out of the hash
			$hash = substr($hash, $offset - $last_offset);

			// Add the part to the password, appending the salt character
			$password .= $part.array_shift($salt);

			// Set the last offset to the current offset
			$last_offset = $offset;
		}

		// Return the password, with the remaining hash appended
		return $password.$hash;
	}

	/**
	 * Perform a hash, using the configured method.
	 *
	 * @param   string  string to hash
	 * @return  string
	 */
	public function hash($str)
	{
		return hash($this->obj->config->item('hash_method'), $str);
	}

	/**
	 * Finds the salt from a password, based on the configured salt pattern.
	 *
	 * @param   string  hashed password
	 * @return  string
	 */
	public function find_salt($password)
	{
		$salt = '';

		foreach ($this->obj->config->item('salt_pattern') as $i => $offset)
		{
			// Find salt characters, take a good long look...
			if(isset($password[$offset + $i])) $salt .= $password[$offset + $i];
		}

		return $salt;
	}

} // End Auth