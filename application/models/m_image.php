<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_image extends CI_Model
{
    function __construct()
    {
        // Call the Model constructor
        parent::__construct();    
    }
	
	function gets($site_id)
	{
		return $this->db->from('places')->where('approved','yes')->where('site_id', $site_id)->where('attached','image')->get()->result();
	}
	
	function get($id)
	{
		return $this->db->from('places')->where('places.id', $id)->join('attaches','places.id = attaches.parent_id','left')->select('places.*, attaches.filename as file')->get()->row();
	}

	function get_image($parent_id) {
		$result = $this->db->from('attaches')->where('parent_id', $parent_id)->get()->row();
		if($result) return $result->filename;
		return false;
	}

	function add($data) {
		if(!$data['file']) return false;

		$data['attached'] = 'no';
		if(in_array($data['file']['type'], array('image/png','image/jpeg','image/gif'))) {
			$data['attached'] = 'image';
		} else {
			return false;
		}

		$upload_dir = date('Ymd', mktime());		

		if(!is_dir(APPPATH.'/webroot/files/uploads/')) {
			@mkdir(APPPATH.'/webroot/files/uploads/');			
			@chmod(APPPATH.'/webroot/files/uploads/', 0777);
		}

		if(!is_dir(APPPATH.'/webroot/files/uploads/'.$upload_dir)) {
			@mkdir(APPPATH.'/webroot/files/uploads/'.$upload_dir);			
			@chmod(APPPATH.'/webroot/files/uploads/'.$upload_dir, 0777);
		}

		$ext = '';
		if($pos = strrpos($data['file']['name'],'.')) {
			$ext = substr($data['file']['name'],$pos);
		}

		$filename_not_ext = $upload_dir.'/'.md5($data['file']['name'].' '.mktime());
		$filename = $filename_not_ext . $ext;
		move_uploaded_file($data['file']['tmp_name'], APPPATH.'/webroot/files/uploads/'.$filename);

		if($data['attached'] == 'image') {
			$this->load->library('image_resize');		

			$upload_dir = APPPATH.'/webroot/files/uploads/';

			// small
			$this->image_resize->cropCenter($upload_dir . $filename,
											$upload_dir . $filename_not_ext . '_s' . $ext,
											30,30);
											 
			// midium
			$this->image_resize->cropCenter($upload_dir . $filename,
											$upload_dir . $filename_not_ext . '_m' . $ext,
											130,130);
		}

		unset($data['file']);
		
		if(!isset($data['lat'])) $data['lat'] = 0;
		if(!isset($data['lng'])) $data['lng'] = 0;

		if(isset($data['address']) && (!isset($data['address_is_position']) || $data['address_is_position'] == 'yes')) {
			$cuts = explode(',',$data['address']);
			
			if(count($cuts) == 2) {
				$data['lat'] = doubleval($cuts[0]);
				$data['lng'] = doubleval($cuts[1]);
				
				$data['address_is_position'] = 'yes';
			}
		}

		$insert_id = null;
		if($this->db->insert('places', $data)) {
			$insert_id = $this->db->insert_id();
		}

		if($insert_id) {
			$attach = new StdClass;
			$attach->parent_id = $insert_id;
			$attach->filename = $filename;

			$this->db->insert('attaches', $attach);
			return $insert_id;
		}
		
		return false;
	}
	
	function update($id, $data) {
		//TODO: 파일을 새롭게 업로드하면 새롭게 적용하도록 ...
		
		if(!isset($data['lat'])) $data['lat'] = 0;
		if(!isset($data['lng'])) $data['lng'] = 0;
		
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

	function delete($id) {
		$this->delete_images_by_parent_id($id);

		// course deletes
		$this->db->delete('course_targets', array('target_id'=>$id));

		return $this->db->delete('places', array('id'=>$id));
	}

	function delete_images_by_parent_id($parent_id)
	{
		return $this->db->delete('attaches', array('parent_id'=>$parent_id));
	}
}