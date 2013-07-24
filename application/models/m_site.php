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

	function gets_all()
	{
		return $this->db->from('sites')->get()->result();
	}
}