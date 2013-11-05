<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_Place extends CI_Model
{
    function __construct()
    {
        // Call the Model constructor
        parent::__construct();    
    }
	
	function get($id)
	{
		return $this->db->from('places')->join('place_types','places.type_id = place_types.id','left')->where('places.id', $id)->join('attaches','places.id = attaches.parent_id','left')->select('places.*, place_types.name as type_name, attaches.filename as file')->get()->row();
	}
	
	function gets($map_id)
	{
		return $this->db->from('places')->join('place_types','places.type_id = place_types.id','left')->join('attaches','places.id = attaches.parent_id','left')->where('places.status','approved')->where('places.map_id', $map_id)->order_by('places.id DESC')->select('places.*, place_types.name as type_name, attaches.filename as file')->get()->result();
	}
	
	function gets_by_ids($ids)
	{
		$result = $this->db->from('places')->join('place_types','places.type_id = place_types.id','left')->join('attaches','places.id = attaches.parent_id','left')->where('places.status','approved')->where_in('places.id', $ids)->order_by('places.id DESC')->select('places.*, place_types.name as type_name, attaches.filename as file')->get()->result();
		if($result) {
			$output = array();
			foreach($result as $item) $output[$item->id] = $item;
			return $output;
		}
		return false;
	}
	
	function gets_all($map_id, $count, $index = 1)
	{
        if($index > 1) {
            $this->db->limit($count, ($index - 1));
        } else {
            $this->db->limit($count);
        }
		
		return $this->db->from('places')->join('place_types','places.type_id = place_types.id','left')->where('places.map_id', $map_id)->order_by('places.id DESC')->select('places.*, place_types.name as type_name')->get()->result();		
	}
	
	function gets_by_status($map_id, $status, $count, $index = 1)
	{
		$this->db->where('places.status', $status);
		
        if($index > 1) {
            $this->db->limit($count, ($index - 1));
        } else {
            $this->db->limit($count);
        }
		
		return $this->db->from('places')->join('place_types','places.type_id = place_types.id','left')->where('places.map_id', $map_id)->select('places.*, place_types.name as type_name')->order_by('places.id DESC')->get()->result();		
	}
	
	
	function gets_by_type($map_id, $type_id)
	{
		$this->db->from('places')->where('map_id', $map_id)->order_by('id DESC');
		$this->db->where('status', $type_id);
		
		return $this->get()->result();	
	}
	
	function get_count_by_approved($map_id)
	{
		$result = $this->db->from('places')->where('status','approved')->where('map_id', $map_id)->select('count(*) as count')->order_by('id DESC')->get()->row();
		if($result) return $result->count;
		return 0;
	}
	
	function get_count_by_rejected($map_id)
	{
		$result = $this->db->from('places')->where('status','rejected')->where('map_id', $map_id)->select('count(*) as count')->order_by('id DESC')->get()->row();
		if($result) return $result->count;
		return 0;
	}
	
	function get_count_by_pending($map_id)
	{
		$result = $this->db->from('places')->where('status','pending')->where('map_id', $map_id)->select('count(*) as count')->get()->row();
		if($result) return $result->count;
		return 0;
	}
	
	function get_count($map_id)
	{
		$result = $this->db->from('places')->where('map_id', $map_id)->select('count(*) as count')->get()->row();
		if($result) return $result->count;
		return 0;
	}

	function gets_type($map_id)
	{
		return $this->db->from('place_types')->where('map_id', $map_id)->order_by('order_index ASC')->get()->result();
	}

	function check_place($map_id, $lat, $lng)
	{
		$result = $this->db->from('places')->where('map_id', $map_id)->where(array('lat'=>$lat,'lng'=>$lng))->get()->row();
		if($result) return true;
		else return false;
	}
	
	function add($data) {
		$data['lat'] = 0;
		$data['lng'] = 0;

		if(isset($data['address']) && (!isset($data['address_is_position']) || $data['address_is_position'] == 'yes')) {
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
		$data['lat'] = 0;
		$data['lng'] = 0;
		
		if(isset($data['address']) && (!isset($data['address_is_position']) || $data['address_is_position'] == 'yes')) {
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

	function update_field($id, $field, $value)
	{
		$data = array();
		$data[$field] = $value;

		
		$this->db->where('id', $id);
		return $this->db->update('places', $data);
	}

	function delete($id) {
		// course deletes
		$this->db->delete('course_targets', array('target_id'=>$id));

		return $this->db->delete('places', array('id'=>$id));
	}

	// Type
	function add_type($data) {
		if($this->db->insert('place_types', $data)) {
			return $this->db->insert_id();
		}

		return false;
	}

	function insert_place_types($datas) {
		$this->db->insert_batch('place_types', $datas, true);
	}

	function update_place_types($datas) {
		$this->db->update_batch('place_types', $datas, 'id');
	}

	function get_type($id) {
		return $this->db->from('place_types')->where('id', $id)->get()->row();
	}

	function get_types($map_id)
	{
		$result = $this->db->from('place_types')->where('map_id', $map_id)->order_by('order_index ASC')->get()->result();
		$result = array_merge($result, $this->__default_types($map_id, count($result)));

		return $result;
	}

	function get_types_count($map_id) {
		$result = $this->db->from('places')->where('map_id', $map_id)->group_by('type_id')->select('type_id, COUNT(type_id) as count')->get()->result();

		return $result; // IMPORT_TYPE
	}

	function delete_type($id) {
		$this->db->delete('place_types', array('id'=>$id));

		// 삭제한 분류를 사용하는 장소들 자동으로 분류없음으로...
		$this->db->update('places', array('type_id'=>0), array('type_id' => $id));

		return true;
	}

	function update_type($id, $data) {
		$this->db->update('place_types', $data, array('id' => $id));
		return true;
	}

	function get_max_type_id($map_id)
	{
		$result = $this->db->from('place_types')->where('map_id', $map_id)->select('max(order_index) as max_id')->limit(1)->get()->row();
		if($result) {
			return $result->max_id;
		} else {
			return 0;
		}
	}

	function get_exist_type_by_name($map_id, $name)
	{
		if($this->db->from('place_types')->where('map_id', $map_id)->where('name', $name)->get()->row()) {
			return true;
		} else {
			return false;
		}
	}

	function import($datas, $check_position = false)
	{
		if($check_position) {
			foreach($datas as $data) {
				if(!$this->check_place($data->map_id, $data->lat, $data->lng)) {
					$this->db->insert('places', $data);
				}
			}
		} else {
			$this->db->insert_batch('places', $datas, true);
		}

		return true;
	}

	function delete_by_map_id($map_id) {
		$this->db->delete('place_types', array('map_id'=>$map_id));
		$this->db->delete('places', array('map_id'=>$map_id));
	}

	function get_icon_id_by_type_id($type_id)
	{
		if($type_id == IMPORT_TYPE_ID) return $type_id;
		else {
			$result = $this->db->from('place_types')->where('id', $type_id)->get()->row();
			if($result) {
				return $result->icon_id;
			} else {
				return false;
			}
		}
	}

	function __default_types($map_id, $order_index)
	{
		$result = array();

		$type = new StdClass;
		$type->id = IMPORT_TYPE_ID;
		$type->map_id = $map_id;
		$type->icon_id = IMPORT_TYPE_ID;
		$type->name = '가져오기';
		$type->order_index = $order_index + 1;

		$result[] = $type;

		return $result;
	}
}