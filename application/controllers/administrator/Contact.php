<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Contact extends MY_Controller {

	public function index(){
		$this->login_check();
		$this->load->model('Contact_admin');
		$this->load->model('Page_model');
		$this->load->library('form_validation');
 		$this->load->helper('string');
		$this->setData('thisurl', 'contact');

		$this->setData('info_page', $this->Page_model->get(array('page_name' => 'contact')));
		$this->setData('header', $this->Page_model->get_header($this->data['info_page']['id']));
		$this->setData('title','Редактирование страницы "Контакты"');
		$this->setData('contact_active','class="active"');

		if($this->input->post('save')){
			$this->form_validation->set_rules('title', '"Заголовок"', 'trim');
			$this->form_validation->set_rules('keywords', '"keywords"', 'trim');
			$this->form_validation->set_rules('description', '"description"', 'trim');
			$this->form_validation->set_rules('content', '"Контент"', 'trim');

			if($this->form_validation->run() == FALSE){
				$array = array(
					'title' => $this->form_validation->set_value('title'),
					'keywords' => $this->form_validation->set_value('keywords'),
					'description' => $this->form_validation->set_value('description'),
					'content' => $this->form_validation->set_value('content')
				);
				$this->setData('info_page', $array);
				$this->setData('error', validation_errors());
				$this->display('backend/contact-page/index');
			}else{
				$title = $this->security->xss_clean($this->input->post('title'));
				$keywords = $this->security->xss_clean($this->input->post('keywords'));
				$description = $this->security->xss_clean($this->input->post('description'));
				$content = $this->security->xss_clean($this->input->post('content'));
				$update = array(
					'title' => $title,
					'keywords' => $keywords,
					'description' => $description,
					'content' => $content
				);
				if($this->Page_model->update(array('page_name' => 'contact'),$update)){
					$_SESSION['message'] = 'Данные обновлены';
					redirect($this->data['base_url'] . 'administrator/contact');
				}
			}
		}elseif($this->input->post('upload')){
			if(empty($_FILES['file']['name']) && $this->input->post('title') == $this->data['header']['title'] && $this->input->post('active') == $this->data['header']['active']){
				redirect($this->data['base_url'] . 'administrator/contact');
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
				$this->display('backend/contact-page/index');
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
						redirect($this->data['base_url'] . 'administrator/contact');
					}
		    	}else{
		    		if($id = $this->Page_model->insert_header($insert)){
			    		if(isset($insert['img'])){
							$_SESSION['message_header'] = 'Изображение успешно загружено';
			    		}
						redirect($this->data['base_url'] . 'administrator/contact');
					}
		    	}
			}
		}else{
			$this->display('backend/contact-page/index');
			$_SESSION['message'] = '';
			$_SESSION['message_header'] = '';
		}

	}
	public function redactor_upload(){
		$this->login_check();
		$config = array(
			'upload_path' => 'uploads/content/',
            'upload_url' => base_url() . 'uploads/content/',
            'allowed_types' => 'jpg|gif|png',
            'overwrite' => false,
        	'max_size' => 512000,            
    	);

    	$this->load->library('upload', $config);

    	if($this->upload->do_upload('file')){
        	$data = $this->upload->data();
        	$array = array(
            	'filelink' => $config['upload_url'] . $data['file_name']
        	);            
        	echo stripslashes(json_encode($array));
    	}else{
        	echo json_encode(array('error' => $this->upload->display_errors('', '')));
    	}
	}
}
