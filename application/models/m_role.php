<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_Role extends CI_Model
{
    //----------------------- PUBLIC METHODS --------------------------//
    
	public function get($id)
	{
		if(is_numeric($id)) 
		{
			return $this->db->get_where('roles', array('id'=>$id))->row();
		}
		else if(is_string($id)) 
		{
			return $this->db->get_where('roles', array('name'=>$id))->row();
		}
		else 
		{
			return false;
		}
	}
	
    //----------------------- STATIC METHODS --------------------------//
    //----------------------- PRIVATE METHODS --------------------------//
     
}//END class