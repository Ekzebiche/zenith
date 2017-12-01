<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends MY_Controller {

	public function index(){
		$this->login_check();
		$this->load->model('Users_admin');
		$this->setData('users', $this->Users_admin->getAll());
		$this->setData('title','Добавить / Редактировать пользователя');
		$this->setData('users_active','class="active"');
		$this->display('backend/users-page/index');
		$_SESSION['message'] = '';
	}
	public function add(){
		$this->login_check();
		$this->load->model('Users_admin');
		$this->load->library('form_validation');
		$this->setData('title','Добавление нового пользователя');
		$this->setData('title_user','Добавление нового пользователя');

		if($this->input->post('save')){
			$this->form_validation->set_rules('email', '"Ваш E-mail"', 'trim|required|valid_email',array('required' => 'Ваш email обязательное поле','valid_email' => 'Введите правильно email'));
			$this->form_validation->set_rules('password', '"Пароль"', 'trim|required|min_length[5]',array('required' => 'Пароль обязательное поле', 'min_length' => 'Минимальная длинна 5 символов'));
			if($this->form_validation->run() == FALSE){
				$array = array(
					'email' => $this->form_validation->set_value('email'),
					'password' => $this->form_validation->set_value('password')
				);

				$this->setData('user', $array);
				$this->setData('error', validation_errors());
				$this->display('backend/users-page/action');
			}else{
				$random_salt = hash('sha512', uniqid(openssl_random_pseudo_bytes(16), TRUE));
				$email = $this->security->xss_clean($this->input->post('email'));
				$password = $this->security->xss_clean($this->input->post('password'));
				$password = hash('sha512', $password . $random_salt);
				$insert = array(
					'email' => $email,
					'password' => $password,
					'salt' => $random_salt
				);
				if($this->Users_admin->insert($insert)){
					$_SESSION['message'] = 'Пользователь добавлен';
					redirect($this->data['base_url'] . 'administrator/users');
				}
			}
		}else{
			$this->display('backend/users-page/action');
		}
	}
	public function edit($id = null){
		$this->login_check();
		$this->load->model('Users_admin');
		$this->load->library('form_validation');
		$this->setData('title','Редактирование пользователя');
		$this->setData('users', $this->Users_admin->get(array('id_user' => $id)));
		$this->setData('title_user','Редактирование пользователя');

		if($this->input->post('save')){
			$this->form_validation->set_rules('email', '"Ваш E-mail"', 'trim|required|valid_email',array('required' => 'Ваш email обязательное поле'));
			$this->form_validation->set_rules('password', '"Пароль"', 'trim|required|min_length[5]',array('required' => 'Пароль обязательное поле', 'min_length' => 'Минимальная длинна 5 символов'));
			if($this->form_validation->run() == FALSE){
				$array = array(
					'email' => $this->form_validation->set_value('email'),
					'password' => $this->form_validation->set_value('password')
				);

				$this->setData('user', $array);
				$this->setData('error', validation_errors());
				$this->display('backend/users-page/action');
			}else{
				$random_salt = hash('sha512', uniqid(openssl_random_pseudo_bytes(16), TRUE));
				$email = $this->security->xss_clean($this->input->post('email'));
				$password = $this->security->xss_clean($this->input->post('password'));
				$password = hash('sha512', $password . $random_salt);
				$update = array(
					'email' => $email,
					'password' => $password,
					'salt' => $random_salt 
				);
				if($this->Users_admin->update(array('id_user' => $id), $update)){
					$_SESSION['message'] = 'Данные обновлены';
					redirect($this->data['base_url'] . 'administrator/users');
				}
			}
		}else{
			$this->display('backend/users-page/action');
		}
	}

	public function delete($id = null){
		$this->load->model('Users_admin');
		if($delete = $this->Users_admin->delete(array('id_user' => $id))){
			$_SESSION['message'] = 'Пользователь удален';
			redirect($this->data['base_url'] . 'administrator/users');
		}
	}
}