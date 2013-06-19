<?php
class Page extends APP_Controller {
    function __construct() 
    {
        parent::__construct();
    }
    
    public function index()
    {
    	$this->load->model('m_place');
    	$this->load->model('m_course');
		$this->load->model('m_image');
		
		$this->load->helper('parse');
		
		
		$course_lists = $this->m_course->gets();
		$course_targets = $this->m_course->gets_targets();
		
		$course_mode = false;
		
		if(!empty($course_lists) && !empty($course_targets)) {
			$course_mode = true;
		}
		
		$this->set('course_mode', $course_mode);
			
			
		$place_types = $this->m_place->get_types();
		$this->set('place_types', $place_types);
		
		$place_lists_by_type = array();
		$place_lists = $this->m_place->gets();
		uasort($place_lists, 'parseForLat');
		  
		$full_lat = 0;
        $full_lng = 0;
        $full_count = 0;
		
		$count_by_type = array();
		foreach($place_types as $place_type) $count_by_type[$place_type->key] = 0;
		
        foreach($place_lists as $key => $place) {
          $place_lists[$key]->description = str_replace(array("\r\n","\n","\r"),'<br />',$place->description);
        	
		  if(!isset($place_lists_by_type[$place->type])) $place_lists_by_type[$place->type] = array();
		  $place_lists_by_type[$place->type][] = $place;
		  
          $full_lat += $place->lat;
          $full_lng += $place->lng;
          $full_count ++;
		  
          $count_by_type[$place->type]++;
        }
				
        $this->set('place_lists', $place_lists);
		$this->set('place_lists_by_type', $place_lists_by_type);
		$this->set('count_by_type', $count_by_type);
		
		$image_lists  = $this->m_image->gets();
		$this->set('image_lists', $image_lists);
		

        $default_lat = $full_lat / $full_count;
        $default_lng = $full_lng / $full_count;
		$this->set('default_lat', $default_lat);
		$this->set('default_lng', $default_lng);
		
    	$this->view('index');
    }
}
?>