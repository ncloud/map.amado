<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_Role extends CI_Model
{
    //----------------------- PUBLIC METHODS --------------------------//
    
	public function get($id)
	{
		if(is_numeric($id)) 
		{
			return $this->db->get_where('roles', array('id'=>$id))->row();
		}
		else if(is_string($id)) 
		{
			return $this->db->get_where('roles', array('name'=>$id))->row();
		}
		else 
		{
			return false;
		}
	}
	
	public function get_role($site_id, $user_id)
	{
		$row1 = $this->db->from('role_users')->where(array('role_users.site_id'=>null, 'role_users.user_id'=>$user_id))->join('roles','roles.id = role_users.role_id','left')->select('roles.*')->get()->row();
		$row2 = $this->db->from('role_users')->where(array('role_users.site_id'=>$site_id, 'role_users.user_id'=>$user_id))->join('roles','roles.id = role_users.role_id','left')->select('roles.*')->get()->row();
		
		if($row1 && $row2) {
			if($row1->level > $row2->level) {
				return $row1->name;
			} else {
				return $row2->name;
			}
		} else {
			if($row1 && !$row2) return $row1->name;
			else if(!$row1 && $row2) return $row2->name;
		}
		return false;
	}
	
	public function user_check($site_id, $user_id, $role_id)
	{
		$result = $this->db->get_where('role_users', array('site_id'=>$site_id, 'user_id'=>$user_id, 'role_id'=>$role_id))->row();
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