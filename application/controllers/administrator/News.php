<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class News extends MY_Controller {

	public function index(){
		$this->login_check();
		$this->load->model('News_admin');
		$this->load->model('Page_model');
		$this->load->library('form_validation');
 		$this->load->helper('string');
		$this->setData('thisurl', 'news');

		$this->setData('info_page', $this->Page_model->get(array('page_name' => 'news')));
		$this->setData('news', $this->News_admin->getAll());
		$this->setData('header', $this->Page_model->get_header($this->data['info_page']['id']));
		$this->setData('title','Редактирование страницы "Блог"');
		$this->setData('news_active','class="active"');

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
				$this->display('backend/news-page/index');
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
				if($this->Page_model->update(array('page_name' => 'news'),$update)){
					$_SESSION['message'] = 'Данные обновлены';
					redirect($this->data['base_url'] . 'administrator/news');
				}
			}
		}elseif($this->input->post('upload')){
			if(empty($_FILES['file']['name']) && $this->input->post('title') == $this->data['header']['title'] && $this->input->post('active') == $this->data['header']['active']){
				redirect($this->data['base_url'] . 'administrator/news');
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
				$this->display('backend/news-page/index');
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
						redirect($this->data['base_url'] . 'administrator/news');
					}
		    	}else{
		    		if($id = $this->Page_model->insert_header($insert)){
			    		if(isset($insert['img'])){
							$_SESSION['message_header'] = 'Изображение успешно загружено';
			    		}
						redirect($this->data['base_url'] . 'administrator/news');
					}
		    	}
			}
		}else{
			$this->display('backend/news-page/index');
			$_SESSION['message'] = '';
			$_SESSION['message_header'] = '';
			$_SESSION['message_news'] = '';
		}	
	}
	public function add(){
		$this->login_check();
		$this->load->model('News_admin');
		$this->load->helper('string');
		$this->setData('title', 'Создание Статьи');
		$this->setData('news_active','class="active"');

		if($this->input->post('add')){
			$this->load->library('form_validation');
			$this->load->helper('form');
			//Проверка на ввалидность
			$this->form_validation->set_rules('title', '"Название"', 'trim|required',array('required' => 'Заголовок обязательное поле'));
			$this->form_validation->set_rules('date', '"Дата"', 'trim|required',array('required' => 'Дата обязательное поле'));

			if($this->form_validation->run() == FALSE){
				$array = array(
					'title' => $this->form_validation->set_value('title'),
					'date' => $this->form_validation->set_value('date'),
				);
				$this->setData('input', $array);
				$this->setData('error', validation_errors());
				$this->display('backend/news-page/add');
			}else{
				//Добавляем данные в БД
				$title = $this->security->xss_clean(trim($this->input->post('title')));
				$date = $this->security->xss_clean(trim($this->input->post('date')));
				//Генерируем название
				$nameDir = random_string('alnum', 10);

				$data = array(
					'title' => $title,
					'date' => $date,
					'folder' => $nameDir
				);
				$insert = array();
				foreach($data as $key => $value){
					if(!empty($value)){
						$insert[$key] = $value;
					}
				}
				$datecreated = new DateTime();
				$insert['datecreated'] = $datecreated->format('Y-m-d H:i:s');
				if($add = $this->News_admin->insert($insert)){
					//Создаем папку для загрузок
					if(!file_exists('uploads/news/'.$nameDir)){
						mkdir('uploads/news/'.$nameDir, 0777);
					}
					redirect(base_url().'administrator/news/edit/'.$add);
				}
			}
		}else{
			$this->display('backend/news-page/add');
		}
	}
	public function edit($id = null){
		$this->login_check();
		$this->load->model('News_admin');
		$this->setData('title', 'Редактирование статьи');
		$this->setData('news_active','class="active"');

		if(!empty($id) && is_numeric($id) && $this->News_admin->checkId($id)){
			if($this->input->post('save')){
				$this->load->library('form_validation');
				$this->load->helper('form');
				$this->form_validation->set_rules('title', 'Заголовок', 'trim|required');
				$this->form_validation->set_rules('date', 'Дата', 'trim|required');

				if($this->form_validation->run() == FALSE){
					$array = array(
						'title' => $this->form_validation->set_value('title'),
						'date' => $this->form_validation->set_value('date'),
						'keywords' => $this->form_validation->set_value('keywords'),
						'description' => $this->form_validation->set_value('description'),
						'text' => $this->form_validation->set_value('text'),
						'pretext' => $this->form_validation->set_value('pretext'),
					);
					$this->setData('input', $array);
					$this->setData('error', validation_errors());
					$this->display('backend/news-page/edit');
				}else{
					//Обновление
					$title = $this->security->xss_clean(trim($this->input->post('title')));
					$date = $this->security->xss_clean(trim($this->input->post('date')));
					$keywords = $this->security->xss_clean(trim($this->input->post('keywords')));
					$description = $this->security->xss_clean(trim($this->input->post('description')));
					$fulltext = trim($this->input->post('text'));
					$pretext = trim($this->input->post('pretext'));
					$data = array(
						'title' => $title,
						'date' => $date,
						'text' => $fulltext,
						'pretext' => $pretext,
						'keywords' => $keywords,
						'description' => $description,
					);
					$update = array();
					foreach($data as $key => $value){
						if(!empty($value)){
							$update[$key] = $value;
						}else{
							$update[$key] = NULL;
						}
					}
					if($add = $this->News_admin->update(array('id' => $id), $update)){
						$_SESSION['message_news'] = 'Запись обновлена';
						redirect(base_url().'administrator/news');
					}
				}

			}else{
				$this->setData('input', $this->News_admin->get(array('id' =>  $id)));
				$this->display('backend/news-page/edit');
			}
		}else{
			redirect(base_url().'administrator/news');
			$_SESSION['message_header'] = '';
		}

	}
	//Загрузка и все что с ней связано
	public function upload(){
		$this->login_check();
		if (!empty($_FILES)) {
			$this->load->helper('string');
			$id = $this->input->post('postid');
			$dirName = $this->input->post('folder');
	    	$tempFile = $_FILES['file']['tmp_name'];

	    	$targetPath = 'uploads/news/'.$dirName.'/';
	    	$newName = random_string('md5').'.jpg';
	    	$targetFile = $targetPath . $newName;

	    	move_uploaded_file($tempFile, $targetFile);

	    	$this->db->insert('images', array('postId' => $id, 'name' => $newName));

		}
	}
	public function redactor_upload(){
		$this->login_check();
		$config = array(
			'upload_path' => 'uploads/news/redactor_photo/',
            'upload_url' => base_url() . 'uploads/news/redactor_photo/',
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

	public function refreshImg(){
		$this->login_check();
		$this->load->model('News_admin');
		$id = $this->input->post('postid');
		$nameDir = $this->input->post('folder');
		$result = $this->News_admin->getImages($id);
		$this->setData('set_values', $_POST);
		if($result){
			echo '<div class="col-md-12"><div class="form-group"><label>Изображения записи</label></div>
			<ul id="list" class="list-unstyled team-members">';
			foreach($result as $item){
				echo '<li id="item-'.$item['id'].'"><div class="row"><div class="col-xs-3"><div class="avatar">
					<img src="'.$this->data['base_url'].'uploads/news/'.$nameDir.'/'.$item['name'].'" alt="Circle Image" class="img-circle img-no-padding img-responsive"></div> </div><div class="col-xs-6">'.$item['name'].'</div><div class="col-xs-3 text-right"><a href="javascript:void(0);" id="'.$item['id'].'"" class="delete_img btn btn-sm btn-success btn-icon"><i class="fa fa-trash-o"></i></a></div> </div></li>';
			}
			echo '</ul></div>';
		}
	}
	public function deleteImg(){
		$this->login_check();

		$id = $this->input->post('id');
		$folder = $this->input->post('folder');

		$this->load->model('News_admin');
		$image = $this->News_admin->get_image($id);

		unlink('uploads/news/'.$folder.'/'.$image['name']);
		$this->db->where('id', $image['id']);
		$this->db->delete('images');
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
			$this->db->update('images', $data);
		}
		
	}
	public function delete($id = null){
		$this->login_check();
		$this->load->model('News_admin');
		$this->load->helper('file');
		if(!empty($id) && is_numeric($id) && $this->News_admin->checkId($id)){
			$post = $this->News_admin->get(array('id' =>  $id));
			$images = $this->News_admin->getImages($id);
			//Удаление изображений
			foreach($images as $item){
				unlink('./uploads/news/'.$post['folder'].'/'.$item['name']);
				$this->db->where('name', $item['name']);
				$this->db->delete('images');
			}
			//Удаление папки
			rmdir('./uploads/news/'.$post['folder']);
			//Удаление данных из бд
			if($this->News_admin->delete(array('id' => $id))){
				$_SESSION['message_news'] = 'Запись успешно удалена!';
				redirect(base_url().'administrator/news');
			}
		}else{
			redirect(base_url().'administrator/news');
		}
	}
}
