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
		
		if(!$this->site->id) {
			
		} else {
			$course_lists = $this->m_course->gets($this->site->id);
			
			$course_mode = false;
			
			if(!empty($course_lists) && !empty($course_targets)) {
				$course_mode = true;
			}
			
			$this->set('course_mode', $course_mode);
				
				
			$place_types = $this->m_place->get_types($this->site->id);
			$this->set('place_types', $place_types);
			
			$place_lists_by_type = array();
			$place_lists = $this->m_place->gets($this->site->id);
			uasort($place_lists, 'parseForLat');
			  
			$full_lat = 0;
	        $full_lng = 0;
	        $full_count = 0;
			
			$place_types_by_id = array();
			$count_by_type = array();
			foreach($place_types as $place_type) {
				$count_by_type[$place_type->id] = 0;
				$place_types_by_id[$place_type->id] = $place_type;
			}
			
	        foreach($place_lists as $key => $place) {
	          $place_lists[$key]->icon_id = $place_types_by_id[$place->type_id]->icon_id;
	          $place_lists[$key]->description = str_replace(array("\r\n","\n","\r"),'<br />',$place->description);
	        	
			  if(!isset($place_lists_by_type[$place->type_id])) $place_lists_by_type[$place->type_id] = array();
			  $place_lists_by_type[$place->type_id][] = $place;
			  
	          $full_lat += $place->lat;
	          $full_lng += $place->lng;
	          $full_count ++;
			  
	          $count_by_type[$place->type_id]++;
	        }
					
	        $this->set('place_lists', $place_lists);
			$this->set('place_lists_by_type', $place_lists_by_type);
			$this->set('count_by_type', $count_by_type);
			
			$image_lists  = $this->m_image->gets($this->site->id);
			$this->set('image_lists', $image_lists);
			
	
	        $default_lat = $full_lat / $full_count;
	        $default_lng = $full_lng / $full_count;
			$this->set('default_lat', $default_lat);
			$this->set('default_lng', $default_lng);
			
	    	$this->view('index');
	    }
    }
    
    function login() 
    {
        if(!empty($this->user_data->id)) // 로그인 되어 있으면
        {
            redirect('/');
            return false;
        }
        
    	$this->layout->setLayout('layouts/user');
		
		$redirect = empty($this->queries['redirect_uri']) ? (!empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : site_url('/')) : $this->queries['redirect_uri'];
		$this->set('redirect_url', $redirect);
		
        $this->set('join_mode', false);
		
		$this->view('user/login');
    }

    function join()
    {
        if(!empty($this->user_data->id)) // 로그인 되어 있으면
        {
            redirect('/');
            return false;
        }
		
		$this->layout->setLayout('layouts/user');
        
		$redirect = empty($this->queries['redirect_uri']) ? (!empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : site_url('/')) : $this->queries['redirect_uri'];
		$this->set('redirect_url', $redirect);
		
        $this->set('join_mode', true);        
        
		$this->view('user/login');
    }
}
?>