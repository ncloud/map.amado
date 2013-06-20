<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_Place extends CI_Model
{
    function __construct()
    {
        // Call the Model constructor
        parent::__construct();    
    }
	
	function get_types()
	{
		return $this->db->from('place_types')->get()->result();
	}
	
	function gets()
	{
		return $this->db->from('places')->where('approved','yes')->get()->result();
	}
	
	function get_count_by_approved()
	{
		$result = $this->db->from('places')->where('approved','yes')->select('count(*) as count')->get()->row();
		if($result) return $result->count;
		return false;
	}
	
	function get_count_by_rejected()
	{
		$result = $this->db->from('places')->where('approved','no')->select('count(*) as count')->get()->row();
		if($result) return $result->count;
		return false;
	}
	
	function get_count_by_pending()
	{
		$result = $this->db->from('places')->where('approved',null)->select('count(*) as count')->get()->row();
		if($result) return $result->count;
		return false;
	}
	
	function get_count()
	{
		$result = $this->db->from('places')->select('count(*) as count')->get()->row();
		if($result) return $result->count;
		return false;
	}
}