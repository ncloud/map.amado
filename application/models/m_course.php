<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_Course extends CI_Model
{
    function __construct()
    {
        // Call the Model constructor
        parent::__construct();    
    }
	
	function gets($site_id)
	{
		return $this->db->from('courses')->where('site_id', $site_id)->get()->result();
	}
	
	function gets_targets($course_id)
	{
		return $this->db->from('course_targets')->where('course_id', $course_id)->order_by('order_index ASC')->get()->result();
	}
	
	
}