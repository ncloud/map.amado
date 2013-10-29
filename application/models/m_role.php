<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_Role extends CI_Model
{
    function gets_all() {
    	return $this->db->from('roles')->order_by('level ASC')->get()->result();
    }

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

	public function get_by_invite_code($code)
	{
		return $this->db->from('role_users')->join('roles','roles.id = role_users.role_id','left')->where('role_users.invite_code', $code)->select('role_users.*, roles.name as role_name, roles.description as role_description')->get()->row();
	}

	function get_by_map_and_user_id($map_id, $user_id)
	{
		return $this->db->from('role_users')->join('roles','roles.id = role_users.role_id','left')->where('role_users.map_id', $map_id)->where('role_users.user_id', $user_id)->select('role_users.*, roles.name as role_name, roles.description as role_description')->get()->row();		
	}

	public function get_id_by_name($name)
	{
		$result = $this->db->from('roles')->where('name', $name)->get()->row();
		if($result) return $result->id;
		else return false;
	}
	
	public function get_role($map_id, $user_id)
	{
		$row1 = $this->db->from('role_users')->where(array('role_users.map_id'=>null, 'role_users.user_id'=>$user_id))->join('roles','roles.id = role_users.role_id','left')->select('roles.*')->get()->row();
		$row2 = $this->db->from('role_users')->where(array('role_users.map_id'=>$map_id, 'role_users.user_id'=>$user_id))->join('roles','roles.id = role_users.role_id','left')->select('roles.*')->get()->row();
		
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

	public function gets_by_map_id($map_id)
	{
		return $this->db->from('role_users')->join('roles','roles.id = role_users.role_id', 'left')->join('users', 'users.id = role_users.user_id', 'left')->where('role_users.map_id', $map_id)
						->select('users.id, users.name, role_users.role_id, roles.name as role_name, roles.level as role_level, roles.description as role_description, role_users.invite_status as role_invite_status, role_users.invite_code as role_invite_code')->get()->result();
	}
	
	public function user_check($map_id, $user_id, $role_id)
	{
		$result = $this->db->get_where('role_users', array('map_id'=>$map_id, 'user_id'=>$user_id, 'role_id'=>$role_id))->row();
		if($result) 
		{
			return true;
		}
		else 
		{
			return false;
		}
	}

	public function user_add($map_id, $user_id, $role_id, $invite_email = '') {
		$data = new StdClass;

		$data->map_id = $map_id;
		$data->user_id = $user_id;
		$data->role_id = $role_id;

		if(!empty($invite_email)) {
			$data->invite_status = 'send_email';
			$data->invite_email = $invite_email;
			$data->invite_code = $map_id.substr(md5(mktime().$user_id.$role_id.$invite_email), 0, 28);
		}

		$data->insert_time = date('Y-m-d H:i:s', mktime());

	 	if($this->db->insert('role_users', $data)) {
	 		$data->id = $this->db->insert_id();
	 		return $data;
	 	} else {
	 		return false;
	 	}
	}

	public function user_delete($map_id, $user_id) 
	{
		return $this->db->delete('role_users', array('map_id'=>$map_id, 'user_id'=>$user_id));
	}

	public function user_delete_by_code($code) 
	{
		return $this->db->delete('role_users', array('invite_code'=>$code));
	}

	public function delete_by_map_id($map_id)
	{
		return $this->db->delete('role_users', array('map_id'=>$map_id));		
	}

	public function update_invite_user($code, $user_id)
	{
		$data = new StdClass;
		$data->user_id = $user_id;
		$data->invite_status = 'invited';

		return $this->db->update('role_users', $data, array('invite_code'=>$code));
	}
     
    public function update_user_role($map_id, $user_id, $role_id)
    {
    	$data = new StdClass;
    	$data->role_id = $role_id;
    	
 		return $this->db->update('role_users', $data, array('map_id'=>$map_id, 'user_id'=>$user_id));
    }
}//END class