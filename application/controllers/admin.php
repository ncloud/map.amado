<?php
class Admin extends APP_Controller {
    function __construct() 
    {
        parent::__construct();
		
		$this->layout->setLayout('layouts/admin');
		
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
	
	function index($page = 1)
	{
		$this->__get_lists('all', $page);

		$this->view('admin/index');
	}
	
	function lists($status, $page = 1)
	{
		$this->__get_lists($status, $page);

		$this->view('admin/list');
	}
	
	function edit($id)
	{
		if($place = $this->m_place->get($id)) {
			$message = null;
				
			if(!empty($_POST)) {
				$this->m_place->update($id, $_POST);
				
				$message = new StdClass;
				$message->type = 'success';
				$message->content = '변경사항을 저장했습니다.';
				
				$place = $this->m_place->get($id);
			}
			
			$this->set('message', $message);
			
			$this->set('place', $place);
			$this->set('place_types', $this->m_place->get_types());
			
			$this->view('admin/edit');
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