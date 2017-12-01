<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Prices extends MY_Controller {

	public function index(){
		$this->login_check();
		$this->load->model('Prices_admin');
		$this->load->model('Page_model');
		$this->load->library('form_validation');
 		$this->load->helper('string');
		$this->setData('thisurl', 'prices');

		$this->setData('info_page', $this->Page_model->get(array('page_name' => 'prices')));
		$this->setData('title','Редактирование страницы "Главная страница"');
		$this->setData('header', $this->Page_model->get_header($this->data['info_page']['id']));
		$this->setData('prices_active','class="active"');
		$this->setData('prices', $this->Prices_admin->getAll());
		
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
				$this->display('backend/prices-page/index');
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
				if($this->Page_model->update(array('page_name' => 'prices'),$update)){
					$_SESSION['message'] = 'Данные обновлены';
					redirect($this->data['base_url'] . 'administrator/prices');
				}
			}
		}elseif($this->input->post('upload')){
			if(empty($_FILES['file']['name']) && $this->input->post('title') == $this->data['header']['title'] && $this->input->post('active') == $this->data['header']['active']){
				redirect($this->data['base_url'] . 'administrator/prices');
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
				$this->display('backend/prices-page/index');
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
						redirect($this->data['base_url'] . 'administrator/prices');
					}
		    	}else{
		    		if($id = $this->Page_model->insert_header($insert)){
			    		if(isset($insert['img'])){
							$_SESSION['message_header'] = 'Изображение успешно загружено';
			    		}
						redirect($this->data['base_url'] . 'administrator/prices');
					}
		    	}
			}
		}else{
			$this->display('backend/prices-page/index');
			$_SESSION['message'] = '';
			$_SESSION['message_prices'] = '';
		}
	}
	public function add(){
		$this->login_check();
		$this->load->model('Prices_admin');
		$this->load->model('Page_model');
		$this->load->library('form_validation');
 		$this->load->helper('string');
		$this->setData('thisurl', 'prices');
		$this->setData('title','Добавление проекта');
		$this->setData('prices_active','class="active"');

		if($this->input->post('add')){
			$this->form_validation->set_rules('title', '"Заголовок"', 'trim|required',array('required' => 'Заголовок обязательное поле'));
			$this->form_validation->set_rules('text', '"Текст"', 'trim|required',array('required' => 'Текст обязательное поле'));
			if($this->form_validation->run() == FALSE){
				$array = array(
					'title' => $this->input->post('title'),
					'text' => $this->input->post('text') 
				);
				$this->setData('set_value', $array);
				$this->setData('error', validation_errors());
				$this->display('backend/prices-page/add');
			}else{
				$insert['title'] = $this->security->xss_clean($this->input->post('title'));
				$insert['text'] = $this->security->xss_clean($this->input->post('text'));

				if($id = $this->Prices_admin->add_price($insert)){
					redirect($this->data['base_url'] . 'administrator/prices/edit/' . $id);
				}
			}
		}else{
			$this->display('backend/prices-page/add');
		}
	}
	public function edit($id = null){
		$this->login_check();
		$this->load->model('Prices_admin');
		$this->load->model('Page_model');
		$this->load->library('form_validation');
 		$this->load->helper('string');
		$this->setData('thisurl', 'prices');
		$this->setData('title','Редактирование проекта');
		$this->setData('prices_active','class="active"');
		$this->setData('price', $this->Prices_admin->get(array('id' => $id)));

		if($this->input->post('save')){
			$this->form_validation->set_rules('title', '"Заголовок"', 'trim|required',array('required' => 'Заголовок обязательное поле'));
			$this->form_validation->set_rules('text', '"Текст"', 'trim|required',array('required' => 'Текст обязательное поле'));
			if($this->form_validation->run() == FALSE){
				$array = array(
					'title' => $this->input->post('title'),
					'text' => $this->input->post('text') 
				);
				$this->setData('set_value', $array);
				$this->setData('error', validation_errors());
				$this->display('backend/prices-page/edit');
			}else{
				$update['title'] = $this->security->xss_clean($this->input->post('title'));
				$update['text'] = $this->security->xss_clean($this->input->post('text'));

				if($this->Prices_admin->edit_price($id,$update)){
					$_SESSION['message_prices'] = 'Данные обновлены';
					redirect($this->data['base_url'] . 'administrator/prices/');
				}
			}
		}else{
			$this->display('backend/prices-page/edit');
		}
	}
	public function upload(){
		$this->login_check();
		if (!empty($_FILES)) {
			$this->load->helper('string');
			$id = $this->input->post('price_id');
	    	$tempFile = $_FILES['file']['tmp_name'];

	    	$targetPath = 'uploads/prices/';
	    	$newName = random_string('md5').'.jpg';
	    	$targetFile = $targetPath . $newName;

	    	move_uploaded_file($tempFile, $targetFile);

	    	$this->db->insert('prices_img', array('price_id' => $id, 'name' => $newName));
		}
	}
	public function refreshImg(){
		$this->login_check();
		$this->load->model('Prices_admin');
		$id = $this->input->post('price_id');
		$result = $this->Prices_admin->get_images($id);
		$this->setData('price', $_POST);
		if($result){
			echo '<div class="col-md-12"><div class="form-group"><label>Изображения проекта</label></div>
			<ul id="list" class="list-unstyled team-members">';
			foreach($result as $item){
				echo '<li id="item-'.$item['id'].'"><div class="row"><div class="col-xs-3"><div class="avatar">
					<img src="'.$this->data['base_url'].'uploads/prices/'.$item['name'].'" alt="Circle Image" class="img-circle img-no-padding img-responsive"></div> </div><div class="col-xs-6">'.$item['name'].'</div><div class="col-xs-3 text-right"><a href="javascript:void(0);" id="'.$item['id'].'"" class="delete_img btn btn-sm btn-success btn-icon"><i class="fa fa-trash-o"></i></a></div> </div></li>';
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
			$this->db->update('prices_img', $data);
		}
		
	}
	public function deleteImg(){
		$this->login_check();

		$id = $this->input->post('id');

		$this->load->model('Prices_admin');
		$image = $this->Prices_admin->get_image($id);

		unlink('uploads/prices/' . $image['name']);
		$this->db->where('id', $image['id']);
		$this->db->delete('prices_img');
	}
	public function delete($id){
		$this->login_check();
		$this->load->model('Prices_admin');
		$this->load->helper('file');
		if(!empty($id) && is_numeric($id)){
			$post = $this->Prices_admin->get(array('id' =>  $id));
			$images = $this->Prices_admin->get_images($id);
			//Удаление изображений
			foreach($images as $item){
				unlink('uploads/prices/'.$item['name']);
				$this->db->where('name', $item['name']);
				$this->db->delete('prices_img');
			}
			//Удаление данных из бд
			if($this->Prices_admin->delete(array('id' => $id))){
				$_SESSION['message_prices'] = 'Запись успешно удалена!';
				redirect(base_url().'administrator/prices');
			}
		}else{
			redirect(base_url().'administrator/prices');
		}
	}
}