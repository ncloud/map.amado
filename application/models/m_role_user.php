<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_Role_User extends CI_Model
{
    //----------------------- PUBLIC METHODS --------------------------//
    
	public function check($user_id, $role_id)
	{
		$result = $this->db->get_where('role_users', array('user_id'=>$user_id, 'role_id'=>$role_id))->row();
		if($result) 
		{
			return true;
		}
		else 
		{
			return false;
		}
	}
	
    //----------------------- STATIC METHODS --------------------------//
    //----------------------- PRIVATE METHODS --------------------------//
     
}//END class