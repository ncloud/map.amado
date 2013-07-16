<?php
class Manage extends APP_Controller {
    function __construct() 
    {
        parent::__construct();
		
		$this->layout->setLayout('layouts/manage');
		
		$this->load->model('m_place');
		
		if($this->site->id && !$this->input->is_ajax_request()) {
			$total_approved = $this->m_place->get_count_by_approved($this->site->id);
			$total_rejected = $this->m_place->get_count_by_rejected($this->site->id);
			$total_pending = $this->m_place->get_count_by_pending($this->site->id);
			$total_all = $this->m_place->get_count($this->site->id);
			
			$this->set('total_approved', $total_approved);
			$this->set('total_rejected', $total_rejected);
			$this->set('total_pending', $total_pending);
			$this->set('total_all', $total_all);
		}
    }
	
	function rebuild_geocode()
	{
		$this->load->model('m_work');
		$this->m_work->rebuild_geocode_for_places();
	}
	
	function index($page = 1)
	{
		if(empty($this->user_data->id)) {
			redirect('/login');
		}
		
		if(empty($this->site->id)) {
		} else {
			$this->__get_lists($this->site->id, 'all', $page);
			$this->view('manage/index');
		}
	}
	
	function lists($status, $page = 1)
	{
		if(empty($this->site->id)) redirect('/manage');
		
		if(empty($this->user_data->id)) {
			redirect('/login');
		}
		
		$this->__get_lists($this->site->id, $status, $page);

		$this->view('manage/list');
	}
	
	function add($type = 'place') {
		if(empty($this->site->id)) redirect('/');
		
		$message = null;

		switch($type) {
			case 'image':	
				$default_image = new StdClass;
				$default_image->title = '';
				$default_image->description = '';
				$default_image->address = '';
				$default_image->address_is_position = 'no';
				$default_image->lat = '37.5935645';
				$default_image->lng = '127.0010451';
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
						$_POST['site_id'] = $this->site->id;
						$_POST['user_id'] = isset($this->user_data->id) ? $this->user_data->id : 0;

						unset($_POST['image']);

						if(isset($_POST['approved'])) {
							if(in_array($this->user_data->role, array('admin','super-admin')) && $_POST['approved'] == 'on') {
								$_POST['status'] = 'approved';
							}
							unset($_POST['approved']);
						}
						
						$image_id = $this->m_image->add($_POST);

						$this->load->model('m_work');
						$this->m_work->rebuild_geocode_for_places();	

						if(!$this->input->is_ajax_request()) {
							if($image_id) {
								redirect($this->site->permalink.'/manage');
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
						$_POST['site_id'] = $this->site->id;
						$_POST['user_id'] = isset($this->user_data->id) ? $this->user_data->id : 0;
						
						if(isset($_POST['approved'])) {
							if(in_array($this->user_data->role, array('admin','super-admin')) && $_POST['approved'] == 'on') {
								$_POST['status'] = 'approved';
							}
							unset($_POST['approved']);
						}

						$place_id = $this->m_place->add($_POST);
						
						$this->load->model('m_work');
						$this->m_work->rebuild_geocode_for_places();

						if(!$this->input->is_ajax_request()) {
							if($place_id) {
								redirect($this->site->permalink.'/manage');
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
					$this->set('place_types', $this->m_place->get_types($this->site->id));	
					
					$this->view('manage/add/place');
				}
			break;		
		}
	}

	function delete($id)
	{
		if(empty($this->site->id)) redirect('/');
		if($place = $this->m_place->get($id)) {

			if($place->user_id != $this->user_data->id && !($this->user_data->role == 'super-admin' || $this->user_data->role == 'admin')) {
			 	// 에러
			 	$this->error('에러가 발생했습니다', '삭제할 권한이 없습니다.');
			} else {
				// 삭제완료

				if($place->attached == 'image') {
					$this->load->model('m_image');
					$this->m_image->delete($place->id);
				} else {
					$this->m_place->delete($place->id);
				}

				redirect($this->site->permalink.'/manage');
			}
		}
	}
	
	function edit($id)
	{
		if(empty($this->site->id)) redirect('/');
		
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
							if(in_array($this->user_data->role, array('admin','super-admin')) && $_POST['approved'] == 'on') {
								$_POST['status'] = 'approved';
							}
							unset($_POST['approved']);
						}

						if($place->attached == 'image') {										
							$this->m_image->update($id, $_POST);
						} else {
							$this->m_place->update($id, $_POST);
						}
					
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
					$this->set('place_types', $this->m_place->get_types($this->site->id));
				
					$this->view('manage/add/place');
				}
			}
		} else {
			$this->error('에러가 발생했습니다', '잘못된 페이지 주소입니다.');
		}
	}
	
	function change($type, $id, $value)
	{
		if(empty($this->user_data->id) || !in_array($this->user_data->role, array('admin', 'super-admin'))) {
			return false;
		}
		
		$redirect = empty($this->queries['redirect_uri']) ? (!empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : site_url('/')) : $this->queries['redirect_uri'];
		
		switch($type) {
			case 'status':
				if(in_array($value, array('approved','rejected','pending')) && $place = $this->m_place->get($id)) {
					$data = array();
					$data['status'] = $value;
					$this->m_place->update($id, $data);
					
					$this->load->model('m_work');
					$this->m_work->rebuild_geocode_for_places();
					
					redirect($redirect);
				}
			break;
		}
		
		redirect($redirect);
		return false;
	}
	
	private function __check_for_place_form($form, &$change_place = null)
	{
		$errors = array();
		
		if(!isset($form['title']) || empty($form['title'])) {
			$errors['title'] = '이름을 입력해주세요';
		} else {
			if($change_place) $change_place->title = $form['title'];
		}
		
		if(!isset($form['type_id']) || empty($form['type_id'])) {
			$errors['type_id'] = '종류를 선택해주세요';
		} else {
			if($change_place) $change_place->type_id = $form['type_id'];
		}
		
		if(!isset($form['address']) || empty($form['address'])) {
			$errors['address'] = '주소를 입력해주세요';
		} else {
			if($change_place) $change_place->address = $form['address'];
		}
		
		if(!isset($form['owner_name']) || empty($form['owner_name'])) {
			$errors['owner_name'] = '등록자 이름을 입력해주세요';
		} else {
			if($change_place) $change_place->owner_name = $form['owner_name'];
		}
		
		if(!isset($form['owner_email']) || empty($form['owner_email'])) {
			$errors['owner_email'] = '등록자 이메일을 입력해주세요';
		}else {
			if($change_place) $change_place->owner_email = $form['owner_email'];
		}
		
		if(isset($form['url']) && $change_place) $change_place->url = $form['url'];
		if(isset($form['description']) && $change_place) $change_place->description = $form['description'];
		
		if(count($errors) == 0) return false;
		return $errors;
	}
	

	private function __check_for_image_form($form, &$change_image = null, $edit_mode = false)
	{
		$errors = array();

		if(!$edit_mode) {
			if(!isset($form['image']) || empty($form['image'])) {
				$errors['image'] = '사진을 업로드해주세요';
			} else {
				if($change_image) $change_image->title = $form['title'];
			}

			if(isset($form['image']['type']) && !in_array($form['image']['type'],array('image/png','image/jpeg','image/gif'))) {
				$errors['image'] = '허용하지 않는 사진 종류입니다. [허용 : png, jpeg, gif]';
			}
		}
		
		if(!isset($form['title']) || empty($form['title'])) {
			$errors['title'] = '이름을 입력해주세요';
		} else {
			if($change_image) $change_image->title = $form['title'];
		}
		
		if(!isset($form['address']) || empty($form['address'])) {
			$errors['address'] = '주소를 입력해주세요';
		} else {
			if($change_image) $change_image->address = $form['address'];
		}
		
		if(!isset($form['owner_name']) || empty($form['owner_name'])) {
			$errors['owner_name'] = '등록자 이름을 입력해주세요';
		} else {
			if($change_image) $change_image->owner_name = $form['owner_name'];
		}
		
		if(!isset($form['owner_email']) || empty($form['owner_email'])) {
			$errors['owner_email'] = '등록자 이메일을 입력해주세요';
		}else {
			if($change_image) $change_image->owner_email = $form['owner_email'];
		}
		
		if(isset($form['description']) && $change_image) $change_image->description = $form['description'];
		
		if(count($errors) == 0) return false;
		return $errors;
	}

	private function __get_lists($site_id, $status, $page = 1)
	{
		$this->set('status', $status);
		$this->set('menu', $status);
		
		$paging = new StdClass;
		$paging->page = $page;
		$paging->per_page = 15;

		switch($status) {
			case 'all':
				$paging->total_count = $this->get('total_all');
				$places = $this->m_place->gets_all($site_id, $paging->per_page, ($page-1)*$paging->per_page);
			break;			
			default:
				$paging->total_count = $this->get('total_' . $status);
				$places = $this->m_place->gets_by_status($site_id, $status, $paging->per_page, ($page-1)*$paging->per_page);
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
}