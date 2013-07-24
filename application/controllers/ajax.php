<?php
class Ajax extends APP_Controller {
    function __construct() 
    {
        parent::__construct();

		$this->layout->setLayout('layouts/empty');
	}

	function places($site_id) {
		$output = new StdClass;
		$output->success = false;

		$output->input = isset($_POST['query']) ? $_POST['query'] : '';

		if($site_id && !empty($output->input)) {
			if($site = $this->m_site->get($site_id)) {		
				$this->load->model('m_place');
				
				$places = $this->m_place->gets($site->id);					
				if($places) {

					$output->success = true;				
					$output->data = array();

					$checks = strtolower(str_replace(' ','',slice_texts($output->input)));

					foreach($places as $place) {
						$text = strtolower(str_replace(' ','',slice_texts($place->title)));
						if(strpos($text, $checks) !== false) {
							$output->data[] = array('id'=>$place->id, 'title'=>$place->title, 'slice_title'=>$text, 'address'=>$place->address, 'lat'=>$place->lat, 'lng'=>$place->lng);
						}
					}
				}
			}
		}

		echo json_encode($output);
	}

	function weather($lat = '37.588710224492', $lng = '127.00605010202')
	{
		$url = 'http://www.kma.go.kr/wid/queryDFS.jsp?gridx=' . $lat . '&gridy=' . $lng;
		$file = @file_get_contents($url);
		if($file) {
			$xml = simplexml_load_string($file);
			if($xml) {
				$output = new StdClass;
				$output->temp = array();

				$output->time = $xml->header->tm;
				
				foreach($xml->body->data as $data) {

					$temp = new StdClass;
					$temp->hour = $data->hour;
					$temp->day = $data->day;
					$temp->temp = $data->temp;
					$temp->tmx = $data->tmx;
					$temp->tmn = $data->hour;
					$temp->wfKor = $data->wfKor;
					$temp->wfEn = $data->wfEn;

					$output->temp[] = $temp;
				}

				echo json_encode($output);
			}
		}
	}
}