<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller{

	// данные которые передаются в шаблон
	protected $data = array();

	// конструктор контроллера
	public function __construct() {
		parent::__construct();
        $this->sec_session_start();

        $this->load->config('my_config');
		$this->load->library('Templater', array('views_folder_path' => VIEWS_FOLDER_PATH));
		$this->setData('base_url', base_url());
	}

	// метод, который кладет данные в массив, который передается в шаблон
	protected function setData($key, $value){
		$this->data[$key] = $value;
	}

	// метод для показа отрендеринового шаблона
	protected function display($template){
		echo $this->templater->render($template, $this->data);
	}

	// метод который генерит шаблон нативного CI и возвращает разметку
	protected function renderHTML($template, $data = array(), $return = false){
		return $this->load->view($template, $data, $return);
	}

	//Создание сессии
    public function sec_session_start() {
    	$session_name = 'sec_session_id';
    	$secure = false;

    	$httponly = true;

	    if (ini_set('session.use_only_cookies', 1) === FALSE) {
            redirect(base_url());
	        exit();
	    }

    	$cookieParams = session_get_cookie_params();
    	session_set_cookie_params($cookieParams["lifetime"],
        	$cookieParams["path"],
        	$cookieParams["domain"],
        	$secure,
        	$httponly);

    	session_name($session_name);
    	session_start();
    	session_regenerate_id();
	}

    //Проверка входа + смена браузера
    public function login_check(){
        $this->load->model('auth_model');
        if(isset($_SESSION['user_id'], $_SESSION['username'], $_SESSION['login_string'])){

            $user_id = $_SESSION['user_id'];
            $login_string = $_SESSION['login_string'];
            $username = $_SESSION['username'];
            //Информация о брацзере
            $user_browser = $_SERVER['HTTP_USER_AGENT'];

            $result = $this->auth_model->get_info($user_id);
            $login_check = hash('sha512', $result['password'] . $user_browser);

            if($login_check == $login_string){
                return true;
            }
        }else{
            redirect($this->data['base_url'] . 'administrator/login');
        }
    }
    function random_string($length) {
        $key = '';
        $keys = array_merge(range(0, 9), range('a', 'z'));

        for ($i = 0; $i < $length; $i++) {
            $key .= $keys[array_rand($keys)];
        }

        return $key;
    }

    public function delete_header($page_id, $url){
        $this->load->model('Page_model');
        $header = $this->Page_model->get_header($page_id);
        unlink('uploads/headers/' . $header['img']);
        $update = array('img' => null);
        if($this->Page_model->update_header(array('id' => $header['id']), $update)){
            $_SESSION['message_header'] = 'Изображение удалено';
            redirect($this->data['base_url'] . 'administrator/' . $url);
        }
    }
    public function redactor_upload_content(){
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
    public function send_email(){
        if($this->input->post()){
            
            $this->load->library('form_validation');

            if($this->input->post('uname')){
                $this->form_validation->set_rules('uname', '"Ваше имя"', 'trim|required',array('required' => 'Ваше имя обязательное поле'));
            }

            if($this->input->post('uemail')){
                $this->form_validation->set_rules('uemail', '"Email"', 'trim|required',array('required' => 'Email обязательное поле'));
            }
            
            if($this->input->post('uphone')){
                $this->form_validation->set_rules('uphone', '"Телефон"', 'trim|required',array('required' => 'Телефон обязательное поле'));
            }

            $this->form_validation->set_rules('utext', '"Заголовок"', 'trim');
            $this->form_validation->set_rules('formReferer', '"Заголовок"', 'trim');
            $this->form_validation->set_rules('formInfo', '"Заголовок"', 'trim');

            if($this->form_validation->run() == FALSE){
                echo 'Ошибка. Вы заполнили не все обязательные поля!';
            }else{
                $uname = $this->input->input_stream('uname', TRUE);
                $uemail = $this->input->input_stream('uemail', TRUE);
                $uphone = $this->input->input_stream('uphone', TRUE);
                $utext = $this->input->input_stream('utext', TRUE);
                $formReferer = $this->input->input_stream('formReferer', TRUE);
                $formInfo = $this->input->input_stream('formInfo', TRUE);

                $to = "kurandero@yandex.ru"; /*Укажите адрес, на который должно приходить письмо*/
                $sendfrom = "kurandero@yandex.ru"; /*Укажите адрес, с которого будет приходить письмо */
                $headers  = "From: " . strip_tags($sendfrom) . "\n";
                $headers .= "Reply-To: ". strip_tags($sendfrom) . "\n";
                $headers .= "MIME-Version: 1.0\n";
                $headers .= "Content-Type: text/html;charset=utf-8 \n";
                $headers .= "Content-Transfer-Encoding: 8bit \n";
                $subject = "$formInfo";
                $message = "$unameFieldset $uname
                            $uemailFieldset $uemail
                            $uphoneFieldset $uphone
                            $utextFieldset $utext
                            $formRefererFieldset $formReferer
                            $formInfoFieldset $formInfo";

                $send = mail ($to, $subject, $message, $headers);
                    if ($send == 'true') {
                        echo 'Спасибо за отправку вашего сообщения!';
                        die();
                    } else {
                      echo 'Ошибка. Сообщение не отправлено!';
                      die();
                    }
            }
        }else{
            redirect($this->data['base_url']);  
        }
    }
}
