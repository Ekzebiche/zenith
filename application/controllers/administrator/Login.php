<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends MY_Controller {

	public function index(){
		$this->load->model('Auth_model');
		$this->setData('title', 'Вход в систему');
		//Если пользователь авторизован
		if(isset($_SESSION['user_id'], $_SESSION['username'], $_SESSION['login_string'])){

            $user_id = $_SESSION['user_id'];
            $login_string = $_SESSION['login_string'];
            $username = $_SESSION['username'];
            //Информация о брацзере
            $user_browser = $_SERVER['HTTP_USER_AGENT'];
            
            $result = $this->Auth_model->get_info($user_id);
            $login_check = hash('sha512', $result['password'] . $user_browser);

            if($login_check == $login_string){
                redirect($this->data['base_url']);
            }
        }

        if($this->input->post('enter')){
       		//Проверка введенных данных
        	$email = $this->input->post('email');
        	$psw = $this->input->post('password');
        	if($id = $this->Auth_model->check_login($email)){
        		//проверка на брут
                if($this->Auth_model->check_brute($id)){
                    $result = $this->Auth_model->get_info($id);
                    //проверка пароля
                    $password = hash('sha512', $psw . $result['salt']);

                    if($password == $result['password']){

                        $user_browser = $_SERVER['HTTP_USER_AGENT'];
                        $user_id = preg_replace("/[^0-9]+/", "", $id);
                        $_SESSION['user_id'] = $user_id;
                        $_SESSION['username'] = $result['email'];
                        $_SESSION['login_string'] = hash('sha512', $password . $user_browser);
                        redirect($this->data['base_url'] . 'administrator/main');
                    }else{
                        $this->Auth_model->detected_brute($id);
                        $this->setData('error', 'Введены неверные данные!');
                        $this->display('backend/login-page/index');
                    }

                }else{
                    $this->setData('error', 'Ваш учетная запись заблокирована на 2 часа!');
                    $this->display('backend/login-page/index');
                }
        	}else{
        		$this->setData('error', 'Такой email не зарегистрирован');
               $this->display('backend/login-page/index');
        	}
        }else{
        	$this->display('backend/login-page/index');
        	$_SESSION['message'] = '';
        }
	}

	public function logout(){
        $_SESSION = array();
        $params = session_get_cookie_params();
        setcookie(session_name(),
                    '', time() - 42000, 
                    $params["path"], 
                    $params["domain"], 
                    $params["secure"], 
                    $params["httponly"]);
 
        session_destroy();

        redirect($this->data['base_url']);
    }
}