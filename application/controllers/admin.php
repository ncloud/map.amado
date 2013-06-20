<?php
class Admin extends APP_Controller {
    function __construct() 
    {
        parent::__construct();
		
		$this->layout->setLayout('layouts/admin');
    }
	
	function index()
	{
		$this->load->model('m_place');
		
		$total_approved = $this->m_place->get_count_by_approved();
		$total_rejected = $this->m_place->get_count_by_rejected();
		$total_pending = $this->m_place->get_count_by_pending();
		$total_all = $this->m_place->get_count();
		
		$this->set('total_approved', $total_approved);
		$this->set('total_rejected', $total_rejected);
		$this->set('total_pending', $total_pending);
		$this->set('total_all', $total_all);

		$this->view('admin/index');
	}
}