<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_Place extends CI_Model
{
    function __construct()
    {
        // Call the Model constructor
        parent::__construct();    
    }
	
	function get_types($site_id)
	{
		return $this->db->from('place_types')->where('site_id', $site_id)->get()->result();
	}
	
	function get($id)
	{
		return $this->db->from('places')->where('id', $id)->get()->row();
	}
	
	function gets($site_id)
	{
		return $this->db->from('places')->where('status','approved')->where('site_id', $site_id)->order_by('id DESC')->get()->result();
	}
	
	function gets_all($site_id, $count, $index = 1)
	{
        if($index > 1) {
            $this->db->limit($count, ($index - 1));
        } else {
            $this->db->limit($count);
        }
		
		return $this->db->from('places')->where('site_id', $site_id)->order_by('id DESC')->get()->result();		
	}
	
	function gets_by_status($site_id, $status, $count, $index = 1)
	{
		$this->db->where('status', $status);
		
        if($index > 1) {
            $this->db->limit($count, ($index - 1));
        } else {
            $this->db->limit($count);
        }
		
		return $this->db->from('places')->where('site_id', $site_id)->order_by('id DESC')->get()->result();		
	}
	
	
	function gets_by_type($site_id, $type)
	{
		$this->db->from('places')->where('site_id', $site_id)->order_by('id DESC');
		
		switch($type) {
			case 'approved':
			case 'rejected':
			case 'pending':				
				$this->db->where('status', $type);
				break;
		}	
		
		return $this->get()->result();	
	}
	
	function get_count_by_approved($site_id)
	{
		$result = $this->db->from('places')->where('status','approved')->where('site_id', $site_id)->select('count(*) as count')->order_by('id DESC')->get()->row();
		if($result) return $result->count;
		return false;
	}
	
	function get_count_by_rejected($site_id)
	{
		$result = $this->db->from('places')->where('status','rejected')->where('site_id', $site_id)->select('count(*) as count')->order_by('id DESC')->get()->row();
		if($result) return $result->count;
		return false;
	}
	
	function get_count_by_pending($site_id)
	{
		$result = $this->db->from('places')->where('status','pending')->where('site_id', $site_id)->select('count(*) as count')->get()->row();
		if($result) return $result->count;
		return false;
	}
	
	function get_count($site_id)
	{
		$result = $this->db->from('places')->where('site_id', $site_id)->select('count(*) as count')->get()->row();
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