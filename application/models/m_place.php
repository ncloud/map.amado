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
	
	function get($id)
	{
		return $this->db->from('places')->where('id', $id)->get()->row();
	}
	
	function gets()
	{
		return $this->db->from('places')->where('status','approved')->order_by('id DESC')->get()->result();
	}
	
	function gets_all($count, $index = 1)
	{
        if($index > 1) {
            $this->db->limit($count, ($index - 1));
        } else {
            $this->db->limit($count);
        }
		
		return $this->db->from('places')->order_by('id DESC')->get()->result();		
	}
	
	function gets_by_status($status, $count, $index = 1)
	{
		$this->db->where('status', $status);
		
        if($index > 1) {
            $this->db->limit($count, ($index - 1));
        } else {
            $this->db->limit($count);
        }
		
		return $this->db->from('places')->order_by('id DESC')->get()->result();		
	}
	
	
	function gets_by_type($type)
	{
		$this->db->from('places')->order_by('id DESC');
		
		switch($type) {
			case 'approved':
			case 'rejected':
			case 'pending':				
				$this->db->where('status', $type);
				break;
		}	
		
		return $this->get()->result();	
	}
	
	function get_count_by_approved()
	{
		$result = $this->db->from('places')->where('status','approved')->select('count(*) as count')->order_by('id DESC')->get()->row();
		if($result) return $result->count;
		return false;
	}
	
	function get_count_by_rejected()
	{
		$result = $this->db->from('places')->where('status','rejected')->select('count(*) as count')->order_by('id DESC')->get()->row();
		if($result) return $result->count;
		return false;
	}
	
	function get_count_by_pending()
	{
		$result = $this->db->from('places')->where('status','pending')->select('count(*) as count')->get()->row();
		if($result) return $result->count;
		return false;
	}
	
	function get_count()
	{
		$result = $this->db->from('places')->select('count(*) as count')->get()->row();
		if($result) return $result->count;
		return false;
	}
	
	function add($data) {
		if(isset($data['address']) && !isset($data['address_is_position'])) {
			$cuts = explode(',',$data['address']);
			
			if(count($cuts) == 2) {
				$data['lat'] = doubleval($cuts[0]);
				$data['lng'] = doubleval($cuts[1]);
				
				$data['address_is_position'] = 'yes';
			}
		}
		
		if($this->db->insert('places', $data)) {
			return $this->db->insert_id();
		}
		
		return false;
	}
	
	function update($id, $data) {
		if(!isset($data['lat'])) $data['lat'] = 0;
		if(!isset($data['lng'])) $data['lng'] = 0;
		
		if(isset($data['address']) && !isset($data['address_is_position'])) {
			$cuts = explode(',',$data['address']);
			
			if(count($cuts) == 2) {
				$data['lat'] = doubleval($cuts[0]);
				$data['lng'] = doubleval($cuts[1]);
				
				$data['address_is_position'] = 'yes';
			}
		}
		
		$this->db->where('id', $id);
		return $this->db->update('places', $data);
	}
}