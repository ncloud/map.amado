<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_Map extends CI_Model
{
    function __construct()
    {
        // Call the Model constructor
        parent::__construct();    
    }
	
	function get($id)
	{
		return $this->db->from('maps')->where('id', $id)->get()->row();
	}

	function get_map_owner($id)
	{
		return $this->db->from('maps')->join('users','users.id = maps.user_id','left')->where('maps.id', $id)->select('users.*')->get()->row();
	}
	
	function get_by_permalink($permalink, $except_map_id = false)
	{
		if($except_map_id) 
			return $this->db->from('maps')->where('permalink', $permalink)->where('id !=', $except_map_id)->get()->row();
		else 
			return $this->db->from('maps')->where('permalink', $permalink)->get()->row();
	}

	function gets_all($home_mode = true, $count = null, $index = 1)
	{
		$this->db->from('maps')->join('users','maps.user_id = users.id', 'join');

		if($home_mode)  $this->db->where('is_viewed_home', 'yes');

        if($index > 1) {
            $this->db->limit($count, ($index - 1));
        } else {
            $this->db->limit($count);
        }
		
		return $this->db->select('maps.*, users.name as user_name')->get()->result();
	}

	function gets_all_by_user_id($user_id, $count = null, $index = 1)
	{
		$this->db->from('maps')->join('users','maps.user_id = users.id', 'join')->where('maps.user_id', $user_id);

        if($index > 1) {
            $this->db->limit($count, ($index - 1));
        } else {
            $this->db->limit($count);
        }
		
		return $this->db->select('maps.*, users.name as user_name')->get()->result();
	}

	function get_count()
	{
		$result = $this->db->from('maps')->select('count(*) as count')->get()->row();
		if($result) return $result->count;
		return 0;
	}

	function get_count_by_user_id($user_id)
	{
		$result = $this->db->from('maps')->where('user_id', $user_id)->select('count(*) as count')->get()->row();
		if($result) return $result->count;
		return 0;
	}

	
	function add($data) {
		$data['create_time'] = $data['update_time'] = date('Y-m-d H:i:s', mktime());

		if($this->db->insert('maps', $data)) {
			return $this->db->insert_id();
		}
		
		return false;
	}

	function update($map_id, $data) {
		return $this->db->update('maps', $data, array('id'=>$map_id));
	}

	function update_time($map_id) {
		$data = new StdClass;
		$data->update_time = date('Y-m-d H:i:s', mktime());

		return $this->db->update('maps', $data, array('id'=>$map_id));
	}
}