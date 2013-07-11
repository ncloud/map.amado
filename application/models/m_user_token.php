<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_User_Token extends CI_Model
{
	protected $now;

    //----------------------- PUBLIC METHODS --------------------------//
    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
        
        $this->now = time();
    }
    
    public function get($id)
    {
    	if(is_numeric($id))
    	{
    		return $this->db->get_where('user_tokens', array('id'=>$id))->row();
    	}
    	else if(is_string($id))
    	{
    	    return $this->db->get_where('user_tokens', array('token'=>$id))->row();
    	}
    	else 
    	{
    		return false;
    	}
    }
    
    public function get_token($id) {
        $result = $this->get($id);
        if($result) return $result->token;
        return false;
    }
    
	public function get_by_userid($user_id, $vendor_id = 0)
    {
		return $this->db->get_where('user_tokens', array('user_id'=>$user_id, 'vendor_id'=>$vendor_id))->row();
    }
    

    public function update($user_id, $lifetime, $vendor_id = 0, $token = '', $secret = null) 
    {
    	$this->load->library('user_agent');
		
		$old_token = $this->get_by_userid($user_id, $vendor_id);

		if(empty($old_token)) {
			// Set token data
			$new_token				= new StdClass;
			$new_token->user_id 	= $user_id;
			$new_token->vendor_id	= $vendor_id;
			$new_token->expires 	= time() + $lifetime;
			$new_token->user_agent 	= sha1($this->agent->agent_string());
			$new_token->token		= empty($token)?$this->create_token():$token;
			$new_token->secret		= $secret;
            $new_token->can_use     = 'yes';
			$new_token->created 	= $this->now;

    		$this->db->insert('user_tokens', $new_token);
            
            return $new_token;
		} else {
			$old_token->expires 	= time() + $lifetime;
			$old_token->user_agent 	= sha1($this->agent->agent_string());
			$old_token->token		= empty($token)?$this->create_token():$token;
			$old_token->secret		= $secret;
            $old_token->can_use     = 'yes';
			$old_token->created 	= $this->now;

			$this->db->where('id', $old_token->id);
			$this->db->update('user_tokens', $old_token);
        
            return $old_token;
		}
    }
    
    public function delete($id) 
    {
    	$this->db->where('id', $id);
    	$this->db->delete('user_tokens'); 
    }
    
    //----------------------- STATIC METHODS --------------------------//
	
	/**
	 * Finds a new unique token, using a loop to make sure that the token does
	 * not already exist in the database. This could potentially become an
	 * infinite loop, but the chances of that happening are very unlikely.
	 *
	 * @return  string
	 */
	protected function create_token()
	{
		$this->load->helper('string');
		
		while (TRUE)
		{
			// Create a random token
			$token = random_string('alnum', 32);

			// Make sure the token does not already exist
			if ($this->db->from('user_tokens')->select('id')->where('token', $token)->get()->count_all_results() === 0)
			{
				// A unique token has been found
				return $token;
			}
		}
	}
	
    //----------------------- PRIVATE METHODS --------------------------//
    
     
}//END class