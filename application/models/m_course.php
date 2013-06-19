<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_Course extends CI_Model
{
    function __construct()
    {
        // Call the Model constructor
        parent::__construct();    
    }
	
	function gets()
	{
		return $this->db->from('courses')->get()->result();
	}
	
	function gets_targets()
	{
		return $this->db->from('course_targets')->order_by('order_index ASC')->get()->result();
	}
	
	
}