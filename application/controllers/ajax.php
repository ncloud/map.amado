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

	function add_role($site_id) {
		header('Content-Type: application/json');

		$this->load->model('m_role');
		
		$output = new StdClass;
		$output->success = false;

		$role_name = $_POST['privacy'];
		$email = $_POST['email'];

		if($this->user_data->id) {
			$role_names = array();

			$roles = $this->m_role->gets_all();
			foreach($roles as $role) {
				$role_names[$role->id] = $role->name;
			}
			
			$max_role = end($role_names);

			if($role_name != $max_role) { // super admin 			
				$site = $this->m_site->get($site_id);
				if($site) {
					$role = $this->m_role->get_role($site_id, $this->user_data->id);
					if(in_array($role, array('admin','super-admin'))) {
						if(in_array($role_name, $role_names)) {
							if(!empty($email)) {
								if($add_user = $this->m_role->user_add($site_id, 0, $this->m_role->get_id_by_name($role_name), $email)) {
									$output->success = true;

									// email send
									$this->load->library('email');

									$site_owner = $this->m_site->get_site_owner($site_id);

									$config = array();
									$config['mailtype'] = 'html';
									$this->email->initialize($config);

									$this->email->from($site_owner->email, '아마도 지도');
									$this->email->to($email); 

									$link = site_url('/invite/' . $add_user->invite_code);
									$message = '아마도 지도의 ' . $site_owner->name .'님께서 지도 [' . $site->name . ']에 초대하셨습니다. <br />';
									$message.= '아래 주소를 클릭해주세요.<br /><br />';
									$message.= '<a href="'.$link.'">' . $link . '</a>';

									$this->email->subject('아마도 지도에서 초대합니다');
									$this->email->message($message);	

									$this->email->send();
								} else {
									$output->message = '알 수 없는 오류가 발생했습니다. 다시 시도해주세요.';
								}
							} else {
								$output->message = '이메일을 입력해주세요.';
							}

						} else {
							$output->message = '잘못된 권한값입니다.';
						}
					} else {
						$output->message = '접근 권한이 없습니다.';
					}
				} else {
					$output->message = '잘못된 접근입니다.';
				}
			} else {
				$output->message = '최고 관리자 권한은 추가하실 수 없습니다.';
			}
		} else {
			$output->message = '접근 권한이 없습니다.';
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