<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends MY_Controller {

	public function index(){
		$this->login_check();
		$this->load->model('Main_admin');
		$this->load->model('Page_model');
		$this->load->library('form_validation');
		$this->load->helper('string');
		$this->setData('thisurl', 'main');

		$this->setData('info_page', $this->Page_model->get(array('page_name' => 'main')));
		$this->setData('header', $this->Page_model->get_header($this->data['info_page']['id']));
		$this->setData('text_info_1', $this->Main_admin->get_text_info($this->data['info_page']['id'], 1));
		$this->setData('text_info_2', $this->Main_admin->get_text_info($this->data['info_page']['id'], 2));
		$this->setData('sliders', $this->Main_admin->get_slider());
		
		$this->setData('title','Редактирование страницы "Главная страница"');
		$this->setData('main_active','class="active"');
		if($this->input->post('save')){
			$this->form_validation->set_rules('title', '"Заголовок"', 'trim|required',array('required' => 'Заголовок обязательное поле'));
			$this->form_validation->set_rules('keywords', '"keywords"', 'trim');
			$this->form_validation->set_rules('description', '"description"', 'trim');

			if($this->form_validation->run() == FALSE){
				$array = array(
					'title' => $this->form_validation->set_value('title'),
					'keywords' => $this->form_validation->set_value('keywords'),
					'description' => $this->form_validation->set_value('description'),
					'content' => $this->form_validation->set_value('content')
				);
				$this->setData('info_page', $array);
				$this->setData('error', validation_errors());
				$this->display('backend/first-page/index');
			}else{
				$title = $this->security->xss_clean($this->input->post('title'));
				$keywords = $this->security->xss_clean($this->input->post('keywords'));
				$description = $this->security->xss_clean($this->input->post('description'));
				//$content = $this->security->xss_clean($this->input->post('content'));
				$update = array(
					'title' => $title,
					'keywords' => $keywords,
					'description' => $description,
					//'content' => $content
				);
				if($this->Page_model->update(array('page_name' => 'main'),$update)){
					$_SESSION['message'] = 'Данные обновлены';
					redirect($this->data['base_url'] . 'administrator/main');
				}
			}
		}elseif($this->input->post('upload')){

			if(empty($_FILES['file']['name']) && $this->input->post('title') == $this->data['header']['title'] && $this->input->post('active') == $this->data['header']['active']){
				redirect($this->data['base_url'] . 'administrator');
			}

			$this->form_validation->set_rules('title', '"Название"', 'trim|required',array('required' => 'Заголовок обязательное поле'));
			$this->form_validation->set_rules('active', '"Отображение"', 'trim|required',array('required' => 'Показывать на странице обязательное поле'));

			if($this->form_validation->run() == FALSE){
				$array = array(
					'title' => $this->form_validation->set_value('title'),
					'active' => $this->form_validation->set_value('active')
				);

				$this->setData('header', $array);
				$this->setData('error_header', validation_errors());
				$this->display('backend/first-page/index');
			}else{
				if(!empty($_FILES['file']['name'])){
					//Если header уже существует то его надо удалить
					if(isset($this->data['header']['id'])){
						if(!empty($this->data['header']['img'])){
							unlink('uploads/headers/' . $this->data['header']['img']);
						}
					}
					
			    	$tempFile = $_FILES['file']['tmp_name'];

					$targetPath = 'uploads/headers/';
					
			    	$newName = random_string('md5').'.jpg';
			    	$targetFile = $targetPath . $newName;

			    	move_uploaded_file($tempFile, $targetFile);
			    	$insert['img'] = $newName;
				}


		    	$title = $this->security->xss_clean($this->input->post('title'));
		    	if(empty($title)){
					$title = $this->data['header']['title'];
		    	}
		    	$insert['title'] = $title;
		    	$insert['active'] = $this->security->xss_clean($this->input->post('active')); 
		    	$insert['page_id'] = $this->data['info_page']['id'];
		    	if(isset($this->data['header']['id'])){
		    		if($id = $this->Page_model->update_header(array('id'=> $this->data['header']['id']),$insert)){
		    			$_SESSION['message_header'] = 'Данные обновлены';    		
						redirect($this->data['base_url'] . 'administrator');
					}
		    	}else{
		    		if($id = $this->Page_model->insert_header($insert)){
			    		if(isset($insert['img'])){
							$_SESSION['message_header'] = 'Изображение успешно загружено';
			    		}
						redirect($this->data['base_url'] . 'administrator');
					}
		    	}
			}
		}elseif($this->input->post('save_text')){

			$this->form_validation->set_rules('title', '"Заголовок"', 'trim',array('required' => 'Заголовок обязательное поле'));
			$this->form_validation->set_rules('left', '"left"', 'trim');
			$this->form_validation->set_rules('right', '"right"', 'trim');
			if($this->form_validation->run() == FALSE){

				$array = array(
					'title' => $this->form_validation->set_value('title'),
					'left' => $this->form_validation->set_value('left'),
					'right' => $this->form_validation->set_value('right'),
				);
				$this->setData('text_info_1', $array);
				$this->setData('error_text', validation_errors());
				$this->display('backend/first-page/index');
			}else{
				
				$title = $this->security->xss_clean($this->input->post('title'));
				$left = $this->security->xss_clean($this->input->post('left'));
				$right = $this->security->xss_clean($this->input->post('right'));

				$update = array(
					'title' => $title,
					'left' => $left,
					'right' => $right,
				);
				if($id = $this->Page_model->update_info_text(1,1,$update)){
					$_SESSION['message_text'] = 'Данные обновлены';
					redirect($this->data['base_url'] . 'administrator/main');	
				}
			}
			
		}elseif($this->input->post('save_text_2')){
			$this->form_validation->set_rules('title', '"Заголовок"', 'trim|required',array('required' => 'Заголовок обязательное поле'));
			$this->form_validation->set_rules('left', '"left"', 'trim');
			$this->form_validation->set_rules('right', '"right"', 'trim');
			if($this->form_validation->run() == FALSE){
				$array = array(
					'title' => $this->form_validation->set_value('title'),
					'left' => $this->form_validation->set_value('left'),
					'right' => $this->form_validation->set_value('right'),
				);
				$this->setData('text_info_2', $array);
				$this->setData('error_text', validation_errors());
				$this->display('backend/first-page/index');
			}else{
				$title = $this->security->xss_clean($this->input->post('title'));
				$left = $this->security->xss_clean($this->input->post('left'));
				$right = $this->security->xss_clean($this->input->post('right'));

				$update = array(
					'title' => $title,
					'left' => $left,
					'right' => $right,
				);
				if($this->Page_model->update_info_text(1,2,$update)){
					$_SESSION['message_text'] = 'Данные обновлены';
					redirect($this->data['base_url'] . 'administrator/main');
				}
			}
		}else{
			$this->display('backend/first-page/index');
			$_SESSION['message'] = '';
			$_SESSION['message_text'] = '';
			$_SESSION['message_header'] = '';
			$_SESSION['message_slide'] = '';
		}
	}
	public function add_slide(){
		$this->login_check();
		$this->load->model('Main_admin');
		$this->load->library('form_validation');
		$this->load->helper('string');
		$this->setData('title', 'Добавление слайда');
		$this->setData('main_active','class="active"');

		if($this->input->post('add')){
			$this->form_validation->set_rules('fio', '"ФИО"', 'trim|required',array('required' => 'ФИО обязательное поле'));
			$this->form_validation->set_rules('company', '"Компания"', 'trim|required',array('required' => 'Компания обязательное поле'));
			$this->form_validation->set_rules('text', '"Компания"', 'trim|required',array('required' => 'Отзыв обязательное поле'));
			if($this->form_validation->run() == FALSE){
				$array = array(
					'fio' => $this->input->post('fio'),
					'company' => $this->input->post('company'),
					'text' => $this->input->post('text') 
				);
				$this->setData('set_value', $array);
				$this->setData('error', validation_errors());
				$this->display('backend/first-page/add_slide');
			}else{
				if(!empty($_FILES['file_bg']['name'])){

			    	$tempFile = $_FILES['file_bg']['tmp_name'];
					$targetPath = 'uploads/slider/';
			    	$newName = random_string('md5').'.jpg';
			    	$targetFile = $targetPath . $newName;
			    	move_uploaded_file($tempFile, $targetFile);
			    	$insert['bg'] = $newName;
				}

				if(!empty($_FILES['file_photo']['name'])){
			    	$tempFile = $_FILES['file_photo']['tmp_name'];
					$targetPath = 'uploads/slider/';
			    	$newName = random_string('md5').'.jpg';
			    	$targetFile = $targetPath . $newName;
			    	move_uploaded_file($tempFile, $targetFile);
			    	$insert['photo'] = $newName;
				}

				$insert['fio'] = $this->security->xss_clean($this->input->post('fio'));
				$insert['company'] = $this->security->xss_clean($this->input->post('company'));
				$insert['text'] = $this->security->xss_clean($this->input->post('text'));

				if($this->Main_admin->add_slide($insert)){
					$_SESSION['message_slide'] = 'Отзыв добавлен';
					redirect($this->data['base_url'] . 'administrator/main');
				}

			}

		}else{
			$this->display('backend/first-page/add_slide');
		}
	}

	public function edit_slide($id){
		$this->login_check();
		$this->load->model('Main_admin');
		$this->load->library('form_validation');
		$this->load->helper('string');
		$this->setData('title', 'Редактирование');
		$this->setData('slide',$this->Main_admin->get_slide($id));
		$this->setData('main_active','class="active"');

		if($this->input->post('edit')){
			$this->form_validation->set_rules('fio', '"ФИО"', 'trim|required',array('required' => 'ФИО обязательное поле'));
			$this->form_validation->set_rules('company', '"Компания"', 'trim|required',array('required' => 'Компания обязательное поле'));
			$this->form_validation->set_rules('text', '"Компания"', 'trim|required',array('required' => 'Отзыв обязательное поле'));
			if($this->form_validation->run() == FALSE){
				$array = array(
					'fio' => $this->input->post('fio'),
					'company' => $this->input->post('company'),
					'text' => $this->input->post('text'),
					'bg' => $this->data['slide']['bg'],
					'photo' => $this->data['slide']['photo']
				);
				$this->setData('slide', $array);
				$this->setData('error', validation_errors());
				$this->display('backend/first-page/edit_slide');
			}else{
				if(!empty($_FILES['file_bg']['name'])){
					//Если bg существует
					if(isset($this->data['slide']['bg'])){
						if(!empty($this->data['slide']['bg'])){
							unlink('uploads/slider/' . $this->data['slide']['bg']);
						}
					}
			    	$tempFile = $_FILES['file_bg']['tmp_name'];
					$targetPath = 'uploads/slider/';
			    	$newName = random_string('md5').'.jpg';
			    	$targetFile = $targetPath . $newName;
			    	move_uploaded_file($tempFile, $targetFile);
			    	$update['bg'] = $newName;
				}

				if(!empty($_FILES['file_photo']['name'])){
					//Если фото существует
					if(isset($this->data['slide']['photo'])){
						if(!empty($this->data['slide']['photo'])){
							unlink('uploads/slider/' . $this->data['slide']['photo']);
						}
					}
			    	$tempFile = $_FILES['file_photo']['tmp_name'];
					$targetPath = 'uploads/slider/';
			    	$newName = random_string('md5').'.jpg';
			    	$targetFile = $targetPath . $newName;
			    	move_uploaded_file($tempFile, $targetFile);
			    	$update['photo'] = $newName;
				}

				$update['fio'] = $this->security->xss_clean($this->input->post('fio'));
				$update['company'] = $this->security->xss_clean($this->input->post('company'));
				$update['text'] = $this->security->xss_clean($this->input->post('text'));
				if($this->Main_admin->update_slide($id, $update)){
					$_SESSION['message_slide'] = 'Отзыв обновлен';
					redirect($this->data['base_url'] . 'administrator/main');
				}
			}
		}else{
			$this->display('backend/first-page/edit_slide');
			$_SESSION['message_slide'] = '';
		}
	}

	public function delete_slide($id){
		$this->load->model('Main_admin');
        $slide = $this->Main_admin->get_slide($id);
        if($slide['bg']){
        	unlink('uploads/slider/' . $slide['bg']);
        }
        if($slide['photo']){
        	unlink('uploads/slider/' . $slide['photo']);
        }
        
        if($this->Main_admin->delete_slide($slide['id'])){
            $_SESSION['message_slide'] = 'Слайд удален';
            redirect($this->data['base_url'] . 'administrator/main');
        }
	}
	public function delete_photo($id){
		$this->load->model('Main_admin');
        $slide = $this->Main_admin->get_slide($id);

        unlink('uploads/slider/' . $slide['photo']);
        
        $array['photo'] = '';

        if($this->Main_admin->update_slide($slide['id'], $array)){
            $_SESSION['message_slide'] = 'Фото удалено';
            redirect($this->data['base_url'] . 'administrator/main/edit_slide/' . $id);
        }
	}
	public function delete_bg($id){
		$this->load->model('Main_admin');
        $slide = $this->Main_admin->get_slide($id);

        unlink('uploads/slider/' . $slide['bg']);
        
        $array['bg'] = '';

        if($this->Main_admin->update_slide($slide['id'], $array)){
            $_SESSION['message_slide'] = 'Фон удален';
            redirect($this->data['base_url'] . 'administrator/main/edit_slide/' . $id);
        }
	}
}
