<?php
class Admin extends APP_Controller {
    function __construct() 
    {
        parent::__construct();
		
		$this->layout->setLayout('layouts/admin');
    }
	
	function index($page = 1)
	{
		$this->load->model('m_place');
		
		$total_approved = $this->m_place->get_count_by_approved();
		$total_rejected = $this->m_place->get_count_by_rejected();
		$total_pending = $this->m_place->get_count_by_pending();
		$total_all = $this->m_place->get_count();
		
		$paging = new StdClass;
		$paging->page = $page;
		$paging->per_page = 15;
		
		$paging->total_count = $total_all;
		
		$paging->max = floor($total_all / $paging->per_page);
		if($total_all % $paging->per_page > 0) $paging->max ++;
		
		$paging->start = ($page / 10) * 10;
		$paging->end = $paging->start + 10;
		if($paging->end > $paging->max) {
			$paging->end = $paging->max;
		}
		
		$this->set('paging', $paging);
		
		$places = $this->m_place->gets_all($paging->per_page, ($page-1)*$paging->per_page);
		$this->set('places', $places);
		
		$this->set('total_approved', $total_approved);
		$this->set('total_rejected', $total_rejected);
		$this->set('total_pending', $total_pending);
		$this->set('total_all', $total_all);

		$this->view('admin/index');
	}
	
	function lists($type)
	{
		echo $type;
	}
}