<?php
class Manage extends APP_Controller {
    function __construct() 
    {
        parent::__construct();
		
		$this->layout->setLayout('layouts/manage');
		
		$this->load->model('m_place');
		$this->load->model('m_course');
		
		if($this->map && !$this->input->is_ajax_request()) {
			$total_place_approved = $this->m_place->get_count_by_approved($this->map->id);
			$total_place_rejected = $this->m_place->get_count_by_rejected($this->map->id);
			$total_place_pending = $this->m_place->get_count_by_pending($this->map->id);
			$total_place_all = $this->m_place->get_count($this->map->id);
			
			$this->set('total_place_approved', $total_place_approved);
			$this->set('total_place_rejected', $total_place_rejected);
			$this->set('total_place_pending', $total_place_pending);
			$this->set('total_place_all', $total_place_all);

			$total_course_approved = $this->m_course->get_count_by_approved($this->map->id);
			$total_course_rejected = $this->m_course->get_count_by_rejected($this->map->id);
			$total_course_pending = $this->m_course->get_count_by_pending($this->map->id);
			$total_course_all = $this->m_course->get_count($this->map->id);

			$this->set('total_course_approved', $total_course_approved);
			$this->set('total_course_rejected', $total_course_rejected);
			$this->set('total_course_pending', $total_course_pending);
			$this->set('total_course_all', $total_course_all);
		}
    }
	
	function rebuild_geocode()
	{
		$this->load->model('m_work');
		$this->m_work->rebuild_geocode_for_places();
	}
	
	function index($page = 1)
	{
		if(!$this->__check_login()) return false;

		if(empty($this->map->id)) {
			$paging = new StdClass;
			$paging->page = $page;
			$paging->per_page = 15;

			if(in_array($this->user_data->role, array('admin','super-admin'))) {
				$paging->total_count = $this->m_map->get_count();
				$maps = $this->m_map->gets_all($paging->per_page, ($page-1) * $paging->per_page);
			} else {
				$paging->total_count = $this->m_map->get_count_by_user_id($this->user_data->id);
				$maps = $this->m_map->gets_all_by_user_id($this->user_data->id,$paging->per_page, ($page-1) * $paging->per_page);
			}

			$paging->max = floor($paging->total_count / $paging->per_page);
			if($paging->total_count % $paging->per_page > 0) $paging->max ++;
			
			$paging->start = floor($page / 10) * 10;
			$paging->end = $paging->start + 10;
			if($paging->end > $paging->max) {
				$paging->end = $paging->max;
			}
			if($paging->start == 0) $paging->start = 1;

			$this->set('paging', $paging);

			$this->set('maps', $maps);
			$this->view('manage/index_map');
		} else {
			$this->__get_place_lists($this->map->id, 'all', $page);
			$this->view('manage/index');
		}
	}
	
	function lists($type, $status, $page = 1)
	{
		if(!$this->__check_map()) return false;
		if(!$this->__check_login()) return false;
		if(!$this->__check_role()) return false;
		
		if(empty($this->user_data->id)) {
			redirect('/login?redirect_uri='.urlencode(site_url($_SERVER['PATH_INFO'])));
		}

		if($type == 'place') {
			$this->__get_place_lists($this->map->id, $status, $page);
			$this->view('manage/list');
		} else if($type == 'course') {
			$this->__get_course_lists($this->map->id, $status, $page);
			$this->view('manage/course/list');
		}
	}

	function add_map()
	{
		if(!$this->__check_login()) return false;

		$message = null;
	
		$map = new StdClass;
		$map->name = '';
		$map->permalink = '';
		$map->type_template = 'none';

		if($_POST && !empty($_POST)) {
			$errors =$this->__check_for_add_map_form($_POST, $map);
			if(!$errors) {
				$_POST['user_id'] = $this->user_data->id;
				if($map_id = $this->m_map->add($_POST)) {
					$this->load->model('m_role');
					$this->m_role->user_add($map_id, $this->user_data->id, $this->m_role->get_id_by_name('super-admin'));

					redirect($map->permalink.'/manage');
				}
			} else {
				$message = new StdClass;
				$message->type = 'error';
				$message->content = $errors;
			}
		}

		$this->set('map_data', $map);
		$this->set('message', $message);

		$this->view('manage/add/map');
	}
	
	function add($type = 'place') {
		if(!$this->__check_map()) return false;
		
		$message = null;


        if($this->map->add_role == 'guest' || 
            ($this->map->add_role == 'member' && in_array($this->user_data->role,array('member','workman','admin','super-admin'))) ||
            ($this->map->add_role == 'workman' && in_array($this->user_data->role,array('workman','admin','super-admin'))) ||
            ($this->map->add_role == 'admin' && in_array($this->user_data->role,array('admin','super-admin')))) {
        } else {
        	return false;
        }

		switch($type) {
			case 'image':	
				$default_image = new StdClass;
				$default_image->title = '';
				$default_image->description = '';
				$default_image->address = '';
				$default_image->address_is_position = 'no';
				$default_image->lat = DEFAULT_LAT;
				$default_image->lng = DEFAULT_LNG;
				$default_image->attached = 'no';
				$default_image->owner_name = '';
				$default_image->owner_email = '';
				
				if(!empty($_POST)) {
					$this->load->model('m_image');

					if(isset($_FILES) && !empty($_FILES)) {
						foreach($_FILES as $key=>$file) $_POST[$key] = $file;
					}

					$errors = $this->__check_for_image_form($_POST, $default_image);
					if(!$errors) {
						$_POST['file'] = $_POST['image'];
						$_POST['map_id'] = $this->map->id;
						$_POST['user_id'] = isset($this->user_data->id) ? $this->user_data->id : 0;

						unset($_POST['image']);

						if(isset($_POST['approved'])) {
							if(in_array($this->user_data->role, array('workman','admin','super-admin')) && $_POST['approved'] == 'on') {
								$_POST['status'] = 'approved';
							}
							unset($_POST['approved']);
						}
						
						$image_id = $this->m_image->add($_POST);
						$this->m_map->update_time($this->map->id);

						$this->load->model('m_work');
						$this->m_work->rebuild_geocode_for_places();	

						if(!$this->input->is_ajax_request()) {
							if($image_id) {
								redirect($this->map->permalink.'/manage');
							}
						} else {
							$message = new StdClass;
							$message->type = 'success';
							$message->content = array('id'=>$image_id, 'status'=>isset($_POST['status']) ? $_POST['status'] : 'pending');
						}
					} else {
						$message = new StdClass;
						$message->type = 'error';
						$message->content = $errors;
					}
				}

				if($this->input->is_ajax_request()) {
					$output = new StdClass;
					$output->success = $message->type == 'success' ? true : false;
					$output->content = $message->content;
					
					$this->layout->setLayout('layouts/empty');
					echo json_encode($output);	
				} else {
					$this->set('message', $message);
					
					$this->set('image', $default_image);
					
					$this->view('manage/add/image');
				}
			break;
			case 'place':
				$default_place = new StdClass;
				$default_place->type_id = '';
				$default_place->title = '';
				$default_place->description = '';
				$default_place->address = '';
				$default_place->address_is_position = 'no';
				$default_place->lat = '37.5935645';
				$default_place->lng = '127.0010451';
				$default_place->url = '';
				$default_place->owner_name = '';
				$default_place->owner_email = '';
				
				if(!empty($_POST)) {
					$errors = $this->__check_for_place_form($_POST, $default_place);
					if(!$errors) {
						$_POST['map_id'] = $this->map->id;
						$_POST['user_id'] = isset($this->user_data->id) ? $this->user_data->id : 0;
						
						if(isset($_POST['approved'])) {
							if(in_array($this->user_data->role, array('workman','admin','super-admin')) && $_POST['approved'] == 'on') {
								$_POST['status'] = 'approved';
							}
							unset($_POST['approved']);
						}

						$place_id = $this->m_place->add($_POST);
						$this->m_map->update_time($this->map->id);
						
						$this->load->model('m_work');
						$this->m_work->rebuild_geocode_for_places();

						if(!$this->input->is_ajax_request()) {
							if($place_id) {
								redirect($this->map->permalink.'/manage');
							}
						} else {
							$message = new StdClass;
							$message->type = 'success';
							$message->content = array('id'=>$place_id, 'status'=>isset($_POST['status']) ? $_POST['status'] : 'pending');
						}
					} else {
						$message = new StdClass;
						$message->type = 'error';
						$message->content = $errors;
					}
				}

				if($this->input->is_ajax_request()) {
					$output = new StdClass;
					$output->success = $message->type == 'success' ? true : false;
					$output->content = $message->content;
					
					$this->layout->setLayout('layouts/empty');
					echo json_encode($output);	
				} else {
					$this->set('message', $message);
					
					$this->set('place', $default_place);
					$this->set('place_types', $this->m_place->get_types($this->map->id));	
					
					$this->view('manage/add/place');
				}
			break;
			case 'course':
				$default_course = new StdClass;				
				$default_course->permalink = '';
				$default_course->title = '';
				$default_course->description = '';

				$default_course_targes = array();
				
				if(!empty($_POST)) {
					$errors = $this->__check_for_course_form($_POST, $default_course, $default_course_targes);
					if(!$errors) {
						$_POST['map_id'] = $this->map->id;
						$_POST['user_id'] = isset($this->user_data->id) ? $this->user_data->id : 0;
						
						if(isset($_POST['approved'])) {
							if(in_array($this->user_data->role, array('workman','admin','super-admin')) && $_POST['approved'] == 'on') {
								$_POST['status'] = 'approved';
							}
							unset($_POST['approved']);
						}

						$course_id = $this->m_course->add($_POST);
						$this->m_map->update_time($this->map->id);

						if($course_id) 
							$this->m_course->update_targets($course_id, $default_course_targes);

						if(!$this->input->is_ajax_request()) {
							if($course_id) {
								redirect($this->map->permalink.'/manage/course');
							}
						} else {
							$message = new StdClass;
							$message->type = 'success';
							$message->content = array('id'=>$course_id, 'status'=>isset($_POST['status']) ? $_POST['status'] : 'pending');
						}
					} else {
						$message = new StdClass;
						$message->type = 'error';
						$message->content = $errors;
					}
				}

				if(count($default_course_targes)) {
					// array to object
					foreach($default_course_targes as $key => $course_target) {
						$default_course_targes[$key] = (object)$course_target;
					}
				}

				if($this->input->is_ajax_request()) {
					$output = new StdClass;
					$output->success = $message->type == 'success' ? true : false;
					$output->content = $message->content;
					
					$this->layout->setLayout('layouts/empty');
					echo json_encode($output);	
				} else {
					// address get
					if($default_course_targes) {
						$place_ids = array();
						foreach($default_course_targes as $course_target) $place_ids[] = $course_target->target_id;

						$places = $this->m_place->gets_by_ids($place_ids);			

						foreach($default_course_targes as $key => $course_target) {
							if($course_target->target_id) {
								$default_course_targes[$key]->address = $places[$course_target->target_id]->address;
							} else {
								$default_course_targes[$key]->address = '';
							}
						}
					}

					$this->set('message', $message);
					
					$this->set('course', $default_course);
					$this->set('course_targets', $default_course_targes);
					$this->set('place_lists', $this->m_place->gets($this->map->id));

					$this->view('manage/add/course');
				}
			break;
		}
	}

	function place_delete($id)
	{
		if(!$this->__check_map()) return false;

		if($place = $this->m_place->get($id)) {

			if($place->user_id != $this->user_data->id && !($this->user_data->role == 'super-admin' || $this->user_data->role == 'admin')) {
			 	// 에러
			 	$this->error('삭제할 권한이 없습니다.');
			} else {
				// 삭제완료

				if($place->attached == 'image') {
					$this->load->model('m_image');
					$this->m_image->delete($place->id);
				} else {
					$this->m_place->delete($place->id);
				}
				
				$this->m_map->update_time($this->map->id);

				redirect($this->map->permalink.'/manage');
			}
		}
	}
	
	function place($id = null, $page = 1)
	{
		if(!$this->__check_map()) return false;
		
		$this->set('menu', 'place');
		
		if($id) {

		} else {
			$this->__get_place_lists($this->map->id, 'all', $page);

			$this->view('manage/index');
		}
	}
	
	function place_edit($id)
	{
		if(!$this->__check_map()) return false;
		
		if($place = $this->m_place->get($id)) {
			$message = null;

			if($place->user_id != $this->user_data->id && !($this->user_data->role == 'super-admin' || $this->user_data->role == 'admin')) {
				$message = new StdClass;
				$message->type = 'error';
				$message->content = '변경 권한이 없습니다.';
			} else {
				if($place->attached == 'image') {
					$this->load->model('m_image');		

					$place->file = $this->m_image->get_image($place->id);
					if($place->file) {
			          	$place->image = site_url('files/uploads/'.$place->file);
			          	$place->image_small = site_url('files/uploads/'.str_replace('.','_s.',$place->file));
			          	$place->image_medium = site_url('files/uploads/'.str_replace('.','_m.',$place->file));
			        }		
				}

				if(!empty($_POST)) {
					if(isset($_FILES) && !empty($_FILES)) {
						foreach($_FILES as $key=>$file) $_POST[$key] = $file;
					}

					if($place->attached == 'image') {
						$errors = $this->__check_for_image_form($_POST, $place, true);
					} else {
						$errors = $this->__check_for_place_form($_POST, $place);
					}

					if(!$errors) {
						if(isset($_POST['approved'])) {
							if(in_array($this->user_data->role, array('workman','admin','super-admin')) && $_POST['approved'] == 'on') {
								$_POST['status'] = 'approved';
							}
							unset($_POST['approved']);
						}

						if($place->attached == 'image') {										
							$this->m_image->update($id, $_POST);
						} else {
							$this->m_place->update($id, $_POST);
						}
						
						$this->m_map->update_time($this->map->id);
					
						$this->load->model('m_work');
						$this->m_work->rebuild_geocode_for_places();

						$message = new StdClass;
						$message->type = 'success';
						$message->content = '변경사항을 저장했습니다.';
						
						if($place->attached == 'image')
							$place = $this->m_image->get($id);
							if($place->file) {
					          	$place->image = site_url('files/uploads/'.$place->file);
					          	$place->image_small = site_url('files/uploads/'.str_replace('.','_s.',$place->file));
					          	$place->image_medium = site_url('files/uploads/'.str_replace('.','_m.',$place->file));
					        }		
						else
							$place = $this->m_place->get($id);
					} else {
						$message = new StdClass;
						$message->type = 'error';
						$message->content = $errors;
					}
				}
			}
			
			if($this->input->is_ajax_request()) {
				$output = new StdClass;
				$output->success = $message->type == 'successs' ? true : false;
				$output->content = $message->content;
				
				$this->layout->setLayout('layouts/empty');
				echo json_encode($output);	 
			} else {
				$this->set('message', $message);
				
				$this->set('edit_mode', true);
				
				if($place->attached == 'image') {
					$this->set('image', $place);
				
					$this->view('manage/add/image');
				} else {
					$this->set('place', $place);
					$this->set('place_types', $this->m_place->get_types($this->map->id));
				
					$this->view('manage/add/place');
				}
			}
		} else {
			$this->error('잘못된 페이지 주소입니다.');
		}
	}
	
	function place_change($type, $id, $value)
	{
		if(!$this->__check_map()) return false;
		if(!$this->__check_login()) return false;
		if(!$this->__check_role()) return false;

		
		$redirect = empty($this->queries['redirect_uri']) ? (!empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : site_url('/')) : $this->queries['redirect_uri'];
		
		switch($type) {
			case 'status':
				if(in_array($value, array('approved','rejected','pending')) && $place = $this->m_place->get($id)) {
					$data = array();
					$data['status'] = $value;
					$this->m_place->update($id, $data);
					
					$this->m_map->update_time($this->map->id);
					
					$this->load->model('m_work');
					$this->m_work->rebuild_geocode_for_places();
					
					redirect($redirect);
				}
			break;
		}
		
		redirect($redirect);
		return false;
	}

	function course($id = null, $page = 1) {		
		if(!$this->__check_map()) return false;
			
		$this->set('menu', 'course');

		if($id) {

		} else {
			$this->__get_course_lists($this->map->id, 'all', $page);

			$this->view('manage/course/index');
		}
	}
	
	function course_edit($id) {
		if(!$this->__check_map()) return false;
		
		$this->set('menu', 'course');

		if($course = $this->m_course->get($id)) {
			$message = null;
			$course_targets = $this->m_course->gets_targets($id);

			if($course->user_id != $this->user_data->id && !($this->user_data->role == 'super-admin' || $this->user_data->role == 'admin')) {
				$message = new StdClass;
				$message->type = 'error';
				$message->content = '변경 권한이 없습니다.';
			} else {					

				if(!empty($_POST)) {
					$errors = $this->__check_for_course_form($_POST, $course, $course_targets, true);

					if(!$errors) {
						if(isset($_POST['approved'])) {
							if(in_array($this->user_data->role, array('workman','admin','super-admin')) && $_POST['approved'] == 'on') {
								$_POST['status'] = 'approved';
							}
							unset($_POST['approved']);
						}

						$this->m_course->update($id, $_POST);
						$this->m_course->update_targets($id, $course_targets);

						$this->m_map->update_time($this->map->id);

						$message = new StdClass;
						$message->type = 'success';
						$message->content = '변경사항을 저장했습니다.';
						
						$course = $this->m_course->get($id);
					} else {
						$message = new StdClass;
						$message->type = 'error';
						$message->content = $errors;
					}
				}
			}

			if(count($course_targets)) {
				// array to object
				foreach($course_targets as $key => $course_target) {
					$course_targets[$key] = (object)$course_target;
				}
			}

			if($this->input->is_ajax_request()) {
				$output = new StdClass;
				$output->success = $message->type == 'successs' ? true : false;
				$output->content = $message->content;
				
				$this->layout->setLayout('layouts/empty');
				echo json_encode($output);	 
			} else {
				$this->set('message', $message);
				
				$this->set('edit_mode', true);

				// address get
				if($course_targets) {
					$place_ids = array();
					foreach($course_targets as $course_target) $place_ids[] = $course_target->target_id;

					$places = $this->m_place->gets_by_ids($place_ids);			

					foreach($course_targets as $key => $course_target) {
						if($course_target->target_id) {
							$course_targets[$key]->address = $places[$course_target->target_id]->address;
						} else {
							$course_targets[$key]->address = '';
						}
					}
				}

				$this->set('course', $course);
				$this->set('course_targets', $course_targets);

				$this->set('place_lists', $this->m_place->gets($this->map->id));
			
				$this->view('manage/add/course');
			}
		} else {
			$this->error('잘못된 페이지 주소입니다.');
		}	
	}

	function course_change($type, $id, $value) {
		if(!$this->__check_map()) return false;
		if(!$this->__check_login()) return false;
		if(!$this->__check_role()) return false;
		
		$redirect = empty($this->queries['redirect_uri']) ? (!empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : site_url('/')) : $this->queries['redirect_uri'];
		
		switch($type) {
			case 'status':
				if(in_array($value, array('approved','rejected','pending')) && $course = $this->m_course->get($id)) {
					$data = array();
					$data['status'] = $value;
					$this->m_course->update($id, $data);

					$this->m_map->update_time($this->map->id);
										
					redirect($redirect);
				}
			break;
		}
		
		redirect($redirect);
		return false;
	}

	function basic()
	{
		if(!$this->__check_map()) return false;
		if(!$this->__check_login()) return false;
		if(!$this->__check_role()) return false;

		$this->set('menu', 'basic');			

		$map_data = clone $this->map;

		$message = null;
		if(!empty($_POST)) { // edit mode 
			$errors = $this->__check_for_basic_form($this->map->id, $_POST, $map_data);
			
			if(!$errors) {
				if($this->m_map->update($this->map->id, $_POST)) {
					if($this->map->permalink != $map_data->permalink) {
						redirect($map_data->permalink.'/manage/basic');	
					}
		
					$message = new StdClass;
					$message->type = 'success';
					$message->content = '변경사항을 저장했습니다.';
				}
			} else {
				$message = new StdClass;
				$message->type = 'error';
				$message->content = $errors;
			}
		}

		$this->set('message', $message);
		$this->set('map_data', $map_data);

		$this->view('manage/setting/basic');
	}

	function user()
	{
		if(!$this->__check_map()) return false;
		if(!$this->__check_login()) return false;
		if(!$this->__check_role()) return false;

		$this->set('menu', 'user');			

		$users = $this->m_role->gets_by_map_id($this->map->id);
		$this->set('users', $users);

		$this->set('roles', $this->m_role->gets_all());

		$this->view('manage/setting/user');
	}

	function type()
	{
		if(!$this->__check_map()) return false;
		if(!$this->__check_login()) return false;
		if(!$this->__check_role()) return false;

		$this->set('menu', 'type');			

		$message = null;

		if(!empty($_POST)) { // edit mode 
			if(!($this->user_data->role == 'super-admin' || $this->user_data->role == 'admin')) {
				$this->error('변경 권한이 없습니다.');
				return false;
			} else {
				$types = array();
				foreach($_POST as $key => $data) {
					$key_cuts = explode('_', $key, 2);
					if(count($key_cuts) == 2 && substr($key_cuts[0],0,4) == 'type') {
						if(!isset($types[$key_cuts[0]])) {
							$types[$key_cuts[0]] = array();
						}

						$types[$key_cuts[0]][$key_cuts[1]] = $data;
					}
				}

				if(count($types)) {
					$update_datas = array();
					$insert_datas = array();

					foreach($types as $type_data) {
						if(isset($type_data['id']) && !empty($type_data['id'])) {
							$update_data = array();
							$update_data['id'] = $type_data['id'];
							$update_data['icon_id'] = $type_data['icon_id'];
							$update_data['name'] = $type_data['name'];
							$update_data['order_index'] = $type_data['order'];

							$update_datas[] = $update_data;
						} else {
							$insert_data = array();
							$insert_data['map_id'] = $this->map->id;
							$insert_data['icon_id'] = $type_data['icon_id'];
							$insert_data['name'] = $type_data['name'];
							$insert_data['order_index'] = $type_data['order'];

							$insert_datas[] = $insert_data;
						}
					}

					if(count($update_datas)) $this->m_place->update_place_types($update_datas);
					if(count($insert_datas)) $this->m_place->insert_place_types($insert_datas);
				}
				
				$message = new StdClass;
				$message->type = 'success';
				$message->content = '변경사항을 저장했습니다.';
			}

		}

		if($this->input->is_ajax_request()) {
			$this->layout->setLayout('layouts/empty');

			$output = new StdClass;
			$output->success = $message->type == 'success';
			$output->message = $message->content;
			echo json_encode($output);
		} else {
			$this->set('message', $message);

			$types = $this->m_place->gets_type($this->map->id);
			
			$types_counts = array();
			$result = $this->m_place->get_types_count($this->map->id);
			foreach($result as $item) {
				$types_counts[$item->type_id] = $item->count;
			}

			foreach($types as $key => $type) {
				if(isset($types_counts[$type->id])) {
					$types[$key]->count = $types_counts[$type->id];
				} else {
					$types[$key]->count = 0;
				}
			}

			$this->set('types', $types);

			$this->view('manage/setting/type');
		}
	}

	// AJAX Only
	function type_add($name, $icon_id = false) {
		$this->layout->setLayout('layouts/empty');

		$output = new StdClass;
		$output->success = false;

		if(!$this->map->id) {
			$output->success = false;
			$output->message = '잘못된 접근입니다';
		} else {
			if(!($this->user_data->role == 'super-admin' || $this->user_data->role == 'admin')) {
				$output->success = false;			
				$output->message = '추가 권한이 없습니다.';		
			} else {
				$name = urldecode($name);
				if(empty($name)) {
					$output->success = false;			
					$output->message = '필수 항목이 없습니다. (이름)';	
				} else {
					$this->load->model('m_place');

					if($this->m_place->get_exist_type_by_name($this->map->id, $name)) {
						$output->success = false;			
						$output->message = '이미 이름이 존재합니다.';
					} else {
						$data = new StdClass;
						$data->map_id = $this->map->id;
						$data->icon_id = ($icon_id === false || !is_numeric($icon_id)) ? 0 : $icon_id;
						$data->name = $name;
						$data->order_index = $this->m_place->get_max_type_id($this->map->id) + 1;

						if($data->id = $this->m_place->add_type($data)) {
							$output->success = true;
							$output->content = $data;
						}
					}
				}
			}
		}

		echo json_encode($output);
	}

	// AJAX ONLY
	function type_edit($id) {		
		$this->layout->setLayout('layouts/empty');

		$output = new StdClass;
		$output->success = false;

		if(!$this->map->id) {
			$output->success = false;
			$output->message = '잘못된 접근입니다';
		} else {
			if(!($this->user_data->role == 'super-admin' || $this->user_data->role == 'admin')) {
				$output->success = false;			
				$output->message = '삭제 권한이 없습니다.';		
			} else {
				$datas = array();
				$check_names = array('name');
				if(isset($_POST) && !empty($_POST)) {
					foreach($_POST as $key => $data) {
						if(in_array($key, $check_names)) {
							$datas[$key] = urldecode($data);
						}
					}
				}

				if(empty($id)) {
					$output->success = false;			
					$output->message = '필수 항목이 없습니다. (ID)';	
				} else {
					$this->load->model('m_place');

					if($type = $this->m_place->get_type($id)) {
						if($type->map_id == $this->map->id) {
							if(count($datas)) {
								$this->m_place->update_type($id, $datas);
							}

							$output->success = true;
						} else {
							$output->success = false;
							$output->message = '잘못된 접근입니다.';
						}
					} else { // 없는 TYPE
						$output->success = false;
						$output->message = '잘못된 접근입니다.';
					}
				}
			}
		}

		echo json_encode($output);
	}
	// AJAX Only
	function type_delete($id) {		
		$this->layout->setLayout('layouts/empty');

		$output = new StdClass;
		$output->success = false;

		if(!$this->map->id) {
			$output->success = false;
			$output->message = '잘못된 접근입니다';
		} else {
			if(!($this->user_data->role == 'super-admin' || $this->user_data->role == 'admin')) {
				$output->success = false;			
				$output->message = '삭제 권한이 없습니다.';		
			} else {
				if(empty($id)) {
					$output->success = false;			
					$output->message = '필수 항목이 없습니다. (ID)';	
				} else {
					$this->load->model('m_place');

					if($type = $this->m_place->get_type($id)) {
						if($type->map_id == $this->map->id) {
							$this->m_place->delete_type($id);
							$output->success = true;
						} else {
							$output->success = false;
							$output->message = '잘못된 접근입니다.';
						}
					} else { // 없는 TYPE
						$output->success = false;
						$output->message = '잘못된 접근입니다.';
					}
				}
			}
		}

		echo json_encode($output);
	}

	// INVITE // NOT REQUIRE SITE ID
    function invite_cancel($v1, $v2 = false)
    {
		if(!$this->__check_login()) return false;
		
		$this->layout->setLayout('layouts/manage');

    	$this->load->model('m_role');
    	$this->load->model('m_map');

    	if($v2) {
    		$role = $this->m_role->get_by_map_and_user_id($v1, $v2);
    	} else {
    		$role = $this->m_role->get_by_invite_code($v1);
    	}

		if($role) {
    		if($map = $this->m_map->get($role->map_id)) {
    			$user_role_name = $this->m_role->get_role($map->id, $this->user_data->id);
    			if(in_array($user_role_name, array('admin','super-admin'))) {
    				$this->load->library('user_agent');

    				if($role->user_id) { // 가입한 사용자
    					$this->m_role->user_delete($role->map_id, $role->user_id);
    				} else { // 초대메일 발송한 사용자
    					$this->m_role->user_delete_by_code($role->invite_code);
    				}

    				redirect($this->agent->referrer());
    			}
    		}
		}
    }

    // change_role
    function change_role($user_id, $role_name)
    {
		if(!$this->__check_map()) return false;
		if(!$this->__check_login()) return false;
		if(!$this->__check_role()) return false;

    	$role_id = $this->m_role->get_id_by_name($role_name);
    	if(!$role_id) {
    		$this->error('잘못된 접근입니다');
    		return false;
    	}

		$this->m_role->update_user_role($this->map->id, $user_id, $role_id);
		
		redirect($this->map->permalink.'/manage/user');
    }
		
	private function __check_for_place_form(&$form, &$change_place = null)
	{
		$errors = array();
		
		if(!isset($form['title']) || empty($form['title'])) {
			$errors['title'] = '장소명을 입력해주세요';

			if($change_place) $change_place->title = '';
		} else {
			if($change_place) $change_place->title = $form['title'];
		}
		/*
		if(!isset($form['type_id']) || empty($form['type_id'])) {
			$errors['type_id'] = '분류를 선택해주세요';

			if($change_place) $change_place->type_id = '';
		} else {
			if($change_place) $change_place->type_id = $form['type_id'];
		}*/
		
		if(!isset($form['address']) || empty($form['address'])) {
			$errors['address'] = '주소를 입력해주세요';

			if($change_place) $change_place->address = '';
		} else {
			if($change_place) $change_place->address = $form['address'];
		}
		
		if(!isset($form['owner_name']) || empty($form['owner_name'])) {
			$errors['owner_name'] = '등록자 이름을 입력해주세요';

			if($change_place) $change_place->owner_name = '';
		} else {
			if($change_place) $change_place->owner_name = $form['owner_name'];
		}
		
		if(!isset($form['owner_email']) || empty($form['owner_email'])) {
			$errors['owner_email'] = '등록자 이메일을 입력해주세요';

			if($change_place) $change_place->owner_email = '';
		}else {
			if($change_place) $change_place->owner_email = $form['owner_email'];
		}
		
		if(isset($form['url']) && $change_place) $change_place->url = $form['url'];
		else if($change_place) $change_place->url = '';

		if(isset($form['description']) && $change_place) $change_place->description = $form['description'];
		else if($change_place) $change_place->description = '';

		if(count($errors) == 0) return false;
		return $errors;
	}
	

	private function __check_for_image_form(&$form, &$change_image = null, $edit_mode = false)
	{
		$errors = array();

		if(!$edit_mode) {
			if(!isset($form['image']) || empty($form['image'])) {
				$errors['image'] = '사진을 업로드해주세요';
			}

			if(isset($form['image']['type']) && !in_array($form['image']['type'],array('image/png','image/jpeg','image/gif'))) {
				$errors['image'] = '허용하지 않는 사진 종류입니다. [허용 : png, jpeg, gif]';
			}
		}
		
		if(!isset($form['title']) || empty($form['title'])) {
			$errors['title'] = '장소명을 입력해주세요';

			if($change_image) $change_image->title = '';
		} else {
			if($change_image) $change_image->title = $form['title'];
		}
		
		if(!isset($form['address']) || empty($form['address'])) {
			$errors['address'] = '주소를 입력해주세요';

			if($change_image) $change_image->address = '';
		} else {
			if($change_image) $change_image->address = $form['address'];
		}
		
		if(!isset($form['owner_name']) || empty($form['owner_name'])) {
			$errors['owner_name'] = '등록자 이름을 입력해주세요';

			if($change_image) $change_image->owner_name = '';
		} else {
			if($change_image) $change_image->owner_name = $form['owner_name'];
		}
		
		if(!isset($form['owner_email']) || empty($form['owner_email'])) {
			$errors['owner_email'] = '등록자 이메일을 입력해주세요';

			if($change_image) $change_image->owner_email = '';
		}else {
			if($change_image) $change_image->owner_email = $form['owner_email'];
		}
		
		if(isset($form['description']) && $change_image) $change_image->description = $form['description'];
		else if($change_image) $change_image->description = '';

		if(count($errors) == 0) return false;
		return $errors;
	}

	private function __check_for_course_form(&$form, &$change_course = null, &$change_course_targets = null, $edit_mode = false) 
	{
		$errors = array();

		if(!isset($form['title']) || empty($form['title'])) {
			$errors['title'] = '이름을 입력해주세요';
			
			if($change_course) $change_course->title = '';
		} else {
			if($change_course) $change_course->title = $form['title'];
		}

		if(isset($form['permalink']) && !empty($form['permalink'])) {
			if($this->m_course->check_permalink($this->map->id, $form['permalink'])) {
				$errors['permalink'] = '고유값이 중복되었습니다.';
				if($change_course) $change_course->permalink = $form['permalink'];
			} else {
				if($change_course) $change_course->permalink = $form['permalink'];
			}
		}

		$targets = array();

		foreach($form as $key=>$item) {
			if(substr($key,0,6) == 'course') {
				$new_key = explode('_', substr($key,6));
				if(count($new_key) == 2) {
					if(!isset($targets[$new_key[0]])) $targets[$new_key[0]] = array();
					$targets[$new_key[0]][$new_key[1]] = $item;

					unset($form[$key]);
				}
			}
		}

		$change_course_targets = array();
		$order = 1;

		foreach($targets as $target) {
			if(!empty($target['title'])) {
				$target_data = array();
				$target_data['course_id'] = isset($change_course->id) ? $change_course->id : null;
				$target_data['target_id'] = !empty($target['id']) ? $target['id'] : null;
				$target_data['title'] = $target['title'];
				$target_data['order_index'] = $order++;

				$change_course_targets[] = $target_data;			
			}
 		}
		
		if(count($errors) == 0) return false;
		return $errors;
	}

	private function __check_for_add_map_form(&$form, &$map = null)
	{
		$this->load->helper('string');
		
		$errors = array();

		if(!isset($form['name']) || empty($form['name'])) {
			$errors['name'] = '지도명을 입력해주세요';

			if($map) $map->name = '';
		} else {
			if($map) $map->name = $form['name'];
		}

		if(!isset($form['permalink']) || empty($form['permalink'])) {
			$errors['permalink'] = '주소를 입력해주세요';

			if($map) $map->permalink = '';
		} else {
			if($this->m_map->get_by_permalink($form['permalink'])) {
				$errors['permalink'] = '이미 존재하는 주소입니다. 다른 주소를 입력해주세요';
			} else if(!only_english($form['permalink'])) {
				$errros['permalink'] = '주소는 영문만 사용하실 수 있습니다.';
			} else {
				if($map) $map->permalink = $form['permalink'];
			}
		}

		if(count($errors) == 0) return false;
		return $errors;
	}

	private function __check_for_basic_form($map_id, &$form, &$map = null) 
	{
		$this->load->helper('string');

		$errors = array();

		if(!isset($form['privacy']) || empty($form['privacy']) || !in_array($form['privacy'], array('public', 'private'))) {
			$errors['privacy'] = '잘못된 접근일 수 있습니다. 새로고침해주세요';

			if($map) $map->privacy = 'public';
		} else {
			if($map) $map->privacy = $form['privacy'];
		}

		if(!isset($form['add_role']) || empty($form['add_role']) || !in_array($form['add_role'], array('guest', 'member', 'workman', 'admin'))) {
			$errors['add_role'] = '잘못된 접근일 수 있습니다. 새로고침해주세요';

			if($map) $map->add_role = 'member';
		} else {
			if($map) $map->add_role = $form['add_role'];
		}

		if(!isset($form['name']) || empty($form['name'])) {
			$errors['name'] = '지도명을 입력해주세요';

			if($map) $map->name = '';
		} else {
			if($map) $map->name = $form['name'];
		}

		if(!isset($form['permalink']) || empty($form['permalink'])) {
			$errors['permalink'] = '주소를 입력해주세요';

			if($map) $map->permalink = '';
		} else {
			if($this->m_map->get_by_permalink($form['permalink'], $map_id)) {
				$errors['permalink'] = '이미 존재하는 주소입니다. 다른 주소를 입력해주세요';
			}  else if(!only_english($form['permalink'])) {
				$errors['permalink'] = '주소는 영문만 사용하실 수 있습니다.';
			} 
			
			if($map) $map->permalink = $form['permalink'];
		}

		if(count($errors) == 0) return false;
		return $errors;
	}

	private function __get_course_lists($map_id, $status, $page = 1) {
		$this->set('status', $status);
		$this->set('menu', 'course_' . $status);

		$paging = new StdClass;
		$paging->page = $page;
		$paging->per_page = 15;

		switch($status) {
			case 'all':
				$paging->total_count = $this->get('total_course_all');
				$courses = $this->m_course->gets_all($map_id, $paging->per_page, $page);
			break;			
			default:
				$paging->total_count = $this->get('total_course_' . $status);
				$courses = $this->m_course->gets_by_status($map_id, $status, $paging->per_page, ($page-1)*$paging->per_page);
			break;
		}

		$this->set('courses', $courses);

		$paging->total_count = $this->get('total_course_all');

		$paging->max = floor($paging->total_count / $paging->per_page);
		if($paging->total_count % $paging->per_page > 0) $paging->max ++;
		
		$paging->start = floor($page / 10) * 10;
		$paging->end = $paging->start + 10;
		if($paging->end > $paging->max) {
			$paging->end = $paging->max;
		}
		if($paging->start == 0) $paging->start = 1;

		$this->set('paging', $paging);
	}

	private function __get_place_lists($map_id, $status, $page = 1)
	{
		$this->set('status', $status);
		$this->set('menu', 'place_' . $status);
		
		$paging = new StdClass;
		$paging->page = $page;
		$paging->per_page = 15;

		switch($status) {
			case 'all':
				$paging->total_count = $this->get('total_place_all');
				$places = $this->m_place->gets_all($map_id, $paging->per_page, ($page-1)*$paging->per_page);
			break;			
			default:
				$paging->total_count = $this->get('total_place_' . $status);
				$places = $this->m_place->gets_by_status($map_id, $status, $paging->per_page, ($page-1)*$paging->per_page);
			break;
		}
		$this->set('places', $places);
		
		$paging->max = floor($paging->total_count / $paging->per_page);
		if($paging->total_count % $paging->per_page > 0) $paging->max ++;
		
		$paging->start = floor($page / 10) * 10;
		$paging->end = $paging->start + 10;
		if($paging->end > $paging->max) {
			$paging->end = $paging->max;
		}
		if($paging->start == 0) $paging->start = 1;
		
		$this->set('paging', $paging);
	}

	private function __check_map()
	{
		if(empty($this->map->id)) {
			redirect('/manage');
			return false;
		}

		return true;
	}

	private function __check_login()
	{
		if(empty($this->user_data->id)) {
			redirect('/login?redirect_uri='.urlencode(site_url($_SERVER['PATH_INFO'])));
			return false;
		}

		return true;
	}

	private function __check_role($check_roles = array('admin', 'super-admin'))
	{
		if(in_array($this->user_data->role, $check_roles)) {
			return true;
		} else {
			$this->error('접근 권한이 없습니다.');
			return false;
		}
	}
}