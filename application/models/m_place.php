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
	
}