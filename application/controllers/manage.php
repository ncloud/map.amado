<?php
class Manage extends APP_Controller {
    function __construct() 
    {
        parent::__construct();
		
		$this->layout->setLayout('layouts/manage');
		
		$this->load->model('m_place');
		
		$total_approved = $this->m_place->get_count_by_approved();
		$total_rejected = $this->m_place->get_count_by_rejected();
		$total_pending = $this->m_place->get_count_by_pending();
		$total_all = $this->m_place->get_count();
		
		$this->set('total_approved', $total_approved);
		$this->set('total_rejected', $total_rejected);
		$this->set('total_pending', $total_pending);
		$this->set('total_all', $total_all);
    }
	
	function rebuild_geocode()
	{
		$this->load->model('m_work');
		$this->m_work->rebuild_geocode_for_places();
	}
	
	function index($page = 1)
	{
		$this->__get_lists('all', $page);

		$this->view('manage/index');
	}
	
	function lists($status, $page = 1)
	{
		$this->__get_lists($status, $page);

		$this->view('manage/list');
	}
	
	function add($type = 'place') {
		$message = null;
		
		switch($type) {
			case 'place':
				$default_place = new StdClass;
				$default_place->title = '';
				$default_place->description = '';
				$default_place->type = '';
				$default_place->address = '';
				$default_place->uri = '';
				$default_place->owner_name = '';
				$default_place->owner_email = '';
				
				if(!empty($_POST)) {
					$errors = $this->__check_for_place_form($_POST, $default_place);
					if(!$errors) {
						if($place_id = $this->m_place->add($_POST)) {
							redirect('/manage');
						}
					} else {
						$message = new StdClass;
						$message->type = 'error';
						$message->content = $errors;
					}
				}

				$this->set('message', $message);
				
				$this->set('place', $default_place);
				$this->set('place_types', $this->m_place->get_types());	
				
				$this->view('manage/add/place');
			break;		
		}
	}
	
	function edit($id)
	{
		if($place = $this->m_place->get($id)) {
			$message = null;
				
			if(!empty($_POST)) {
				$errors = $this->__check_for_place_form($_POST, $place);
				if(!$errors) {
					$this->m_place->update($id, $_POST);
				
					$message = new StdClass;
					$message->type = 'success';
					$message->content = '변경사항을 저장했습니다.';
				
					$place = $this->m_place->get($id);
				} else {
					$message = new StdClass;
					$message->type = 'error';
					$message->content = $errors;
				}
			}
			
			$this->set('message', $message);
			
			$this->set('edit_mode', true);
			
			$this->set('place', $place);
			$this->set('place_types', $this->m_place->get_types());
			
			$this->view('manage/add/place');
		} else {
			$this->error('에러가 발생했습니다', '잘못된 페이지 주소입니다.');
		}
	}
	
	function change($type, $id, $value)
	{
		$redirect = empty($this->queries['redirect_uri']) ? (!empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : site_url('/')) : $this->queries['redirect_uri'];
		
		switch($type) {
			case 'status':
				if(in_array($value, array('approved','rejected','pending')) && $place = $this->m_place->get($id)) {
					$data = array();
					$data['status'] = $value;
					$this->m_place->update($id, $data);
					
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
			$errors['title'] = '제목을 입력해주세요';
		} else {
			if($change_place) $change_place->title = $form['title'];
		}
		
		if(!isset($form['type']) || empty($form['type'])) {
			$errors['type'] = '종류를 선택해주세요';
		} else {
			if($change_place) $change_place->type = $form['type'];
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
	
	private function __get_lists($status, $page = 1)
	{
		$this->set('status', $status);
		$this->set('menu', $status);
		
		$paging = new StdClass;
		$paging->page = $page;
		$paging->per_page = 15;
		
		switch($status) {
			case 'all':
				$paging->total_count = $this->get('total_all');
				$places = $this->m_place->gets_all($paging->per_page, ($page-1)*$paging->per_page);
			break;			
			default:
				$paging->total_count = $this->get('total_' . $status);
				$places = $this->m_place->gets_by_status($status, $paging->per_page, ($page-1)*$paging->per_page);
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