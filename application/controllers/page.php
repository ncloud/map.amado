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

		if(!$this->map->id) {
			$this->layout->setLayout('layouts/welcome');
			$my_maps = array();
			if($this->user_data->id) {
				$my_maps = $this->m_map->gets_all_by_user_id($this->user_data->id);
			}
			$this->set('my_maps', $my_maps);

			$maps = $this->m_map->gets_all();
			$this->set('maps', $maps);

			$this->view('welcome');
		} else {
			$course_mode = false;

			$full_lat = 0;
	        $full_lng = 0;
	        $full_count = 0;

			$course_lists = $this->m_course->gets($this->map->id);
			if(!empty($course_lists)) {
				$course_index = 1;
				foreach($course_lists as $key => $course) {					
					$course_lists[$key]->course_index = $course_index ++;

					$course_lists[$key]->color = '#0099ff';
					$course_lists[$key]->icon = 1;
					$course_lists[$key]->targets = $this->m_course->gets_targets($course->id, true);
					foreach($course_lists[$key]->targets as $target) {
						if($target->place_id && $target->place_lat && $target->place_lng) {
				        	$full_lat += $target->place_lat;
				        	$full_lng += $target->place_lng;
				        	$full_count ++;
				        }
					}
				}

				$course_mode = true;
			}

			$this->set('course_mode', $course_mode);
			$this->set('course_lists', $course_lists);

			$course_default = new StdClass;
			if($full_count) {
	        	$course_default->lat = $full_lat / $full_count;
	        	$course_default->lng = $full_lng / $full_count;
	        } else {
	        	$course_default->lat = DEFAULT_LAT;
	        	$course_default->lng = DEFAULT_LNG;
	        }
			$this->set('course_default', $course_default);

				
			$place_types = $this->m_place->get_types($this->map->id);
			$this->set('place_types', $place_types);
			
			$place_lists_by_type = array();
			$place_lists = $this->m_place->gets($this->map->id);
			if($place_lists) {
				uasort($place_lists, 'parseForLat');
			}
			  
			$full_lat = 0;
	        $full_lng = 0;
	        $full_count = 0;
			
			$place_types_by_id = array();
			$count_by_type = array();
			
			// 분류없음
			$notype = new StdClass;
			$notype->icon_id = 1;
			$notype->name = '분류없음';

			$count_by_type[0] = 0;
			$place_types_by_id[0] = $notype;

			foreach($place_types as $place_type) {
				$count_by_type[$place_type->id] = 0;
				$place_types_by_id[$place_type->id] = $place_type;
			}
			
			if($place_lists) {
		        foreach($place_lists as $key => $place) {
		          $place_lists[$key]->icon_id = $place_types_by_id[$place->type_id]->icon_id;
		          $place_lists[$key]->description = str_replace(array("\r\n","\n","\r"),'<br />',$place->description);
		        	
				  if(!isset($place_lists_by_type[$place->type_id])) $place_lists_by_type[$place->type_id] = array();
				  if($place->type_id) $place_lists_by_type[$place->type_id][] = $place;
				  
		          if($place->type_id) $count_by_type[$place->type_id]++;

		          if($place->attached == 'image') {
		          	$place_lists[$key]->image = site_url('files/uploads/'.$place->file);
		          	$place_lists[$key]->image_small = site_url('files/uploads/'.str_replace('.','_s.',$place->file));
		          	$place_lists[$key]->image_medium = site_url('files/uploads/'.str_replace('.','_m.',$place->file));
		          } else if($place->attached == 'no') {
			        $full_lat += $place->lat;
			        $full_lng += $place->lng;
			        $full_count ++;
		          }

		          unset($place_lists[$key]->file);
		        }
		    }

	        $this->set('place_lists', $place_lists);
			$this->set('place_lists_by_type', $place_lists_by_type);
			$this->set('count_by_type', $count_by_type);
			

			$category_default = new StdClass;
			if($full_count) {
	        	$category_default->lat = $full_lat / $full_count;
	        	$category_default->lng = $full_lng / $full_count;
	        } else {
	        	$category_default->lat = DEFAULT_LAT;
	        	$category_default->lng = DEFAULT_LNG;
	        }
			$this->set('category_default', $category_default);
			
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

    function edit()
    {
        if(empty($this->user_data->id)) // 로그인 되어 있지 않으면
        {
            redirect('/');
            return false;
        }

        $this->layout->setLayout('layouts/welcome');
		
		$user_data = $this->user_data;
		$message = null;

		if($_POST && !empty($_POST)) {
			$message = new StdClass;

			$errors = array();

			if(!isset($_POST['name']) || empty($_POST['name'])) {
				$errors['name'] = '이름을 입력해주세요';

				$user_data->name = '';
			} else {
				$user_data->name = $_POST['name'];
			}

			if(!isset($_POST['email']) || empty($_POST['email'])) {
				$errors['email'] = '이메일을 입력해주세요';

				$user_data->email = '';
			} else {
				$user_data->email = $_POST['email'];
			}

			if(!count($errors)) {
				$data = new StdClass;
				$data->name = $data->display_name = $_POST['name'];
				$data->email = $_POST['email'];

				if($this->m_user->update($this->user_data->id, $data)) {
					$message->type = 'success';
					$message->content = '변경사항이 수정되었습니다.';
				} else {
					$message->type = 'error';
					$message->content = array();
				}
			} else {
				$message->type = 'error';
				$message->content = $errors;
			}
		}

		$this->set('message', $message);
		$this->set('user_data', $user_data);

		$this->view('user/edit');
    }

    function invite($code) 
    {
    	$this->layout->setLayout('layouts/manage');

    	$this->load->model('m_role');
    	$this->load->model('m_map');

    	$this->set('invite_code', $code);

    	$message = new StdClass;
    	$message->type = 'success';

    	if($role = $this->m_role->get_by_invite_code($code)) {
    		$map = $this->m_map->get($role->map_id);

    		$this->set('role', $role);
    		$this->set('map', $map);
		} else {
			$this->error('잘못된 초대코드입니다. 코드를 확인해주세요.');
			return false;
		}

		$this->set('message', $message);
		$this->view('invite');
    }

    function invite_do($code)
    {
        if(empty($this->user_data->id)) redirect('/');
		
		$this->layout->setLayout('layouts/manage');

    	$this->load->model('m_role');
    	$this->load->model('m_map');

    	$this->set('invite_code', $code);

    	$message = new StdClass;
    	$message->type = 'success';

    	if($role = $this->m_role->get_by_invite_code($code)) {
    		$map = $this->m_map->get($role->map_id);
    		$this->m_role->update_invite_user($code, $this->user_data->id);
    	
    		redirect($map->permalink);
		} else {
			$this->error('잘못된 초대코드입니다. 코드를 확인해주세요.');
			return false;
		}

		$this->set('message', $message);
		$this->view('invite');
    }
}
?>