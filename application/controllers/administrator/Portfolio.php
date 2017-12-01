<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Portfolio extends MY_Controller {

	public function index(){
		$this->login_check();
		$this->load->model('Portfolio_admin');
		$this->load->model('Page_model');
		$this->load->library('form_validation');
		$this->load->helper('string');
		$this->setData('thisurl', 'portfolio');

		$this->setData('info_page', $this->Page_model->get(array('page_name' => 'portfolio')));
		$this->setData('header', $this->Page_model->get_header($this->data['info_page']['id']));
		$this->setData('projects', $this->Portfolio_admin->getAll());
		$this->setData('title','Редактирование страницы "Главная страница"');
		$this->setData('portfolio_active','class="active"');
		
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
				$this->display('backend/portfolio-page/index');
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
				if($this->Page_model->update(array('page_name' => 'portfolio'),$update)){
					$_SESSION['message'] = 'Данные обновлены';
					redirect($this->data['base_url'] . 'administrator/portfolio');
				}
			}
		}elseif($this->input->post('upload')){
			if(empty($_FILES['file']['name']) && $this->input->post('title') == $this->data['header']['title'] && $this->input->post('active') == $this->data['header']['active']){
				redirect($this->data['base_url'] . 'administrator/portfolio');
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
				$this->display('backend/portfolio-page/index');
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
						redirect($this->data['base_url'] . 'administrator/portfolio');
					}
		    	}else{
		    		if($id = $this->Page_model->insert_header($insert)){
			    		if(isset($insert['img'])){
							$_SESSION['message_header'] = 'Изображение успешно загружено';
			    		}
						redirect($this->data['base_url'] . 'administrator/portfolio');
					}
		    	}
			}

		}else{
			$this->display('backend/portfolio-page/index');
			$_SESSION['message'] = '';
			$_SESSION['message_header'] = '';
			$_SESSION['message_project'] = '';
		}
	}

	public function add_project(){
		$this->login_check();
		$this->load->model('Portfolio_admin');
		$this->load->library('form_validation');
		$this->load->helper('string');
		$this->setData('title','Добавление проекта');
		$this->setData('portfolio_active','class="active"');

		if($this->input->post('add')){
			$this->form_validation->set_rules('title', '"Название"', 'trim|required',array('required' => 'Название обязательное поле'));
			$this->form_validation->set_rules('text', '"Описание"', 'trim|required',array('required' => 'Описание обязательное поле'));
			if($this->form_validation->run() == FALSE){
				$array = array(
					'title' => $this->form_validation->set_value('title'),
					'text' => $this->form_validation->set_value('text')
				);
				$this->setData('set_value', $array);
				$this->setData('error', validation_errors());
				$this->display('backend/portfolio-page/add_project');
			}else{
				$insert = array(
					'title' =>$this->security->xss_clean($this->input->post('title')),
					'text' =>$this->security->xss_clean($this->input->post('text'))
				);
				if($id = $this->Portfolio_admin->add_project($insert)){
					redirect($this->data['base_url'] . 'administrator/portfolio/edit_project/' . $id);
				}
			}
		}else{
			$this->display('backend/portfolio-page/add_project');
		}

	}
	public function edit_project($id){
		$this->login_check();
		$this->load->model('Portfolio_admin');
		$this->load->library('form_validation');
		$this->load->helper('string');
		$this->setData('title','Добавление проекта');
		$this->setData('portfolio_active','class="active"');
		$this->setData('project', $this->Portfolio_admin->get(array('id' => $id)));

		if($this->input->post('save')){
			$this->form_validation->set_rules('title', '"Название"', 'trim|required',array('required' => 'Название обязательное поле'));
			$this->form_validation->set_rules('text', '"Описание"', 'trim|required',array('required' => 'Описание обязательное поле'));
			$this->form_validation->set_rules('index', '"index"', 'trim');
			if($this->form_validation->run() == FALSE){
				$array = array(
					'title' => $this->form_validation->set_value('title'),
					'text' => $this->form_validation->set_value('text'),
					'index' => $this->form_validation->set_value('index'),
				);
				$this->setData('project', $array);
				$this->setData('error', validation_errors());
				$this->display('backend/portfolio-page/edit_project');
			}else{
				$update = array(
					'title' =>$this->security->xss_clean($this->input->post('title')),
					'text' =>$this->security->xss_clean($this->input->post('text')),
					'index' =>$this->security->xss_clean($this->input->post('index'))
				);
				if($this->Portfolio_admin->update_project($id, $update)){
					$_SESSION['message_project'] = 'Проект обновлен';
					redirect($this->data['base_url'] . 'administrator/portfolio');
				}
			}
		}else{
			$this->display('backend/portfolio-page/edit_project');
		}
		
	}

	public function delete($id){
		$this->login_check();
		$this->load->model('Portfolio_admin');
		$this->load->helper('file');
		if(!empty($id) && is_numeric($id)){
			$post = $this->Portfolio_admin->get(array('id' =>  $id));
			$images = $this->Portfolio_admin->get_images($id);
			//Удаление изображений
			foreach($images as $item){
				unlink('uploads/project/'.$item['name']);
				$this->db->where('name', $item['name']);
				$this->db->delete('project_img');
			}
			//Удаление данных из бд
			if($this->Portfolio_admin->delete(array('id' => $id))){
				$_SESSION['message_project'] = 'Запись успешно удалена!';
				redirect(base_url().'administrator/portfolio');
			}
		}else{
			redirect(base_url().'administrator/portfolio');
		}
	}

	public function upload(){
		$this->login_check();
		if (!empty($_FILES)) {
			$this->load->helper('string');
			$id = $this->input->post('portfolio_id');
	    	$tempFile = $_FILES['file']['tmp_name'];

	    	$targetPath = 'uploads/project/';
	    	$newName = random_string('md5').'.jpg';
	    	$targetFile = $targetPath . $newName;

	    	move_uploaded_file($tempFile, $targetFile);

	    	$this->db->insert('project_img', array('project_id' => $id, 'name' => $newName));
		}
	}
	public function refreshImg(){
		$this->login_check();
		$this->load->model('Portfolio_admin');
		$id = $this->input->post('project_id');
		$result = $this->Portfolio_admin->get_images($id);
		$this->setData('project', $_POST);
		if($result){
			echo '<div class="col-md-12"><div class="form-group"><label>Изображения проекта</label></div>
			<ul id="list" class="list-unstyled team-members">';
			foreach($result as $item){
				echo '<li id="item-'.$item['id'].'"><div class="row"><div class="col-xs-3"><div class="avatar">
					<img src="'.$this->data['base_url'].'uploads/project/'.$item['name'].'" alt="Circle Image" class="img-circle img-no-padding img-responsive"></div> </div><div class="col-xs-6">'.$item['name'].'</div><div class="col-xs-3 text-right"><a href="javascript:void(0);" id="'.$item['id'].'"" class="delete_img btn btn-sm btn-success btn-icon"><i class="fa fa-trash-o"></i></a></div> </div></li>';
			}
			echo '</ul></div>';
		}
	}
	public function save_position() {
		$this->login_check();
		$items = $this->input->post('item');
		$total_items = count($this->input->post('item'));
		
		for($item = 0; $item < $total_items; $item++ ) {
			$data = array(
				'id' => $items[$item],
				'position' => $order = $item
			);
			$this->db->where('id', $data['id']);
			$this->db->update('project_img', $data);
		}
		
	}
	public function deleteImg(){
		$this->login_check();

		$id = $this->input->post('id');

		$this->load->model('Portfolio_admin');
		$image = $this->Portfolio_admin->get_image($id);

		unlink('uploads/project/' . $image['name']);
		$this->db->where('id', $image['id']);
		$this->db->delete('project_img');
	}
}