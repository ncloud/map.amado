<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_Course extends CI_Model
{
    function __construct()
    {
        // Call the Model constructor
        parent::__construct();    
    }

	function get($id)
	{
		return $this->db->from('courses')->where('id', $id)->get()->row();
	}
	
	function gets($site_id)
	{
		return $this->db->from('courses')->where('site_id', $site_id)->where('status','approved')->get()->result();
	}

	function gets_all($site_id, $count, $index = 1)
	{
        if($index > 1) {
            $this->db->limit($count, ($index - 1));
        } else {
            $this->db->limit($count);
        }
		
		return $this->db->from('courses')->where('site_id', $site_id)->order_by('id DESC')->get()->result();		
	}
	
	function gets_targets($course_id)
	{
		return $this->db->from('course_targets')->where('course_id', $course_id)->order_by('order_index ASC')->get()->result();
	}
	
	function get_count($site_id)
	{
		$result = $this->db->from('courses')->where('site_id', $site_id)->select('count(*) as count')->get()->row();
		if($result) return $result->count;
		return false;
	}
	
	function add($data) {
		if($this->db->insert('courses', $data)) {
			return $this->db->insert_id();
		}
		
		return false;
	}
	
	function update($id, $data) {
		$this->db->where('id', $id);
		return $this->db->update('courses', $data);
	}

	function delete($id) {
		// course deletes
		$this->db->delete('course_targets', array('course_id'=>$id));

		return $this->db->delete('courses', array('id'=>$id));
	}

	function delete_target($id) {
		return $this->db->delete('course_targets', array('id'=>$id));		
	}
}