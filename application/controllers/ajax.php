<?php
class Ajax extends APP_Controller {
    function __construct() 
    {
        parent::__construct();

		$this->layout->setLayout('layouts/empty');
	}

	function check_places($map_id, $last_id)
	{
		$output = new StdClass;
		$output->success = false;

		$this->load->model('m_place');
		$this->load->helper('parse');

		if($places = $this->m_place->gets_new_places($map_id, $last_id)) {
			$output->success = true;
			$output->result_count = count($places);

			uasort($places, 'parseForLat');

	        foreach($places as $key => $place) {
	          $places[$key]->icon_id = $this->m_place->get_icon_id_by_type_id($place->type_id);
	          $places[$key]->description = str_replace(array("\r\n","\n","\r"),'<br />',$place->description);
	        	
	          if($place->attached == 'image') {
	          	$places[$key]->image = site_url('files/uploads/'.$place->file);
	          	$places[$key]->image_small = site_url('files/uploads/'.str_replace('.','_s.',$place->file));
	          	$places[$key]->image_medium = site_url('files/uploads/'.str_replace('.','_m.',$place->file));
	          }

	          unset($places[$key]->file);
	        }
			
			$output->result = $places;
		}

		echo json_encode($output);
	}

	function places($map_id) {
		$output = new StdClass;
		$output->success = false;

		$output->input = isset($_POST['query']) ? $_POST['query'] : '';

		if($map_id && !empty($output->input)) {
			if($map = $this->m_map->get($map_id)) {		
				$this->load->model('m_place');
				
				$places = $this->m_place->gets($map->id);					
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

	function add_role($map_id) {
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
				$map = $this->m_map->get($map_id);
				if($map) {
					$role = $this->m_role->get_role($map_id, $this->user_data->id);
					if(in_array($role, array('admin','super-admin'))) {
						if(in_array($role_name, $role_names)) {
							if(!empty($email)) {
								if($add_user = $this->m_role->user_add($map_id, 0, $this->m_role->get_id_by_name($role_name), $email)) {
									$output->success = true;

									// email send
									$this->load->library('email');

									$map_owner = $this->m_map->get_map_owner($map_id);

									$config = array();
									$config['mailtype'] = 'html';
									$this->email->initialize($config);

									$this->email->from($map_owner->email, '아마도 지도');
									$this->email->to($email); 

									$link = site_url('/invite/' . $add_user->invite_code);
									$message = '아마도 지도의 ' . $map_owner->name .'님께서 지도 [' . $map->name . ']에 초대하셨습니다. <br />';
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

	function update_user_data($user_id)
	{
		$error = false;

		$output = new StdClass;
		$output->success = true;

		if(!$this->user_data->id) {
			$error = '잘못된 접근입니다.';
		} else {
			if($this->user_data->id != $user_id) {
				$error = '잘못된 접근입니다.';
			} else if(empty($_POST) || !isset($_POST['old_password']) || !isset($_POST['new_password']) || !isset($_POST['new_password_re'])) {
				$error = '잘못된 접근입니다.';
			} else {
				$user_data = $this->m_user->get($this->user_data->id, true);

				if(empty($_POST['old_password'])) {
					$error = '현재 비밀번호를 입력해주세요.';
				} else if($this->auth->password($_POST['old_password']) != $user_data->password) {
					$error = '현재 비밀번호가 틀렸습니다.';
				} else if($_POST['new_password'] != $_POST['new_password_re']) {
					$error = '"새로운 비밀번호"와 "새로운 비밀번호 확인"을 같게 입력해주세요.';
				} else {
					$update_data = new StdClass;
					$update_data->password = $this->auth->password($_POST['new_password']);

					$this->m_user->update($user_id, $update_data);
				}
			}
		}

		if($error !== false) {
			$output->success = false;
			$output->message = $error;
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


	function map_data($map_id) {
    	$this->load->model('m_place');
    	$this->load->model('m_course');
		$this->load->model('m_image');
		
		$this->load->helper('parse');

		header('Content-Type: application/json');

		$output = new StdClass;
		$output->success = false;

		if($map = $this->m_map->get($map_id)) {
			$output->success = true;
			
			if($this->user_data->id) {
				$output->user = new StdClass;
				$output->user->id = $this->user_data->id;
				$output->user->name = $this->user_data->name;
				$output->user->display_name = $this->user_data->display_name;
				$output->user->map_role = $this->m_role->get_role($map_id, $this->user_data->id);
				$output->user->can_add = ($map->add_role == 'guest' || 
									($map->add_role == 'member' && in_array($output->user->map_role,array('member','workman','admin','super-admin'))) ||
            						($map->add_role == 'workman' && in_array($output->user->map_role,array('workman','admin','super-admin'))) ||
            						($map->add_role == 'admin' && in_array($output->user->map_role,array('admin','super-admin'))));
			} else {
				$output->user = null;
			}

			$output->map = $map;
			$output->place = new StdClass;
			$output->course = false;

			$course_mode = false;

			$course_lists = $this->m_course->gets($map->id);
			if(!empty($course_lists)) {
				$course_index = 1;
				foreach($course_lists as $key => $course) {					
					$course_lists[$key]->course_index = $course_index ++;

					$course_lists[$key]->color = '#0099ff'; // TODO: change color
					$course_lists[$key]->icon = 1;
					$course_lists[$key]->targets = $this->m_course->gets_targets($course->id, true);
				}

				$course_mode = true;
			}

			$output->course = new StdClass;
			if($course_mode) {
				$output->course->courses = $course_lists;
		    } else {
		    	$output->course->courses = array();
		    }
				
			$place_types = $this->m_place->get_types($map->id);

			$place_lists = $this->m_place->gets($map->id);
			if($place_lists) {
				uasort($place_lists, 'parseForLat');
			}
			
			$place_types_by_id = array();
			$count_by_type = array();
			
			// 분류없음
			$notype = new StdClass;
			$notype->id = 0;
			$notype->map_id = $map->id;
			$notype->icon_id = 0;
			$notype->name = '분류없음';
			$notype->order_index = "1";
			$notype->view_add_mode = false;

			array_unshift($place_types, $notype);
			
			$count_by_type[0] = 0;
			$place_types_by_id[0] = $notype;

			foreach($place_types as $place_type) {
				$count_by_type[$place_type->id] = 0;
				$place_types_by_id[$place_type->id] = $place_type;
			}
			
			if($place_lists) {
		        foreach($place_lists as $key => $place) {
		          $place_lists[$key]->description = str_replace(array("\r\n","\n","\r"),'<br />',$place->description);
		        
		          if($place->attached == 'image') {
		          	$place_lists[$key]->image = site_url('files/uploads/'.$place->file);
		          	$place_lists[$key]->image_small = site_url('files/uploads/'.str_replace('.','_s.',$place->file));
		          	$place_lists[$key]->image_medium = site_url('files/uploads/'.str_replace('.','_m.',$place->file));
		          } else if($place->attached == 'no') {
					$place_lists[$key]->icon_id = $place_types_by_id[$place->type_id]->icon_id;
					  
			        $count_by_type[$place->type_id]++;
		          }

		          unset($place_lists[$key]->file);
		        }
		    }

		    // 가져오기 분류가 비어있으면 숨긴다.
			if(!isset($place_lists_by_type[IMPORT_TYPE_ID]) || count($place_lists_by_type[IMPORT_TYPE_ID]) == 0) {
				array_pop($place_types);
			}

			foreach($place_types as $key => $place_type) {
				$place_types[$key]->count = $count_by_type[$place_type->id];
			}

			$output->place->types = $place_types;
	        $output->place->places = $place_lists;
		}

		echo json_encode($output);		
	}
}