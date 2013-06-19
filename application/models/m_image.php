<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_image extends CI_Model
{
    function __construct()
    {
        // Call the Model constructor
        parent::__construct();    
    }
	
	function gets()
	{
		return $this->db->from('images')->where('approved','yes')->get()->result();
	}
	
}