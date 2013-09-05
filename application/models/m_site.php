<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_Site extends CI_Model
{
    function __construct()
    {
        // Call the Model constructor
        parent::__construct();    
    }
	
	function get($id)
	{
		return $this->db->from('sites')->where('id', $id)->get()->row();
	}
	
	function get_by_permalink($permalink)
	{
		return $this->db->from('sites')->where('permalink', $permalink)->get()->row();
	}

	function gets_all($count = null, $index = 1)
	{
		$this->db->from('sites')->join('users','sites.user_id = users.id', 'join');

        if($index > 1) {
            $this->db->limit($count, ($index - 1));
        } else {
            $this->db->limit($count);
        }
		
		return $this->db->select('sites.*, users.name as user_name')->get()->result();
	}

	function gets_all_by_user_id($user_id, $count = null, $index = 1)
	{
		$this->db->from('sites')->join('users','sites.user_id = users.id', 'join')->where('sites.user_id', $user_id);

        if($index > 1) {
            $this->db->limit($count, ($index - 1));
        } else {
            $this->db->limit($count);
        }
		
		return $this->db->select('sites.*, users.name as user_name')->get()->result();
	}

	function get_count()
	{
		$result = $this->db->from('sites')->select('count(*) as count')->get()->row();
		if($result) return $result->count;
		return 0;
	}

	function get_count_by_user_id($user_id)
	{
		$result = $this->db->from('sites')->where('user_id', $user_id)->select('count(*) as count')->get()->row();
		if($result) return $result->count;
		return 0;
	}
}