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
	
	function gets_targets($course_id, $only_have_place = false)
	{
		$this->db->from('course_targets')->join('places','places.id = course_targets.target_id','left')->where('course_targets.course_id', $course_id)->order_by('course_targets.order_index ASC')->select('course_targets.*, places.id as place_id, places.status as place_status, places.title as place_title, places.lat as place_lat, places.lng as place_lng, places.address as place_address');

		if($only_have_place) $this->db->where('places.id IS NOT NULL');

		return $this->db->get()->result();
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

	function add_targets($datas) {
		return $this->db->insert_batch('course_targets', $datas);
	}
	
	function update($id, $data) {
		$this->db->where('id', $id);
		return $this->db->update('courses', $data);
	}

	function update_targets($id, $targets) {
		$this->delete_targets_by_course_id($id);

		if(count($targets)) {				
			foreach($targets as $key=>$target) {
				$targets[$key]['course_id'] = $id;
			}

			$this->add_targets($targets);
		}

		return true;
	}

	function delete($id) {
		// course deletes
		$this->db->delete('course_targets', array('course_id'=>$id));

		return $this->db->delete('courses', array('id'=>$id));
	}

	function delete_target($id) {
		return $this->db->delete('course_targets', array('id'=>$id));		
	}

	function delete_targets_by_course_id($id) {
		return $this->db->delete('course_targets', array('course_id'=>$id));		
	}
}